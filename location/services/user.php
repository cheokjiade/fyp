<?php
/**
 * Created by IntelliJ IDEA.
 * User: VMprogramming
 * Date: 5/26/13
 * Time: 11:08 PM
 * To change this template use File | Settings | File Templates.
 */
if($_REQUEST['action']) {
    require_once('../db/conn.php');
    $action = $_REQUEST['action'];
    switch($action){
        //signup a new email address
        case "signup":
            if($_REQUEST['email'] && $_REQUEST['pw']){
                $email = strtolower($_REQUEST['email']);
                $pw = $_REQUEST['pw'];

                $query = $conn->prepare("SELECT COUNT(*) AS Num FROM userdata WHERE userdata_email = :email");
                $query->bindParam(":email",$email);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if($result["Num"] > 0){
                    echo "error: email already exists";
                    break;
                }
                else{
                    $insert = "INSERT INTO userdata(userdata_email, userdata_password)
                    VALUES (:email, :password)";
                    $query = $conn->prepare($insert);
                    $query->bindParam(":email", $email);
                    $query->bindParam(":password", $pw);
                    $query->execute();
                    echo "Success";
                }
            }
            break;
        //this is for weblogin. devices that send location use methods in device.php
        //not working
        case "login":
            if($_REQUEST['email'] && $_REQUEST['pw']){
                $email = strtolower($_REQUEST['email']);
                $pw = $_REQUEST['pw'];

                $query = $conn->prepare("SELECT COUNT(*) AS Num FROM userdata WHERE userdata_email = :email AND userdata_password = :pw");
                $query->bindParam(":email",$email);
                $query->bindParam(":pw",$pw);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if($result["Num"] ==1){
                    echo "error: email already exists";
                    break;
                }
                else{
                    $insert = "INSERT INTO userdata(userdata_email, userdata_password)
                    VALUES (:email, :password)";
                    $query = $conn->prepare($insert);
                    $query->bindParam(":email", $email);
                    $query->bindParam(":password", $pw);
                    $query->execute();
                    echo "Success";
                }
            }
            break;
        case "changepw":
            if($_REQUEST['email'] && $_REQUEST['pw'] && $_REQUEST['newpw']){
                $email = strtolower($_REQUEST['email']);
                $pw = $_REQUEST['pw'];
                $newpw = $_REQUEST['newpw'];

                $query = $conn->prepare("SELECT COUNT(*) AS Num FROM userdata WHERE userdata_email = :email AND userdata_password = :pw");
                $query->bindParam(":email",$email);
                $query->bindParam(":pw",$pw);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if($result["Num"] ==1){
                    $query = $conn->prepare("UPDATE userdata SET userdata_password = :newpw WHERE userdata_email = :email AND userdata_password = :pw");
                    $query->bindParam(":email",$email);
                    $query->bindParam(":pw",$pw);
                    $query->bindParam(":newpw",$newpw);
                    $query->execute();
                    echo "success: Password changed";
                    break;
                }else {
                    echo "error: Invalid login";
                }
            }

            break;
    }
    $conn = null;


}