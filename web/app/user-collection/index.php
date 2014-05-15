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
<html ng-app="search-result" lang="en">
<?php 
include('../config.php');
session_start();

if(!isset ($_SESSION['user'])){
	header ("Location: ../login.php");
}
?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>My Collection | <?php echo $_SESSION['user']?></title>
	<meta name="description" content="euTV search interface">
	<meta name="keywords" content="search interface, eutv, music, audio, documents, images, videos">
	<meta name="author" content="Lorenzo Cioni">
	<link rel="image_src" href="../img/micc-logo.png" />
	<link rel="icon" href="../favicon.ico" type="image/x-icon" />
	<link rel="SHORTCUT ICON" href="../favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="../css/main.css" type="text/css">
	<link rel="stylesheet" href="../css/fancybox.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css" />
	
	<script type="text/javascript" src="../lib/angular.js"></script>
	<script type="text/javascript" src="../lib/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="../lib/jquery-ui-1.10.4.js"></script>
	<script type="text/javascript" src="../lib/jquery.fancybox.js"></script>
	<script type="text/javascript" src="../lib/jquery.form.js"></script>
	<script type="text/javascript" src="../js/directives.js"></script>
	<script type="text/javascript" src="../js/config.js"></script>
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/controllers.js"></script>
	<script type="text/javascript" src="../js/droppable.js"></script>
	
	<!-- Required for components -->
	<script type="text/javascript" src="../js/im3iInclude.js"></script>
	<script type="text/javascript" src="../js/im3iSoapCalls.js"></script>
	<script type="text/javascript" src="../js/utils.js"></script>
	<script type="text/javascript" src="../js/solrCalls.js"></script>
	<script type="text/javascript" src="../lib/jquery.soapRequest.js"></script>
	<script type="text/javascript" src="../js/components/jquery.ui.imagecomponent.js"></script>
	<script type="text/javascript" src="../js/components/jquery.ui.documentcomponent.js"></script>
	<script type="text/javascript" src="../js/components/jquery.ui.audiocomponent.js"></script>
	<script type="text/javascript" src="../js/components/jquery.ui.videocomponent.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/jquery.ui.imagecomponent.css" />
	<link rel="stylesheet" type="text/css" href="../css/jquery.ui.documentcomponent.css" />
	<link rel="stylesheet" type="text/css" href="../css/jquery.ui.audiocomponent.css" />
	<link rel="stylesheet" type="text/css" href="../css/jquery.ui.videocomponent.css" />
	
	<link rel="stylesheet" media="screen" type="text/css" href="../css/tipsy.css"/>
        
    <script type="text/javascript" src="../lib/jquery.svg.js"></script>
    <script type="text/javascript" src="../lib/jquery.jscrollpane.min.js"></script>
    <script type="text/javascript" src="../lib/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="../lib/dom-drag.js"></script>
    <script type="text/javascript" src="../lib/jquery.editinplace.js"></script>
    <script type="text/javascript" src="../lib/jquery.extensions.js"></script>
    <script type="text/javascript" src="../lib/jquery.tipsy.js"></script>
    <link rel="stylesheet" media="screen" type="text/css" href="../css/jquery.jscrollpane.css"/>
    <link rel="stylesheet" media="screen" type="text/css" href="../css/jquery.jscrollpane.lozenge.css"/>
    <link rel="stylesheet" media="screen" type="text/css" href="../css/svg-rules.css"/>
    <link rel="stylesheet" media="screen" type="text/css" href="../css/iconic/iconic_fill.css" />
    <link rel="stylesheet" media="screen" type="text/css" href="../css/iconic/iconic_stroke.css" />
