<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 5/10/14
 * Time: 3:26 PM
 * To change this template use File | Settings | File Templates.
 */
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
        var chart;
        google.load("visualization", "1", {packages:["corechart","timeline"]});
        //google.setOnLoadCallback(drawChart);
        var sessionHash = "";
        var path = new Array();
        var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
        var points = [];
        var pointInfoWindows = [];
        var pointType = "Constant";
        var pointSelector = "All";
        var map;
        var pathCoordinates;
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
            chart = new google.visualization.PieChart(document.getElementById('pie-time'));
            google.visualization.events.addListener(chart, 'onmouseover', function() {
                alert('A table row was selected');
            });
            chart.draw(data, options);
        }

        function drawTimeline(timeline_data) {

            var container = document.getElementById('timeline');
            var chart = new google.visualization.Timeline(container);

            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn({ type: 'string', id: 'Day' });
            dataTable.addColumn({ type: 'string', id: 'Activity' });
            dataTable.addColumn({ type: 'date', id: 'Start' });
            dataTable.addColumn({ type: 'date', id: 'End' });
            dataTable.addRows(timeline_data);

            chart.draw(dataTable);
        }


    </script>
</head>
<body>
<div id="clickme">Click Me</div>
<span id='datepicker-container' style='font-size:200%'><div id="datepicker"></div></span>


<script>
    $( "#datepicker" ).datepicker({
        onSelect: function(date) {
            alert(date);
        },
        dateFormat: "yy-mm-dd",
        defaultDate: "2013-11-11",
        maxDate:"2014-01-01",
        minDate:"2013-10-06"
    });
</script>
<div id="timeline"  style="height: 50%;"></div>
<script>
    $("#clickme").click(function() {
        $.post("../services/viewer/viewTimeVariantModel.php",{username:"a@b.c", password:"password"},function( data ) {
            var timeVariantModel = new Array();
            $.each(data, function(d, day){
                $.each(day, function(h, hour){
                    for(var locationID in hour) break;
                    timeVariantModel.push([d,locationID,eval("new Date(0,0,0,"+h+",0,0)"),eval("new Date(0,0,0,"+h+",59,59)")]);
                });

            });
            //alert(pathArray);
            drawTimeline(timeVariantModel);
            //alert( data[0]['location_lat'] ); // John
            //alert( data[1] ); // 2pm
        }, "json");
    });


</script>
</body>
</html>