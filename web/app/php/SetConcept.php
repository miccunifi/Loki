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
	include ('../config.php');
	
	//includo il file con le funzioni utilizzate nel pan
	include 'function.php';
	
	$myFile = "log_query.txt";
	$fh = fopen($myFile, 'a') or die("ERROR");
	
	fwrite($fh, $stringData);

	//estrazione dei dati da salvare
	$film = @$_GET['film'];
	$conceptname = @$_GET['conceptname'];
	$starttime = @$_GET['starttime'];
	$endtime = @$_GET['endtime'];
	$concepttype = @$_GET['concepttype'];
	$owner = @$_GET['owner'];
	
	$mode = (@$_GET['mode']=="free"?"free":"concept");
	
	//$idbox = $_GET['id_box'];
	$fps = @$_GET['fps'];
	$ontology = @$_GET['idOntology'];
	
	$stringData = "\n\nRegistrazione: "." | film: ".$film
	." | ".$conceptname
	." | ".$conceptname
	." | ".$starttime
	." | ".$endtime
	." | ".$concepttype
	." | ".$owner
	." | ".$mode
	//$idbox = $_GET['id_box'];
	." | ".$fps
	." | ".$ontology;
	//echo $stringData ;
	fwrite($fh, $stringData);
	
	//correzione bug selezione video sul titolo
	if(isset($_GET['id'])) $cond = " media.id_media ='" . $_GET['id'] . "' ";
		else if(isset($_GET['id_media'])) $cond = " media.id_media ='" . $_GET['id_media'] . "' ";
			else  $cond = " media.title ='" . $film . "' ";

	$filename = "";

	if (  isset($owner) && isset($conceptname) && isset($starttime) && isset($endtime)) {

		//controllo se il film gia presente nella tabella
		$chkquery = "SELECT * FROM media WHERE $cond ";

		//lancio della query
		$chkresult = mysql_query($chkquery) or die('ERROR');
		$num_record = mysql_num_rows($chkresult);

		//se esito negativo allora lo inserisco
		if ($num_record == 0) {
			fclose($fh);
			die("ERROR");
		}
		else {
			//estraggo l'id del film
			$array = mysql_fetch_array($chkresult);
			$idfilm = $array["id_media"];
			$filename = $array["filename"];
		}
		
		if($mode == "concept") {
		
				$queryConcept = "SELECT id_concepts FROM concepts,ontologies WHERE ontologies.id_ontologies=concepts.id_ontologies AND ontologies.id_ontologies=". $ontology ." AND concepts.name='".$conceptname."'";
		
				//lancio della query
				$chkresult_concepts = mysql_query($queryConcept) or die('ERROR QUERY concepts');
				$num_record_concepts = mysql_num_rows($chkresult_concepts);
		
				//se esito negativo allora lo inserisco
				if ($num_record_concepts == 0) {
					//estraggo il maxID
					$query_id_concepts = mysql_query("SELECT MAX(id_concepts) AS id FROM concepts");
					$row_concepts = mysql_fetch_array($query_id_concepts);
					$maxid_concepts = $row_concepts[id];
					$id_concepts = $maxid_concepts + 1;
					//creo la query
					$insquery_concepts = "INSERT INTO concepts (id_concepts, name , id_ontologies) VALUES ('" . $id_concepts . "', '" . $conceptname . "', '" . $ontology . "')";
					//lancio la query di inserimento del film
					$insresult_concepts = mysql_query($insquery_concepts);
				}
				else {
					//estraggo l'id del film
					$array_concepts = mysql_fetch_array($chkresult_concepts);
					$id_concepts = $array_concepts["id_concepts"];
				}
		
		}
		else $id_concepts = null;
		//converto i frame in millisecondi
		
// 		$starttime_point = round($starttime*1000/$fps);
// 		$endtime_point = round($endtime*1000/$fps);
		
// 		$start_time_second = (int)round($starttime/$fps);
		$start_time_second = $starttime/1000;
		$starttime_point = $starttime;
		$endtime_point = $endtime;
		
		fwrite($fh, "selezionato secondo esportazione : ".$start_time_second."\n");

		//estrazione della thumbnail
		$starttime_point_extraction = sec2hms($start_time_second);
		fwrite($fh, "selezionato timecode esportazione : ".$starttime_point_extraction."\n");

		$thumbnail_name = "$filename-$starttime.png";
		
		$command = "ffmpeg -ss ".$starttime_point_extraction." -i ".$miccDirectory."media/video/".$filename." -f image2 -vframes 1 -s 320x240 ".$miccDirectory."media/image/".$thumbnail_name;

		fwrite($fh, "\neseguito: ".$command."\n");

		$result = exec($command);

// 		$insert_id_concept = ($id_concepts != null ?"'".$id_concepts."'": " null ");
		$insert_id_concept = '10';
		
		$now = date('Y-m-d H:i:s');
		$querys = "UPDATE media SET last_modified = '".$now."' WHERE id_media = '".$idfilm."'";
		$result = mysql_query($querys) or die('ERROR');
		
		//creo la query di inserimento del concetto
		$querys = "INSERT INTO annotations (id_media, title, timepoint, endpoint, id_concepts, id_users, id_annotations_types,thumbnail) VALUES ('" . $idfilm . "', '" . $conceptname . "', '" . $starttime_point . "', '" . $endtime_point . "', '" .$insert_id_concept ."', '" . $owner . "', '2','$thumbnail_name')";
		
		$date = date('Y-m-d H:i:s');
		$dataserverpath = $absolutePath.'media/image/';
				//echo $querys;
		
		$stringData = $querys."\n";
		
		fwrite($fh, $stringData);

		//lancio la query di inserimento del concetto
		$result = mysql_query($querys) or die('ERROR');
		$id_annotations = mysql_insert_id();
		
		$mediauri = $thumbnail_name.str_replace('.png', '');
		
		$query = "INSERT INTO media(id_media_types, uri, filesize, dataserverpath, mediauri, title, created, modified, fps, filename, processed_status, id_media_video, last_modified, author, shot_startpoint, shot_endpoint, owner) VALUES (2, '', null , '".$dataserverpath."', '".$mediauri."', null, '".$date."', '".$date."', 0, '".$thumbnail_name."', 0, '".$idfilm."', '".$date."', null, '".$starttime_point."','".$endtime_point."', '".$_SESSION['user_id']."' )";
		
		$result = mysql_query($query) or die('ERROR');
	
		$xml_result = "";
			
		$query = "SELECT * FROM annotations WHERE id_annotations = ".$id_annotations;
		//lancio della query
		$results = mysql_query($query);

		$num_record = mysql_num_rows($results);

		// controllo l'esito
		if (!$result) {
		   print "ERROR";
		}
		else {
			if ($num_record != 0) {
				header("Content-type: text/xml; charset=utf-8");
				// creo l'header xml per il refresh della cache
				$xml_result .=  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				$xml_result .= "<response><annotations>";
				while ($row = mysql_fetch_assoc($results)) {
					$xml_result .= "<sourceId>".$row["id_media"]."</sourceId>";
					$xml_result .= "<start>".$row["timepoint"]."</start>";
					$xml_result .= "<keyword>".$row["title"]."</keyword>";
					$xml_result .= "<id>".$id_annotations."</id>";
				}
				// chiudo l'xml'
				$xml_result .= "</annotations></response>";
			}
			
			echo $xml_result;
			
			fwrite($fh, "\nXML generato nuovo inserimento: $xml_result");
	
		}
	} else {
		print "ERROR SET";
	}

	fclose($fh);

?>