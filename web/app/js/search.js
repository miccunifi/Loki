

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
		console.log(files);
		// We need to send dropped files to Server
		$('#dragandrophandler').fadeOut(0);
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
		beforeSend : function() {
			$("#progress").show();
			$("#bar").width('0%');
			$("#upload-message").html("");
			$("#percent").html("0%");
		},
		uploadProgress : function(event, position, total, percentComplete) {
			$("#bar").width(percentComplete + '%');
			$("#percent").html(percentComplete + '%');

		},
		success : function() {
			$("#bar").width('100%');
			$("#percent").html('100%');

		},
		complete : function(response) {
			window.name = response.responseText;
			$('#upload-message').show();
			$("#upload-message").html("Success!");
			console.log(CFG.absolutePath + "main");
			window.location.replace(CFG.absolutePath + "main");
		},
		error : function() {
			$("#upload-message").html("ERROR!");
			$("#upload-message").addClass('error');
		}
	};
	$("#upload-form").ajaxForm(options);
});

function sendFileToServer(formData,status)
{
    var uploadURL = CFG.absolutePath + "php/uploadImage.php"; //Upload URL
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
            $('#query-file').prop("disabled",true);
            window.name = data;
            window.location.replace('main');
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
function handleFileUpload(files,obj){
	var type = null;
	var text = null;
	for (var i = 0; i < files.length; i++){
        var fd = new FormData();
        fd.append('file', files[i]);
        type = (files[i].type).split('/')[0];
        console.log('Dragged ' + type);
        if(type == 'image'){
            var status = new createStatusbar(obj); //Using this we can set progress.
            status.setFileNameSize(files[i].name,files[i].size);
            $("#progress").show();
            $('#upload-message').show();
            sendFileToServer(fd,status);
        } else {
        	//Search by extracted keywords
        	text = (files[i].name).split('.')[0];
        	text = text.split('-').join(' ');
        	text = text.split('_').join(' ');
        	console.log(text);
        	$('#search-form').val(text);
        	$('#form-search').submit();
        }
   }
}