

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

$(document).ready(function() {
	
	$('#query-file').on('change', function(){
		var filename = $('#query-file').val();
		filename = filename.split("\\")[2];
		var ext = filename.split('.')[1].toLowerCase();
		if(ext == 'jpg' 
			|| ext == 'png' 
			|| ext == 'gif'
			|| ext == 'jpeg'){
			window.name = 'null';
			$('#upload-form').submit();
			$('#open-close').click();
		} else {
	    	$('.input-form-search').val((filename.split('.')[0]).toLowerCase());
	    	$('#left-search-form').submit();
		}
	});
	
});

//New search form
$(document).on('submit', '#new-search-form', function(e){
	e.preventDefault();
	var loc = location.href.split('/');
	if(loc[loc.length - 2] == 'main'){
		var content = '<span id="drag-text">Drop file here</span>';
		content += 	'<div id="progress"><div id="bar"></div><div id="percent">0%</div></div>';
		$('.dragged-file').html(content);
		e.preventDefault();
		window.name = 'null';
		var searchTags = $('#input-new-search').val();
		searchTags = searchTags.split(" ").join("+");
		window.history.pushState("", "micc Search", CFG.absolutePath + "main/index.php?q=" + searchTags);
		var scope = angular.element(wrapper).scope();
		scope.init();
	} else {
		e.preventDefault();
		window.name = 'null';
		var searchTags = $('#input-new-search').val();
		searchTags = searchTags.split(" ").join("+");
		window.location.replace(CFG.absolutePath + "main/index.php?q=" + searchTags);
	}
});

$(document).on('click', '#new-search-icon', function(){
	$('#new-search-form').submit();
});

//Left search submit without reloading page
$(document).on('submit', '#left-search-form', function(e){
	var loc = location.href.split('/');
	if(loc[loc.length - 2] == 'main'){
		e.preventDefault();
		window.name = 'null';
		$('#open-close').click();
		var searchTags = $('.input-form-search').val().replace(' ', '+');
		window.history.pushState("", "micc Search", CFG.absolutePath + "main/index.php?q="+searchTags);
		var scope = angular.element(wrapper).scope();
		scope.init();
	} 
});

//Video open component
$(document).on('click', '.video-trigger', function(e){
	e.preventDefault();
	$('.tooltip').remove();
	var vid_id = $(this).attr('data-id');
	var vid_title = $(this).attr('data-title');
	
	var componentContainer = jQuery('<div></div>').addClass('component-container');

	var videoComponent = jQuery('<div id="videoComponent"></div>').addClass('component');
	
	var login = false;
	
	$.ajax({
		url : '../php/session.php',
		dataType : "html",
		success : function(data) {
		     if( data != "Expired" ) {
		         login = true;
		         console.log('Edit data allowed');
		     } else {
		    	 console.log('Edit data not allowed');
		     }
		},
		async: false
	});

	videoComponent.videocomponent({
		sourceId : vid_id,
		width : 700,
		height : 460,
		themeColor : "0xdddddd",
		textColor : "0x001100",
		highlightBg : "0xaa0000",
		highlightText : "0xffffff",
		serverOwner : "unifi",
		showData : true,
		allowEdit : login,
		sessionId : "1",
		searchResultPerPage : 5,
		duration : "6000",
		type : "annotations",
		dataPosition : "overlay",
		similaritySearch : true,
		startPoint : '0'
	});
	
	componentContainer.append(videoComponent);
	
	$.fancybox({ 
		content: componentContainer,
		openEffect	: 'none',
		closeEffect	: 'none',
		padding: 0,
		margin: 0,
		minWidth: 900,
		minHeight: 480,
		title: vid_title,
		afterClose: function(){
			//Fixing Chrome bug on replay video //TODO fix
			if(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())){
				window.location.reload();
			}
		}
	});
});

