<?

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



?><?php
$output_dir = "../img/avatars/";
session_start();

include ("../config.php");
include ("../php/SmartImage.class.php");

if(isset($_POST['upload']))
{
	if(isset($_FILES['myfile']) ){
		if ($_FILES["myfile"]["error"] > 0) {
	 		 echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}
		else {
			$src = $_FILES["myfile"]["tmp_name"];
    		$img = new SmartImage($src);
    		$img->resize(200, 200, true);
    		$src = $output_dir.$_SESSION['user_id'].'-'.$_FILES["myfile"]["name"];
    		$img->saveImage($src, 90);
			
    		$avatar = $interfacePath.'img/avatars/'.$_SESSION['user_id'].'-'.$_FILES["myfile"]["name"];
    		
			$result = mysql_query("UPDATE users SET avatar = '".$avatar."' WHERE username = '".$_SESSION['email']."'") or trigger_error(mysql_error());
			if(mysql_affected_rows()!= -1){
				echo "Success! File uploaded.";
				$_SESSION['avatar'] = $avatar;
			}
			else {
				echo '<span class="error">Error saving in database!</span>';
			}
		}
	}
}
?>