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

include(__DIR__ . '/../params.php');

$miccDirectory = EW_ABS_PATH;//."web/";
$absolutePath = EW_SERVER_NAME;
$solrCoreUrl = EW_SOLR_URL;

$interfacePath = EW_ABS_PATH."app/";

$uploadDir = EW_ABS_PATH.'media/';

$appUrl = EW_SERVER_NAME.'app/';


// for executing pdfinfo by php exec from localhost
putenv('PATH=' . getenv('PATH') . ':/usr/local/bin');

// connect to db
$db_connect = @mysql_connect(EW_CONN_HOST, EW_CONN_USER , EW_CONN_PASS);
if (!$db_connect) {
    die('Not connected : ' . mysql_error());
}

if (! mysql_select_db(EW_CONN_DB) ) {
    die ('Can\'t conntect to database : ' . mysql_error());
}


?>