//Image open component
$(document).on('click', '.image-trigger', function(e){
	e.preventDefault();
	$('.tooltip').remove();
	if($(this).attr('data-keyframe') == ''){
		var image_id = $(this).attr('data-id');
		var image_title = $(this).attr('data-title');
		
		var componentContainer = jQuery('<div></div>').addClass('component-container');

		var imageComponent = jQuery('<div id="imageComponent"></div>').addClass('component');
		
		var login = false;
		$.ajax({
			url : '../php/session.php',
			dataType : "html",
			success : function(data) {
			     if( data != "Expired" ) {
			         login = true;
			         console.log('Edit data allowed');
			     } else {
			    	 console.log('Edit data not allowed');
			     }
			},
			async: false
		});
		
		imageComponent.imagecomponent({	
			serverOwner : "unifi",
			sourceId : image_id,
			width : "600px",
			themeColor: "rgb(206, 206, 206)",
			textColor: "rgb(33, 33, 33)",
			highlightBg:"rgb(35, 35, 35)",
			highlightText:"rgb(33, 33, 33)",
			sessionId: "1",
			showData: true,
			allowEdit: login,
			similaritySearch: false
		});
		
		componentContainer.append(imageComponent);
		
		$.fancybox({ 
			content: componentContainer,
			openEffect	: 'none',
			closeEffect	: 'none',
			padding: 0,
			margin: 0,
			minWidth: 860,
			minHeight: 430,
			title: image_title
		});


	} else {
		var vid_id = $(this).attr('data-sourceid');
		var startpoint = $(this).attr('data-keyframe');
		var title = $(this).attr('data-title');
		
		var componentContainer = jQuery('<div></div>').addClass('component-container');

		var videoComponent = jQuery('<div id="videoComponent"></div>').addClass('component');
		
		var login = false;
		
		$.ajax({
			url : '../php/session.php',
			dataType : "html",
			success : function(data) {
			     if( data != "Expired" ) {
			         login = true;
			         console.log('Edit data allowed');
			     } else {
			    	 console.log('Edit data not allowed');
			     }
			},
			async: false
		});

		videoComponent.videocomponent({
			sourceId : vid_id,
			width : 700,
			height : 460,
			themeColor : "0xdddddd",
			textColor : "0x001100",
			highlightBg : "0xaa0000",
			highlightText : "0xffffff",
			serverOwner : "unifi",
			showData : true,
			allowEdit : login,
			sessionId : "1",
			searchResultPerPage : 5,
			duration : "6000",
			type : "annotations",
			dataPosition : "overlay",
			similaritySearch : true,
			startPoint : startpoint
		});
		
		componentContainer.append(videoComponent);
		
		$.fancybox({ 
			content: componentContainer,
			openEffect	: 'none',
			closeEffect	: 'none',
			padding: 0,
			margin: 0,
			minWidth: 900,
			minHeight: 480,
			title: title
		});
	}
	

});

//Audio open component
$(document).on('click', '.audio-trigger', function(e){
	e.preventDefault();
	$('.tooltip').remove();
	var aux_id = $(this).attr('data-id');
	var aux_title = $(this).attr('data-title');
	
	var componentContainer = jQuery('<div></div>').addClass('component-container');

	var audioComponent = jQuery('<div id="audioComponent"></div>').addClass('component');
	
	var login = false;
	
	$.ajax({
		url : '../php/session.php',
		dataType : "html",
		success : function(data) {
		     if( data != "Expired" ) {
		         login = true;
		         console.log('Edit data allowed');
		     } else {
		    	 console.log('Edit data not allowed');
		     }
		},
		async: false
	});

	audioComponent.audiocomponent({
		sourceId : aux_id,
		width : 640,
		height : 170,
		themeColor : "0xdddddd",
		textColor : "0x001100",
		highlightBg : "0xaa0000",
		highlightText : "0xffffff",
		serverOwner : "unifi",
		showData : true,
		allowEdit : login,
		sessionId : "1",
		searchResultPerPage : 5,
		duration : "6000",
		type : "annotations",
		dataPosition : "overlay",
		similaritySearch : false,
		startPoint : 0
	});
	
	componentContainer.append(audioComponent);
	
	$.fancybox({ 
		content: componentContainer,
		openEffect	: 'none',
		closeEffect	: 'none',
		padding: 0,
		margin: 0,
		minWidth: 665,
		minHeight: 200,
		title: aux_title
	});
});

