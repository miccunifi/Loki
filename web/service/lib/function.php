<?php
include "../json/include.php";
//define("EW_DEBUG_ENABLED", TRUE, TRUE);

$main_colors = array(
"000000"=>"Black",
"0000ff"=>"Blue",
"000080"=>"Navy",
"00ff00"=>"Lime",
"00ffff"=>"Acqua",
"00ff80"=>"Cyan",
"008000"=>"Green",
"0080ff"=>"Blue",
"008080"=>"Teal",
"800000"=>"Maroon",
"800000"=>"Purple",
"800080"=>"Purple",
"808000"=>"Olive",
"8080ff"=>"Blue",
"808080"=>"Gray",
"80ff00"=>"Green",
"80ff80"=>"Green",
"80ffff"=>"Acqua",
"ff0000"=>"Red",
"ff0080"=>"Magenta",
"ff00ff"=>"Magenta",
"ff8000"=>"Orange",
"ff8080"=>"Pink",
"ff80ff"=>"Pink",
"ffff00"=>"Yellow",
"ffff80"=>"Light Yellow",
"ffffff"=>"White"
	);
function _convertToTimestamp($frame_to_convert, $fps){
		
		$inputval = $frame_to_convert/$fps; // USER DEFINES NUMBER OF SECONDS FOR WORKING OUT | 3661 = 1HOUR 1MIN 1SEC 
		$sss = (int) (($inputval - (int) $inputval)*1000);	// salvo i decimali
		$inputval = (int) $inputval;	// arrotondo 
		$hh = intval($inputval / 3600);    // '/' given value by num sec in hour... output = HOURS 
		$ss_remaining = ($inputval - ($hh * 3600));        // '*' number of hours by seconds, then '-' from given value... output = REMAINING seconds 
		$mm = intval($ss_remaining / 60);    // take remaining sec and devide by sec in a min... output = MINS 
		$ss = ($ss_remaining - ($mm * 60));        // '*' number of mins by seconds, then '-' from remaining sec... output = REMAINING seconds. 
		if ($hh<10) {
			$hh = '0'. $hh;
		}
		if ($mm<10) {
			$mm = '0'. $mm;
		}
		if ($ss<10) {
			$ss = '0'. $ss;
		}
		if ($sss<10) {
			$sss = '00'. $sss;
		}else{
			if ($sss<100) {
				$sss = '0'. $sss;
			}
		}
		return   $hh . ':' . $mm . ':' . $ss . '.' .$sss;
}

