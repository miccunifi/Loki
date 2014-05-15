<?php include "../../ewcfg50.php" ?>
<?php include "../../ewmysql50.php" ?>
<?php include "../../phpfn50.php" ?>
<?php require_once "../lib/function.php" ?>
<?				
	require_once('../lib/Solr/Service.php');
	require_once('../lib/Solr/HttpTransport/CurlNoReuse.php');
?>
<?php
///include('include.php');

$conn = ew_Connect();

$methods = array(
	'sayHello' => function($request)
	{
		$request->result('Hello World!');
	},
	
	'substract' => function($request)
	{
		if(!is_array($request->params)) return $request->error(-32602);
		return $request->result(intval($request->params[0]) - intval($request->params[1]));
	},
	
	
	//funzione per inserire in processamento una url di una immagine per trovare il dominant color
	'getdominantcolor' => function($request)
	{
		global $conn;
		
		if(!is_array($request->params)) return $request->error(-32602);
		
		if(!isset($request->params['url'])) return $request->error(-32602,'no image sent');
		
		
		$image = $request->params['url'];
	
		$idSetProcess = 20;
		
		$x_processId = rand_str(12);
		
		$image_url = urlencode($image);
		
		$command = "http://localhost/eutv-tools/process/service/lib/ExtractImageDominantColor.php?image=$image_url";
						
		$sSqlWrk = "INSERT INTO process ( `processId`, `idSetProcess`, `idProcessStatus`, `start`, `end`, `OSProcessId`, `command`) VALUES".
				"('".$x_processId."', ".$idSetProcess.", 1, NULL, NULL, NULL, '$command&pid=$x_processId');";
		$rswrk = $conn->Execute($sSqlWrk);
		
		/*$idProcess = $conn->Insert_ID();
		
		$sSqlWrk = "UPDATE process SET command = '$command&pid=$idProcess' WHERE idProcessNum = '$idProcess'";
		$conn->Execute($sSqlWrk);*/
		
		return $request->result(getProcessStarted($x_processId));
	
	},
	
	
	
	//funzione per inserire in processamento una url di una immagine
	'getimageanalysis' => function($request)
	{
		global $conn;
		
		if(!is_array($request->params)) return $request->error(-32602);
		
		if(!isset($request->params['url'])) return $request->error(-32602,'no url sent');
		
		
		
		
		
		$url = $request->params['url'];
		$x_processId = rand_str(12);
		$encode_url = urlencode($url);
		
		$media_path = EW_IMAGE_PATH;

		$media_tag = "image";
		$id_media_type = 2;
		$process_id = 10;
		$server_path = EW_IMAGE_HTTP_URL;

		add_log('startJSONimageAnalysis:starting',"Input params url: $url");
							
			
			if(strpos($url,'file:/')!== false){ //caso di file locale
				
					$file_local_path = str_replace('file:/','',$url);
					$basename =  basename($url);
					
					
					$path_parts = pathinfo($file_local_path);
			
					$file_local_folder =  $path_parts['dirname'].EW_SEPARATOR;
				
				} else { //caso di immagine da scaricare
					
					$basename =  $x_processId.'_'.basename($url);
					$file_local_path = EW_IMAGE_PATH.$basename;
					
					$file_local_folder =  EW_IMAGE_PATH;
					add_log('startUNIFIProcess:downloading image',"command: ".'wget '.$url.' -O '.$file_local_path."");

					$result = exec('wget "'.$url.'" -O "'.$file_local_path.'"');

					//sleep(3);
				}
			
			
				
			$exists = ew_ExecuteScalar("select count(*) from ".EW_CONN_DB_MEDIA.".media WHERE filename = '".$basename."'");
	
			if($exists) {    
			
					$id_media = ew_ExecuteScalar("select id_media from ".EW_CONN_DB_MEDIA.".media WHERE filename = '".$basename."'");
					$query_insert = "";
			}
				else {
				
					$query_insert = "INSERT INTO ".EW_CONN_DB_MEDIA.".media (dataserverpath,mediauri,created,modified,filename,title,fps,id_media_types) VALUES ('".$server_path."', '".$basename."', now(),now(), '".$basename."', '".$basename."', '25',".$id_media_type.");";
									
					$conn->Execute($query_insert);
					$id_media = mysql_insert_id();
					$conn->Execute("UPDATE ".EW_CONN_DB_MEDIA.".media set uri = '$id_media' WHERE id_media = '$id_media' ");
																		
				}
				
		
			
			// EXECUTING VIDEO ANALYZING PROCESS
			$runCall = EW_URL_LOCALHOST."/process/launchProcess.php?a_add=A&idSetProcess=".$process_id."&".$media_tag."-name=".$basename."&uri=".$id_media."&mode=ws&id_media=".$id_media."&basename=".$basename."&x_processId=".$x_processId."&file-path=".$file_local_folder."&mode_json=1";
			
			//$result = "only test<br>";	
			$result = file_get_contents($runCall);
			
			add_log('JsonStartImageAnalysis:started',"command: "."query db: $query_insert \n executing analisys call: $runCall");
	
			add_log('JsonStartImageAnalysis:started',"command: ".'Started process: processId = '.$x_processId. '\n ');
			
		
		return $request->result(getProcessStarted($x_processId));
	
	},
	
	
	
	
	
		
	
	//funzione per inserire in processamento una url di una immagine
	'getvideoanalysis' => function($request)
	{
		global $conn;
		
		if(!is_array($request->params)) return $request->error(-32602);
		
		if(!isset($request->params['url'])) return $request->error(-32602,'no url sent');
		
		
		$url = $request->params['url'];
		$x_processId = rand_str(12);
		$encode_url = urlencode($url);
		
		$media_path = EW_VIDEO_PATH;

		$media_tag = "video";
		$id_media_type = 1;
		$process_id = 4;
		$server_path = EW_VIDEO_RTMP_URL;

		add_log('startJSONVideoAnalysis:starting',"Input params url: $url");
							
			
			if(strpos($url,'file:/')!== false){ //caso di file locale
				
					$file_local_path = str_replace('file:/','',$url);
					$basename =  basename($url);
					
					
					$path_parts = pathinfo($file_local_path);
			
					$file_local_folder =  $path_parts['dirname'].EW_SEPARATOR;
				
				} else { //caso di file da scaricare
					
					$basename =  $x_processId.'_'.basename($url);
					$file_local_path = EW_VIDEO_PATH.$basename;
					
					$file_local_folder =  EW_VIDEO_PATH;
					
					$command = 'wget "'.$url.'" -O "'.$file_local_path.'"';
					
					add_log('startJSONVideoAnalysis:downloading',"command: ".$command);

					
					
					
					//download video
					$idSetProcess = 13;
					$x_processId_download = rand_str(12);

					
					$sSqlWrk = "INSERT INTO process ( `processId`, `idSetProcess`, `idProcessStatus`, `start`, `end`, `OSProcessId`, `command`) VALUES".
			"('".$x_processId_download."', ".$idSetProcess.", 1, NULL, NULL, NULL, '$command');";
					$rswrk = $conn->Execute($sSqlWrk);
					
					add_log('startJSONVideoAnalysis:downloading',"inserting process with query: ".addslashes($sSqlWrk)."");

					//sleep(3);
				}
			
			
				
			$exists = ew_ExecuteScalar("select count(*) from ".EW_CONN_DB_MEDIA.".media WHERE filename = '".$basename."'");
	
			if($exists) {    
			
					$id_media = ew_ExecuteScalar("select id_media from ".EW_CONN_DB_MEDIA.".media WHERE filename = '".$basename."'");
					$query_insert = "";
			}
				else {
				
					$query_insert = "INSERT INTO ".EW_CONN_DB_MEDIA.".media (dataserverpath,mediauri,created,modified,filename,title,fps,id_media_types) VALUES ('".$server_path."', '".$basename."', now(),now(), '".$basename."', '".$basename."', '25',".$id_media_type.");";
									
					$conn->Execute($query_insert);
					$id_media = mysql_insert_id();
					$conn->Execute("UPDATE ".EW_CONN_DB_MEDIA.".media set uri = '$id_media' WHERE id_media = '$id_media' ");
																		
				}
				
		
			
			// EXECUTING VIDEO ANALYZING PROCESS
			$runCall = EW_URL_LOCALHOST."/process/launchProcess.php?a_add=A&idSetProcess=".$process_id."&".$media_tag."-name=".$basename."&uri=".$id_media."&mode=ws&id_media=".$id_media."&basename=".$basename."&x_processId=".$x_processId."&file-path=".$file_local_folder."&mode_json=1";
			
			//$result = "only test<br>";	
			$result = file_get_contents($runCall);
			
			
			//aggiorno l'id del processo master del download per associarlo a questa analisi e gestirlo bene con la coda
			if(@$x_processId_download!=""){
				
					//recupero l'id numerico del processo master di analisi video
					$idProcessNum = ew_ExecuteScalar("SELECT idProcessNum from process WHERE processId = '$x_processId'");
					$conn->Execute("UPDATE process set idProcessNumMaster = '$idProcessNum' WHERE processId = '$x_processId_download' ");
				
				}
			
			add_log('JsonStartVideoAnalysis:started',"command: "."query db: $query_insert \n executing analisys call: $runCall");
	
			add_log('JsonStartVideoAnalysis:started',"command: ".'Started process: processId = '.$x_processId. '\n ');
			
		
		return $request->result(getProcessStarted($x_processId));
	
	},
	
	
	
	
	
	//funzione per inserire in processamento una url di un documento
	'getdocumentanalysis' => function($request)
	{
		global $conn;
		
		if(!is_array($request->params)) return $request->error(-32602);
		
		if(!isset($request->params['url'])) return $request->error(-32602,'no url sent');
		
		
		$url = $request->params['url'];
		$sourceId = $request->params['sourceId'];
		$x_processId = rand_str(12);
		$encode_url = urlencode($url);
		
		$media_path = EW_IMAGE_PATH;

		$media_tag = "image";
		$id_media_type = 3;
		$process_id = 25;
		$server_path = EW_IMAGE_HTTP_URL;

		add_log('startJSONdocumentAnalysis:starting',"Input params url: ".serialize($url));
		
				
		// ADDING DOCUMENT_ANALYSIS PROCESS TO PIPELINE
		$runCall = EW_URL_LOCALHOST."process/launchProcess.php?a_add=A&idSetProcess=".$process_id."&params_142=".$encode_url."&params_141=".$sourceId."&mode=ws&x_processId=".$x_processId;
		
		//$result = "only test<br>";	
		$result = file_get_contents($runCall);
							
			
		
		return $request->result(getProcessStarted($x_processId));
	
	},
	
	
	
	
	
	
	//funzione per inserire in processamento un xml con i riferimenti ai video di cui fare il merge
	'mergevideos' => function($request)
	{
		global $conn;
		
		if(!is_array($request->params)) return $request->error(-32602);
		
		if(!isset($request->params['url'])) return $request->error(-32602,'no url sent');
		
		
		$url = $request->params['url'];

		$x_processId = rand_str(12);
		
		//$encode_url = urlencode($url);
		$encode_url = urldecode($url);
		
		$media_path = EW_VIDEO_PATH.$x_processId.'.mp4';

		$media_tag = "image";
		$id_media_type = 3;
		$process_id = 26;
		$server_path = EW_IMAGE_HTTP_URL;

		add_log('startJSONmergevideos:starting',"Input params url: ".serialize($url));
		
		$input = array(
		"idSetProcess"=>$process_id,
		"params_143"=>" '".$encode_url."'",
		"params_144"=>" ".$media_path,
		"x_processId"=>$x_processId
		);
		
		executeProcess($input);
		
		$output = array("video_url"=>EW_VIDEO_HTTP_URL.$x_processId.'.mp4',
		"web_url"=>"http://".EW_ABS_HOST.'/eutv-tools/process/service/json/get-merge-video.php?processId='.$x_processId);
		
		setProcessOutput($x_processId,$output);/**/
		
		return $request->result(getProcessStarted($x_processId,'Pending',$output));
	
	},
	
	
	
	
	
	
	
	//funzione per inserire in processamento una url di un documento
	'indexdocument' => function($request)
	{
		global $conn;
		
		if(!is_array($request->params)) return $request->error(-32602);
		
		if(!isset($request->params['url'])) return $request->error(-32602,'no url sent');
		
		
		$url = $request->params['url'];
		$sourceId = $request->params['sourceId'];
		$x_processId = rand_str(12);
		$encode_url = urlencode($url);
		
		$media_path = EW_IMAGE_PATH;

		$media_tag = "image";
		$id_media_type = 3;
		$process_id = 25;
		$server_path = EW_IMAGE_HTTP_URL;

		add_log('startJSONindexdocument:starting',"Input params url: $url");
		
				
				
				
				// solr connection
				$transportInstance = new Apache_Solr_HttpTransport_CurlNoReuse();
				$solr = new Apache_Solr_Service(EW_SOLR_CONN_HOST, EW_SOLR_CONN_PORT, '/'.EW_SOLR_CONN_INSTANCE.'/',$transportInstance);
  

	
	
				//recupero il testo dalla url
				$text = str_replace('&','&amp;',file_get_contents($url));
				//echo "File scaricato da $artifactUrl: <br><pre>";
				//var_dump($text);
				//echo "</pre>";
				
				
				//parso l'xml nel testo estratto
				$xml = simplexml_load_string($text);
				
				add_log('indexdocument:solrIndexing',"Found ".@count(@$xml->page)." pages to index");

				//echo "Processamento XML in pagine:<br>";
				
				foreach($xml->page as $page)
					{ 
					
					//echo "<br><br>Processamento pagina<br>";
					//echo "<strong>Numero</strong>: ".$page->attributes()->number."<br>";
					//echo "<strong>Testo</strong>: $page<br>";
					
						$doc = new Apache_Solr_Document();
				
				
						$id = $sourceId."_PAGE_".$page->attributes()->number;
						
						$doc->id = $id;
						$doc->sourceId = $sourceId;
						$doc->type= "PAGE";
						$doc->page = (int)$page->attributes()->number;
						$doc->text_extracted = $page;
						
						add_log('indexdocument:solrIndexing',"Inserimento pagina ".(int)$page->attributes()->number." text: ".$page);
						
						
						$result_insert = $solr->addDocument($doc);
						
						add_log('indexdocument:solrIndexing',"Ris. inserimento status:".$result_insert->getHttpStatus()." <br>Ris. inserimento message:".$result_insert->getHttpStatusMessage());
						
						//$array_insert_docs[] = $doc;
						
						 	//if you're going to be adding documents in bulk using addDocuments
						
					}

				
				//$solr->addDocuments($array_insert_docs);
				
				$solr->commit(); //commit to see the deletes and the document
				
				add_log('indexdocument:solrIndexing',"Completed ");
							
			
		
		return $request->result(getProcessStarted($x_processId,'Finished'));
	
	},
	
	
	
	//funzione per inserire in processamento una url di una immagine per trovare il dominant color
	'getresults' => function($request)
	{
			global $conn;
			if(!is_array($request->params)) return $request->error(-32602);
			
			if(!isset($request->params['processId'])) return $request->error(-32602,'no processId sent');
			
			
			$idProcessNum = $request->params['processId'];
		
		
			$rs = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$idProcessNum'");
			
			
			
			
			if($rs->RecordCount()==0)  return $request->error(-32000,'processId not founds');
			
			$output_string = $rs->fields('output');
			//var_dump($output_string);
			
			$output_array = unserialize($output_string);
			//var_dump($output_array);
			
			$completed = ($rs->fields('idProcessStatus')=="7"?'Y':'N');
			
			if(isset($request->params['level']) && $request->params['level']!="" && isset($output_array[$request->params['level']])) {
				$output_array = $output_array[$request->params['level']];
				
				$completed = 'Y';
			}
			
			$result = array(
				"completed"=>$completed,
				"status"=>$rs->fields('name'),
				"processId"=>$idProcessNum,
				"output"=>$output_array,
			);
			
			return $request->result($result);
	
	},
	
	
	//funzione per inserire in processamento una url di una immagine per trovare il dominant color
	'setprocessstatus' => function($request)
	{
			global $conn;
			if(!is_array($request->params)) return $request->error(-32602);
			
			if(!isset($request->params['processId'])) return $request->error(-32602,'no processId sent');
			if(!isset($request->params['status'])) return $request->error(-32602,'no status sent');
			
			$status = $request->params['status'];
			
			$idProcessStatus = ew_ExecuteScalar("SELECT idProcessStatus from processstatus where name = '$status'");
			 
			if($idProcessStatus=="")  return $request->error(-32602,'status value not allowed');
			
			$processId = $request->params['processId'];
			
			
			
			$rs_check = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$processId'");
			
			
			if($rs_check->RecordCount()==0)  return $request->error(-32000,'processId not founds');
			
			add_log('setProcessStatus',"$processId,$idProcessStatus");
			
			setProcessStatus($processId,$idProcessStatus);

			//$rs = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$idProcessNum'");
			
			$result = array(
				"changed"=>'Y',
				"status"=>$status,
				"processId"=>$processId
			);
			
			return $request->result($result);
	
	},
	
		//funzione per inserire in processamento una url di una immagine per trovare il dominant color
	'killprocess' => function($request)
	{
			global $conn;
			if(!is_array($request->params)) return $request->error(-32602);
			
			if(!isset($request->params['processId'])) return $request->error(-32602,'no processId sent');
			
			$processId = $request->params['processId'];
			
			
			
			$rs_check = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$processId'");
			
			
			if($rs_check->RecordCount()==0)  return $request->error(-32000,'processId not founds');
			
			
			setProcessStatus($processId,EW_KILLED_PROCESS_STATUS);

			//$rs = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$idProcessNum'");
			
			
			
			$result = array(
				"changed"=>'Y',
				"status"=>'Killed',
				"processId"=>$processId
			);
			
			return $request->result($result);
	
	},
	
	
	//funzione per inserire in processamento una url di una immagine per trovare il dominant color
	'getprocessinfo' => function($request)
	{
			global $conn;
			if(!is_array($request->params)) return $request->error(-32602);
			
			if(!isset($request->params['processId'])) return $request->error(-32602,'no processId sent');
			
			
			$idProcessNum = $request->params['processId'];
		
		
			$rs = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$idProcessNum'");
			
			
			
			
			if($rs->RecordCount()==0)  return $request->error(-32000,'processId not founds');
			
			$output_string = $rs->fields('output');
			//var_dump($output_string);
			
			$output_array = unserialize($output_string);
			//var_dump($output_array);
			
			$completed = ($rs->fields('idProcessStatus')=="7"?'Y':'N');
			
			if(isset($request->params['level']) && $request->params['level']!="" && isset($output_array[$request->params['level']])) {
				$output_array = $output_array[$request->params['level']];
				
				$completed = 'Y';
			}
			
			
			//subprocess
			$rs_subprocess =  $conn->execute("SELECT * FROM `process` LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE ((( (idProcessNum <> '' ) AND (idProcessNumMaster = '".$rs->fields('idProcessNum')."' ) ))) ORDER BY idProcessNum DESC LIMIT 0, 20");
			
			$subprocess_array = array();
			
			while(!$rs_subprocess->EOF){// esiste un processo da eseguire
						
					$subprocess_array[] =  array(
							"completed"=>$completed,
							"status"=>$rs_subprocess->fields('name'),
							"processId"=>$rs_subprocess->fields('idProcessNum'),
							'name'=>ew_ExecuteScalar("SELECT `name` FROM `setprocess` WHERE `idSetProcess` = '" . $rs_subprocess->fields('idSetProcess') . "'"),
							'start'=>$rs_subprocess->fields('start'),
							'end'=>$rs_subprocess->fields('end'),
							'OSProcessId'=>$rs_subprocess->fields('OSProcessId')
			
						);	
						
				$rs_subprocess->MoveNext();
			}
			
			
			
			$result = array(
				"completed"=>$completed,
				"status"=>$rs->fields('name'),
				"processId"=>$idProcessNum,
				"output"=>$output_array,
				'name'=>ew_ExecuteScalar("SELECT `name` FROM `setprocess` WHERE `idSetProcess` = '" . $rs->fields('idSetProcess') . "'"),
				'start'=>$rs->fields('start'),
				'end'=>$rs->fields('end'),
				'subprocess'=> $subprocess_array

			);
			
			return $request->result($result);
	
	}
	
);

