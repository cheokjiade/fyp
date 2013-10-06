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
            var path;
            var map;
            function initialize() {
                var mapOptions = {
                    center: new google.maps.LatLng(1.3708097, 103.8529281),
                    zoom: 13,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("map-canvas"),
                    mapOptions);

                var pathCoordinates = [
                    new google.maps.LatLng(37.772323, -122.214897),
                    new google.maps.LatLng(21.291982, -157.821856),
                    new google.maps.LatLng(-18.142599, 178.431),
                    new google.maps.LatLng(-27.46758, 153.027892)
                ];

                path = new google.maps.Polyline({
                    path: pathCoordinates,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });
            }

            function addLine(pathCoordinates) {
                path.setPath(pathCoordinates);
                path.setMap(map);
            }

            function removeLine() {
                path.setMap(null);
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
                    var pathArray = [];
                    $.each(data, function(i, item){
                        pathArray.push(new google.maps.LatLng(item.location_lat, item.location_lng));
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
