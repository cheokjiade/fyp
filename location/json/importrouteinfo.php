<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/22/14
 * Time: 3:09 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');

if ($handle = opendir('./busroutes')) {
    echo "Directory handle: $handle\n";
    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        set_time_limit(20);
        $busID = str_replace('.json',"",$entry);
        echo str_replace('.json',"",$entry) . "\n";
        $busRouteInfo = json_decode(file_get_contents('./busroutes/'. $entry),true);
        $busRouteID = 1;
        foreach($busRouteInfo as $routeInfo){
            if(($routeSize = sizeof($routeInfo['route']))>0){
                for($i=0;$i<$routeSize;$i+=1){
                    $latlng = explode(',',$routeInfo['route'][$i]);
                    $insert = "INSERT INTO publictransportserviceroutepoints(publictransportservices_id, publictransportservices_route_id, publictransportserviceroutepoints_order, publictransportserviceroutepoints_lat, publictransportserviceroutepoints_lng)
                          VALUES (:publictransportservices_id, :publictransportservices_route_id, :publictransportserviceroutepoints_order, :publictransportserviceroutepoints_lat, :publictransportserviceroutepoints_lng)";
                    $query = $conn->prepare($insert);
                    $query->bindParam(":publictransportservices_id", $busID);
                    $query->bindParam(":publictransportservices_route_id", $busRouteID);
                    $query->bindParam(":publictransportserviceroutepoints_order", $i);
                    $query->bindParam(":publictransportserviceroutepoints_lat", $latlng[0]);
                    $query->bindParam(":publictransportserviceroutepoints_lng", $latlng[1]);
                    $query->execute();
                    echo $i.'i';
                }
            }
            if(($stops = sizeof($routeInfo['stops']))>0){
                for($i=0;$i<$stops;$i+=1){
                    $insert = "INSERT INTO publictransportservicestops(publictransportservices_id, publictransportservices_route_id, publictransportservicestops_order, publictransportstops_id)
                          VALUES (:publictransportservices_id, :publictransportservices_route_id, :publictransportservicestops_order, :publictransportstops_id)";
                    $query = $conn->prepare($insert);
                    $query->bindParam(":publictransportservices_id", $busID);
                    $query->bindParam(":publictransportservicestops_order", $i);
                    $query->bindParam(":publictransportservices_route_id", $busRouteID);
                    $query->bindParam(":publictransportstops_id", $routeInfo['stops'][$i]);
                    $query->execute();
                }
            }
            $busRouteID+=1;
        }
    }

    closedir($handle);
}