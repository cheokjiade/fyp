<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 12/17/13
 * Time: 8:48 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
require_once('../util/others.php');

$date = '2013-09-30';
$dateStart = $date . " 00:00:00";
$dateEnd = '2013-10-05' . " 23:59:59";
$sessionHash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3';
$query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE location_time BETWEEN :dateStart AND :dateEnd AND session_hash = :sessionHash");

$query->bindParam(":dateStart",$dateStart);
$query->bindParam(":dateEnd",$dateEnd);
$query->bindParam(":sessionHash",$sessionHash);
$query->execute();
$returnArray = $query->fetchAll(PDO::FETCH_ASSOC);//array();
//foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row)  {
//    $returnArray[]= array("location_lat"=>$row['location_lat'],"location_lng"=>$row['location_lng'],"location_height"=>$row['location_height'],"location_accuracy"=>$row['location_accuracy'],"location_time"=>$row['location_time'],"mid"=>$row['session_hash']);
//}

//smooth the array
$smoothedArray = smoothPoints($returnArray);
//then add them into a points array
$pointsArray = mergePoints(retrievePointsFromLocations($smoothedArray));
//$pointsArray = retrievePointsFromLocations($smoothedArray);

$numPoints = count($pointsArray);
$pathArray = array();
for ($i=0;$i<$numPoints-2;$i++){
    $query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE location_time BETWEEN :dateStart AND :dateEnd AND session_hash = :sessionHash ORDER BY location_time");

    $query->bindParam(":dateStart",$pointsArray[$i]["end_time"]);
    $query->bindParam(":dateEnd",$pointsArray[$i+1]["start_time"]);
    $query->bindParam(":sessionHash",$sessionHash);
    $query->execute();
    $pathArray[]=smoothPoints($query->fetchAll(PDO::FETCH_ASSOC));
}
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
                <th>Start Time</th>
                <th>End Time</th>
                <th>Lat</th>
                <th>Lng</th>
                <th>Accuracy</th>
                <th>Time Spent</th>
            </tr>
            <?php
            $totalMinutes = "";
            foreach($pointsArray as $point){
            ?>
            <tr>
                <td><?php echo $point["start_time"]?></td>
                <td><?php echo $point["end_time"]?></td>
                <td><?php echo $point["point_center_lat"]?></td>
                <td><?php echo $point["point_center_lng"]?></td>
                <td><?php echo $point["accuracy"]?></td>
                <td><?php
                    $timeDiff = timeDifference($point["start_time"],$point["end_time"]);
                    $totalMinutes+= $timeDiff;
                    echo $timeDiff ?> minutes</td>
            </tr>
            <?php
            }
            ?>
            <p><?php echo $totalMinutes . " minutes in a single location out of " . timeDifference($dateStart,$dateEnd) . " total minutes" ?></p>
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
        $numPaths = count($pathArray);
        for($i=0;$i<$numPaths;$i++){
        ?>
        pathArray[<?php echo $i ?>] = new Array();
        <?php
            foreach($pathArray[$i] as $path){
        ?>
        pathArray[<?php echo $i ?>].push(new google.maps.LatLng(<?php echo $path["location_lat"]?>, <?php echo $path["location_lng"]?>));
        <?php
            }
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
            for(var i =0 ; pathArray.length;i++){
                var tmpPath= new google.maps.Polyline({
                    path: pathArray[i],
                    map: map,
                    strokeColor: colors[i%colors.length],
                    strokeOpacity: 0.7,
                    strokeWeight: 2
                });
                //path[i].setPath(pathCoordinates[i]);
                //path[i].setMap(map);
                //path.push(tmpPath);
            }
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</body>
</html>
