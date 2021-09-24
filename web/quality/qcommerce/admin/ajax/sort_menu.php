<?php
// Connecting to Database
mysql_connect($hostname, $user_name, $password) or die('Cant Connceto to MySQL');
 
// Selecting Database
mysql_select_db($db_name) or die('Cant select Database');
 
$menu = $_POST['menu'];
for ($i = 0; $i < count($menu); $i++) {
    mysql_query("UPDATE `menu` SET `sort`=" . $i . " WHERE `id`='" . $menu[$i] . "'") or die(mysql_error());
}
