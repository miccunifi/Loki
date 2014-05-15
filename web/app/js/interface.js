$(document).on('change', '#query-file', function(){
	var filename = $('#query-file').val();
	filename = filename.split("\\")[2];
	var ext = filename.split('.')[1].toLowerCase();
	if(ext == 'jpg' 
		|| ext == 'png' 
		|| ext == 'gif'
		|| ext == 'jpeg'
		|| ext == 'bmp'	){
		$('#upload-form').submit();
		$('#dragandrophandler').hide();
		if(window.name != 'null'){
			window.location.replace("main");
		}
	} else {
    	$('#search-form').val((filename.split('.')[0]).toLowerCase());
    	$('#form-search').submit();
	}
});

//Info tooltip

$(document).on('mouseenter', '#info-image', function(event) {
	$('#info-tooltip').fadeIn('fast');
}).on('mouseleave', '#info-image', function() {
	$('#info-tooltip').fadeOut('fast');
});