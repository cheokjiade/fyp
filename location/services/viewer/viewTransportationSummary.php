<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 5/11/14
 * Time: 8:00 PM
 * To change this template use File | Settings | File Templates.
 * SELECT transportation_type,publictransportservices_id, count(*) FROM
(
SELECT * FROM fyp.routepoint where session_hash = "ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3" and transportation_type <> "Walking" group by route_id,transportation_type,publictransportservices_id
) tmp GROUP BY tmp.transportation_type, tmp.publictransportservices_id
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');
$sessionHash = $_REQUEST['sessionHash'];
if(isset($_REQUEST['date'])){
    $date = $_REQUEST['date'];
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT transportation_type,publictransportservices_id, count(*) AS count FROM (SELECT * FROM fyp.routepoint WHERE session_hash = :session_hash AND transportation_type <> \"Walking\" AND transportation_type <> \"Unknown\" AND transportation_type <> \"Vehicle\" AND (location_time BETWEEN :dateStart AND :dateEnd) GROUP BY route_id,transportation_type,publictransportservices_id) tmp GROUP BY tmp.transportation_type, tmp.publictransportservices_id;");
    $query->bindParam(":session_hash",$sessionHash);
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $transportationSummary = $query->fetchAll(PDO::FETCH_ASSOC);

}else{
    $query = $conn->prepare("SELECT transportation_type,publictransportservices_id, count(*) AS count FROM (SELECT * FROM fyp.routepoint WHERE session_hash = :session_hash AND transportation_type <> \"Walking\" AND transportation_type <> \"Unknown\" AND transportation_type <> \"Vehicle\" group by route_id,transportation_type,publictransportservices_id) tmp GROUP BY tmp.transportation_type, tmp.publictransportservices_id;");
    $query->bindParam(":session_hash",$sessionHash);
    $query->execute();
    $transportationSummary = $query->fetchAll(PDO::FETCH_ASSOC);
}
print json_encode($transportationSummary);
$conn = null;