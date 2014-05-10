<?php
/**
 * Created by IntelliJ IDEA.
 * User: VMprogramming
 * Date: 5/26/13
 * Time: 9:21 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<html>
<head>
    <title>Jia De's FYP</title>
	<style>
	div
{
border:15px solid transparent;
width:250px;
padding:10px 20px;
}

#round
{
-webkit-border-image:url(border.png) 30 30 round; /* Safari 5 */
-o-border-image:url(border.png) 30 30 round; /* Opera */
border-image:url(border.png) 30 30 round;
}

#stretch
{
-webkit-border-image:url("border.png") 30 30 stretch; /* Safari 5 */
-o-border-image:url("border.png") 30 30 stretch; /* Opera */
border-image:url("border.png") 30 30 stretch;
}
</style>

</head>
<body bgcolor="#FFFFFF"><center><div id="stretch">
<h1><font face="verdana">Welcome to Jia De's FYP Page</font></h1>
<form action="./viewer/view.php" method="post"><table>
    <tr><td><font face="verdana">Login ID: </font></td><td> <input id="username" name="username" type="text" /></td></tr>
    <tr><td><font face="verdana">Password: </font></td><td> <input id="password" name="password" type="password"/></td></tr>
    <tr><td><input type="submit" value="Submit"/></td></tr></table>
</form>
</div></center>
</body>
</html>