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

$date = '2013-12-16';
require_once('../db/conn.php');
require_once('../util/others.php');
$dateStart = $date . " 16:15:00";
$dateEnd = $date . " 18:00:00";
$query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE (location_time BETWEEN :dateStart AND :dateEnd) AND session_hash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3'");
$query->bindParam(":dateStart",$dateStart);
$query->bindParam(":dateEnd",$dateEnd);
$query->execute();
$returnArray = smoothPoints($query->fetchAll(PDO::FETCH_ASSOC));
$pointArray =mergePoints(retrievePointsFromLocations($returnArray));
$actualLat = 1.370706;
$actualLng = 103.85283565217;
$totalDistance = 0;
$distanceArray = array();
$maxDistance = 0;
for($i=0;$i<sizeof($returnArray);$i+=1){
    $distance = distanceGeoPoints($actualLat,$actualLng,$returnArray[$i]['location_lat'],$returnArray[$i]['location_lng']);
    $totalDistance += $distance;
    $distanceArray[] = $distance;
    if($distance > $maxDistance) $maxDistance = $distance;
}
sort($distanceArray);
print_r ($pointArray);
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
    <p>Min: <?php echo $distanceArray[0]?></p>
    <p>Max: <?php echo $distanceArray[sizeof($returnArray)-1]?></p>
    <p>Mean: <?php echo $totalDistance/sizeof($distanceArray)?></p>
    <p>Median: <?php echo $distanceArray[sizeof($returnArray)/2]?></p>
</div>
<div id="map-canvas"/>
<script>
    var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
    var locPoints = [];
    var pointsArray = [];
    var pointsAcc = [];
    var pathArray = [];
    <?php
    foreach($returnArray as $point){
    ?>
    locPoints.push(new google.maps.LatLng(<?php echo $point["location_lat"]?>, <?php echo $point["location_lng"]?>));

    <?php
    }
    foreach($pointArray as $point){
    ?>
    pointsArray.push(new google.maps.LatLng(<?php echo $point["point_center_lat"]?>, <?php echo $point["point_center_lng"]?>));
    pointsAcc.push(<?php echo $point["accuracy"]?>);
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

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(1.370706, 103.852817),
            map: map,
            title: 'Location'
        });
        // Construct the circle for each value in citymap.
        // Note: We scale the population by a factor of 20.
        for (var i =0 ; i<locPoints.length;i++) {
            var circleOptions = {
                strokeColor: '#FF0000',
                strokeOpacity: 0.2,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.25,
                map: map,
                center: locPoints[i],
                radius: 1//locPoints[locPoint].population / 20
            };
            // Add the circle for this city to the map.
            cityCircle = new google.maps.Circle(circleOptions);
        }
        for (var i =0 ; i<pointsArray.length;i++) {
            var circleOptions = {
                strokeColor: '#00FF00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#00FF00',
                fillOpacity: 0.65,
                map: map,
                center: pointsArray[i],
                radius: pointsAcc[i]//locPoints[locPoint].population / 20
            };
            // Add the circle for this city to the map.
            cityCircle = new google.maps.Circle(circleOptions);
        }
    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
</body>
</html>