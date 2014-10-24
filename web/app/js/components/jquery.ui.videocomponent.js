/*
 * Depends:
 *   jquery.ui.core.js
 *   jquery.ui.widget.js
 *   jquery.ui.effects.js
 *	 jquery.ui.mouse.js
 *   jquery.ui.draggable.js
 *   jquery.ui.droppable.js
 *	 jquery.editinplace.js
 */


(function( $ ) {
	
$.widget( "ui.videocomponent", {
   
	options: {
		sourceId : "3",
		width : 640,
		height : 480,
		themeColor: "0xdddddd",
		textColor: "0x001100",
		highlightBg: "0xaa0000",
		highlightText: "0xffffff",
		serverOwner : "hku",
		showData: true,
		allowEdit: true,
		sessionId: null,
		searchResultPerPage: 5,
		duration: "5000",
		type: "annotations",
		dataPosition: "overlay",
		similaritySearch: true,
		startPoint: 0
	},
	
	dataId : null,
	loadedData: null,
	currentTimePoint: 0,
	currentPeriod: null,

	_create: function() {
		var self = this; 
		var opts = self.options;
		var el = self.element;
		el
			.addClass( "eutv-videocomponent" )
			.attr({
				role: "videocomponent"
			})
			.empty()
			.css({
				'width': opts.width + "px", 
				'height': opts.height + "px" 
		});
						 
		self.currentPeriod = 0 - (self.options.duration/1000);
		self._initializePreloader();
		self.getVideoInfo();
		return false;
	},
	
	
	_destroy: function() {
		this.element
			.removeClass( "eutv-videocomponent" )
			.removeAttr( "role" );
			
		return false;
	},


	_setOption: function( key, value ) {
		var self = this;
		var opts = self.options;
		
		if ( key === "sourceId" ) {
			opts.sourceId = value;
		}
		
		if ( key === "userId" ) {
			opts.sessionId = value;
		}
		
		if ( key === "duration" ) {
			opts.duration = value;
		}
		
		if ( key === "videoURL" ) {
			opts.videoURL = value;
		}
		
		if ( key === "width" ) {
			opts.width = value;
		}
		
		if ( key === "height" ) {
			opts.height = value;
		}
		
		if ( key === "themeColor" ) {
			opts.themeColor = value;
		}
		
		if ( key === "textColor" ) {
			opts.textColor = value;
		}
		
		if ( key === "highlightBg" ) {
			opts.highlightBg = value;
		}
		
		if ( key === "highlightText" ) {
			opts.highlightText = value;
		}
		
		if ( key === "type" ) {
			opts.type = value;
		}
		
		if ( key === "showData" ) {
			opts.showData = value;
		}
		if ( key === "allowEdit" ) {
			opts.allowEdit = value;
		}
		if ( key === "searchResultPerPage" ) {
			opts.searchResultPerPage = value;
		}
		
		if ( key === "dataPosition" ) {
			opts.dataPosition = value;
		}
		
		if ( key === "similaritySearch" ) {
			opts.similaritySearch = value;
		}
		if ( key === "startPoint" ) {
			opts.startPoint = value;
		}		
		
		self._super( "_setOption", key, value );
		
	},

	_refreshValue: function() {
		return false;
	},
	
	_initializePreloader: function() {
		var self = this;
		var opts = self.options;
		var preloader = jQuery('<img></img>').attr('class','video-preloader');
		
		preloader.attr('src', '../img/ajax-loader-white.gif');
		
		preloader.css({
			'borderColor': (opts.themeColor).replace(/0x/, "#"),
			'top': '135px', //(opts.height / 2 - 16) + "px",
			'left': '325px', //(opts.width / 2 - 16) + "px"
			'width': '50px',
			'height': '50px',
			'position': 'absolute'
		})
			.addClass('ui-corner-all')
			.ajaxStart(function() {
				$(this).css("display", "");
			})
			.ajaxStop(function() {
				$(this).css("display", "none");
			});
		
		self.element.append(preloader);	
		
		return false;
	},
	
	_loadPlayer: function(filepath, fileName) {
		var self = this;
		var element = self.element;
		var opts = self.options;
//		var context = element.context;
		var type = opts.type;
		
		var level = 0;
		var sd = opts.showData;
		var ad = opts.allowEdit;
		if(sd && ad) level = 2;
		if(sd && !ad) level = 1;	
		console.log('level ' + level);
		//TODO Loading video info
		
		//Video player
		var videoContainer = "<div id='video-container'></div>";
		element.append(videoContainer);
		var videoElement = "<video id='video-player'></video>";
		$('#video-container').append(videoElement);
		var videoSource = "<source src='" + filepath + "' type='video/mp4'>";
		$('#video-player').append(videoSource);
		
		//Video controls (custom)
		var videoControls = "<div id='video-controls'></div>";
		$('#video-container').append(videoControls);
		var videoBar = "<div id='video-top-controls'>";
		videoBar += "<input type='range' id='video-seek-bar' value='0'></div>";
		$('#video-controls').append(videoBar);
		
		var buttons = "<div id='video-bottom-controls'>";
		buttons += "<img src='../img/controls/play.png' id='play-pause-button' alt='play-pause'>";
		buttons += "<img src='../img/controls/volume.png' id='volume-icon' alt='volume icon'>";
		buttons += "<input type='range' id='volume-bar' min='0' max='1' step='0.1' value='1'>";
		buttons += "<span id='video-timer'>00:00 / 00:00</span>";
		buttons += "<img src='../img/controls/annotations.png' title='Toggle annotations' id='annot-icon' alt='annotations icon'>";
		
		if(level == 2){
			buttons += "<input type='text' id='tag-input' placeholder='Add new tag'>";
			buttons += "<img src='../img/controls/plus.png' id='add-icon' alt='add icon'>";
		}
		
		buttons += "<img src='../img/controls/fullscreen.png' id='fullscreen-icon' alt='fullscreen icon'>";
		buttons += "<img src='../img/controls/search.png' id='search-icon' alt='search icon'></div>";
		
		$('#video-controls').append(buttons);
		
		$('#tag-input').keypress(function(e){
			if(e.which == 13){
				var video = document.getElementById('video-player');
				var text = $('#tag-input').val();
				if (text != '') {
					self.addAnnotation(text, Math.floor(video.currentTime * 1000));
					$('#tag-input').val('');
				} else {
					console.log('No tag!');
				}
			}	 
		});
		
		if (level !== 0) {
			
			var innerContainer = jQuery('<div></div>').attr('class','innerContainer video-inner');
			innerContainer.css({
				'width': $('#video-player').width() - 20 + "px", 
				'height': $('#video-player').height() - 60 + "px", 
				'color': (opts.textColor).replace(/0x/, "#")
			})
				.click( function(){
					self.hideAllOverlay();
			});		
			
			
			var resultsContainer = jQuery('<div></div>').attr('class','resultsContainer');
			resultsContainer.css({
				'width': $('#video-player').width() - 20 + "px", 
				'height': $('#video-player').height() - 60 + "px", 
				'color': (opts.textColor).replace(/0x/, "#"),
				'opacity': 0.75,
				'display': 'none'
			})
			.addClass('ui-corner-all');
			
			var color = (opts.textColor).replace(/0x/, "#");
			
			element
				.append(innerContainer)
				.append(resultsContainer);
				
			var searchContainer = $('<div></div>')
				.attr({'class':'searchContainer'})
				.css({
					'color': color,
					'border-color': color,
					'background-color':(opts.themeColor).replace(/0x/, "#"),
					'display': 'none'
				})
				.addClass('ui-corner-all')
				.append("<label for='searchKeyword'>search: </label><input class='searchKeyword' name='searchKeyword'>");
		
		
		if(type == "annotations") {
		 
			$(".searchKeyword", searchContainer)
				.keypress(function(e){
					var val = this.value;
			  		if(e.which == 13){
			   			self.searchData(
							val,
							0,
							opts.searchResultPerPage
						);
						$('.suggestions-list-search', element.context).css('display', 'none');	
			   		} else {
				   		if (val.length > 0) {
				   			self.suggestAnnotationSearch(val);
				   		}
					}
			 });
		} else {
			$(".searchKeyword", searchContainer)
				.keypress(function(e){
					if(e.which == 13){
						$('.suggestions-list-search', element.context).css('display', 'none');	
						self.searchData(this.value,0,opts.searchResultPerPage);
					} 
			  });
			}
			element.append(searchContainer);	
		}
		
		if (level == 2 && type == "annotations") {
			
			var suggestionsContainer = jQuery('<div></div>').attr('class','suggestions-list');
			var textColor = (opts.textColor).replace(/0x/, "#");
			
			suggestionsContainer
				.css({
					'color': textColor,
					'border-color': textColor,
					'background-color':(opts.themeColor).replace(/0x/, "#")
				})
				.addClass('ui-corner-all');	
			var suggestionList = jQuery('<ul></ul>');
			suggestionsContainer.append(suggestionList);
			element.append(suggestionsContainer);
		}
		
		if (level >= 1 && type == "annotations") {
			var searchSuggestionsContainer = jQuery('<div></div>').attr('class','suggestions-list-search video-search');
			var textColor = (opts.textColor).replace(/0x/, "#");
			
			searchSuggestionsContainer
				.css({
					'color': textColor,
					'border-color': textColor,
					'background-color':(opts.themeColor).replace(/0x/, "#")
				})
				.addClass('ui-corner-all');					
			
			var searchSuggestionList = jQuery('<ul></ul>');
			searchSuggestionsContainer.append(searchSuggestionList);
			element.append(searchSuggestionsContainer);
			
		}

		
		if (opts.type == "annotations" && opts.similaritySearch == true) {
			var similarityContainer = jQuery('<div></div>').attr('class','similarity-container');
			var innerContainer = jQuery('<div></div>').attr('class','inner-container');
			var imagesContainer = jQuery('<div></div>').attr('class','images-container');
			var innerImagesContainer = jQuery('<div></div>').attr('class','inner-images-container');
			var similarityButton = jQuery('<div></div>').attr('class','similarity-button');
			var preloader = jQuery('<div></div>').attr('class','images-preloader');
			console.log('here');
			similarityButton
					.text("Images")
					.css({
						'color': (opts.textColor).replace(/0x/, "#"),
						'border-color': (opts.textColor).replace(/0x/, "#"),
						'background-color':(opts.themeColor).replace(/0x/, "#"),
						'right': -1,
						'top': -4, 
						'width': 50, 
						'height': 18
					})
					.toggle(
						function() {
							if(opts.dataPosition == "top") {
								similarityContainer.animate({'top': parseInt(similarityContainer.css('top')) - similarityContainer.height() + 9}, 250, 'linear', function() {
								//jQuery('.images-preloader').show()
								});
								
							} else {
								similarityContainer.animate({'top': -(similarityContainer.height())}, 250, 'linear', function() {
								//jQuery('.images-preloader').hide();
								});
							}
						}, 
						function() {
							if(opts.dataPosition == "top") {
								
								similarityContainer.animate({'top': parseInt(similarityContainer.css('top')) + similarityContainer.height() - 9}, 250, 'linear', function() {
								//jQuery('.images-preloader').fadeIn()
									});
							} else {
							similarityContainer.animate({'top': 10}, 250, 'linear', function() {
								//jQuery('.images-preloader').hide();
  							});
							}
					});

			
			
			imagesContainer.append(preloader);
			
			
//			similarityContainer
//				.css({
//					'width': parseInt((opts.width).replace(/0x/, "#")) - 1,	
//				});
			
				
			imagesContainer.css({
					'width': similarityContainer.innerWidth()
			});	
			
			innerImagesContainer.css({
					'width': similarityContainer.innerWidth()					
			});
			
//			topborder.css({
//					'background-color':(opts.textColor).replace(/0x/, "#"),
//					
//			});
			
			imagesContainer.append(innerImagesContainer);
			innerContainer.append(similarityButton)
						  .append(imagesContainer);
			similarityContainer.append(innerContainer);	
			console.log(similarityContainer);
			element.append(similarityContainer);
		}
		self.loadVideoMetadata();
	},
	
	// Load video metadata
	loadVideoMetadata : function () {
		console.log('adding metadata');
		var self = this;
		var video = document.getElementById('video-player');
		var videoDurationTime = '';
		// Getting video duration
		$(video).on('loadedmetadata', function() {
			var minutes = Math.floor(video.duration / 60);
			var seconds = Math.floor(video.duration - minutes * 60);
			minutes = '' + minutes;
			seconds = '' + seconds;
			minutes = minutes.replace(/^(\d)$/, '0$1');
			seconds = seconds.replace(/^(\d)$/, '0$1');
			videoDurationTime = minutes + ':' + seconds;
//			$('.video-preloader').hide();
			self.loadSimilarImages(Math.floor(video.currentTime * 1000));
			self.gotoFrame(self.options.startPoint);
			$('.video-preloader').hide();
			self.playVideo(0);
		});
		// Video updating
		$(video).on('timeupdate', function() {
			var minutes = Math.floor(video.currentTime / 60);
			var seconds = Math.floor(video.currentTime - minutes * 60);
			minutes = '' + minutes;
			seconds = '' + seconds;
			minutes = minutes.replace(/^(\d)$/, '0$1');
			seconds = seconds.replace(/^(\d)$/, '0$1');
			var videoCurrentTime = minutes + ':' + seconds;
			$('#video-timer').text(videoCurrentTime + " / " + videoDurationTime);
			var percent = (100 / video.duration) * video.currentTime;
			$('#video-seek-bar').val(Math.floor(percent));
			if (Math.floor(video.currentTime) == (self.currentPeriod + (self.options.duration / 1000))) {
				self.loadData(Math.floor(video.currentTime * 1000));
				self.currentPeriod += (self.options.duration/1000);
			}
			self.paintInfoFromActionScript(Math.floor(video.currentTime * 1000));
		});
		
		$('#play-pause-button').click(function() {
			var video = document.getElementById('video-player');
			if (video.paused) {
				self.playVideo();
			} else {
				self.pauseVideo();
				$(this).attr('src', '../img/controls/play.png');
			}
		});
		
		// Mute button
		$('#volume-icon').click(function() {
			var video = document.getElementById('video-player');
			if (video.muted) {
				video.muted = false;
				$(this).attr('src', '../img/controls/volume.png');
			} else {
				video.muted = true;
				$(this).attr('src', '../img/controls/mute.png');
			}
		});

		// Volume control
		$('#volume-bar').change(function() {
			var video = document.getElementById('video-player');
			video.volume = $('#volume-bar').val();
		});

		// Slide video
		$('#video-seek-bar').click(function() {
			var video = document.getElementById('video-player');
			var time = ((video.duration * $(this).val()) / 100) * 1000;
			self.gotoFrame(time);
		});

		// Volume control
		$('#video-seek-bar').change(function() {
			var video = document.getElementById('video-player');
			var time = (video.duration * $(this).val()) / 100;
			video.currentTime = time;
		});

		// Add annotation
		$('#add-icon').click(function() {
					var video = document.getElementById('video-player');
					var text = $('#tag-input').val();
					if (text != '') {
						self.addAnnotation(text, Math.floor(video.currentTime * 1000));
					} else {
						console.log('No tag!');
					}
		});

		//Slide video
		$('#annot-icon').click(function() {
			self.toggleAnnotations();
		});
		
		// Search
		$('#search-icon').click(function() {
			self.toggleSearchBox();
		});

		//Fullscreen button
		$('#fullscreen-icon').click(function() {
			var video = document.getElementById('video-player');
			if (video.requestFullscreen) {
				video.requestFullscreen();
			} else if (video.mozRequestFullScreen) {
			    video.mozRequestFullScreen(); // Firefox
			} else if (video.webkitRequestFullscreen) {
			    video.webkitRequestFullscreen(); // Chrome and Safari
			}
		});
	},
	
	setDataPosition : function () {
		var self = this;
		var opts = self.options;
		var context = self.element.context;
		var container = $('.innerContainer', context);
		var resultsContainer = $('.resultsContainer', context);
		var searchContainer = $('.searchContainer', context);
		var suggestionList = $('.suggestions-list', context);
		var suggestionListSearch = $('.suggestions-list-search', context);
		var similarityContainer = $('.similarity-container', context);
		var similarityButton = $('.similarity-button', context);
		var innerContainer = $('.inner-container', context);
		var imagesContainer = $('.images-container', context);
		var innerImagesContainer = $('.inner-images-container', context);
		var imagesPreloader = $('.images-preloader', context);
		var contId = context.id;
		var flashObj = $("#EuTVVideoPlayer-" + contId);
		
		similarityContainer.css({'height': (parseInt(opts.height / 2))});
		innerContainer.css({'height': (parseInt(opts.height / 2))});
		imagesContainer.css({'height': ((parseInt(opts.height / 2) - 15) + 5)});
		innerImagesContainer.css({'height': ((parseInt(opts.height / 2) - 22))});
		
		switch(opts.dataPosition) {
			case "right" :
				container.css({'margin-left': (parseInt(opts.width) + 20), 'background-color': (opts.themeColor).replace(/0x/, "#")});
				resultsContainer.css({'margin-left': (parseInt(opts.width) + 20)});
			    similarityContainer.css({'top': '-15px'});
				imagesContainer.css({'top': 15});
				similarityButton.addClass('ui-corner-tl').addClass('ui-corner-tr');
				imagesPreloader.css({
							'top': (imagesContainer.height() / 2) - (imagesPreloader.height() / 2),
							'left': (imagesContainer.width() / 2) - (imagesPreloader.width() / 2)
				});
			break;
			case "bottom" :
				container.css({'margin-top': (parseInt(opts.height) + 40), 'background-color': (opts.themeColor).replace(/0x/, "#")});
				resultsContainer.css({'margin-top': (parseInt(opts.height) + 40)});
				similarityContainer.css({'top': '-15px'});
				imagesContainer.css({'top': 15});
				similarityButton.addClass('ui-corner-tl').addClass('ui-corner-tr');
				imagesPreloader.css({
							'top': (imagesContainer.height() / 2) - (imagesPreloader.height() / 2),
							'left': (imagesContainer.width() / 2) - (imagesPreloader.width() / 2)
				});
			break;
			case "top" :
				flashObj.css({'top': (parseInt(opts.height) + 20)});
				container.css({'background-color': (opts.themeColor).replace(/0x/, "#")});
				searchContainer.css({'bottom': - (parseInt(opts.height)) + 16});
				suggestionListSearch.css({'bottom': - (parseInt(opts.height)) + 18 + searchContainer.outerHeight(), 'right': 25});
				similarityContainer.css({'top': parseInt(parseInt(flashObj.css('top')) + parseInt(opts.height) + 25)});
				similarityButton.css({'top': '', 'bottom': -5, 'border-top': 'none', 'border-bottom': '1px solid'});
				similarityButton.addClass('ui-corner-bl').addClass('ui-corner-br');
				imagesContainer.css({'top': 0});
				imagesPreloader.css({
							'top': (imagesContainer.height() / 2) - (imagesPreloader.height() / 2),
							'left': (imagesContainer.width() / 2) - (imagesPreloader.width() / 2)
				});
			break;
			case "left" :
				flashObj.css({'left': (parseInt(opts.width) + 20)});
				container.css({'background-color': (opts.themeColor).replace(/0x/, "#")});
				searchContainer.css({'left': (parseInt(opts.width) * 2 + 20)  - parseInt(searchContainer.outerWidth() + 5)});
				suggestionListSearch.css({'left': ((parseInt(opts.width) * 2 + 20)) - (suggestionListSearch.outerWidth() + 10)});
				
				similarityContainer.css({'top': '-15px'});
				similarityContainer.css({'left': (parseInt(opts.width) + 20)});
				imagesContainer.css({'top': 15});
				similarityButton.addClass('ui-corner-tl').addClass('ui-corner-tr');
				imagesPreloader.css({
							'top': (imagesContainer.height() / 2) - (imagesPreloader.height() / 2),
							'left': (imagesContainer.width() / 2) - (imagesPreloader.width() / 2)
				});
			break;
			case "overlay" :
				
				
				similarityContainer.css({'top': '10px',
										'display': 'block !important',
										'left': '710px',
										'width': '170px'});
				
				imagesContainer.css({'top': 15});
				similarityButton.addClass('ui-corner-tl').addClass('ui-corner-tr');
				imagesPreloader.css({
							'top': (imagesContainer.height() / 2) - (imagesPreloader.height() / 2),
							'left': (imagesContainer.width() / 2) - (imagesPreloader.width() / 2)
				});
				
				similarityButton.trigger('click');
			break;
		}
	},
	
	//Getting video info by soap call
	getVideoInfo: function() {
		var self = this;
		var opts = self.options;
		var sessionId = opts.sessionId;
		var sourceId = opts.sourceId;
		var serverOwner = opts.serverOwner;
		im3iSoapCalls.getSourceId(
				sessionId, 
				sourceId, 
				serverOwner, 
				function(data){
					self.parseVideoInfo(data);}, 
				function(data){
					self.errorVideoInfo(data);});
	},
	
	parseVideoInfo: function(data) {
		console.log(data);
    	var filepath  = $(data).find('filepath').text();		
		var fileName = $(data).find('mediauri').text();
		//Load player
		this._loadPlayer(filepath, fileName);
		this.setDataPosition();
	},
	
	errorVideoInfo: function(data) {
	},
	
	loadNewData : function(data){
		var self = this;
		var el = self.element;
		var opts = this.options;
		
		if(opts.showData){
			var self = this;
			var remoteUrl = $(data).find("remoteURL").text();
			var url = $(data).find("url").text();
			
			if(remoteUrl == ""){
				consoleLog("WARNING no remote url found");
			} else {
				url = remoteUrl;
			}
			
			var content = "";
			var contentData = $(data);
			content+='<h2>Repository data for source id '+opts.sourceId+': </h2>';
			content+='<p><ul>';
			content+='<li>fileName: '+ contentData.find("fileName").text() +'</li>';
			content+='<li>remoteUrl: '+ contentData.find("remoteURL").text() +'</li>';
			content+='<li>sourceType: '+ contentData.find("sourceType").text() +'</li>';
			content+='</ul></p>';
			content+='<h2>Raw xml data: </h2>';
			el.html(content);
			$('<p></p>').text(data).appendTo(el);
		}
	},
	
	paintInfoFromActionScript : function(playInstant) {
		var self = this;
		var opts = self.options;
		var loadedData = self.loadedData;
		
		if(opts.showData){	
			
			var hb = (opts.highlightBg).replace(/0x/, "#");
			var ht = (opts.highlightText).replace(/0x/, "#");
			var tc = (opts.themeColor).replace(/0x/, "#");
			var txc = (opts.textColor).replace(/0x/, "#");
			
			if (loadedData !== null) {
				self.highlightData(hb, ht, tc, txc, loadedData, playInstant, opts.type);
			}
		}
	},
	
	highlightData : function(hb, ht, tc, txc, loadedData, playInstant, type) {
		var elCont = this.element.context;
		var opts = this.options;
		
		$(".innerContainer div", elCont).fadeTo(0, 0.75);
		
		var key;
		if ( type == "annotations"){
			key = opts.serverOwner == 'unifi' ? "annotations" : "annotation";
		}else{
			key = opts.serverOwner == 'unifi' ? "transcriptions" : "transcription";
		}
		var elArr = $(loadedData).find(key);
		
		var itemObj;
		var itemId;
		var itemStart;
		var duration;
		var instan;
		var itemIdObj;
			
		for (var i=0; i < elArr.length; i++){
			itemObj = $(elArr[i]);
			itemId = itemObj.find('id').text();
			itemStart = parseInt(itemObj.find('start').text());
			duration = parseInt(itemObj.find('duration').text());
			instant = parseInt(playInstant);
			itemIdObj = $("#" + itemId + "-" + itemStart, elCont);
			if ((itemStart < instant) && (itemStart + duration) > instant) {
				itemIdObj
					.fadeTo(0, 1)
					.css({'backgroundColor': hb})
					.find('span')
					.css({'color': ht});
			} else {
				itemIdObj
					.fadeTo(0, 0.75)
					.css({'backgroundColor': tc})
					.find('span')
					.css({'color': txc});
			}
		
		}		
	},
	
	addAnnotation: function( text, timePoint)  {
		console.log(text, timePoint);
		var self = this;
		var opts = self.options;
		$('#tag-input').val('');
		var agent = "Annotation component";
		var comment = "Manual annotation from annotation component";
		var sessionId = opts.sessionId;
		var confidence = "1.0";
		var soapServer = opts.serverOwner;
		var type = "manual";
		var keyword = text;
		
		self.currentTimePoint = timePoint;
		
		var videoAnnotation = {'sourceId': opts.sourceId, 'keyword': text, 'timePoint': timePoint, 'duration': 1000 };
		var similarAnnotations;
		
		var similarImages = jQuery('div.inner-images-container div.selected', self.element.context);
		
		var loader = '<img class="annot-video-loader" src=' + CFG.absolutePath + 'img/ajax-loader.gif>';
		$('.innerContainer').append(loader);
		
		var annotations = [];
		annotations.push(videoAnnotation);
		
		if (similarImages && similarImages.length) {
			for (var i = 0; i < similarImages.length; i++) {
				var annItem = similarImages[i];
				var id = jQuery(annItem).attr('id');
				var dataArray = id.split('-');
				var startPoint = dataArray[0];
				var sourceId = dataArray[1];
				var annotation = {'sourceId': sourceId, 'keyword': keyword, 'timePoint': startPoint, 'duration': 1000 };
				annotations.push(annotation);
			}
		}
		
		var endPoint = timePoint + 1000;
		
		var params = 'id=' + opts.sourceId + '&mode=free&starttime=' + timePoint + '&endtime=' + endPoint + '&owner=1&conceptname=' + text;
		
		console.log(params);
		
		$.ajax({
			url : '../php/SetConcept.php?' + params,
			dataType : "html",
			success : function(data) {
			     if( data != "ERROR" ) {
			    	 solr.index();
			    	 $('.annot-video-loader').hide();
			    	 self.addedAnnotation(data);
			     } else {
			    	 self.errorAddingAnnotation(data);   	 
			     }
			},
		});	
		
//		im3iSoapCalls.addAnnotationVideoAudio(
//			agent, 
//			comment, 
//			sessionId, 
//			confidence, 
//			soapServer, 
//			type, 
//			annotations,
//			function(data){
//				self.addedAnnotation(data);
//			}, 
//			function(data){
//				self.errorAddingAnnotation(data);
//			}
//		);
	},
	
	positionSuggest: function(position, suggestWidth) {
		var self = this;
		var context = self.element.context;
		var opts = self.options;
	    var suggestionList = $('.suggestions-list', context);
		
		if (opts.dataPosition == 'left') {
			suggestionList.css({'left': position, 'width': suggestWidth});
			
		} else if (opts.dataPosition == 'top') {
			suggestionList.css({'bottom': -(parseInt(opts.height)), 'width': suggestWidth, 'left': position });
		} else {
			suggestionList.css({'left': (parseInt(opts.width) + 20) + position, 'width': suggestWidth });
		}
	},
	
	positionSuggest: function(position, suggestWidth) {
		var self = this;
		var context = self.element.context;
		var opts = self.options;
	    var suggestionList = $('.suggestions-list', context);
		
		if (opts.dataPosition == 'left') {
			suggestionList.css({'left': (parseInt(opts.width) + 20) + position, 'width': suggestWidth });
		} else if (opts.dataPosition == 'top') {
			suggestionList.css({'bottom': -(parseInt(opts.height)), 'width': suggestWidth, 'left': position });
		} else {
			suggestionList.css({'left': position, 'width': suggestWidth});
		}
	},
	
	suggestAnnotation: function(text)  {
		var self = this;
		var opts = self.options;
		
		var sessionId = opts.sessionId;
		var source = opts.sourceId;
		var soapServer = opts.serverOwner;
		
		im3iSoapCalls.suggestAnnotation(
			sessionId, 
			source, 
			text,
			soapServer,
			function(data){
				self.suggestedAnnotation(data);
			}, 
			function(data){
				self.errorSuggestingAnnotation(data);
		});
	},
	
	suggestAnnotationSearch: function(text)  {
		var self = this;
		var opts = self.options;
		
		var sessionId = opts.sessionId;
		var source = opts.sourceId;
		var soapServer = opts.serverOwner;
		
		im3iSoapCalls.suggestAnnotation(
			sessionId, 
			source, 
			text,
			soapServer,
			function(data){
				self.suggestedAnnotationSearch(data);
			}, 
			function(data){
				self.errorSuggestingAnnotation(data);
		});
	},
	
	
	
	suggestedAnnotation : function(data) {
   
    	var self = this;
    	var opts = self.options;
		var element = self.element;
		
		//--- da spostare nel caso in cui suggest è su add
		
		var container = jQuery(".suggestions-list ul", element.context);
		container.empty();
		
		if ($('.searchContainer', element.context).is(":visible")) {
			self.toggleSearchBox();
		}

		var servOwner = opts.serverOwner;
		
		var dataKey = (servOwner == "unifi")? 'keywords':'annotation';
		
		var suggArray = $(data).find(dataKey);
		if (suggArray.length > 0 ) {
			container.parent().show();
			for (var i=0; i< suggArray.length; i++){
				self.addSuggestionItem(suggArray[i], container);
			}
			
		} else {
			container.parent().hide();
		}
	},
	
	suggestedAnnotationSearch : function(data) {
   
    	var self = this;
    	var opts = self.options;
		var element = self.element;
		
		//--- da spostare nel caso in cui suggest è su add
		
		var container = jQuery(".suggestions-list-search ul", element.context);
		container.empty();
		
		var servOwner = opts.serverOwner;
		
		var dataKey = (servOwner == "unifi")? 'keywords':'annotation';

			
		var suggArray = $(data).find(dataKey);
		
		if(suggArray.length > 0 ) {
			container.parent().show();
			for(var i =0; i< suggArray.length; i++){
				self.addSuggestionItemSearch(suggArray[i], container);
			}
			
		} else {
			container.parent().hide();
		}
		
	},
	
	addSuggestionItem : function (el, container){
		var self = this;
		var element = self.element;
		var opts = self.options;
		
		var suggestionText = $(el).text();
		var suggestionItem = $('<li></li>');
		suggestionItem.append(suggestionText);
		container.append(suggestionItem);
		suggestionItem
			.click(function(){
				var text = $(this).text();
				self.thisMovie("EuTVVideoPlayer-" + element.context.id).setAnnotationText(text);
				container.parent().hide();
			})
			.hover(
				   function() {
					  $(this).addClass('hoverSuggest');
				   },
				   function() {
					   $(this).removeClass('hoverSuggest');
				   }
			);	
	},
	
	addSuggestionItemSearch : function (el, container){
		var self = this;
		var element = self.element;
		var opts = self.options;
		
		var suggestionText = $(el).text();
		var suggestionItem = $('<li></li>');
		suggestionItem.append(suggestionText);
		container.append(suggestionItem);
		suggestionItem
			.click(function(){
				var text = $(this).text();
				container.parent().hide();
				$('.searchKeyword', element.context).val(text);
			})
			.hover(
				   function() {
					  $(this).addClass('hoverSuggest');
				   },
				   function() {
					   $(this).removeClass('hoverSuggest');
				   }
			);
		
			
	},

	errorSuggestingAnnotation : function(data) {
    	var self = this;
   	 	var id = $(data).find('id').text();
    	var message = $(data).find('message').text();
    	var feedback = jQuery('<div></div>').attr('class','feedback');
    	self.element.append(feedback);
    	feedback.css({
        	'borderColor': (this.options.themeColor).replace(/0x/, "#"),
        	'top': (this.options.height / 2 - 16) + "px",
        	'left': (this.options.width / 2 - 16) + "px"
    	})
    		.addClass('ui-corner-all');
    	
		feedback.text(message);
    	feedback.fadeOut("slow", function() {
      		$(this).remove();
    	});
	},
	
	
	searchData: function( keyword, page, recPerPage) {
		var self = this;
    	var opts = self.options;
		var interval = "1000";
		var sessionId = opts.sessionId;
		var source = opts.sourceId;
		var soapServer = opts.serverOwner;

		switch(opts.type) {
				case "transcriptions":
					im3iSoapCalls.searchTranscriptions(
						source, 
						sessionId, 
						keyword, 
						page, 
						recPerPage, 
						interval,
						soapServer,  
						function(data){
							self.searchDataResults(data);
						}, 
						function(data){
							self.searchDataError(data);
						
						}
					);
				break;
			     default: 
				 	im3iSoapCalls.searchAnnotations(
						source, 
						sessionId, 
						keyword, 
						page, 
						recPerPage, 
						interval,
						soapServer,  
						function(data){
								self.searchDataResults(data);
						}, 
						function(data){
							self.searchDataError(data);
						}
					);
			}

	},
	
	searchDataResults: function(data){
		var self = this;
		var element = self.element;
		var opts = self.options;
		var context = element.context;
		var resultsContainer = $(".resultsContainer", context);
		
		$(".innerContainer",context).hide();
		
		// if is present, remove the search box for input
		var searchContainer = jQuery(".searchContainer", context);
		if(searchContainer.is(":visible") ){	
			self.toggleSearchBox();
		}
		
		resultsContainer.empty();
		resultsContainer.show();
		
		var closeBtn = $('<span class="delete-icon"></span>');
		//closeBtn.css({'position':'relative', 'top':'0px', 'right':'0px', 'color':'#ff0000'});
		closeBtn.css({'background-color': (opts.themeColor).replace(/0x/, '#'), 'opacity':0.8})
			.addClass('ui-corner-all')
			.hover(
						function () {
							$(this).css({'opacity':1 });
						}, 
						function () {
							$(this).css({'opacity':0.8 });
						}
					)
			.click(function(event){
				resultsContainer.hide();
				$(".innerContainer", context).show();
			});
			
		resultsContainer.append(closeBtn);	
		var title = $("<span class='searchTerm'></span>").css({'color': (opts.themeColor).replace(/0x/, "#")});
		resultsContainer.append(title);

		if($(data).find('keyword').length > 0){
			var term = opts.serverOwner == 'unifi'? $(data).find('searchTerm').text() :  $(data).find('context').find('keyword').text();
			
			$(".searchTerm",resultsContainer).html("Results for '"+term+"'");
			
			var resultList = $('<div class="paginateResults"><ul class="resList"></ul></div>')
			resultsContainer.append(resultList);
			
			if(opts.type=="annotations"){
				var key = opts.serverOwner == 'unifi' ? 'annotations' : 'annotation';
				var annArray = $(data).find(key);
				for (var i=0; i< annArray.length; i++){
					self.createSearchAnnotationItem(annArray[i]);
				}
				
			} else{
				var key = opts.serverOwner == 'unifi' ? 'transcriptions' : 'transcription';
				var traArray = $(data).find(key);
				for (var i=0; i< traArray.length; i++){
					self.createSearchTranscriptionItem(traArray[i]);
				}
			}
			
			var nav= $("<div class='resultNav'></div>");
			var dataObject = $(data);
			var pag = opts.serverOwner == 'unifi'? parseInt(dataObject.find('page_loaded').text()) : parseInt(dataObject.find('page').text());
			var totalPag = opts.serverOwner == 'unifi'? parseInt(dataObject.find('page_number').text()) : parseInt(dataObject.find('totalPages').text());
			
			if (pag > 0 ){
				var prev = $('<span>Prev</span>').css({'color': (opts.themeColor).replace(/0x/, "#"), 'float':'left', 'cursor':'pointer'});	
				prev.click( function(ev) {
					self.searchData(term, pag-1, opts.searchResultPerPage);
				});
				resultsContainer.append(prev);
			}
			if (pag < (totalPag -1) ){
				var next = $('<span>Next</span>').css({'color': (opts.themeColor).replace(/0x/, "#"), 'float':'right','cursor':'pointer'});	
				next.click( function(ev) {
					self.searchData(term, pag+1, opts.searchResultPerPage);
				});
				resultsContainer.append(next);
			}
		
		}else{
			
			$(".searchTerm",resultsContainer).html("No results found");
		
		}
	},

	searchDataError: function(data){
		console.log(data);
	},
	
	loadData : function(timePoint) {
		var self = this;
		var opts = self.options;
		if(opts.showData){
			
			$('div.innerContainer', self.element).empty();
			
			switch(opts.type) {
				case "transcriptions":
					self.getTranscriptions(timePoint);
					break;
			    default: 
				 	self.getAnnotations(timePoint);
				}
		}
	},
	
	getAnnotations: function(timePoint){
		var self = this;
		var opts = self.options;
		im3iSoapCalls.mpeg7GetAnnotation(
			opts.sourceId, 
			opts.sessionId, 
			timePoint, 
			opts.duration, 
			opts.serverOwner, 
			function(data){
				self.showAnnotation(data);
			}, 
			function(data){
				self.errorAnnotation(data);
			});
	},
	
	getTranscriptions: function(timePoint){
		var self = this;
		var opts = self.options;
		im3iSoapCalls.mpeg7GetTranscription(
			opts.sourceId, 
			opts.sessionId, 
			timePoint, 
			opts.duration, 
			opts.serverOwner, 
			function(data){
				self.showTranscription(data);
			}, 
			function(data){
				self.errorTranscription(data);
			});
	},
	
	showTranscription: function(data) {
		var self = this;
		var opts = self.options;
		var innCont = $('div.innerContainer', self.element.context);
		innCont.empty();
		
		/*var responseEl = $('div.serverResponse' );
		responseEl.empty();
		var wrappedData = $('<p></p>').text(utils.xmlToString(data));
		responseEl.append(wrappedData);*/
	
		if(data.length==0) {
			var feedback = $('<div></div>');
			innCont.append(feedback);
			return;
		}
		
		var key = opts.serverOwner == 'unifi' ? 'transcriptions' : 'transcription';
    	var traArray = $(data).find(key);
		
		for (var i=0; i < traArray.length; i++){
			var transRef = $(traArray[i]);
			var text = opts.serverOwner == 'unifi' ? transRef.find('keyword').text() : transRef.find('text').text();
			self.createConceptItem(
				text,
				transRef.find('id').text(), 
				transRef.find('start').text()
			);
		}
		
		self.loadedData = data;
		$("#" + self.dataId, self.element.context).fadeTo(0, 1).css({'fontWeight': 'bold'});
		self.dataId = null;
			
	},
	
	errorTranscription: function(data) {
		console.log("errorTranscription: " + data);
	},

	deleteAnnotation: function( id )  {
		var self = this;
		var opts = self.options;
		var sessionId = opts.sessionId;
		var soapServer = opts.serverOwner;
		
		im3iSoapCalls.deleteAnnotation(
							id, 
							sessionId, 
							soapServer, 
							function(data){ 
								self.deletedAnnotation(data);
							}, 
							function(data){
								self.errorDeletingAnnotation(data);
							}
		);
	},
	
	editTranscription: function(id, newText)  {
		var self = this;
		var opts = self.options;
		var sessionId = opts.sessionId;
		var source = opts.sourceId;
		var soapServer = opts.serverOwner;
		im3iSoapCalls.editTranscription(
				id, 
				newText, 
				sessionId,
				source,
				soapServer, 
				function(data){ 
					self.editedTranscription(data);
				}, 
				function(data){
					self.errorEditingTranscription(data);
		});
		
	},
	
	editedTranscription : function(data) {
		console.log("editedTranscription: " + data);
		
	},
	
	errorEditingTranscription : function(data) {
		console.log("errorEditingTranscription: " + data);
	},
	
	toggleSearchBox: function (){
		var self = this;
		var searchContainer = $('.searchContainer');
		searchContainer.css({
			'top': $('#video-container').height() - $('#video-controls').height() + 'px', 
			'left': $('#video-container').width() - $('.searchContainer').width() + 'px'
		});
		self.hideAllOverlay();

		if (searchContainer.is(":visible")) {	
			searchContainer.hide();
		} else {	
			self.pauseVideo();
			searchContainer.show();
		} 			
	},

	thisMovie : function(movieName) {
		if (navigator.appName.indexOf("Microsoft") != -1) {
			return window[movieName];
		} else {
			return document[movieName];
		}
	},

	gotoFrame : function(value) {
		var video = document.getElementById('video-player');
		if(video != null){
			var temp = Math.floor((value/1000)/(this.options.duration / 1000));
			this.currentPeriod = temp * (this.options.duration / 1000);
			video.currentTime = value/1000;
			this.loadData(Math.floor(video.currentTime * 1000));
			this.loadSimilarImages(Math.floor(video.currentTime * 1000));
		}
	},
	
	pauseVideo : function() {
		var video = document.getElementById('video-player');
		video.pause();
		$('#play-pause-button').attr('src', '../img/controls/play.png');
		this.loadSimilarImages(Math.floor(video.currentTime * 1000));
	},
	
	playVideo : function() {
		var video = document.getElementById('video-player');
		if(video != null){
			video.play();
			$('#play-pause-button').attr('src', '../img/controls/pause.png');
		}
	},
	

	errorAnnotation : function(data) {
		/*var responseEl = $('div.serverResponse');
		responseEl.empty();
		var wrappedData = $('<p></p>').text(JSON.stringify(data));
		responseEl.append(wrappedData);*/
	},

	showAnnotation : function(data) {
		var self = this;
		var opt = self.options;
		var innCont = $('div.innerContainer', self.element.context);
		innCont.empty();
		
		/*var responseEl = $('div.serverResponse');
		responseEl.empty();
		var wrappedData = $('<p></p>').text(utils.xmlToString(data));
		responseEl.append(wrappedData);*/
//		console.log(data);
		var annotations = $(data).find('annotations');
		
		if(data.length==0 || annotations.length == 0) {
			var feedback = $('<div></div>');
			innCont.append(feedback);
			return;
		}	
		
		var items = "";
		var key = opt.serverOwner == "unifi" ? 'annotations' : 'annotation';
		
		var annArray = $(data).find(key);
		
		for (var i=0; i<annArray.length; i++){
			var annRef = $(annArray[i]);
			self.createConceptItem(
				annRef.find('keyword').text(),
				annRef.find('id').text(), 
				annRef.find('start').text()
			);
		}
		
		$("#" + self.dataId, self.element.context).fadeTo(0, 1).css({'fontWeight': 'bold'});
		self.dataId = null;
		self.loadedData = data;
	},

	addedAnnotation : function(data) {
    	var self = this;
    	var op = self.options;
		var dataRef = $(data);
		if (op.serverOwner == "unifi") {
			
			var annotationItems = dataRef.find('annotations');
			console.log(annotationItems[0]);
			for (var i = 0; i < annotationItems.length; i++) {
				var sourceId =  jQuery(annotationItems[i]).find('sourceId').text(); 
				var timePoint =  jQuery(annotationItems[i]).find('start').text(); 
				
				if (sourceId == op.sourceId) {
					var annotationItemId = jQuery(annotationItems[i]).find('id').text(); 
					var annotationText = jQuery(annotationItems[i]).find('keyword').text(); 
				}
			}
			//var annotationItemId = dataRef.find('id').text();
			//var annotationText = dataRef.find('concept').text();
		} else {
			var annotationItemId = dataRef.find('id').text();  
			var annotationText = dataRef.find('keyword').text(); 
			
		}
		self.createConceptItem(annotationText,annotationItemId,0);
	},

	errorAddingAnnotation : function(data) {
    	var self = this;
		var opts = self.options;
		var dataRef = $(data);
   	 	var id = dataRef.find('id').text();
    	var message = dataRef.find('message').text();
    	var feedback = jQuery('<div></div>').attr('class','feedback');
    	self.element.append(feedback);
    	feedback.css({
        	'borderColor': (opts.themeColor).replace(/0x/, "#"),
        	'top': (opts.height / 2 - 16) + "px",
        	'left': (opts.width / 2 - 16) + "px"
    	})
    		.addClass('ui-corner-all');
    	
		feedback.text(message);
    	feedback.fadeOut("slow", function() {
      		$(this).remove();
    	});
	},

	deletedAnnotation : function(data) {},

	errorDeletingAnnotation : function(data) {},

	createConceptItem :function(itemText, itemId, itemStart) {		
		var self = this;
		var opts = self.options;
		
		if (itemText.length > 0) {
		
		var itemCont = $('<div></div>')
			.attr({'id': itemId + "-" + itemStart, 'class': 'overlayItem'})
			.css({
				 'color': (opts.textColor).replace(/0x/, '#'), 
				 'cursor': 'pointer', 
				 'background': (opts.themeColor).replace(/0x/, '#'), 
				 "marginBottom": "5px"})
			.addClass('ui-corner-all')
			.css({'display': "none"})
			.fadeTo(250, 0.75)
			.click(function() {
	            var $this = $(this);
				var id = $this.attr('id');
				var dataArray = id.split('-');
				var startPoint = dataArray[1];
				self.dataId = id;
				$this.parent().children().fadeTo(50, 0.75).css({'fontWeight': 'normal'});
				$this.fadeTo(0, 1).css({'fontWeight': 'bold'});
				self.gotoFrame(startPoint);	
			});
			
			
			$('<span>' + itemText + '</span>').appendTo(itemCont);
				
			if (opts.allowEdit){
				self.editableItem(opts.type, itemCont);
			}	
		}	
		
		$('div.innerContainer',self.element.context).append(itemCont);	  
	},
	
	editableItem : function(type, itemCont) {
		var self = this;
		switch(type) {
			case "annotations" : 
				self.editableAnnItem(itemCont);
			break;
			case "transcriptions" : 
				self.editableTransItem(itemCont);
			break;
			default: self.editableAnnItem(itemCont);
		}		
	},
	
	editableAnnItem : function(itemCont) {
		var self = this;
		var opts = self.options;
		$('<span class="delete-icon"></span>')
					.css({'background-color': (opts.themeColor).replace(/0x/, '#'), 'opacity':0.8})
					.addClass('ui-corner-all')
					.hide()
					.hover(
						function () {
							$(this).css({'opacity':1 });
						}, 
						function () {
							$(this).css({'opacity':0.8 });
						}
					)
					.click(function(event){
						event.stopPropagation();
						var conc = $(this).parent();
						var id = conc.attr('id');
						var dataArray = id.split('-');
						id = dataArray[0]
						self.deleteAnnotation(id);
							conc.remove();
					})
					.appendTo(itemCont);
			
												   
			
				itemCont.hover(
					function () {
						$(this).find(".delete-icon").show();
					}, 
					function () {
						$(this).find(".delete-icon").hide();
					}
				);
				
		
	},
	
	editableTransItem : function(itemCont) {
		var self = this;
		var opts = self.options;
		$('<span class="edit-icon"></span>')
					.css({'background-color': (opts.themeColor).replace(/0x/, '#'), 'opacity':0.8})
					.addClass('ui-corner-all')
					.hide()
					.hover(
						function () {
							$(this).css({'opacity':1 });
						}, 
						function () {
							$(this).css({'opacity':0.8 });
						}
					)
					.bind('click', function(event){
						event.stopPropagation();
						self.pauseVideo();
						var $this = $(this);
						var $thisParent = $this.parent();
						var editEl = $thisParent.find("span").eq(0);
						
						editEl.editInPlace({
							callback: function(original_element, html, original){
								var id = $thisParent.attr('id');
								var dataArray = id.split('-');
				                var idTrans = dataArray[0];
								self.editTranscription(idTrans, html);
								return html;
							}
						});
						
						editEl.trigger('click.editInPlace');
						$thisParent.unbind('click');
						$this.hide();
						
					})
					.appendTo(itemCont);
			
												   
			
				itemCont.hover(
					function () {
						$(this).find(".edit-icon").show();
					}, 
					function () {
						$(this).find(".edit-icon").hide();
					}
				);
	},
	
	createSearchAnnotationItem : function ( el){
		var self =this;
		var element = self.element;
		var opts = self.options;
		var elRef = $(el);
		var context = element.context;
		
		res = $("<li></li>");
		var id_item = elRef.find('id').text();
		var start = elRef.find('start').text();
		
		var time = $("<span></span>");
		time.css({'background-color': (opts.highlightBg).replace(/0x/, '#'), 'color': (opts.highlightText).replace(/0x/, '#')})
			.addClass('ui-corner-all')
			.append( this.millisecToStringTime( start ));
		
		res.append( time );
		
		res.attr('id',  start);
		res.append( elRef.find('context_pre').text() );
		res.append("<strong style='margin: 0 3px'>"+$(el).find('keyword').text() +"</strong>");
		res.append( elRef.find('context_post').text() );
		res.css({'background-color': (opts.themeColor).replace(/0x/, '#')}).addClass('ui-corner-all');
		res.hover(
						function () {
							$(this).css({'opacity':'1' });
							self.gotoFrame( $(this).attr('id') );
						}, 
						function () {
							$(this).css({'opacity':'0.8' });
						}
					);
		res.click(function(event){
			self.dataId = id_item+"-"+start;
			self.gotoFrame( $(this).attr('id') );
			$(".resultsContainer", context).css({'display':'none'});
			$(".innerContainer", context).css({'display':'inline'});
			//$("#" + id_item+"-"+start, self.element.context).css({'fontWeight': 'bold'});
			
		});
					
		$(".resultsContainer .resList", context).append(res);
		return false;
			
	},
	createSearchTranscriptionItem : function ( el){
		var self =this;
		var element = self.element;
		var opts = self.options;
		
		var elRef = $(el);
		var context = element.context;
		
		res = $("<li></li>");
		
		var id_item = elRef.find('id').text();
		var start = elRef.find('start').text();
		
		var time = $("<span></span>");
		time.css({'background-color': (opts.highlightBg).replace(/0x/, '#'), 'color': (opts.highlightText).replace(/0x/, '#')})
			.addClass('ui-corner-all')
			.append( this.millisecToStringTime( start ));
		res.append( time );
		
		res.attr('id', start );
		var text = opts.serverOwner == "unifi" ? elRef.find('keyword').text() :  elRef.find('text').text();
		res.append( text )
		   	.css({'background-color': (opts.themeColor).replace(/0x/, '#')})
			.addClass('ui-corner-all')
			.hover(
						function () {
							$(this).css({'opacity':'1' });
							self.gotoFrame( $(this).attr('id') );
						}, 
						function () {
							$(this).css({'opacity':'0.8' });
						}
					)
			.click(function(event){
				self.dataId = id_item+"-"+start;
				self.gotoFrame( $(this).attr('id') );
				$(".resultsContainer", context).css({'display':'none'});
				$(".innerContainer", context).css({'display':'inline'});
			});
					
		$(".resultsContainer .resList", context).append(res);
		return false;
	},
	
	loadSimilarImages: function (timepoint){
		var context = this.element.context;
		var self = this;
		var opts = self.options;
		
		var similarityContainer = jQuery('.similarity-container', context);
		
		jQuery('.inner-images-container', similarityContainer).empty();
		
		jQuery('.images-preloader', similarityContainer).css({"display":"inline"});
		
		var display = similarityContainer.css('display');
		if (display == 'none') similarityContainer.show();	
		
		//Getting first keyframe
		var video = document.getElementById('video-player');
		if(video != null){
			var timepoint = Math.floor(video.currentTime * 1000);
			var query = '';
			$.ajax({
				url : '../php/getKeyframeId.php?id='+self.options.sourceId + '&timepoint='+ timepoint,
				dataType : "html",
				success : function(data) {
				     console.log('Finding keyframes');
				     query = utils.getSimilarImagesID(data);
				},
				async: false
			});
			
			var url = CFG.absolutePath + "php/solrProxy.php?proxy_url=" + encodeURIComponent(CFG.solrSelectUrl + "q=*&fq=id:" + query + "&fq=type:keyframe");
			
			//TODO similar images
			
			$.ajax({
				url : url,
				method : "GET",
				dataType : "HTML",
				success : function(data) {
//					console.log(data);
					var images;
					images = $(data).find('doc');
					var response = '<document>';
					for ( var i = 0; i < $(images).length; i++) {
							response += '<images>';
							var path = CFG.mediaDirPath + "image/";

							var src = path + $(images[i]).find("str[name='filename']").text();
							response += '<url>'+ src + '</url>';
							var sourceID = $(images[i]).find("str[name='id_media']").text();
							response += '<sourceId>' + sourceID + '</sourceId>';
							var timepoint = $(images[i]).find("int[name='timepoint']").text();
							response += '<timePoint>' + timepoint + '</timePoint>';
							response += '</images>';	
					}
					response += '</document>';
					self.loadedSimilarImages(response);
				},
				error : function(err) {
					console.log("Error: " + err);
				}
			});
			return false;
		}
		
	},
	
	loadedSimilarImages : function (data){
		if(data != null){
			
			var self = this;
			var context = self.element.context;
			
			jQuery('.images-preloader', context).css({'display':'none'});
			$('.inner-images-container').html('');
			var innerImagesContainer = jQuery('.inner-images-container', context);
			var imgHeight = parseInt(innerImagesContainer.innerHeight());
			var imgWidth = parseInt(innerImagesContainer.innerWidth());
			
			var imgArray = $(data).find('images');
			var imgPerRow = 2;
			
			var top =10 , left = 10;
			var imageWidth = parseInt((imgWidth -30)/imgPerRow );
			var imageHeight = imageWidth * 3/4;
			
			for ( var i=1; i < imgArray.length+1; i++){
		
				var imageRef = $(imgArray[i-1]);
				var image = self.createSimilarImage(
					imageRef.find('timePoint').text(),
					imageRef.find('url').text(), 
					imageRef.find('sourceId').text(),
					i,
					imageWidth,
					imageHeight
				).css({'top':top, 'left':left});
				
				innerImagesContainer.append(image);
				
				left = left + imageWidth +5 ; 
				if( i % (imgPerRow) == 0){
					left = 10;
					top = top +  imageHeight +10;
				}
			}
		
		}
			
		jQuery('img', innerImagesContainer).show();
		return false;
	},
	errorloadingSimilarImages : function (data){
		
	},
	
	createSimilarImage :function(timePoint, url, videoId, imageIndex, imgWidth, imgHeight) {
		var self = this;
		var opts = self.options;
		
		var imageContainer = $('<div>')
			.attr({'id': (timePoint + "-" + videoId +"-container")})
			.css({'position':'absolute',
				 'height':imgHeight,
				 'width':imgWidth,
				 'padding':2,
				 'margin':0,
				  'background-color': (opts.themeColor).replace(/0x/, "#")
				 })
			.addClass('ui-corner-all')	
			.hover(
				function() {
					var $this = $(this);
					
					var newWidth = $this.width()+60;
					var newHeight = $this.height()+30;
					
					$this.css({
					'width': newWidth,
					'height': newHeight,
					'top': parseInt($this.css('top'))-15,
					'left' : parseInt($this.css('left')) -30,
					'z-index':1000
					});
					
					$("img", $this).css({
						'width': newWidth ,
						'height': newHeight,
					});
					
					var id = $this.attr('id');
					var dataArray = id.split('-');
					var startPoint = dataArray[0];
					var sourceId = dataArray[1];
							
					if (sourceId != "" && startPoint != ""){
						 var playButton = $('<div>')
						.addClass('play-btn')
						.css({'left': ( parseInt($this.width()) /2) - 22 })
						.click(function(event) {	
							
							self.options.sourceId = sourceId;
							
							self.updateWidget(startPoint, "startPoint");
						});

						$this.append(playButton);
					}
				
				}, 
				function() {
					var $this = $(this);
					
					var newWidth = $this.width()-60;
					var newHeight = $this.height()-30;
					
					$this.css({
					'width': newWidth,
					'height': newHeight ,
					'top': parseInt($this.css('top'))+15,
					'left' : parseInt($this.css('left')) +30,
					'z-index':10
					});
					
					$("img", $this).css({
						'width': newWidth,
						'height': newHeight
					});
					
					$('.play-btn', $this).remove();
				})
			.toggle(function() {
	            var $this = $(this);
				$this.css({
					'background-color': (opts.highlightBg).replace(/0x/, "#")
					})
					.addClass('selected');
				/*var id = $this.attr('id');
				var dataArray = id.split('-');
				var frameId = dataArray[0];
				var videoId = dataArray[1];*/
				}, function() {
					 var $this = $(this);
					$this.css({
					'background-color': (opts.themeColor).replace(/0x/, "#")
					})
					.removeClass('selected');
					
			});
			
		//if (imageIndex < 10) {
		var imageItem = $('<img />')
			.attr({'id': timePoint + "-" + videoId, 'src': "../php/timthumb.php?src=" + url 
					+ "&w=48"
					+ "&h=48"
					+ "&zc=1"
					})
			.css({
				 'cursor': 'pointer', 
				 //'background': (opts.themeColor).replace(/0x/, '#'),
				 'height':imgHeight,
				 'width':imgWidth,
				 'display': 'none'
			})
			.addClass('ui-corner-all');
				
		imageContainer.append(imageItem);

		return imageContainer;
		
	},
	
	toggleAnnotations : function(){
		if(this.options.showData == true){
			this.options.showData = false;
			$('.innerContainer').hide();
			$('#annot-icon').attr('src', '../img/controls/annotations-none.png');
		} else {
			this.options.showData = true;
			$('.innerContainer').show();
			$('#annot-icon').attr('src', '../img/controls/annotations.png');
		}
	},
	
	updateWidget : function(param, type) {
		var self = this;
		var opts = self.options;
		
		this._destroy();
		switch (type) {
			case "startPoint":
				if (!param){
					param = 0;
				}
				opts.startPoint = param;
				
			break;
			case "sourceId":
				opts.sourceId = param;
			break;
			case "serverOwner":
				opts.serverOwner = param;
			break;
		}
		this._create();
		
	},
	
	hideAllOverlay : function(){
		var self = this;
		var element = self.element;
		$('.suggestions-list-search',element.context).hide();	
		$('.suggestions-list',element.context).hide();	
		return false;
	},
	
	millisecToStringTime : function( ms ){
		totalSec = ms/1000;
		
		hours = parseInt( totalSec / 3600 ) % 24;
		minutes = parseInt( totalSec / 60 ) % 60;
		seconds = parseInt(totalSec % 60);

		if (hours > 0) {
			result = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);
		}else{
			result = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);
		}
		return result;
	}
	
});


$.extend( $.ui.videocomponent, { version: "@VERSION" });

})( jQuery );
