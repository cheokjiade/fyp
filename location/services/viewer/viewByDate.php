<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
if($_REQUEST['date']){
    $date = $_REQUEST['date'];
    require_once('../../db/conn.php');
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE location_time BETWEEN :dateStart AND :dateEnd");
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $returnArray = $query->fetchAll(PDO::FETCH_ASSOC);
    /*foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row)  {
        $returnArray[]= array("location_lat"=>$row['location_lat'],"location_lng"=>$row['location_lng'],"location_height"=>$row['location_height'],"location_accuracy"=>$row['location_accuracy'],"location_time"=>$row['location_time'],"mid"=>$row['session_hash']);
    }*/
    //Attempt to smooth out errors before returning
    //maximum travel time per second should not be more than 45 meters/sec(144km/hr)
    $maxDistPerSec = 30;
    require_once('../../util/distance.php');
    $smoothedArray = array();
    $lastLat = 0;
    $lastLng = 0;
    $lastTime = 0;
    $tempRowArray = array();
    for($i=0, $size=count($returnArray);$i<$size;++$i){
        if(array_key_exists($returnArray[$i]["session_hash"],$tempRowArray)){
            $tempCurLocationRow = $returnArray[$i];
            $tempPrevLocationRow = $tempRowArray[$returnArray[$i]["session_hash"]];
            //if the distance between the 2 locations is less than timeinterval*40meters add it to the smoothed array
            if(distance($tempPrevLocationRow['location_lat'],$tempPrevLocationRow['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<$maxDistPerSec*(strtotime($tempCurLocationRow['location_time'])-strtotime($tempPrevLocationRow['location_time']))){
                $smoothedArray[]= $returnArray[$i];
                $tempRowArray[$returnArray[$i]["session_hash"]] = $returnArray[$i];
            }
        }else{
            //if it is the fist location of a session, juist add it to the smoothed array
            $smoothedArray[]= $returnArray[$i];
            $tempRowArray[$returnArray[$i]["session_hash"]] = $returnArray[$i];
        }
        //$midArray[]$returnArray[$i][]
    }
    print json_encode($smoothedArray);
    //print_r($returnArray);
    $conn = null;
}
