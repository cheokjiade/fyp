<?php
require_once('../db/conn.php');
$gcmId  = isset($_REQUEST['gcmId']) ? $_REQUEST['gcmId'] : null;
$userId = isset($_REQUEST['userId']) ? $_REQUEST['userId'] : null;
$count = 0;
if(is_null($gcmId)){
    echo 'false';
}else{
    if(is_null($userId)){
        try{
            $query = $conn->prepare("SELECT COUNT(*) AS Num FROM gcm WHERE gcmId = :gcmId");
            $query->bindParam(":gcmId",$gcmId);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if($result["Num"] > 0){
                echo "false";
            }else{
                $insert = "INSERT INTO gcm (userId, gcmId) VALUES (NULL, :gcmId)";
                $query = $conn->prepare($insert);
                $query->bindParam(":gcmId",$gcmId);
                $query->execute();
                echo "true ". $gcmId;
            }
        }catch(PDOException $e){
            echo 'ERROR : ' . $e->getMessage();
        }

    }else {
        $query = $conn->prepare("SELECT COUNT(*) AS Num FROM gcm WHERE gcmId = :gcmId AND userId = :userId");
        $query->bindParam(":gcmId",$gcmId);
        $query->bindParam(":userId",$userId);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if($result["Num"] > 0){
            echo "false";
        }else{
            $query = $conn->prepare("UPDATE gcm SET userId = :userId WHERE gcmId = :gcmId");
            $query->bindParam(":gcmId",$gcmId);
            $query->bindParam(":userId",$userId);
            $query->execute();
            echo 'true';
        }

    }


}
$conn = null;