//Document open component
$(document).on('click', '.document-trigger', function(e){
	e.preventDefault();
	$('.tooltip').remove();
	var doc_id = $(this).attr('data-id');
	var doc_title = $(this).attr('data-title');
	
	var componentContainer = jQuery('<div></div>').addClass('component-container');

	var documentComponent = jQuery('<div id="documentComponent"></div>').addClass('component');
	
	var login = false;
	var user_name = "eutv";
	
	$.ajax({
		url : '../php/session.php',
		dataType : "html",
		success : function(data) {
		     if( data != "Expired" ) {
		         login = true;
		         var user = data.split('/');
		         user_name = user[0];
		         console.log('Edit data allowed');
		     } else {
		    	 console.log('Edit data not allowed');
		     }
		},
		async: false
	});

	documentComponent.documentcomponent({	
		serverOwner : "unifi",
		sourceId : doc_id,
		width : 400,
		height : 545,
		themeColor: "rgb(221, 221, 221)",
		textColor: "rgb(51, 51, 51)",
		borderColor: "rgb(51, 51, 51)",
		buttonColor: "rgb(153, 153, 153)",
		annotationMineBg : "rgb(0, 170, 85)",
		annotationMineText : "#FFF",
		annotationAllBg : "rgb(187, 51, 0)",
		annotationAllText : "#FFF",
		sessionId: 1,
		searchResultPerPage: 5,
		showData: true,
		allowEdit: login,
		annotationsQueryType: "all",
		ownerName : user_name,
		similaritySearch: true,
	});
	
	componentContainer.append(documentComponent);
	
	$.fancybox({ 
		content: componentContainer,
		openEffect	: 'none',
		closeEffect	: 'none',
		padding: 0,
		margin: 0,
		minWidth: 530,
		minHeight: 580,
		title: doc_title
	});
});

$(document).on('click', '#open-close', function(e) {
	if ($('#search-panel').css("left") == "-670px") {
		$('#open-close').html('&#9664');
		$('#search-panel').animate({
			left : "+=670px"
		}, 'slow');
		e.stopPropagation();
	} else {
		$('#search-panel').animate({
			left : "-=670px"
		}, 'slow');
		$('#open-close').html('&#9654');
	}
});

//Media reccomend

$(document).on('mouseenter', '.media', function(event) {
	$(this).find('.star').fadeIn('fast');
}).on('mouseleave', '.media', function() {
	$(this).find('.star').fadeOut('fast');
});

$(document).on('mouseenter', '.cluster-media', function(event) {
	$(this).find('.star').fadeIn('fast');
}).on('mouseleave', '.cluster-media', function() {
	$(this).find('.star').fadeOut('fast');
});

$(document).on('mouseenter', '.text-media', function(event) {
	$(this).find('.star').fadeIn('fast');
}).on('mouseleave', '.text-media', function() {
	$(this).find('.star').fadeOut('fast');
});

$(document).on('mouseenter', '.audio-media', function(event) {
	$(this).find('.star').fadeIn('fast');
}).on('mouseleave', '.audio-media', function() {
	$(this).find('.star').fadeOut('fast');
});

//Media filters hover

$(document).on('mouseenter', '.media-filter', function(event) {
	if(!$(this).hasClass('filter-disabled')){
		$(this).addClass('filter-hover');
	}
}).on('mouseleave', '.media-filter', function() {
	if(!$(this).hasClass('filter-disabled')){
		$(this).removeClass('filter-hover');
	}
});

