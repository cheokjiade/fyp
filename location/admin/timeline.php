<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/14/14
 * Time: 1:48 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
$query = $conn->prepare("SELECT DISTINCT session_hash FROM locationvariantprob;");
$query->execute();
$sessionHashes = $query->fetchAll(PDO::FETCH_ASSOC);
$sessionHash = $sessionHashes[0]["session_hash"];

$query = $conn->prepare("SELECT DATE(stoppoint_start_time) as startdate FROM stoppoint WHERE session_hash = :session_hash ORDER BY stoppoint_start_time LIMIT 1;");
$query->bindParam(":session_hash", $sessionHash);
$query->execute();
$startDates = $query->fetchAll(PDO::FETCH_ASSOC);
$startDate = $startDates[0]['startdate'];

$query = $conn->prepare("SELECT * FROM stoppoint WHERE session_hash = :session_hash AND (DATE(stoppoint_start_time) BETWEEN :startDate AND :startDate + INTERVAL 1 WEEK)  ORDER BY stoppoint_start_time;");
$query->bindParam(":session_hash", $sessionHash);
$query->bindParam(":startDate", $startDate);
$query->execute();
$stopPoints = $query->fetchAll(PDO::FETCH_ASSOC);
$formattedStopPoints = array();
for($i=0;$i<sizeof($stopPoints);$i+=1){
    $tmpDateTime = new DateTime($stopPoints[$i]['stoppoint_start_time']);
    $tmpEndDateTime = new DateTime($stopPoints[$i]['stoppoint_end_time']);
    if(!array_key_exists($tmpDateTime->format('Y-m-d'),$formattedStopPoints)){
        $formattedStopPoints[$tmpDateTime->format('Y-m-d')] = array();
    }
    $formattedStopPoints[$tmpDateTime->format('Y-m-d')][] = array(
        "locationID"=>$stopPoints[$i]['locationpoint_id'],
        "date"=>$tmpDateTime->format('Y-m-d'),
        "startTime"=>"0,0,0,".$tmpDateTime->format('H,i,s'),
        "endTime"=>"0,0,0,". ($tmpDateTime->format('d')==$tmpEndDateTime->format('d')?$tmpEndDateTime->format('H,i,s'):"23,59,59"));
    while($tmpDateTime->format('d')!=$tmpEndDateTime->format('d')){
        date_modify($tmpDateTime, '+1 day');
        $formattedStopPoints[$tmpDateTime->format('Y-m-d')][] = array(
            "locationID"=>$stopPoints[$i]['locationpoint_id'],
            "date"=>$tmpDateTime->format('Y-m-d'),
            "startTime"=>"0,0,0,0,0,0",
            "endTime"=>"0,0,0,". ($tmpDateTime->format('d')==$tmpEndDateTime->format('d')?$tmpEndDateTime->format('H,i,s'):"23,59,59"));
    }
}

//echo 'works' . sizeof($stopPoints);
//print_r($formattedStopPoints);
?>
<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization',
       'version':'1','packages':['timeline']}]}"></script>
<script type="text/javascript">

    google.setOnLoadCallback(drawChart);

    function drawChart() {

        var container = document.getElementById('example3.1');
        var chart = new google.visualization.Timeline(container);

        var dataTable = new google.visualization.DataTable();
        dataTable.addColumn({ type: 'string', id: 'Day' });
        dataTable.addColumn({ type: 'string', id: 'Activity' });
        dataTable.addColumn({ type: 'date', id: 'Start' });
        dataTable.addColumn({ type: 'date', id: 'End' });
        dataTable.addRows([
            <?php
             foreach($formattedStopPoints as $stopPointDate){
                foreach($stopPointDate as $stopPoint){
                    print '["' . $stopPoint["date"] . '", "' . $stopPoint["locationID"] . '", new Date('.$stopPoint["startTime"].'), new Date('.$stopPoint["endTime"].')],';
                }
             }
            ?>]);

        chart.draw(dataTable);
    }
</script>

<div id="example3.1" style="width: 1200px; height: 700px;"></div>