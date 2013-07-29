<html>
<head>
    <title></title>
</head>
<body>
<?php
if($_REQUEST['id']) {
    require_once('../db/conn.php');
    $userid = $_REQUEST['id'];

    $query = $conn->prepare("SELECT * FROM device WHERE userdata_id = :userid");
    $query->bindParam(":userid",$userid);
    $query->execute();
    //$result = $query->fetch(PDO::FETCH_ASSOC);

?>

<table>
    <tr>
        <th>Device ID</th>
        <th>Device Salt</th>
        <th>Device Details</th>
    </tr>
    <?php
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