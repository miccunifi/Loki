<?php
error_reporting(0);
// include('class_http.php');
/*
2.
   // FILE: proxy.php
3.
   //
4.
   // LAST MODIFIED: 2006-03-23
5.
   //
6.
   // AUTHOR: Troy Wolf <troy@troywolf.com>
7.
   //
8.
   // DESCRIPTION: Allow scripts to request content they otherwise may not be
9.
   // able to. For example, AJAX (XmlHttpRequest) requests from a
10.
   // client script are only allowed to make requests to the same
11.
   // host that the script is served from. This is to prevent
12.
   // "cross-domain" scripting. With proxy.php, the javascript
13.
   // client can pass the requested URL in and get back the
14.
   // response from the external server.
15.
   //
16.
   // USAGE: "proxy_url" required parameter. For example:
17.
   // http://www.mydomain.com/proxy.php?proxy_url=http://www.yahoo.com
18.
   //
19.

20.
   // proxy.php requires Troy's class_http. http://www.troywolf.com/articles
21.
   // Alter the path according to your environment.
22.
*/
require_once("class_http.php");

$proxy_url = isset($_GET['proxy_url'])?$_GET['proxy_url']:false;

if (!$proxy_url) $proxy_url = isset($_GET['url'])?$_GET['url']:false;


//modifica fatat da giuseppe becchi per ovviare al problema dell'encoding del carattere +
//$proxy_url = str_replace('+','%2B',$proxy_url);


if (!$proxy_url) {

	header("HTTP/1.0 400 Bad Request");

	echo "proxy.php failed because proxy_url parameter is missing";

	exit();

}



// Instantiate the http object used to make the web requests.

// More info about this object at www.troywolf.com/articles

if (!$h = new http()) {

	header("HTTP/1.0 501 Script Error");

	echo "proxy.php failed trying to initialize the http object";

	exit();

}



$h->url = $proxy_url;

$h->postvars = $_POST;

if (!$h->fetch($h->url)) {

	header("HTTP/1.0 501 Script Error");

	echo "proxy.php had an error attempting to query the url";

	exit();

}



// Forward the headers to the client.

$ary_headers = split( "\n",$h->header); //

foreach($ary_headers as $hdr) { header($hdr); }
// Send the response body to the client.
echo $h->body;

//$xml=$h->body;

//writing log
append_txt("\n\n".date('c').'\n'.$h->log,'log.txt');



function append_txt($string,$file_txt){
	$file=fopen($file_txt,"a");
	fseek($file,0);
	fputs($file,$string."\n");
	fclose($file);
}







?>
