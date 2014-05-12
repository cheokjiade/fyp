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
require_once('../db/conn.php');
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

$query = $conn->prepare("SELECT DISTINCT CAST(`location_time` AS DATE ) AS uniqueDate FROM location WHERE session_hash = :session_hash ORDER BY uniqueDate;");
$query->bindParam(":session_hash",$sessionHash);
$query->execute();
$uniqueDates = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Jia De's FYP - Viewer</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0 }

        .extruder.left.a .flap{
            font-size:18px;
            color:white;
            top:0;
            padding:10px 0 10px 10px;
            background:#772B14;
            width:30px;
            position:absolute;
            right:0;
            -moz-border-radius:0 10px 10px 0;
            -webkit-border-top-right-radius:10px;
            -webkit-border-bottom-right-radius:10px;
            -moz-box-shadow:#666 2px 0px 3px;
            -webkit-box-shadow:#666 2px 0px 3px;
        }

        .extruder.left.a .content{
            border-right:3px solid #772B14;
            background:rgba(255,255,255,.95);
        }
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background-color: #555;
            filter:alpha(opacity=50);
            -moz-opacity:0.5;
            -khtml-opacity: 0.5;
            opacity: 0.5;
            z-index: 10000;
        }

    </style>
    <link rel="stylesheet/less" type="text/css" href="../styles/styles.less" />
    <script src="../scripts/less-1.4.1.min.js" type="text/javascript"></script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5AN7Cbf3jecSlyOUHNNoCPE1ZJc6wGEw&sensor=true">
    </script>
    <script src="../scripts/jquery-2.0.3.min.js" type="text/javascript"></script>
    <link rel="stylesheet/less" type="text/css" href="../extruder/css/mbExtruder.css" />
    <script src="../extruder/inc/jquery.hoverIntent.min.js" type="text/javascript"></script>
    <script src="../extruder/inc/jquery.mb.flipText.js" type="text/javascript"></script>
    <script src="../extruder/inc/mbExtruder.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#extruderLeft").buildMbExtruder({
                width:450,
                position:"left",
                flapDim:"100%",
                extruderOpacity:1,
                onClose:function(){},
                onContentLoad: function(){}
            });
        });
        google.load("visualization", "1", {packages:["corechart","timeline"]});
        //google.setOnLoadCallback(drawChart);
        var sessionHash = "<?php echo $sessionHash; ?>";
        var optionsSmoothened = true;
        var path = new Array();
        var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
        var points = [];
        var summaryPoints = [];
        var pointInfoWindows = [];
        var pointType = "Constant";
        var pointSelector = "All";
        var map;
        var pathCoordinates;
        var dailyTimeline;
        var dailyTimelineData;
        var timeVariantModel;
        var timeVariantModelData;
        var transportDetails;
        var transportDetailsData;
        var detailedTransportation;
        var summaryAllLocations;
        var summaryAllLocationsInfoWindows;
        var transportationPathsArray;
        Array.prototype.clear = function()  //Add a new method to the Array Object
        {
            var i;
            for(i=0;i<this.length;i++)
            {
                this.pop();
            }
        }
        function drawChart(chart_data) {
            var data = google.visualization.arrayToDataTable(chart_data);
            var options = {
                title: 'Time Spent At Points'
            };
            var chart = new google.visualization.PieChart(document.getElementById('pie-time'));
            chart.draw(data, options);
        }

        function drawTransportSummaryChart(chart_data){
            var data = google.visualization.arrayToDataTable(chart_data);
            var options = {
                title: 'Transportation Service Summary'
            };
            var chart = new google.visualization.PieChart(document.getElementById('pie-transport-summary'));
            chart.draw(data, options);
        }

        function initialize() {
            var mapOptions = {
                center: new google.maps.LatLng(1.3708097, 103.8529281),
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("map-canvas"),
                mapOptions);

            pathCoordinates = [
                new google.maps.LatLng(37.772323, -122.214897),
                new google.maps.LatLng(21.291982, -157.821856),
                new google.maps.LatLng(-18.142599, 178.431),
                new google.maps.LatLng(-27.46758, 153.027892)
            ];

            $.post("../services/viewer/viewSummary.php",{sessionHash:sessionHash},function( data ) {
                summaryAllLocationsInfoWindows = [];
                summaryAllLocations = data;
                //alert(JSON.stringify(data));
                $.each(data, function(i, point){
                    var tmpCircle = new google.maps.Circle({
                        strokeColor: '#00FF00',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#00FF00',
                        fillOpacity: 0.25,
                        map: map,
                        center: new google.maps.LatLng(point.lat,point.lng),
                        radius: 40//locPoints[locPoint].population / 20
                    });
                    summaryPoints.push(tmpCircle);
                    var infoWindow = new google.maps.InfoWindow({
                        content: "<div class=\"infowindow\">" +
                            "<div>Hours Spent : "+(parseInt(point.timeSpent)/60).toFixed(2)+"</div>" +
                            "<div>Name : "+point.desc+" </div></div>",
                        maxWidth: 700,
                        position: tmpCircle.getCenter()
                    });
                    google.maps.event.addListener(tmpCircle, 'click', function(ev){
                        infoWindow.setPosition(ev.latLng);
                        infoWindow.open(map);
                    });
                    //infoWindow.open(map);
                    summaryAllLocationsInfoWindows.push(infoWindow);
                });

                //alert( data[0]['location_lat'] ); // John
                //alert( data[1] ); // 2pm
            }, "json") ;


        }

        function addLine(pathCoordinates) {

            var count = 0;
            for(var i in pathCoordinates){
                var tmpPath= new google.maps.Polyline({
                    path: pathCoordinates[i],
                    map: map,
                    strokeColor: colors[count++],
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });
                //path[i].setPath(pathCoordinates[i]);
                //path[i].setMap(map);
                path.push(tmpPath);
            }
        }

        function removeLine() {
            for(i=0;i<path.length;i++){
                path[i].setMap(null);
            }
            path.clear();

        }




        function addPoint(pointArray){
            for (var i =0 ; i<pointArray.length;i++) {
                var tmpCircle = new google.maps.Circle({
                    strokeColor: '#00FF00',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#00FF00',
                    fillOpacity: 0.25,
                    map: map,
                    center: new google.maps.LatLng(pointArray[i].lat,pointArray[i].lng),
                    radius: (pointType=="Constant")?40:(pointType=="Accuracy")?pointArray[i].acc:pointArray[i].count//locPoints[locPoint].population / 20
                });
                points.push(tmpCircle);
                var infoWindow = new google.maps.InfoWindow({
                    content: "<div class=\"infowindow\">" +
                        "<div>Hours Spent : "+(parseInt(pointArray[i].totaltime)/60)+"</div>" +
                        "<div>Average Time Spent : "+(parseInt(pointArray[i].totaltime)/parseInt(pointArray[i].count))+"</div>" +
                        "<div>Average Accuracy : "+pointArray[i].acc+" </div></div>",
                    maxWidth: 700,
                    position: tmpCircle.getCenter()
                });
                infoWindow.open(map);
                pointInfoWindows.push(infoWindow);
            }
        }

        function removePoints(){
            for(i=0;i<points.length;i++){
                points[i].setMap(null);
            }
            points.clear();
            for(i=0;i<pointInfoWindows.length;i++){
                pointInfoWindows[i].close();
            }
            pointInfoWindows.clear();
        }

        function drawTimeline(timeline_data) {

            var container = document.getElementById('timeline');
            dailyTimeline = new google.visualization.Timeline(container);
            google.visualization.events.addListener(dailyTimeline, 'onmouseover', function(row) {
                map.panTo(new google.maps.LatLng(summaryAllLocations[timeline_data[row.row][1]].lat,summaryAllLocations[timeline_data[row.row][1]].lng));
                //alert(timeVariantModelData[row.row][1]);
            });
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn({ type: 'string', id: 'Day' });
            dataTable.addColumn({ type: 'string', id: 'Activity' });
            dataTable.addColumn({ type: 'date', id: 'Start' });
            dataTable.addColumn({ type: 'date', id: 'End' });
            dataTable.addRows(timeline_data);

            dailyTimeline.draw(dataTable);
        }

        function drawTimeVariantModel(timeline_data) {

            var container = document.getElementById('timeline-timevariant');
            timeVariantModel = new google.visualization.Timeline(container);
            google.visualization.events.addListener(timeVariantModel, 'onmouseover', function(row) {
                map.panTo(new google.maps.LatLng(summaryAllLocations[timeVariantModelData[row.row][1]].lat,summaryAllLocations[timeVariantModelData[row.row][1]].lng));
                //alert(timeVariantModelData[row.row][1]);
            });
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn({ type: 'string', id: 'Day' });
            dataTable.addColumn({ type: 'string', id: 'Activity' });
            dataTable.addColumn({ type: 'date', id: 'Start' });
            dataTable.addColumn({ type: 'date', id: 'End' });
            dataTable.addRows(timeline_data);

            timeVariantModel.draw(dataTable);
        }

        function drawTransportationType(timeline_data) {

            var container = document.getElementById('timeline-transport-details');
            transportDetails = new google.visualization.Timeline(container);
            google.visualization.events.addListener(transportDetails, 'onmouseover', function(row) {
                //alert(transportationPathsArray);
                var pathArray = new Array();
                var tmpID = timeline_data[row.row][0];
                $.each(transportationPathsArray, function(i, item){
                    //echo(item.publictransportservices_id);
                    var tmpMid = timeline_data[row.row][0] + "_" + item.publictransportservices_id;
                    if(!(tmpMid in pathArray)){
                        pathArray[tmpMid]=new Array();
                    }if(item.route_id == tmpID){
                        pathArray[tmpMid].push(new google.maps.LatLng(item.lat, item.lng));
                    }

                });
                removeLine();
                addLine(pathArray);
                //alert(timeVariantModelData[row.row][1]);
            });
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn({ type: 'string', id: 'Route ID' });
            dataTable.addColumn({ type: 'string', id: 'Service' });
            dataTable.addColumn({ type: 'date', id: 'Start' });
            dataTable.addColumn({ type: 'date', id: 'End' });
            dataTable.addRows(timeline_data);

            transportDetails.draw(dataTable);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<div id="left">
    <div id="accordion">
        <h4>Summary</h4>
        <div>
            <div id="timeline-timevariant"  style="height: 375;"></div>
            <div id="pie-transport-summary" style="height: 200px;padding-left:10px;"></div>
        </div>
        <h4>Detailed View</h4>
        <div>
            <div id="datelist" style="height: 240px; width:500px; padding: 0px;">
                <!--<div id="datelist-title" style="overflow: auto";>Dates</div> -->
                <span id='datepicker-container' style='font-size:100%'><div id="datepicker" style="float:left;"></div></span>
                <!--<div style="float:right;"> Smoothened: <span id="optionSmoothenedYes"> Yes</span> <span id="optionSmoothenedNo"> No</span></div> -->
            </div>
            <div id="details" style="height: 120px;">
                <!--<div id="pie-time" style="height: 350px;padding-left:10%;"></div>-->
                <div id="timeline"  style="height: 100px;"></div>
            </div>
            <div id="timeline-transport-details"  style="height: 500px;"></div>
        </div>

    </div>
    <div id="title" style="text-align: center">
        
        <div id="points-text" class="showPoints" style="float: right">
            
        </div>
        <div id="day-selector" class="day-selector" style="float: right">
            
        </div>
        <div id="all-selector" class="all-selector" style="float: right">
           
        </div>
        <div id="radius-type-constant-selector" class="all-selector" style="float: right">
           
        </div>
        <div id="radius-type-count-selector" class="all-selector" style="float: right">
            
        </div>
        <div id="radius-type-accuracy-selector" class="all-selector" style="float: right">
            
        </div>
    </div>
    <!--<div id="datelist" style="height: 250px; width:350px; padding: 5px">
        <div id="datelist-title" style="overflow: auto";>Dates</div>
        <span id='datepicker-container' style='font-size:100%'><div id="datepicker"></div></span>
    </div>
    <div id="details" style="height: 100%;">
        <div id="pie-time" style="height: 40%;padding-left:0%;"></div>
        <div id="timeline"  style="height: 20%;"></div>
    </div> -->

</div>
<div id="map-canvas"/>

<script>
    $("#optionSmoothenedYes").click(function() {
        optionsSmoothened = true;
    });
    $("#optionSmoothenedNo").click(function() {
        optionsSmoothened = false;
    });
    $(".day-selector").click(function() {
        pointSelector = "Day";
    });
    $(".all-selector").click(function() {
        pointSelector = "All";
    });
    $(".all-selector").click(function() {
        pointSelector = "All";
    });
    $(".all-selector").click(function() {
        pointSelector = "All";
    });
    $(".showPoints").click(function() {
        //alert( $(this).text() +"Handler for .click() called." );
        if(pointSelector=="All"){
            $.post("../services/viewer/viewPoints.php",{sessionHash:sessionHash},function( data ) {
                removeLine();
                removePoints();
                addPoint(data);
                //alert( data[0]['location_lat'] ); // John
                //alert( data[1] ); // 2pm
            }, "json");
        }

    });
    $( "#datepicker" ).datepicker({
        onSelect: function(date) {
            //alert( $(this).text() +"Handler for .click() called." );

            if(pointSelector=="Day"){
                if(optionsSmoothened==true){
                    $.post("../services/viewer/viewPoints.php",{date:date, smoothen:"true", sessionHash:sessionHash},function( data ) {
                        removeLine();
                        removePoints();
                        addPoint(data);
                        //alert( data[0]['location_lat'] ); // John
                        //alert( data[1] ); // 2pm
                    }, "json");
                }else{
                    $.post("../services/viewer/viewPoints.php",{date:date, sessionHash:sessionHash},function( data ) {
                        removeLine();
                        removePoints();
                        addPoint(data);
                        //alert( data[0]['location_lat'] ); // John
                        //alert( data[1] ); // 2pm
                    }, "json");
                }

            }
            $.post("../services/viewer/viewByDate.php",{date:date, sessionHash:sessionHash},function( data ) {
                var pathArray = new Array();
                $.each(data, function(i, item){
                    var tmpMid = item.session_hash;
                    if(!(tmpMid in pathArray)){
                        pathArray[tmpMid]=new Array();
                    }
                    pathArray[item.session_hash].push(new google.maps.LatLng(item.location_lat, item.location_lng));
                });
                removeLine();
                addLine(pathArray);
                //alert( data[0]['location_lat'] ); // John
                //alert( data[1] ); // 2pm
            }, "json");

            /*$.post("../services/viewer/viewTimeAtPoints.php",{date:date, sessionHash:sessionHash},function( data ) {
                //alert(data);
                drawChart(data);
            }, "json");   */

            $.post("../services/viewer/viewTimeline.php",{date:date, sessionHash:sessionHash},function( data ) {
                dailyTimelineData = new Array();
                $.each(data, function(i, item){
                    dailyTimelineData.push(["Timeline",item.locationID,eval("new Date("+item.startTime+")"),eval("new Date("+item.endTime+")")]);
                });
                //alert(pathArray);
                drawTimeline(dailyTimelineData);
                //alert( data[0]['location_lat'] ); // John
                //alert( data[1] ); // 2pm
            }, "json");

            /*$.post("../services/viewer/viewTimeAtPoints.php",{date:date, sessionHash:sessionHash},function( data ) {
                //alert(data);
                //drawChart(data);
            }, "json");  */

            $.post("../services/viewer/viewTransportationPaths.php",{date:date, sessionHash:sessionHash},function( data ) {
                //alert(data);
                transportationPathsArray = data;
            }, "json");

            $.post("../services/viewer/viewTransportationDetails.php",{date:date, sessionHash:sessionHash},function( data ) {
                detailedTransportation = new Array();
                $.each(data, function(i, item){
                    detailedTransportation.push([item.route_id, item.transport_id, eval("new Date(0,0,0,"+item.start+")") ,eval("new Date(0,0,0,"+item.end+")")]);
                });
                //alert(JSON.stringify(detailedTransportation));
                drawTransportationType(detailedTransportation);
                //alert( data[0]['location_lat'] ); // John
                //alert( data[1] ); // 2pm
            }, "json");

        },
        dateFormat: "yy-mm-dd",
        defaultDate: "<?php echo $uniqueDates[count($uniqueDates)-1]['uniqueDate']?>",
        maxDate:"<?php echo $uniqueDates[count($uniqueDates)-1]['uniqueDate']?>",
        minDate:"<?php echo $uniqueDates[0]['uniqueDate']?>"
    });

    $.post("../services/viewer/viewTimeVariantModel.php",{sessionHash:sessionHash},function( data ) {
        timeVariantModelData = new Array();
        $.each(data, function(d, day){
            $.each(day, function(h, hour){
                for(var locationID in hour) break;
                timeVariantModelData.push([d,locationID,eval("new Date(0,0,0,"+h+",0,0)"),eval("new Date(0,0,0,"+h+",59,59)")]);
            });

        });
        //alert(pathArray);
        drawTimeVariantModel(timeVariantModelData);
        //alert( data[0]['location_lat'] ); // John
        //alert( data[1] ); // 2pm
    }, "json");

    $.post("../services/viewer/viewTransportationSummary.php",{sessionHash:sessionHash},function( data ) {
        var transportationSummaryData = [];
        var titleArray = [];
        titleArray.push("Service");
        titleArray.push("Trips");
        transportationSummaryData.push(titleArray);
        $.each(data, function(i, transport){
            var tmpArray = [];
            tmpArray.push(transport.publictransportservices_id);
            tmpArray.push(Number(transport.count));
            transportationSummaryData.push(tmpArray);
        });
        //alert(pathArray);
        drawTransportSummaryChart(transportationSummaryData);
        //alert( data[0]['location_lat'] ); // John
        //alert( data[1] ); // 2pm
    }, "json");

    //var overlay = jQuery('<div id="overlay"> </div>');
    //overlay.appendTo(document.body)
    $(function() {
        $( "#accordion" ).accordion({
            collapsible: true,
            heightStyle: "content"
        });
    });

    function addSummaryPoint(pointArray){
        summaryAllLocationsInfoWindows = [];

        $.each(pointArray, function(d, point){
            var tmpCircle = new google.maps.Circle({
                strokeColor: '#00FF00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#00FF00',
                fillOpacity: 0.25,
                map: map,
                center: new google.maps.LatLng(point.lat,point.lng),
                radius: 40//locPoints[locPoint].population / 20
            });
            summaryPoints.push(tmpCircle);
            var infoWindow = new google.maps.InfoWindow({
                content: "<div class=\"infowindow\">" +
                    "<div>Hours Spent : "+(parseInt(point.timeSpent)/60).toFixed(2)+"</div>" +
                    "<div>Name : "+point.desc+" </div></div>",
                maxWidth: 700,
                position: tmpCircle.getCenter()
            });
            google.maps.event.addListener(tmpCircle, 'click', function(ev){
                infoWindow.setPosition(ev.latLng);
                infoWindow.open(map);
            });
            //infoWindow.open(map);
            summaryAllLocationsInfoWindows.push(infoWindow);

    });
    }
</script>
</body>

</html>
