<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$date = '2013-11-30';
require_once('../db/conn.php');
require_once('../util/distance.php');
$dateStart = $date . " 00:00:00";
$dateEnd = $date . " 10:59:59";
$query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE location_time BETWEEN :dateStart AND :dateEnd");
$query->bindParam(":dateStart",$dateStart);
$query->bindParam(":dateEnd",$dateEnd);
$query->execute();
$returnArray = $query->fetchAll(PDO::FETCH_ASSOC);
$actualLat = 1.370706;
$actualLng = 103.85283565217;
$totalDistance = 0;
$distanceArray = array();
for($i=0;$i<sizeof($returnArray);$i+=1){
    $distance = distance($actualLat,$actualLng,$returnArray[$i]['location_lat'],$returnArray[$i]['location_lng']);
    $totalDistance += $distance;
    $distanceArray[] = $distance;
}
asort($distanceArray);
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
    <p>Min: <?php echo $distanceArray[sizeof($returnArray)-1]?></p>
    <p>Max: <?php echo $distanceArray[0]?></p>
    <p>Mean: <?php echo $totalDistance/sizeof($distanceArray)?></p>
    <p>Median: <?php echo $distanceArray[sizeof($returnArray)/2]?></p>
</div>
<div id="map-canvas"/>
<script>
    var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
    var locPoints = [];
    var pointsAcc = [];
    var pathArray = [];
    <?php
    foreach($returnArray as $point){
    ?>
    locPoints.push(new google.maps.LatLng(<?php echo $point["location_lat"]?>, <?php echo $point["location_lng"]?>));
    pointsAcc.push(<?php echo $point["location_accuracy"]?>);
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
        }
    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
</body>
</html>