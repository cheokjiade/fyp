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
$transportationArray = array();
if(isset($_REQUEST['date'])){
    $date = $_REQUEST['date'];
    $dateStart = $date . " 00:00:00";
    $dateEnd = $date . " 23:59:59";
    $query = $conn->prepare("SELECT * FROM fyp.routepoint WHERE session_hash = :session_hash AND (location_time BETWEEN :dateStart AND :dateEnd);");
    $query->bindParam(":session_hash",$sessionHash);
    $query->bindParam(":dateStart",$dateStart);
    $query->bindParam(":dateEnd",$dateEnd);
    $query->execute();
    $transportationDetails = $query->fetchAll(PDO::FETCH_ASSOC);

    //print_r($transportationDetails);
    if(count($transportationDetails)>0){
        $routeID = $transportationDetails[0]['route_id'];
        $currentStartTime = $transportationDetails[0]['location_time'];
        $transportationMode = empty($transportationDetails[0]['transportation_type'])?"Unknown":$transportationDetails[0]['transportation_type'];
        $transportationService = "";
        if(empty($transportationDetails[0]['transportation_type'])){
            $transportationService="Unknown";
        }
        elseif($transportationDetails[0]['transportation_type']=="Walking"){
            $transportationService="Walking";
        }elseif($transportationDetails[0]['transportation_type']=="Vehicle"){
            $transportationService="Vehicle";
        }elseif($transportationDetails[0]['transportation_type']=="Unknown"){
            $transportationService="Unknown";
        }
        else $transportationService = $transportationDetails[0]['publictransportservices_id'];


        for($i=0;$i<count($transportationDetails);$i+=1){
            $tmpTransportService = "";
            if(empty($transportationDetails[$i]['transportation_type'])){
                $transportationService="Unknown";
            }
            elseif($transportationDetails[$i]['transportation_type']=="Walking"){
                $tmpTransportService="Walking";
            }elseif($transportationDetails[$i]['transportation_type']=="Vehicle"){
                $tmpTransportService="Vehicle";
            }elseif($transportationDetails[$i]['transportation_type']=="Unknown"){
                $tmpTransportService="Unknown";
            }else $tmpTransportService = $transportationDetails[$i]['publictransportservices_id'];

            $tmpTransportDetails = empty($transportationDetails[$i]['transportation_type'])?"Unknown":$transportationDetails[$i]['transportation_type'];

            if($routeID != $transportationDetails[$i]['route_id'] || $transportationMode != $tmpTransportDetails || $transportationService != $tmpTransportService || $i == (count($transportationDetails)-1)){
                $startTime = strtotime($currentStartTime);
                $endTime =   strtotime($transportationDetails[$i-1]['location_time']);
                $transportationArray[] = array("route_id"=>$routeID, "transport_id"=>$transportationService, "start"=>date("H,i,s",$startTime), "end"=>date("H,i,s",$endTime));
                $routeID = $transportationDetails[$i]['route_id'];
                $transportationMode = empty($transportationDetails[$i]['transportation_type'])?"Unknown":$transportationDetails[$i]['transportation_type'];
                $transportationService = $tmpTransportService;
                $currentStartTime = $transportationDetails[$i]['location_time'];
            }
        }
    }

}
//print_r($transportationArray);
print json_encode($transportationArray);
$conn = null;