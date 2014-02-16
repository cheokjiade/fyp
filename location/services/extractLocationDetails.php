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

    $query = $conn->prepare("SELECT DISTINCT session_hash FROM location WHERE CAST(`location_time` AS DATE ) = :date;");
    $query->bindParam(":date", $date["uniqueDate"]);
    foreach($query->fetch(PDO::FETCH_ASSOC) as $sessionHash){
        $query = $conn->prepare("SELECT * FROM location WHERE CAST(`location_time` AS DATE ) = :date AND session_hash = :session_hash;");
        $query->bindParam(":date", $date["uniqueDate"]);
        $query->bindParam(":session_hash", $sessionHash["session_hash"]);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        //smooth the array
        $smoothedArray = smoothPoints($returnArray);
        //then add them into a points array
        $pointsArray = mergePoints(retrievePointsFromLocations($smoothedArray));


    }

}