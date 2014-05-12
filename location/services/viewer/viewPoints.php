<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 4/2/14
 * Time: 11:04 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');
require_once('../../util/others.php');
$sessionHash = $_REQUEST['sessionHash'];
$returnArray = "";
if(isset($_REQUEST['date'])){
    $date = $_REQUEST['date'];
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT *,stoppoint_center_lat as lat,stoppoint_center_lng as lng, stoppoint_accuracy as acc, SUM(stop_time) AS totaltime, COUNT(stop_time) AS count FROM
        (SELECT *, time_to_sec(timediff(stoppoint_end_time,stoppoint_start_time))/60 AS stop_time FROM fyp.stoppoint WHERE session_hash = :sessionHash AND ((stoppoint_end_time BETWEEN :dateStart AND :dateEnd) OR (stoppoint_start_time BETWEEN :dateStart AND :dateEnd))) st
        GROUP BY locationpoint_id
        ORDER BY SUM(stop_time) DESC");
    $query->bindParam(":sessionHash",$sessionHash);
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $returnArray = smoothPoints($query->fetchAll(PDO::FETCH_ASSOC));

}else{
    $query = $conn->prepare("SELECT *,stoppoint_center_lat as lat,stoppoint_center_lng as lng, stoppoint_accuracy as acc, COUNT(stop_time) AS count, SUM(stop_time) AS totaltime FROM
        (SELECT *, time_to_sec(timediff(stoppoint_end_time,stoppoint_start_time))/60 AS stop_time FROM fyp.stoppoint WHERE session_hash = :sessionHash) st
        GROUP BY locationpoint_id
        ORDER BY SUM(stop_time) DESC");
    $query->bindParam(":sessionHash",$sessionHash);
    $query->execute();
    $returnArray = smoothPoints($query->fetchAll(PDO::FETCH_ASSOC));
}
print json_encode($returnArray);
$conn = null;