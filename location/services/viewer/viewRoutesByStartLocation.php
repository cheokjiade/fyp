<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/7/14
 * Time: 2:15 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');
require_once('../../util/others.php');

$sessionHash = $_REQUEST['sessionHash'];
$startLocation = is_array($_REQUEST['startLocation'])?$_REQUEST['startLocation'][0]:$_REQUEST['startLocation'];
$returnRouteArray = array();
//get all the routes from the location by the same session_hash
$query = $conn->prepare("SELECT *
    FROM
    (SELECT r.*, s1.locationpoint_id AS locationpoint_id_start, s2.locationpoint_id AS locationpoint_id_end FROM route r, stoppoint s1, stoppoint s2 WHERE s1.locationpoint_id = :locationpoint_id AND r.session_hash = :session_hash AND s1.stoppoint_id = r.stoppoint_id_start AND s2.stoppoint_id = r.stoppoint_id_end) t
    WHERE locationpoint_id_start <> locationpoint_id_end
    ORDER BY session_hash, locationpoint_id_end, route_id;");
$query->bindParam(":session_hash", $sessionHash);
$query->bindParam(":locationpoint_id", $startLocation);
$query->execute();
$rawRoutesArray = $query->fetchAll(PDO::FETCH_ASSOC);
foreach($rawRoutesArray as $route){
    $routeID = $route["route_id"];
    //get all the points that make up each route
    $query = $conn->prepare("SELECT l.* FROM route r, routepoint rp, location l
        WHERE r.route_id = rp.route_id AND rp.session_hash = l.session_hash AND rp.location_time = l.location_time AND r.route_id = :route_id
        ORDER BY l.location_time;");
    $query->bindParam(":route_id", $routeID);
    $query->execute();
    $routeArray = smoothPoints($query->fetchAll(PDO::FETCH_ASSOC));
    //tmp route point
    $tmpRoutePoint = null;
    //array to store all processed route points
    $tmpRoutePointArray = array();
    foreach($routeArray as $routePoint){
        //first point has 0 speed. just load it into array
        if(is_null($tmpRoutePoint)){
            $tmpRoutePoint = $routePoint;
            $tmpRoutePoint['speed'] = 0;
            $tmpRoutePointArray[] = $tmpRoutePoint;
            continue;
        }
        //speed between 2 points
        $speed = distance($tmpRoutePoint['location_lat'],$tmpRoutePoint['location_lng'],$routePoint['location_lat'],$routePoint['location_lng'])/(strtotime($routePoint['location_time'])-strtotime($tmpRoutePoint['location_time']));
        $routePoint['speed'] = $speed;
        $tmpRoutePointArray[] = $tmpRoutePoint;
        $tmpRoutePoint = $routePoint;
    }
    $tmpRoutePointArray[] = $tmpRoutePoint;
    $returnRouteArray[] = $tmpRoutePointArray;
}
echo json_encode($returnRouteArray);
?>
