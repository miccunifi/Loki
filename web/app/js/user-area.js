

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



var app = angular.module("edit-app", ["xeditable"]);

app.run(function(editableOptions) {
	  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
	});

app.controller('Ctrl', function($scope, $http) {
	  $scope.init = function(){
		  $('.avatar-content a').show();
		   $http({method: 'GET', url: 'getUserInfo.php'}).
	    	success(function(data, status, headers, config) {
	    		var info = data.split(',');
	    		$scope.user.name = info[0];
	    		$scope.user.email = info[1];
	    		$scope.user.avatar = info[2];
	    	}).
	    	error(function(data, status, headers, config) {
	    		return data;
	    	});
		$('#avatar-upload').hide();
	  };
		
	  $scope.user = {
	    name: '',
	    email: '',
	    avatar: ''
	  }; 
	  
	  $scope.updateName = function(data) {
		    $http({method: 'GET', url: 'updateName.php?name=' + data}).
		    	success(function(data, status, headers, config) {
		    		if(data == 'success'){
		    			return true;
		    		} else {
		    			return data;
		    		}
		    	}).
		    	error(function(data, status, headers, config) {
		    		return data;
		    	});
	  };
	  
	  $scope.updateEmail = function(data) {
		    $http({method: 'GET', url: 'updateEmail.php?email=' + data}).
		    	success(function(data, status, headers, config) {
		    		if(data == 'success'){
		    			return true;
		    		} else {
		    			return data;
		    		}
		    	}).
		    	error(function(data, status, headers, config) {
		    		return data;
		    	});
	  };
	  
	  $scope.updateAvatar = function(data) {
		  if(data == undefined){
			  $scope.init();
			  return false;
		  } else{
		    $http({method: 'GET', url: 'updateAvatar.php?avatar=' + data}).
		    	success(function(data, status, headers, config) {
		    		if(data == 'success'){
		    			$scope.init();
		    			return true;
		    		} else {
		    			return data;
		    		}
		    	}).
		    	error(function(data, status, headers, config) {
		    		return data;
		    	});
		  }
	  };
});

$(document).on('click', '.avatar-content a', function(){
	$('#avatar-upload').show();
	$('.avatar-content a').hide();
});

$(document).ready(function(){
	var options = { 
			beforeSend: function(){
				$("#progress").show();
				$("#bar").width('0%');
				$("#message").html("");
				$("#percent").html("0%");
				
			},
			uploadProgress: function(event, position, total, percentComplete) {
				$("#bar").width(percentComplete+'%');
				$("#percent").html(percentComplete+'%');
			},
			success: function(){
				$("#bar").width('100%');
				$("#percent").html('100%');
			},
			complete: function(response){
				$("#message").html("<font color='green'>"+response.responseText+"</font>");
				$('#avatar-upload').hide();
				$('.avatar-content a').show();
				var scope = angular.element('#user-form').scope();
				scope.$apply(function(){
					scope.init();
				});
			},
			error: function(){
				$("#message").html("<font color='red'> ERROR: impossibile caricare il file. Riprovare.</font>");
				$('.avatar-content a').hide();
			}
	}; 
	$("#upload-form").ajaxForm(options);
});

//Close open divs
$(document).click(
		function(e) { //Close login form
			if (e.target.id != "upload-form" && e.target.id != "avatar-img" && e.target.id != "input-file" && e.target.id != "input-submit") {
				$('.avatar-content a').show();
				$('#avatar-upload').hide();
			}
		});