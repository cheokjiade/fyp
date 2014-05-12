<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 5/11/14
 * Time: 8:02 PM
 * To change this template use File | Settings | File Templates.
 */

function cmp($a, $b) {
    return $a['timeSpent'] < $b['timeSpent'] ? 1 : -1;
}

error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../../db/conn.php');
require_once('../../util/others.php');
$sessionHash = $_REQUEST['sessionHash'];

$query = $conn->prepare("SELECT s.*, l.locationpoint_description FROM stoppoint s, locationpoint l WHERE s.session_hash = :session_hash AND s.locationpoint_id = l.locationpoint_id ORDER BY s.stoppoint_start_time ;");
$query->bindParam(":session_hash",$sessionHash);
$query->execute();
$stopPoints = $query->fetchAll(PDO::FETCH_ASSOC);
$pointsSummary = array();
foreach($stopPoints as $point){
    if(!array_key_exists($point['locationpoint_id'],$pointsSummary)){
        $pointsSummary[$point['locationpoint_id']] = array("timeSpent"=>timeDifference($point['stoppoint_start_time'],$point['stoppoint_end_time']),"lat"=>$point['stoppoint_center_lat'],"lng"=>$point['stoppoint_center_lng'],"desc"=>$point['locationpoint_description']);
    }else {
        $pointsSummary[$point['locationpoint_id']]["timeSpent"] = $pointsSummary[$point['locationpoint_id']]["timeSpent"] + timeDifference($point['stoppoint_start_time'],$point['stoppoint_end_time']);
    }
}

uasort($pointsSummary,'cmp');
print json_encode($pointsSummary);
$conn = null;






