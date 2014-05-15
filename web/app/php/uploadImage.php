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
include ('SmartImage.class.php');
include ('../config.php');
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if (in_array($extension, $allowedExts))
  {
	if(isset($_FILES['file']) ){
		if ($_FILES["file"]["error"] > 0) {
	 		 echo "Error";
		}
		else {
			$src = $_FILES["file"]["tmp_name"];
    		$img = new SmartImage($src);
    		$img->resize(200, 200, true);
    		$src = '../img/temp/'.$_FILES["file"]["name"];
    		$img->saveImage($src, 90);			
    		$avatar = $interfacePath.'img/temp/'.$_FILES["file"]["name"];
    		echo $avatar;
		}
	}
  }
else
  {
  echo "Error";
  }
?>
