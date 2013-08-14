<html>
<head>
    <title>GCM Push Creator</title>
</head>
<body>
<?php
require_once('../db/conn.php');
$sql = "SELECT userId FROM gcm;";
?>
<form name="gcmForm" action="gcm.php" method="post">
    Choose user to send to.
    <select name="userId">
        <?php
        foreach ($conn->query($sql) as $row) {
        ?>
        <option value="<?php echo $row['userId']?>"><?php echo $row['userId']?></option>
        <?php
        }
        $conn = null;
        ?>
    </select>
    <br>
    Currently the accepted format is:
    <br>
    left image
    <br>
    {"Id":6, "Expiry":"05-27-2013 00:00:00", "Content":"Lorem ipsum dolor sit amet, consectetur adipiscing elit.", "Picture":"http://jiade.cheok.org/100l.jpg", "PictureLink":"http://google.com", "Barcode":"APPWIZZ"}
    <br>
    right image
    <br>
    {"Id":6, "Expiry":"05-27-2013 00:00:00", "Content":"Lorem ipsum dolor sit amet, consectetur adipiscing elit.", "Picture":"http://jiade.cheok.org/100r.jpg", "PictureLink":"http://google.com", "Barcode":"APPWIZZ"}
    <br>
    <textarea cols="50" rows="15" name="message" wrap="soft"></textarea>
    <input type="submit" value="Submit">
</form>
</body>
</html>