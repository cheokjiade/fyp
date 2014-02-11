<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 2/10/14
 * Time: 8:51 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
if($conn->query("SHOW TABLES LIKE 'publictransportstops'")->rowCount() > 0){
    $conn->query('DROP TABLE IF EXISTS `fyp`.`publictransportstops`')->execute();
}
$conn->query("CREATE TABLE fyp.publictransportstops
(
  publictransportstops_id varchar(50) NOT NULL UNIQUE,
  publictransportstops_lat double NOT NULL,
  publictransportstops_lng double NOT NULL,
  publictransportstops_description varchar(255),
  publictransportstops_radius double NOT NULL DEFAULT 10,
  primary key (publictransportstops_id)
)ENGINE = MyISAM;");
$busStops = json_decode(file_get_contents("./bus-stops.json"),true);
foreach($busStops as $busStop){
    $rad = 15;
    try{
        $insert = "INSERT INTO publictransportstops(publictransportstops_id, publictransportstops_lat, publictransportstops_lng, publictransportstops_description, publictransportstops_radius)
                          VALUES (:publictransportstops_id, :publictransportstops_lat, :publictransportstops_lng, :publictransportstops_description, :publictransportstops_radius)";
        $query = $conn->prepare($insert);
        $query->bindParam(":publictransportstops_id", $busStop["no"]);
        $query->bindParam(":publictransportstops_lat", $busStop["lat"]);
        $query->bindParam(":publictransportstops_lng", $busStop["lng"]);
        $query->bindParam(":publictransportstops_description", $busStop['name']);
        $query->bindParam(":publictransportstops_radius", $rad);
        $query->execute();
    }
    catch(Exception $e){

    }
}

$busStopsServices = json_decode(file_get_contents("./bus-stops-services.json"),true);