// Image tooltip
$(document).on('mouseenter', '.image-media-trigger', function(event) {
	var offset = $(this).parent().position();
	if(offset.left < ($(this).parent().parent().width() - 400)){
		$('#image-content').append('<span class="tooltip right"></span>');
		offset.top += $(this).parent().height()/2 - 10;
		offset.left += ($(this).parent().width()/2 + 5);
	}
	else {
		$('#image-content').append('<span class="tooltip left-image"></span>');
		offset.top += $(this).parent().height()/2 - 10;
		offset.left += ($(this).parent().width()/2 + 5);
	}
	var image_href = $(this).attr("href");
	var title = $(this).attr('data-title');
	$('.tooltip').delay(300).fadeIn('slow');
	$('.tooltip').html('<div class="tooltip-inner"><img src="' + image_href + '"></div>');
	$('.tooltip-inner').append('<p class="tooltip-title">' + title + '</p>');
	var diff = (230 - $('.tooltip').width());
	if(offset.left > ($(this).parent().parent().width() - 300)){
		offset.left += diff;		
	}
	$('.tooltip').offset(offset);
}).on('mouseleave', '.image-media-trigger', function() {
	$('.tooltip').fadeOut('slow');
	$('.tooltip').remove();
});

//Cluster image tooltip
$(document).on('mouseenter', '.cluster-element', function(event) {
	var offset = $(this).position();
	if(offset.left < ($(this).parent().width() - 400)){
		$('#image-content').append('<span class="tooltip right"></span>');
		offset.top += $(this).height()/2 - 10;
		offset.left += ($(this).width()/2 + 5);
	}
	else {
		$('#image-content').append('<span class="tooltip left-image"></span>');
		offset.top += $(this).height()/2 - 10;
		offset.left += ($(this).width()/2 + 5);
	}
	var image_href = $(this).find('a').attr("href");
	$('.tooltip').delay(300).fadeIn('slow');
	$('.tooltip').html("<img src='" + image_href + "'>");
	var diff = (230 - $('.tooltip').width());
	if(offset.left > ($(this).parent().width() - 300)){
		offset.left += diff;		
	}
	$('.tooltip').offset(offset);
}).on('mouseleave', '.cluster-element', function() {
	$('.tooltip').fadeOut('slow');
	$('.tooltip').remove();
});


// Video tooltip
$(document).on('mouseenter','.video-trigger',function(event) {
			var offset = $(this).parent().position();
			offset.top += $(this).parent().height()/2 - 10;
			offset.left += ($(this).parent().width()/2 + 10);
			$('#video-content').append('<span class="tooltip right"></span>');
			$('.tooltip').offset(offset);
			var video_href = $(this).attr("href");
			var title = $(this).attr('data-title');
			$('.tooltip').delay(500).fadeIn('slow');
			$('.tooltip').html('<div class="tooltip-inner"></div>');
			$('.tooltip-inner').append("<video autoplay><source src='" + video_href + "' type='video/mp4'></video>");
			$('.tooltip-inner').append('<p class="tooltip-title">' + title + '</p>');
		}).on('mouseleave', '.video-trigger', function() {
	$('.tooltip').fadeOut('slow');
	$('.tooltip').remove();
});

// Audio tooltip
$(document).on('mouseenter','.audio-trigger', function(event) {
			var offset = $(this).parent().position();
			offset.top += $(this).parent().height()/2 - 10;
			offset.left += ($(this).parent().width()/2);
			$('#audio-content').append('<span class="tooltip left"></span>');
			$('.tooltip').offset(offset);
			var audio_href = $(this).attr("href");
			var title = $(this).attr('data-title');
			$('.tooltip').delay(300).fadeIn('slow');
			$('.tooltip').html('<div class="tooltip-inner"></div>');
			$('.tooltip-inner').append("<audio autoplay><source src='" + audio_href + "' type='video/mp4'></audio>");
			$('.tooltip-inner').append('<p class="tooltip-title">' + title + '</p>');
		}).on('mouseleave', '.audio-trigger', function() {
	$('.tooltip').fadeOut('slow');
	$('.tooltip').remove();
});

