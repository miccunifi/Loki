<?

/**
 * Copyright 2014 Micc (Media Integration and Communication Center) http://www.micc.unifi.it
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @author      Media Integration and Communication Center http://www.micc.unifi.it (Micc) <info@micc.unifi.it>
 * @license     Apache License https://github.com/miccunifi/Loki/LICENSE.txt
 * @link        Official page and description: http://www.micc.unifi.it/vim/opensource/loki-a-cross-media-search-engine/
 *              GitHub Repository: https://github.com/miccunifi/Loki
 * 
*/



?><!DOCTYPE html>
<html>
<?php 
include('config.php');
?>
<head>
	<meta http-equiv="Content-Type" content="text/html charset=UTF-8">	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/register.css" type="text/css">
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon" />
	<title>Register</title>
</head>
<body>
  <div id="container">
	<div id="register-form">	
		<h2>Register</h2>
		<form name="register-form" action="" method="post">	
			Name <br />
			<input type="text" name="name" class="input-form" placeholder="Name Surname"><br>
			<br />
			Email <br />
			<input type="email" name="email" class="input-form" placeholder="Email"><br>
			<br />	
			Password <br />
			<input type="password" name="password" class="input-form" placeholder="Password"><br>
			<br />
			Retype password <br />
			<input type="password" name="repeat-password" class="input-form" placeholder="Password"><br>
			<br />	
			<input type="submit" name="register" value="Register" class="flushright">
		</form>
	</div>
	<?php
	 
if(isset($_POST['register'])){
	if($_POST['email']!="" && $_POST['password']!=""){
		if($_POST['password'] == $_POST['repeat-password']){
				$user = $_POST['email'];
				$password = md5($_POST['password']);
				$name = $_POST['name'];
				$avatar = $interfacePath.'img/avatar-profilo.jpg';
				
				$result = mysql_query("SELECT username FROM users WHERE username ='" . $user . "'");
				if(mysql_num_rows($result)==0){
					$query = mysql_query("INSERT INTO users (username, password, name, avatar, mail, priviledge) VALUES ('".$user."','".$password."', '".$name."', '".$avatar."', '".$user."', 'super')");
					if(mysql_affected_rows()!= -1){
						echo "<span id='message'>Success!</span>";
						header ("Location: index.php");
					}
					else {
						echo "<span id='message'>SQL Error!</span>";
					}
				}
				else {
					echo "<span id='message'>User already in database.</span>";
				}
			}
			else {
				echo "<span id='message'>Password not correct</span>";
			}
	}
	else {
		echo "<span id='message'>Missing information!</span>";
	}
}
?>
	<a href="index.php" class="back-button">Back to Search</a>
  </div>
</body>
</html>