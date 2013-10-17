<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Jia De's FYP - Viewer</title>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <style type="text/css">
            html { height: 100% }
            body { height: 100%; margin: 0; padding: 0 }

        </style>
        <link rel="stylesheet/less" type="text/css" href="/styles/styles.less" />
        <script src="/scripts/less-1.4.1.min.js" type="text/javascript"></script>
        <script type="text/javascript"
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5AN7Cbf3jecSlyOUHNNoCPE1ZJc6wGEw&sensor=true">
        </script>
        <script src="/scripts/jquery-2.0.3.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var path = new Array();
            var colors= new Array("#FF0055","#00FF00","#0000FF","#FFFF00","#FF00FF","#FFFFFF","#000000");
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

            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    </head>
    <body>
        <div id="left">
            <ul>
                <?php
                $sql = "SELECT DISTINCT CAST(`location_time` AS DATE ) AS uniqueDate FROM location;";
                foreach ($conn->query($sql) as $row) {
                ?>
                <li class="dateSelector"><?php echo $row['uniqueDate']?></li>
                <?
                }
                ?>
            </ul>
        </div>
        <div id="map-canvas"/>
    <script>
            $(".dateSelector").click(function() {
                //alert( $(this).text() +"Handler for .click() called." );
                $.post("/services/viewer/viewByDate.php",{date:$(this).text()},function( data ) {
                    var pathArray = new Array();
                    $.each(data, function(i, item){
                        var tmpMid = item.mid;
                        if(!(tmpMid in pathArray)){
                            pathArray[tmpMid]=new Array();
                        }
                        pathArray[item.mid].push(new google.maps.LatLng(item.location_lat, item.location_lng));
                    });
                    removeLine();
                    addLine(pathArray);
                    //alert( data[0]['location_lat'] ); // John
                    //alert( data[1] ); // 2pm
                }, "json");

            });
    </script>
    </body>

</html>
