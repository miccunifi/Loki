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

session_start();

include ('SmartImage.class.php');
include ('../config.php');
$images = array("gif", "jpeg", "jpg", "png");
$videos = array("mp4", "mpeg", "mov", "avi", "wmv", "mpg");
$documents = array("pdf", "doc", "ods", "odt", "docx");
$audios = array("mp3", "wma");
if(isset($_FILES['myfile'])){
	$temp = explode(".", $_FILES["myfile"]["name"]);
	$extension = strtolower(end($temp));
	$media_type = '';
	$upload_dir = '../..'.$uploadDir;
	$id_media_type = 0;
	if(in_array($extension, $images)){
		$upload_dir .= 'image/';
		$media_type = 'image';
		$id_media_type = 2;
	} elseif (in_array($extension, $videos)){
		$upload_dir .= 'video/';
		$media_type = 'video';
		$id_media_type = 1;
	} elseif (in_array($extension, $audios)){
		$upload_dir .= 'audio/';
		$media_type = 'audio';
		$id_media_type = 3;
	} elseif (in_array($extension, $documents)){
		$upload_dir .= 'document/';
		$media_type = 'document';
		$id_media_type = 4;
	} else {
		$upload_dir = null;
	}
	
	if($upload_dir != null ){
		if ($_FILES["myfile"]["error"] > 0) {
			echo "Error: File too large";
		}
		else {
			$log_text = "---------------------------\n";
			$today = date("Y-m-d H:i:s");
			$log_text .= $today;
			$filename = str_replace("'", "", str_replace(" ", "", $_FILES["myfile"]["name"]));
			$filename = strtolower(preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $filename));
			$filename = str_replace("(", "", str_replace(")", "", $filename));
			move_uploaded_file($_FILES["myfile"]["tmp_name"], $upload_dir.$filename);
			
			$log_text .= "\nFilename: ".$filename;
			$log_text .= "\nUser: ".$_SESSION['email'];
			
			$fileSize = '0';
			
			$tmp_mediauri = explode(".", $filename);
			$mediauri = current($tmp_mediauri);
			
			if($id_media_type == 1){ //Video case
				$out_file = $mediauri.'.mp4';
				//Generates also video thumbnail
				exec("sh decoder.sh ".$miccDirectory."media/video/".$filename." ".$miccDirectory."media/video/thumb/".$out_file.".jpg 1>encoding-log.txt 2>&1");
				$filename = $out_file;
			} elseif ($id_media_type == 2){ //Image case
				//Resizing image
// 				$src = $upload_dir.$filename;
// 				$img = new SmartImage($src);
// 				$img->resize(200, 200, true);
// 				$src = '../img/temp/'.$_FILES["file"]["name"];
// 				$img->saveImage($src, 90);
			} elseif($id_media_type == 3){ //Audio case
				include('waveform-png.php');
				$audioInput = $miccDirectory."media/audio/".$filename;
				if($extension != 'mp3'){
					$log_text .= shell_exec("ffmpeg -i ".$audioInput." -acodec libmp3lame -ab 160k ".$audioInput.".mp3");
					$audioInput = $miccDirectory."media/audio/".$filename.".mp3";
					$filename = $filename.".mp3";
					$mediauri = $mediauri.$extension;
				}
				//Creating .ogg file
				$log_text .= shell_exec("ffmpeg -i ".$audioInput." -acodec libvorbis ".$audioInput.".ogg");	
				$waveOutput = $miccDirectory."media/audio/waves/".$filename.".png";
				//Generating audio sound wave
				generateSoundWave($audioInput, $waveOutput, 500, 100, "#FF0000", "");
			} elseif($id_media_type == 4){ //Document case
				$thumbDirectory = $miccDirectory."media/document_thumb/".$mediauri."/";
				$svgDirectory = $miccDirectory."media/document_svg/".$mediauri."/";
				$inputDocument = $miccDirectory."media/document/".$filename;
				if($extension != 'pdf'){
					$log_text .= shell_exec("abiword --to=PDF -o ".$miccDirectory."media/document/".$mediauri.".pdf ".$inputDocument);
					$filename = $mediauri.".pdf";
					$inputDocument = $miccDirectory."media/document/".$filename;
				}
				//Create pdf page thumbnails and svg
				exec("sh convertPDF.sh ".$inputDocument." ".$miccDirectory."media/ ".$mediauri."");
				
				$result = shell_exec("pdftk ".$inputDocument." dump_data | grep NumberOfPages");
				$fileSize = filter_var($result, FILTER_SANITIZE_NUMBER_INT);
			}
			
			$date = date('Y-m-d H:i:s');
			$dataserverpath = $absolutePath.'media/'.$media_type.'/';
			if(isset($_POST['title'])){
				$title = str_replace("'", "\'", htmlspecialchars($_POST['title']));
			} else {
				$title = '';
			}
			if(isset($_POST['author'])){
				$author = str_replace("'", "\'", htmlspecialchars($_POST['author']));
			} else {
				$author = '';
			}
			$query = mysql_query("INSERT INTO media(id_media_types, uri, filesize, dataserverpath, mediauri, title, created, modified, fps, filename, processed_status, id_media_video, last_modified, author, owner) VALUES (".$id_media_type.", '', ".$fileSize." , '".$dataserverpath."', '".$mediauri."', '".$title."', '".$date."', '".$date."', 0, '".$filename."', 0, NULL, '".$date."', '".$author."', '".$_SESSION['user_id']."' )");
			if(mysql_affected_rows()!= -1){
				$id_media = mysql_insert_id();
				$query = mysql_query("INSERT INTO user_collection (user_id, media_id) VALUES ('".$_SESSION['user_id']."','".$id_media."')");
				
				//Adding annotation
				$annotations = strtolower($_POST['tags']);
				$annotations = str_replace(", ", ",", $annotations);
				$annotations = str_replace(" ", "", $annotations);
				$annotations = explode(",", $annotations);
				foreach ($annotations as $a){
					if($a != ''){
						$query = mysql_query("INSERT INTO annotations (title, timepoint, id_media, id_users) VALUES ('".$a."','0','".$id_media."','".$_SESSION['user_id']."')");
						$url = $solrCoreUrl.'dataimport?command=full-import&clean=false';
						$index = curl($url);
						$log_text .= $index;
					}
				}
			
				echo 'Success';
				
				$log_text .= "\n";
				
				$txt = "upload-log.txt";
				$fh = fopen($txt, 'a') or die("can't open file");
				fwrite($fh, $log_text);
				fclose($fh);
			} else {
				echo 'Error: mysql error';
			}
		}
	} else {
		echo 'Error: not allowed extension';
	}
} else {
	echo 'Error: file not set';
}

function curl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
?>