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



?><?php
include('config.php');
session_start();

if(isset($_POST['email']) && isset($_POST['password'])){
	$user = $_POST['email'];
	$password = md5($_POST['password']);
	$result = mysql_query("SELECT id_users, password, name, avatar FROM users WHERE username='".$user."'") or trigger_error(mysql_error());
	if(mysql_num_rows($result) == 1){
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			if($row['password']==$password){
				$_SESSION['user'] = $row['name'];
				$_SESSION['email'] = $_POST['email'];
				$_SESSION['avatar'] = $row['avatar'];
				$_SESSION['user_id'] = $row['id_users'];
				echo 'Success!';
			}
			else {
				echo 'Invalid user or password!';
			}
		}
	}
	else {
		echo 'User not valid!';
	}
}

if(isset($_POST["logout"])){
	session_destroy();
	header ("Location: index.php");
}

?>