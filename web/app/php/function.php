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

	define("ID_USER_AUTOMATIC", '2', TRUE);
	define("EW_VIDEO_PATH", '/home/lorenzo/Documenti/localhost/micc/media/video/', TRUE);
	define("EW_IMAGE_PATH", '/home/lorenzo/Documenti/localhost/micc/media/image/', TRUE);

	
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
		$sss = $timepoint%1000;	// salvo i decimali

		$hh = intval($inputval / 3600);    // '/' given value by num sec in hour... output = HOURS 
		$ss_remaining = ($inputval - ($hh * 3600));        // '*' number of hours by seconds, then '-' from given value... output = REMAINING seconds 
		$mm = intval($ss_remaining / 60);    // take remaining sec and devide by sec in a min... output = MINS 
		$ss = intval(($ss_remaining - ($mm * 60)));        // '*' number of mins by seconds, then '-' from remaining sec... output = REMAINING seconds. 
		
		
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
		}elseif ($sss<100) {
				$sss = '0'. $sss;
		}
		
		
		return   $hh . ':' . $mm . ':' . $ss . '.' .$sss;
}

function sec2hms($sec, $padHours = false) {

    $hms = "";
    
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    return $hms;
}


function log_query($query) {


    $myFile = "log_query.txt";
    $fh = fopen($myFile, 'a') or die("can't open file");

    fwrite($fh, "\n".$query);

    fclose($fh);

}

function log_task($log_text, $txt_file = "upload-log.txt") {

    $fh = fopen($txt_file, 'a') or die("can't open file");
    fwrite($fh, $log_text);
    fclose($fh);

}



function getPDFPages($document)
{
    $cmd = "/path/to/pdfinfo";           // Linux
    $cmd = "C:\\path\\to\\pdfinfo.exe";  // Windows

    // Parse entire output
    exec("$cmd $document", $output);

    // Iterate through lines
    $pagecount = 0;
    foreach($output as $op)
    {
        // Extract the number
        if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1)
        {
            $pagecount = intval($matches[1]);
            break;
        }
    }

    return $pagecount;
}


?>
