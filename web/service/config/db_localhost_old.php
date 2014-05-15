<?php

/**
 * PHPMaker 5 configuration file
 */
 
 define("EW_GOOGLE_API_KEY", "ABQIAAAAOd8HihZgfiOkyu5LQ1DBrBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQ4VtYTAzlZ6GbyYxOLJGG4Im9FRg", TRUE);

 
 

define("EW_PROJECT", "micc", TRUE);

//VERSIONE LOCALE
// Database connection
// Database connection
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


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/Users/giuseppebecchi/Documents/htdocs/eutv-data/media/data/', TRUE);
define("EW_VIDEO_PATH", '/Users/giuseppebecchi/Documents/htdocs/eutv-data/media/data/video/', TRUE);
define("EW_AUDIO_PATH", '/Users/giuseppebecchi/Documents/htdocs/eutv-data/media/data/audio/', TRUE);

define("EW_IMAGE_HTTP_URL", 'http://localhost/eutv/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://shrek.micc.unifi.it/im3i/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://shrek.micc.unifi.it/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/Users/giuseppebecchi/Documents/htdocs/eutv/process/training/', TRUE);

define("EW_TRAINING_TXT_CONCEPT_PATH", '/Users/giuseppebecchi/Documents/htdocs/eutv/software/ShotAnalyzer/training_txt/', TRUE);


// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", FALSE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://octo-dev.hku.nl:9090/octo/webservices/media", TRUE);
//define("EW_HKU_SOAP_SERVER", "http://bintje-02.hku.nl:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);

define("EW_SOLR_CONN_HOST", "localhost", TRUE);
define("EW_SOLR_CONN_PORT", 8080, TRUE);
define("EW_SOLR_CONN_INSTANCE", 'mediaweb', TRUE);


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/eutv/software/log_shot.txt", TRUE);

define("EW_ENABLE_TWEET", FALSE, TRUE);




?>
