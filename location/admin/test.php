<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/5/14
 * Time: 9:20 PM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
echo "Starting Insertion"
for($i=0;$i<200;$i++){
    $query = $conn->prepare("INSERT INTO testgyroscopedata(deviceid,timestamp,accX,accY,accZ)
VALUES('62bc2c49a887d3f6c9982e09f3df801ce3',$i*1000000,7,7,7);");
$query->execute();
}