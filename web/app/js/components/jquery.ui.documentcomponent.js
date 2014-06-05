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
	
$.widget( "ui.documentcomponent", {
   
	options: {
		sourceId : "3",
		width : 640,
		height : 480,
		themeColor: "#f5f5f5",
		textColor: "#555555",
		borderColor: "#333333",
		buttonColor: "#999999",
		highlightBg: "#aa0000",
		highlightThumb: "#ffffff",
		annotationMineBg : "#00aa55",
		annotationMineText : "#fcfcfc",
		annotationAllBg : "#bb3300",
		annotationAllText : "#fcfcfc",
		serverOwner : "unifi",
		ownerName : "eutv",
		ownerID: 1,
		showData: true,
		allowEdit: true,
		annotationsQueryType: 'all',
		sessionId: null,
		activePage: 1,
		searchResultPerPage: 5
	},
	
	doc_url: null,
	num_pages: null,
	page_base_url: null,
	page_extension: null,
	thumb_base_url: null,
	thumb_extension: null,
	doc_ratio: null,
	active_page: null,
	request_in_process: false,
	scale: 0.9,
	translate: '0, 0',
	
	svgScale: null,
    scaleFactor: null,
    originalWrapWidth: null,
    originalWrapHeight: null,
    wrapWrapWidth: null,
    wrapWrapHeight: null,
	rxLength: /^(\d+\.\d*|\d*\.\d+|\d+)(px|in|cm)?$/,
	nMouseOffsetX: null,
	nMouseOffsetY: null,
	noteDragging: false,
	listenersInitialized: false,
	state: "normal",
	currentScale: null,

	resultsStartIndex: 0,
	paginationActive: false,
	searchedTerm: null,
	
	_create: function() {
		var self = this; 
		var opts = self.options;
		var el = self.element;
		var width = opts.width;
		
		el
			.addClass( "eutv-documentcomponent" )
			.attr({
				role: "documentcomponent"
			})
			.empty()
			.css({
				'width': width + "px", 
				'height': opts.height + "px",
				'background-color': opts.themeColor.replace(/0x/, '#'),
				'border-color': opts.borderColor.replace(/0x/, '#'),
				'position': 'absolute',
				'zIndex': '1'
		});
		
		self.state = "normal",
	
		self.active_page = opts.activePage
		self._initializePreloader();
		self.getDocumentInfo();
						 
		return false;
	},
	
	
	_destroy: function() {
		this.element
			.removeClass( "eutv-documentcomponent" )
			.removeAttr( "role" );
			
		return false;
	},


	_setOption: function( key, value ) {
		var self = this;
		var opts = self.options;
		self.listenersInitialized = false;
		if ( key === "sourceId" ) {
			opts.sourceId = value;
		}
		
		if ( key === "userId" ) {
			opts.sessionId = value
		}
		

		if ( key === "width" ) {
			opts.width = value
		}
		
		if ( key === "height" ) {
			opts.height = value
		}
		
		if ( key === "themeColor" ) {
			opts.themeColor = value
		}
		
		if ( key === "textColor" ) {
			opts.textColor = value;
		}
		
		if ( key === "borderColor" ) {
			opts.borderColor = value;
		}
		
		if ( key === "buttonColor" ) {
			opts.buttonColor = value;
		}
		
		if ( key === "highlightBg" ) {
			opts.highlightBg = value;
		}
		if ( key === "annotationMineBg" ) {
			opts.annotationMineBg = value;
		}
		if ( key === "annotationMineText" ) {
			opts.annotationMineText = value;
		}
		if ( key === "annotationAllBg" ) {
			opts.annotationAllBg = value;
		}
		if ( key === "annotationAllText" ) {
			opts.annotationAllText = value;
		}
		 
		if ( key === "highlightThumb" ) {
			opts.highlightThumb = value;
		}
		
		
		if ( key === "showData" ) {
			opts.showData = value;
		}
		if ( key === "allowEdit" ) {
			opts.allowEdit = value;
		}
		
		if (key ==="annotationsQueryType"){
			opts.annotationsQueryType = value;
		}
		
		if (key == "searchResultPerPage") {
			opts.searchResultPerPage = value;
		}
		if (key == "ownerName") {
			opts.ownerName = value;
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
		var preloader = jQuery('<div></div>').attr('class','preloader');
		
		preloader
			.addClass('ui-corner-all')
			.css("display", "block");
					
		self.element.append(preloader);	
		
		return false;
	},
	
	createWrapper: function(url) {
		var self = this;
		var opts = self.options;
		var el = self.element;
		var context = el.context;
		var contextID = context.id
		var svgWrapper = jQuery('<div id="svgWrapper_' + contextID + '"></div>');
		
		//NEW
		var height = Math.round(parseInt(opts.width / self.doc_ratio));
		svgWrapper[0].style.height = height + "px";
		
		//var width = opts.width;
		//var height = Math.round(parseInt(opts.width / self.doc_ratio));
		//svgWrapper[0].style.width = width + "px";
		svgWrapper[0].style.height = height + "px";
		
		
		
		
		
		el.append(svgWrapper);
		
		
		svgWrapper.svg({onLoad: function(svgEl) {
			
    		jQuery(svgEl.root()).wrap('<div class="svgwrapwrap" id="svgwrapwrap_' + contextID + '"><div class="svgwrap" id="svgwrap_'+ contextID +'" /></div>');
			self.loadSVGPage(1);
			
			
			//ADD the annotation sidebar
			var noteSidebar = jQuery('<div class="notesidebar" id="notesidebar_' + contextID + '"></div>');
			noteSidebar.css({
					'height': Number(height) - 32 + "px", 
					'max-height' : "200px", 
					'display': 'none'
				}).addClass('ui-corner-all');

			noteSidebar.appendTo("#svgwrapwrap_" + context.id);
			
			//add option to show/hide annotations from other users
			if (self.options.queryType != "mine"){
				var noteOpts = jQuery('<div class="notesidebar-opt" id="notesidebar_opts_' + contextID + '"><span class="note-options"><input id="note-options-check-'+ contextID +'" type="checkbox" name="check-show-my-note" /> Show only my annotations</span></div>');
				noteOpts.css({
						'background': opts.themeColor.replace(/0x/, '#'),
						'color' : opts.borderColor.replace(/0x/, '#'),
						'border-color' : 'rgba(51, 51, 51, 0.32)',
						'opacity':'0.8'
				    })	
				.addClass('ui-corner-all');
				
				$('#note-options-check-'+ contextID, noteOpts).change(function(e) {
						if( $(this).prop("checked") ){
							$(".annotation-all").hide();
						}else{
							$(".annotation-all").show();
						}
				});
				
				noteSidebar.append(noteOpts);
			}
			noteSidebar.append('<div class="notesidebar-container" id="notesidebar_container_' + contextID + '"></div>')
		}});
	},
	
	thumbsScroller: function() {
		var self = this;
		var opts = self.options;
		var el = self.element;
		var context = el.context;
		var contextID = context.id;
		
		var wrapper;
		var thumbsWrapper = jQuery('#thumbsWrapper_' + contextID);
		
		
		if (!(thumbsWrapper.length > 0)) {
			
			thumbsWrapper = jQuery('<div id="thumbsWrapper_' + contextID + '"></div>');
			el.wrap('<div id="wrapper_'+ contextID +'" />');
			
		
		} 
		
		var height = opts.height;
		
		wrapper = jQuery('#wrapper_'+ contextID);
		var wrapper_style_ref = wrapper[0].style;
		wrapper_style_ref.position = 'relative';
		var outerWidth = opts.width;

		wrapper_style_ref.width = parseInt(outerWidth)  + 'px';
		wrapper_style_ref.height = height;
		
		
		
		var borderColor = opts.borderColor.replace(/0x/, '#');
		var itemHeight = Math.round(Math.floor(height / 5));
		
		var scrollPane;
		
		if (self.num_pages > 5) {
			scrollPane = true;
		} else {
			scrollPane = false;
		}
			
		thumbsWrapper_width = parseInt(itemHeight * self.doc_ratio + (scrollPane ? 20 : 4)) + "px";
		
			
		thumbs_wrapper_style_ref = thumbsWrapper[0].style;
		thumbs_wrapper_style_ref.borderColor = borderColor;
		thumbs_wrapper_style_ref.position = 'absolute';
		thumbs_wrapper_style_ref.top = '0px';
		thumbs_wrapper_style_ref.width = thumbsWrapper_width;
		thumbs_wrapper_style_ref.right = "-" + (parseInt(thumbsWrapper_width) + 1) + 'px';
		thumbs_wrapper_style_ref.zIndex = '0';
		thumbs_wrapper_style_ref.height = height  + 'px';
		
		
		var htmlThumbs = '<div class="scrollable vertical">' +   
   							'<div class="items">' ;
							
		
		
		var itemsNum = Math.floor(Math.round(self.num_pages / 5));
		var itemsNumRest = self.num_pages % 5;
		
		
		
		itemsNum = Math.floor(Math.round(self.num_pages / 5));
		itemsNumRest = self.num_pages % 5;
		
		var offset = 10;
		 //parseInt(thumbsWrapper_width) - offset;
		var imgItem_height = parseInt(itemHeight - offset);
		var imgItem_width = imgItem_height * self.doc_ratio;
		
		var bColor = opts.borderColor.replace(/0x/, '#');
		var rgbVal = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ', 0.3)';
		
		// index difference between unifi and hku
		var imgOffset = (opts.serverOwner == 'unifi' ? 0:1);

		
		if (scrollPane) {	
			
			for (var i=0; i < (itemsNum) ; i++) {
				htmlThumbs += '<div>';
				
				
				for (var p=(5*i); p < 5*(i+1); p++) {
					if (p < self.num_pages){
						htmlThumbs += '<div id="item-' + p + '" class="item" style="height:' + itemHeight + 'px; text-align:center; padding:0; margin:0; border:none; width:'+ thumbsWrapper_width +'; position:relative; cursor: pointer; ">';
						htmlThumbs += 
						'<img height="' + imgItem_height + 'px" width="' + imgItem_width +'" style="position:absolute; top:4px; left:' + ((offset / 2) + 2) + 'px; border:1px solid ' + rgbVal +';" src="' + 
						self.thumb_base_url + (p+imgOffset) + self.thumb_extension +'" />';
						htmlThumbs += '</div>';
					}
				 }
			 htmlThumbs += '</div>'
			}
		
		} else {
				
			//for (var p=0; p < itemsNumRest; p++) {
			for (var p=0; p < self.num_pages; p++) {
				htmlThumbs += '<div id="item-' + p + '" class="item" style="height:' + itemHeight + 'px; text-align:center; padding:0; margin:0; border:none; width:'+ thumbsWrapper_width +'; position:relative;">';
				htmlThumbs += 
						'<img height="' + imgItem_height + 'px" width="' + imgItem_width +'" style="position:absolute; top:4px; left:' + ((offset) / 2) + 'px; border:1px solid ' + rgbVal +';" src="' + 
						self.thumb_base_url + (p+imgOffset) + self.thumb_extension +'" />';
					htmlThumbs += '</div>';
			}
			htmlThumbs += '</div>'
		}
		
		
		htmlThumbs += '</div>' + 
			'</div>';
			
		
			
		
		thumbsWrapper.html(htmlThumbs);
		wrapper.append(thumbsWrapper);	
		
		var scrollableDiv = jQuery(".vertical", wrapper);
		var scrollableDiv_style_ref = scrollableDiv[0].style;
		scrollableDiv_style_ref.top = '0px';
		scrollableDiv_style_ref.width = parseInt(thumbsWrapper_width) + "px";
		scrollableDiv_style_ref.height = el.height()  + "px";
		scrollableDiv_style_ref.backgroundColor = opts.themeColor.replace(/0x/, '#');
		
		var bColor = opts.borderColor.replace(/0x/, '#');
		var rgbValB = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ', 0.3)';
		
		if (scrollPane) {
			scrollableDiv.jScrollPane(
				{
					showArrows: true,
					horizontalGutter: 10
				}
			);
		}
		
		jQuery('.item', scrollableDiv)
			.click(function() {
				var thumbItem = jQuery(this);
				var value = parseInt(thumbItem.attr('id').split('-')[1]) + 1;
				$('.preloader').show();
				self.loadSVGPage(value);
				self.selectThumb(thumbItem);
			});
		
		
		
		var rgbValTrack = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ')';
		jQuery('.jspHorizontalBar, .jspVerticalBar, .jspTrack', scrollableDiv).css({'background': rgbValTrack});
		
		jQuery('.jspTrack .jspActive, .jspTrack .jspHover, .jspDrag, .jspDrag:hover', scrollableDiv).css({'background': rgbValB});
		
	},
	
	selectThumb: function(thumbItem) {
		var self = this;
		var opts = self.options;
		var bColor = opts.borderColor.replace(/0x/, '#');
		var rgbValB = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ', 0.3)';
		var hColor = opts.highlightThumb.replace(/0x/, '#');
		var rgbValH = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ', 1)';
		
		jQuery('img', jQuery('.scrollable')).css({
			'borderColor': rgbValB
		});
		
		jQuery('img', thumbItem).css({
			'borderColor': rgbValH
		});
	},
	
	
	loadSVGPage: function(page) {
		console.log("loadSVGPage " + page);

		var self = this;
		var contextID = self.element.context.id;
		
		var url = self.page_base_url + page + self.page_extension;
		
		var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
		//svgEl.clear();
		
		//Clear annotations & sidebar
		$('#notesidebar_container_' + contextID).html('');
		$('.noteMarker').remove();
		
		if (!self.request_in_process) {
			self.requestInProcess = true;
			svgEl.load(url, {
					addTo: false, 
	        		changeSize: false, 
					onLoad: function(svg, error) {
						self.loadSVGDone(svg, error, page);
						$('.preloader').hide();
						}
					});
		}	
	},
	
	GetLengthValue: function(s) {
		var self = this;
		
		if (s != null) {
			var m = self.rxLength.exec(s);
		
			if (m != null) {
				var w = parseFloat(m[1]);
				//	do inches conversion
				if (m[2] == "pt")
					w *= 96;
				else if (m[2] == "cm")
					w = w * 96 / 2.54;
				return w;
			}
		}
		return null;
	},
	
	ApplyScale: function(newScale) {
		//console.log("newScale: " + newScale);
		var self = this;
		var contextID = self.element.context.id;
		
		if (newScale < 1/9)
			return;
	
		if (typeof(SliderZoomTo) != 'undefined') {
			newScale = Math.max(0.10, Math.min(10.0, newScale));

			if (newScale == self.scaleFactor)
				return;
			
			//SliderZoomTo(newScale * 100, false);
		}

		self.scaleFactor = newScale;

		var sw = document.getElementById("svgwrap_" + contextID);

		var oldw = sw.offsetWidth;
		var oldh = sw.offsetHeight;
		var oldl = sw.offsetLeft; 
		var oldt = sw.offsetTop; 

		//	compute the new width and height
		var neww = self.originalWrapWidth * newScale;
		var newh = self.originalWrapHeight * newScale;

		//	compute the new left and top
		var zoomCenterX = self.wrapWrapWidth / 2;
		var dx1 = zoomCenterX - oldl;
		var dx2 = dx1 * (neww / oldw);
		var newl = oldl + dx1 - dx2;
		var zoomCenterY = self.wrapWrapHeight / 2;
		var dy1 = zoomCenterY - oldt;
		var dy2 = dy1 * (newh / oldh);
		var newt = oldt + dy1 - dy2;

		//  fix the min and max X and Y for panning
		sw.minX = Math.min(self.wrapWrapWidth - neww, (self.wrapWrapWidth - neww) / 2);
		sw.maxX = Math.max(0, (self.wrapWrapWidth - neww) / 2);
		sw.minY = Math.min(self.wrapWrapHeight - newh, (self.wrapWrapHeight - newh) / 2);
		sw.maxY = Math.max(0, (self.wrapWrapHeight - newh) / 2);

		//  limit the new left and top to the new mins and maxes
		newl = Math.min(Math.max(newl, sw.minX), sw.maxX);
		newt = Math.min(Math.max(newt, sw.minY), sw.maxY);

		//	save all the property updating to last
		sw.style.width = neww.toString() + "px";
		sw.style.height = newh.toString() + "px";
		sw.style.left = newl.toString() + "px";
		sw.style.top = newt.toString() + "px";
	
		document.getElementById("thesvg_" + contextID).setAttribute("width", neww.toString() + "px");
		document.getElementById("thesvg_" + contextID).setAttribute("height", newh.toString() + "px");
		
		
	
		//  and, finally, update the svg scale factor
		//	document.getElementById("svgscale").setAttribute("transform", "scale(" + (svgScale * newScale).toString() + ")");
	},
	
	ZoomWheel: function (e, self) {
		
		if (typeof e == 'undefined') e = window.event;

		if (typeof e.wheelDelta != 'undefined') {
			if (e.wheelDelta > 0)
				self.ZoomIt(1.2);
			else if (e.wheelDelta < 0)
				self.ZoomIt(0.833333333);
		}
		else if (typeof e.detail != 'undefined') {
			if (e.detail < 0)
				self.ZoomIt(1.2)
			else if (e.detail > 0)
				self.ZoomIt(0.833333333);
		}

		//return self.CancelEvent(e);
		e = e ? e : window.event;
    	if (e.stopPropagation)
        	e.stopPropagation();
    	if (e.preventDefault)
        	e.preventDefault();
    	e.cancelBubble = true;
    	e.cancel = true;
    	e.returnValue = false;
    	return false;
	},
	
	ZoomIt: function (delta) {
		var self = this;
		var contextID = self.element.context.id;
	// only update if we'll make a change and don't let the new scalefactor get too small
	if (delta != 1 && 0.1 <= self.scaleFactor * delta) {
			
			self.currentScale = self.scaleFactor * delta;
			self.ApplyScale(self.scaleFactor * delta);
			
			
			var noteMarkers = $('.noteMarker');
			$.each(noteMarkers, function (i, marker){
				var markerObj = $(marker)
				markerObj.css({
					'top': parseFloat(markerObj.css('top')) * delta,
					'left': parseFloat(markerObj.css('left')) * delta
				});
			});
		}
	},
	
	addNote: function(e, self) {
		
		var contextID = self.element.context.id;
		var opts = self.options;
		
		var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
		var svgRoot = svgEl.root();	
		
		
   
   		var p = svgRoot.createSVGPoint();
        p.x = e.clientX;
        p.y = e.clientY;

		
		//e.target.parentNode.appendChild(circle);
		var svgScaleElement = document.getElementById("svgscale_" + contextID);
		var svgEl = document.getElementById("thesvg_" + contextID);
		
		
		var svgWrap =  $("#svgwrap_"+contextID);
		
		
		var noteMarker = $('#noteMarker-new-' + contextID, svgWrap);
		console.log(noteMarker.length);
		// Add the new note marker
		if (noteMarker.length == 0){
			 noteMarker=jQuery('<span id="noteMarker-new-' + contextID +'" class="noteMarker" ></span>');
			 noteMarker.svg();
			 noteMarker.load('../img/comment2.svg',function(el, err){

				 $($(this).find("path")[0]).attr('fill',(opts.buttonColor).replace(/0x/, "#"));
								});
			 console.log(noteMarker);
			 svgWrap.append(noteMarker);
			 noteMarker.draggable({containment: "#svgwrap_"+contextID});
			 
			 var textArea = jQuery('<textarea id="annotation-textarea'+contextID+'" ></textarea>');
			 
			 textArea.blur( function (){
					 			if (textArea.val() == ""){
									$('#noteMarker-new-' + contextID ).remove();
									$('#document-annotation-'+contextID+'-new').remove();
								}
					 		});
							
			textArea.keydown( function(e) {
								if (e.which == '27') { // on ESC
									$('#noteMarker-new-' + contextID ).remove();
									$('#document-annotation-'+contextID+'-new').remove();
								}
								
								if (e.which == '13') { //on ENTER
									self.addAnnotation();
								}
							});
				
			 var newNote = jQuery("<div class='document-annotation' id='document-annotation-"+contextID+"-new'></div>").css({
						'float':'left',
						'width' : '180px',
						'background': opts.borderColor.replace(/0x/, '#'),
						'color' : opts.themeColor.replace(/0x/, '#')
				    })	
				  .addClass('ui-corner-all');
			 
			 newNote.append(textArea);
			 
			 
			 $('#notesidebar_container_' + contextID).append(newNote);
			
			 textArea.focus();
			 
			 
			 

		}
		
		console.log(e.clientY + '  ' +e.clientX);
		noteMarker.css({
			'top': e.clientY - 60,
			'left': e.clientX - opts.width - 50,
		});
		//self.initNoteDrag(note);
	},
	
	
	mouseDownNote: function(e, self) {
		self.noteDragging = true;
		self.registerNotePosition(e);
		
		//console.log(" down self.noteDragging: " +  self.noteDragging);
		//console.log(" down e.target: " +  e.target);
	},
	
	mouseUpNote: function(e, self) {
		self.noteDragging = false;
		//console.log(" up  elf.noteDragging: " +  self.noteDragging);
		//console.log(" up e.target: " +  e.target);
		
	},
	
	mouseMoveNote: function(e, self) {
		self.updateNotePosition(e);
		
	},
	
	registerNotePosition: function(e) {
		var self = this;
		var contextID = self.element.context.id;
			var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
			var svgRoot = svgEl.root();	
            var p = svgRoot.createSVGPoint();
            p.x = e.clientX;
            p.y = e.clientY;
        
		  
            var m = e.target.getScreenCTM();
            p = p.matrixTransform(m.inverse());
		
		    var x = parseInt(e.target.getAttribute("x"));
			var y = parseInt(e.target.getAttribute("y"));
		
            self.nMouseOffsetX = p.x - x;
            self.nMouseOffsetY = p.y - y;
			
			//console.log(" self.nMouseOffsetX: " +  self.nMouseOffsetX);
			//console.log(" self.nMouseOffsetY: " +  self.nMouseOffsetY);
	},
	
	updateNotePosition: function(e) {
		var self = this;
		var contextID = self.element.context.id;
		var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
		var svgRoot = svgEl.root();	
        var p = svgRoot.createSVGPoint();
        p.x = e.clientX;
        p.y = e.clientY;
		
        
        if(self.noteDragging) {
			var svgWrap =  $("#svgwrap_"+contextID);
		
			$('.noteMarker_' + contextID).css({
				'display':'',
				'top': e.clientY - parseFloat(svgWrap.css('top')) - parseFloat(noteMarker.css('height'))/2,
				'left': e.clientX - parseFloat(svgWrap.css('left')) - parseFloat(noteMarker.css('width'))/2
			});	
			
           var m = e.target.getScreenCTM();
           p = p.matrixTransform(m.inverse());

           e.target.setAttribute("x", p.x );
           e.target.setAttribute("y", p.y );
               
        }
		
		
		
		//console.log(" down self.noteDragging: " +  self.noteDragging);
		//console.log(" updateNote e.target: " +  e.target);
	  
	},
	
	initNoteDrag: function (note) {
        var self = this;
        if(note) {
			if (window.addEventListener) {
            	note.addEventListener("mousedown", function(e) {self.mouseDownNote(e, self)}, false);
            	note.addEventListener("mouseup", function(e) {self.mouseUpNote(e, self)}, false);
            	note.addEventListener("mousemove", function(e) {self.mouseMoveNote(e, self)}, false);
			} else if (typeof window.attachEvent != 'undefined') {
				note.attachEvent("mousedown", function(e) {self.mouseDownNote(e, self)}, false);
            	note.attachEvent("mouseup", function(e) {self.mouseUpNote(e, self)}, false);
            	note.attachEvent("mousemove", function(e) {self.mouseMoveNote(e, self)}, false);
			}
        }
       
    },
	
	
	InitPanAndZoom: function(svgId, noWheel) {
		var self = this;
		var el = self.element;
		var contextID = el.context.id;
		//console.log("svgId: " + svgId);
		var svgElement = document.getElementById(svgId);
		if (!svgElement) {
			window.alert("Cannot getElementById(\"" + svgId + "\")");
			return;
		}
		
		var svgWidth = self.GetLengthValue(svgElement.getAttribute("width"));
		//console.log("svgWidth: " + svgWidth);
		

		var svgHeight = self.GetLengthValue(svgElement.getAttribute("height"));
		//console.log("svgHeight: " + svgHeight);
		
		var svgScaleElement = document.getElementById("svgscale_" + contextID);
		//console.log("svgScaleElement: " + svgScaleElement);
		
		var t = svgScaleElement.getAttribute("transform");
		if (t != null)
			self.svgScale = parseFloat(t.match(/scale\(\s*(\d+\.\d*|\d*\.\d+|\d+)/)[1]); // grab the first (or the one) scale value
		else
			self.svgScale = 1.0;
			
		//console.log("self.svgScale: " + self.svgScale);
		
		
		var vbw = svgWidth / self.svgScale;
    	var vbh = svgHeight / self.svgScale;
    	var svgViewBox = svgElement.getAttribute("viewBox");
    	
		if (svgViewBox != null) {
        	var vbm = svgViewBox.match(/0\s+0\s+([\+\-\.\d]+)\s+([\+\-\.\d]+)/);
        	if (vbm != null) {
            	vbw = parseFloat(vbm[1]);
            	vbh = parseFloat(vbm[2]);
        	}
    	}
		
		//console.log("vbw: " + vbw);
		//console.log("vbh: " + vbh);
		
		svgElement.setAttribute("viewBox", "0 0 " + vbw.toString() + " " + vbh.toString());
    	svgScaleElement.setAttribute("transform", "scale(1.0)");
    	self.svgScale = 1.0;
		
		
		self.wrapWrapWidth = document.getElementById("svgwrapwrap_" + contextID).offsetWidth;
	    self.wrapWrapHeight = document.getElementById("svgwrapwrap_" + contextID).offsetHeight;
		
		//console.log("self.wrapWrapWidth: " + self.wrapWrapWidth);
		//console.log("self.wrapWrapHeight: " + self.wrapWrapHeight);
		
		var svgScaleToFill = Math.min(self.wrapWrapWidth / svgWidth, self.wrapWrapHeight / svgHeight);

		self.originalWrapWidth = Math.round(svgWidth * svgScaleToFill + 0.5);
		self.originalWrapHeight = Math.round(svgHeight * svgScaleToFill + 0.5);
		
		//console.log("self.originalWrapWidth: " + self.originalWrapWidth);
		//console.log("self.originalWrapHeight: " + self.originalWrapHeight);
		
		svgElement.setAttribute("width", self.originalWrapWidth.toString() + "px");
		svgElement.setAttribute("height", self.originalWrapHeight.toString() + "px");
		
		document.getElementById("svgwrap_" + contextID).style.width = svgElement.getAttribute("width");
		document.getElementById("svgwrap_" + contextID).style.height = svgElement.getAttribute("height");
		
		self.svgScale *= self.svgScaleToFill;
		
		//svgScaleElement.setAttribute("transform", "scale(" + self.svgScale.toString() + ")");
		//	initialize the drag object as code in ApplyScale is going to modify some stuff
		if(!self.listenersInitialized) {
		Drag.init(document.getElementById("svgwrap_" + contextID));
		}
		
		self.ApplyScale(1.0);
		
		document.getElementById("svgwrap_" + contextID).style.visibility = "visible";
		
		var svgwrapwrap = document.getElementById("svgwrapwrap_" + contextID);
		
		if(!self.listenersInitialized) {
			self.listenersInitialized = true;
		if (window.addEventListener) {
	        console.log('here');
			svgwrapwrap.mousewheel = null;
			svgwrapwrap.DOMMouseScroll = null;
	        svgwrapwrap.addEventListener("mousewheel", function(e) {self.ZoomWheel(e, self);}, false);
	        svgwrapwrap.addEventListener("DOMMouseScroll", function(e) {self.ZoomWheel(e, self);}, false);    // firefox
			
			svgElement.onclick = null;
			svgElement.removeEventListener("click", function(e) {
				console.log("click target: " + e.target.ownerSVGElement.tagName);
				if (e.target.tagName !== "circle") {
				self.addNote(e, self);
				}
			});
			
			svgElement.addEventListener("dblclick", function(e) {
				console.log(self.state);
//				if (e.target.tagName !== "circle") {
					//console.log("self.state: " + self.state);
					if(self.state == "annotation" && self.options.allowEdit) {
						self.addNote(e, self);
						//console.log("addNote: " + e.target.tagName);
					}
//				}
			}, false);
	    } else if (typeof window.attachEvent != 'undefined') {
			svgwrapwrap.onmousewheel = null;
			svgwrapwrap.attachEvent("onmousewheel", function(e) {self.ZoomWheel(e, self)});
			
			svgElement.onclick = null;
			svgElement.attachEvent("ondblclick", function(e) {
				if(self.state == "annotation") {
					self.addNote(e, self)
				}
			});
	    }
		}
	},
	

	
	loadSVGDone: function(svg, error, page) { 
		var self = this;
		var el = self.element;
		var opts = self.options;
		var context = el.context;
		var contextID = el.context.id;
		
		
		
		var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
		
		var svgRoot = jQuery(svgEl.root());	
		
		svgRoot.attr('id', 'thesvg_' + contextID);
		
		
		//NEW
		$(svgRoot.find("g")[0]).attr({'id': 'svgscale_' + contextID, 'transform': 'scale(1.333333,1.333333)'});
		
		//$(svgRoot.find("g")[0]).attr({'id': 'svgscale_' + contextID});
		
		var svgObj = document.getElementById('thesvg_' + contextID);
		var svgWidth = self.GetLengthValue(svgObj.getAttribute("width"));
		
		//	compute the new width and height
		//var neww = self.originalWrapWidth * newScale;
		//var newh = self.originalWrapHeight * newScale;

		self.requestInProcess = false;
	
		if (!error) {
			console.log(svg);
			
			//load annotation for the page
			if(opts.serverOwner == "unifi"){
				self.getAnnotationsForPage(page);
			}
			
			var control_bar = jQuery('#svgControlBar_' + contextID, context);
			
			if (!(control_bar.length > 0)) {
			
				
				var control_bar = jQuery('<div id="svgControlBar_' + contextID + '"></div>');
				
				// page scrolling buttons
				control_bar.append('<div class="pager"><span class="up-button"><span class="iconic arrow_up_alt1 up-arrow"></span></span><input type="text" class="current-page"  value="'+ self.active_page + '"/><span class="spacer">/</span><span class="total-pages-counter">'+ self.num_pages +'</span><span class="down-button"><span class="iconic arrow_down_alt1 down-arrow"></span></span></div>');
				
				// zooming buttons
				control_bar.append('<div class="zoom"><span class="plus-button"><span class="iconic plus_alt"></span></span><span class="minus-button"><span class="iconic minus_alt"></span></span></div>');
				
				if (opts.serverOwner == 'unifi'){
					// search controls
					control_bar.append('<div class="saerch-container"><label for="searchKeyword">search: </label><input class="searchKeyword" name="searchKeyword"></div>');
					
					// note button
					var note = jQuery('<span class="note-button"></span></span>').css(
							{ 'opacity':'0.7',
							 'cursor':'pointer', 
							 'border-color':(opts.borderColor).replace(/0x/, "#"),
							 'border':'1px solid'
							 })
							 .addClass('ui-corner-all');
					
					note.svg();
							
					//note.load('img/comment2.svg');
					
					/*note.load('img/comment2.svg',
    							{addTo: true, 
								onLoad: function(el, err){
									console.log(el);	
								}});*/
					
					 note.load('../img/comment2.svg',function(el, err){
									$($(this).find("path")[0]).attr('fill',(opts.borderColor).replace(/0x/, "#"));
									$($(this).find("svg")[0]).attr('height','18px');
								});
					
					/*noteEl.load('img/comment2.svg', {
						addTo: false, 
        				changeSize: false, 
						onLoad: function(svg, error) {
							console.log(svg);
						}
					});*/
					control_bar.append(note);

				}
				
				var textColor = opts.textColor.replace(/0x/, '#');
				var buttonColor = opts.buttonColor.replace(/0x/, '#');
				var themeColor = opts.themeColor.replace(/0x/, '#');
				var borderColor = opts.borderColor.replace(/0x/, '#');
				
				
				
				var control_bar_ref = control_bar[0];
				var control_bar_style_ref = control_bar_ref.style;
				control_bar_style_ref.position = 'absolute';
				control_bar_style_ref.bottom = 0;
				control_bar_style_ref.width = opts.width + 'px';
				control_bar_style_ref.height = '30px';
				control_bar_style_ref.lineHeight = '30px';
				control_bar_style_ref.backgroundColor = themeColor;
				control_bar_style_ref.color = textColor;
				control_bar_style_ref.fontSize = "12px";
				control_bar_style_ref.zIndex = 10;
		
				
				var wrapper = jQuery('#svgWrapper_' + contextID);
				wrapper.addClass('contentwrapper');
				wrapper_style_ref = wrapper[0].style;
				wrapper_style_ref.maxWidth = parseInt(opts.width) + "px";
				wrapper_style_ref.height = parseInt(opts.height) + "px";
				/*wrapper_style_ref.position = 'absolute';
				wrapper_style_ref.overflowX = 'hidden';
				wrapper_style_ref.overflowY = 'auto';
				wrapper_style_ref.height = parseInt(opts.height - 30) + "px";*/
			
				control_bar.insertAfter(wrapper);
				
				var pageInput = jQuery('.current-page', control_bar);
				
				var pageInput_ref = pageInput[0];
				var pageInput_style_ref = pageInput_ref.style;
				pageInput_style_ref.width = '30px';
				pageInput_style_ref.borderRadius = "4px";
				pageInput_style_ref.margin = "0 4px 0 4px";
				pageInput_style_ref.textAlign = "center";
				pageInput_style_ref.border = "1px solid";
				pageInput_style_ref.color = textColor;
				pageInput_style_ref.borderColor = borderColor;
				pageInput_style_ref.background = '#ffffff';
				pageInput_style_ref.padding = '0';
				
				var pageSearch = jQuery('.searchKeyword', control_bar);
				
				if( pageSearch.length > 0){
					var searchInput_ref = pageSearch[0];
					var searchInput_style_ref = searchInput_ref.style;
					searchInput_style_ref.width = '100px';
					searchInput_style_ref.borderRadius = "4px";
					searchInput_style_ref.margin = "8px 4px 0 4px";
					searchInput_style_ref.border = "1px solid";
					searchInput_style_ref.color = textColor;
					searchInput_style_ref.borderColor = borderColor;
					searchInput_style_ref.background = '#ffffff';
					searchInput_style_ref.padding = '0';
					
					pageSearch.keypress(
						function(e){
							var val = this.value;
								if(e.which == 13){
									self.searchData(
											val,
											0,
											opts.searchResultPerPage
									);
								}
						}
					);
				}
				
				
				
				var upDownButtons = jQuery('.up-button, .down-button', control_bar);
				var minusPlusButtons = jQuery('.minus-button, .plus-button', control_bar);
				
				var upButton_style_ref = upDownButtons[0].style;
				var upDown_style_ref = upDownButtons[1].style;
				var minusButton_style_ref = minusPlusButtons[0].style;
				var plusDown_style_ref = minusPlusButtons[1].style;
				
				/*upButton_style_ref.borderColor = buttonColor;
				upDown_style_ref.borderColor = buttonColor;
				minusButton_style_ref.borderColor = buttonColor;
				plusDown_style_ref.borderColor = buttonColor;*/
				
				
				upButton_style_ref.marginLeft = "4px";
				upDown_style_ref.marginLeft = "4px";
				minusButton_style_ref.marginLeft = "4px";
				plusDown_style_ref.marginLeft = "4px";
				
				
				var upDownArrows = jQuery('.up-arrow, .down-arrow', control_bar);
				var minusPlusSymbols = jQuery('.minus_alt, .plus_alt', control_bar);
				
				var upArrow_style_ref = upDownArrows[0].style;
				var downArrow_style_ref = upDownArrows[1].style;
				var minusSymbol_style_ref = minusPlusSymbols[0].style;
				var plusSymbol_style_ref = minusPlusSymbols[1].style;
				
				upArrow_style_ref.color =  buttonColor;
				downArrow_style_ref.color = buttonColor;
				minusSymbol_style_ref.color =  buttonColor;
				plusSymbol_style_ref.color = buttonColor;
				
				/*var panLeft = jQuery('.pan-left', control_bar);
				
				panLeft.click(function() {
					self.processPan(37);
				})*/
				
				var noteButton = jQuery('.note-button', control_bar);
				if (noteButton.length > 0 ){
					
					var noteButton_style_ref = noteButton[0].style;
					noteButton_style_ref.color = buttonColor;
					noteButton_style_ref.display = "block";
					noteButton_style_ref.float = "right";
					noteButton_style_ref.marginRight = "5px";
					noteButton_style_ref.marginTop = "5px";
					self.activateNote(noteButton);


				}
				
				self.activatePager(pageInput, upDownButtons);

				self.activateZoom(minusPlusButtons);

				
				
				/*wrapper.jScrollPane(
					{
						showArrows: false,
						horizontalGutter: 5
					}
				);*/
				
				
				var bColor = opts.borderColor.replace(/0x/, '#');
				var rgbVal = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ', 0.3)';
				var rgbValTrack = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ')';
				jQuery('.jspHorizontalBar, .jspVerticalBar, .jspTrack', wrapper).css({'background': rgbValTrack});
		
				jQuery('.jspTrack .jspActive, .jspTrack .jspHover, .jspDrag, .jspDrag:hover', wrapper).css({'background': rgbVal});
			
			} else {
				self.active_page = page;
				jQuery('.current-page', context).val(page);
				
			}
			var id = '#item-' + parseInt(self.active_page - 1);
			var thumbItem = jQuery(id, jQuery(context).parent())
			self.selectThumb(thumbItem);
			
			
			self.InitPanAndZoom('thesvg_' + contextID);
			
			self.ApplyScale(self.currentScale);
			
			/*var svgwrapwrap = jQuery('#svgwrapwrap_'+ contextID);
			var svgwrapwrap_style_ref = svgwrapwrap[0].style;
			svgwrapwrap_style_ref.height = jQuery('#svgWrapper_' + contextID).height() + "px";*/
		
			
			
		}
	},
	
	
	
	activatePager: function(input, buttons) {
		var self = this;
		var context = self.element.context;
		
		
		input.keypress(function(e) {
    		if(e.keyCode == 13) {
        		//alert('You pressed enter!');
				
				var value = this.value;
				self.loadSVGPage(this.value);
    		}
		});
		
		buttons.click(function(e) {
			var direction = (e.target.className.indexOf("up-arrow") !== -1) ? "up" : "down";
			var currentPage = parseInt(jQuery('.current-page', context).val());
			var value;
			switch(direction) {
				case "up" :
					value = currentPage - 1;
				break;
				case "down" :
				    value = currentPage + 1;
				break;
			}
			
			if (value < 1) value = 1;
			if (value > self.num_pages) value = self.num_pages;
			
			self.loadSVGPage(value);
			
		});
		
		
		
	},
	
	activateZoom: function(buttons) {
		var self = this;
		var opts = self.options;
		var context = self.element.context;
		var contextID = context.id;
		
		
		buttons.click(function(e) {
			
			
			var zoom = (e.target.className.indexOf("plus") !== -1) ? "plus" : "minus";
			
			
			
			switch(zoom) {
				case "plus" :
					//self.scale += 0.2;
					self.ZoomIt(1.2)
					
				break;
				case "minus" :
				    //alert("minus");
					//self.scale -= 0.2;
					self.ZoomIt(0.833333333)
					
				break;
			}
			
			
		});	
	},
	
	activateZoomOld: function(buttons) {
		var self = this;
		var opts = self.options;
		var context = self.element.context;
		var contextID = context.id;
		
		
		buttons.click(function(e) {
			var zoomRate = 1.1;
			
			var zoom = (e.target.className.indexOf("plus") !== -1) ? "plus" : "minus";
			
			var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
			var svgRoot = jQuery(svgEl.root());	
			var gWrapper = jQuery('#surface0', svgRoot);
			
			var viewBox = svgRoot[0].getAttribute('viewBox'); // Grab the object representing the SVG element's viewBox attribute.
			// console.log(viewBox);
			
			var viewBoxValues = viewBox.split(' '); // Create an array and insert each individual view box attribute value (assume they're seperated by a single whitespace character).
			viewBoxValues[2] = parseFloat(viewBoxValues[2]); // Convert string "numeric" values to actual numeric values.
			viewBoxValues[3] = parseFloat(viewBoxValues[3]); 
			
			var tempViewBoxWidth = viewBoxValues[2];
			var tempViewBoxHeight = viewBoxValues[3];
			
			switch(zoom) {
				case "plus" :
					//self.scale += 0.2;
					
					viewBoxValues[2] /= zoomRate; 
					viewBoxValues[3] /= zoomRate; 
				break;
				case "minus" :
				    //alert("minus");
					//self.scale -= 0.2;
					viewBoxValues[2] *= zoomRate; // Increase the width and height attributes of the viewBox attribute to zoom out.
					viewBoxValues[3] *= zoomRate; 
				break;
			}
			
			

		viewBoxValues[0] -= (viewBoxValues[2] - tempViewBoxWidth) / 2;
		viewBoxValues[1] -= (viewBoxValues[3] - tempViewBoxHeight) / 2;
			
			
			svgRoot[0].setAttribute('viewBox', viewBoxValues.join(' ')); // Convert the viewBoxValues array into a string with a white space character between the given values.
			
		});	
	},
	
	
	activateNote: function(button) {
		var self = this;
		var opts = self.options;
		var context = self.element.context;
		var contextID = context.id;
		var noteSidebar = $('#notesidebar_' + contextID, context);

		button.click(function(){
			if(button.css("opacity")=="1"){
				button.css({'opacity':'0.7' , 'background-color':'transparent'});
    			//jQuery('.iconic', jQuery(this))[0].style.color = opts.buttonColor.replace(/0x/, '#');
				self.state = "normal";
				
				// hide annotations
				noteSidebar.hide();
				$('.noteMarker').hide();
			} else {
                button.css({'opacity':'1', 'background-color':(opts.buttonColor).replace(/0x/, "#")});
    			self.state = "annotation";
				//$('#svgwrap_'+contextID).css({'cursor':'url(img/comment.png)'});
				
				// show annotations
				noteSidebar.show();
				$('.noteMarker').show();
			}
		});
		
		},
	
	
	activatePan: function(buttons) {},
	
	processPan: function(code) {
		
		var self = this;
		var opts = self.options;
		var context = self.element.context;
		var contextID = context.id;
		
		var leftArrow = 37; // The numeric code for the left arrow key.
		var upArrow = 38;
		var rightArrow = 39;
		var downArrow = 40;
		var panRate = 10;
		
		var svgEl = jQuery('#svgWrapper_' + contextID).svg('get');
		var svgRoot = jQuery(svgEl.root());	
		
		var viewBox = svgRoot[0].getAttribute('viewBox'); // Grab the object representing the SVG element's viewBox attribute.
		var viewBoxValues = viewBox.split(' '); // Create an array and insert each individual view box attribute value (assume they're seperated by a single whitespace character).
		viewBoxValues[0] = parseFloat(viewBoxValues[0]); // Convert string "numeric" values to actual numeric values.
		viewBoxValues[1] = parseFloat(viewBoxValues[1]);
			
		switch (code) {
			case leftArrow:
				viewBoxValues[0] += panRate; // Increase the x-coordinate value of the viewBox attribute to pan right.
			break;
			case rightArrow:
				viewBoxValues[0] -= panRate; // Decrease the x-coordinate value of the viewBox attribute to pan left.
			break;
			case upArrow:
				viewBoxValues[1] += panRate; // Increase the y-coordinate value of the viewBox attribute to pan down.
			break;
			case downArrow:
				viewBoxValues[1] -= panRate; // Decrease the y-coordinate value of the viewBox attribute to pan up.
			break;
			} // switch
			svgRoot[0].setAttribute('viewBox', viewBoxValues.join(' ')); // Convert the viewBoxValues array into a string with a white space character between the given values.
	},
	
	getDocumentInfo: function() {
		var self = this;
		var opts = self.options;
		var sessionId = opts.sessionId;
		var sourceId = opts.sourceId;
		var serverOwner = opts.serverOwner;
		
		im3iSoapCalls.getSourceId(
			sessionId, 
			sourceId, 
			serverOwner, 
			function(data){self.parseDocumentInfo(data)}, 
			function(data){self.errorDocumentInfo(data)}
		);
	},
	

	
	parseDocumentInfo: function(data) {
//		 console.log(data);
		var self = this;
		var opts = self.options;

		var dataObj = $(data);
		
		if (opts.serverOwner == "unifi"){
			self.doc_url = dataObj.find('rtmp_url').text();
			self.num_pages = dataObj.find('document_num_pages').text();
			self.page_base_url = dataObj.find('page_base_url').text();
			self.page_extension = dataObj.find('page_extension').text();
			self.thumb_base_url = dataObj.find('thumb_base_url').text();
			self.thumb_extension = dataObj.find('thumb_extension').text();
			self.doc_ratio = dataObj.find('doc_ratio').text();
		
		}else{
			var artifactUrl = dataObj.find('artifactUrl').text();
			var rawData = $( dataObj.find('rawData').text() );
			var pagesInfo = $(rawData.find('pages'));
			var thumbInfo = $(rawData.find('thumbs'));
			
			self.num_pages = rawData.find('pagecount').text();
			
			self.page_base_url = artifactUrl +"/"+ pagesInfo.find('subdirectory').text() + "/page_";
			self.page_extension = ".svg";//pagesInfo.find('extension').text();
			
			self.thumb_base_url = artifactUrl +"/"+ thumbInfo.find('subdirectory').text() + "/page_";
			self.thumb_extension = '.jpg';
			
			self.doc_ratio = pagesInfo.find('aspectratio').text();
					
		}
		
		
		
		self.createWrapper();
		self.thumbsScroller();
		
		
	},

	
	errorDocumentInfo: function(data) {
	
	},
	
	
	updateWidget : function(param, type) {
		var self = this;
		var opts = self.options;
		
		this._destroy();
		this._create();
		
	},
	
	
	//SEARCH
	
	searchData: function( keyword, page, recPerPage) {
		var self = this;
    	var element = self.element;
		var opts = self.options;
		var context = element.context;
		var sessionId = opts.sessionId;
		var source = opts.sourceId;
		var soapServer = opts.serverOwner;
        
		var resultsContainer = jQuery('.resultsContainer', context);
		if(resultsContainer.length > 0 && !self.paginationActive) {
			resultsContainer.hide();
		}
		
		var searchTerm = $('.searchKeyword', context).val();
	
		solr.searchDocument(
		opts.sourceId,
		searchTerm,
		page,
		recPerPage,
			function(data){
			console.log(data);
				self.searchDataResults(data);
			}, 
			function(data){
				self.searchDataError(data)
			}
		);
	},
	
	
	searchDataResults: function(data) {
		//console.log(data);
		var self = this;
		var element = self.element;
		var opts = self.options;
		var context = element.context;
		
		var width = opts.width;
		var height = opts.height;
		var textColor = (opts.textColor).replace(/0x/, "#");
		var themeColor = (opts.themeColor).replace(/0x/, "#");
		var resultsPerPage = opts.searchResultPerPage;
		
		var resultsContainer = jQuery('.resultsContainer', context);
		
		if(resultsContainer.length == 0) {
			var resultsContainer = jQuery('<div></div>').attr('class','resultsContainer');
			
			resultsContainer.css({
					'width': width-10 + "px", 
					'height': Number(height) - (Number(height) / 2) + "px", 
					'color': textColor,
					'opacity': 0.95,
					'top' : Number(height) / 2,
					'display': 'none'
				})
				.addClass('ui-corner-all');
			
			var textColor = (opts.textColor).replace(/0x/, "#");
		    var themeColor = (opts.themeColor).replace(/0x/, "#");
			
			resultsContainer.appendTo("#svgwrapwrap_" + context.id);
			
			var closeBtn = $('<span class="delete-icon"><span class="iconic x"></span></span>');
			closeBtn
				.css({'background-color': themeColor, 'opacity':0.8})
				.css({'color': textColor, 'opacity':0.8})
				.addClass('ui-corner-all')
				.hover(
						function () {
							$(this).css({'opacity':1 });
						}, 
						function () {
							$(this).css({'opacity':0.8 });
						}
					)
				.click(
					function(event){
						resultsContainer.hide();
				});
			
			resultsContainer.append(closeBtn);
			
			
		
			
			
			var title = $("<span class='searchTerm'></span>").css({'color': themeColor});
			resultsContainer.append(title);
			title.css({'fontWeight': 'bold'});
			
			var resultList = $('<div class="paginateResults"><ul class="resList"></ul></div>')
			resultsContainer.append(resultList);
			
			
			var nav = $("<div class='resultNav'></div>");
		    nav.insertAfter(title);
			nav.css({'position' : 'absolute', 'right' : '30px', 'top': '2px', 'width': '75px', 'fontWeight': 'bold'});

		}
		
		resultsContainer.find(".resList").empty();
		//var results = jQuery.parseJSON(data);
		var results = data.response;
		var term = data.responseHeader.params.q;
		self.searchedTerm = term;
		$('.resultNav', context).empty();
		var totalPag = data.response.numFound;
		
		if (totalPag > opts.searchResultPerPage) {
			/* PAGINATION */
			var pag = parseInt(data.responseHeader.params.start);
			self.paginationActive = true;
			
		
			if (pag > 0 ){
				var prev = $('<span>Prev</span>').css({'color': (opts.themeColor).replace(/0x/, "#"), 'float':'left', 'cursor':'pointer', 'marginRight': 3});	
				prev.click( function(ev) {
					
					self.searchData(term, pag-1, opts.searchResultPerPage);
				});
				$('.resultNav', context).append(prev);
			}
			if (pag < (totalPag -1) ){
				var next = $('<span>Next</span>').css({'color': (opts.themeColor).replace(/0x/, "#"), 'float':'right','cursor':'pointer', 'marginLeft': 3});	
				next.click( function(ev) {
					self.searchData(term, pag+1, opts.searchResultPerPage);
				});
				
				$('.resultNav', context).append(next);
			}
			
			/* END PAGINATION */
		}
		
		
		if(results.docs.length > 0) {
			
			
				$(".searchTerm",resultsContainer).html("Results for '"+term+"'");
				
				
				
				/*$.each(results.docs, function(i, result) {
					self.createResultItem(i, result, term);
				});*/
				
				
				$.each(data.highlighting, function(key, value) {
					//console.log("puppa: " + key + ': ' + value);
					if (value) {
						var resultItem = utils.getObjects(results.docs, "id", key);
						self.createResultObj(key, value, term, resultItem);
					}
				});
				
				
			} else {
				$(".searchTerm",resultsContainer).html("No results found for <span>" + term + "</span>");
		}
		
			
		
		$('.tipsy').remove();
			
		resultsContainer.show();
		
		
		
	},
	
	searchDataError: function(data) {
		//console.log(data);
	},
	
	createResultItem: function(i, resultItem, term) {
		
		var self =this;
		var element = self.element;
		var opts = self.options;
		var context = element.context;
		
		
		
		
		var page = resultItem.page;
		var text = resultItem.text_extracted;
		var score = resultItem.score;
		var results = jQuery.splitText(term, text, false);
		
		
		
		
		//self.findSearchTerm(term, text)
	    for (var i = 0; i < results.length; i ++ ) {
		    if (i == 0) {
				resultObj = $('<li></li>');
				
				var pageItem = $("<span></span>");
				pageItem.css({
						'background-color': (opts.highlightBg).replace(/0x/, '#'), 
						'color': (opts.highlightThumb).replace(/0x/, '#'),
						'fontSize': "90%",
						"margin": '2px 5px 0 0',
						'padding': '1px 2px 1px 2px'
						})
					.addClass('ui-corner-all')
					.text("p. " + page);
					
				var breakElement = $("<br />");
				var recordScore = $("<span></span>");
				recordScore.css({
						'background-color': (opts.themeColor).replace(/0x/, '#'), 
						'color': (opts.borderColor).replace(/0x/, '#'),
						'fontSize': "80%",
						"margin": '2px 5px 0 0',
						'padding': '1px 2px 1px 2px'
						})
					.addClass('ui-corner-all')
					.text("score: " + score);
		        
				var toggleButton = $("<span></span>");
				
			
				resultObj
					.html("[...] " + results[i] + " [...]" )
					.css({'color': (opts.textColor).replace(/0x/, '#')})
					.attr('data-page', 'page_' + page)
					.attr('data-title', text)
					.css({'background-color': (opts.themeColor).replace(/0x/, '#'), 'opacity': 0.8})
					.addClass('ui-corner-all')
					.hover(
								function () {
									$(this).css({'opacity':'1' });
									
								}, 
								function () {
									$(this).css({'opacity':'0.8' });
								}
							)
							
					.click(function(event){
						var className = $(this).attr('data-page');
						var dataArray = className.split('_');
						var pageNumber = parseInt(dataArray[1]);
						//console.log(pageNumber);
						$(".resultsContainer", context).hide();
						$(this).tipsy("hide");
						$('.preloader').show();
						self.loadSVGPage(pageNumber);
					})
					.tipsy({gravity: 's', html: true, title: function() {
						 var pageNArray = this.getAttribute('data-page').split('_');
						 var pageN = pageNArray[1];
						 var html = '<img style="float:left; margin-right:10px;" src="' + self.thumb_base_url + (parseInt(pageN) - 1) + self.thumb_extension + '">' 
						 			+ jQuery.subText(self.searchedTerm, this.getAttribute('data-title')) + '<br style="clear:both;" />';
						 return  html;
						 }, 'maxWidth' : ($(".resultsContainer", context).width() - $(".resultsContainer", context).width() / 4) + "px",
						 'bg': 'rgba(0, 0, 0, .8)',
						 'color': '#FFF'
					})
					.prepend(pageItem)
					.append(breakElement)
					.append(recordScore)
					.highlight(term).find('span').css({'fontWeight': 'bold'});
					
					//$('.tipsy-inner', context).css({'maxWidth': $(".resultsContainer .resList", context).width()})
					
					$(".resultsContainer .resList", context).append(resultObj);
			} else {
				var subItemContainer = $('ul', resultObj);
					
				if(subItemContainer.length == 0) {
					toggleButton.css({
						'background-color': 'rgba(0, 0, 0, 0.6)', 
						'color': '#FFF',
						'fontSize': '80%',
						'margin': '2px 2px 0 0',
						'padding': '1px',
						'float': 'right'
						})
					.addClass('ui-corner-all')
					.text("more results")
					.toggle(
						function(event) {
						 	//console.log("open");
							$(this).parent().find('ul').show();
							$(this).css({'opacity': 1});
						 	event.stopPropagation();
						},
						function(event) {
						 	//console.log("close");
							$(this).parent().find('ul').hide();
							$(this).css({'opacity': 0.8});
						 	event.stopPropagation();
						}
						
					)
					
					
					subItemContainer = $('<ul style="display:none;"></ul>');
					resultObj
					.append(toggleButton)
					.append(subItemContainer)
				}
				var subItem = $('<li></li>')
					
				subItem.html("[...] " + results[i] + " [...]").highlight(term).find('span').css({'fontWeight': 'bold'});
				subItemContainer.append(subItem);
			}
		}
	},
	
	createResultObj: function(key, value, term, resultItem) {
		var self =this;
		var element = self.element;
		var opts = self.options;
		var context = element.context;

		
		/*var page = resultItem.page;
		var text = resultItem.text_extracted;
		var score = resultItem.score;
		var results = jQuery.splitText(term, text, false);*/
		
		
		
		var resultItem = resultItem[0];
		var page = resultItem.page;
		var docText = resultItem.text_extracted;
		var score = resultItem.score;
		var results = value.text_extracted;
		
		
		console.log("page: " + page);
//		console.log("docText: " + docText);
		console.log("score: " + score);
		
		for (var i = 0; i < results.length; i ++ ) {
			  if (i == 0) {
				resultObj = $('<li></li>');
				
				var pageItem = $("<span></span>");
				pageItem.css({
						'background-color': (opts.highlightBg).replace(/0x/, '#'), 
						'color': (opts.highlightThumb).replace(/0x/, '#'),
						'fontSize': "90%",
						"margin": '2px 5px 0 0',
						'padding': '1px 2px 1px 2px'
						})
					.addClass('ui-corner-all')
					.text("p. " + page);
					
				var breakElement = $("<br />");
				var recordScore = $("<span></span>");
				recordScore.css({
						'background-color': (opts.themeColor).replace(/0x/, '#'), 
						'color': (opts.borderColor).replace(/0x/, '#'),
						'fontSize': "80%",
						"margin": '2px 5px 0 0',
						'padding': '1px 2px 1px 2px'
						})
					.addClass('ui-corner-all')
					.text("score: " + score);
		        
				var toggleButton = $("<span></span>");
				
			
				resultObj
					.html("[...] " + results[i] + " [...]" )
					.css({'color': (opts.textColor).replace(/0x/, '#')})
					.attr('data-page', 'page_' + page)
					.attr('data-title', docText)
					.css({'background-color': (opts.themeColor).replace(/0x/, '#'), 'opacity': 0.8})
					.addClass('ui-corner-all')
					.hover(
								function () {
									$(this).css({'opacity':'1' });
									
								}, 
								function () {
									$(this).css({'opacity':'0.8' });
								}
							)
							
					.click(function(event){
						var className = $(this).attr('data-page');
						var dataArray = className.split('_');
						var pageNumber = parseInt(dataArray[1]);
						//console.log(pageNumber);
						$(".resultsContainer", context).hide();
						$(this).tipsy("hide");
						self.loadSVGPage(pageNumber);
					})
					.tipsy({gravity: 's', html: true, title: function() {
						 var pageNArray = this.getAttribute('data-page').split('_');
						 var pageN = pageNArray[1];
						 var html = '<img style="float:left; margin-right:10px; max-width:80px;" src="' + self.thumb_base_url + (parseInt(pageN) - 1) + self.thumb_extension + '">' 
						 			+ jQuery.subText(self.searchedTerm, this.getAttribute('data-title')) + '<br style="clear:both;" />';
						 return  html;
						 }, 'maxWidth' : ($(".resultsContainer", context).width() - $(".resultsContainer", context).width() / 4) + "px",
						 'bg': 'rgba(0, 0, 0, .8)',
						 'color': '#FFF'
					})
					.prepend(pageItem)
					.append(breakElement)
					.append(recordScore)
					.highlight(term).find('em').css({'fontWeight': 'bold'});
					
					//$('.tipsy-inner', context).css({'maxWidth': $(".resultsContainer .resList", context).width()})
					
					$(".resultsContainer .resList", context).append(resultObj);
			} else {
				var subItemContainer = $('ul', resultObj);
					
				if(subItemContainer.length == 0) {
					toggleButton.css({
						'background-color': 'rgba(0, 0, 0, 0.6)', 
						'color': '#FFF',
						'fontSize': '80%',
						'margin': '2px 2px 0 0',
						'padding': '1px',
						'float': 'right'
						})
					.addClass('ui-corner-all')
					.text("more results")
					.toggle(
						function(event) {
						 	//console.log("open");
							$(this).parent().find('ul').show();
							$(this).css({'opacity': 1});
//						 	event.stopPropagation();
						},
						function(event) {
						 	//console.log("close");
							$(this).parent().find('ul').hide();
							$(this).css({'opacity': 0.8});
//						 	event.stopPropagation();
						}
						
					)
					
					
					subItemContainer = $('<ul style="display:none;"></ul>');
					resultObj
					.append(toggleButton)
					.append(subItemContainer)
				}
				var subItem = $('<li></li>')
					
				subItem.html("[...] " + results[i] + " [...]").highlight(term).find('em').css({'fontWeight': 'bold'});
				subItemContainer.append(subItem);
			}
		}
	},
	
	findSearchTerm: function(searchTerm, text) {
		var reg = "/" + searchTerm + "/i";
		var index = text.search(reg);
		//console.log("search start index: " + index);
	},
	
	
	// END SEARCH
	
	// ANNOTATION HANDLING
	
	getAnnotationsForPage: function( page ){
		var self = this;
		var opts = self.options;
		var sessionId = opts.sessionId;
		var sourceId = opts.sourceId;
		var serverOwner = opts.serverOwner;
		var queryType = opts.annotationsQueryType;
		
		im3iSoapCalls.getAnnotationDocuments(
			sourceId, 
			sessionId, 
			serverOwner,
			page, 
			queryType,
			function(data){
				console.log(data);
				self.parseAnnotations(data)}, 
			function(data){self.errorAnnotation(data)}
		);
	},
	
	parseAnnotations: function (data){
		var self = this;
		var opts = self.options;
		var element = self.element;
		var context = element.context;
		var contextID = context.id;
		
				
		var annArray = $(data).find("annotations");
		
		for (var i=0; i<annArray.length; i++){
			var annRef = $(annArray[i]);
			self.createAnnotationItem(annRef);
			
		}
		
		var noteSidebar = $('#notesidebar_' + contextID)
		noteSidebar.jScrollPane(
				{
					showArrows: true,
					horizontalGutter: 10,
					autoReinitialise: true
				}
		);
		
		var bColor = opts.borderColor.replace(/0x/, '#');
		var rgbValTrack = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ')';
		var rgbValB = 'rgba(' + utils.hexToR(bColor) + ', ' + utils.hexToG(bColor) + ', ' + utils.hexToB(bColor) + ', 0.3)';

		jQuery('.jspHorizontalBar, .jspVerticalBar, .jspTrack', noteSidebar).css({'background': rgbValTrack});
		
		jQuery('.jspTrack .jspActive, .jspTrack .jspHover, .jspDrag, .jspDrag:hover', noteSidebar).css({'background': rgbValB});
	},
	
	errorAnnotation: function (data){
		//console.log(data);
	},
	
	createAnnotationItem: function (ann){
		var self = this;
		var element = self.element;
		var context = element.context;
		var contextID = context.id;
		var opts = self.options;

		
		var annId = $(ann).find('id').text();
		var text = $(ann).find('keyword').text();
		
		var x_pos = $(ann).find('box_x').text();
		var y_pos = $(ann).find('box_y').text();
		
		var noteColor="";
		var textColor="";
		var canEdit = false;
		var user_name = "";
		
		$.ajax({
			url : '../php/getUserName.php?user=' + $(ann).find('owner').text(),
			dataType : "html",
			success : function(data) {
			     if( data != "" ) {
			         user_name = data;
			     } else {
			    	 user_name = $(ann).find('owner').text();
			     }
			},
			async: false
		});
		
		var authorDiv = jQuery("<div>by "+ user_name +"</div>").css({
			'border-top':'1px solid',
			'margin-top':'5px',
			'padding-top':'5px',
			'border-color' : opts.themeColor.replace(/0x/, '#'),
			'font-size':'0.7em',
			'font-weight':'bold',
		});
		
		var note = jQuery("<div class='document-annotation' data-annid="+annId+" id='document-annotation-"+contextID+"-"+annId+"'></div>")
				   .css({
						'float':'left',
						'clear':'both',
						'width' : '180px',
						'opacity':'0.8'
				    })	
				  .append('<span class="annotation-text">'+text+'</span>')
				  .append(authorDiv)
				  .addClass('ui-corner-all');
				  
		
		var noteMarker=jQuery('<span id="noteMarker-' + contextID +'-'+annId+'" data-annid='+annId+' class="noteMarker" ></span>');

		if (opts.ownerName == $(ann).find('owner').text()){
			
			note.addClass('annotation-mine');
			noteMarker.addClass('annotation-mine');
			
			noteColor = opts.annotationMineBg.replace(/0x/, '#');
			textColor = opts.annotationMineText.replace(/0x/, '#');
			canEdit = true;
			
			
		}else{
			note.addClass('annotation-all');
			noteMarker.addClass('annotation-all');

			noteColor = opts.annotationAllBg.replace(/0x/, '#');
			textColor = opts.annotationAllText.replace(/0x/, '#');
			canEdit = false;
			
		}
		note.css({'background': noteColor,
					  'color' : textColor
					 });
		// edit on click
		
		$('#notesidebar_container_' + contextID).append(note);
		
		
		var svgWrap= $("#svgwrap_"+contextID);

		noteMarker.css({
			'top': parseFloat(svgWrap.css('height')) *y_pos,
			'left': parseFloat(svgWrap.css('width'))*x_pos,
			'display' : self.state == "annotation" ? 'block' : 'none'
		});
		noteMarker.svg();
		noteMarker.load('../img/comment2.svg',function(el, err){
							
							$($(this).find("path")[0]).attr('fill',noteColor);
						});
	 
	
		svgWrap.append(noteMarker);
		
		if (canEdit){
			
			note.click(function(){
				if ( $(".annotation-text", this).length >0 ){
					
					var txt = $(".annotation-text", this).text();
					var thisNote = this;
					$(".annotation-text", this).remove();
					
					$(this).prepend('<textarea>'+txt+'</textarea>');
					$("textarea",this).focus();
					
					$("textarea",this).blur(function(){
						
						
	
					});
					$("textarea",this).keydown( function(e) {
									if (e.which == '27') { // on ESC
										$(this).remove();
										$(thisNote).prepend('<span class="annotation-text">'+txt+'</span>');
									}
									
									if (e.which == '13') { //on ENTER
										txt = $(this).val();
										$(this).remove();
										$(thisNote).prepend('<span class="annotation-text">'+txt+'</span>');
						
										self.updateAnnotation( $(thisNote).data('annid') );
									}
								});
				}
			});
			
			noteMarker.draggable({ 
				containment: "#svgwrap_"+contextID,
				stop: function(event, ui) { self.updateAnnotation( $(this).data('annid') ); } 
			});
			
			//delete icon
			$('<span class="delete-icon"><span class="iconic x"></span></span>')
						.css({'background-color': noteColor, 'opacity':0.8, 'border-color':textColor, 'color':textColor})
						.addClass('ui-corner-all')
						.hover(
							function () {
								$(this).css({'opacity':1 });
							}, 
							function () {
								$(this).css({'opacity':0.8 });
							}
						)
						.click(function(e){
							e.stopPropagation();
							var id = $(this).parent().data('annid');
							
							im3iSoapCalls.deleteAnnotation( id, opts.sessionId, opts.serverOwner, 
								function(data){
									
									self.deletedAnnotaion(id,data);
								}, 
								function(data){
									//console.log("error deleting:"+data);
								})
						})
						.appendTo(note).hide();
				
													   
				
			note.hover(
				function () {
					$(this).find(".delete-icon").show();
				}, 
				function () {
					$(this).find(".delete-icon").hide();
				}
			);
		}
		
		note.hover(function(){
					self.highlightAnnotation($(this).data('annid'),true);
				}, 
				function (){
					self.highlightAnnotation($(this).data('annid'),false);
				}
		);
		
		noteMarker.hover(function(){
					self.highlightAnnotation($(this).data('annid'),true);
				}, 
				function (){
					self.highlightAnnotation($(this).data('annid'),false);
				}
		);
		
		
		
		
		//if the option is checked, hide annotations from other users
			if($('#note-options-check-'+ contextID).prop("checked") == true && opts.ownerName != $(ann).find('owner').text() ){
				note.hide();
				noteMarker.hide();		
			}
		
		
		
		
	},
	
	highlightAnnotation:function (annId, status){
		var self = this;
		var element = self.element;
		var context = element.context;
		var contextID = context.id;
		
		if (status == true){
			$('#noteMarker-' + contextID +'-'+annId).css({'opacity':1});
			$('#document-annotation-'+contextID+'-'+annId).css({'opacity':1});
		}else{
			$('#noteMarker-' + contextID +'-'+annId).css({'opacity':0.8});
			$('#document-annotation-'+contextID+'-'+annId).css({'opacity':0.8});

		}
		
	},
	
	addAnnotation : function (){
		var self = this;
		var element = self.element;
		var context = element.context;
		var contextID = context.id;
		var opts = self.options;

		
		var agent = "Document annotation component";
		var comment = "Manual annotation from document annotation component";
		var sessionId = opts.sessionId;
		var sourceId = opts.sourceId;
		var soapServer = opts.serverOwner;
		var type = "manual";
		var confidence = 1;
		var page = self.active_page;
		var owner = opts.ownerName;
		
		var svgWrap= $("#svgwrap_"+contextID);
		var marker = $('#noteMarker-new-' + contextID);
		var textArea = $('#annotation-textarea'+contextID);
		
		var box_y = parseFloat( marker.css('top') )/ parseFloat(svgWrap.css('height')); 
		var box_x = parseFloat( marker.css('left') )/ parseFloat(svgWrap.css('width'));

		var documentAnnotation = {'sourceId': sourceId, 'keyword': textArea.val(),'page':page, 'box_x': box_x, 'box_y': box_y, 'owner': opts.ownerName };
		
		var annotations = [];
		annotations.push(documentAnnotation);

		im3iSoapCalls.addAnnotationDocument(agent, comment, sessionId, confidence, soapServer, owner ,type, annotations,
			function(data){
				self.addedAnnotation(data);
			}, 
			function(data){
				self.errorAddingAnnotation(data);
			}
		);
	},
	
	updateAnnotation : function( annId ){
		var self = this;
		var element = self.element;
		var context = element.context;
		var contextID = context.id;
		var opts = self.options;

		
		var agent = "Document annotation component";
		var comment = "Manual annotation from document annotation component";
		var sessionId = opts.sessionId;
		var sourceId = opts.sourceId;
		var soapServer = opts.serverOwner;
		var type = "manual";
		var confidence = 1;
		var page = self.active_page;
		
		var svgWrap= $("#svgwrap_"+contextID);
		
		var marker = $('#noteMarker-' + contextID +'-'+annId);
		
		var annText = $("#document-annotation-"+contextID+"-"+annId+" .annotation-text").text();


		var box_y = parseFloat( marker.css('top') )/ parseFloat(svgWrap.css('height')); 
		var box_x = parseFloat( marker.css('left') )/ parseFloat(svgWrap.css('width'));
		
		var documentAnnotation = {'id':annId, 'sourceId': sourceId, 'keyword':annText, 'page':page, 'box_x': box_x, 'box_y': box_y, 'owner':opts.ownerName };
		
		var annotations = [];
		annotations.push(documentAnnotation);

		im3iSoapCalls.updateAnnotation(agent, comment, sessionId, confidence, soapServer, type, annotations,
			function(data){
				//console.log(data);
			}, 
			function(data){
				//console.log(data);
			}
		);
	},
	
	addedAnnotation : function (data){
		var self = this;
		var element = self.element;
		var context = element.context;
		var contextID = context.id;
		
		//clear input
		$('#noteMarker-new-' + contextID ).remove();
		$('#document-annotation-'+contextID+'-new').remove();
		
		var ann = $(data).find("annotations");
		self.createAnnotationItem(ann);
	},
	
	errorAddingAnnotation : function (data){
		//console.log(data);
	},
	
	deletedAnnotaion: function(annId,data){
		
		var contextID = this.element.context.id;
		
		$('#noteMarker-' + contextID +'-'+annId).fadeOut("slow", function() {
      		$(this).remove();
    	})
		$('#document-annotation-'+contextID+'-'+annId).fadeOut("slow", function() {
      		$(this).remove();
    	})
	},
	
		
	
});


$.extend( $.ui.component, { version: "@VERSION" })

})( jQuery )
