<?php
/**
 * Created by IntelliJ IDEA.
 * User: VMprogramming
 * Date: 5/27/13
 * Time: 12:05 AM
 * To change this template use File | Settings | File Templates.
 */
$username = "jiade";
$password = "password"  ;
try{
    $conn = new PDO('mysql:host=cheokorg.ipagemysql.com;dbname=jiadegcm',$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    echo 'ERROR : ' . $e->getMessage();
}

?>