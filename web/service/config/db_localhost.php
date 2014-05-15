<?php

/**
 * PHPMaker 5 configuration file
 */
 
define("EW_GOOGLE_API_KEY", "ABQIAAAAOd8HihZgfiOkyu5LQ1DBrBTkVYe2MSGxyXJmw4Ue396xysj7jhSTY5PoPSy3lXPhRfjgcr0Lmr5Cwg", TRUE);


define("EW_PROJECT", "eutv", TRUE);

define("EW_ABS_PATH", "/home/lorenzo/Documenti/localhost/micc/", TRUE);
define("EW_ABS_HOST", "127.0.0.1", TRUE);
define("EW_CONN_HOST", "localhost", TRUE);
define("EW_CONN_PORT", 3306, TRUE);
define("EW_CONN_USER", "micc", TRUE);
define("EW_CONN_PASS", "micc", TRUE);
define("EW_CONN_DB", "eutv_media", TRUE);
define("EW_CONN_DB_MEDIA", "eutv_media", TRUE);
define("EW_SEPARATOR", "/");

define("EW_URL_LOCALHOST", "http://127.0.0.1/micc/", TRUE);

define("EW_ABS_MEDIA_PATH", '/home/lorenzo/Documenti/localhost/micc/media/', TRUE);

//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/eutv-data/media/data/', TRUE);
define("EW_VIDEO_PATH", '/home/lorenzo/Documenti/localhost/micc/media/video/', TRUE);
define("EW_AUDIO_PATH", '/home/lorenzo/Documenti/localhost/micc/media/audio/', TRUE);
define("EW_IMAGE_PATH", '/home/lorenzo/Documenti/localhost/micc/media/image/', TRUE);

define("EW_AUDIO_HTTP_URL", 'http://localhost/micc/media/audio/', TRUE);
define("EW_IMAGE_HTTP_URL", 'http://localhost/micc/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://localhost/micc/media/video', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://shrek.micc.unifi.it/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/var/www/eutv-data/media/training_images/', TRUE);

define("EW_DOCUMENT_HTTP_URL", 'http://localhost/micc/media/document/', TRUE);
define("EW_DOCUMENT_SVG_HTTP_URL", 'http://localhost/micc/media/document_svg/', TRUE);
define("EW_DOCUMENT_THUMB_HTTP_URL", 'http://localhost/micc/media/document_thumb/', TRUE);

define("EW_DOCUMENT_THUMB_ABS_PATH_URL", '/home/lorenzo/Documenti/localhost/micc/media/document_thumb/', TRUE);



// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", FALSE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://bintje-01.hku.nl:9090/octo/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);


// Insert all video keyframes as single images
define("EW_INSERT_KEYFRAMES_AS_IMAGES", TRUE, TRUE); // Uncomment to debug
//define("EW_INSERT_KEYFRAMES_AS_IMAGES", FALSE, TRUE); // Uncomment to debug
//define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", TRUE, TRUE); // Uncomment to debug
define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", FALSE, TRUE); // Uncomment to debug


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/eutv-software/log_shot.txt", TRUE);

define("EW_SHOTANALYZER_PATH", '/var/www/eutv-software/ShotAnalyzer/', TRUE);

define("EW_ENABLE_TWEET", TRUE, TRUE);

//enable this params when all software is on the same server
//define("EW_SINGLE_SERVER", TRUE, TRUE);
define("EW_SINGLE_SERVER", FALSE, TRUE);

define("EW_LOCAL_HKU_FILEPATH", '', TRUE);



//solr connection

define("EW_ENABLE_INDEX_DOCUMENT_SOLR", FALSE, TRUE);

define("EW_SOLR_CONN_HOST", "localhost", TRUE);
define("EW_SOLR_CONN_PORT", 9090, TRUE);
define("EW_SOLR_CONN_INSTANCE", 'solr', TRUE);


define("EW_VIDEODROME_SERVER_URL", 'http://150.217.35.81:10000/', TRUE);


define("EW_MAX_PROCESS_IN_EXECUTION", 3 , TRUE);



?>
