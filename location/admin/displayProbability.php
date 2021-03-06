<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 2/21/14
 * Time: 10:56 PM
 * To change this template use File | Settings | File Templates.
 *
 * SELECT l.*, s.count FROM
(SELECT locationpoint_id, count(locationpoint_id) as count FROM fyp.stoppoint GROUP BY locationpoint_id ORDER BY count desc) s
INNER JOIN
locationpoint l
ON s.locationpoint_id = l.locationpoint_id
ORDER BY s.count DESC
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');
require_once('../util/others.php');


$query = $conn->prepare("SELECT DISTINCT session_hash FROM locationvariantprob;");
$query->execute();
$sessionHashes = $query->fetchAll(PDO::FETCH_ASSOC);
$sessionHash = $sessionHashes[0]["session_hash"];


$query = $conn->prepare("SELECT
	lvp.*,
	lp1.locationpoint_center_lat AS 'locationpoint_from_lat', lp1.locationpoint_center_lng AS 'locationpoint_from_lng' ,
	lp1.locationpoint_description AS 'locationpoint_from_description',
	lp2.locationpoint_center_lat AS 'locationpoint_to_lat', lp2.locationpoint_center_lng AS 'locationpoint_to_lng',
	lp2.locationpoint_description AS 'locationpoint_to_description'
FROM
	locationvariantprob lvp, locationpoint lp1, locationpoint lp2
WHERE
	locationpoint_from_id <> locationpoint_to_id
AND lp1.locationpoint_id = lvp.locationpoint_from_id
AND lp2.locationpoint_id = lvp.locationpoint_to_id
AND lvp.session_hash = :session_hash
ORDER BY
lvp.locationpoint_from_id;");
$query->bindParam(":session_hash", $sessionHash);
$query->execute();
$rawPointsArray = $query->fetchAll(PDO::FETCH_ASSOC);
$pointsArray = array();
$pointsIDArray = array();
foreach($rawPointsArray as $point){
    if(isset($pointsArray[$point['locationpoint_from_id']])){
        //add the counts up
        $pointsArray[$point['locationpoint_from_id']]['totalCount'] = $pointsArray[$point['locationpoint_from_id']]['totalCount'] + $point['locationvariantprob_count'];
        //insert destination into the list
        $pointsArray[$point['locationpoint_from_id']]['destinationList'][] = array('id'=>$point['locationpoint_to_id'],'lat'=>$point['locationpoint_to_lat'],'lng'=>$point['locationpoint_to_lng'], 'description'=>$point["locationpoint_to_description"], 'count'=>$point['locationvariantprob_count']);
    }
    else{
        $pointsIDArray[] = $point['locationpoint_from_id'];
        $pointsArray[$point['locationpoint_from_id']] = array('pointID'=>$point['locationpoint_from_id'],'lat'=>$point['locationpoint_from_lat'],'lng'=>$point['locationpoint_from_lng'], 'description'=>$point["locationpoint_from_description"], 'totalCount'=>$point['locationvariantprob_count'],'destinationList'=>array());
        $pointsArray[$point['locationpoint_from_id']]['destinationList'][] = array('id'=>$point['locationpoint_to_id'],'lat'=>$point['locationpoint_to_lat'],'lng'=>$point['locationpoint_to_lng'], 'description'=>$point["locationpoint_to_description"], 'count'=>$point['locationvariantprob_count']);
    }
}
//print_r($pointsArray);
//exit();
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Point Viewer</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0 }

    </style>
    <link rel="stylesheet/less" type="text/css" href="../styles/styles.less" />
    <script src="../scripts/less-1.4.1.min.js" type="text/javascript"></script>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5AN7Cbf3jecSlyOUHNNoCPE1ZJc6wGEw&sensor=true">
    </script>
    <script src="../scripts/jquery-2.0.3.min.js" type="text/javascript"></script>
</head>
<body>
<div id="left">
    <!--<table>
        <tr>
            <th>Lat</th>
            <th>Lng</th>
            <th>Accuracy</th>
        </tr>
        <?php
        foreach($pointsArray as $point){
            ?>
            <tr>
                <td><?php echo $point["lat"]?></td>
                <td><?php echo $point["lng"]?></td>
                <td><?php echo 20?></td>
            </tr>
        <?php
        }
        ?>
    </table>-->
</div>
<div id="speedlegend" class="hidden">
    test
