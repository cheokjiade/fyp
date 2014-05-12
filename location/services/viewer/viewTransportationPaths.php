<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 5/12/14
 * Time: 8:39 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');
$sessionHash = $_REQUEST['sessionHash'];
$transportationArray = array();
if(isset($_REQUEST['date'])){
    $date = $_REQUEST['date'];
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT r.*, l.location_lat as lat,l.location_lng as lng  FROM routepoint r, location l WHERE r.session_hash = :session_hash AND r.session_hash = l.session_hash AND r.location_time = l.location_time AND (r.location_time BETWEEN :dateStart AND :dateEnd);");
    $query->bindParam(":session_hash",$sessionHash);
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $transportationDetails = $query->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($transportationDetails);$i+=1){
        if(empty($transportationDetails[$i]["publictransportservices_id"])){
            $transportationDetails[$i]["publictransportservices_id"] = $transportationDetails[$i]["transportation_type"];
        }
    }
    print json_encode($transportationDetails);
    $conn = null;
}