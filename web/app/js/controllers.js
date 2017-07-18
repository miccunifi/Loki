

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

// Media controller , Load and search media

function mediaCtrl($scope, $timeout) {

	//Initialize search view	
	$scope.init = function(collection) {
		if(typeof(collection)==='undefined') collection = true;
		var loc = location.href.split('/');
		if(loc[loc.length - 2] == 'main' || collection == false){
			// Initialize arrays
			clearScope();
			$scope.favourites = [];
			
			initializeFilters($scope);
			
			//Clear all previous results
			$('.scrollable div').hide();
			
			// Search parameter
			if(window.name != 'Error' && window.name != 'null' && window.name != ''){
				var url = window.name;
				var content = '<img class="drop-search-image" src="' + window.name + '">';
				$('.dragged-file').addClass('dropped-file');
				$(".dragged-file").removeClass('dropped-hover');
				$(".dragged-file").html(content);
				$(".dragged-file").append('<span id="exit-tooltip"></span>');
				$('#exit-tooltip').html("Clear");
				$('#upload-form').hide();
				$scope.getSimilarImagesURL(url);
			} else {
				$('.dragged-file').removeClass('dropped-file');
				$(".dragged-file #drag-text").html('Drop file here');
				var query = getURLParameter('q');
				if (query == 'null') {
					query = '';
				}
				
				
				document.title = query.split("+").join(" ") + " | micc Search";
							
				$('#input-new-search').val(query.split("+").join(" "));

				query += "&q.op=AND";
								
				var query_url = "../php/solrProxy.php?proxy_url=" + CFG.solrSelectUrl + "q=" + encodeURIComponent(query); //TODO escaping accent
				console.log('Query keys: ' + query);
				
				getFavourites();
				
				$scope.getVideos(query_url, false);	
				$scope.getAudios(query_url, false);
				$scope.getDocuments(query_url, false);
				$scope.getImages(query_url, false, true);
				$scope.getKeyframes(query_url, false, true);
			}
		} else {
			$scope.user_init();
		}
	};
	
	// Initialize my collection	
	$scope.user_init = function() {
		clearScope();
		initializeFilters($scope);

		//Clear all previous results
		$('.scrollable div').hide();
		
		// Search parameter
		var query = getFavourites();
		var query_url;
		
		query_url = CFG.solrProxyUrl + "?proxy_url=";
		var url = CFG.solrSelectUrl + "q=*&fq=id:" + query;
		query_url += encodeURIComponent(url).replace(/'/g,"%27").replace(/"/g,"%22");	
		
		$scope.getVideos(query_url, true);
		$scope.getImages(query_url, true, false);	
		$scope.getAudios(query_url, true);
		$scope.getDocuments(query_url, true);
		$scope.getKeyframes(query_url, true, false);
	};
	
	// Shows similar images by id	
	$scope.getSimilarImages = function(id){
		clearScope();
		var query = utils.getSimilarImagesID(id);
		var query_url = CFG.solrProxyUrl +  "?proxy_url=";
		url = CFG.solrSelectUrl + "q=*&fq=id:" + query;
		query_url += encodeURIComponent(url).replace(/'/g,"%27").replace(/"/g,"%22");
		$scope.getVideos(query_url, false, false);
		$scope.getAudios(query_url, false, false);
		$scope.getDocuments(query_url, false, false);
		$scope.getImages(query_url, false, false);
		$scope.getKeyframes(query_url, false, true);
	};
	
	// Shows similar images by URL
	$scope.getSimilarImagesURL = function(url){
		console.log(url);
		var url_tmp = url.split("/");
		var filename = url_tmp[url_tmp.length - 1];
		filename = filename.split('.')[0];
		filename = (filename).split('-').join('+').split('_').join('+');
		console.log(filename);
		clearScope();
		var query = utils.getSimilarImagesURL(url);
		var query_url = CFG.solrProxyUrl +  "?proxy_url=";
		var query_tags = query_url + CFG.solrSelectUrl + "q=" + encodeURIComponent(filename + "&q.op=AND");
		url = CFG.solrSelectUrl + "q=*&fq=id:" + query;
		query_url += encodeURIComponent(url).replace(/'/g,"%27").replace(/"/g,"%22");
		$scope.getVideos(query_tags, false, false);
		$scope.getAudios(query_tags, false, false);
		$scope.getDocuments(query_tags, false, false);
		$scope.getImages(query_url, false, false);
		$scope.getKeyframes(query_url, false, true);
	};
	
	// Shows similar documents by id
	$scope.getSimilarDocuments = function(id){
		clearScope();
		var query_url = "../php/solrProxy.php?proxy_url=" +  encodeURIComponent(CFG.solrCoreUrl + "mlt?mlt.fl=text_extracted&q=id:" + id + "&rows=3");
		$scope.getDocuments(query_url, false);
	};
	
	// Add new filter	
	$scope.addFilter = function(filter) {
		$scope.filters.push(filter);
	};

	// Toggle filters
	$scope.addRemoveFilter = function(filter) {
		if (filter.active == true) {
			filter.active = false;
		} else {
			filter.active = true;
		}
		$scope.$broadcast('dataloaded');
	};

	// Return true if a media element must be hidden or visible
	$scope.isVisible = function(media) {
		if (activeFilters() == 0){
			if (media.inCluster != 0) {
				return false;
			} else {
				return true;
			}
		} else {
				if(media.tags.length == 0){
					return false;
				}
				for ( var i = 0; i < media.tags.length; i++) {
					if (media.tags[i] != undefined && media.tags[i].active == true) {
						return true;
					}
				}
			return false;
		}
	};
	
	// Return true if a media element must be hidden or visible
	$scope.isClusterVisible = function() {
		if (activeFilters() == 0){
			return true;
		} else {
			return false;
		}
	};

	$scope.isAudioTextVisible = function(media) {
		if (activeFilters() == 0) {
			return true;
		} else {
			for ( var i = 0; i < media.tags.length; i++) {
				if (media.tags[i] != undefined && media.tags[i].active == true) {
					return true;
				}
			}
			return false;
		}
	};
	
	// Check if a filter is already in set
	function inSet(filter) {
		var i = 0;
		while (i < $scope.filters.length) {
			if ($scope.filters[i].text == filter) {
				return true;
			}
			i++;
		}
		return false;
	};

	// Return the number of active filters
	function activeFilters() {
		var count = 0;
		for ( var i = 0; i < $scope.filters.length; i++) {
			if ($scope.filters[i].active == true) {
				count++;
			}
		}
		return count;
	};

	//Return the index of a filter in set
	$scope.indexOfFilter = function(filter) {
		var i = 0;
		while (i < $scope.filters.length) {
			if ($scope.filters[i].text == filter) {
				return i;
			}
			i++;
		}
		return null;
	};

	// Change views
	$scope.videoToggle = function() {
		if ($scope.video.active == false) {
			$scope.video.active = true;
			$("#video").addClass('filter-hidden');
		} else {
			$scope.video.active = false;
			$("#video").removeClass('filter-hidden');
		}
		$scope.$broadcast('dataloaded');
	};

	$scope.imageToggle = function() {
		if ($scope.image.active == false) {
			$scope.image.active = true;
			$("#image").addClass('filter-hidden');
		} else {
			$scope.image.active = false;
			$("#image").removeClass('filter-hidden');
		}
		$scope.$broadcast('dataloaded');
	};

	$scope.audioToggle = function() {
		if ($scope.audio.active == false) {
			$scope.audio.active = true;
			$("#audio").addClass('filter-hidden');
		} else {
			$scope.audio.active = false;
			$("#audio").removeClass('filter-hidden');
		}
		$scope.$broadcast('dataloaded');
	};

	$scope.documentToggle = function() {
		if ($scope.document.active == false) {
			$scope.document.active = true;
			$("#document").addClass('filter-hidden');
		} else {
			$scope.document.active = false;
			$("#document").removeClass('filter-hidden');
		}
		$scope.$broadcast('dataloaded');
	};

	$scope.getRandomIndex = function(cluster) {
		return Math.floor((Math.random() * cluster.media.length));
	};

	//Get videos from database
	$scope.getVideos = function (url, collection){
		$('#video-content .loader').css({
			'top': $('#video-content .scrollable').height() /2,
			'left': $('#video-content .scrollable').width() /2
		});
		$('#video-content .loader').show();
		$('#video-content .scrollable div').hide();
		$.ajax({
			 url : url + encodeURIComponent("&fq=type:video"),
			 method : "GET",
			 dataType : "xml",
			 success : function(data) {
				 var videos;
				 videos = $(data).find('doc');
			
				 for ( var k = 0; k < $(videos).length; k++) {
					 var path = $(videos[k]).find('str[name="dataserverpath"]').text();
					 var src = path + $(videos[k]).find('str[name="filename"]').text();
					 var thumb = CFG.mediaDirPath + "video/thumb/" + $(videos[k]).find("str[name='filename']").text() + '.jpg';
					 // Thumbnail (timthumb)
					 thumb = '../php/timthumb.php?src=' + thumb + '&&w=70&h=70&zc=1';
					 var title = $(videos[k]).find("str[name='title']").text();
					 var video_tags = $(videos[k]).find('arr').find('str');
					 var id = $(videos[k]).find("str[name='id']").text();
					 var owner = $(videos[k]).find("int[name='owner']").text();
					 var author = $(videos[k]).find("str[name='author']").text();
					 if(owner == ''){
						 owner = null;
					 }
					
					 var tags = [];
				
					 for ( var v = 0; v < $(video_tags).length; v++) {
						 if (!inSet($(video_tags[v]).text())) {
							 $scope.$apply(function() {
								 $scope.filters.push({
									 text : $(video_tags[v]).text(),
									 active : false
								 });
							 });
						 }
						 var index = $scope.indexOfFilter($(video_tags[v]).text());
						 tags.push($scope.filters[index]);		
				     }
						
					 var favourite = false;
						
					if(collection == true){
						favourite = true;
					}
					else {
						//User is connected
						if($scope.favourites != 'false'){		
							if(isFavourite(id, $scope.favourites) == true){
								console.log(id + ' favourite');
								favourite = true;
							}
						}
					}

					$scope.$apply(function() {
						$scope.videos.push({
							id: id,
							src : src,
							thumb : thumb,
							title : title,
							tags : tags,
							favourite: favourite,
							inCluster : false,
							owner : owner,
							author : author
						});
					});
				}
					if($scope.videos.length > 0){
						$scope.$apply(function() {
							$scope.video.disabled = false;
						});
					}
				$('#video-content .loader').hide();
//				makeClusters();
				$scope.$broadcast('dataloaded');
			},
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	};
	
	//Get images from database	
	$scope.getImages = function (url, collection, cluster){
		$('#image-content .loader').css({
			'top': $('#image-content .scrollable').height() /2,
			'left': $('#image-content .scrollable').width() /2
		});
		$('#image-content .loader').show();
		$.ajax({
			url : url + encodeURIComponent("&fq=type:image"),
			method : "GET",
			dataType : "xml",
			success : function(data) {
				var images;
				images = $(data).find('doc');

				for ( var i = 0; i < $(images).length; i++) {
					var path = $(images[i]).find("str[name='dataserverpath']").text();

					var thumb = path;

					if ($(images[i]).find("str[name='filename']"))
						thumb += $(images[i]).find("str[name='filename']").text();



					var title = $(images[i]).find("str[name='title']").text();
					var id = $(images[i]).find("str[name='id']").text();
					var author = $(images[i]).find("str[name='author']").text();
					var owner = $(images[i]).find("int[name='owner']").text();
					if(owner == ''){
						owner = null;
					}
					
					var tags = [];

					var image_tags = $(images[i]).find('arr').find('str');

					for ( var t = 0; t < $(image_tags).length; t++) {
						var temp = ($(image_tags[t]).text().split(/(?:_| )+/));
						for ( var h = 0; h < temp.length; h++) {
							if (temp[h].length > 2) {
								if (!inSet(temp[h])) {
									$scope.$apply(function() {
										$scope.filters.push({
											text : temp[h],
											active : false
										});
									});
								}
							}

							var index = $scope.indexOfFilter(temp[h]);
							tags.push($scope.filters[index]);
						}
					}
					var favourite = false;
					
					if(collection == true){
						favourite = true;
					}
					else {
						//User is connected
						if($scope.favourites != 'false'){		
							if(isFavourite(id, $scope.favourites) == true){
								console.log(id + ' favourite');
								favourite = true;
							}
						}
					}

					$scope.$apply(function() {
						$scope.images.push({
							id : id,
							src : src,
							thumb : thumb,
							title : title,
							tags : tags,
							favourite: favourite,
							inCluster : 0,
							timepoint: null,
							sourceID: null,
							owner: owner,
							author: author
						});
					});
				}
				if($scope.images.length > 0){
					$scope.$apply(function() {
						$scope.image.disabled = false;
					});
				}
				if(cluster == true){
					makeClusters();
				}			
				$('#image-content .loader').hide();
				$scope.$broadcast('dataloaded');
			},
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	};
	
	//Get keyframes from database	
	$scope.getKeyframes = function (url, collection, cluster){
		$('#image-content .loader').css({
			'top': $('#image-content .scrollable').height() /2,
			'left': $('#image-content .scrollable').width() /2
		});
		$('#image-content .loader').show();
		$.ajax({
			url : url + encodeURIComponent("&fq=type:keyframe"),
			method : "GET",
			dataType : "xml",
			success : function(data) {
				var images;
				images = $(data).find('doc');

				for ( var i = 0; i < $(images).length; i++) {
					var path = CFG.mediaDirPath + "image/";
					var src = path + $(images[i]).find("str[name='filename']").text();

					// Thumbnail (timthumb)
					var thumb = '../php/timthumb.php?src=' + src + '&&w=70&h=70&zc=1';
					var title = $(images[i]).find("str[name='title']").text();
					var sourceID = $(images[i]).find("str[name='id_media']").text();
					var id = $(images[i]).find("str[name='id']").text();
					var author = $(images[i]).find("str[name='author']").text();
					var timepoint = $(images[i]).find("int[name='timepoint']").text();
					var owner = $(images[i]).find("int[name='owner']").text();
					if(owner == ''){
						owner = null;
					}
					var tags = [];

					var image_tags = $(images[i]).find('arr').find('str');

					for ( var t = 0; t < $(image_tags).length; t++) {
						var temp = ($(image_tags[t]).text().split(/(?:_| )+/));
						for ( var h = 0; h < temp.length; h++) {
							if (temp[h].length > 4) {
								if (!inSet(temp[h])) {
									$scope.$apply(function() {
										$scope.filters.push({
											text : temp[h],
											active : false
										});
									});
								}
							}

							var index = $scope.indexOfFilter(temp[h]);
							tags.push($scope.filters[index]);
						}
					}
					var favourite = false;
					
					if(collection == true){
						favourite = true;
					}
					else {
						//User is connected
						if($scope.favourites != 'false'){		
							if(isFavourite(id, $scope.favourites) == true){
								console.log(id + ' favourite');
								favourite = true;
							}
						}
					}

					$scope.$apply(function() {
						$scope.images.push({
							id : id,
							src : src,
							thumb : thumb,
							title : title,
							tags : tags,
							favourite: favourite,
							inCluster : 0,
							timepoint: timepoint,
							sourceID: sourceID,
							owner: owner,
							author: author
						});
					});
				}
				if($scope.images.length > 0){
					$scope.$apply(function() {
						$scope.image.disabled = false;
					});
				}
				if(cluster == true){
					makeClusters();
				}
				
				$('#image-content .loader').hide();
				$scope.$broadcast('dataloaded');
			},
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	};
	
	// Get audio from database	
	$scope.getAudios = function (url, collection){
		$('#audio-content .loader').css({
			'top': $('#audio-content .scrollable').height() /2,
			'left': $('#audio-content .scrollable').width() /2
		});
		$('#audio-content .loader').show();
		$('#audio-content .scrollable div').hide();
		$.ajax({
			url : url + encodeURIComponent("&fq=type:audio"),
			method : "GET",
			dataType : "xml",
			success : function(data) {
				var audios;
				audios = $(data).find('doc');

				for ( var j = 0; j < $(audios).length; j++) {
					var path = $(audios[j]).find("str[name='dataserverpath']").text();
					var src = path + $(audios[j]).find("str[name='filename']").text();
					var title = $(audios[j]).find("str[name='title']").text();
					if (title == '') {
						title = $(documents[s]).find("str[name='mediauri']").text();
					}
					var author = $(audios[j]).find("str[name='author']").text();
					if (author == '') {
						author = '';
					}
					var owner = $(audios[j]).find("int[name='owner']").text();
					if(owner == ''){
						owner = null;
					}
					var id = $(audios[j]).find("str[name='id']").text();
					var audio_tags = $(audios[j]).find('arr').find('str');

					var tags = [];

					var temp = ((author + ' ' + title).split(/(?:_| )+/));
					for ( var h = 0; h < $(temp).length; h++) {
						if((temp[h]).length > 2){
							if (!inSet(temp[h])) {
								$scope.$apply(function() {
									$scope.filters.push({
										text : temp[h],
										active : false
									});
								});
							}
						}

						var index = $scope.indexOfFilter(temp[h]);
						tags.push($scope.filters[index]);
					}
					
					for ( var t = 0; t < $(audio_tags).length; t++) {
						temp = ($(audio_tags[t]).text().split(/(?:_| )+/));
						for ( var h = 0; h < temp.length; h++) {
							if (temp[h].length > 4) {
								if (!inSet(temp[h])) {
									$scope.$apply(function() {
										$scope.filters.push({
											text : temp[h],
											active : false
										});
									});
								}
							}

							var index = $scope.indexOfFilter(temp[h]);
							tags.push($scope.filters[index]);
						}
					}

					var favourite = false;
					
					if(collection == true){
						favourite = true;
					}
					else {
						//User is connected
						if($scope.favourites != 'false'){		
							if(isFavourite(id, $scope.favourites) == true){
								console.log(id + ' favourite');
								favourite = true;
							}
						}
					}
					
					$scope.$apply(function() {
						$scope.audios.push({
							id : id,
							src : src,
							author : author,
							title : title,
							favourite: favourite,
							tags : tags,
							owner: owner
						});
					});

				}
				if($scope.audios.length > 0){
					$scope.$apply(function() {
						$scope.audio.disabled = false;
					});
				}
				$('#audio-content .loader').hide();
				$scope.$broadcast('dataloaded');
			},
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	};
	
	// Get documents from database	
	$scope.getDocuments = function (url, collection){
		$('#document-content .loader').css({
			'top': $('#document-content .scrollable').height() /2,
			'left': $('#document-content .scrollable').width()/2
		});
		$('#document-content .loader').show();
		$('#document-content .scrollable div').hide();
		$.ajax({
			url : url + encodeURIComponent("&fq=type:document"),
			method : "GET",
			dataType : "xml",
			success : function(data) {
				//console.log(data);
				var documents;
				documents = $(data).find('result[name="response"]').find('doc');

				for ( var s = 0; s < $(documents).length; s++) {
					var path = $(documents[s]).find("str[name='dataserverpath']").text();
					var src = path + $(documents[s]).find("str[name='filename']").text();
					var title = $(documents[s]).find("str[name='title']").text();
					if (title == '') {
						title = $(documents[s]).find("str[name='mediauri']").text();
					}
					var author = $(documents[s]).find("str[name='author']").text();
					if (author == '') {
						author = '';
					}
					var owner = $(documents[s]).find("int[name='owner']").text();
					if(owner == ''){
						owner = null;
					}
					var id = $(documents[s]).find("str[name='id']").text();

					var tags = [];
					//var doc_tags = $(documents[s]).find('arr').find('str');
					//for ( var t = 0; t < $(doc_tags).length; t++) {
					
						var temp = ((author + ' ' + title).split(/(?:_| )+/));
						for ( var h = 0; h < $(temp).length; h++) {
							if((temp[h]).length > 2){
								if (!inSet(temp[h])) {
									$scope.$apply(function() {
										$scope.filters.push({
											text : temp[h],
											active : false
										});
									});
								}
							}

							var index = $scope.indexOfFilter(temp[h]);
							tags.push($scope.filters[index]);
						}	
					//}
						var favourite = false;
						
						if(collection == true){
							favourite = true;
						}
						else {
							//User is connected
							if($scope.favourites != 'false'){		
								if(isFavourite(id, $scope.favourites) == true){
									console.log(id + ' favourite');
									favourite = true;
								}
							}
						}

					$scope.$apply(function() {
						$scope.documents.push({
							id : id,
							src : src,
							author : author,
							title : title,
							favourite: favourite,
							tags : tags,
							owner: owner
						});
					});
				}
				if($scope.documents.length > 0){
					$scope.$apply(function() {
						$scope.document.disabled = false;
					});
				}
				$('#document-content .loader').hide();
				$scope.$broadcast('dataloaded');
			},
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	}
	
	// Get favourites	
	function getFavourites(){
		//Getting the id of the favourite media	
		var favourites = null;
		$.ajax({
			url : CFG.absolutePath + 'user-collection/favourites.php',
			method : "GET",
			dataType : "html",
			success : function(data) {
				favourites = data;
			},
			async: false,
			error : function(err) {
				console.log("Error: " + err);
			}
		});
		
		var query = '';
		
		if(favourites != 'false' && favourites != null){
			$scope.favourites = favourites.split(',');
			query = '(';
			for (var i = 0; i < $scope.favourites.length - 1; i++){
//				console.log($scope.favourites[i]);
				query += $scope.favourites[i] + '%20';
			}
			query += '1)';
		} else {
			query = '0';
		}		
		return query;
	}
	

	
	// Clear scope
	function clearScope(){	
		$scope.videos = [];
		$scope.images = [];
		$scope.audios = [];
		$scope.documents = [];
		$scope.clusters = [];
		$scope.filters = [];
		console.log('Arrays initialized');
	}
	
	function initializeFilters(scope){
			scope.video = {
				text : 'video',
				active : false,
				disabled: true
			};
			scope.image = {
				text : 'image',
				active : false,
				disabled: true
			};
			scope.audio = {
				text : 'audio',
				active : false,
				disabled: true
			};
			scope.document = {
				text : 'document',
				active : false,
				disabled: true
			};
	}

	
	//Check if a media is in the collection
	function isFavourite(id, favourites){
		for(var i = 0; i < (favourites.length - 1); i++){
			if(id == favourites[i]){
				return true;
			}
		}
		return false;
	}


	// Clusters type
	function cluster(id, type, media) {
		this.id = id;
		this.type = type;
		this.media = media;
	};
	
	function makeClustersa(scope){
		console.log(scope.images);
	}
	
	function makeClusters() {
		console.log('Clustering images');
		var clusters = [];
		var clusterID = $scope.clusters.length;
		// Make images clusters
		var images = $scope.images;
		for (var i = 0; i < images.length; i++) {
			var img = images[i];
			if(img.inCluster == 0){
				//Find very similar images (distance set to 10)
				var simID = decodeURIComponent(utils.getSimilarImagesID(img.id, CFG.LIREdistanceCluster));
				simID = simID.replace("(", "").replace(" 1)", "").split(" ");
				if(simID.length > 1){
					var imgs = [];
					clusterID += 1;
					var temp_id = [];
					for (var j = 0; j < simID.length; j++) {
						for (var k = 0; k < $scope.images.length; k++){
							if(simID[j] == $scope.images[k].id) {
								if($scope.images[k].inCluster == 0){
									temp_id.push(k);
								}	
								break;
							}
						}
					}

					if(temp_id.length > 1){
						for(var s = 0; s < temp_id.length; s++){
							$scope.$apply(function() {
								$scope.images[temp_id[s]].inCluster = clusterID;
							});
							imgs.push($scope.images[temp_id[s]]);
						}
						clusters.push(new cluster(clusterID, 'image', imgs));
					}
				}
			}		
		}
		if(clusters.length > 0){
			$scope.$apply(function() {
				$scope.clusters = clusters;
			});
		}
	};
};

// Get search parameters
function getURLParameter(name) {
	return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(
			location.search) || [ , null ])[1]);
}