<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 2/12/14
 * Time: 1:03 AM
 * To change this template use File | Settings | File Templates.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('../db/conn.php');

$query = $conn->prepare("SELECT * FROM userdata WHERE userdata_email = :email");
$query->bindParam(":email",$email);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);