</div>
<div id="testlegend" class="hidden">
    <ul class="legend-holder">
        <li class="legend-item">
            <div class="input-color">
                <!--<input type="text" value="Orange" />--><div class="legend-text">1km/h</div>
                <div class="color-box" style="background-color: #3ADF00;"></div>
                <!-- Replace "#FF850A" to change the color -->
            </div>
        </li><!--
        --><li class="legend-item">
            <div class="input-color">
                <div class="legend-text">2km/h</div>
                <div class="color-box" style="background-color: #5bf4c5;"></div>
                <!-- Replace "navy" to change the color -->
            </div>
        </li><!--
        --><li class="legend-item">
            <div class="input-color">
                <div class="legend-text">4km/h</div>
                <div class="color-box" style="background-color: #3289ea;"></div>
                <!-- Replace "navy" to change the color -->
            </div>
        </li><!--
        --><li class="legend-item">
            <div class="input-color">
                <div class="legend-text">10km/h</div>
                <div class="color-box" style="background-color: #fccb0f;"></div>
                <!-- Replace "navy" to change the color -->
            </div><!--
        --><li class="legend-item">
            <div class="input-color">
                <div class="legend-text">20km/h</div>
                <div class="color-box" style="background-color: #ea3424;"></div>
                <!-- Replace "navy" to change the color -->
            </div>
        </li><!--
        --><li class="legend-item">
            <div class="input-color">
                <div class="legend-text">35km/h</div>
                <div class="color-box" style="background-color: #fffc65;"></div>
                <!-- Replace "navy" to change the color -->
            </div>
        </li><!--
        --><li class="legend-item">
            <div class="input-color">
                <div class="legend-text">50km/h+</div>
                <div class="color-box" style="background-color: #3B0B0B;"></div>
                <!-- Replace "navy" to change the color -->
            </div>
        </li>
    </ul>
</div>
<div id="map-canvas"/>

