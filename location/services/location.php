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
        try{
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
            if(array_key_exists('objActivity',$item)){
                $objActivity = $item['objActivity'];
                $insert = "INSERT INTO humanactivity(session_hash, location_time, humanactivity_probableactivity, humanactivity_probableactivityconfidence)
                    VALUES (:session_hash, :location_time, :humanactivity_probableactivity, :humanactivity_probableactivityconfidence)";
                $query = $conn->prepare($insert);
                $query->bindParam(":session_hash", $sessionHash);
                $query->bindParam(":location_time", date('Y-m-d H:i:s',strtotime($item['locationTimeStamp'])));
                $query->bindParam(":humanactivity_probableactivity", $objActivity['activityProbableActivity']);
                $query->bindParam(":humanactivity_probableactivityconfidence", $objActivity['activityProbableActivityConfidence']);
                $query->execute();
            }
            if(array_key_exists('objSMS',$item)){
                $objSMS = $item['objSMS'];
                $insert = "INSERT INTO sms(session_hash, location_time, sms_number, sms_isadv, sms_length, sms_incomming)
                    VALUES (:session_hash, :location_time, :SMSNumber, :SMSisADV, :SMSlength, :SMSIO)";
                $query = $conn->prepare($insert);
                $query->bindParam(":session_hash", $sessionHash);
                $query->bindParam(":location_time", date('Y-m-d H:i:s',strtotime($item['locationTimeStamp'])));
                $query->bindParam(":SMSNumber", $objSMS['SMSNumber']);
                $query->bindParam(":SMSisADV", $objSMS['SMSisADV']);
                $query->bindParam(":SMSlength", $objSMS['SMSlength']);
                $query->bindParam(":SMSIO", $objSMS['SMSIO']);
                $query->execute();
            }
        }catch (Exception $e){
            $query = $conn->prepare("SELECT * FROM location WHERE session_hash = :session_hash ORDER BY location_time DESC LIMIT 1");
            $query->bindParam(":session_hash", $sessionHash);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $lastTime = $result['location_time'];
            break;
        }

//        $count++;
//        if(array_key_exists('objSMS',$item))
//            $countSMS++;
    }
    print date('d M Y H:i:s', strtotime($lastTime));
    $conn = null;

?>
