<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
if($_REQUEST['date']){
    $date = $_REQUEST['date'];
    require_once('../../db/conn.php');
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT location_lat, location_lng,	location_height, location_accuracy, location_time FROM location WHERE location_time BETWEEN :dateStart AND :dateEnd");
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $returnArray = array();
    foreach ($query->fetchAll() as $row)  {
        $returnArray[]= array("location_lat"=>$row['location_lat'],"location_lng"=>$row['location_lng'],"location_height"=>$row['location_height'],"location_accuracy"=>$row['location_accuracy'],"location_time"=>$row['location_time']);
    }
    print json_encode($returnArray);
    //print_r($returnArray);
    $conn = null;
}
