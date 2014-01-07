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
function timeDifference($strTime1,$strTime2){
    $time1 = new DateTime($strTime1);
    $time2 = new DateTime($strTime2);
    $interval = $time1->diff($time2);
    $minutes = $interval->days * 24 * 60;
    $minutes += $interval->h * 60;
    $minutes += $interval->i;
    return $minutes;
}