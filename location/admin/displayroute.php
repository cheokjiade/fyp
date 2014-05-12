<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


require_once('../db/conn.php');
require_once('../util/others.php');
$route_id = !isset($_REQUEST['r']) ? 31 : $_REQUEST['r'];
$query = $conn->prepare("SELECT l.* FROM location l, routepoint rp WHERE l.session_hash = rp.session_hash AND l.location_time = rp.location_time AND rp.route_id = :route_id ORDER BY l.location_time");
$query->bindParam(":route_id",$route_id);
$query->execute();
$returnArray = $query->fetchAll(PDO::FETCH_ASSOC);


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
    <link rel="stylesheet/less" type="text/css" href="../styles/styles.less" />
    <script src="../scripts/less-1.4.1.min.js" type="text/javascript"></script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5AN7Cbf3jecSlyOUHNNoCPE1ZJc6wGEw&sensor=true">
    </script>
    <script src="../scripts/jquery-2.0.3.min.js" type="text/javascript"></script>
</head>
<body>
<div id='left'>
    <table>
        <?php foreach($returnArray as $point) { ?>
        <tr>
            <td><?php echo $point['location_time']; ?></td>
            <td class="locationSelector"><?php echo $point['location_lat'].','.$point['location_lng']; ?></td>
        </tr>

    <?php } ?>
    </table>
</div>
<div id="map-canvas"/>
<script>
    var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
    var busRouteArray = [];
    var locPoints = [];
    var pointsAcc = [];
    var pathArray = [];
    var map;
    <?php
        foreach($returnArray as $point) {
    ?>
    busRouteArray.push(new google.maps.LatLng(<?php echo $point["location_lat"]?>, <?php echo $point["location_lng"]?>));
    <?php
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

        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var flightPath = new google.maps.Polyline({
            path: busRouteArray,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        flightPath.setMap(map);

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

    $(".locationSelector").click(function() {
        var tmpCircle = new google.maps.Circle({
            strokeColor: '#00FF00',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#00FF00',
            fillOpacity: 0.25,
            map: map,
            center: new google.maps.LatLng(Number($(this).text().split(',')[0]),Number($(this).text().split(',')[1])),
            radius: 100//locPoints[locPoint].population / 20
        });
        points.push(tmpCircle);
    });
</script>
</body>
</html>