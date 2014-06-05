

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



// JavaScript Document

utils = {

	colorToHex: function(color) {
		if (color.substr(0, 1) === '#') {
			return '0x' + color.substr(1);
		}
		var digits = /(.*?)rgb\((\d+), (\d+), (\d+)\)/.exec(color);
		
		var red = parseInt(digits[2]);
		var green = parseInt(digits[3]);
		var blue = parseInt(digits[4]);
		
		var rgb = blue | (green << 8) | (red << 16);
		return digits[1] + '0x' + rgb.toString(16);
	},
	
	hexToR: function(h) {return parseInt((this.cutHex(h)).substring(0,2),16)},
    hexToG: function(h) {return parseInt((this.cutHex(h)).substring(2,4),16)},
    hexToB: function(h) {return parseInt((this.cutHex(h)).substring(4,6),16)},
    cutHex: function(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h},
	
	getObjects: function(obj, key, val) {
    	var objects = [];
    	for (var i in obj) {
        	if (!obj.hasOwnProperty(i)) continue;
        	if (typeof obj[i] == 'object') {
           		objects = objects.concat(utils.getObjects(obj[i], key, val));
        	} else if (i == key && obj[key] == val) {
            	objects.push(obj);
        	}
    	}
    	return objects;
	},
    
 // Get similar images id 
    getSimilarImagesURL: function(URL, distance){
    	if(typeof(distance)==='undefined') distance = CFG.LIREdistanceSearch;
    	
    	var query_url = CFG.solrProxyUrl +  "?proxy_url=";
    	var images_id = [];
    	
    	//Getting similar images' id
    	var url = CFG.solrLireUrl + "url=" + URL + "&rows=" + CFG.maxResults;
    	query_url += encodeURIComponent(url).replace(/'/g,"%27").replace(/"/g,"%22");
    	$.ajax({
    		url : query_url,
    		method : "GET",
    		dataType : "xml",
    		success : function(data) {
    			var images;
    			images = $(data).find('arr').find('lst');
    			for ( var i = 0; i < $(images).length; i++) {
    				var image_id = $(images[i]).find("str[name='id']").text();
    				var d = $(images[i]).find("float[name='d']").text();
    				//Take images until a useful distance
    				if(d > distance){
    					break;
    				}
    				images_id.push(image_id);
    			}
    		},
    		async: false,
    		error : function(err) {
    			console.log("Error: " + err);
    		}
    	});
    	
    	var query = '(';
    	for (var i = 0; i < images_id.length; i++){
    		query += images_id[i] + '%20';
    	}
    	query += '1)';
    	
    	return query;
    },
    
 // Get similar images id 
    getSimilarImagesID: function(id, distance){
    	if(typeof(distance)==='undefined') distance = CFG.LIREdistanceSearch;
    	var query_url = CFG.solrProxyUrl +  "?proxy_url=";
    	var images_id = [];
    	
    	//Getting similar images' id
    	var url = CFG.solrLireUrl + "id=" + id + "&rows=" + CFG.maxResults;
    	query_url += encodeURIComponent(url).replace(/'/g,"%27").replace(/"/g,"%22");
    	$.ajax({
    		url : query_url,
    		method : "GET",
    		dataType : "xml",
    		success : function(data) {
    			var images;
    			images = $(data).find('arr').find('lst');
    			for ( var i = 0; i < $(images).length; i++) {
    				var image_id = $(images[i]).find("str[name='id']").text();
    				var d = $(images[i]).find("float[name='d']").text();
    				//Take images until a useful distance
    				if(d > distance){
    					break;
    				}
    				images_id.push(image_id);
    			}
    		},
    		async: false,
    		error : function(err) {
    			console.log("Error: " + err);
    		}
    	});
    	
    	var query = '(';
    	for (var i = 0; i < images_id.length; i++){
    		query += images_id[i] + '%20';
    	}
    	query += '1)';
    	
    	return query;
    }

}