<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 4/2/14
 * Time: 9:02 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');
$userName = $_REQUEST['username'];
$passWord = $_REQUEST['password'];
$sessionHash = "";

$query = $conn->prepare("SELECT s.session_hash FROM userdata u, session s WHERE s.userdata_id = u.userdata_id AND u.userdata_email = :userdata_email AND u.userdata_password = :userdata_password ORDER BY s.session_timestamp DESC");
$query->bindParam(":userdata_email",$userName);
$query->bindParam(":userdata_password",$passWord);
$query->execute();
$sessionArray = $query->fetchAll(PDO::FETCH_ASSOC);
if(sizeof($sessionArray)>0){
    $sessionHash = $sessionArray[0]['session_hash'];
}

$query = $conn->prepare("SELECT DISTINCT DATE(stoppoint_start_time) AS udate,DAYNAME(stoppoint_start_time) AS uname FROM stoppoint WHERE session_hash = :session_hash ORDER BY stoppoint_start_time ;");
$query->bindParam(":session_hash",$sessionHash);
$query->execute();
$uniqueDates = $query->fetchAll(PDO::FETCH_ASSOC);
$model = array("Monday"=>[],"Tuesday"=>[],"Wednesday"=>[],"Thursday"=>[],"Friday"=>[],"Saturday"=>[],"Sunday"=>[]);

$date = $uniqueDates[0]['udate'];
$endDate = $uniqueDates[count($uniqueDates)-1]['udate'];
while (strtotime($date) <= strtotime($endDate)) {
    for($i=0;$i<24;$i+=1){
        if($i<10) $time = $date ." 0" . $i . ":00:00";
        else $time = $date . " " . $i . ":00:00";
        //SELECT * FROM stoppoint WHERE ( :time BETWEEN stoppoint_start_time AND stoppoint_end_time) AND session_hash = :session_hash
        $query = $conn->prepare("SELECT * FROM stoppoint WHERE ( :time BETWEEN stoppoint_start_time AND stoppoint_end_time) AND session_hash = :session_hash;");
        $query->bindParam(":session_hash",$sessionHash);
        $query->bindParam(":time",$time);
        $query->execute();
        $stopPoint = $query->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($stopPoint) && array_key_exists("locationpoint_id",$stopPoint[0])){
            $dayName = date('l', strtotime($date));
            if(!array_key_exists($i,$model[$dayName])){
                $model[$dayName][$i] = array(/*"dates"=>array()*/);
            }
            if(!array_key_exists($stopPoint[0]['locationpoint_id'],$model[$dayName][$i])){
                $model[$dayName][$i][$stopPoint[0]['locationpoint_id']] = 1;
            }else {
                $model[$dayName][$i][$stopPoint[0]['locationpoint_id']] = $model[$dayName][$i][$stopPoint[0]['locationpoint_id']] +1;
            }
            arsort($model[$dayName][$i]);
            //$model[$dayName][$i]['dates'][]=$date;

        }
        //echo $time;

    }
    $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
}
//print_r($model);
print json_encode($model);
$conn = null;

//foreach($uniqueDates as $uniqueDate){
//    for($i=0;$i<24;$i+=1){
//        if($i<10) $time = $uniqueDate['udate'] ." 0" . $i . ":00:00";
//        else $time = $uniqueDate['udate'] . " " . $i . ":00:00";
//       //SELECT * FROM stoppoint WHERE ( :time BETWEEN stoppoint_start_time AND stoppoint_end_time) AND session_hash = :session_hash
//        $query = $conn->prepare("SELECT * FROM stoppoint WHERE ( :time BETWEEN stoppoint_start_time AND stoppoint_end_time) AND session_hash = :session_hash;");
//        $query->bindParam(":session_hash",$sessionHash);
//        $query->bindParam(":time",$time);
//        $query->execute();
//        $stopPoint = $query->fetchAll(PDO::FETCH_ASSOC);
//        echo $time;
//        print_r($stopPoint);
//    }
//}
?>