<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 2/21/14
 * Time: 10:56 PM
 * To change this template use File | Settings | File Templates.
 *
 * SELECT l.*, s.count FROM
(SELECT locationpoint_id, count(locationpoint_id) as count FROM fyp.stoppoint GROUP BY locationpoint_id ORDER BY count desc) s
INNER JOIN
locationpoint l
ON s.locationpoint_id = l.locationpoint_id
ORDER BY s.count DESC
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
require_once('../util/others.php');

$query = $conn->prepare("SELECT l.locationpoint_center_lat AS point_center_lat, l.locationpoint_center_lng AS point_center_lng, s.count as accuracy FROM
(SELECT locationpoint_id, count(locationpoint_id) as count FROM fyp.stoppoint GROUP BY locationpoint_id ORDER BY count desc) s
INNER JOIN
locationpoint l
ON s.locationpoint_id = l.locationpoint_id
ORDER BY s.count DESC");
$query->execute();
$pointsArray = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Point Viewer</title>
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
<div id="left">
    <table>
        <tr>
            <th>Lat</th>
            <th>Lng</th>
            <th>Accuracy</th>
        </tr>
        <?php
        foreach($pointsArray as $point){
            ?>
            <tr>
                <td><?php echo $point["point_center_lat"]?></td>
                <td><?php echo $point["point_center_lng"]?></td>
                <td><?php echo $point["accuracy"]?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<div id="map-canvas"/>
<script>
    var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
    var locPoints = [];
    var pointsAcc = [];
    var pathArray = [];
    <?php
    foreach($pointsArray as $point){
    ?>
    locPoints.push(new google.maps.LatLng(<?php echo $point["point_center_lat"]?>, <?php echo $point["point_center_lng"]?>));
    pointsAcc.push(<?php echo $point["accuracy"]?>);
    <?php
    }
    ?>

    function initialize() {
        // Create the map.
        var mapOptions = {
            zoom: 13,
            center: new google.maps.LatLng(1.37081484, 103.85283565217),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

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