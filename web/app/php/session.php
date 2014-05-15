<?php
include('../config.php');
session_start();

if(!isset($_SESSION['user'])) {
	print "Expired";
}
else {
	$result = mysql_query("SELECT name FROM users WHERE username='".$_SESSION['email']."'") or trigger_error(mysql_error());
	
	while($data = mysql_fetch_row($result)){
		$name = $data[0];
	}
	print $_SESSION['email']."/".$name;
}
?>