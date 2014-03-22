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

$query = $conn->prepare("SELECT
			sp1.locationpoint_id,
			sp1.session_hash AS session_hash_1,
			sp1.stoppoint_start_time AS stoppoint_start_time_1,
			sp1.stoppoint_end_time AS stoppoint_end_time_1,
			sp2.session_hash  AS session_hash_2,
			sp2.stoppoint_start_time AS stoppoint_start_time_2,
			sp2.stoppoint_end_time AS stoppoint_end_time_2,
			CASE WHEN (sp1.stoppoint_start_time > sp2.stoppoint_start_time) THEN (sp1.stoppoint_start_time) ELSE (sp2.stoppoint_start_time) END AS overlapstart,
			CASE WHEN (sp1.stoppoint_end_time < sp2.stoppoint_end_time) THEN (sp1.stoppoint_end_time) ELSE (sp2.stoppoint_end_time) END AS overlapend,
			TIMESTAMPDIFF(MINUTE,
			CASE WHEN (sp1.stoppoint_start_time > sp2.stoppoint_start_time) THEN (sp1.stoppoint_start_time) ELSE (sp2.stoppoint_start_time) END,
			CASE WHEN (sp1.stoppoint_end_time < sp2.stoppoint_end_time) THEN (sp1.stoppoint_end_time) ELSE (sp2.stoppoint_end_time) END )
			AS overlaptime
	FROM	stoppoint sp1, stoppoint sp2
	WHERE	((sp1.stoppoint_start_time BETWEEN sp2.stoppoint_start_time AND sp2.stoppoint_end_time
			OR sp2.stoppoint_start_time BETWEEN sp1.stoppoint_start_time AND sp1.stoppoint_end_time)
			AND sp1.session_hash <> sp2.session_hash
			AND sp1.locationpoint_id = sp2.locationpoint_id
			AND sp1.stoppoint_id > sp2.stoppoint_id)
	ORDER BY overlapstart LIMIT 25;");
$query->execute();
$stopPoints = $query->fetchAll(PDO::FETCH_ASSOC);
$formattedStopPoints = array();
for($i=0;$i<sizeof($stopPoints);$i+=1){
    $tmpDateTime = new DateTime($stopPoints[$i]['overlapstart']);
    $tmpEndDateTime = new DateTime($stopPoints[$i]['overlapend']);
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