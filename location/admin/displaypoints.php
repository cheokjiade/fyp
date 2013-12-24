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
$date = '2013-09-30';
$dateStart = $date . " 00:00:00";
$dateEnd = $date . " 23:59:59";
$query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE location_time BETWEEN :dateStart AND :dateEnd");

$query->bindParam(":dateStart",$dateStart);
$query->bindParam(":dateEnd",$dateEnd);
$query->execute();
$returnArray = array();
foreach ($query->fetchAll() as $row)  {
    $returnArray[]= array("location_lat"=>$row['location_lat'],"location_lng"=>$row['location_lng'],"location_height"=>$row['location_height'],"location_accuracy"=>$row['location_accuracy'],"location_time"=>$row['location_time'],"mid"=>$row['session_hash']);
}

require_once('../util/distance.php');
$size=count($returnArray);
//To store latlng points that may be used to define a point
$tmpPoint = array();
//To store the points
$pointsArray = array();

$currTmpPointLat = 0;
$currTmpPointLng = 0;

for($i=0;$i<$size;++$i){
    $tempCurLocationRow = $returnArray[$i];
    $closenessCounter = 0;
    if($i<5){
        for($tempCounter = $i;$tempCounter<$i+12;$tempCounter++){
            if(distance($returnArray[$tempCounter]['location_lat'],$returnArray[$tempCounter]['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<($tempCurLocationRow['location_accuracy']+$returnArray[$tempCounter]['location_accuracy'])*1.3){
                $closenessCounter++;
            }
        }
    }
    elseif($size-$i<12){
        for($tempCounter = $i-12;$tempCounter<$i;$tempCounter++){
            if(distance($returnArray[$tempCounter]['location_lat'],$returnArray[$tempCounter]['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<($tempCurLocationRow['location_accuracy']+$returnArray[$tempCounter]['location_accuracy'])*1.3){
                $closenessCounter++;
            }
        }
    }
    else{
        for($tempCounter = $i-5;$tempCounter<$i+6;$tempCounter++){
            if(distance($returnArray[$tempCounter]['location_lat'],$returnArray[$tempCounter]['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<($tempCurLocationRow['location_accuracy']+$returnArray[$tempCounter]['location_accuracy'])*1.3){
                $closenessCounter++;
            }
        }
    }
    if($closenessCounter>8){
        $tmpPoint[] = $tempCurLocationRow;

    }
    else{
        if(count($tmpPoint)>0){
            if(count($tmpPoint)>12){
                $tmpPointSize = count($tmpPoint);

                $totalAcc = 0;
                $totalLat = 0;
                $totalLng = 0;

                $avgAcc = 0;
                $avgLat = 0;
                $avgLng = 0;

                foreach($tmpPoint as $tmpRow){
                    $totalLat += $tmpRow['location_lat'];
                    $totalLng += $tmpRow['location_lng'];
                    $totalAcc += $tmpRow['location_accuracy'];
                }

                $avgLat = $totalLat/$tmpPointSize;
                $avgLng = $totalLng/$tmpPointSize;
                $avgAcc = $totalAcc/$tmpPointSize;
                //only register points under a certain accuracy
                if($avgAcc<100){
                    $pointsArray[] = array("start_time"=>$tmpPoint[0]['location_time'],"end_time"=>$tmpPoint[$tmpPointSize-1]['location_time'],"point_center_lat"=>$avgLat,"point_center_lng"=>$avgLng,"accuracy"=>$avgAcc);
                }

            }

            $tmpPoint = array();
        }

    }

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
    <link rel="stylesheet/less" type="text/css" href="/styles/styles.less" />
    <script src="/scripts/less-1.4.1.min.js" type="text/javascript"></script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5AN7Cbf3jecSlyOUHNNoCPE1ZJc6wGEw&sensor=true">
    </script>
    <script src="/scripts/jquery-2.0.3.min.js" type="text/javascript"></script>
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
            </tr>
            <?php
            foreach($pointsArray as $point){
            ?>
            <tr>
                <td><?php echo $point["start_time"]?></td>
                <td><?php echo $point["end_time"]?></td>
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
        var locPoints = [];
        var pointsAcc = [];
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
                zoom: 11,
                center: new google.maps.LatLng(1.37081484, 103.85283565217),
                mapTypeId: google.maps.MapTypeId.TERRAIN
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
