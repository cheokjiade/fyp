<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 2/12/14
 * Time: 1:03 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
require_once("../util/others.php");
$query = $conn->prepare("SELECT DISTINCT CAST(`location_time` AS DATE ) AS uniqueDate FROM location ORDER BY uniqueDate LIMIT 1;");
$query->execute();
//$result = $query->fetch(PDO::FETCH_ASSOC);

foreach ($query->fetch(PDO::FETCH_ASSOC) as $row) {

}

$query = $conn->prepare("SELECT COUNT(*) AS Count FROM stoppoint");
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);
if($result['Count'] == 0){
    echo "no rows found";
    $query = $conn->prepare("SELECT DISTINCT CAST(`location_time` AS DATE ) AS uniqueDate FROM location ORDER BY uniqueDate LIMIT 1;");
    $query->execute();
    $date = $query->fetch(PDO::FETCH_ASSOC);
    print_r($date);
    echo $date["uniqueDate"];
    $query = $conn->prepare("SELECT DISTINCT session_hash FROM location WHERE location_time BETWEEN :udate AND :udate + INTERVAL 1 WEEK;");
    $query->bindParam(":udate", $date["uniqueDate"]);
    $query->execute();
    $sessionHashes = $query->fetchAll(PDO::FETCH_ASSOC);
    print_r($sessionHashes);

    foreach($sessionHashes as $sessionHash){
        set_time_limit(100);
        $query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time, session_hash FROM location WHERE location_time BETWEEN :udate AND :udate + INTERVAL 1 WEEK AND session_hash = :session_hash ORDER BY location_time;");
        $query->bindParam(":udate", $date["uniqueDate"]);
        $query->bindParam(":session_hash", $sessionHash["session_hash"]);
        $query->execute();
        $returnedArray = $query->fetchAll(PDO::FETCH_ASSOC);
        //smooth the array
        $smoothedArray = smoothPoints($returnedArray);
        $basicPointsArray = retrievePointsFromLocations($smoothedArray);
        //then add them into a points array
        $pointsArray = mergePoints($basicPointsArray);
        foreach($pointsArray as $point){
            echo "p";
            $query = $conn->prepare("SELECT *, distance(:lat1,:lng1,locationpoint_center_lat,locationpoint_center_lng) AS distance FROM locationpoint HAVING distance <0.020 ORDER BY distance;");
            $query->bindParam(":lat1", $point["point_center_lat"]);        //point_center_lat
            $query->bindParam(":lng1", $point["point_center_lng"]);
           // $query->bindParam(":session_hash", $sessionHash["session_hash"]);
            $query->execute();
            $locationPoint = $query->fetch(PDO::FETCH_ASSOC);
            $lastInsertID = null;
            //print_r($locationPoint);
            if(empty($locationPoint)){
                $query = $conn->prepare("INSERT INTO locationpoint(locationpoint_center_lat, locationpoint_center_lng)
                    VALUES(:locationpoint_center_lat, :locationpoint_center_lng);");
                $query->bindParam(":locationpoint_center_lat", $point["point_center_lat"]);        //point_center_lat
                $query->bindParam(":locationpoint_center_lng", $point["point_center_lng"]);
                // $query->bindParam(":session_hash", $sessionHash["session_hash"]);
                $query->execute();
                $lastInsertID = $conn->lastInsertId();
            }
            else {
                $lastInsertID = $locationPoint["locationpoint_id"];
            }
            $query = $conn->prepare("INSERT INTO stoppoint(session_hash, locationpoint_id, stoppoint_start_time, stoppoint_end_time, stoppoint_center_lat, stoppoint_center_lng, stoppoint_accuracy)
                    VALUES(:session_hash, :locationpoint_id, :stoppoint_start_time, :stoppoint_end_time, :stoppoint_center_lat, :stoppoint_center_lng, :stoppoint_accuracy);");
            $query->bindParam(":session_hash", $sessionHash["session_hash"]);        //point_center_lat
            $query->bindParam(":locationpoint_id", $lastInsertID);
            $query->bindParam(":stoppoint_start_time", $point["start_time"]);
            $query->bindParam(":stoppoint_end_time", $point["end_time"]);
            $query->bindParam(":stoppoint_center_lat", $point["point_center_lat"]);
            $query->bindParam(":stoppoint_center_lng", $point["point_center_lng"]);
            $query->bindParam(":stoppoint_accuracy", $point["accuracy"]);
            // $query->bindParam(":session_hash", $sessionHash["session_hash"]);
            $query->execute();
        }

    }



}