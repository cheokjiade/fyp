

<?php
require_once('../db/conn.php');
// Actual code starts here

$sql = "SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine <> 'InnoDB'";
$rs = mysql_query($sql);

while($row = mysql_fetch_array($rs))
{
    $tbl = $row[0];
    $sql = "ALTER TABLE $tbl ENGINE=INNODB";
    mysql_query($sql);
}
?>

