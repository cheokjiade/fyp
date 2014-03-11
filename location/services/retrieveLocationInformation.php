<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/11/14
 * Time: 10:11 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
require_once('../util/parameters.php');

//get the locations that need to be queried
$query = $conn->prepare("SELECT * FROM locationpoint WHERE locationpoint_id NOT IN (SELECT DISTINCT locationpoint_id FROM locationandtags);");
$query->execute();
$locations = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($locations as $location){
    set_time_limit(30);
    $request = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $location["locationpoint_center_lat"] . "," . $location["locationpoint_center_lng"] . "&sensor=true&key=" . $reverse_geocoding_api_key;
    $session = curl_init($request);
    curl_setopt($session, CURLOPT_CAINFO, "C:\\xampp\\htdocs\\fyp\\util\\cacert.pem");
    curl_setopt($session,CURLOPT_HEADER,false);
    curl_setopt($session,CURLOPT_RETURNTRANSFER,true);
    //$response = ;
    //echo curl_error($session);
    $jsonResponse = json_decode(curl_exec($session),true);
    curl_close($session);

    if(array_key_exists("results", $jsonResponse)){
        $query = $conn->prepare("UPDATE locationpoint SET locationpoint_description = :locationpoint_description WHERE locationpoint_id = :locationpoint_id;");
        $query->bindParam(":locationpoint_description", $jsonResponse["results"][1]["formatted_address"]);
        $query->bindParam(":locationpoint_id", $location["locationpoint_id"]);
        $query->execute();

        foreach($jsonResponse["results"][1]["types"] as $type){
            //add the tag if it does not exist
            $query = $conn->prepare("INSERT IGNORE INTO locationtag(locationtag_text) VALUES(:locationtag_text);");
            $query->bindParam(":locationtag_text", $type);
            $query->execute();
            //get the tag id
            $query = $conn->prepare("SELECT * FROM locationtag WHERE locationtag_text = :locationtag_text;");
            $query->bindParam(":locationtag_text", $type);
            $query->execute();
            $locationTag = $query->fetch(PDO::FETCH_ASSOC);
            //insert into the many-many table (tag-location)
            $query = $conn->prepare("INSERT INTO locationandtags(locationtag_id, locationpoint_id) VALUES(:locationtag_id, :locationpoint_id);");
            $query->bindParam(":locationtag_id", $locationTag["locationtag_id"]);
            $query->bindParam(":locationpoint_id", $location["locationpoint_id"]);
            $query->execute();
            echo 'added';
        }
    }
    else{
        echo "fail\n".$request."\n".$jsonResponse;

        //print_r($jsonResponse);
    }
}
