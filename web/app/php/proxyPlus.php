<?php

/*==========================
proxyPlus.php

- php script which solves crossdomain issues
- gets url and sets mimeType of response
- if a file is posted it will be posted along with the outgoing request

created at: Aug 19, 2010
scripted by:

Remy Blom,
Utrecht School of Arts,
The Netherlands

www.hku.nl
remy.blom@kmt.hku.nl
==========================*/

/* FirePHPCore is used to allow debugging in FireBug (messages are send by headers */
// require_once('../../eutv-image-component/php/lib/FirePHPCore/fb.php');

$url = $_GET['url'];
$mimeType = $_GET['mimeType'];

$url = preg_replace('/\s/','+', $url);
$timeout = 14000;

$postvars = array();

if ($_POST) {
	foreach ($_POST as $key => $value) {
		$postvars[$key] = $value;
	}
//	fb($postvars);
} else {
//	fb('no $_POST data');
	$postvars = @$GLOBALS['HTTP_RAW_POST_DATA'];

}
if ($_FILES) {
	foreach ($_FILES as $key => $value) {
		$postvars[$key . '_filename'] = $value["name"];
		$postvars[$key . '_mimetype'] = $value["type"];
		$postvars[$key] = '@' . $value['tmp_name'];
	}
} else {
//	fb('no $_FILES data');
}

/* send request to crossdomain url */

$session = curl_init($url);

//$headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';

$headers = array(
	"Content-type: text/xml;charset=\"utf-8\"",
	"Accept: text/xml",
	"Cache-Control: no-cache",
	"Pragma: no-cache",
	"SOAPAction: \"run\"",
	"Content-length: ".strlen($postvars),
);

curl_setopt($session, CURLOPT_USERAGENT, "ProxyPlus/PHP/Remy.Blom@kmt.hku.nl");
curl_setopt($session, CURLOPT_TIMEOUT, $timeout);
curl_setopt($session, CURLOPT_CONNECTTIMEOUT, $timeout);
//curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
if ($postvars) {
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $postvars);

}

$response = curl_exec($session);

/* send response to browser */

$charset = "UTF-8";
if ($mimeType != "") {
	header("Content-Type: ".$mimeType);
} else {
	header("Content-Type: text/html; charset=".$charset);
}
echo $response;

curl_close($session);

?>
