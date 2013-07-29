<?php
/**
 This service class allows a device to register to the system and it returns a session hash which has too be attached
 * to every location/action update. It also returns the session hash if the device and user have been previously registered
 */
if($_REQUEST['action']) {
    require_once('../db/conn.php');
    $action = $_REQUEST['action'];
    switch($action){
        case "connect":
            if($_REQUEST['email'] && $_REQUEST['pw'] && $_REQUEST['deviceid']){
                $email = strtolower($_REQUEST['email']);
                $pw = $_REQUEST['pw'];
                $deviceid = $_REQUEST['deviceid'];
                //check if user exists
                $query = $conn->prepare("SELECT * FROM userdata WHERE userdata_email = :email AND userdata_password = :pw");
                $query->bindParam(":email",$email);
                $query->bindParam(":pw",$pw);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if($result){
                    $userid = $result["userdata_id"];
                    $query = $conn->prepare("SELECT * FROM device WHERE device_id = :deviceid AND userdata_id = :userid");
                    $query->bindParam(":deviceid",$deviceid);
                    $query->bindParam(":userid",$userid);
                    $query->execute();
                    $result = $query->fetch(PDO::FETCH_ASSOC);
                    //device/user already registered so just find the session hash and return it
                    if($result){
                        $query = $conn->prepare("SELECT * FROM session WHERE device_id = :deviceid AND userdata_id = :userid");
                        $query->bindParam(":deviceid",$deviceid);
                        $query->bindParam(":userid",$userid);
                        $query->execute();
                        $result = $query->fetch(PDO::FETCH_ASSOC);
                        echo "Success: " . $result['session_hash'];
                    }else{
                        //new device/user
                        include_once("../util/hash.php");
                        //generate a random salt to secure the hash
                        $salt = generateSalt();
                        //create a new device
                        $insert = "INSERT INTO device(userdata_id, device_id, device_salt, device_details)
                          VALUES (:userid, :deviceid, :devicesalt, :devicedetails)";
                        $query = $conn->prepare($insert);
                        $query->bindParam(":userid", $userid);
                        $query->bindParam(":deviceid", $deviceid);
                        $query->bindParam(":devicesalt", $salt);
                        $query->bindParam(":devicedetails", $_REQUEST['details']);
                        $query->execute();
                        $sessionHash = hash("sha512", $userid . $deviceid . $salt);
                        //create a new session
                        $insert = "INSERT INTO session(session_hash, userdata_id, device_id, session_timestamp)
                          VALUES (:sessionHash, :userid, :deviceid, NOW())";
                        $query = $conn->prepare($insert);
                        $query->bindParam(":sessionHash", $sessionHash);
                        $query->bindParam(":userid", $userid);
                        $query->bindParam(":deviceid", $deviceid);
                        $query->execute();
                        echo "Success: " . $sessionHash;
                    }
                    break;
                }else{
                    echo "error: Invalid login";
                    break;
                }
            }else{
                echo "error: Required fields are email, password, deviceid and device details.";
            }

            break;

    }
    $conn = null;
}
