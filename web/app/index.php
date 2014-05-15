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
<?php 
include('config.php');
?>
<html lang="en">
<head>
	<title>micc Search</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="euTV search interface">
	<meta name="keywords" content="search interface, eutv, music, audio, documents, images, videos">
	<meta name="author" content="Lorenzo Cioni">
	<link rel="image_src" href="img/micc-logo.png" />
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon" />
	<link  rel="stylesheet" href="css/search-template.css" type="text/css">
	<script type="text/javascript" src="lib/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="lib/jquery.form.js"></script>
	<script type="text/javascript" src="js/config.js"></script>
	<script type="text/javascript" src="js/search.js"></script>
	<script type="text/javascript" src="js/interface.js"></script>
</head>
<body>
  <div id="container">
	<div id="logo">
		<a href="#">
			<img src="img/micc-logo.png" alt="micc logo">
		</a>
	</div>
	<?php 
		session_start();
		if(isset($_SESSION['user'])){
			echo '<div id="user-area-logged">';
			echo '<a href="user-collection">';
			echo '<img class="user-avatar" src="'.$_SESSION['avatar'].'" alt="avatar">'.$_SESSION['user'].'</a>';
		?>
			<form action="index.php" method="post">
				<input type="submit" name="logout" class="logout-button btn btn-primary" value="Logout">
			</form>
		<?php
		}
		else {
	?>
	<div id="user-area">
		<a href="login.php">Login</a>
		<a href="register.php" class="tab">Register</a>
	<?php 
	}	
	if(isset($_POST["logout"])){
		session_destroy();
		header ("Location: index.php");
	}
	?>
	</div>
	<?php 
	if(isset($_SESSION['user'])){
	?>
	<div id="upload-wrapper">
		<a href="upload">
			Upload media
			<img alt="upload" src="img/upload-icon.png">
		</a>
	</div>
	<?php 
	}
	?>
	<div id="main-wrapper">	
		<form action="main/index.php" id="form-search" method="get">
			<input type="text" name="q" id="search-form" placeholder="Search" autocomplete="off" autofocus="autofocus"/>
		</form>	
		<form id="upload-form" action="php/uploadImage.php" method="post" enctype="multipart/form-data">
				<div id="filedrag">
    				<div class="dropzone" id="dragandrophandler">
      					<span id="drag-text" class="big-font" >Drop file here<br>or</span>					
					</div>
					<img src="img/info.png" alt="info" id="info-image">
					<span id="info-tooltip">
						Search is different depending on the file media type:
						<ul>
							<li>IMAGE: <i>similarity by color layout feature</i></li>
							<li>DOCUMENT, VIDEO and AUDIO: <i>search by file name</i></li>
						</ul>
					</span>
					<div id="progress">
        				<div id="bar"></div>
        				<div id="percent">0%</div>
					</div>
    				<div id="upload-message"></div>
    				<span class="query-file-box btn btn-primary">
    					<span class="select-file-text">Select a file</span>
     					<input type="file" size="60" name="file" id="query-file" />
     				</span>
				</div>
		  </form>
	</div>
	</div>
  </div>
</div>
</body>
</html>
