<?php

/**
 * PHPMaker 5 configuration file
 */

include(__DIR__ . '/../../params.php');


//web absolute path
//example: http://localhost/Loki/web/
define("EW_URL_PATH", EW_SERVER_NAME, TRUE);


//web database path
if (!defined("EW_CONN_DB_MEDIA")) define("EW_CONN_DB_MEDIA", EW_CONN_DB, TRUE);

define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", EW_ABS_PATH.'/media/data/', TRUE);
define("EW_VIDEO_PATH", EW_ABS_PATH.'/media/video/', TRUE);
define("EW_AUDIO_PATH", EW_ABS_PATH.'/media/audio/', TRUE);
define("EW_IMAGE_PATH", EW_ABS_PATH.'/media/image/', TRUE);

define("EW_AUDIO_HTTP_URL", EW_URL_PATH.'/media/audio/', TRUE);
define("EW_IMAGE_HTTP_URL", EW_URL_PATH.'/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", EW_URL_PATH.'/media/video', TRUE);




define("EW_DOCUMENT_HTTP_URL", EW_URL_PATH.'/media/document/', TRUE);
define("EW_DOCUMENT_SVG_HTTP_URL", EW_URL_PATH.'/media/document_svg/', TRUE);
define("EW_DOCUMENT_THUMB_HTTP_URL", EW_URL_PATH.'/media/document_thumb/', TRUE);

define("EW_DOCUMENT_THUMB_ABS_PATH_URL", EW_ABS_PATH.'media/document_thumb/', TRUE);




// Insert all video keyframes as single images
define("EW_INSERT_KEYFRAMES_AS_IMAGES", TRUE, TRUE); // Uncomment to debug

?>