// Document tooltip
$(document).on('mouseenter','.document-trigger',function(event) {
					var offset = $(this).parent().position();
					offset.top += $(this).parent().height()/2 - 10;
					offset.left += ($(this).parent().width()/2);
					$('#document-content').append('<span class="tooltip left"><div class="tooltip-inner"><div id="tooltip-text"></div></div></span>');
					$('.tooltip').offset(offset);
					var id = $(this).attr("data-id");
					var title = $(this).attr('data-title');
					var query = getURLParameter('q');
					var url = '../php/solrProxy.php?proxy_url=';
					if(query == 'null'){
						url = url + encodeURIComponent(CFG.solrSelectUrl + "q=*&fq=%20id:" + id);
					} else {
						url = url + encodeURIComponent(CFG.solrSelectUrl + "hl=true&hl.fl=text_extracted&hl.fragsize=50&fl=*&fq=%20id:" + id +"&q=text_extracted:" + query + "&hl.snippets=5");
					}
					$('.tooltip').delay(1000).fadeIn('slow');
					$('.tooltip-inner').append('<p class="tooltip-title">' + title + '</p>');
					$.ajax({
						url : url,
						dataType : "xml",
						success : function(data) {
							console.log(data);
							var text = $(data).find('lst[name="highlighting"]').find('str');
							var result = '';
							for(var i = 0; i < text.length; i++){
								result += $(text[i]).text() + '...';
							}
							if(result == ''){
								result = $(data).find('str[name="text_extracted"]').text();
							}
							$('#tooltip-text').html(result);
						}
					});
				}).on('mouseleave', '.document-trigger', function() {
			$('.tooltip').fadeOut('slow');
			$('.tooltip').remove();
		});

// Cluster tooltip

$(document).on('mouseenter', '.cluster', function(event) {
	if(!$(this).hasClass('changed')){
		var offset = $(this).position();
		offset.top += $(this).height() / 3 + 15;
		offset.left += $(this).width()* 2 - 110;
		var cluster = $(this).attr('data-cluster-id');
		$(this).parent().parent().parent().append('<span class="cluster-tooltip tooltip right"></span>');
		$('.tooltip').offset(offset);
		$('.tooltip').delay(1500).fadeIn('slow');
		var content = '';
		var img = $('.cluster-element[cluster-id="' + cluster + '"]');
		for (var i = 0; $(img[i]).length; i++){
			var src = $(img[i]).find('img').attr('src');
			content = content + '<div class="cluster-preview"><img  src="' + src + '"></div>';
			if(i == 5){
				break;
			}
		}
		$('.tooltip').html(content);
	}
}).on('mouseleave', '.cluster', function() {
	if(!$(this).hasClass('changed')){
		$('.tooltip').fadeOut('slow');
		$('.tooltip').remove();
	}
});

// Open login menù
$(document).on('click', '.login-menu', function(e) {
	$('.login-menu').toggleClass('selected');
	$('#login-form').slideToggle(300);
	e.stopPropagation();
});

// Cluster click

$(document).on('click','.cluster', function() {
	$('.tooltip').remove();
					var clusterID = $(this).attr('data-cluster-id');
					if ($('.cluster-element[cluster-id="' + clusterID + '"]').css('display') == 'none') {
						$('.cluster-element[cluster-id="' + clusterID + '"]').fadeIn('slow');
						$(this).find('img').css('border', '4px solid #8E98AD');
						$(this).find('img').css({
											'-webkit-box-shadow' : '2px 2px 10px 5px rgba(0, 0, 0, 0.4)',
											'-moz-box-shadow' : '2px 2px 10px 5px rgba(0, 0, 0, 0.4)',
											'box-shadow' : '2px 2px 10px 5px rgba(0, 0, 0, 0.4)'
										});
						$(this).addClass('changed');
					} else {
						$('.cluster-element[cluster-id="' + clusterID + '"]').fadeOut('slow');
						$(this).find('img').css('border', '');
						$(this).removeClass('changed');
						$(this).find('img').css(
								{
									'-webkit-box-shadow' : '',
									'-moz-box-shadow' : '',
									'box-shadow' : ''
								});
					}
				});

