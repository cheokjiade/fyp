<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
function distanceGeoPoints ($lat1, $lng1, $lat2, $lng2) {

    $earthRadius = 3958.75;

    $dLat = deg2rad($lat2-$lat1);
    $dLng = deg2rad($lng2-$lng1);


    $a = sin($dLat/2) * sin($dLat/2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $dist = $earthRadius * $c;

    // from miles
    $meterConversion = 1609;
    $geopointDistance = $dist * $meterConversion;

    return $geopointDistance;
}

require_once('../db/conn.php');
require_once('../util/others.php');

$query = $conn->prepare("SELECT l.* FROM location l, routepoint rp WHERE l.session_hash = rp.session_hash AND l.location_time = rp.location_time AND rp.route_id = 31 ORDER BY l.location_time");
$query->execute();
$returnArray = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->prepare("SELECT publictransportstops_id, publictransportstops_lat AS lat, publictransportstops_lng AS lng FROM publictransportstops;;");
$query->execute();
$publicTransportStops = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->prepare("SELECT * FROM route LIMIT 30,1");
$query->execute();
$routes = $query->fetchAll(PDO::FETCH_ASSOC);
$busstopsforroute = array();
foreach($routes as $route){
    set_time_limit(100);
    $busStopArray = array();
    $query = $conn->prepare("SELECT l.* FROM location l, routepoint rp WHERE l.session_hash = rp.session_hash AND l.location_time = rp.location_time AND rp.route_id = :route_id ORDER BY l.location_time");
    $query->bindParam(":route_id",$route['route_id']);
    $query->execute();
    $routePoints = $query->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<sizeof($routePoints)-1;$i+=1){
        //find bus stops along path
        foreach($publicTransportStops as $stop){
            if($routePoints[$i]!=$routePoints[$i+1]['location_lat'] && $routePoints[$i]['location_lng']!=$routePoints[$i+1]['location_lng']){
                $distance = get_geo_distance_point_to_segment(
                    array("lat"=>$routePoints[$i]['location_lat'],"lng"=>$routePoints[$i]['location_lng']),
                    array("lat"=>$routePoints[$i+1]['location_lat'],"lng"=>$routePoints[$i+1]['location_lng']),
                    array("lat"=>$stop['lat'],"lng"=>$stop['lng']));

                //add bus stops in range to bus stop array
                if($distance <= (($routePoints[$i]['location_accuracy']+$routePoints[$i+1]['location_accuracy'])/2)){
                    if(!in_array($stop['publictransportstops_id'],$busStopArray)){
                        $busStopArray[] =  $stop['publictransportstops_id'];
                    }
                }
            }

        }

    }
    $busstopsforroute[$route['route_id']] = $busStopArray;

}

//print_r($busstopsforroute);
$routesAndMatchedBusServices = array();
foreach($busstopsforroute as $routeID => $busStopList){
    set_time_limit(100);
    $routesAndMatchedBusServices[$routeID] = array();
    $busServicesArray = array();
    $busServicesCountLikelyArray = array();
    foreach($busStopList as $busStop){
        set_time_limit(20);
        $query = $conn->prepare("SELECT publictransportservices_id, publictransportservices_route_id FROM publictransportservicestops WHERE publictransportstops_id = :publictransportstops_id GROUP BY publictransportservices_id, publictransportservices_route_id");
        $query->bindParam(":publictransportstops_id",$busStop);
        $query->execute();
        $busServices = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($busServices as $busService){
            if(!array_key_exists($busService['publictransportservices_id'].';'.$busService['publictransportservices_route_id'],$busServicesArray)){
                $busServicesArray[$busService['publictransportservices_id'].';'.$busService['publictransportservices_route_id']] = array();
            }
            $busServicesArray[$busService['publictransportservices_id'].';'.$busService['publictransportservices_route_id']][]= $busStop;
        }
    }
    //print_r($busServicesArray);
    //select bus services that have 2 bus stops at least.
    foreach($busServicesArray as $busServices => $busStops){
        if(sizeof($busStops)>2){
            $busServicesCountLikelyArray[$busServices] = $busStops;
        }
    }

    foreach($busServicesCountLikelyArray as $busServices => $busStops){
        set_time_limit(20);
        $busStopsMatched = array();
        $busServiceDetails = explode(';',$busServices);
        $query = $conn->prepare("SELECT publictransportstops_id FROM publictransportservicestops WHERE publictransportservices_id = :publictransportservices_id AND publictransportservices_route_id = :publictransportservices_route_id ORDER BY publictransportservicestops_order;");
        $query->bindParam(":publictransportservices_id",$busServiceDetails[0]);
        $query->bindParam(":publictransportservices_route_id",$busServiceDetails[1]);
        $query->execute();
        $busStopsInOrder = $query->fetchAll(PDO::FETCH_ASSOC);

        $initialMatch = -1;
        $currentMatch = -1;
        $skipped = 0;
        for($i=0;$i<sizeof($busStops);$i++){
            /*if($skipped==4){
                break;
            } */
            //initial match is -1. this will find the first match
            if($initialMatch == -1){
                for($j=0;$j<sizeof($busStopsInOrder);$j++){
                    if($busStopsInOrder[$j]['publictransportstops_id']==$busStops[$i]){
                        $initialMatch = $j;
                        $currentMatch = $j;
                        $busStopsMatched[] = $busStops[$i];
                        break;
                    }
                }
                continue;
            }
            //if not same, advance both arrays until a match is found
            if($busStopsInOrder[$currentMatch+1]['publictransportstops_id']!=$busStops[$i]){
                for($k=$i;$k<sizeof($busStops);$k+=1){
                    for($l=$currentMatch+1;$l<sizeof($busStopsInOrder);$l++){
                        if($busStopsInOrder[$l]['publictransportstops_id']==$busStops[$k]){
                            //if the matched bus stop is at least 4 bus stops, we restart from the new bus stop
                            if($l-$currentMatch>3){
                                $initialMatch = $l;
                                $busStopsMatched = array();
                            }
                            $currentMatch = $l;
                            $busStopsMatched[] = $busStops[$k];
                            $i=$k;
                            $skipped +=1;
                            break 2;
                        }
                    }
                }

            }
            else{
                $currentMatch+=1;
                $busStopsMatched[] = $busStops[$i];
            }
        }
        //at least a sequence of 3 is matched
        if(sizeof($busStopsMatched)>2){
            $routesAndMatchedBusServices[$routeID][$busServices] = $busStopsMatched;
        }
    }
}
$busServicesRouteArray = $routesAndMatchedBusServices['31'];
$allBusArray = array();
foreach($busServicesRouteArray as $key => $valueList){
    $busArray = array();
    foreach($valueList as $value){
        $query = $conn->prepare("SELECT publictransportstops_lat AS lat, publictransportstops_lng AS lng FROM publictransportstops WHERE publictransportstops_id = :publictransportstops_id;");
        $query->bindParam(":publictransportstops_id",$value);
        $query->execute();
        $busArray[] = $query->fetch(PDO::FETCH_ASSOC);
    }
    $allBusArray[] = $busArray;
}
//for($i=0;$i<sizeof($returnArray);$i+=1){
//    $nearestPoint = 1000;
//    for($j=0;j<sizeof($busRouteArray);$j+=1){
//        $distance = distanceGeoPoints($returnArray[$i]['location_lat'],$returnArray[$i]['location_lng'],$busRouteArray[$j][lat],$busRouteArray[$j][lng]);
//        if($distance<$nearestPoint){
//            $nearestPoint = $distance;
//            $lastJ=$j;
//        }
//    }
//    if($j==0){
//        $distanceArray[] = get_geo_distance_point_to_segment($busRouteArray[$j],$busRouteArray[$j+1],array("lat"=>$returnArray[$i]['location_lat'],"lng"=>$returnArray[$i]['location_lng']));
//    }
//}


