<?php

/**
 * PHPMaker 5 configuration file
 */
 


define("EW_PROJECT", "loki", TRUE);

define("EW_ABS_PATH", "/var/www/search-interface/", TRUE);

define("EW_URL_PATH", "http://shrek.micc.unifi.it", TRUE);



define("EW_ABS_HOST", "127.0.0.1", TRUE);
define("EW_CONN_HOST", "localhost", TRUE);
define("EW_CONN_PORT", 3306, TRUE);
define("EW_CONN_USER", "your-username", TRUE);
define("EW_CONN_PASS", "your-password", TRUE);
define("EW_CONN_DB", "micc-interface", TRUE);
define("EW_CONN_DB_MEDIA", "micc-interface", TRUE);
define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", EW_ABS_PATH.'/media/data/', TRUE);
define("EW_VIDEO_PATH", EW_ABS_PATH.'/media/video/', TRUE);
define("EW_AUDIO_PATH", EW_ABS_PATH.'/media/audio/', TRUE);
define("EW_IMAGE_PATH", EW_ABS_PATH.'/media/image/', TRUE);

define("EW_AUDIO_HTTP_URL", EW_URL_PATH.'/search-interface/media/audio/', TRUE);
define("EW_IMAGE_HTTP_URL", EW_URL_PATH.'/search-interface/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", EW_URL_PATH.'/search-interface/media/video', TRUE);




define("EW_DOCUMENT_HTTP_URL", EW_URL_PATH.'/search-interface/media/document/', TRUE);
define("EW_DOCUMENT_SVG_HTTP_URL", EW_URL_PATH.'/search-interface/media/document_svg/', TRUE);
define("EW_DOCUMENT_THUMB_HTTP_URL", EW_URL_PATH.'/search-interface/media/document_thumb/', TRUE);

define("EW_DOCUMENT_THUMB_ABS_PATH_URL", EW_ABS_PATH.'media/document_thumb/', TRUE);




// Insert all video keyframes as single images
define("EW_INSERT_KEYFRAMES_AS_IMAGES", TRUE, TRUE); // Uncomment to debug

?>
