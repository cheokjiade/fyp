<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 5/11/14
 * Time: 12:48 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');


require_once('../db/conn.php');
require_once('../util/others.php');
$query = $conn->prepare("SELECT * FROM route");
//$query->bindParam(":route_id",$route_id);
$query->execute();
$routes = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $conn->prepare("SELECT publictransportstops_id, publictransportstops_lat AS lat, publictransportstops_lng AS lng, publictransportstops_radius AS radius FROM publictransportstops;;");
$query->execute();
$publicTransportStops = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($routes as $route){
    set_time_limit(60);
    $query = $conn->prepare("SELECT l.* FROM location l, routepoint rp WHERE l.session_hash = rp.session_hash AND l.location_time = rp.location_time AND rp.route_id = :route_id ORDER BY l.location_time");
    $query->bindParam(":route_id",$route['route_id']);
    $query->execute();
    $routePoints = smoothPoints($query->fetchAll(PDO::FETCH_ASSOC));
    $totalDistance= totalDistance($routePoints);
    $tripTimeLength = timeDifference($routePoints[0]['location_time'],$routePoints[count($routePoints)-1]['location_time']);
    if($tripTimeLength ==0) echo $route['route_id'] . " ";
    $speedArray = array();

    //no trip exceeds 200 minutes
    if($tripTimeLength > 180){
        $query = $conn->prepare("UPDATE routepoint SET transportation_type = \"Unknown\" WHERE route_id = :route_id ;");
        $query->bindParam(":route_id",$route['route_id']);
        $query->execute();
        continue;
    }
    elseif($tripTimeLength ==0 || ($totalDistance/$tripTimeLength)<6 ){
            $query = $conn->prepare("UPDATE routepoint SET transportation_type = \"Walking\" WHERE route_id = :route_id ;");
            $query->bindParam(":route_id",$route['route_id']);
            $query->execute();
            continue;

    }
    $busStopArray = array();
    for($i=0;$i<sizeof($routePoints)-1;$i+=1){
        //find bus stops along path
        foreach($publicTransportStops as $stop){
            if($routePoints[$i]!=$routePoints[$i+1]['location_lat'] && $routePoints[$i]['location_lng']!=$routePoints[$i+1]['location_lng']){
                $distance = get_geo_distance_point_to_segment(
                    array("lat"=>$routePoints[$i]['location_lat'],"lng"=>$routePoints[$i]['location_lng']),
                    array("lat"=>$routePoints[$i+1]['location_lat'],"lng"=>$routePoints[$i+1]['location_lng']),
                    array("lat"=>$stop['lat'],"lng"=>$stop['lng']));

                //add bus stops in range to bus stop array
                if($distance <= (((($routePoints[$i]['location_accuracy']+$routePoints[$i+1]['location_accuracy'])/2)+$stop['radius']))){
                    if(!in_array($stop['publictransportstops_id'],$busStopArray)){
                        $busStopArray[] =  $stop['publictransportstops_id'];
                        //echo $stop['publictransportstops_id'] . ' ';
                    }
                }
            }
        }
    }
    if(count($busStopArray)<2){
        $query = $conn->prepare("UPDATE routepoint SET transportation_type = \"Walking\" WHERE route_id = :route_id;");
        $query->bindParam(":route_id",$route['route_id']);
        $query->execute();
    }
}