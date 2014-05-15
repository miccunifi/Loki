

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

var search = angular.module('search-result', []);

search.directive('draggable', function() {
	return {
		restrict : 'A',
		link : function(scope, elem, attr) {
			$(elem).draggable({
				helper : 'clone',
				appendTo: 'body',
				start: function (e, ui) {
					$('.tooltip').remove();
					if($(elem).hasClass('text-media') || $(elem).hasClass('audio-media') ){ //Drag text or audio
						ui.helper.width(200);
				        ui.helper.height(60);  
					} else {
				        ui.helper.width(70);
				        ui.helper.height(70);  
					}
			    }
			});
		}
	};
});

search.directive('droppable',function() {
	return {
		restrict : 'A',
		link : function(scope, elem, attr) {
			$(elem).droppable({
				hoverClass : 'dragged-hover',
				tolerance : 'pointer',
				drop : function(event, ui) {
					$('.tooltip').remove();
					if(ui.draggable.hasClass('audio-media')){ //Dragged audio
						console.log('dropped audio');
						var title = ui.draggable.find('a').attr('data-title');
						var author = ui.draggable.find('a').attr('data-author');
						var tags = ui.draggable.find('a').attr('tag').toLowerCase();
						var content = '<div class="audio-media">';
						if(author == ''){
							content += '<p class="author no-author"></p>';
						} else {
							content += '<p class="author">' + author + '</p>';
						}
						content += '<p class="title">' + title + '</p></div>';
						$('.dragged-file').addClass('dropped-file');
						$(".dragged-file").html(content);
						
						var query_url = "../php/solrProxy.php?proxy_url=" + CFG.solrSelectUrl + "q=" + encodeURIComponent(tags);
						scope.getVideos(query_url, false);	
						scope.getAudios(query_url, false);
						scope.getDocuments(query_url, false);
						scope.getImages(query_url, false);
						scope.getKeyframes(query_url, false);
					} else if (ui.draggable.hasClass('text-media')) { //Dragged document
						console.log('dropped document');
						var title = ui.draggable.find('a').attr('data-title');
						var author = ui.draggable.find('a').attr('data-author');
						var id = ui.draggable.find('a').attr('data-id');
						var content = '<div class="text-media">';
						if(author == ''){
							content += '<p class="author no-author"></p>';
						} else {
							content += '<p class="author">' + author + '</p>';
						}
						content += '<p class="title">' + title + '</p></div>';
						$('.dragged-file').addClass('dropped-file');
						$(".dragged-file").html(content);

						scope.getSimilarDocuments(id);
						var tags = title.toLowerCase().split(' ').join("+");
						//console.log(tags);
						var query_url = "../php/solrProxy.php?proxy_url=" + CFG.solrSelectUrl + "q=" + encodeURIComponent(tags + '&rows=5');
						scope.getVideos(query_url, false);	
						scope.getAudios(query_url, false);
						scope.getImages(query_url, false);
						scope.$apply();
					} else if(ui.draggable.hasClass('image-media')){
						console.log('dropped image');
						var src = ui.draggable.attr('src');
						var tags = ui.draggable.attr('alt');
						var id = ui.draggable.attr('data-id');
						var content = '<img src="'+ src + '" alt="'+ tags + '">';	
						var query_url = "../php/solrProxy.php?proxy_url=" + CFG.solrSelectUrl + "q=" + encodeURIComponent(tags + '&rows=5');
						scope.getSimilarImages(id);
						scope.getVideos(query_url, false);	
						scope.getAudios(query_url, false);
						scope.getDocuments(query_url, false);
						scope.$apply();
						$('.dragged-file').addClass('dropped-file');
						$(".dragged-file").html(content);
					} else if(ui.draggable.hasClass('video-media')){
						console.log('dropped video');
						var src = ui.draggable.attr('src');
						var tags = ui.draggable.attr('alt');
						//var id = ui.draggable.attr('data-id');
						var content = '<img src="'+ src + '" alt="'+ tags + '">';
						$('.dragged-file').addClass('dropped-file');
						$(".dragged-file").html(content);
						var query_url = "../php/solrProxy.php?proxy_url=" + CFG.solrSelectUrl + "q=" + encodeURIComponent(tags + '&rows=5');
						scope.getVideos(query_url, false);	
						scope.getAudios(query_url, false);
						scope.getDocuments(query_url, false);
						scope.getImages(query_url, false);
						scope.getKeyframes(query_url, false);
						scope.$apply();
					}
					$(".dragged-file").append('<span id="exit-tooltip"></span>');
					$('#exit-tooltip').html("Clear");
					$('#upload-form').hide();
				}
			});
		}
	};
});

