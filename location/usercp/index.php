<html>
<head>
    <title>User CP</title>
</head>
<body>
<table>
    <tr>
        <th>Device ID</th>
        <th>Device Salt</th>
        <th>Device Details</th>
    </tr>
    <?php
    if($_REQUEST['action']) {
        require_once('../db/conn.php');
        $email = strtolower($_REQUEST['email']);
        $pw = $_REQUEST['pw'];

        $query = $conn->prepare("SELECT d.* FROM device d, userdata u WHERE  u.userdata_id = d.userdata_id AND u.userdata_email = :email AND u.userdata_password = :pw");
        $query->bindParam(":email",$email);
        $query->bindParam(":pw",$pw);
        $query->execute();
        //$result = $query->fetch(PDO::FETCH_ASSOC);

        foreach ($query->fetchAll() as $row)  {
        ?>
        <tr>
            <td><?php echo $row['device_id']?></td>
            <td><?php echo $row['device_salt']?></td>
            <td><?php echo $row['device_details']?></td>
        </tr>

        <?php
        }
        $conn = null;
    }
    ?>


</table>
</body>
</html>