</head>
<body ng-controller="mediaCtrl" ng-init="user_init()">
	<div id="toolbar">
		<a href="../index.php">
			<img id="logo" src="../img/micc-logo-white.png" alt="micc logo">
		</a>
		<span id="collection-avatar">
			<span id="collection-text">My Collection</span>
			<!-- <a href="../user-panel">
				<img src="<?php //echo $_SESSION['avatar'];?>" alt="avatar">
			</a>
			 -->
		</span>
		<?php if(isset($_SESSION["user"])){?>
		<span id="upload-area">
			<a href="../upload">
				<img id="upload-logo" src="../img/upload-icon-white.png" alt="micc logo">
				<span id="upload-text">Upload media</span>
			</a>
		</span>
		<?php }?>
		<ul id="filters">
			<li id="video" class="media-filter" ng-click="videoToggle()"></li>
			<li id="image" class="media-filter" ng-click="imageToggle()"></li>
			<li id="audio" class="media-filter" ng-click="audioToggle()"></li>
			<li id="document" class="media-filter" ng-click="documentToggle()"></li>
		</ul>
		<a href="../main/index.php">
				<span id="back-text">Back to search</span>
		</a>
		<div class="login-menu">
			<?php echo $_SESSION["user"]; echo '&nbsp  &#9662'; ?>
		</div>
		<div id="login-form">
			<p class="email-bold">
				Email: <span class="user-email"><?php echo $_SESSION['email'];?> </span>
			</p>
			<img src="<?php echo $_SESSION['avatar'];?>" alt="avatar">
			<span id="user-panel">
				<a href="../user-panel">Edit profile</a>
			</span>
			<span id="collection">
				<a href="../user-collection">My Collection</a>
				<form id="form-logout" action="index.php" method="post">
					<br><input id="logout-button" type="button" name="logout" value="Logout">
				</form>
			</span>
		</div>
	</div>
	<div id="wrapper">
		<div id="left-panel">
			<div id="new-search">
				<form action="index.php" name="search" method="get" id="new-search-form">
					<input type="text" name="q" id="input-new-search" placeholder="Search" />
					<img src="../img/search-icon.png" alt="search" id="new-search-icon">
				</form>
			</div>
			<div id="similarity">
				<h4>Similarity search</h4>
				<img src="../img/info.png" alt="info" id="info-image">
					<span id="info-tooltip">
						You can drag&drop here any media in results set to start a new similarity search:
						<ul>
							<li>IMAGE: <i>similarity by color layout feature</i></li>
							<li>DOCUMENT: <i>similarity by text content</i></li>
							<li>VIDEO and AUDIO: <i>search by file name</i></li>
						</ul>
						You can also drag&drop an external file from your PC. In this case:
						<ul>
							<li>IMAGE: <i>similarity by color layout feature</i></li>
							<li>DOCUMENT, VIDEO and AUDIO: <i>search by file name</i></li>
						</ul>
					</span>
				<div class="dragged-file" droppable>
					<span id="drag-text">Drop file here</span>
					<div id="progress">
        				<div id="bar"></div>
        				<div id="percent">0%</div>
					</div>
    			</div>
    			<form id="upload-form" action="../php/uploadImage.php" method="post" enctype="multipart/form-data">
    				<span class="similar-box-file btn btn-primary">
    					<span class="select-file-text">Select file</span>
     					<input type="file" size="60" name="file" id="query-similar-file"/>
     				</span>
		  		</form>
			</div>
			<div id="active-filters">
				<div id="search-panel">
					<form action="../main/index.php" name="search" method="get" id="left-search-form">
						<input type="text" name="q" class="input-form-search" placeholder="Search" />
					</form>
					<form id="upload-form" action="../php/uploadImage.php" method="post" enctype="multipart/form-data">
						<div id="filedrag">
    						<div class="dropzone" id="dragandrophandler">
      							<span id="drag-text">Drop file here<br>to search similar images</span>					
							</div>
							<!--  
							<div id="progress">
        						<div id="bar"></div>
        						<div id="percent">0%</div>
							</div>
    						<div id="upload-message"></div>-->
     						<input type="file" size="60" name="file" id="query-file" />
						</div>
		  			</form>
					<div id="open-close">&#9654</div>
				</div>
				<h4>Active filters</h4>
					<ul class="active-filters">
						<li ng-repeat="filter in filters | filter:filter.active=true | orderBy:'text'">
							<div class="button-close" ng-click="addRemoveFilter(filter)"></div>
							<span class="active-filter">{{filter.text}}</span>
						</li>
					</ul>
			</div>
			<div id="filters-panel">
				<h4>
					Filters
					</h3>
					<input type="text" name="new-filter" id="new-filter"
						ng-model="query" placeholder="Search filter">
					<div id="filters-box">
						<ul class="filter-list">
							<li ng-repeat="filter in filters | filter:query | orderBy:'text'"
								ng-click="addRemoveFilter(filter)">{{filter.text}}
								<div class="check-button" ng-if="filter.active"></div>
							</li>
						</ul>
					</div>
			</div>
		</div>
		<div id="results" changeview>
			<div id="search-default">No results found</div>
			<div id="video-panel" class="media-panel" ng-hide="video.active">
				<div class="panel-header">
					<img alt="video" src="../img/video.png">
					<p>Videos</p>
				</div>
				<div id="video-content" class="content">
					<div class="scrollable">
						<img class="loader" src="../img/loader.gif" alt="loader">
						<div ng-repeat="video in videos" class="media">
						    <div class="buttons" data-author="{{video.author}}" data-title="{{video.title}}" data-id="{{video.id}}" ng-if="video.owner == <?php echo $_SESSION['user_id'];?>" >
								<img src="../img/button-close.png" id="delete-media-button" title="Delete media" alt="delete media">
								<img src="../img/edit-icon.png" id="edit-media-button" title="Edit media" alt="edit media">
							</div>
							<a class='video-trigger' href="{{video.src}}" data-id="{{video.id}}" data-owner="{{video.owner}}"
								data-title="{{video.title}}" ng-show='isVisible(video)'><img
								class="video-media" ng-src="{{video.thumb}}" alt="{{video.tags[0].text}}" data-id="{{video.id}}" draggable>
							</a>
						</div>
						<p class="scrollable-clearfix"></p>
					</div>
				</div>
			</div>
			<div id="image-panel" class="media-panel" ng-hide="image.active">
				<div class="panel-header">
					<img alt="images" src="../img/image.png">
					<p>Images</p>
				</div>
				<div id="image-content" class="content some-content-related-div">
					<div class="scrollable">
						<img class="loader" src="../img/loader.gif" alt="loader">
						<div ng-repeat="cluster in clusters | filter:(cluster.type='image')">
							<div class='cluster' data-cluster-id='{{cluster.id}}' ng-show='isClusterVisible()'>
								<img ng-src="{{cluster.media[0].thumb}}">
							</div>
							<div ng-repeat="image in cluster.media"
								class='cluster-element cluster-media' ng-show='isVisible(image)' cluster-id="{{cluster.id}}" >
								<a class="image-trigger cluster-image-trigger" data-title="{{image.title}}" data-id="{{image.id}}"
								href="{{image.src}}" data-owner="{{image.owner}}" data-keyframe="{{image.timepoint}}" data-sourceid="{{image.sourceID}}">
									<img class="image-media" ng-src="{{image.thumb}}" data-id="{{image.id}}" alt="{{image.tags[0].text}}" draggable>
								</a>
							</div>
						</div>
						<div ng-repeat="image in images" class='media'
							ng-show='isVisible(image)' data-cluster="{{image.inCluster}}">
							<div class="buttons" data-id="{{image.id}}" data-author="{{image.author}}" data-title="{{image.title}}" ng-if="image.owner == <?php echo $_SESSION['user_id'];?>" >
								<img src="../img/button-close.png" id="delete-media-button" title="Delete media" alt="delete media">
								<img src="../img/edit-icon.png" id="edit-media-button" title="Edit media" alt="edit media">
							</div>
							<a class='image-trigger image-media-trigger' href="{{image.src}}"  
								data-id="{{image.id}}" data-owner="{{image.owner}}" data-title="{{image.title}}" data-keyframe="{{image.timepoint}}" data-sourceid="{{image.sourceID}}"><img
								ng-src="{{image.thumb}}" class="image-media"  alt="{{image.tags[0].text}}" data-id="{{image.id}}" draggable>
							</a>
						</div>
						<p class="scrollable-clearfix"></p>
					</div>
				</div>
			</div>
			<div id="audio-panel" class="media-panel" ng-hide="audio.active">
				<div class="panel-header">
					<img alt="audio" src="../img/audio.png">
					<p>Audio</p>
				</div>
				<div id="audio-content" class="content">
					<div class="scrollable">
					  <img class="loader" src="../img/loader.gif" alt="loader">
					  <div ng-repeat="audio in audios" class="audio-media" ng-show='isAudioTextVisible(audio)' draggable>
							<div class="buttons" data-title="{{audio.title}}"  data-id="{{audio.id}}" data-author="{{audio.author}}" ng-if="audio.owner == <?php echo $_SESSION['user_id'];?>" >
								<img src="../img/button-close.png" id="delete-media-button" title="Delete media" alt="delete media">
								<img src="../img/edit-icon.png" id="edit-media-button" title="Edit media" alt="edit media">
							</div>					
						<a class='audio-trigger' data-owner="{{audio.owner}}"
							href="{{audio.src}}" data-id="{{audio.id}}"
							data-title="{{audio.title}}" data-author="{{audio.author}}"
							 tag="{{audio.tags[0].text}}"><p class='author' ng-class="{ 'no-author' : audio.author == ''}">{{audio.author}}</p>
							<p class='title' title="{{audio.title}}">{{audio.title}}</p> </a>
					  </div>
					  <p class="scrollable-clearfix"></p>
					</div>
				</div>
			</div>
			<div id="document-panel" class="media-panel"
				ng-hide="document.active">
				<div class="panel-header">
					<img alt="document" src="../img/document.png">
					<p>Documents</p>
				</div>
				<div id="document-content" class="content">
					<div class="scrollable">
						<img class="loader" src="../img/loader.gif" alt="loader">
						<div ng-repeat="document in documents" class="text-media"
							ng-show='isAudioTextVisible(document)' draggable>
							<div class="buttons" data-author="{{document.author}}" data-title="{{document.title}}" data-id="{{document.id}}" ng-if="document.owner == <?php echo $_SESSION['user_id'];?>" >
								<img src="../img/button-close.png" id="delete-media-button" title="Delete media" alt="delete media">
								<img src="../img/edit-icon.png" id="edit-media-button" title="Edit media" alt="edit media">
							</div>
							<a class='document-trigger' href="{{document.src}}"
								data-author="{{document.author}}" data-owner="{{document.owner}}" data-id="{{document.id}}"
								data-title="{{document.title}}" tag="{{document.tags[0].text}}"><p class='author' ng-class="{ 'no-author' : document.author == ''}">{{document.author}}</p>
								<p class='title' title="{{document.title}}">{{document.title}}</p> </a>
						</div>
						<p class="scrollable-clearfix"></p>
					</div>
				</div>
			</div>
			<div id="message"></div>
		</div>
	</div>
</body>
</html>