// Close open divs
$(document).click(
		function(e) { //Close login form
			if (e.target.id != "login-form"
					&& e.target.className != "input-form-login"
					&& e.target.id != "form-login") {
				$('#login-form').slideUp(300);
				$('.login-menu').removeClass("selected");
			} //Close left search panel
			if (e.target.id != "search-panel"
					&& e.target.className != "input-form-search"
					&& e.target.id != "filedrag"
					&& e.target.id != "drop_button"
					&& e.target.id != "query-file"
					&& e.target.className != "search-button") {
				$('#search-panel').animate({
					left : "-670px"
				}, 'slow');
				$('#open-close').html('&#9654');
			} //Close clusters
			if (!$(e.target).parent().hasClass('cluster')
				&& !$(e.target).parent().hasClass('cluster-element')) {
					$('.cluster-element').fadeOut('slow');
					$('.cluster').find('img').css('border', '');
					$('.cluster').removeClass('changed');
					$('.cluster').find('img').css(
							{
								'-webkit-box-shadow' : '',
								'-moz-box-shadow' : '',
								'box-shadow' : ''
							});
					
			}
		});

$(document).keydown(function(e) {
	if (e.keyCode == 27) {
		$('#content').html('');
		$('#lightbox-panel').css('background-color', 'transparent');
		$("#lightbox, #lightbox-panel, #lightbox-title").fadeOut(500);
		$('#search-panel').animate({
			left : "-670px"
		}, 'slow');
		$('#open-close').html('&#9654');
		$('#login-form').slideUp(300);
		$('.login-menu').removeClass("selected");
	}
});

// Drag&Drop

$(document).on('click', '#exit-tooltip', function() {
	window.name = 'null';
	var scope = angular.element(wrapper).scope();
	scope.init();
	scope.$apply();
	var content = '<span id="drag-text">Drop file here</span>';
	content += 	'<div id="progress"><div id="bar"></div><div id="percent">0%</div></div>';
	$('.dragged-file').html(content);
	$('.dragged-file').removeClass('dropped-file');
	$('#upload-form').show();
});

//User collection add/remove

$(document).on('click', '.star', function() {
	var url = CFG.absolutePath + 'php/';
	var media_id = $(this).parent().find('a').attr('data-id');
	var success = '';
	if ($(this).find('img').hasClass('star-selected')) {
		$.ajax({
			url : url + "remove-media.php?media_id=" + media_id,
			method : "GET",
			dataType : "html",
			success : function(data) {
				success = data;
			},
			async: false,
			error : function(err) {
				console.log("Error: " + err);
			}
		});
		if(success == 'true'){
			$(this).find('img').removeClass('star-selected');
			$(this).removeClass('star-selected');
			$('#message').html('Removed from collection!');
			$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
		}
	}
	else{
		$.ajax({
			url : url + "add-media.php?media_id=" + media_id,
			method : "GET",
			dataType : "html",
			success : function(data) {
				success = data;
			},
			async: false,
			error : function(err) {
				console.log("Error: " + err);
			}
		});
		if(success == 'true'){	
			$(this).find('img').addClass('star-selected');
			$(this).addClass('star-selected');
			$('#message').html('Added to collection!');
			$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
		}
		else if(success == 'false'){
			$('#message').html('You must be logged!');
			$('#message').fadeIn('slow').delay(1200).fadeOut('slow');	
		}
	}
});

//User collection add/remove

$(document).on('click', '.star-collection', function() {
	var url = CFG.absolutePath + 'php/';
	var media_id = $(this).parent().find('a').attr('data-id');
	var success = '';
	if ($(this).find('img').hasClass('star-selected')) {
		$.ajax({
			url : url + "remove-media.php?media_id=" + media_id,
			method : "GET",
			dataType : "html",
			success : function(data) {
				success = data;
			},
			async: false,
			error : function(err) {
				console.log("Error: " + err);
			}
		});
		if(success == 'true'){
			angular.element(wrapper).scope().$broadcast('dataloaded');
			$(this).parent().remove();
			$('#message').html('Removed from collection!');
			$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
		}
	}
});

