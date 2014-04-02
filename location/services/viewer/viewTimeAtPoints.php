<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 4/2/14
 * Time: 10:42 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');

$sessionHash = $_REQUEST['sessionHash'];
$returnArray = "";
if(isset($_REQUEST['date'])){
    $date = $_REQUEST['date'];
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT *, SUM(stop_time) AS totaltime FROM
        (SELECT *, time_to_sec(timediff(stoppoint_end_time,stoppoint_start_time))/60 AS stop_time FROM fyp.stoppoint WHERE session_hash = :sessionHash AND ((stoppoint_end_time BETWEEN :dateStart AND :dateEnd) OR (stoppoint_start_time BETWEEN :dateStart AND :dateEnd))) st
        GROUP BY locationpoint_id
        ORDER BY SUM(stop_time) DESC
        LIMIT 10");
    $query->bindParam(":sessionHash",$sessionHash);
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $returnArray = $query->fetchAll(PDO::FETCH_ASSOC);
}else{
    $query = $conn->prepare("SELECT *, SUM(stop_time) AS totaltime FROM
        (SELECT *, time_to_sec(timediff(stoppoint_end_time,stoppoint_start_time))/60 AS stop_time FROM fyp.stoppoint WHERE session_hash = :sessionHash) st
        GROUP BY locationpoint_id
        ORDER BY SUM(stop_time) DESC
        LIMIT 10");
    $query->bindParam(":sessionHash",$sessionHash);
    $query->execute();
    $returnArray = $query->fetchAll(PDO::FETCH_ASSOC);
}
$jsonArray = array();
$jsonArray[] = array("Location Point ID","Minutes Spent");
foreach($returnArray as $location){
    $jsonArray[] = array($location['locationpoint_id'],(int)$location['totaltime']);
}
print json_encode($jsonArray);
$conn = null;