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
session_start();
?>
<head>
	<meta http-equiv="Content-Type" content="text/html charset=UTF-8">	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/login.css" type="text/css">
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon" />
	<title>Login</title>
</head>
<body>
  <div id="container">
	<div id="login-form">
		<form name="login" method="post" action="">
			<h2>Login</h2>
			Email <br />
			<input type="email" name="email" class="input-form" placeholder="Email"><br>
			<br />	
			Password <br />
			<input type="password" name="password" class="input-form" placeholder="Password"><br>
			<br />	
			<a href="register.php">Register new account</a>
			<input type="submit" name="login" value="Login" class="flushright">
	 	</form>
	</div>
<?php 
		if(isset($_POST['login'])){
			$password = md5($_POST['password']);
			$result = mysql_query("SELECT id_users, password, name, avatar FROM users WHERE username='".$_POST['email']."'") or trigger_error(mysql_error());
			if(mysql_num_rows($result)){
				while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
					if($row['password']==$password){
						echo "<span id='message'>Login successful!<span id='message'>";						$_SESSION['user'] = $row['name'];
						$_SESSION['email'] = $_POST['email'];
						$_SESSION['avatar'] = $row['avatar'];
						$_SESSION['user_id'] = $row['id_users'];
						header ("Location: index.php");
					}
					else {
						echo "<span id='message'>Invalid user or password!</span>";
					}
				}
			}
			else {
				echo "<span id='message'>Invalid user or password!</span>";
			}
		}
?>
	<a href="index.php" class="back-button">Back to Search</a>
  </div>
</body>
</html>