search.directive('changeview', [ '$timeout', function($timeout) {
	return {
		link : function($scope, element, attrs) {
			$scope.$on('dataloaded', function(event) {
				$timeout(function() {

					if ($scope.video.active == false) {
						$('#video-panel').show();
					}
					if ($scope.image.active == false) {
						$('#image-panel').show();
					}
					if ($scope.audio.active == false) {
						$('#audio-panel').show();
					}
					if ($scope.document.active == false) {
						$('#document-panel').show();
					}
					
					if ($scope.video.disabled == true) {
						$("#video").addClass('filter-disabled');
					} else {
						$("#video").removeClass('filter-disabled');
					}
					if ($scope.image.disabled == true) {
						$("#image").addClass('filter-disabled');
					} else {
						$("#image").removeClass('filter-disabled');
					}
					if ($scope.audio.disabled == true) {
						$("#audio").addClass('filter-disabled');
					} else {
						$("#audio").removeClass('filter-disabled');
					}
					if ($scope.document.disabled == true) {
						$("#document").addClass('filter-disabled');
					} else {
						$("#document").removeClass('filter-disabled');
					}

					if ($(".video-trigger:visible").length == 0 && $("#video-content .cluster:visible").length == 0 && $('#video-content .loader').css('display') == 'none') {
						$('#video-panel').hide();
						$("#video").addClass('filter-hidden');
					} else {
						$("#video").removeClass('filter-hidden');
						$('#video-panel').show();
					}

					if ($(".image-trigger:visible").length == 0 && $("#image-content .cluster:visible").length == 0 && $('#image-content .loader').css('display') == 'none') {
						$('#image-panel').hide();
						$("#image").addClass('filter-hidden');
					} else {
						$("#image").removeClass('filter-hidden');
						$('#image-panel').show();
					}
					if ($(".audio-media:visible").length == 0 && $('#audio-content .loader').css('display') == 'none') {
						$('#audio-panel').hide();
						$("#audio").addClass('filter-hidden');
					} else {
						$("#audio").removeClass('filter-hidden');
						$('#audio-panel').show();
					}
					if ($(".text-media:visible").length == 0 && $('#document-content .loader').css('display') == 'none') {
						$('#document-panel').hide();
						$("#document").addClass('filter-hidden');
					} else {
						$("#document").removeClass('filter-hidden');
						$('#document-panel').show();				
					}
					resize();
				}, 0, false);
			});	
		}
	};
} ]);

function resize() {
	var count = numActivePanel();
	$('#search-default').hide();
	$('#filters-panel').show();
	
	$('#video-content .loader').css({
		'top': $('#video-content .scrollable').height() /2,
		'left': $('#video-content .scrollable').width()/2 - 15
	});
	$('#image-content .loader').css({
		'top': $('#image-content .scrollable').height() /2,
		'left': $('#image-content .scrollable').width()/2 - 15
	});
	$('#audio-content .loader').css({
		'top': $('#audio-content .scrollable').height() /2,
		'left': $('#audio-content .scrollable').width()/2 - 15
	});
	$('#document-content .loader').css({
		'top': $('#document-content .scrollable').height() /2,
		'left': $('#document-content .scrollable').width()/2 - 15
	});
	
		if (count == 1) {
			$('.media-panel').css("width", "99.8%");
		}
		if (count == 2) {
			$('.media-panel').css("width", "49.82%");
		}
		if (count == 3) {
			$('.media-panel').css("width", "33.15%");
		}
		if (count == 4) {
			$('.media-panel').css("width", "24.8%");
		}
		
		if(count == 0){
			$('#search-default').show();
			$('#filters-panel').hide();
		}
		
		var filters_panel_height = $('#left-panel').height() - 200 - 160;
		$('#filters-box').css('height', filters_panel_height - 125);
		
		$('#message').css('left', $('#results').width()/2 + $('#left-panel').width() - 80);
		$('#message').css('top', $('#results').height()/2);
}

function numActivePanel() {
	var count = 0;
	if ($('#video-panel').css('display') != 'none') {
		count++;
	}
	if ($('#image-panel').css('display') != 'none') {
		count++;
	}
	if ($('#audio-panel').css('display') != 'none') {
		count++;
	}
	if ($('#document-panel').css('display') != 'none') {
		count++;
	}
	return count;
}

$(window).resize(function(){
	clearTimeout($.data(this, 'resizeTimer'));
    $.data(this, 'resizeTimer', setTimeout(function() {
    	resize();
    }, 200));
});


//search.config([ "$routeProvider", "$locationProvider",
//		function($routeProvider, $locationProvider) {
//			$routeProvider.when("/", {
//				templateUrl : "similarity.html"
//			}).when("/user-collection", {
//				templateUrl : "user-collection.php"
//			}).otherwise({
//				redirectTo : '/'
//			});
//
////			 $locationProvider.html5Mode(true).hashPrefix('!');
//		} ]);