function _convertMillisecondToTimestamp($timepoint){
		
		$inputval = $timepoint/1000;	// arrotondo 
		$hh = intval($inputval / 3600);    // '/' given value by num sec in hour... output = HOURS 
		$ss_remaining = ($inputval - ($hh * 3600));        // '*' number of hours by seconds, then '-' from given value... output = REMAINING seconds 
		$mm = intval($ss_remaining / 60);    // take remaining sec and devide by sec in a min... output = MINS 
		$ss = ($ss_remaining - ($mm * 60));        // '*' number of mins by seconds, then '-' from remaining sec... output = REMAINING seconds. 
		if ($hh<10) {
			$hh = '0'. $hh;
		}
		if ($mm<10) {
			$mm = '0'. $mm;
		}
		if ($ss<10) {
			$ss = '0'. $ss;
		}
		if ($sss<10) {
			$sss = '00'. $sss;
		}else{
			if ($sss<100) {
				$sss = '0'. $sss;
			}
		}
		return   $hh . ':' . $mm . ':' . $ss . '.' .$sss;
}
function MediaInfoToArray($info_media){

$array_info = preg_split('/\n/',$info_media);



		foreach($array_info as $info) {
		
				$key = trim(substr($info,0,strpos($info,':')));
				$value = trim(substr($info,strpos($info,':')+1));
				if($key!="") $array_final[$key] = $value;


		
		}

		return $array_final;


}
function connectHkuSoap($url = EW_HKU_SOAP_SERVER)
{
global $client;
$proxyhost = isset($_POST['proxyhost']) ? $_POST['proxyhost'] : '';
$proxyport = isset($_POST['proxyport']) ? $_POST['proxyport'] : '';
$proxyusername = isset($_POST['proxyusername']) ? $_POST['proxyusername'] : '';
$proxypassword = isset($_POST['proxypassword']) ? $_POST['proxypassword'] : '';
$useCURL = isset($_POST['usecurl']) ? $_POST['usecurl'] : '0';
$client = new nusoap_client($url, false,
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
$client->setUseCurl($useCURL);


$params = array(
    'login' 		=> EW_CONN_USER_HKU,
    'password'      => EW_CONN_PASS_HKU
);


$result = $client->call('doLogin',$params,EW_HKU_SOAP_NAMESPACE);//, $params, 'http://soap.amazon.com', 'http://soap.amazon.com');


$session = $result['session'];

// print_r($session) . '<br>';

return $session;

}

function xml2array($contents, $get_attributes=1, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();
        
        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else { //There was another element with the same tag name

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;


            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        
                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    
    return($xml_array);
}  

function sendTweet($status)
{

require_once('../twitterapps/twitteroauth/twitteroauth.php');
require_once('../twitterapps/config.php');


//define('TWITTER_ACCESS_TOKEN', "190186681-P2zaw9k33gZytOIWFw89keCkmosyGeCZn0Nyakgw");
//define('TWITTER_ACCESS_TOKEN_SECRET', "6qSCVlEjLV74HS3S4FbaoD3w756XntZDh9KemWIMDw");

define('TWITTER_ACCESS_TOKEN', "fSWvBic1Om1bUMGKXzGmoQ");
define('TWITTER_ACCESS_TOKEN_SECRET', "LRDsOVQCMBXFb2NPVxCwLFymi64Gey18nwkQ67pQ");



/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$result = $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
//echo "Connection result:<br><pre>";print_r($result);echo "</pre>";

$message = date("Y-m-d | H:i:s")." - ".$status; 
$result = $connection->post('statuses/update', array('status' => $message));
//echo "Post result:<br><pre>";print_r($result);echo "</pre>";

}


function rgb2hsl($rgb){
    $clrR = ($rgb[0]);
    $clrG = ($rgb[1]);
    $clrB = ($rgb[2]);
    
    $clrMin = min($clrR, $clrG, $clrB);
    $clrMax = max($clrR, $clrG, $clrB);
    $deltaMax = $clrMax - $clrMin;
    
    $L = ($clrMax + $clrMin) / 510;
    
    if (0 == $deltaMax){
        $H = 0;
        $S = 0;
    }
    else{
        if (0.5 > $L){
            $S = $deltaMax / ($clrMax + $clrMin);
        }
        else{
            $S = $deltaMax / (510 - $clrMax - $clrMin);
        }

        if ($clrMax == $clrR) {
            $H = ($clrG - $clrB) / (6.0 * $deltaMax);
        }
        else if ($clrMax == $clrG) {
            $H = 1/3 + ($clrB - $clrR) / (6.0 * $deltaMax);
        }
        else {
            $H = 2 / 3 + ($clrR - $clrG) / (6.0 * $deltaMax);
        }

        if (0 > $H) $H += 1;
        if (1 < $H) $H -= 1;
    }
    return array($H, $S,$L);
}

function decodeXML($string){

$string = str_replace("&lt;","<",$string);
$string = str_replace("&gt;",">",$string);
$string = str_replace("ns:","",$string);
return $string;
}


function timecodeToMillisecond($timecode) {
		$hours = $minutes = $seconds = $milliseconds = 0;
		$timeOnly = substr($timecode,1,strlen($timecode)-4);
		
		$array_value = explode(':',$timeOnly);
		
		
		$timeMillisecond =0;
		$timeMillisecond += $array_value[0]*1000*60*60;
		$timeMillisecond += $array_value[1]*1000*60;
		$timeMillisecond += $array_value[2]*1000;
		$timeMillisecond += $array_value[3]*25;
		
		return $timeMillisecond;
	}
	


function timecodeDurationToMillisecond($timecode) {
		$hours = $minutes = $seconds = $milliseconds = 0;
		$timecode = substr($timecode,2,strlen($timecode)-3);
		
		$array_value = array();

		$array_value[] = substr($timecode,0,2);
		$array_value[] = substr($timecode,3,2);
		$array_value[] = substr($timecode,6,2);
		$array_value[] = substr($timecode,9,2);
		
		
		
		$timeMillisecond =0;
		$timeMillisecond += $array_value[0]*1000*60*60;
		$timeMillisecond += $array_value[1]*1000*60;
		$timeMillisecond += $array_value[2]*1000;
		$timeMillisecond += $array_value[3]*25;
		
		return $timeMillisecond;
	}
	
function timecodeShortDurationToMillisecond($timecode) {
		$hours = $minutes = $seconds = $milliseconds = 0;
		$timecode = substr($timecode,2,strlen($timecode)-3);
		
		

		//example PT0H0M3S51N100F
		
	//hour
		$hour = substr($timecode,0,(strpos($timecode,'H')));
		
		//minute
		$minute = substr($timecode,(strpos($timecode,'H')+1),(strpos($timecode,'M'))-(strpos($timecode,'H')+1));
		
		//second
		$second = substr($timecode,(strpos($timecode,'M')+1),(strpos($timecode,'S'))-(strpos($timecode,'M')+1));
		
		//frame
		$frame = substr($timecode,(strpos($timecode,'S')+1),(strpos($timecode,'N'))-(strpos($timecode,'S')+1));
		//framerate
		$framerate = substr($timecode,(strpos($timecode,'N')+1));
		
		
		
	/*	var_dump($hour);
		var_dump($minute);
		var_dump($second);
		var_dump($frame);
		var_dump($framerate);
		*/
		
		
		$timeMillisecond =0;
		$timeMillisecond += $hour*1000*60*60;
		$timeMillisecond += $minute*1000*60;
		$timeMillisecond += $second*1000;
		$timeMillisecond += round($frame/$framerate*1000);
		
		return $timeMillisecond;
	}
	
	function convertMillisecondToMp7Timecode($milliseconds){
		
		
			if ($milliseconds > 3600000) {
			  $hours = floor($milliseconds/3600000);
			  $hours = intTo2Digit($hours);
			  
			  $milliseconds -= ($hours*3600000);
			} else {
			  $hours = "00";
			}
			if ($milliseconds > 60000) {
			  $minutes = floor($milliseconds/60000);
			  $minutes = intTo2Digit($minutes);
			  $milliseconds -= ($minutes*60000);
			} else {
			  $minutes = "00";
			}
			if ($milliseconds > 1000) {
			  $seconds = floor($milliseconds/1000);
			  $seconds = intTo2Digit($seconds);
			  $milliseconds -= ($seconds*1000);
			} else {
			  $seconds = "00";
			}
			$frame = floor($milliseconds/(1000/24));
			  $frame = intTo2Digit($frame);
			$timecode = "T$hours:$minutes:$seconds:".$frame."F25";
			
			return $timecode;
					
		
		}
		
		function intTo2Digit($input){
			
			return substr("00".$input, -2);
			}
		
		function convertMillisecondToMp7Duration($milliseconds){
		
		
		
		if ($milliseconds > 3600000) {
			  $hours = floor($milliseconds/3600000);
			  $hours = intTo2Digit($hours);
			  
			  $milliseconds -= ($hours*3600000);
			} else {
			  $hours = "00";
			}
			if ($milliseconds > 60000) {
			  $minutes = floor($milliseconds/60000);
			  $minutes = intTo2Digit($minutes);
			  $milliseconds -= ($minutes*60000);
			} else {
			  $minutes = "00";
			}
			if ($milliseconds > 1000) {
			  $seconds = floor($milliseconds/1000);
			  $seconds = intTo2Digit($seconds);
			  $milliseconds -= ($seconds*1000);
			} else {
			  $seconds = "00";
			}
			$frame = floor($milliseconds/(1000/24));
			  $frame = intTo2Digit($frame);
			$timecode = "PT".$hours."H".$minutes."M".$seconds."S".$frame."N";
			
				return $timecode;
					
		}
	
function indexImage($image_name){
if($image_name=="") return "ERROR NO FILE";


		$runCall  = EW_UNIFI_SERVER_INDEX_IMAGE.$image_name;
			
    	$runCall = str_replace(" ", "%20",$runCall);
    	
    	//$log ="Calling service: ".$runCall;	
		//echo $log;
		//$result = "";
		$result = @file_get_contents($runCall);
		$log = $result;
		
		return $log;


}

function add_log($type,$text=""){
global $conn;
$log_activity = "INSERT INTO ".EW_DB_LOGGING_TABLE." (type,text) VALUES ('$type','".addslashes($text)."');";
if(EW_DB_LOGGING) $conn->Execute($log_activity);

}

function setProcessStatus($processId,$idProcessStatus){
	global $conn;

	//segnalo la fine del processo
	$query_update_status = "UPDATE process set idProcessStatus = '$idProcessStatus'  WHERE processId = '".$processId."'";
	$conn->Execute($query_update_status);
	
	//echo $query_update_status;
}

function setProcessStatusByNumId($processId,$idProcessStatus){
	global $conn;

	//segnalo la fine del processo
	$query_update_status = "UPDATE process set idProcessStatus = '$idProcessStatus' WHERE idProcessNum = '".$processId."'";
	$conn->Execute($query_update_status);
	
}

function setProcessOutputByNumProcessId($idProcessNum,$output){
	
		$processId =  ew_ExecuteScalar("SELECT processId from process WHERE  idProcessNum = '$idProcessNum'");

	     setProcessOutput($processId,$output);
	
	}



function setProcessOutput($processId,$output){
	global $conn;
	
	//echo "entrato send output";
	$rs = $conn->execute("SELECT * from process LEFT JOIN processstatus ON (process.idProcessStatus = processstatus.idProcessStatus) WHERE processId = '$processId'");
	//echo "ricercato processo";
	
	if($rs->RecordCount()==0)  return;
	
	$output_string = $rs->fields('output');
	//var_dump($output_string);
	
	$output_array = unserialize($rs->fields('output'));
	
	if(is_array($output_array)) {
		
		//echo "l'output è già un array e lo integro";
		$output_array = array_merge($output,$output_array);
	}
	else {
		 $output_array =  $output;
		 //echo "risultato vuoto. lo metto uguale a quello passato";
	
	}

	//segnalo la fine del processo
	$query_update_status = "UPDATE process set output = '".serialize($output_array)."', end = now() WHERE processId = '$processId'";
	
	$conn->Execute($query_update_status);
	//echo "risultati salvati con query: $query_update_status ";
	
	
}


function setProcessEnd($processId,$result = null){
	global $conn;
	
	add_log('setProcessEnd',"Imposto processo $processId come completato");
	setProcessStatus($processId,EW_END_PROCESS_STATUS);
	add_log('setProcessEnd',"Operazione eseguita");
	
	//verifico se il processo terminato era l'ultimo di una pipeline e chiudo anche la pipeline
	
	//recupero l'id del processo principale
	$idProcessNumMaster =  ew_ExecuteScalar("SELECT idProcessNumMaster from process WHERE processId = '".$processId."'");
	if($result != null) {
		echo "Aggiungo risultati al processo master";	
		add_log('setProcessEnd',"Aggiungo risultati al processo master");
		setProcessOutputByNumProcessId($idProcessNumMaster,$result);
		echo "Aggiunti risultati al processo master";	
			add_log('setProcessEnd',"Aggiunti risultati al processo master");
}
	
	//cerco quanto processi figli non sono completi
	$num_process_running = ew_ExecuteScalar("select count(*) from process WHERE (idProcessStatus <> '".EW_END_PROCESS_STATUS."') AND (idProcessNumMaster = '$idProcessNumMaster' ) AND idProcessNum <> idProcessNumMaster");
	echo "processi attivi sulla pipeline: $num_process_running";
	//caso in cui anche tutti i sotto processi siano terminati metto su terminato anche il processo principale 
	if(!$num_process_running) {
			echo "chiudo pipeline: $idProcessNumMaster";
			setProcessStatusByNumId($idProcessNumMaster,EW_END_PROCESS_STATUS);
			add_log('setProcessEnd',"Imposto processo pipeline idProcessNumMaster $idProcessNumMaster come completato");
	
	
	}
	
	
	
}


function runPendingProcess(){

global $conn;


$pid_query = "select *  from process JOIN setprocess ON ( process.idSetProcess = setprocess.idSetProcess ) WHERE (idProcessStatus = 2 OR idProcessStatus = 3) AND (idProcessNumMaster <> idProcessNum OR idProcessNumMaster is null)";
		$rswrk = $conn->Execute($pid_query);
		
		
$num_process_running =  $rswrk->RecordCount();

spool_video_all_folders();

spool_document();

downloadPendingImages();

checkFinishedTraining();

addNewConcept();

$log="";



if($num_process_running) {  ///PARTE DI CONTROLLO SE I PROCESSI ATTIVI SONO ANCORA IN ESECUZIONE O IN TIMEOUT - INIZIO

		echo("$num_process_running Processes  running. <br>");
		
		while(!$rswrk->EOF) {
					//PsExec("ls /var/www/octo/");
					echo("Stampa controllo esecuzione processo <br>"); 
					$pid = $rswrk->fields("OSProcessId");
					
					
					//verifica timeout
					$to_time=time();
					$from_time=strtotime($rswrk->fields("start"));
					$second_in_execution= round(abs($to_time - $from_time));
					
					echo("Controllo timeout. Processo in esecuzione da ".$rswrk->fields("start")." -> $second_in_execution secondi.<br>");
					
					switch ($rswrk->fields('idProcessType')) {
						    case "1"://caso di un eseguibile
									$timeout_limit = 60 * 60; //60 minuti
							   break;
						    case "2"://caso di un web service http
						    
						    					$timeout_limit = 2 * 60; //2 minuti
						    
						       break;
						    case "3"://caso di un JSONRPC
						    
						    $timeout_limit = 60 * 60; //60 minuti
										
							break;
					}

					
					
					
					if($second_in_execution>$timeout_limit) {
						echo "Processo andato in timeout (più di $timeout_limit secondi). Fermato.";
						//setProcessEnd($rswrk->fields("processId"));
						setProcessStatus($rswrk->fields("processId"),6);
					}
					
					else switch ($rswrk->fields('idProcessType')) {
						    case "1"://caso di un eseguibile
									echo "Controllo esecuzione eseguibile<br>";
					
					
									//eseguo la chiamata per verificare se il processo è ancora in esecuzione
									$exeCall = "ps --pid ".$pid;
									$result = exec($exeCall);
									//echo "primo <pre>".$result."</pre>";
									if($result =="  PID TTY          TIME CMD") 
										{
													//il processo non è più attivo. lo blocco dal gestionale
													$log_terminate = "Il processo non è più ".$pid." attivo ";
													add_log('runPendingProcess:finishing',$log_terminate);
													setProcessEnd($rswrk->fields("processId"));
										}
					
									else echo "il processo ".$pid." attivo<br>";
							
							   break;
						    case "2"://caso di un web service http
						    
						    						echo "Controllo esecuzione web service http<br>";
						    
						    						$to_time=time();
												$from_time=strtotime($rswrk->fields("start"));
												$second_in_execution= round(abs($to_time - $from_time));
												
												echo("Controllo timeout. Processo in esecuzione da ".$rswrk->fields("start")." -> $second_in_execution secondi.<br>");
												
												if($second_in_execution>120) {
													echo "Processo andato in timeout (più di 120 secondi). Fermato.";
													//setProcessEnd($rswrk->fields("processId"));
													setProcessStatus($rswrk->fields("processId"),6);
												}
						    
						       break;
						    case "3"://caso di un JSONRPC
						    
						    
						    			
						    
						    				echo "Controllo esecuzione web service JSON-RPC (con getResults)<br>";

										$processId = (int)$rswrk->fields("processId"); 
										$target = EW_VIDEODROME_SERVER_URL;
										$request = Tivoka::createRequest(56, 'getResults',array('processId'=>$processId));
										
										Tivoka::connect($target)->send($request);
										
										if($request->isError())
											{
												// an error occured
												var_dump($request->error);
												var_dump($request->errorMessage);
												var_dump($request->response);
											}
											else {
												echo "<br>Stato processo: ".$request->result['status']."<br> log: ";
												var_dump($request->result);
											}
										
										
										switch (strtolower($request->result['status'])) {
										    case "completed":
										    			echo "<br>PROCESSO COMPLETATO ";
													echo "Output:<pre>";
													var_dump($request->result['output']);
													echo "</pre>";
											  		setProcessOutput($rswrk->fields("processId"),$request->result['output']);
													setProcessEnd($rswrk->fields("processId"));
													
											   break;
										    case 'not found':
										    	echo "<br>PROCESSO NON TROVATO (FERMATO) ";
												 setProcessEnd($processId);
											   break;
										    
										    default:
											  echo "Process running";
										}
										
										
										
							break;
					}

												
							
					$rswrk->MoveNext();
		}
		
		
		
		
		
}   ///PARTE DI CONTROLLO SE I PROCESSI ATTIVI SONO ANCORA IN ESECUZIONE O IN TIMEOUT - FINE
echo "terminato controllo processi in esecuzione";
echo "limite processi in parallelo: ".EW_MAX_PROCESS_IN_EXECUTION;




if($num_process_running < EW_MAX_PROCESS_IN_EXECUTION)  { // caso di nessun processo in esecuzione->quindi cerco se ce n'è qualcuno da eseguire

						$select_query = "SELECT * FROM process JOIN setprocess ON ( process.idSetProcess = setprocess.idSetProcess ) WHERE idProcessStatus =1 OR  idProcessStatus =8  ORDER BY process.priority,idProcessNum";
						
						$rswrk = $conn->Execute($select_query);
						
						while(!$rswrk->EOF){// esiste un processo da eseguire
						
						
						//controllo se il processo non è un sotto processo di una coda già in esecuzione
						$pending_previous = ew_ExecuteScalar("SELECT count(*) FROM process WHERE idProcessNumMaster = '".$rswrk->fields('idProcessNumMaster')."' AND (idProcessStatus = 2 OR idProcessStatus = 3) AND (idProcessNumMaster <> idProcessNum)");
						
						if($pending_previous) {
							$rswrk->MoveNext();
							 continue;
						}
						
						//metto il servizio su starting
						$query_update_status = "UPDATE process set idProcessStatus = 2, start = now() WHERE idProcessNum = ".$rswrk->fields("idProcessNum");
						$conn->Execute($query_update_status);
						add_log('runPendingProcess:starting process',"INIZIALIZZATO PROCESSO ".$rswrk->fields("idProcessNum"));
						
						//aumento il numero di processi in esecuzione
						$num_process_running++;
						
						
						//caso del processo master di un multiprocess
						if($rswrk->fields('idProcessNumMaster')==$rswrk->fields('idProcessNum')){ 
							echo ("Pipeline id ".$rswrk->fields("idProcessNum")." running"); 
							runPendingProcess();
							return;
						}
						
						switch ($rswrk->fields('idProcessType')) {
						    case "1"://caso di un eseguibile

									$command_to_append = "lynx -dump http://".EW_ABS_HOST."/eutv-tools/process/service/lib/setProcessEnd.php?processId=".$rswrk->fields("processId");
									$command_complete = "( ".$rswrk->fields("command")." ; ".$command_to_append." )";
									
									$log .="<br>Report IdProcess: ".$rswrk->fields("idProcessNum")." <br><br>The command executed is: "
									. $command_complete . " on server ".$rswrk->fields("server")."<br><br>.";
									
									$result = PsExec($command_complete);
									
									//aggiorno l'OSProcessId eseguito
									$query_update_status = "UPDATE process set OSProcessId ='".$result."' WHERE idProcessNum = ".$rswrk->fields("idProcessNum");
									$conn->Execute($query_update_status);
								

							   break;
						    case "2"://caso di un web service http
										add_log('runPendingProcess:startingwebservice',"URL executed:".$rswrk->fields("command"));
										$runCall = "".$rswrk->fields("command");
										$log .="\n<br><br>Report IdProcess: ".$rswrk->fields("idProcessNum")." <br><br>The command (service) executed is: "
										. $rswrk->fields("command") . " of service ".$rswrk->fields("service").".";
										$runCall = str_replace(" ", "%20",$runCall);
										
										$log .="Calling service: ".$runCall;	
											
										//$result = "";
										$result = file_get_contents($runCall);
										$log .= $result;
								break;
						    case "3"://caso di un JSONRPC
										add_log('runPendingProcess:startingwebserviceJSONRPC',"URL executed:".$rswrk->fields("server"));
										
										
										


				$method = "";
										
										
										//caricamento parametri processo
				$sSqlWrk = "SELECT * from processparams as pp JOIN processparamsvalue as ppv ON (pp.idProcessParams = ppv.idProcessParams) WHERE idProcessNum = ".$rswrk->fields("idProcessNum")."";
				$rswrk_params = $conn->Execute($sSqlWrk);
										while(!$rswrk_params->EOF) {
											
											//recupero il valore della variabile
											$value = $rswrk_params->fields('value');
										
											//recupero il nome della variabile
											$var_name = $rswrk_params->fields('code');

											if($var_name=="method") $method = $value;
											else
											{ 
											echo "ANALISI parametro $var_name con valore $value<br><br>";
											
											//faccio alcuni controlli
											if($value == "true") {
												echo "Parametro $var_name di tipologia true<br>";
												$value = true;
												}
												
											elseif($value == "false") {
														echo "Parametro $var_name di tipologia false<br>";
														$value = false;
											}
											
											elseif(is_numeric($value)) {
												echo "Parametro $var_name di tipologia int<br>";
												$value = (int)$value;
											}
											
											elseif($value=='MEDIA_NAME_DB'){
												echo "<br>Entrato valore speciale MEDIA_NAME_DB";
												$id_media = ew_ExecuteScalar("SELECT value FROM  processparams as pp JOIN processparamsvalue as ppv ON (pp.idProcessParams = ppv.idProcessParams) WHERE idProcessNum = ".$rswrk->fields("idProcessNum")." AND code = 'id_media'");
												
												echo "<br>Recuperato id_media$id_media ";
												$conn->Execute("SET SESSION group_concat_max_len = 1000000;");

												$list_image = ew_ExecuteScalar("SELECT GROUP_CONCAT(distinct thumbnail) FROM ".EW_CONN_DB_MEDIA.".annotations WHERE id_media = '$id_media'");
												//echo "<br>Recuperata lista immagini <em>$list_image</em>";

												if($list_image!="")$array_images = explode(',',$list_image);
												else $array_images = array();
												
												echo "<br><br>Array immagini:<br><em>".serialize($array_images)."</em><br><br><br>";

												
												$value = $array_images;
												}
												
											}
											echo "<br><br>Inserisco il parametro $var_name con valore:<br><em>".serialize($value)."</em><br><br><br>";
											$params_input[$var_name] = $value;
											
											
												
											$rswrk_params->MoveNext();
						}
		
										
//eseguo l'analisi

$params_input['processId'] = (int)$rswrk->fields("processId");

$target = EW_VIDEODROME_SERVER_URL;

echo "<br>Method: $method<br>Params: <pre>";
var_dump($params_input);
echo "</pre>";

$request = Tivoka::createRequest(1, $method,$params_input);
start_crono('analysis');
 add_log('analyzeBowImage:analysis',"Analysis started. Method: $method - Params input: ".serialize($params_input));

Tivoka::connect($target)->send($request);
 

if($request->isError())
{
	// an error occured
	var_dump($request->error);
	var_dump($request->errorMessage);
	var_dump($request->response);
}
else if($debug) var_dump($request->result);// the result
?> </pre><br /><br />
<?

										
										
										
										
										
										
										
										
										
										
										
										
										
										
								break;
						    default:
						}
						
						
						echo $log;
						
						add_log('runPendingProcess:starting REPORT',$log);
						
						
						//se sono al numero massimo di processi in esecuzione contemporanea non ne eseguo più
						if($num_process_running >= EW_MAX_PROCESS_IN_EXECUTION) {
							echo "Numero di processi massimi in esecuzione";
							break;
						}
						
						//ho eseguito il primo programma in esecuzione
						
						$rswrk->MoveNext();
						
			
			} 
			
			if($rswrk->RecordCount()==0) 
					{
						echo ("<br><br>No process pending.");	
						//sendTweet(date(DATE_RFC822)." - No process pending");
						syncroNewConcept();
					}		

		/*$log_activity = "INSERT INTO logs (type,text) VALUES ('runPendingProcess:test_cron','test call');";
		$conn->Execute($log_activity);
*/

}	//fine caso di esecuzione processo






}

function downloadPendingImages(){

global $conn;



$log="";

$num_images_pending = ew_ExecuteScalar("select count(*) from training_images WHERE (processed_status = 0)");

echo "<br>$num_images_pending remaining to download<br>";
if($num_images_pending) {
	
	$image_to_download =EW_FLICKR_IMAGE_DOWNLOADED;
	$rs = $conn->Execute("SELECT * from training_images WHERE (processed_status = 0) LIMIT 0,$image_to_download");
	
	$rs->MoveFirst();
	while (!$rs->EOF) {

			 $rs->fields('title');
			 
			 $dest_file_path = EW_TRAINING_IMAGE_PATH.$rs->fields('filename');

				$command = 'wget "'.$rs->fields('url').'" -O "'.$dest_file_path.'"';
				echo "command executed: $command <br>";
				$result = exec($command);
				
				$conn->Execute("UPDATE training_images  set processed_status = 1 WHERE id = '".$rs->fields('id')."'");

			
			$rs->MoveNext();
		
		}

		echo "scaricate $image_to_download immagini";

}


}



function checkFinishedTraining(){

global $conn;



$log="";

$num_images_pending = ew_ExecuteScalar("select count(*) from training_concepts WHERE (id_training_concepts_status 	 = 3)");

echo "$num_images_pending training concepts processing<br>";
if($num_images_pending) {
	
	$image_to_download =EW_FLICKR_IMAGE_DOWNLOADED;
	$rs = $conn->Execute("SELECT * from training_concepts WHERE (id_training_concepts_status 	 = 3 ) ");
	
	$rs->MoveFirst();
	while (!$rs->EOF) {

			 
			 $pid = $rs->fields('processId');
			 
			 if(PsExists($pid)) echo "<br> Il concetto ".$rs->fields('name')." con pid: $pid è ancora in fase di processing";
			 
			 else {
				 
				 echo "<br> Il concetto ".$rs->fields('name')." con pid: $pid ha terminato il processing";
				// die();
				 
				 $conn->Execute("UPDATE training_concepts  set id_training_concepts_status = 4,  end_processing = NOW() WHERE id = '".$rs->fields('id')."'");
				 
				 }
			 
			 

			
			$rs->MoveNext();
		
		}


}


}



function syncroNewConcept(){

global $conn;



$log="";
$rs = $conn->Execute("SELECT * from ".EW_CONN_DB_MEDIA.".annotations 
			LEFT JOIN ".EW_CONN_DB_MEDIA.".media as m ON (m.id_media = annotations.id_media)  WHERE sync = 0 GROUP BY m.id_media ");
	
	
	if($rs->RecordCount()) {
				$rs->MoveFirst();
				while (!$rs->EOF) {
							$id_media = $rs->fields('id_media');
			
							addMediaAnnotationXml($id_media);
							syncAnalysisMpeg7($id_media);
							$conn->Execute("UPDATE ".EW_CONN_DB_MEDIA.".annotations set sync = 1 WHERE id_media = '$id_media'"); 
							echo	" <br>aggiornate annotazioni id_media $id_media <br>";
						
			
						
						$rs->MoveNext();
					
					}
				}
		
		else  echo "<br> Tutte le annotazioni sono sincronizzate<br>";


}






function getVideoAnnotationMp7($id_media,$compact = false){
	
			global $conn;
			
			
			

$query = "SELECT * from ".EW_CONN_DB_MEDIA.".media WHERE media.id_media = '$id_media '";
$rs_media = $conn->Execute($query);
								



if(!$compact) $xml_output = '<Mpeg7 xmlns="urn:mpeg:mpeg7:schema:2001" xmlns:mpeg7="urn:mpeg:mpeg7:schema:2001" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:mpeg:mpeg7:schema:2001 Mpeg7-2001.xsd">
<Description xsi:type="ContentEntityType">
<MultimediaContent xsi:type="VideoType">
<Video id="/var/www/eutv-data/media/data/'.$rs_media->fields('filename').'.mp7.xml">
<MediaLocator>
<MediaUri>'.$rs_media->fields('filename').'</MediaUri>
</MediaLocator>';


$xml_output .= '<TemporalDecomposition gap="false" overlap="false">';



$query = "SELECT * FROM ".EW_CONN_DB_MEDIA.".annotations WHERE annotations.id_media = '$id_media' and deleted = 0 GROUP BY  timepoint,endpoint ORDER BY timepoint,endpoint";

									
								$rs = $conn->Execute($query);
								$num=0;
								while(!$rs->EOF){
								
								$num++;
								
								

				$row = $rs->fields;

	

				
				$durationMM = $row['endpoint']-$row['timepoint'];
				$timePoint = convertMillisecondToMp7Timecode($row['timepoint']);
				
				debug_var($row['timepoint']);
				debug_var($timePoint);
				 
				$duration = convertMillisecondToMp7Duration($durationMM);
				if($row["latitude"] != ''){
					$tagged = 'Y';
				}else{
					$tagged = 'N';
				}
				
				
				$xml_output .='<VideoSegment id="shot'.$num.'">
<MediaLocator>
<MediaUri>'.EW_IMAGE_HTTP_URL.$row["thumbnail"].'</MediaUri>
</MediaLocator>
<TextAnnotation>';

				//scorro tutte le annotazioni all'interno dello shot
				
				$query = "SELECT *, ct.value as concepttype,  ifnull(at.name,'Other') as type FROM ".EW_CONN_DB_MEDIA.".annotations LEFT JOIN ".EW_CONN_DB_MEDIA.".concepts as c ON (c.id_concepts = annotations.id_concepts) 
							LEFT JOIN ".EW_CONN_DB_MEDIA.".concept_types as ct ON (c.id_concept_types = ct.id_concept_types) 
							LEFT JOIN ".EW_CONN_DB_MEDIA.".annotations_types as at ON (annotations.id_annotations_types = at.id_annotations_types) 
							WHERE annotations.id_media = '$id_media' and deleted = 0 AND  timepoint = '".$row["timepoint"]."' AND endpoint = '".$row["endpoint"]."' ";

									
								$rs_ann = $conn->Execute($query);
								while(!$rs_ann->EOF){
									
									$row_ann = $rs_ann->fields;
									
									$xml_output .='<FreeTextAnnotation type="'.$row_ann["type"].'" concepttype="'.$row_ann["concepttype"].'" >'.$row_ann["title"].'</FreeTextAnnotation>
									';
								
									$rs_ann->MoveNext();
								}
								
								$xml_output .='</TextAnnotation>
<MediaTime>
<MediaTimePoint>'.$timePoint.'</MediaTimePoint>
<MediaDuration>'.$duration.'</MediaDuration>
</MediaTime>
</VideoSegment>
';
								
					$rs->MoveNext();			
				
					}
					
						$xml_output .='</TemporalDecomposition>';
						
						
if(!$compact) $xml_output .='</Video>
</MultimediaContent>
</Description>
</Mpeg7>
';
				
				
				return $xml_output;
				
				}



function getMediaAnnotationMp7($id_media){
	
			global $conn;
			
			$query = "SELECT * from ".EW_CONN_DB_MEDIA.".media WHERE media.id_media = '$id_media '";
			$rs_media = $conn->Execute($query);
			
			if($rs_media->fields('id_media_types') == "1") return getVideoAnnotationMp7($id_media,true);
			if($rs_media->fields('id_media_types') == "2") return getImageAnnotationMp7($id_media);
			return ;
					
	
}

function getImageAnnotationMp7($id_media){
	
			global $conn;
			

$query = "SELECT * from ".EW_CONN_DB_MEDIA.".media WHERE media.id_media = '$id_media '";
$rs_media = $conn->Execute($query);
								
$xml_output ='<Mpeg7Fragment>
<TextAnnotation>';

			
		$image_name = $rs_media->fields('filename');

				//scorro tutte le annotazioni all'interno dello shot
				
				$query = "SELECT *, ifnull(ct.value,'Other')  as concepttype, ifnull(at.name,'Other') as type FROM ".EW_CONN_DB_MEDIA.".annotations LEFT JOIN ".EW_CONN_DB_MEDIA.".concepts as c ON (c.id_concepts = annotations.id_concepts) 
							LEFT JOIN ".EW_CONN_DB_MEDIA.".concept_types as ct ON (c.id_concept_types = ct.id_concept_types) 
							LEFT JOIN ".EW_CONN_DB_MEDIA.".annotations_types as at ON (annotations.id_annotations_types = at.id_annotations_types) 
							WHERE annotations.id_media = '$id_media' and deleted = 0".
							" AND (
ct.id_concept_types IS NULL
OR ct.id_concept_types <>5
)";

									
								$rs_ann = $conn->Execute($query);
								while(!$rs_ann->EOF){
									
									$row_ann = $rs_ann->fields;
									
									$xml_output .='<FreeTextAnnotation type="'.$row_ann["type"].'" concepttype="'.$row_ann["concepttype"].'" >'.$row_ann["title"].'</FreeTextAnnotation>
									';
								
									$rs_ann->MoveNext();
								}
								
						$xml_output .='</TextAnnotation>';		
								
								
						include_once("colors.inc.php");
						$ex=new GetMostCommonColors();
						$ex->image=EW_ABS_MEDIA_PATH."image/".$image_name."";
						$colors=$ex->Get_Color();
						
						$how_many=1;
						$colors_key=array_keys($colors);
						global $main_colors;
						//echo "Trovati colori dominanti:<br>";
						//var_dump($colors);
						
						for ($i = 0; $i < $how_many; $i++)
						{
						    $string_dominant_color = $main_colors[$colors_key[$i]];
							$xml_output .= "\n<Color type=\"DominantColor\"  color=\"$string_dominant_color\" >".$colors_key[$i]."</Color>";
						}

						$xml_output .='</Mpeg7Fragment>';		
				//echo "Mpeg7 da inviare:<br>".$xml_output;

				return $xml_output;
				
				}

	
function getMediaAnnotationXml($id_media){
	
			global $conn;
			
			$conn->Execute("SET SESSION group_concat_max_len = 1000000;");
			
			$tags = ew_ExecuteScalar("SELECT GROUP_CONCAT( distinct '<annotation concepttype=\"',ct.value,'\">',title,'</annotation>' SEPARATOR '\n') 
			from ".EW_CONN_DB_MEDIA.".annotations 
			LEFT JOIN ".EW_CONN_DB_MEDIA.".concepts as c ON (c.id_concepts = annotations.id_concepts) 
			LEFT JOIN ".EW_CONN_DB_MEDIA.".concept_types as ct ON (c.id_concept_types = ct.id_concept_types) 
			
			WHERE id_media = '$id_media' ");
			
			debug_var($tags);
			
			return "<annotations>
			".
			$tags.
			"</annotations>"
			;

	}
	
	
	
	
	
	
	
	
	function addMediaAnnotationXml($id_media) {
						global $conn,$session;
						
						if(!isset($session)) $session = connectHkuSoap();
						
						$uri = ew_ExecuteScalar("SELECT uri from ".EW_CONN_DB_MEDIA.".media WHERE id_media = '$id_media'");
						
						if($uri!="") {
							$annotations_xml = getMediaAnnotationXml($id_media);
							
							$esempio_artifact_annotation = array(
							   'rawData' => $annotations_xml,
							   'sourceId' => $uri,
							   'schemaName' => 'unifiAnnotation'
							);
							
							$success = addArtifacts($esempio_artifact_annotation);
							
							if ($success) {
								echo "File registrato:";
								add_log('addResult:processing',"Unifi Annotation artifact inserted correctly.  $annotations_xml");
							}
						} else add_log('addResult:processing',"Unifi Annotation artifact error. uri not found for id_media $id_media");
					
				}

	function syncAnalysisMpeg7($id_media) {
						global $conn,$session,$client;
						
						
					 $session = connectHkuSoap();
						
						
									
			$query = "SELECT * from ".EW_CONN_DB_MEDIA.".media WHERE media.id_media = '$id_media '";
			$rs_media = $conn->Execute($query);
			$uri = $rs_media->fields('uri');
			if($uri==""){
				
				echo "SourceId not found";
				return;
				}
			
			
			if($rs_media->fields('id_media_types') == "1") { //caso del video
			
			
			$contenuto_estratto = getVideoAnnotationMp7($id_media, true); 

			$esempio_analysis = array(
			   'rawData' => $contenuto_estratto,
			   'sourceId' => $uri,
			   'schemaName' => 'VIDEO_SEGMENTATION'
			);
			
			
			echo "richiesta registrazione:<br><pre>";
			print_r(array('session'=>$session,'analysis' => $esempio_analysis,'overwrite' => true));
			echo "</pre>";
			$result = $client->call('addAnalysis',array('session'=>$session,'analysis' => $esempio_analysis,'overwrite' => true),EW_HKU_SOAP_NAMESPACE);
			
			if ($client->fault) {
				echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
			} else {
				$err = $client->getError();
				if ($err) {
					echo '<h2>Error</h2><pre>' . $err . '</pre>';
				} else {
					echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
				}
			}
			
					if (!$client->fault) {
						echo "File registrato:";
						print_r($result);
			
					}
				
				
				
				
				
			}
			
			
			if($rs_media->fields('id_media_types') == "2") {
				
				$contenuto_da_inviare = getImageAnnotationMp7($id_media);
								
					$esempio_analysis = array(
					   'rawData' => $contenuto_da_inviare,
					   'sourceId' => $uri,
					   'schemaName' => 'IMAGE_ANALISYS'
					);
					
					
					echo "richiesta registrazione:<br><pre>";
					print_r(array('session'=>$session,'analysis' => $esempio_analysis,'overwrite' => true));
					echo "</pre>";
					
					$result = $client->call('addAnalysis',array('session'=>$session,'analysis' => $esempio_analysis,'overwrite' => true),EW_HKU_SOAP_NAMESPACE);
							
					if ($client->fault) {
						echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
					} else {
						$err = $client->getError();
						if ($err) {
							echo '<h2>Error</h2><pre>' . $err . '</pre>';
						} else {
							echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
						}
					}
							
				
 					add_log('addResult:processing',"Analysis. added for sourceId $uri  $id_media");
					
				}
	}

	
function addArtifacts($esempio_artifact){
	
	
	
	
	global $client;
					echo "richiesta sessione:<br>";
					$session = connectHkuSoap();
						
										echo "sessione aperta<br>";

					$request = array('session'=>$session,'artifact' => $esempio_artifact, 'overwrite' => true);
					echo "richiesta registrazione:<br><pre>";
					print_r($request);
					echo "</pre>";
					$result = $client->call('addArtifact',$request,EW_HKU_SOAP_NAMESPACE);
					echo "<br>richiesta fatta";
					
					if ($client->fault) {
						echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
					} else {
						$err = $client->getError();
						if ($err) {
							echo '<h2>Error</h2><pre>' . $err . '</pre>';
						} else {
							echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
						}
					}
					
					return (!$client->fault);
					
	
	}
	
	
	function addAnalysis($esempio_analysis){
	
	
	
	
	global $client;
					echo "richiesta sessione:<br>";
					$session = connectHkuSoap();
						
					echo "sessione aperta<br>";
					
					$request = array('session'=>$session,'analysis' => $esempio_analysis,'overwrite' => true);

					echo "richiesta registrazione:<br><pre>";
					print_r($request);
					echo "</pre>";
					
					$result = $client->call('addAnalysis',$request,EW_HKU_SOAP_NAMESPACE);

					
					echo "<br>richiesta fatta";
					
					if ($client->fault) {
						echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
					} else {
						$err = $client->getError();
						if ($err) {
							echo '<h2>Error</h2><pre>' . $err . '</pre>';
						} else {
							echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
						}
					}
					
					return (!$client->fault);
					
	
	}
	
	
	
	
	

function addNewConcept(){

global $conn;



$log="";

$num_images_pending = ew_ExecuteScalar("select count(*) from training_concepts WHERE (id_training_concepts_status 	 = 4)");

echo "$num_images_pending training concepts to add<br>";
if($num_images_pending) {
	
	$image_to_download =EW_FLICKR_IMAGE_DOWNLOADED;
	$rs = $conn->Execute("SELECT * from training_concepts WHERE (id_training_concepts_status 	 = 4 ) ");
	
	$rs->MoveFirst();
	while (!$rs->EOF) {

			 
			 $pid = $rs->fields('processId');
			 
			 
				 
				 echo "<br> Il concetto ".$rs->fields('name')." con pid: $pid ha terminato il processing";
				 
				 $name = $rs->fields('name');
				 
				 $bow_svm_models = EW_SHOTANALYZER_PATH."/data/BoWNew/bow_svm_models.txt";
				 append_txt($name.",".$name."/",$bow_svm_models);
					 
				$file_bow_synonyms = EW_SHOTANALYZER_PATH."/data/BoWNew/bow_synonyms.txt";
				 
				 append_txt($name,$file_bow_synonyms);
				 
			 
				 
				 $conn->Execute("UPDATE training_concepts  set id_training_concepts_status = 5 WHERE id = '".$rs->fields('id')."'");
				 

			 
			 

			
			$rs->MoveNext();
		
		}


}


}


    function PsExec($commandJob) {

        $command = $commandJob.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $pid = (int)$op[0];

        if($pid!="") return $pid;

        return false;
    }
    
    
    function PsExists($pid) {

        exec("ps ax | grep $pid 2>&1", $output);
	   //var_dump($output);

        while( list(,$row) = each($output) ) {

                $row_array = explode(" ", $row);
			 //echo "<pre>"; var_dump($row_array);echo "</pre>"; 
                $check_pid = $row_array[0];
                $check_pid_1 = $row_array[1];

                if($pid == $check_pid || $pid == $check_pid_1) {
                        return true;
                }

        }

        return false;
    } 


function append_txt($string,$file_txt){
$file=fopen($file_txt,"a");
		fseek($file,0);
		fputs($file,$string."\n");
		fclose($file);
}



function spool_video_all_folders(){
	global $conn;
	
	spool_video();
	
	$rs = $conn->Execute(" SELECT * from videodromeparams ");
	
	$rs->MoveFirst();
	while (!$rs->EOF) {

			 
			 $name = $rs->fields('name');
			 
			 spool_video($name);
			$rs->MoveNext();
		
		}
	
	
	$media_path = (defined("EW_ABS_MEDIA_PATH")?EW_ABS_MEDIA_PATH:EW_ABS_PATH.EW_SEPARATOR.'media'.EW_SEPARATOR);
	
}


function spool_video($sub_folder = ""){
	
	$media_path = (defined("EW_ABS_MEDIA_PATH")?EW_ABS_MEDIA_PATH:EW_ABS_PATH.EW_SEPARATOR.'media'.EW_SEPARATOR);
	
	

$image_file_path = $media_path.'spool_video'.EW_SEPARATOR; //- this is the full server path to your images folder

if($sub_folder != "") $image_file_path = $image_file_path.$sub_folder.EW_SEPARATOR;

$image_file_path_dest = $media_path.'video'.EW_SEPARATOR; //- this is the full server path to your images 

if(!is_dir($image_file_path)) {
	
	echo "<br><br>Wrong path: $image_file_path";
	return false;

	
}


//echo "<br>spool video in folder $image_file_path";
$d = dir($image_file_path) or die("Wrong path: $image_file_path");

$file_trovati = false;

while (false !== ($entry = $d->read())) {
if($entry != '.' && $entry != '..' && !is_dir($image_file_path.$entry))
	{
		$filename = $entry;
		
		echo "Found video: ". $entry."<br>";
		
		 $file_path_to_copy = $image_file_path.$filename;
		 //die($file_path_to_copy);
		
		$filename_path = $image_file_path.$entry;
	   
	   $returnvalue = shell_exec("exiftool ".$filename_path);
	   $array_media_info =  MediaInfoToArray($returnvalue); 
	   
	   $array_media_info['file_size_byte'] = filesize($filename_path);

		
		
		$rand_suff = "_".rand(100000,999999);
		$path_info = pathinfo($filename);
       // print_r($path_info); //['extension'];
	  
	  $file_name_correct =  $path_info['filename']; 
	  	  $file_name_correct = str_replace(' ','_',$file_name_correct);
	  	  $file_name_correct = str_replace('(','',$file_name_correct);
	  	  $file_name_correct = str_replace(')','',$file_name_correct);
	  	  $file_name_correct = str_replace('.','_',$file_name_correct);

	   
	   $dest_file = $file_name_correct.$rand_suff.".".$path_info['extension'];
	   
	  
	   
	   rename($file_path_to_copy,$image_file_path_dest.$dest_file);
	   echo "Copiato ".$file_path_to_copy." ->in  file: ". $image_file_path_dest.$dest_file."<br>";
	   
	   
	   
	   
	   
	   
	   
	   $video_name = $dest_file; 

$path_parts = pathinfo($video_name);

$basename =  $path_parts['filename']; // since PHP 5.2.0


if(!EW_HKU_CONNECT_REPOSITORY) { $octo_id= rand_str(6);}

else {

global $conn,$client;
$client = null;
require_once('./nusoap/nusoap.php');
$session = connectHkuSoap();



echo "<h1>Register video</h1>";

$esempio_video = array(
   'sourceType' => 'VIDEO',
   'url' => EW_VIDEO_HTTP_URL.$video_name,
   'fileSize' => $array_media_info['file_size_byte'],
   'mimetype' => $array_media_info['MIME Type']
);

//$esempio_request = array('file' => $esempio_video);


$result = $client->call('doRegisterSource',array('session'=>$session,'file'=>$esempio_video),EW_HKU_SOAP_NAMESPACE);

if ($client->fault) {
	echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
} else {
	$err = $client->getError();
	if ($err) {
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	} else {
		echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
	}
}

if (!$client->fault) {
echo "File registrato con id:".$result['sourceId'];

}
else die("Inserimento bloccato per un errore");


//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
//echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';


$octo_id = $result['sourceId'];

}
// $video_name = $_GET['video_name']; //already called
//die("inizio processamento");


?>
<?php
define("EW_DEBUG_ENABLED", TRUE, TRUE);

// Open connection to the database
$conn = ew_Connect();


$fps = 25;
if($array_media_info['Video Frame Rate']!="0" &&$array_media_info['Video Frame Rate']!="") $fps =  $array_media_info['Video Frame Rate'];

$query_insert = "INSERT INTO ".EW_CONN_DB_MEDIA.".media (dataserverpath,mediauri,uri,created,modified,filename,title,fps,id_media_types) VALUES ('".EW_VIDEO_RTMP_URL."', '".$basename."', '".$octo_id."',now(),now(), '".$video_name."', '".$basename."', '".$fps."',1);";
					
					$conn->Execute($query_insert);
					$id_media = mysql_insert_id();
		

$insert_process = true;

$log = "File inserted video-name=".$video_name." SourceId".$octo_id."\n";
echo $log;
//sendTweet("Video analisys starting: Video: $video_name - SourceId: $octo_id");		



if(@$insert_process) {		

		$suffix_url = ($sub_folder!=""?"&videodrome_set=$sub_folder":"");
		// EXECUTING VIDEO ANALYZING PROCESS
		$runCall = EW_URL_LOCALHOST."process/launchProcess.php?a_add=A&idSetProcess=4&video-name=".$video_name."&uri=".$octo_id."&mode=ws&id_media=".$id_media."&basename=".$basename.$suffix_url;
		echo "called service: ".$runCall."<br>" ;
		//$result = "only test<br>";	
		$result = file_get_contents($runCall);
		echo $result ;
		}

		add_log('spoolVideo:inserting',addslashes($log));

		$file_trovati = true;
		
		break;
	}
}
$d->close();

if(!$file_trovati) echo "<br>No videos found ($sub_folder)";

return $file_trovati;

}

function spool_image(){
			
			$media_path = (defined("EW_ABS_MEDIA_PATH")?EW_ABS_MEDIA_PATH:EW_ABS_PATH.EW_SEPARATOR.'media'.EW_SEPARATOR);
			$image_file_path = $media_path.'spool_image'.EW_SEPARATOR; //- this is the full server path to your images folder
			//echo "image file path: $image_file_path ";
			$image_file_path_dest = EW_IMAGE_PATH; //- this is the full server path to your images 
			
			$d = dir($image_file_path) or die("Wrong path: $image_file_path");
			
			$file_trovati = false;
			
			while (false !== ($entry = $d->read())) {
			if($entry != '.' && $entry != '..' && !is_dir($dir.$entry))
				{
					$filename = $entry;
					
					echo "Trovato file: ". $entry."<br>";
					
					
					
					$filename_path = $image_file_path.$entry;
				   
				   $returnvalue = shell_exec("exiftool ".$filename_path);
				   $array_media_info =  MediaInfoToArray($returnvalue); 
				   
				   $array_media_info['file_size_byte'] = filesize($filename_path);
			
					
					
					$rand_suff = "_".rand(100000,999999);
					$path_info = pathinfo($filename);
				  // print_r($path_info); //['extension'];
				   
				   $dest_file = $path_info['filename'].$rand_suff.".".$path_info['extension'];
				   
				   rename($image_file_path.$entry,$image_file_path_dest.$dest_file);
				   echo "Copiato file: ".$image_file_path.$entry." -> ". $image_file_path_dest.$dest_file."<br>";
				   
				   
				   
				   
				   
				   
				   
				   $video_name = $dest_file; 
			
			$path_parts = pathinfo($video_name);
			
			$basename =  $path_parts['filename']; // since PHP 5.2.0
			
			//$mode_test = true;
			$mode_test = false;
			
			
			echo "mode: ".(EW_HKU_CONNECT_REPOSITORY?"connected":" not connected");
			
			
			if(!EW_HKU_CONNECT_REPOSITORY) { $octo_id= '';}
			
			else {
			
		
			
			
			echo "<h1>Register Image</h1>";
			
			$esempio_video = array(
			   'sourceType' => 'IMAGE',
			   'url' => 'http://shrek.micc.unifi.it/eutv-data/media/image/'.$video_name,
			   'fileSize' => $array_media_info['file_size_byte'],
			   'mimetype' => $array_media_info['MIME Type']
			);
			
			//$esempio_request = array('file' => $esempio_video);
			
			echo "richiesta registrazione:<br><pre>";
			print_r(array('session'=>$session,'file'=>$esempio_video));
			echo "</pre>";
			$result = $client->call('doRegisterSource',array('session'=>$session,'file'=>$esempio_video));
			
			if ($client->fault) {
				echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
			} else {
				$err = $client->getError();
				if ($err) {
					echo '<h2>Error</h2><pre>' . $err . '</pre>';
				} else {
					echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
				}
			}
			
			if (!$client->fault) {
			echo "File registrato con id:".$result['sourceId'];
			
			}
			else die("Inserimento bloccato per un errore");
			
			
			//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
			echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
			//echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			
			
			$octo_id = $result['sourceId'];
			
			}
			// $video_name = $_GET['video_name']; //already called
			//die("inizio processamento");
			
			
			?>
			<?php
			define("EW_DEBUG_ENABLED", TRUE, TRUE);
			
			// Open connection to the database
			$conn = ew_Connect();
			
			
			
			
			$query_insert = "INSERT INTO ".EW_CONN_DB_MEDIA.".media (dataserverpath,mediauri,uri,created,modified,filename,title,fps,id_media_types) VALUES ('".EW_IMAGE_HTTP_URL."', '".$basename."', '".$octo_id."',now(),now(), '".$video_name."', '".$array_media_info['Title']."', '0',2);";
								
								$conn->Execute($query_insert);
								$id_media = mysql_insert_id();
					
			
			$insert_process = true;
			
			$log = "File inserted image-name=".$video_name." octo_id".$octo_id."\n";
			
			
									add_log('addImageResult:IndexImage:start',$video_name);
							
									$log = indexImage($video_name);
									//$log = "";
								
									add_log('addImageResult:IndexImage:finish',addslashes($log));
			
			
			if(@$insert_process) {		
					// EXECUTING VIDEO ANALYZING PROCESS
					$runCall = EW_URL_LOCALHOST."process/launchProcess.php?a_add=A&idSetProcess=10&image-name=".$video_name."&uri=".$octo_id."&mode=ws&id_media=".$id_media."&basename=".$basename;
					echo "called service: ".$runCall."<br>" ;
					//$result = "only test<br>";	
					$result = file_get_contents($runCall);
					echo $result ;
					}
			
					add_log('spoolImage:inserting',addslashes($log));
					
					runPendingProcess();

			
					$file_trovati = true;
					
					break;
				}
			}
			$d->close();
			
			if(!$file_trovati) echo "No image in spool folder";
			
				
	
	
	
	
	}


function start_crono($counter = "default"){
	
	global $time_start;
	
	$time_start[$counter] = microtime(true);
	
	}			
						

function end_crono($counter = "default"){
	
	global $time_start, $crono;
	
	
	$time_end = microtime(true);
	$time = number_format(($time_end - $time_start[$counter]),5);
	
	$msg = "<br />Execution time counter <em>$counter</em>: <strong>$time</strong> seconds";
	$crono[$counter] = $time;
	debug($msg);
	
	return $msg;
	
	}			

function spool_document(){
			
			$media_path = (defined("EW_ABS_MEDIA_PATH")?EW_ABS_MEDIA_PATH:EW_ABS_PATH.EW_SEPARATOR.'media'.EW_SEPARATOR);
			$file_path = $media_path.'spool_document'.EW_SEPARATOR; //- this is the full server path to your images folder
			//echo "image file path: $file_path ";
			$file_path_dest = $media_path.'document'.EW_SEPARATOR; //- this is the full server path to your images 
			
			$d = @dir($file_path);
			
			if(!$d) {
				echo "Wrong path: $file_path";
				return;
			};
			
			$file_trovati = false;
			
			while (false !== ($entry = $d->read())) {
			if($entry != '.' && $entry != '..' && !is_dir($dir.$entry))
				{
					$filename = $entry;
					
					echo "Trovato file: ". $entry."<br>";
					
					
					
					$filename_path = $image_file_path.$entry;
				   
				   $returnvalue = shell_exec("exiftool ".$filename_path);
				   $array_media_info =  MediaInfoToArray($returnvalue); 
				   
				   $array_media_info['file_size_byte'] = filesize($filename_path);
			
					
					
					$rand_suff = "_".rand(100000,999999);
					$path_info = pathinfo($filename);
				  // print_r($path_info); //['extension'];
				   
				   
				   
				   //sostituisco gli spazi con gli _ nel nome del file
				   
				   $file_name_correct =  $path_info['filename']; 
	  	  $file_name_correct = str_replace(' ','_',$file_name_correct);
	  	  $file_name_correct = str_replace('(','',$file_name_correct);
	  	  $file_name_correct = str_replace(')','',$file_name_correct);
	  	  $file_name_correct = str_replace('.','_',$file_name_correct);

				   
				    $dest_file = $file_name_correct.$rand_suff.".".$path_info['extension'];
				  
				   rename($file_path.$entry,$file_path_dest.$dest_file);
				   echo "Copiato file: ".$file_path.$entry." -> ". $file_path_dest.$dest_file."<br>";
				   
				   
				   
				   
				   
				   
				   
				   $file_name = $dest_file; 
			
			$path_parts = pathinfo($file_name);
			
			$basename =  $path_parts['filename']; // since PHP 5.2.0
			
			
			mkdir($media_path.'document_svg'.EW_SEPARATOR.$basename.EW_SEPARATOR);
			mkdir($media_path.'document_thumb'.EW_SEPARATOR.$basename.EW_SEPARATOR);
			mkdir($media_path.'document_pages_pdf'.EW_SEPARATOR.$basename.EW_SEPARATOR);
			
			
			
			//individuo il numero delle pagine del PDF
			define("EW_DEBUG_ENABLED", TRUE, TRUE);
			$page_number = count_pdf_pages($file_path_dest.$dest_file);
			if(!$page_number) $page_number = 0;
			echo "individuate $page_number pagine nel documento<br>";
			
			//$mode_test = true;
			$mode_test = false;
			
			$octo_id = "";
			
			
			
			// Open connection to the database
			$conn = ew_Connect();
			
			
			
			
			$query_insert = "INSERT INTO ".EW_CONN_DB_MEDIA.".media (dataserverpath,mediauri,uri,created,modified,filename,title,fps,id_media_types,filesize) VALUES ('".EW_DOCUMENT_HTTP_URL."', '".$basename."', '".$octo_id."',now(),now(), '".$file_name."', '".$array_media_info['Title']."', '0',4,'$page_number');";
								
								$conn->Execute($query_insert);
								$id_media = mysql_insert_id();
					
			
			$insert_process = true;
			
			$log = "File inserted image-name=".$file_name." octo_id".$octo_id."\n";
			
			
								/*	add_log('addImageResult:IndexImage:start',$video_name);
							
									$log = indexImage($video_name);
									//$log = "";
								
									add_log('addImageResult:IndexImage:finish',addslashes($log));*/
			
			
			if(@$insert_process) {		
					// EXECUTING VIDEO ANALYZING PROCESS
					$runCall = EW_URL_LOCALHOST."process/launchProcess.php?a_add=A&idSetProcess=16&file-name=".$file_name."&uri=".$octo_id."&mode=ws&id_media=".$id_media."&basename=".$basename;
					echo "called service: ".$runCall."<br>" ;
					//$result = "only test<br>";	
					$result = file_get_contents($runCall);
					echo $result ;
					}
					
					add_log('spoolDocument:inserting',addslashes($log));
					
					$file_trovati = true;
					
					break;
				}
			}
			$d->close();
			
			if(!$file_trovati) echo "<br>No document in spool folder (".$image_file_path.")";
			
				
	
	
	
	
	}

function count_pdf_pages($pdfname) {
  $pdftext = file_get_contents($pdfname);
  $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
  return $num;
}


function get_homer_analysis($url,  $text = "")
{

		$result = "<results>";
		$result.= get_homer_analysis_single('language',$url,  $text );
		$result.= get_homer_analysis_single('ned',$url,  $text );
		$result.= get_homer_analysis_single('topic',$url,  $text );
		$result .= "</results>";
		return $result;


}

function get_homer_analysis_single($analysis,$url,  $text = "")
{
		$http_homer_url = "http://".EW_ABS_HOST.":8080/homer/Homer";
		$numkeywords = -1;
		$numtopics = -1;
		
		$entitytypes_suff = "&entitytypes[]=Allents";
		
		//params  building
		
		if($url!="") $text = ""; // se metto l'url azzero il campo text
		
		$params = "";
		$params.="process=process";
		$params.="&analysis=$analysis";
		$params.="&text=".$text;
		$params.="&docurl=".$url;
		$params.=$entitytypes_suff;
		
		$ch = curl_init( $http_homer_url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		
		$response = curl_exec( $ch );
		
		$posizione_iniziale = strpos($response,"<item");
		$posizione_finale = strpos($response,"</channel>");
		$contenuto_estratto = substr($response,$posizione_iniziale,($posizione_finale-$posizione_iniziale));
		
		if($posizione_iniziale===false) return "";
		
		return $contenuto_estratto;
}

function executeProcess($input){
	
	global $conn;
	
	//inizializzo il parametro che viene utilizzato per passare ai processi il processId del processo precedente
	$previousIdProcessNum = "";

$idSetProcess = $input['idSetProcess'];


$command = "";

$x_processId = isset($input['x_processId'])? $input['x_processId'] : rand_int(12);

$sSqlWrk = "INSERT INTO `process` ( `processId`, `idSetProcess`, `idProcessStatus`, `start`, `end`, `OSProcessId`, `command`) VALUES".
 		"('".$x_processId."', ".$idSetProcess.", 1, NULL, NULL, NULL, '');";
$rswrk = $conn->Execute($sSqlWrk);


$idProcessNum = $conn->Insert_ID();




$sSqlWrk = "SELECT * from setprocess WHERE idSetProcess = ".$idSetProcess."";
$rswrk_setprocess = $conn->Execute($sSqlWrk);

$priority =  $rswrk_setprocess->fields('priority');

	$separator="";
				$equal_symbol="";
				$log_suff = "";

				if($rswrk_setprocess->fields('idProcessType')=="1"){
					//caso di un eseguibile
					$command .= $rswrk_setprocess->fields('exe')." ";
					$send_pid = ($rswrk_setprocess->fields('sendPid')=="1"?" --pid=".$x_processId:"");
					$separator=" ";
					$equal_symbol="";
					$log_suff = " >> ".EW_EXE_FILE_LOG;
				
				}
				
				elseif($rswrk_setprocess->fields('idProcessType')=="2"){
					//caso di un web service http
					$command .= $rswrk_setprocess->fields('service')."?";
					$send_pid = ($rswrk_setprocess->fields('sendPid')=="1"?"&pid=".$x_processId:"");
					
					
					$separator="&";
					$equal_symbol="=";
					$log_suff = "";


				}


				//caricamento parametri processo
				$sSqlWrk = "SELECT * from processparams WHERE idSetProcess = ".$idSetProcess." AND type = 0 ORDER BY `order`,idProcessParams";
				$rswrk = $conn->Execute($sSqlWrk);
				$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
				$rowswrk = count($arwrk);
				
				for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
				//controllo se il valore è passato con l'id del paramentro
				if($arwrk[$rowcntwrk]['mode']=="0" && @$input["params_".$arwrk[$rowcntwrk]['idProcessParams']]=="1") $command .= $arwrk[$rowcntwrk]['code'].$separator;
				//controllo se il valore è passato con il code del paramentro
				if($arwrk[$rowcntwrk]['mode']=="0" && @$input[$arwrk[$rowcntwrk]['code']]=="1") $command .= $arwrk[$rowcntwrk]['code'].$separator;
				
				if($arwrk[$rowcntwrk]['mode']=="1" && @$input["params_".$arwrk[$rowcntwrk]['idProcessParams']]!="") $command .= $arwrk[$rowcntwrk]['code'].$equal_symbol.@$input["params_".$arwrk[$rowcntwrk]['idProcessParams']].$separator;
				if($arwrk[$rowcntwrk]['mode']=="1" && @$input[$arwrk[$rowcntwrk]['code']]!="") $command .= $arwrk[$rowcntwrk]['code'].$equal_symbol.@$input["params_".$arwrk[$rowcntwrk]['idProcessParams']].$separator;
				
				$sSqlWrk = "INSERT INTO `processparamsvalue` ( `idProcessParams`, idProcessNum,value) VALUES".
						"(".$arwrk[$rowcntwrk]['idProcessParams'].", ".$idProcessNum.", '".$_GET["params_".$arwrk[$rowcntwrk]['idProcessParams']].$input[$arwrk[$rowcntwrk]['code']]."');";
				$conn->Execute($sSqlWrk);
				
				}
				$command .=$send_pid;
				$command .=$log_suff;
if($rswrk_setprocess->fields('isMultiProcess')=="0") {
	$sSqlWrk = "UPDATE `process` set `command` = '".addslashes($command)."', priority='".$priority."' WHERE idProcessNum= $idProcessNum";
	$conn->Execute($sSqlWrk);
}

//caso di un multiprocesso
if($rswrk_setprocess->fields('isMultiProcess')=="1") {

//imposto il valore di idProcessNumMaster uguale al numero stesso
$sSqlWrk = "UPDATE `process`  SET idProcessNumMaster = $idProcessNum, priority='".$priority."' WHERE idProcessNum = $idProcessNum ";
$rswrk = $conn->Execute($sSqlWrk);


$idProcessNumMaster =$idProcessNum ;

		$sSqlWrk = "SELECT *,multiprocesssteps.priority as priority_queue from multiprocesssteps JOIN setprocess ON(setprocess.idSetProcess = multiprocesssteps.idSetProcessDetail) WHERE idSetProcessMaster = ".$idSetProcess." AND `order` > 0  ORDER BY `order`";
		$rswrk_subproc = $conn->Execute($sSqlWrk);
		$arwrk_subproc = ($rswrk_subproc) ? $rswrk_subproc->GetRows() : array();
		$rowswrk_subproc = count($arwrk_subproc);
		
		for ($i = 0; $i < $rowswrk_subproc; $i++) { //inserimento step
				
				$sotto_processo = $arwrk_subproc[$i];
				//if($arwrk_subproc[$i]['mode']=="0" && @$_GET["params_".$arwrk[$rowcntwrk]['idProcessParams']]=="1")

						
				$idSetProcess = $sotto_processo['idSetProcessDetail'];
				$command = "";
				
				$x_processId = rand_int(12);
				
				$status = ($i==0?"1":"8");
				
				$priority = $sotto_processo['priority_queue'];
				
				$sSqlWrk = "INSERT INTO `process` ( `processId`, `idSetProcess`, `idProcessStatus`, `start`, `end`, `OSProcessId`, `command`,idProcessNumMaster,idMultiProcessSteps,priority) VALUES".
						"('".$x_processId."', ".$idSetProcess.", $status, NULL, NULL, NULL, '',".$idProcessNumMaster.",".$sotto_processo['idMultiProcessSteps'].",'$priority');";
				$rswrk = $conn->Execute($sSqlWrk);
				
				
				$idProcessNum = $conn->Insert_ID();
				
				
				$sSqlWrk = "SELECT * from setprocess WHERE idSetProcess = ".$idSetProcess."";
				$rswrk = $conn->Execute($sSqlWrk);
				$separator="";
				$equal_symbol="";
				$log_suff = "";

				if($rswrk->fields('idProcessType')=="1"){
					//caso di un eseguibile
					$command .= $rswrk->fields('exe')." ";
					$send_pid = ($rswrk->fields('sendPid')=="1"?" --pid=".$x_processId:"");
					$separator=" ";
					$equal_symbol="";
					$log_suff = " >> ".EW_EXE_FILE_LOG;
				
				}
				
				elseif($rswrk->fields('idProcessType')=="2"){
					//caso di un web service http
					$command .= $rswrk->fields('service')."?";
					$send_pid = ($rswrk->fields('sendPid')=="1"?"&pid=".$x_processId:"");
					
					//aggiungo se esiste il previous processId
					$send_pid .= ($previousIdProcessNum!=""?"&previousIdProcessNum=".$previousIdProcessNum:"");

					$separator="&";
					$equal_symbol="=";
					$log_suff = "";


				}
				
				//salvo il valore per utilizzarlo al processo successivo 
				$previousIdProcessNum = $idProcessNum;
				
				$sSqlWrk = "SELECT * from processparams JOIN paramsconnection ON (processparams.idProcessParams = paramsconnection.idProcessParamsOutput) WHERE idMultiProcessSteps = ".$sotto_processo['idMultiProcessSteps'];
				$rswrk = $conn->Execute($sSqlWrk);
				$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
				$rowswrk = count($arwrk);
				
				for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
				$input_value = "";
				
						if($arwrk[$rowcntwrk]['mode']=="0" && $arwrk[$rowcntwrk]['fixedInput']=="1") 
						{ 
						  $command .= $arwrk[$rowcntwrk]['code'].$separator;
						  $input_value = "1";
						}
						
						else{
						//caso del valore da recuperare da un'altro ingresso
						$sSqlWrk = "SELECT value from `processparamsvalue` JOIN  process ON(processparamsvalue.idProcessNum = process.idProcessNum) WHERE idProcessNumMaster = $idProcessNumMaster AND idProcessParams = ".$arwrk[$rowcntwrk]['idProcessParamsInput'];
						
						$rswrk_1 = $conn->Execute($sSqlWrk);
						if ($rswrk_1) {
							if (!$rswrk_1->EOF) {
								$command .= $arwrk[$rowcntwrk]['code'].$equal_symbol.$rswrk_1->fields('value').$arwrk[$rowcntwrk]['fixedInput'].$separator;
						 		$input_value = $rswrk_1->fields('value');
								
							}
							$rswrk_1->Close();
						} 
						elseif($arwrk[$rowcntwrk]['mode']=="1" && $arwrk[$rowcntwrk]['fixedInput']!="") 
						{// caso del solo valore fisso
						 $command .= $arwrk[$rowcntwrk]['code'].$equal_symbol.$arwrk[$rowcntwrk]['fixedInput'].$separator;
						 $input_value = $arwrk[$rowcntwrk]['fixedInput'];
						}
						
						
						
						
						}
						
						if($input_value!=""){
						
						echo "<br> assegnato valore ".$arwrk[$rowcntwrk]['idProcessParams'].", ".$idProcessNum.", '".$input_value;
						
						$sSqlWrk = "INSERT INTO `processparamsvalue` ( `idProcessParams`, idProcessNum,value) VALUES".
								"(".$arwrk[$rowcntwrk]['idProcessParams'].", ".$idProcessNum.", '".$input_value."');";
						$rswrk = $conn->Execute($sSqlWrk);
						}
						else echo "<br> non è stato assegnato il valore al parametro ".$arwrk[$rowcntwrk]['code'];
				
				}
				$command .=$send_pid;
				
				//se è un processo di analisi videodrono con idSetProcess == 
				
				if($idSetProcess== "5"){
					
					echo "analisi parametri opzionali videodrome_set ".@$input['videodrome_set'];
					
					if(@$input['videodrome_set']!=""){
						
						$query_ts = "SELECT id_training_concepts FROM videodromeparams WHERE name = '". $_GET['videodrome_set']."' ";
						$training_sets = ew_ExecuteScalar($query_ts);
						//add_log("starting analysis:get training sets","query:".$query_ts." id trovati: ".$training_sets);
						echo "starting analysis:get training sets","query:".$query_ts." id trovati: ".$training_sets;
						$query_op = "SELECT GROUP_CONCAT( '--bow-concept=', name
											SEPARATOR ' ' )
											FROM training_concepts AS tc
											WHERE id_training_concepts_status =5
											AND tc.id_training_sets
											IN ($training_sets)";
						
						$optional_params = ew_ExecuteScalar($query_op);
						//add_log("starting analysis:get optional params","query:".$query_op." stringa trovata: ".$optional_params);
						echo "starting analysis:get optional params","query:".$query_op." stringa trovata: ".$optional_params;

						//aggiungo i parametri opzionali alla linea di comando
						if($optional_params!="") $command .= " ".$optional_params;
						
						}
					
					
					
					}
				
				$command .=$log_suff;
				
				

				echo "<br> inserito command ".$command;
				$sSqlWrk = "UPDATE `process` set `command` = '".$command."' WHERE idProcessNum= $idProcessNum";
				echo "<br> con query ".$sSqlWrk;
				echo "<br><br><br>";
				$rswrk = $conn->Execute($sSqlWrk);
				
						
		
		
		}//inserimento step - fine



}//caso di un multiprocesso - fine
	
	
	
	
	
	}
	
	function mysql_insert_query($table, $inserts) {
    $values = array_values($inserts);
    $keys = array_keys($inserts);
       
    return 'INSERT INTO '.$table.' (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\');'."\n";
}



function mysql_update_query($table, $rsnew,$id_key) {
    $upd_query = "UPDATE $table SET ";
    $flag = false;
    $updates_value = array();
    foreach($rsnew as $key=>$value){
	   /* echo "<br><br>analisi campo $key";
	    echo "<br>vecchio valore ".$rsold->fields($key);
	    echo "<br>nuovo valore ".$updates[$key];*/
	    
		    //se è il campo id vado al campo successivo
		    if($key==$id_key) continue;
		    
		    if($value == 'NOW()') $newvalue = $value;
		    elseif($value == 'null') $newvalue = 'null';
		    else $newvalue =  "'".check_string($value)."'";
		    
		    $updates_value[] = " $key = $newvalue ";
		   
	    
	    }
	    
	    
	 return $upd_query." ".implode(',', $updates_value)." WHERE $id_key = '".$rsnew[$id_key]."'; ";
}

function check_string($string){
	
	
	//return addslashes($string);
	return $string;
	}
	
	function getSoapValue($var){
		if(!isset($var)) return false;
		if(!isset($var)) return false;
		
		}


function requestVar($str,$default_value = "") {
	return ($_REQUEST[$str]!=""?mysql_real_escape_string($_REQUEST[$str]):$default_value);
}
function varRequest($str,$default_value = "") {
	return requestVar($str,$default_value);
}
function requestVarEscape($str,$default_value = "") {
	return ($_REQUEST[$str]!=""?mysql_real_escape_string($_REQUEST[$str]):$default_value);
}

function requestVarIntEscape($str,$default_value = null) {
	return (($_REQUEST[$str]!=""&&is_numeric($_REQUEST[$str]))?$_REQUEST[$str]:$default_value);
}

?>