Tivoka::createServer($methods);





//---------------  UTILS FUNCTION  -----------------------

function getresults()
{
	global $conn;
	$idProcessNum = varRequest('processId','');
	
	if($idProcessNum=="") sendOutput(getError('No processId sent'));
	
	$rs = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE idProcessNum = '$idProcessNum'");
	
	if($rs->RecordCount()==0) sendOutput(getError("No process found with processId $idProcessNum"));
	
	$output_string = $rs->fields('output');
	//var_dump($output_string);
	
	$output_array = unserialize($output_string);
	//var_dump($output_array);
	
	$result = array(
		"completed"=>($rs->fields('idProcessStatus')=="7"?'Y':'N'),
		"status"=>$rs->fields('name'),
		"processId"=>$idProcessNum,
		"output"=>$output_array,
	);
	
	sendOutput($result);
	
}


function getdominantcolor()
{
	global $conn;
	$image = varRequest('image','');
	
	if($image=="") sendOutput(getError('No image sent'));
	
	
	$idSetProcess = 20;
	
	$x_processId = rand_str(12);
	
	$image_url = urlencode($image);
	
	$command = "http://localhost/eutv-tools/process/service/lib/ExtractImageDominantColor.php?image=$image_url";
					
	$sSqlWrk = "INSERT INTO process ( `processId`, `idSetProcess`, `idProcessStatus`, `start`, `end`, `OSProcessId`, `command`) VALUES".
			"('".$x_processId."', ".$idSetProcess.", 1, NULL, NULL, NULL, '$command');";
	$rswrk = $conn->Execute($sSqlWrk);
	
	$idProcess = $conn->Insert_ID();
	
	$sSqlWrk = "UPDATE process SET command = '$command&pid=$idProcess' WHERE idProcessNum = '$idProcess'";
	$conn->Execute($sSqlWrk);
	
	sendOutput(getProcessStarted($idProcess));
}
	
function getProcessStarted($processId,$status = 'Pending',$output = null){
	
	$result = array(
		"status"=>$status,
		"processId"=>$processId,
		"output"=>$output
	);
	
	return $result;
	
	}



?>