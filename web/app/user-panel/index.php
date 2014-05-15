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
<html ng-app="edit-app">
<?php 
include('../config.php');
session_start();

if(!isset ($_SESSION['user'])){
	header ("Location: ../login.php");
}
?>
<head>
	<meta http-equiv="Content-Type" content="text/html charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="../favicon.ico" type="image/x-icon" />
	<link rel="SHORTCUT ICON" href="../favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="../css/user-area.css" type="text/css">
	<script type="text/javascript" src="../lib/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="../lib/angular.js"></script>
	
	<script type="text/javascript" src="../lib/jquery.form.js"></script>
	<link href="../css/xeditable.css" rel="stylesheet">
	<script src="../lib/xeditable.js"></script>
	<script src="../js/user-area.js"></script>
	
	<title>Edit profile | <?php echo $_SESSION['user'];?></title>
</head>
</head>
<body ng-controller="Ctrl" ng-init="init()">
  <div id="container">
	<div id="user-form">
		<h1>Edit profile</h1>
		<a href="edit-profile.php" class="modify"></a><br />
		<p>
			<span>Name:</span>
			<a href="#" editable-text="user.name" buttons="no" e-placeholder="Enter name" onbeforesave="updateName($data)">
				{{ user.name || "None" }}
			</a>
		</p>
		<p>
			<span>Email:</span>
			<a href="#" editable-text="user.email" buttons="no" e-placeholder="Enter email" onbeforesave="updateEmail($data)">
				{{ user.email || "None" }}
			</a>
		</p>
		<p class="avatar-content">
		  <a href="#" editable-url="user.url" buttons="no" e-class="input-avatar" e-placeholder="Enter avatar URL" onbeforesave="updateAvatar($data)">
			<img id="avatar-img" alt="avatar" ng-src="{{user.avatar}}">
		  </a>
		</p>
		
		<div id="avatar-upload">
			<form id="upload-form" action="upload.php" method="post" enctype="multipart/form-data">
     			<input type="file" size="60" name="myfile" id="input-file">
     			<input type="submit" name="upload" value="Upload" id="input-submit">
 			</form>
  			<div id="progress">
        		<div id="bar"></div>
        		<div id="percent">0%</div>
			</div>
			<br/>   
			<div id="message"></div>	
		</div>
	</div>
	<a href="../index.php" class="back-button">Back to Search</a>
  </div>
</body>
</html>
