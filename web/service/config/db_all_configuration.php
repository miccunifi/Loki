<?php

/**
 * PHPMaker 5 configuration file
 */

define("EW_FLICKR_KEY", "4657cfbd178976be1413a73c4d8b7028", TRUE);
define("EW_FLICKR_SECRET", "cedba00d5ab4fe68", TRUE);
 

if($server == 0){
define("EW_PROJECT", "eutv", TRUE);

//VERSIONE LOCALE
// Database connection
// Database connection
define("EW_ABS_PATH", "/Users/giuseppebecchi/Documents/htdocs/eutv", TRUE);
define("EW_ABS_HOST", "127.0.0.1", TRUE);
define("EW_CONN_HOST", "localhost", TRUE);
define("EW_CONN_PORT", 3306, TRUE);
define("EW_CONN_USER", "root", TRUE);
define("EW_CONN_PASS", "root", TRUE);
define("EW_CONN_DB", "eutv_process", TRUE);
define("EW_CONN_DB_MEDIA", "eutv_media", TRUE);
define("EW_SEPARATOR", "/");

define("EW_URL_LOCALHOST", "http://127.0.0.1/eutv/", TRUE);


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

define("EW_HKU_SOAP_SERVER", "http://eutv.hku.nl:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);

//exe file log
define("EW_EXE_FILE_LOG", "/var/www/eutv/software/log_shot.txt", TRUE);

define("EW_ENABLE_TWEET", FALSE, TRUE);






} 

// online shrek.micc.unifi.it / im3i
if($server == 1){ 
define("EW_PROJECT", "im3i", TRUE);
define("EW_ABS_PATH", "/var/www/im3i", TRUE);
define("EW_ABS_HOST", "shrek.micc.unifi.it", TRUE);
define("EW_CONN_HOST", "localhost");
define("EW_CONN_PORT", 3306);
define("EW_CONN_USER", "root");
define("EW_CONN_PASS", "m1cc321");
define("EW_CONN_DB", "im3i_process");
define("EW_CONN_DB_MEDIA", "im3i_media", TRUE);

define("EW_URL_LOCALHOST", "http://127.0.0.1/im3i/", TRUE);



define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/im3i/media/data/', TRUE);
define("EW_VIDEO_PATH", '/var/www/im3i/media/video/', TRUE);
define("EW_AUDIO_PATH", '/var/www/im3i/media/audio/', TRUE);

define("EW_IMAGE_HTTP_URL", 'http://shrek.micc.unifi.it/im3i/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://shrek.micc.unifi.it/im3i/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://shrek.micc.unifi.it/vod/', TRUE);


// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", TRUE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://bintje-02.hku.nl:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/im3i/software/log_shot.txt", TRUE);
define("EW_SHOTANALYZER_PATH", '/var/www/eutv/software/ShotAnalyzer/', TRUE);

define("EW_ENABLE_TWEET", FALSE, TRUE);

}



// online shrek.micc.unifi.it / eutv
if($server == 2){ 
define("EW_PROJECT", "eutv", TRUE);

define("EW_ABS_PATH", "/var/www/eutv", TRUE);
define("EW_ABS_HOST", "shrek.micc.unifi.it", TRUE);
define("EW_CONN_HOST", "localhost");
define("EW_CONN_PORT", 3306);
define("EW_CONN_USER", "root");
define("EW_CONN_PASS", "m1cc321");
define("EW_CONN_DB", "eutv_process");
define("EW_CONN_DB_MEDIA", "eutv_media", TRUE);

define("EW_URL_LOCALHOST", "http://127.0.0.1/eutv/", TRUE);

define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/eutv/media/data/', TRUE);
define("EW_VIDEO_PATH", '/var/www/eutv/media/video/', TRUE);
define("EW_AUDIO_PATH", '/var/www/eutv/media/audio/', TRUE);

define("EW_AUDIO_HTTP_URL", 'http://shrek.micc.unifi.it/eutv/media/audio/', TRUE);
define("EW_IMAGE_HTTP_URL", 'http://shrek.micc.unifi.it/eutv/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://shrek.micc.unifi.it/eutv/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://shrek.micc.unifi.it/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/var/www/eutv/process/training/', TRUE);


// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", TRUE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://bintje-01.hku.nl:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);

define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", FALSE, TRUE); // Uncomment to debug


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/eutv/software/log_shot.txt", TRUE);

define("EW_SHOTANALYZER_PATH", '/var/www/eutv/software/ShotAnalyzer/', TRUE);

define("EW_ENABLE_TWEET", TRUE, TRUE);

}


//eutv.hku.nl/eutv
if($server == 3){ 
define("EW_PROJECT", "eutv", TRUE);

define("EW_ABS_PATH", "/var/www/eutv", TRUE);
define("EW_ABS_HOST", "eutv.hku.nl", TRUE);
define("EW_CONN_HOST", "localhost");
define("EW_CONN_PORT", 3306);
define("EW_CONN_USER", "root");
define("EW_CONN_PASS", "qEfr4xuHustE");
define("EW_CONN_DB", "eutv_process");
define("EW_CONN_DB_MEDIA", "eutv_media", TRUE);

define("EW_URL_LOCALHOST", "http://127.0.0.1/eutv/", TRUE);


define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/eutv/media/data/', TRUE);
define("EW_VIDEO_PATH", '/var/www/eutv/media/video/', TRUE);
define("EW_AUDIO_PATH", '/var/www/eutv/media/audio/', TRUE);

define("EW_AUDIO_HTTP_URL", 'http://eutv.hku.nl/eutv/media/audio/', TRUE);
define("EW_IMAGE_HTTP_URL", 'http://eutv.hku.nl/eutv/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://eutv.hku.nl/eutv/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://eutv.hku.nl/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/var/www/eutv/process/training/', TRUE);



// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", TRUE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://eutv.hku.nl:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);
define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", FALSE, TRUE); // Uncomment to debug


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/eutv/software/log_shot.txt", TRUE);


define("EW_SHOTANALYZER_PATH", '/var/www/eutv/software/ShotAnalyzer/', TRUE);

define("EW_ENABLE_TWEET", FALSE, TRUE);

}


// online ciuchino.micc.unifi.it / eutv
if($server == 4){ 
define("EW_PROJECT", "eutv", TRUE);

define("EW_ABS_PATH", "/var/www/eutv", TRUE);
define("EW_ABS_HOST", "ciuchino.micc.unifi.it", TRUE);
define("EW_CONN_HOST", "localhost");
define("EW_CONN_PORT", 3306);
define("EW_CONN_USER", "root");
define("EW_CONN_PASS", "m1cc321");
define("EW_CONN_DB", "eutv_process");
define("EW_CONN_DB_MEDIA", "eutv_media", TRUE);

define("EW_URL_LOCALHOST", "http://127.0.0.1/eutv/", TRUE);

define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/eutv/media/data/', TRUE);
define("EW_VIDEO_PATH", '/var/www/eutv/media/video/', TRUE);
define("EW_AUDIO_PATH", '/var/www/eutv/media/audio/', TRUE);

define("EW_IMAGE_HTTP_URL", 'http://ciuchino.micc.unifi.it/eutv/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://ciuchino.micc.unifi.it/eutv/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://ciuchino.micc.unifi.it/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/var/www/eutv/process/training/', TRUE);

define("EW_SOFTWARE_PATH", '/var/www/eutv-software/ShotAnalyzer/bowdatabuilder/', TRUE);


// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", FALSE, TRUE);

define("EW_HKU_SOAP_SERVER", "", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);

define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", FALSE, TRUE); // Uncomment to debug


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/eutv/software/log_shot.txt", TRUE);

define("EW_TRAINING_TXT_CONCEPT_PATH", '/var/www/eutv/software/ShotAnalyzer/training_txt/', TRUE);
define("EW_SHOTANALYZER_PATH", '/var/www/eutv/software/ShotAnalyzer/', TRUE);

}

// online 82.198.215.196(hermes.in-two.com) / im3i
if($server == 5){ 
define("EW_PROJECT", "im3i", TRUE);

define("EW_ABS_PATH", "/var/www/im3i", TRUE);
define("EW_ABS_HOST", "hermes.in-two.com", TRUE);
define("EW_CONN_HOST", "localhost");
define("EW_CONN_PORT", 3306);
define("EW_CONN_USER", "root");
define("EW_CONN_PASS", "intwoim3i");
define("EW_CONN_DB", "im3i_process");
define("EW_CONN_DB_MEDIA", "im3i_media", TRUE);

define("EW_URL_LOCALHOST", "http://127.0.0.1/im3i/", TRUE);

define("EW_SEPARATOR", "/");

//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/im3i/media/data/', TRUE);
define("EW_VIDEO_PATH", '/var/www/im3i/media/video/', TRUE);
define("EW_AUDIO_PATH", '/var/www/im3i/media/audio/', TRUE);

define("EW_IMAGE_HTTP_URL", 'http://hermes.in-two.com/im3i/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://hermes.in-two.com/im3i/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://hermes.in-two.com/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/var/www/im3i/process/training/', TRUE);


// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", TRUE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://hermes.in-two.com:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);

define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", FALSE, TRUE); // Uncomment to debug


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/im3i/software/log_shot.txt", TRUE);

define("EW_TRAINING_TXT_CONCEPT_PATH", '/var/www/im3i/software/ShotAnalyzer/training_txt/', TRUE);
define("EW_SHOTANALYZER_PATH", '/var/www/im3i/software/ShotAnalyzer/', TRUE);

}





//im3i-server.hku.nl/im3i
if($server == 6){ 
define("EW_PROJECT", "eutv", TRUE);

define("EW_ABS_PATH", "/var/www/im3i-tools", TRUE);
define("EW_ABS_HOST", "im3i-server.hku.nl", TRUE);
define("EW_CONN_HOST", "localhost");
define("EW_CONN_PORT", 3306);
define("EW_CONN_USER", "root");
define("EW_CONN_PASS", "qEfr4xuHustE");
define("EW_CONN_DB", "im3i_process");
define("EW_CONN_DB_MEDIA", "im3i_media", TRUE);

define("EW_URL_LOCALHOST", "http://127.0.0.1/im3i-tools/", TRUE);


define("EW_SEPARATOR", "/");


//define data mpeg7 folder
define("EW_DATAMPEG7_PATH", '/var/www/im3i-tools/media/data/', TRUE);
define("EW_VIDEO_PATH", '/var/www/im3i-tools/media/video/', TRUE);
define("EW_AUDIO_PATH", '/var/www/im3i-tools/media/audio/', TRUE);

define("EW_AUDIO_HTTP_URL", 'http://im3i-server.hku.nl/eutv/media/audio/', TRUE);
define("EW_IMAGE_HTTP_URL", 'http://im3i-server.hku.nl/eutv/media/image/', TRUE);
define("EW_VIDEO_HTTP_URL", 'http://im3i-server.hku.nl/eutv/media/video/', TRUE);
define("EW_VIDEO_RTMP_URL", 'rtmp://im3i-server.hku.nl/vod/', TRUE);

define("EW_TRAINING_IMAGE_PATH", '/var/www/im3i-tools/process/training/', TRUE);



// HKU repository connection  

//PARAMETRO PER ABILITARE O NO LA CONNESSIONE AL REPOSITORY HKU
define("EW_HKU_CONNECT_REPOSITORY", TRUE, TRUE);

define("EW_HKU_SOAP_SERVER", "http://im3i-server.hku.nl:9090/im3i/webservices/media", TRUE);
define("EW_CONN_USER_HKU", "unifi_analyzer1", TRUE);
define("EW_CONN_PASS_HKU", "unifi_analyzer1", TRUE);
define("EW_SEND_KEYFRAMES_AS_IMAGES_TO_HKU", FALSE, TRUE); // Uncomment to debug


//exe file log
define("EW_EXE_FILE_LOG", "/var/www/im3i-tools/software/log_shot.txt", TRUE);


define("EW_SHOTANALYZER_PATH", '/var/www/im3i-tools/software/ShotAnalyzer/', TRUE);

define("EW_ENABLE_TWEET", FALSE, TRUE);

}

?>
