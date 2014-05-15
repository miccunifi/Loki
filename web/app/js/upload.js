

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



var file = null;
var availableTags = [];

$(document).ready(function() {
	window.name = 'null';

	var obj = $("#dragandrophandler");
	obj.on('dragenter', function(e) {
		e.stopPropagation();
		e.preventDefault();
		$(this).parent().css('border-color', 'rgba(255, 255, 255, 0.5)');
		$(this).parent().css('color', 'rgba(255, 255, 255, 0.5)');
	});
	obj.on('dragover', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});
	obj.on('drop', function(e) {
		$(this).parent().css('border-color', 'rgba(255, 255, 255, 0.5)');
		$(this).parent().css('color', 'rgba(255, 255, 255, 0.5)');
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		getTags(files[0].name);
		console.log(files);
		// We need to send dropped files to Server
//		$('#dragandrophandler').fadeOut(0);
		handleFileUpload(files, obj);
	});
	$(document).on('dragenter', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});
	$(document).on('dragover', function(e) {
		e.stopPropagation();
		e.preventDefault();
		$(this).parent().css('border-color', 'rgba(255, 255, 255, 0.5)');
		$(this).parent().css('color', 'rgba(255, 255, 255, 0.5)');
	});
	$(document).on('drop', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});

	var options = {
		beforeSerialize: function($form, options) { 
			if(file != null){
				$('#dragandrophandler').hide();
				$("#progress").show();
				$("#bar").width('0%');
				$("#upload-message").html("");
				$("#percent").html("0%");
				$('#bar').removeClass('bar-error');
				$("#upload-message").removeClass('error');
		        var fd = new FormData();
		        fd.append('myfile', file);
		        fd.append('title', $('#title-input').val());
		        fd.append('author', $('#author-input').val());
		        fd.append('tags', $('#tags-input').val());
		        var status = new createStatusbar(obj); //Using this we can set progress.
		        status.setFileNameSize(file.name,file.size);
		        $('#dragandrophandler').hide();
		        $("#progress").show();
		        $('#upload-message').show();
		        sendFileToServer(fd,status);
				return false;
			}                
		},
		beforeSend : function(e) {
			$('#dragandrophandler').hide();
			$("#progress").show();
			$("#bar").width('0%');
			$("#upload-message").html("");
			$("#percent").html("0%");
			$('#bar').removeClass('bar-error');
			$("#upload-message").removeClass('error');
		},
		uploadProgress : function(event, position, total, percentComplete) {
			$("#bar").width(percentComplete + '%');
			$("#percent").html(percentComplete + '%');
			
			if(percentComplete == 100){
				$('#upload-message').show();
				$("#upload-message").html("<img src='../img/loader.gif' class='loading' alt='loading'>");
				$('#upload-message').append("<span class='loading-msg>'>Encoding...</span>");
			}

		},
		success : function() {
			$("#bar").width('100%');
			$("#percent").html('100%');
		},
		complete : function(response) {
			console.log(response.responseText);
			$('#upload-message').show();
			if(response.responseText == 'Success'){
				$("#upload-message").html("Success!<br><a href='" + CFG.absolutePath + "user-collection/'>Go to My Collection</a>");
				//index();
				$('input').val('');
				$('.search-button').val('Upload');
//				window.location.replace(CFG.absolutePath + "user-collection/");;
			} else {
				$("#bar").width('100%');
				$('#bar').addClass('bar-error');
				$("#percent").html('100%');
				$("#upload-message").html(response.responseText);
				$("#upload-message").addClass('error');
			}
		},
		error : function() {
			$("#upload-message").html("ERROR!");
			$("#upload-message").addClass('error');
		}
	};
	$("#upload-form").ajaxForm(options);
	                   
	function split( val ) {
		return val.split( /,\s*/ );
	}
	
	function extractLast( term ) {
		return split( term ).pop();
	}
	                   
	$( "#tags-input" )
    // don't navigate away from the field on tab when selecting an item
    .bind( "keydown", function( event ) {
      if ( event.keyCode === $.ui.keyCode.TAB &&
          $( this ).data( "ui-autocomplete" ).menu.active ) {
        event.preventDefault();
      }
    })
    .autocomplete({
      minLength: 0,
      source: function( request, response ) {
        // delegate back to autocomplete, but extract the last term
        response( $.ui.autocomplete.filter(
          availableTags, extractLast( request.term ) ) );
      },
      focus: function() {
        // prevent value inserted on focus
        return false;
      },
      select: function( event, ui ) {
        var terms = split( this.value );
        // remove the current input
        terms.pop();
        // add the selected item
        terms.push( ui.item.value );
        // add placeholder to get the comma-and-space at the end
        terms.push( "" );
        this.value = terms.join( ", " );
        return false;
      }
    });
	
	$('#query-file').on('change', function(){
		//Adding tags
		if($(this).val().split("\\") != undefined){
			getTags($(this).val());
		}
	});
});

function getTags(string){
	var filename = string.split("\\");
	filename = filename[filename.length - 1];
	filename = filename.split('.');
	var title = '';
	for(var k = 0; k < filename.length - 1 ; k++){
		var temp = filename[k].toLowerCase();		
		var tags = temp.split(/[\s,_-]+/);
		for(var i = 0; i < tags.length; i++){
			availableTags.push(tags[i]);
			title += tags[i] + ' ';
		}
		console.log(availableTags);
		console.log(title);
	}
}

function sendFileToServer(formData,status)
{
    var uploadURL = CFG.absolutePath + "php/uploadMedia.php"; //Upload URL
    var extraData = {}; //Extra Data.
    var jqXHR = $.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        
            			if(percent == 100){
            				$('#upload-message').show();
            				$("#upload-message").html("<img src='../img/loader.gif' class='loading' alt='loading'>");
            				$('#upload-message').append("<span class='loading-msg>'>Encoding...</span>");
            			}
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
    url: uploadURL,
    type: "POST",
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
            status.setProgress(100);
            $("#upload-message").html("Success!<br><a href='" + CFG.absolutePath + "user-collection/'>Go to My Collection</a>");
            $('input').val('');
            $('.search-button').val('Upload');
            //index();
            $('#query-file').show();
         }
    }); 
}

function createStatusbar(obj)
{
     this.statusbar = $("#upload-message");
     this.progressBar = $("#progress");
 
    this.setFileNameSize = function(name,size)
    {
    	$("#upload-message").html(name);
    };
    this.setProgress = function(progress)
    {       
        $('#bar').css('width',progress + '%');
        $('#percent').html(progress + "% ");
    };
}
function handleFileUpload(files,obj)
{
   for (var i = 0; i < files.length; i++) 
   {
	   file = files[i];
        $('#query-file').hide();
        console.log(files[i]);
        $('#drag-text').html("<i>File selected</i><br><br>" + files[i].name);
   }
}

function index() {
	var url = CFG.solrProxyUrl + "?proxy_url=";
	$.ajax({
		url : url + CFG.solrCoreUrl + encodeURIComponent('dataimport?command=full-import&clean=false'),
		method : "GET",
		dataType : "html",
		success : function(data) {
			console.log(data);
		},
		async: false,
		error : function(err) {
			console.log("Error: " + err);
		}
	});
}