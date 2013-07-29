<html>
<head>
    <title></title>
</head>
<body>
<?php
require_once('../db/conn.php');
$sql = "SELECT * FROM userdata;";
?>
<table>
    <tr>
        <th>User ID</th>
        <th>User Email</th>
        <th>Devices</th>
    </tr>
<?php
foreach ($conn->query($sql) as $row) {
?>
    <tr>
        <td><?php echo $row['userdata_id']?></td>
        <td><?php echo $row['userdata_email']?></td>
        <td><a href="./viewdevices.php?id=<?php echo $row['userdata_id']?>">Devices</a> </td>
    </tr>
<?php
}
$conn = null;
?>

</body>
</html>
