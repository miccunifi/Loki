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
	
$.widget( "ui.imagecomponent", {
   
	options: {
		sourceId : "3",
		width : 'auto',
		themeColor: "0xdddddd",
		textColor: "0x001100",
		highlightBg: "0xaa0000",
		highlightText: "0xffffff",
		serverOwner : "unifi",
		showData: true,
		allowEdit: true,
		sessionId: null,
		similaritySearch: true
	},
	

	_create: function() {
		var self = this; 
		var opts = self.options;
		var el = self.element;
		el
			.addClass( "eutv-imagecomponent" )
			.attr({
				role: "imagecomponent"
			})
			.empty();
			
	
		
		this.getImageInfo();
		this._initializePreloader();
			 
		return false;
	},
	
	
	_destroy: function() {
		this.element
			.removeClass( "eutv-imagecomponent" )
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
		
		
		if ( key === "showData" ) {
			opts.showData = value;
		}
		if ( key === "allowEdit" ) {
			opts.allowEdit = value;
		}
		
		
		if ( key === "similaritySearch" ) {
			opts.similaritySearch = value;
		}
		
		self._super( "_setOption", key, value );
		
	},

	_refreshValue: function() {
		var self = this;
		return false;
	},
	
	_initializePreloader: function() {
		var self = this;
		var opts = self.options;
		var preloader = jQuery('<img></img>').attr('class','image-preloader');
		
		preloader.attr('src', '../img/loader.gif');
		
		preloader.css({
			'borderColor': (opts.themeColor).replace(/0x/, "#"),
			'top': '150px', //(opts.height / 2 - 16) + "px",
			'left': '295px', //(opts.width / 2 - 16) + "px"
			'width': '50px',
			'height': '50px',
			'position': 'absolute',
			'z-index': '100'
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
	
	_loadImage: function(img_url) {
		
		var self = this;
		var opts = self.options;
		var element = self.element;
		var context = self.element.context;
		
		var imageContent = jQuery('<div></div>').attr('class','imageContainer').css({'position':'absolute'});
		var image = jQuery('<img></img>')
					.attr('class','sourceImage').attr('src',img_url ).css({'position':'absolute'});
					
		
		if (self.options.width != 'auto'){
//			image.attr('width', self.options.width);
		}
		imageContent.append(image);
		element.append(imageContent);
		
		var level = 0;
		var sd = opts.showData;
		var ad = opts.allowEdit;
		if(sd && ad) level = 2;
		if(sd && !ad) level = 1;
		
		console.log('level ' + level);
		
		image.load( function(){
			imageContent.css({
						'width' : image.width(),
						'height': image.height(), 

					});
			
			if (level !== 0) {
				
				var resultsContainer = jQuery('<div></div>').attr('class','tagContainer');
				
				resultsContainer.css({
					'position' : 'absolute',
					'left': image.width() + 5,
					'width': "200px", 
					'height': image.height(), 
					'background': (opts.themeColor).replace(/0x/, "#"),
					'z-index' : '5',
				
				}).addClass('ui-corner-bottom ui-corner-tl').data('state','open');
				
				var annotationContainer = jQuery('<div></div>').attr('class','annotationContainer');
				annotationContainer.css({
					"width": "95%",
					"height": image.height() - 40 + "px",
					"overflow" : "auto",
					"padding" : "5px"
				});
				element.append(resultsContainer);
				
				
				var conteninerToggle = jQuery('<div><p>close</p></div>').addClass('tagContainerToggle');
				conteninerToggle.css({
					'position' : 'absolute',
					'left': '200px',
					'width': "30px", 
					'top': '0px', 
					'height' : '60px',
					'cursor' : 'pointer',
					'font-weight' : 'bold',
					'background': (opts.themeColor).replace(/0x/, "#"),
				
				}).addClass('ui-corner-right');
				
				//Annotations toggle
				conteninerToggle.click(function(){
					if( resultsContainer.data('state') == 'open'){
						resultsContainer.animate({ 
						  "left": "-=205", "opacity": 0.8 
						}, { duration: "medium" }); 
						resultsContainer.data('state','close');
						conteninerToggle.html("<p>open</p>");
					}else{
						resultsContainer.animate({ 
						  "left": "+=205", "opacity": 1 
						}, { duration: "medium" }); 
						resultsContainer.data('state','open');
						conteninerToggle.html("<p>close</p>");

					}
					
				});
				resultsContainer.append(conteninerToggle);
			
				
				if (level == 2){
					var annotationToolsContainer = jQuery('<div></div>').attr('class','annotationToolsContainer');
					annotationToolsContainer.css({
						'margin' : '5px',
						'width' : '190px',
						'height': '30px', 
						'border-bottom' : '1px solid',
						'border-color': (opts.textColor).replace(/0x/, "#"),
						'border-color': (opts.textColor).replace(/0x/, "#"),
						'padding-bottom': '10px'
					}).append("<input class='addTagInput' name='addTagInput' title='Add new tag' placeholder='Add new tag' />");

					annotationToolsContainer.append( jQuery("<div class='toggleTagPosition iconic pin'></div>").css({'font-size':'14px','text-align':'center','color': (opts.textColor).replace(/0x/, "#"), 'display': 'block', 'padding-top': '2px'}) );
					
					annotationToolsContainer.append( jQuery("<span class='addTagButton iconic plus_alt'></span>").css({'font-size':'16px','color': (opts.textColor).replace(/0x/, "#")}));
					//add button click listener
					$('.addTagButton',annotationToolsContainer).click(function(){
						self.addAnnotation();
					});
					
					// Set default value in text input
					var inputText = $(".addTagInput",annotationToolsContainer);
					
					inputText.keypress(function(e){
						if(e.which == 13){
							self.addAnnotation();
						}	 
					});
					
					resultsContainer.append(annotationToolsContainer);
					
					
					// Toggle button for box tagging over the image
					$('.toggleTagPosition').click(
							function(){
								if($(this).hasClass('toggled')){
									$(this).removeClass('toggled');
									self.removeTagPositioner();
								} else {
									$(this).addClass('toggled');
									self.createTagPositioner();
								}
							}
						);
					
				} // end-if level 2
				
				resultsContainer.append(annotationContainer);
				
				self.getAnnotations();
			}// end of level != 0
		});//end of image load
		$('.image-preloader').hide();
	},
	
	
	getImageInfo: function() {
		var self = this;
		var opts = self.options;
		var sessionId = opts.sessionId;
		var sourceId = opts.sourceId;
		var serverOwner = opts.serverOwner; 
		im3iSoapCalls.getSourceId(
			sessionId, 
			sourceId, 
			serverOwner, 
			function(data){self.parseImageInfo(data);}, 
			function(data){self.errorImageInfo(data);}
		);
	},
	
	parseImageInfo: function(data) {
		var self = this;

		var img_url = $(data).find('http_url').text();
		
		self._loadImage(img_url);
		
	},
	
	errorImageInfo: function(data) {
	
	},
	
	addAnnotation : function(data) {
		var self = this;
		var opts = self.options;
		var context = self.element.context;
		
		
		var inputText = $(".addTagInput",context);
		
		if(inputText.val() != "" &&  !inputText.hasClass('defaultTextActive')){
			var text = inputText.val();
			
			var agent = "Images annotation component";
			var comment = "Manual annotation from images annotation component";
			var sessionId = opts.sessionId;
			var sourceId = opts.sourceId;
			var confidence = "1.0";
			var soapServer = opts.serverOwner;
			var type = "manual";
			var keyword = text;
			var timePoint =0;
			
			var videoAnnotation = {'sourceId': sourceId, 'keyword': text, 'timePoint': timePoint, 'duration': 0 };
			
			//check if bounding box of the annotation is enabled (length = 0 => false)
			var annotationBox = $('.tagBox',context);
			
			if (annotationBox.length){
				var img = $('.sourceImage',context);
				
				var box ={
					'box_x': parseInt(annotationBox.css('left')) / img.width(), 
					'box_y': parseInt(annotationBox.css('top')) / img.height(),
					'box_width': annotationBox.width() / img.width(),
					'box_height':annotationBox.height() / img.height()
				};
				
				$.extend(videoAnnotation, box);

				//videoAnnotation.push(box)
				
			}
			
			
			var annotations = [];
			annotations.push(videoAnnotation);
		
			
			//reset annotations tools
			inputText.val("");
			inputText.blur();
			self.removeTagPositioner();
			$('.toggleTagPosition',context).removeClass('toggled');
			
			console.log(annotations);
			
			var loader = '<img class="image-loader" src=' + CFG.absolutePath + 'img/ajax-loader.gif>';
			$('.annotationContainer').append(loader);
			
			im3iSoapCalls.addAnnotationImage(
				agent, 
				comment, 
				sessionId, 
				confidence, 
				soapServer,
				type, 
				annotations,
				function(data){
					$('.image-loader').hide();
					self.addedAnnotation(data);
				}, 
				function(data){
					self.errorAddingAnnotation(data);
				}
			);
	
		}// end if (text not empty)
	},
	
	addedAnnotation : function (data){
		var self = this;
    	var op = self.options;
		var dataRef = $(data);
		var annotationItemId;
		var annotationText;
		
		if (op.serverOwner == "unifi") {
			var annotationItems = dataRef.find('annotations');
			for (var i = 0; i < annotationItems.length; i++) {
				var sourceId =  jQuery(annotationItems[i]).find('sourceId').text(); 
				
				if (sourceId == op.sourceId) {
					
					self.createAnnotationItem($(annotationItems[i]));
				}
			}
			//var annotationItemId = dataRef.find('id').text();
			//var annotationText = dataRef.find('concept').text();
		} else {
			annotationItemId = dataRef.find('id').text();  
			annotationText = dataRef.find('keyword').text(); 
			//self.createAnnotationItem(annotationText,annotationItemId);
		}
		
	},
	
	errorAddingAnnotation : function (data){
	
	},
	
	createTagPositioner : function (x,y,width,height){
		var self = this;
		var el = self.element;
		var opts = self.options;
		
		var imgContainer = 	$('.imageContainer', el);
		var image = $('.sourceImage', el.context);
		
		if( !x) x = 50;
		if( !y) y = 50;
		if( !width) width = 150;
		if( !height) height = 150;
		
		
		var imgCover = jQuery('<div></div>').attr('class','imageCover')
			.css({
				'width' : image.width(),
				'height' : image.height(),
				'background':'#222',
				'opacity':0.7
			});
		
		imgContainer.append(imgCover);
		
		var box = jQuery('<div></div>').attr('class','tagBox')
					.css({ 'position':'absolute',
						    'cursor' : 'pointer',
						   'top' : x,
						   'left' : y,
						   'width' : width,
						   'height' : height,
						   'border' : '2px solid #efefef',
						   'background-image': 'url('+image.attr('src') + ')',
						   'background-position' :  '-'+ parseInt(x +2) +'px -'+ parseInt(y+2) +'px', 
						   'background-size' :   image.width() +'px '+ image.height() +'px' 

						});
		box.draggable({
			containment: image, 
			scroll: false,
			drag: function() {
				$(this).css({'background-position' : '-'+parseInt(parseInt($(this).css('left')) +2)  +'px -'+ parseInt(parseInt($(this).css('top')) +2)+'px' });
			}
		});
		box.resizable({containment: image});	
		
		var boxLabel = jQuery('<span></span>').attr('class','tagBoxLabel')
					.css({ 'position':'absolute',
						   'top' : -2,
						   'left' : -2,
						   'height' : 15,
						   'background' : '#efefef',
						   'padding': '2px',
						   'font-size': '10px'
						});		
		
		var tagInput = $('.addTagInput', el);
		if(tagInput.val() == ''){
			boxLabel.html('Add new tag');
		} else {
			boxLabel.html(tagInput.val());
		}

		tagInput.keyup(function(){
			boxLabel.html( $(this).val() );
		});

		box.append(boxLabel);					
		imgContainer.append(box);
	},
	
	removeTagPositioner : function (){
		var self = this;
		var el = self.element;		
		$('.imageContainer .tagBox', el).remove();
		$('.imageContainer .imageCover', el).remove();
	},
	
	loadSimilarImages: function (timepoint){
		var self = this;
		var opts = self.options;
		
		im3iSoapCalls.findSimilarImages(
				opts.sessionId, 
				opts.sourceId, 
				timepoint,
				1,
				1, 
				0,
				10,
				opts.serverOwner,
				function(data){ 
					self.loadedSimilarImages(data);
				}, 
				function(data){
					self.errorloadingSimilarImages(data);
		});
		return false;
	},
	
	getAnnotations: function(){
		var self = this;
		var opts = self.options;
		
		var timePoint = 0;
		var duration = 0; 
		
		im3iSoapCalls.getAnnotationImage(
			opts.sourceId, 
			opts.sessionId, 
			timePoint, 
			duration, 
			opts.serverOwner, 
			function(data){
				self.showAnnotation(data);
			}, 
			function(data){
				self.errorAnnotation(data);
			});
	},
	
	showAnnotation : function(data) {
		var self = this;
		var opt = self.options;
		var el = self.element;
		
		
		/*var responseEl = $('div.serverResponse');
		responseEl.empty();
		var wrappedData = $('<p></p>').text(utils.xmlToString(data));
		responseEl.append(wrappedData);*/
	
		if(data.length==0) {
			var feedback = $('<div></div>');
			innCont.append(feedback);
			return;
		}
		
		
		var items = "";
		var key = opt.serverOwner == "unifi" ? 'annotations' : 'annotation';
		
		var annArray = $(data).find(key);
		
		for (var i=0; i<annArray.length; i++){
			var annRef = $(annArray[i]);
			self.createAnnotationItem(annRef);
			
			
		}
		
		/*$("#" + self.dataId, self.element.context).fadeTo(0, 1).css({'fontWeight': 'bold'});
		self.dataId = null;
		self.loadedData = data;*/
	},
	
	errorAnnotation : function (data){
	
	},
	
	createAnnotationItem : function (annRef){
		var self = this;
		var opts = self.options;
		var el = self.element;
		
		var keyword = $(annRef).find('keyword').text();
		var id = $(annRef).find('id').text();
		
		var resContainer = $('.annotationContainer', el.context);

		var itemCont = $('<div></div>')
			.attr({'id': "annotation-" + id , 'class': 'annotationItem'})
			.css({
				 'color': (opts.themeColor).replace(/0x/, '#'), 
				 'cursor': 'pointer', 
				 'background': (opts.textColor).replace(/0x/, '#'), 
				 "marginBottom": "5px"})
			.addClass('ui-corner-all')
			.css({'display': "none"})
			.fadeTo(250, 0.75);
			
		
		$('<span>' + keyword + '</span>').appendTo(itemCont);
		
		//if bounding box exist create box handler
		if ( annRef.find('box_x').text() ){
			itemCont.append('<span class="iconic pin" style="float:left; font-size:12px; margin:0px 3px"></span>');
			var imgContainer = 	$('.imageContainer', el);
			var image = $('.sourceImage', el.context);
			
			itemCont.hover(
				function () {
					var box = jQuery('<div></div>').attr('class','tagBoxVisualize')
					.css({ 'position':'absolute',
						   'left' : annRef.find('box_x').text() * image.width(),
						   'top' : annRef.find('box_y').text() * image.height(),
						   'width' : annRef.find('box_width').text() * image.width(),
						   'height' : annRef.find('box_height').text() * image.height(),
						   'border' : '2px solid #efefef',
						});
					
					var boxLabel = jQuery('<span></span>').attr('class','tagBoxLabel')
					.css({ 'position':'absolute',
						   'top' : -2,
						   'left' : -2,
						   'height' : 15,
						   'background' : '#efefef',
						   'padding': '2px',
						   'font-size': '10px'
						});		
		
					boxLabel.html(keyword);
					box.append(boxLabel);	
					imgContainer.append(box);

				}, 
				function () {
					$('.imageContainer .tagBoxVisualize', el).remove();
				}
			);
		
		}
		
			
		
			
		if (opts.allowEdit){
			self.editableAnnItem(itemCont);
		}	
	
		
		resContainer.append(itemCont);	
	},
	
	editableAnnItem : function(itemCont) {
		var self = this;
		var opts = self.options;
		var el = self.element;
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
						id = dataArray[1];
						self.deleteAnnotation(id);
						conc.remove();
						
						$('.tagBoxVisualize',el.context).remove();
					})
					.appendTo(itemCont,el);
			
												   
			
				itemCont.hover(
					function () {
						$(this).find(".delete-icon").show();
					}, 
					function () {
						$(this).find(".delete-icon").hide();
					}
				);
				
		
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
								self.deletedAnnotation(data)
							}, 
							function(data){
								self.errorDeletingAnnotation(data)
							}
		);
	},
	deletedAnnotation : function(data) {},

	errorDeletingAnnotation : function(data) {},

	
	loadedSimilarImages : function (data){
		
	},
	errorloadingSimilarImages : function (data){
		
	},
	
	updateWidget : function(param, type) {
		var self = this;
		var opts = self.options;
		
		this._destroy();
		
		this._create();
		
	},
	
});


$.extend( $.ui.component, { version: "@VERSION" });

})( jQuery );
