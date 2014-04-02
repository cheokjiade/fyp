<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 4/3/14
 * Time: 12:33 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');

$sessionHash = $_REQUEST['sessionHash'];
$date = $_REQUEST['date'];

$query = $conn->prepare("SELECT * FROM stoppoint WHERE session_hash = :session_hash AND ((DATE(stoppoint_start_time) = :startDate) OR (DATE(stoppoint_end_time) = :startDate)) ORDER BY stoppoint_start_time;");
$query->bindParam(":session_hash", $sessionHash);
$query->bindParam(":startDate", $date);
$query->execute();
$stopPoints = $query->fetchAll(PDO::FETCH_ASSOC);
$formattedStopPoints = array();
for($i=0;$i<sizeof($stopPoints);$i+=1){
    $tmpDateTime = new DateTime($stopPoints[$i]['stoppoint_start_time']);
    $tmpEndDateTime = new DateTime($stopPoints[$i]['stoppoint_end_time']);
    $dateTime = new DateTime($date);

    if(sizeof($formattedStopPoints)==0){
        $formattedStopPoints[] = array(
            "locationID"=>$stopPoints[$i]['locationpoint_id'],
            "date"=>$dateTime->format('Y-m-d'),
            "startTime"=>"0,0,0,0,0,0",
            "endTime"=>"0,0,0,". ($dateTime->format('d')==$tmpEndDateTime->format('d')?$tmpEndDateTime->format('H,i,s'):"23,59,59"));
    }
    else{
        $formattedStopPoints[] = array(
            "locationID"=>$stopPoints[$i]['locationpoint_id'],
            "date"=>$dateTime->format('Y-m-d'),
            "startTime"=>"0,0,0,".$tmpDateTime->format('H,i,s'),
            "endTime"=>"0,0,0,". ($dateTime->format('d')==$tmpEndDateTime->format('d')?$tmpEndDateTime->format('H,i,s'):"23,59,59"));
    }
}

print json_encode($formattedStopPoints);
$conn = null;