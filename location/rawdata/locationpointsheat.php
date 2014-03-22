<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/19/14
 * Time: 11:57 PM
 * To change this template use File | Settings | File Templates.        SELECT locationpoint_id, COUNT(locationpoint_id), stoppoint_center_lat, stoppoint_center_lng  FROM fyp.stoppoint GROUP BY locationpoint_id;
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
require_once('../util/others.php');


$query = $conn->prepare("SELECT locationpoint_id, COUNT(locationpoint_id) AS amount, stoppoint_center_lat, stoppoint_center_lng  FROM fyp.stoppoint GROUP BY locationpoint_id;");
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row){
    echo $row['stoppoint_center_lat'] . ',' . $row['stoppoint_center_lng'] . ','. $row['amount']  . "\n";
}