//print_r($distanceArray);


//$distanceArray = array();
//for($i=0;$i<sizeof($returnArray);$i+=1){
//    $distance = distance($actualLat,$actualLng,$returnArray[$i]['location_lat'],$returnArray[$i]['location_lng']);
//    $totalDistance += $distance;
//    $distanceArray[] = $distance;
//}

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Route Viewer</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0 }

    </style>
    <link rel="stylesheet/less" type="text/css" href="../styles/fullmapstyles.less" />
    <script src="../scripts/less-1.4.1.min.js" type="text/javascript"></script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5AN7Cbf3jecSlyOUHNNoCPE1ZJc6wGEw&sensor=true">
    </script>
    <script src="../scripts/jquery-2.0.3.min.js" type="text/javascript"></script>
</head>
<body>
<div id='left'>

</div>
<div id="map-canvas"/>
<script>
    var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF");
    var busRouteArray = [];
    var locPoints = [];
    var pointsAcc = [];
    var pathArray = [];
    var allBusArray = [];
    <?php
        foreach($returnArray as $point) {
    ?>
    busRouteArray.push(new google.maps.LatLng(<?php echo $point["location_lat"]?>, <?php echo $point["location_lng"]?>));
    <?php
        }
    ?>
    <?php
        $counter = 0;
        foreach($allBusArray as $busArray){
            echo 'var busArray'.$counter.' = [];' . "\n";
            foreach($busArray as $stops){
    ?>
            busArray<?php echo $counter?>.push(new google.maps.LatLng(<?php echo $stops["lat"]?>, <?php echo $stops["lng"]?>));
    <?php
            }

    ?>
            allBusArray.push(busArray<?php echo $counter?>);
    <?php
        $counter+=1;
        }

    ?>


    function initialize() {
        // Create the map.
        var mapOptions = {
            zoom: 13,
            scaleControl: true,
            center: new google.maps.LatLng(1.37081484, 103.85283565217),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var flightPath = new google.maps.Polyline({
            path: busRouteArray,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 3
        });

        flightPath.setMap(map);
        for (var i =0 ; i<allBusArray.length;i++) {
            var flightPath = new google.maps.Polyline({
                path: allBusArray[i],
                geodesic: true,
                map: map,
                strokeColor: colors[i%4],
                strokeOpacity: 0.6,
                strokeWeight: 2
            });
        }
        /*var marker = new google.maps.Marker({
         position: new google.maps.LatLng(1.36996326781056, 103.85356664655984),
         map: map,
         title: 'Location'
         });
         var circleOptions = {
         strokeColor: '#FF0000',
         strokeOpacity: 0.8,
         strokeWeight: 2,
         fillColor: '#FF0000',
         fillOpacity: 0.25,
         map: map,
         center: new google.maps.LatLng(1.36996326781056, 103.85356664655984),
         radius: 50//locPoints[locPoint].population / 20
         };
         // Add the circle for this city to the map.
         cityCircle = new google.maps.Circle(circleOptions);
         // Construct the circle for each value in citymap.
         // Note: We scale the population by a factor of 20.
         for (var i =0 ; i<locPoints.length;i++) {
         var circleOptions = {
         strokeColor: '#FF0000',
         strokeOpacity: 0.8,
         strokeWeight: 2,
         fillColor: '#FF0000',
         fillOpacity: 0.25,
         map: map,
         center: locPoints[i],
         radius: 1//locPoints[locPoint].population / 20
         };
         // Add the circle for this city to the map.
         cityCircle = new google.maps.Circle(circleOptions);
         }*/
    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
</body>
</html>