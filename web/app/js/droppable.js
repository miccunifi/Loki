$(document).ready(function() {
	
	var obj = $(".dragged-file");

	obj.on('dragenter', function(e) {
		$(".dragged-file").addClass('dragged-hover');	
	});
	
	obj.on('dragend', function(e) {
		$(".dragged-file").removeClass('dragged-hover');	
	});
	
	obj.on('dragover', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});
	
	obj.on('drop', function(e) {		
		e.preventDefault();
		e.stopPropagation();
		if(e.originalEvent.dataTransfer){
			var files = e.originalEvent.dataTransfer.files;
			console.log('dropped external file');
			$('#upload-form').hide();
			handleFileUpload(files, obj);
		}
	});

	$(document).on('drop', function(e) {
		e.stopPropagation();
		e.preventDefault();
	});
	
	var options = {
			beforeSubmit: function() {
				var filename = $('#query-similar-file').val();
				filename = filename.split("\\")[2];
				var ext = filename.split('.')[1].toLowerCase();
				if(ext != 'jpg' 
					&& ext != 'png' 
					&& ext != 'gif'
					&& ext != 'jpeg'
					&& ext != 'bmp'	){
					var query = (filename.split('.')[0]).toLowerCase();
					$('#input-new-search').val(query);
					$('#new-search-form').submit();
					return false;
				}       
			},
			beforeSend : function() {
				$('#upload-form').hide();
				$('#drag-text').hide();
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
	            $(".dragged-file").removeClass('dragged-hover');
	    		$(".dragged-file").addClass('dropped-file');
	            $("#progress").hide();
	            var scope = angular.element(wrapper).scope();
	        	scope.init(false);
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
            window.name = data;
            $(".dragged-file").removeClass('dragged-hover');
    		$(".dragged-file").addClass('dropped-file');
            $("#progress").hide();
            var scope = angular.element(wrapper).scope();
        	scope.init(false);
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
            $('#drag-text').hide();
            $("#progress").show();
            $('#upload-message').show();
            sendFileToServer(fd,status);
        } else {
        	//Search by extracted keywords
        	text = (files[i].name).toLowerCase().split('.')[0];
        	text = (text).split('-').join(' ').split('_').join(' ');
        	$('#input-new-search').val(text);
        	$(".dragged-file").removeClass('dragged-hover');
        	$('#new-search-form').submit();
        }
   }
}