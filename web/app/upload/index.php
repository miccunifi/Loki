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
session_start();
if(!isset ($_SESSION['user'])){
	header ("Location: ../login.php");
}
?>
<html lang="en">
<head>
	<title>micc Upload</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="micc search interface">
	<meta name="keywords" content="search interface, micc, music, audio, documents, images, videos">
	<meta name="author" content="Lorenzo Cioni">
	<link rel="image_src" href="img/micc-logo.png" />
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="SHORTCUT ICON" href="../favicon.ico" type="image/x-icon" />
	<link  rel="stylesheet" href="../css/search-template.css" type="text/css">
	<link  rel="stylesheet" href="../css/upload.css" type="text/css">
	<link  rel="stylesheet" href="../css/jquery-ui.css" type="text/css">
	<script type="text/javascript" src="../lib/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="../lib/jquery-ui-1.10.4.js"></script>
	<script type="text/javascript" src="../lib/jquery.form.js"></script>
	<script type="text/javascript" src="../js/config.js"></script>
	<script type="text/javascript" src="../js/upload.js"></script>
</head>
<body>
  <div id="container">
	<div>
		<a href="../">
			<img id="logo-home" src="../img/micc-logo.png" alt="micc logo">
		</a>
		<h1>Upload media</h1>
		<a href="../">
			<img id="back-button" src="../img/back-button.png" alt="back">
		</a>
	</div>
	<div id="main-wrapper" class="fix">	
		<form id="upload-form" action="../php/uploadMedia.php" method="post" enctype="multipart/form-data">
				<div id="filedrag">
    				<div class="dropzone" id="dragandrophandler">
      					<span id="drag-text"><span class="big-font">Drop file here<br> or</span></span>					
					</div>
					<div id="progress">
        				<div id="bar"></div>
        				<div id="percent">0%</div>
					</div>
    				<div id="upload-message"></div>
    				<span class="query-file-box btn btn-primary">
    					<span class="select-file-text">Select a file</span>
     					<input type="file" size="60" name="myfile" id="query-file" />
     				</span>
				</div>
				<div id="media-info">
					<input type="text" name="title" class="input" placeholder="Title"/ id="title-input">
					<input type="text" name="author" class="input" placeholder="Author" id="author-input"/>
					<input type="text" name="tags" class="input" placeholder="Tags (example, example)" id="tags-input" required/>
					<input type="submit" name="search-file" value="Upload" class="search-button btn btn-primary"/> 
				</div>
		  </form>
	</div>
	</div>
  </div>
</div>
</body>
</html>