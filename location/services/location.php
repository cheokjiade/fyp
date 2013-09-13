<?php
/**
 * Created by IntelliJ IDEA.
 * User: VMprogramming
 * Date: 5/24/13
 * Time: 9:06 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
    require_once('../db/conn.php');
    $jsonString = $_REQUEST['location'];
    $jsonObj = json_decode($jsonString,true);
    $sessionHash = $jsonObj['deviceHash'];
    $jsonArray = $jsonObj['locations'];
    $lastTime = '';
    foreach($jsonArray as $item){
        $insert = "INSERT INTO location(session_hash, location_lat, location_lng, location_height, location_accuracy, location_time)
                    VALUES (:session_hash, :location_lat, :location_lng, :location_height, :location_accuracy, :location_time)";
        $query = $conn->prepare($insert);
        $query->bindParam(":session_hash", $sessionHash);
        $query->bindParam(":location_lat", $item['locationLat']);
        $query->bindParam(":location_lng", $item['locationLng']);
        $query->bindParam(":location_height", $item['locationAlt']);
        $query->bindParam(":location_accuracy", $item['locationAcc']);
        $query->bindParam(":location_time", date('Y-m-d H:i:s',strtotime($item['locationTimeStamp'])));
        $query->execute();
        $lastTime = $item['locationTimeStamp'];
//        $count++;
//        if(array_key_exists('objSMS',$item))
//            $countSMS++;
    }
    print date('d M Y H:i:s', strtotime($lastTime));
    $conn = null;

?>