<script>
    var sessionHash = '<?php echo $sessionHash;?>';
    var pointsArray = <?php echo json_encode($pointsArray);?>;
    var pointsIDArray = <?php echo json_encode($pointsIDArray);?>;
    var colors= new Array("#3ADF00","#5bf4c5","#3289ea","#fccb0f","#ea3424","#fffc65","#3B0B0B");
    var locPoints = [];
    var pointsAcc = [];
    var pathArray = [];
    var paths = [];
    var circleArray = [];
    var nextPoints = [];
    var infoWindows = [];
    var nextInfoWindows = [];
    // Create the map.
    var mapOptions = {
        zoom: 13,
        center: new google.maps.LatLng(1.37081484, 103.85283565217),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'),
        mapOptions);
    //map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(document.getElementById('speedlegend'));
    var geocoder =new google.maps.Geocoder();

    Array.prototype.clear = function()  //Add a new method to the Array Object
    {
        var i;
        for(i=0;i<this.length;i++)
        {
            this.pop();
        }
    }
    //alert(pointsArray[pointsIDArray[4]]["lat"]);
    function initialize() {
        document.getElementById('testlegend').index = 1;
        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(document.getElementById('testlegend'));

        /*var homeControlDiv = document.createElement('div');
        var homeControl = new SpeedLegend(homeControlDiv, map);

        homeControlDiv.index = 1;
        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(homeControlDiv);*/
        /*var infoWindow = new google.maps.InfoWindow({
            content: "hi",
            maxWidth: 500
        });*/
        // Construct the circle for each value in citymap.
        // Note: We scale the population by a factor of 20.
        for (var i =0 ; i<pointsIDArray.length;i++) {
            var circleOptions = {
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.25,
                map: map,
                clickable: true,
                center: new google.maps.LatLng(pointsArray[pointsIDArray[i]]["lat"], pointsArray[pointsIDArray[i]]["lng"]),
                radius: 20//locPoints[locPoint].population / 20
            };
            // Add the circle for this city to the map.
            var cityCircle = new google.maps.Circle(circleOptions);
            clickClickableCircle(map,cityCircle,[pointsIDArray[i]]);
            circleArray[pointsIDArray[i]] = cityCircle;

        }
    }
    //adds the onclick events to each circle that is a start point
    function clickClickableCircle(map, circle, pointID){
        var infoWindow = new google.maps.InfoWindow({
            content: "<div>"+pointID+"</div>",
            maxWidth: 500
        });
        google.maps.event.addListener(circle, 'click', function(ev){
            for(var i=0;i<infoWindows.length;i++){
                infoWindows[i].close();
            }
            showNextDestinations(map,circle,pointID);
            infoWindow.setPosition(circle.getCenter());
            infoWindow.open(map);
            showRoutesToDestinations(pointID);
        });
        infoWindows.push(infoWindow);
    }
    //shows the routes to destinations from a point given its id
    function showRoutesToDestinations(pointID){
        document.getElementById('testlegend').style.display = "inline";
        for(var i=0;i<paths.length;i++){
            paths[i].setMap(null);
        }
        paths.clear();
        $.post(
            "../services/viewer/viewRoutesByStartLocation.php",
            {sessionHash: sessionHash, startLocation: pointID},
            function(data){
                $.each(data,function(i,path){
                    $.each(path,function(j,point){
                        if(j>0){
                            var tempPathCoordinates =[];
                            tempPathCoordinates.push(new google.maps.LatLng(path[j-1].location_lat,path[j-1].location_lng));
                            tempPathCoordinates.push(new google.maps.LatLng(point.location_lat,point.location_lng));
                            var tempPath = new google.maps.Polyline({
                                path: tempPathCoordinates,
                                geodesic: true,
                                strokeColor: speedToColor(point.speed),
                                strokeOpacity: 0.9,
                                strokeWeight: 5,
                                map: map
                            });
                            paths.push(tempPath);
                        }
                    });
                });
            },
            "json"
        );
    }

    function speedToColor(speed){
        if(speed < 1){
            return colors[0];
        }if(speed < 2){
            return colors[1];
        }if(speed < 4){
            return colors[2];
        }if(speed < 10){
            return colors[3];
        }if(speed < 20){
            return colors[4];
        }if(speed < 30){
            return colors[5];
        }return colors[6];
    }

    function showNextDestinations(map,circle,pointID){
        //empty the array and remove previous circles
        for(var i=0;i<nextPoints.length;i++){
            nextPoints[i].setMap(null);
            nextInfoWindows[i].close();
        }
        nextPoints.clear();
        nextInfoWindows.clear();
        for(var i=0;i< pointsArray[pointID]["destinationList"].length;i++){
            var circleOptions = {
                strokeColor: '#00FF00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#00FF00',
                fillOpacity: 0.25,
                map: map,
                clickable: true,
                center: new google.maps.LatLng(pointsArray[pointID]["destinationList"][i]["lat"], pointsArray[pointID]["destinationList"][i]["lng"]),
                radius: 30//locPoints[locPoint].population / 20
            };
            // Add the circle for this city to the map.
            var cityCircle = new google.maps.Circle(circleOptions);
            nextPoints.push(cityCircle);
            /*var infoWindowText = "";
            geocoder.geocode({'latLng': cityCircle.getCenter()}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        //document.getElementById("info-window-"+i).innerHTML = results[1].formatted_address;
                        infoWindowText = results[1].formatted_address;
                    } else {
                        alert('No results found');
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            }); */
            var infoWindow = new google.maps.InfoWindow({
                content: "<div class=\"infowindow\">"+(parseFloat(pointsArray[pointID]["destinationList"][i]["count"])/parseFloat(pointsArray[pointID]["totalCount"])*100).toFixed(2)+"%<br><div id=\"info-window-"+ i +"\">"+pointsArray[pointID]["destinationList"][i]["description"]+"</div></div>",
                maxWidth: 700,
                position: cityCircle.getCenter()
            });
            infoWindow.open(map);
            nextInfoWindows.push(infoWindow);


        }
    }

    function getInformation(pointID){
        var infoString ="";

    }

    function SpeedLegend(controlDiv, map) {

        // Set CSS styles for the DIV containing the control
        // Setting padding to 5 px will offset the control
        // from the edge of the map
        controlDiv.style.padding = '2px';

        // Set CSS for the control border
        var controlUI = document.createElement('div');
        controlUI.style.backgroundColor = 'white';
        controlUI.style.borderStyle = 'solid';
        controlUI.style.borderWidth = '2px';
        controlUI.style.cursor = 'pointer';
        controlUI.style.textAlign = 'center';
        controlUI.title = 'Click to set the map to Home';
        controlDiv.appendChild(controlUI);

        // Set CSS for the control interior
        var controlText = document.createElement('div');
        controlText.style.fontFamily = 'Arial,sans-serif';
        controlText.style.fontSize = '12px';
        controlText.style.paddingLeft = '4px';
        controlText.style.paddingRight = '4px';
        controlText.innerHTML = document.getElementById('testlegend').innerHTML;
        controlUI.appendChild(controlText);
    }
    $(function() {
        initialize();
    });
    //google.maps.event.addDomListener(window, 'load', initialize);

</script>
</body>
</html>