$(document).on('click', '.login-button', function(e){
	e.preventDefault();
	var email = $('#input-form-email').val();
	var password = $('#input-form-password').val();
	    $.ajax({
	        url: '../user-login.php',
	        type: 'POST',
	        data: {
	            email: email,
	            password: password
	        },
	        success: function(msg){
	                console.log(msg);
	            }                   
	        });
	window.location.reload();
});

$(document).on('click', '#logout-button', function(e){
	e.preventDefault();
	    $.ajax({
	        url: '../user-login.php',
	        type: 'POST',
	        data: {
	            logout: true
	        },
	        success: function(msg){
	                console.log(msg);
	            }                   
	        });
	window.location.reload();
});


$(document).on('click', '.search-button', function() {
	if ($('#query-file').val() != '') {
		$('#upload-form').submit();
		$('#dragandrophandler').hide();
	}
});

//Delete and edit button in My Collection
$(document).on('click', '#delete-media-button', function(){
	var self = $(this);
	var url = CFG.absolutePath + 'php/';
	var id = $(this).parent().attr('data-id');
	console.log('Deleting media ID: ' + id);
	var choose = confirm('Delete media with ID ' + id + '?');
	if(choose == true){
		$.ajax({
			url : url + "delete-media.php?media_id=" + id,
			method : "GET",
			dataType : "html",
			success : function(data) {
				if(data == 'true'){
					$('#message').html('Media deleted successfully!');
					$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
					self.parent().parent().fadeOut(300);
					solr.index();
					
				} else {
					$('#message').html('Error deleting media!');
					$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
				}			
			},
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	}
});

$(document).on('click', '#edit-media-button', function(){
	var id = $(this).parent().attr('data-id');
	var title = $(this).parent().attr('data-title');
	var author = $(this).parent().attr('data-author');
	if(author == undefined){
		author = '';
	}
	console.log('Editing media ID: ' + id);
	
	var content = '<div id="edit-box" data-id="' + id + '">';
	content += '<form id="edit-form" method="post" action="#">';
	content += '<label>Edit media</label>';
	content += '<input type="text" value="' + title + '" id="edit-media-title" placeholder="Title">';
	content += '<input type="text" value="' + author + '" id="edit-media-author" placeholder="Author">';
	content += '<input type="button" value="Submit" id="submit-edit">';
	content += '</form></div>';
	
	$.fancybox({ 
		content: content,
		openEffect	: 'none',
		closeEffect	: 'none',
		padding: 0,
		margin: 0,
		minWidth: 420,
		minHeight: 230
	});
});

//Editing media (form)

$(document).on('click', '#submit-edit', function(){
	var id = $('#edit-box').attr('data-id');
	var title = encodeURIComponent($('#edit-media-title').val());
	var author = encodeURIComponent($('#edit-media-author').val());
	
	var params = '';
	
	if(title != '' && author != ''){
		params = '&title=' + title + '&author=' + author;
	} else if (title != '') {
		params = '&title=' + title;
	} else if (author != '') {
		params = '&author=' + author;
	} 
		
	var url = CFG.absolutePath + 'php/';
	$.ajax({
		url : url + "updateMedia.php?id=" + id + params,
		method : "GET",
		dataType : "html",
		success : function(data) {
			console.log(data);
			if(data == 'success'){
				$.fancybox.close();
				$('#message').html('Media updated successfully!');
				$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
				solr.index();
				var scope = angular.element(wrapper).scope();
				scope.init();
			} else {
				$.fancybox.close();
				$('#message').html('Error updating media!');
				$('#message').fadeIn('slow').delay(1200).fadeOut('slow');
			}			
		},
		error : function(err) {
			console.log("Error: " + err);
		}
	});
});

//Similar images submit

$(document).on('change', '#query-similar-file', function(){
	$('#upload-form').submit();
});

//INFO search tooltip

$(document).on('mouseenter', '#info-image', function(event) {
	$('#info-tooltip').fadeIn('fast');
}).on('mouseleave', '#info-image', function() {
	$('#info-tooltip').fadeOut('fast');
});