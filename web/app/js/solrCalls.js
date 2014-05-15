// JavaScript Document

solr = {
	solrProxy: '../php/solrProxy.php',
	
	searchDocument : function( sourceID, searchTerm, startIndex, numResults,  returnFunction, errorFunction) {
		var terms = searchTerm.split(' ');
		var term = "";
		if( terms.length > 1) {
			for (var i = 0; i< terms.length; i++) {
				if (i !== terms.length - 1) {
					term += terms[i] + "+";
				} else {
					term += terms[i];
				}
			}
		} else {
			term = terms[0];
		}
        $url = encodeURIComponent(CFG.solrSelectUrl + "hl=true&hl.fl=text_extracted&hl.fragsize=50&fl=*,score&wt=json&json.nl=map&fq=type:PAGE&fq=id_media:" + sourceID +"&q=text_extracted:" + term + "&start=" + startIndex + "&rows=" + numResults + "&hl.snippets=5");
        console.log($url);
        $.ajax({
            type: "GET",
            url: this.solrProxy + "?proxy_url=" + $url,
            dataType: 'json',           
 
            success: function(data) {

				if(typeof(returnFunction) == 'function') {
					returnFunction(data);
				}
			},
			error: function(data) {
				if(typeof(errorFunction) == 'function') {
					errorFunction(data);
				}
			},
            beforeSend: function(){
                //alert("BEFORELOADING")
            },
            complete: function(){
                //alert("COMPLETE")
            }
        });
         
    },
	index: function () {
		var url = CFG.solrProxyUrl + "?proxy_url=";
		$.ajax({
			url : url + CFG.solrCoreUrl + encodeURIComponent('dataimport?command=full-import&clean=false'),
			method : "GET",
			dataType : "html",
			success : function(data) {
				console.log('Indexed successfully!');
			},
			async: false,
			error : function(err) {
				console.log("Error: " + err);
			}
		});
	}
}