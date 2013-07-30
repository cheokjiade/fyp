<?php
/**
 * Created by IntelliJ IDEA.
 * User: VMprogramming
 * Date: 5/24/13
 * Time: 9:06 AM
 * To change this template use File | Settings | File Templates.
 */
if($_REQUEST['action']) {
    require_once('../db/conn.php');
    $action = $_REQUEST['action'];
    switch($action){
        case "location":
            $locJson = $_REQUEST[''];
            break;
    }
}
