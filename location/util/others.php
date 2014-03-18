<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 1/7/14
 * Time: 10:04 PM
 * To change this template use File | Settings | File Templates.
 */
/*
 * This function takes in 2 timestamp strings and returns the time difference in minutes.
 * The first argument is the earlier time while the second is the later time.
 */
function distance($lat1, $lon1, $lat2, $lon2/*, $unit*/) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
//    $unit = strtoupper($unit);
    return ($miles * 1609.344);  //meters
//    if ($unit == "K") {
//      return ($miles * 1.609344);
//    } else if ($unit == "N") {
//        return ($miles * 0.8684);
//    } else {
//        return $miles;
//    }
}

function timeDifference($strTime1,$strTime2){
    $time1 = new DateTime($strTime1);
    $time2 = new DateTime($strTime2);
    $interval = $time1->diff($time2);
    $minutes = $interval->days * 24 * 60;
    $minutes += $interval->h * 60;
    $minutes += $interval->i;
    return $minutes;
}

/*
 * Takes in an array of location rows and smooths it before return it
 */
function smoothPoints($returnArray){
    $maxDistPerSec = 30;
    $smoothedArray = array();
    $lastLat = 0;
    $lastLng = 0;
    $lastTime = 0;
    $tempRowArray = array();
    $spikePointsArray = array();
    for($i=0, $size=count($returnArray);$i<$size;++$i){
        if(array_key_exists($returnArray[$i]["session_hash"],$tempRowArray)){
            $tempCurLocationRow = $returnArray[$i];
            $tempPrevLocationRow = $tempRowArray[$returnArray[$i]["session_hash"]];
            //if the distance between the 2 locations is less than timeinterval*40meters add it to the smoothed array
            if(distance($tempPrevLocationRow['location_lat'],$tempPrevLocationRow['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<$maxDistPerSec*(strtotime($tempCurLocationRow['location_time'])-strtotime($tempPrevLocationRow['location_time']))){
                if(array_key_exists($returnArray[$i]["session_hash"],$spikePointsArray)){
                    //This checks if the gps keeps defaulting to a single spiked point. If not it will be added
                    if($spikePointsArray[$returnArray[$i]["session_hash"]]['location_lng']!= $returnArray[$i]['location_lng'] && $spikePointsArray[$returnArray[$i]["session_hash"]]['location_lat']!= $returnArray[$i]['location_lat']){
                        $smoothedArray[]= $returnArray[$i];
                        $tempRowArray[$returnArray[$i]["session_hash"]] = $returnArray[$i];
                    }
                }else{
                    $smoothedArray[]= $returnArray[$i];
                    $tempRowArray[$returnArray[$i]["session_hash"]] = $returnArray[$i];
                }

            }else{ //add the location to an array of last spikes so we can remove the spike if it occurs int he same location
                $spikePointsArray[$returnArray[$i]["session_hash"]] = $returnArray[$i];
            }
        }else{
            //if it is the fist location of a session, juist add it to the smoothed array
            $smoothedArray[]= $returnArray[$i];
            $tempRowArray[$returnArray[$i]["session_hash"]] = $returnArray[$i];
        }
        //$midArray[]$returnArray[$i][]
    }
    return $smoothedArray;
}

/*
 * Takes in an array of locations and returns an array of the points
 * Each point is an object containing "start_time","end_time","point_center_lat","point_center_lng","accuracy"
 */
function retrievePointsFromLocations($locationsArray){
    $size=count($locationsArray);
    //To store latlng points that may be used to define a point
    $tmpPoint = array();
    //To store the points
    $pointsArray = array();

    $currTmpPointLat = 0;
    $currTmpPointLng = 0;

    for($i=0;$i<$size;++$i){
        $tempCurLocationRow = $locationsArray[$i];
        $closenessCounter = 0;
        if($i<5){
            for($tempCounter = $i;$tempCounter<$i+12;$tempCounter++){
                if(distance($locationsArray[$tempCounter]['location_lat'],$locationsArray[$tempCounter]['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<($tempCurLocationRow['location_accuracy']+$locationsArray[$tempCounter]['location_accuracy'])*1.3){
                    $closenessCounter++;
                }
            }
        }
        elseif($size-$i<12){
            for($tempCounter = $i-12;$tempCounter<$i;$tempCounter++){
                if(distance($locationsArray[$tempCounter]['location_lat'],$locationsArray[$tempCounter]['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<($tempCurLocationRow['location_accuracy']+$locationsArray[$tempCounter]['location_accuracy'])*1.3){
                    $closenessCounter++;
                }
            }
        }
        else{
            for($tempCounter = $i-5;$tempCounter<$i+6;$tempCounter++){
                if(distance($locationsArray[$tempCounter]['location_lat'],$locationsArray[$tempCounter]['location_lng'],$tempCurLocationRow['location_lat'],$tempCurLocationRow['location_lng'])<($tempCurLocationRow['location_accuracy']+$locationsArray[$tempCounter]['location_accuracy'])*1.3){
                    $closenessCounter++;
                }
            }
        }
        if($closenessCounter>8){
            $tmpPoint[] = $tempCurLocationRow;

        }
        else{
            if(count($tmpPoint)>0){
                if(count($tmpPoint)>12){
                    $tmpPointSize = count($tmpPoint);

                    $totalAcc = 0;
                    $totalLat = 0;
                    $totalLng = 0;

                    $avgAcc = 0;
                    $avgLat = 0;
                    $avgLng = 0;

                    foreach($tmpPoint as $tmpRow){
                        $totalLat += $tmpRow['location_lat'];
                        $totalLng += $tmpRow['location_lng'];
                        $totalAcc += $tmpRow['location_accuracy'];
                    }

                    $avgLat = $totalLat/$tmpPointSize;
                    $avgLng = $totalLng/$tmpPointSize;
                    $avgAcc = $totalAcc/$tmpPointSize;
                    //only register points under a certain accuracy
                    if($avgAcc<150){
                        $pointsArray[] = array("start_time"=>$tmpPoint[0]['location_time'],"end_time"=>$tmpPoint[$tmpPointSize-1]['location_time'],"point_center_lat"=>$avgLat,"point_center_lng"=>$avgLng,"accuracy"=>$avgAcc);
                    }

                }

                $tmpPoint = array();
            }

        }

    }
    return $pointsArray;
}

function mergePoints($pointsArray){
    $mergedPointsArray = array();
    $tempPoint = null;
    foreach($pointsArray as $point){
        if(is_null($tempPoint)){
            $tempPoint = $point;
            continue;
        }
        if(/*timeDifference($tempPoint["end_time"],$point["start_time"])<3 and*/ distance($tempPoint["point_center_lat"],$tempPoint["point_center_lng"],$point["point_center_lat"],$point["point_center_lng"])<15){
            $tempPoint["end_time"] = $point["end_time"];
        }else{
            $mergedPointsArray[]= $tempPoint;
            $tempPoint = $point;
        }
    }
    $mergedPointsArray[] = $tempPoint;
    return $mergedPointsArray;
}


// distance between two points in kilometres
// 3958      - Earth's radius (miles)
// 3.1415926 - PI
// 57.29578  - Number of degrees/radian (for conversion)
// 1.609344  - meters = 0.001 mile
function get_geo_distance_point_to_point($lat1, $lon1, $lat2, $lon2)
{
    return 	(3958 * 3.1415926 * sqrt(($lat2 - $lat1) * ($lat2 - $lat1)
        + cos($lat2 / 57.29578) * cos($lat1 / 57.29578) * ($lon2 - $lon1) * ($lon2 - $lon1)) / 180) * 1609.344;
}

// get height from triangle where A or B are not obtuse
function get_height_from_base_triangle($ab, $ac, $bc)
{
    // find $s (semiperimeter) for Heron's formula
    $s = ($ab + $ac + $bc) / 2;

    // Heron's formula - area of a triangle
    $area = sqrt($s * ($s - $ab) * ($s - $ac) * ($s - $bc));

    // find the height of a triangle - ie - distance from point to line segment
    $height = $area / (.5 * $ab);

    return $area;
}

// returns angles of a triangle from the sides
function get_angles_from_sides($ab, $bc, $ac)
{
    $a = $bc;
    $b = $ac;
    $c = $ab;

    $angle['a'] = rad2deg(acos((pow($b,2) + pow($c,2) - pow($a,2)) / (2 * $b * $c)));
    $angle['b'] = rad2deg(acos((pow($c,2) + pow($a,2) - pow($b,2)) / (2 * $c * $a)));
    $angle['c'] = rad2deg(acos((pow($a,2) + pow($b,2) - pow($c,2)) / (2 * $a * $b)));

    return $angle;
}

// $a, $b, $c lat lon array of line segments ($a and $b) and the off point ($c)
function get_geo_distance_point_to_segment($a, $b, $c)
{
    $ab = get_geo_distance_point_to_point($a['lat'], $a['lng'], $b['lat'], $b['lng']); // base or line segment
    $ac = get_geo_distance_point_to_point($a['lat'], $a['lng'], $c['lat'], $c['lng']);
    $bc = get_geo_distance_point_to_point($b['lat'], $b['lng'], $c['lat'], $c['lng']);

    $angle = get_angles_from_sides($ab, $bc, $ac);

    if($ab + $ac == $bc) // then points are collinear - point is on the line segment
    {
        return 0;
    }
    elseif($angle['a'] <= 90 && $angle['b'] <= 90) // A or B are not obtuse - return height as distance
    {
        return get_height_from_base_triangle($ab, $ac, $bc);
    }
    else // A or B are obtuse - return smallest side as distance
    {
        return ($ac > $bc) ? $bc : $ac;
    }

}
/*
	// line segment
	$a['lat'] = 37.083667;
	$a['lon'] = -1.84948;
	$b['lat'] = 37.069149;
	$b['lon'] = -1.849823;

	// point
	$c['lat'] = 37.14;
	$c['lon'] = -1.85;

	echo get_geo_distance_point_to_segment($a, $b, $c);
*/
?>