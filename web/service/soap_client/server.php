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



?><?php include "../include/ewcfg50.php" ?>
<?php include "../include/ewmysql50.php" ?>
<?php include "../include/phpfn50.php" ?>
<?php require_once "../lib/function.php" ?>
<?php

// Open connection to the database
$conn = ew_Connect();



// Pull in the NuSOAP code
require_once('../lib/nusoap/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('Wsdl Unifi', 'urn:wsdlunifiim3i',false,'document');



/*//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'getAnnotationsForRange',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
        'timePoint' => array('name' => 'timePoint', 'type' => 'xsd:int'),
        'duration'  => array('name' => 'duration',  'type' => 'xsd:int')
    )

);

*/

//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'getAnnotationsForRangeResponse',
    'complexType',
    'struct',
  'sequence',
    '',
	
	array(
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:string'),
        'num' => array('name' => 'num', 'type' => 'xsd:int'),
	   'annotations' => array('name' => 'annotations', 'type' => 'tns:annotation',
      'minOccurs' => '0', 
      'maxOccurs' => 'unbounded')
    )

);


//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'getAnnotationsForRangeRequest',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
       'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
        'timePoint' => array('name' => 'timePoint', 'type' => 'xsd:int'),
        'duration'  => array('name' => 'duration',  'type' => 'xsd:int'),
        'page'  => array('name' => 'page',  'type' => 'xsd:int')
    )

);



//Defining getAnnotations Search Request and response
$server->wsdl->addComplexType(
    'getAnnotationsSearchRequest',
    'complexType',
    'struct',
    'all',
    '',
	array(
			'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
			'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
			'keyword' => array('name' => 'keyword', 'type' => 'xsd:string'),
			'page' => array('name' => 'page', 'type' => 'xsd:int'),
			'record_per_page' => array('name' => 'record_per_page', 'type' => 'xsd:int'),
			'interval' => array('name' => 'interval', 'type' => 'xsd:int')
   	 )
);


$server->wsdl->addComplexType(
    'getAnnotationsSearchResponse',
    'complexType',
    'struct',
  'sequence',
    '',
	
	array(
       'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:string'),
		'num' => array('name' => 'num', 'type' => 'xsd:int'), //--> numero risultati totali       
		'page_number' => array('name' => 'page_number', 'type' => 'xsd:int'), //--> valore numerico numero pagine totali
		'page_loaded' => array('name' => 'page_loaded', 'type' => 'xsd:int'), //--> valore numerico della pagina corrente
	       'searchTerm' => array('name' => 'searchTerm', 'type' => 'xsd:string'),
		'annotations' => array('name' => 'annotations', 'type' => 'tns:annotation','minOccurs' => '0','maxOccurs' => 'unbounded') 

    )

);



//Defining getTranscriptions Search Request and response
$server->wsdl->addComplexType(
    'getTranscriptionsSearchRequest',
    'complexType',
    'struct',
    'all',
    '',
	array(
			'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
			'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
			'keyword' => array('name' => 'keyword', 'type' => 'xsd:string'),
			'page' => array('name' => 'page', 'type' => 'xsd:int'),
			'record_per_page' => array('name' => 'record_per_page', 'type' => 'xsd:int')
   	 )
);


$server->wsdl->addComplexType(
    'getTranscriptionsSearchResponse',
    'complexType',
    'struct',
  'sequence',
    '',
	
	array(
       'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:string'),
		'num' => array('name' => 'num', 'type' => 'xsd:int'), //--> numero risultati totali       
		'page_number' => array('name' => 'page_number', 'type' => 'xsd:int'), //--> valore numerico numero pagine totali
		'page_loaded' => array('name' => 'page_loaded', 'type' => 'xsd:int'), //--> valore numerico della pagina corrente
       	'searchTerm' => array('name' => 'searchTerm', 'type' => 'xsd:string'),
		'transcriptions' => array('name' => 'transcriptions', 'type' => 'tns:transcriptions','minOccurs' => '0','maxOccurs' => 'unbounded') 

    )

);


//Defining getSimilarImages Search Request and response
$server->wsdl->addComplexType(
    'getSimilarImagesRequest',
    'complexType',
    'struct',
    'all',
    '',
	array(	'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
			'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
			'timePoint' => array('name' => 'timePoint', 'type' => 'xsd:int'),
			'exact_timepoint'  => array('name' => 'exact_timepoint',  'type' => 'xsd:int')  ,
			'find_all'  => array('name' => 'find_all',  'type' => 'xsd:int'), 
			'page' => array('name' => 'page', 'type' => 'xsd:int'),
			'record_per_page' => array('name' => 'record_per_page', 'type' => 'xsd:int')
   	 )
);


$server->wsdl->addComplexType(
    'getSimilarImagesResponse',
    'complexType',
    'struct',
  'sequence',
    '',
	
	array(
       'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:string'),
		'num' => array('name' => 'num', 'type' => 'xsd:int'), //--> numero risultati totali       
		'page_number' => array('name' => 'page_number', 'type' => 'xsd:int'), //--> valore numerico numero pagine totali
		'page_loaded' => array('name' => 'page_loaded', 'type' => 'xsd:int'), //--> valore numerico della pagina corrente
       	'searchTerm' => array('name' => 'searchTerm', 'type' => 'xsd:string'),
		'images' => array('name' => 'images', 'type' => 'tns:images','minOccurs' => '0','maxOccurs' => 'unbounded') 

    )

);

       





//Defining getAutoComplete  Request and response - start
$server->wsdl->addComplexType(
    'getAutoCompleteRequest',
    'complexType',
    'struct',
    'all',
    '',
	array(
			'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
			'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
			'keyword' => array('name' => 'keyword', 'type' => 'xsd:string')
   	 )
);


$server->wsdl->addComplexType(
    'getAutoCompleteResponse',
    'complexType',
    'struct',
  	'sequence',
    '',
	array(
	   'keywords' => array('name' => 'keywords', 'type' => 'xsd:string', 'minOccurs' => '0','maxOccurs' => 'unbounded')
     )
);
//Defining getAutoComplete  Request and response - end

//Definig ArrayofString complex type
$server->wsdl->addComplexType(
  'ArrayOfString',
  'complexType',
  'array',
  'sequence',
  '',
  array(
    'item' => array(
      'name' => 'item', 
      'type' => 'xsd:string',
      'minOccurs' => '0', 
      'maxOccurs' => 'unbounded'
    )
  )
);



//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'getTranscriptionsForRangeResponse',
    'complexType',
    'struct',
  'sequence',
    '',
	
	array(
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:string'),
        'num' => array('name' => 'num', 'type' => 'xsd:int'),
	   'transcriptions' => array('name' => 'transcriptions', 'type' => 'tns:transcriptions',
      'minOccurs' => '0', 
      'maxOccurs' => 'unbounded')
    )

);


//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'getTranscriptionsForRangeRequest',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
       'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
        'timePoint' => array('name' => 'timePoint', 'type' => 'xsd:int'),
        'duration'  => array('name' => 'duration',  'type' => 'xsd:int')
    )

);


/*
//Definig complex type
$server->wsdl->addComplexType(
    'setAnnotationsRequest',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
        'timePoint' => array('name' => 'timePoint', 'type' => 'xsd:int'),
        'duration'  => array('name' => 'duration',  'type' => 'xsd:int'),
        'keyword'  => array('name' => 'keyword',  'type' => 'xsd:int'),
        'owner'  => array('name' => 'owner',  'type' => 'xsd:string')
    )

);




//Definig complex type
$server->wsdl->addComplexType(
    'setAnnotationsResponse',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'message' => array('name' => 'message', 'type' => 'xsd:string'),
        'concept' => array('name' => 'concept', 'type' => 'xsd:string'),
        'status'  => array('name' => 'status',  'type' => 'xsd:string')
    )

);

*/
//Definig complex type
$server->wsdl->addComplexType(
    'setAnnotationsRequest',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
	   'annotations' => array('name' => 'annotations', 'type' => 'tns:annotations') 
    )

);

//Definig complex type
$server->wsdl->addComplexType(
    'setAnnotationsRequest',
    'complexType',
    'struct',
    'sequence',
    '',
	
	array(
        'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
	   
	   'annotations' => array('name' => 'annotations', 'type' => 'tns:annotations') 
    )

);




//Definig complex type
$server->wsdl->addComplexType(
    'setAnnotationsResponse',
    'complexType',
    'struct',
    'sequence',
    '',
	
	array(
        'message' => array('name' => 'message', 'type' => 'xsd:string'),
        'status'  => array('name' => 'status',  'type' => 'xsd:string'),
	   'annotations' => array('name' => 'annotations', 'type' => 'tns:annotation','minOccurs' => '0','maxOccurs' => 'unbounded')
	   )

);



		

//Definig complex type
$server->wsdl->addComplexType(
    'delAnnotationsRequest',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'id'  => array('name' => 'id',  'type' => 'xsd:string'),
        'owner'  => array('name' => 'owner',  'type' => 'xsd:string')
    )

);




//Definig complex type
$server->wsdl->addComplexType(
    'delAnnotationsResponse',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'concept' => array('name' => 'concept', 'type' => 'xsd:string'),
        'message' => array('name' => 'message', 'type' => 'xsd:string'),
        'status'  => array('name' => 'status',  'type' => 'xsd:string')
    )

);


//Edit transcription request and response
$server->wsdl->addComplexType(
    'editTranscriptionsRequest',
    'complexType',
    'struct',
    'all',
    '',
	array(
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int'),
        'sourceId' => array('name' => 'sourceId', 'type' => 'xsd:int'),
        'keyword'  => array('name' => 'keyword',  'type' => 'xsd:int'),
        'owner'  => array('name' => 'owner',  'type' => 'xsd:string')
    )
);




//Definig complex type
$server->wsdl->addComplexType(
    'editTranscriptionsResponse',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'id' => array('name' => 'id', 'type' => 'xsd:string'),
        'timePoint' => array('name' => 'timePoint', 'type' => 'xsd:int'),
        'duration'  => array('name' => 'duration',  'type' => 'xsd:int'),
        'message' => array('name' => 'message', 'type' => 'xsd:string'),
        'status'  => array('name' => 'status',  'type' => 'xsd:string')
    )

);


//Definig complex type
$server->wsdl->addComplexType(
    'getSourceIdDetailRequest',
    'complexType',
    'struct',
    'all',
    '',
	array(
                'sourceId'  => array('name' => 'sourceId',  'type' => 'xsd:string'),
			 'sessionId' => array('name' => 'sessionId', 'type' => 'xsd:int')
    )

);



//Definig complex type
$server->wsdl->addComplexType(
    'getSourceIdDetailResponse',
    'complexType',
    'struct',
    'all',
    '',
	
	array(
        'message' => array('name' => 'message', 'type' => 'xsd:string'),
        'status'  => array('name' => 'status',  'type' => 'xsd:string'),
        'title' => array('name' => 'title', 'type' => 'xsd:string'),
        'filename' => array('name' => 'filename', 'type' => 'xsd:string'),
        'mediauri' => array('name' => 'mediauri', 'type' => 'xsd:string'),
        'filepath' => array('name' => 'filepath', 'type' => 'xsd:string'),
        'http_url' => array('name' => 'http_url', 'type' => 'xsd:string'),
        'doc_url' => array('name' => 'doc_url', 'type' => 'xsd:string'),
        'document_num_pages' => array('name' => 'document_num_pages', 'type' => 'xsd:int'),
        'page_base_url' => array('name' => 'page_base_url', 'type' => 'xsd:string'),
        'page_extension' => array('name' => 'page_extension', 'type' => 'xsd:string'),
        'thumb_base_url' => array('name' => 'thumb_base_url', 'type' => 'xsd:string'),
        'thumb_extension' => array('name' => 'thumb_extension', 'type' => 'xsd:string'),
        'doc_ratio' => array('name' => 'doc_ratio', 'type' => 'xsd:float')
    )

);


//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'annotation',
    'complexType',
	'struct',
    'all',
    '',
	
	array(
        'agent' => 		array('name' => 'agent', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'comment' => 	array('name' => 'comment', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'confidence' => 	array('name' => 'confidence', 'type' => 'xsd:float'),
        'created' => 	array('name' => 'created', 'type' => 'xsd:long'),
        'duration' => 	array('name' => 'duration', 'type' => 'xsd:long'),
        'id' => 		array('name' => 'id', 'type' => 'xsd:long'),
        'keyword' => 	array('name' => 'keyword', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'modified' => 	array('name' => 'modified', 'type' => 'xsd:long'),
        'owner' => 		array('name' => 'owner', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'ownerName' => 	array('name' => 'ownerName', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'sourceId' => 	array('name' => 'sourceId', 'type' => 'xsd:long'),
        'start' => 		array('name' => 'start', 'type' => 'xsd:long'),
        'type' => 		array('name' => 'type', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
	   'context_pre' => 	array('name' => 'context_pre', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
	   'context_post' => 	array('name' => 'context_post', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'box_x' => 	array('name' => 'box_x', 'type' => 'xsd:float','minOccurs' => '0'),
        'box_y' => 	array('name' => 'box_y', 'type' => 'xsd:float','minOccurs' => '0'),
        'box_width' => 	array('name' => 'box_width', 'type' => 'xsd:float','minOccurs' => '0'),
        'box_height' => 	array('name' => 'box_height', 'type' => 'xsd:float','minOccurs' => '0'),
        'page' => 	array('name' => 'page', 'type' => 'xsd:int','minOccurs' => '0')

    )

);

//Definig complex type
$server->wsdl->addComplexType(
    'annotations',
    'complexType',
    'struct',
    'sequence',
    '',
	
	array(
	   
	   'annotation' => array('name' => 'annotation', 'type' => 'tns:annotation','minOccurs' => '0','maxOccurs' => 'unbounded') 
    )

);


//Definig Transcriptions complex type
$server->wsdl->addComplexType(
    'transcriptions',
    'complexType',
	'struct',
    'all',
    '',
	
	array(
        'agent' => 		array('name' => 'agent', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'comment' => 	array('name' => 'comment', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'confidence' => 	array('name' => 'confidence', 'type' => 'xsd:float'),
        'created' => 	array('name' => 'created', 'type' => 'xsd:long'),
        'duration' => 	array('name' => 'duration', 'type' => 'xsd:long'),
        'id' => 		array('name' => 'id', 'type' => 'xsd:long'),
        'keyword' => 	array('name' => 'keyword', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'modified' => 	array('name' => 'modified', 'type' => 'xsd:long'),
        'owner' => 		array('name' => 'owner', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'sourceId' => 	array('name' => 'sourceId', 'type' => 'xsd:long'),
        'start' => 		array('name' => 'start', 'type' => 'xsd:long'),
        'type' => 		array('name' => 'type', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */)
    )

);




//Definig Im3iProcess complex type
$server->wsdl->addComplexType(
    'images',
    'complexType',
	'struct',
    'all',
    '',
	
	array(
        'filename' => 		array('name' => 'filename', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'url' => 	array('name' => 'url', 'type' => 'xsd:string','minOccurs' => '0'/* ,'maxOccurs' => 'unbounded' */),
        'timePoint' => 	array('name' => 'timePoint', 'type' => 'xsd:long'),
        'sourceId' => 	array('name' => 'sourceId', 'type' => 'xsd:long')
    )

);


/*<xs:element minOccurs="0" name="agent" type="xs:string"/>
<xs:element minOccurs="0" name="comment" type="xs:string"/>
<xs:element name="confidence" type="xs:float"/>
<xs:element name="created" type="xs:long"/>
<xs:element name="duration" type="xs:long"/>
<xs:element name="id" type="xs:long"/>
<xs:element minOccurs="0" name="keyword" type="xs:string"/>
<xs:element name="modified" type="xs:long"/>
<xs:element minOccurs="0" name="owner" type="xs:string"/>
<xs:element name="sourceId" type="xs:long"/>
<xs:element name="start" type="xs:long"/>
<xs:element minOccurs="0" name="type" type="xs:string"/>
*/



// Register the method to expose
$server->register('hello',                // method name
    array('name' => 'xsd:string'),        // input parameters
    array('result' => 'xsd:string'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Says hello to the caller'            // documentation
);



function hello($name) {

		global $conn;
		define("EW_DEBUG_ENABLED", TRUE, TRUE);

		$log_activity = "INSERT INTO logs (type,text) VALUES ('unifisoapserver:hello','Hello test');";
		$res = $conn->Execute($log_activity);
		
		$return_array = 'Hello new test, ' . $name;

		
		
        return $return_array;
}



// Register the method to expose
$server->register('getSourceIdDetail',                // method name
    array('getSourceIdDetailRequest' => 'tns:getSourceIdDetailRequest'),        // input parameters
    array('getSourceIdDetailResponse' => 'tns:getSourceIdDetailResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Get all information of a video'            // documentation
);



function getSourceIdDetail($getSourceIdDetailRequest) {

		global $conn;

	
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $getSourceIdDetailRequest['sourceId'];
		
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $id_media . "' ";
		
		
		//check if media already exists in the database
		$rs = $conn->Execute("select * from media WHERE $cond ");
		if($rs->RecordCount() == 0){
			
			 $return_array = array(
								'message' 	=> 'Media with sourceId '.$id_media.' not found.',
								'status'         => 'STATUS_ERROR'
				);
				return $return_array;
			
		}
		
		$rs->MoveFirst();
		
		
		
		    
		    
		//informazioni addizionali per i documenti
		
		if($rs->fields('id_media_types')=="4"){
			
			add_log('getSourceIdDetail',"document");
			
				$width = $rs->fields('framew');
				$height = $rs->fields('frameh');
				
				if($height!="0" && $height!="") $ratio = number_format($width/$height,2);
				else {
					
					$img_path = EW_DOCUMENT_THUMB_ABS_PATH_URL.$rs->fields('mediauri')."/page_0.jpg";
					
					add_log('getSourceIdDetail:getRatio','search image: '.$img_path);
					$immagine = @imagecreatefromjpeg($img_path);
					add_log('getSourceIdDetail:getRatio','passed search image: '.$img_path);
					
					
					
						if(isset($immagine))
						{
							
						$w = imagesx($immagine);
						$h = imagesy($immagine);
						
						$ratio = number_format($w/$h,2);
						add_log('getSourceIdDetail:getRatio','found image: '.$img_path.' ratio: '.$ratio);
						}
						else {
							$w = "-1";
							$h = "-1";
							
							$ratio = "-1";
							add_log('getSourceIdDetail:getRatio','not found image: '.$img_path);
						}
						
						
						$conn->execute("UPDATE ".EW_CONN_DB_MEDIA.".media SET framew = '$w', frameh='$h' WHERE $cond");
					
					}
			    
			   $return_array = array(
				'status' => 'STATUS_OK',
				'title' => $rs->fields('title'),
				'filename' => $rs->fields('filename'),
				'mediauri' => $rs->fields('mediauri'),
				'doc_url' => EW_DOCUMENT_HTTP_URL.$rs->fields('mediauri'),
				'document_num_pages' => (int)$rs->fields('filesize'),
				'page_base_url' => EW_DOCUMENT_SVG_HTTP_URL.$rs->fields('mediauri').'/page_',
				'page_extension' => '.svg',
			     'thumb_base_url' => EW_DOCUMENT_THUMB_HTTP_URL.$rs->fields('mediauri').'/page_',
			     'thumb_extension' => '.jpg',
			     'doc_ratio' => $ratio
		    );
		    
		    
		     //$return_array['http_url']=;
			   
			    
			    
	    } elseif($rs->fields('id_media_types')=="1"){ // VIDEO
		    
		    
		    $return_array = array(
				'status' => 'STATUS_OK',
				'title' => $rs->fields('title'),
				'filename' => $rs->fields('filename'),
				'mediauri' => $rs->fields('title'),
				'filepath' => $rs->fields('dataserverpath').$rs->fields('filename')
		    );
		    
		    
		    
		    } elseif($rs->fields('id_media_types')=="2"){ // IMMAGINI
		    
		    
		    $return_array = array(
				'status' => 'STATUS_OK',
				'title' => $rs->fields('title'),
				'filename' => $rs->fields('filename'),
				'mediauri' => $rs->fields('title'),
				'http_url' => $rs->fields('dataserverpath').$rs->fields('filename')
		    );
		  		    
		    } elseif($rs->fields('id_media_types')=="3"){ // AUDIO
		    
		    
		    $return_array = array(
				'status' => 'STATUS_OK',
				'title' => $rs->fields('title'),
				'filename' => $rs->fields('filename'),
				'mediauri' => $rs->fields('title'),
				'filepath' => $rs->fields('dataserverpath').$rs->fields('filename')
		    );
		    
		    
		    
		    }
				

        return $return_array;
		
		
		}



// Register the method to expose
$server->register('getAnnotationsForRange',                // method name
    array('getAnnotationsForRangeRequest' => 'tns:getAnnotationsForRangeRequest'),        // input parameters
    array('getAnnotationsForRangeResponse' => 'tns:getAnnotationsForRangeResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Get annotations for a Specified Range'            // documentation
);



$server->register('getAnnotationsSearch',                // method name
    array('getAnnotationsSearchRequest' => 'tns:getAnnotationsSearchRequest'),        // input parameters
    array('getAnnotationsSearchResponse' => 'tns:getAnnotationsSearchResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Search annotations in a media'            // documentation
);

$server->register('getTranscriptionsSearch',                // method name
    array('getTranscriptionsSearchRequest' => 'tns:getTranscriptionsSearchRequest'),        // input parameters
    array('getTranscriptionsSearchResponse' => 'tns:getTranscriptionsSearchResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Search transcriptions in a media'            // documentation
);

$server->register('getAutoComplete',                // method name
    array('getAutoCompleteRequest' => 'tns:getAutoCompleteRequest'),        // input parameters
    array('getAutoCompleteResponse' => 'tns:getAutoCompleteResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Search autocomplete keywords'            // documentation
);


// Register the method to expose
$server->register('getTranscriptionsForRange',                // method name
    array('getTranscriptionsForRangeRequest' => 'tns:getTranscriptionsForRangeRequest'),        // input parameters
    array('getTranscriptionsForRangeResponse' => 'tns:getTranscriptionsForRangeResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Get transcriptions for a Specified Range'            // documentation
);



// Register the method to expose
$server->register('getSimilarImages',                // method name
    array('getSimilarImagesRequest' => 'tns:getSimilarImagesRequest'),        // input parameters
    array('getSimilarImagesResponse' => 'tns:getSimilarImagesResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Get sililar images'            // documentation
);


// Register the method to expose
$server->register('editTranscriptions',                // method name
    array('editTranscriptionsRequest' => 'tns:editTranscriptionsRequest'),        // input parameters
    array('editTranscriptionsResponse' => 'tns:editTranscriptionsResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Edit a transcription'            // documentation
);


// Register the method to expose
$server->register('setAnnotations',                // method name
    array('setAnnotationsRequest' => 'tns:setAnnotationsRequest'),        // input parameters
    array('setAnnotationsResponse' => 'tns:setAnnotationsResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'Add new annotation'            // documentation
);



// Register the method to expose
$server->register('delAnnotations',                // method name
    array('delAnnotationsRequest' => 'tns:delAnnotationsRequest'),        // input parameters
    array('delAnnotationsResponse' => 'tns:delAnnotationsResponse'),      // output parameters
    'false',                      // namespace
    'false',                // soapaction
    'document',                                // style
    'literal',                            // use
    'delete annotations'            // documentation
);



// Define the method as a PHP function
function getAnnotationsForRange($getAnnotationsForRange) {

		global $conn;
		//recupero i parametri in input
		
		$sourceId = $getAnnotationsForRange['sourceId'];
		$sessionId = $getAnnotationsForRange['sessionId'];
		$timePoint = $getAnnotationsForRange['timePoint'];
		$duration = $getAnnotationsForRange['duration'];
		$page = $getAnnotationsForRange['page'];
		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		if($duration > $timePoint) {
			$start = 0;
		} else {
			$start = $timePoint - $duration;
		}
		$end = $timePoint + $duration;
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $id_media . "'"; 
		
		//inserisco la condizione sul time point se nella ricerca vengono passati questi valori
		//TODO remove condition $timePoint!="0"
		if($timePoint!="") $cond.= " AND timepoint BETWEEN $start AND $end ";
		
		//inserisco la condizione sul time point se nella ricerca vengono passati questi valori
		if($page!="") $cond.= " AND page = '$page'  ";
		
			
	//scrivo la query
	//$querys = "SELECT id_annotations,annotations.title, starttime, endtime, scrubvalue,concepttype,id_users FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	//$querys = "SELECT id_annotations,annotations.title, timepoint, endpoint, concepttype,id_users, fps FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	
		$query = "SELECT id_annotations,annotations.title, timepoint, endpoint, concepttype, annotations.id_users, fps, username, latitude, deleted, box_x, box_y, box_width, box_height FROM ".EW_CONN_DB_MEDIA.".media, ".EW_CONN_DB_MEDIA.".annotations, ".EW_CONN_DB_MEDIA.".users WHERE media.id_media = annotations.id_media and users.id_users = annotations.id_users and $cond  ORDER BY timepoint,endpoint";
	//lancio della query
	
		add_log('getAnnotationsForRange:search',"Query: $query");

		
		
		$annotations_array = array();
		
		
		
		if ($rs = $conn->Execute($query)) {
			$rs->MoveFirst();
				while(!$rs->EOF) {
					
					$duration = $rs->fields("endpoint")-$rs->fields("timepoint");
					
					$annotation = array(
					 'agent' => "AnnotationToolV1.2",
					   'comment' => "",
					   'confidence' => $rs->fields("confidence"),
					   'created' => 0,
					   'duration' => $duration,
					   'id' => $rs->fields("id_annotations"),
					   'keyword' => "".$rs->fields("title"),
					   'modified' => 0,
					   'owner' => $rs->fields("username"),
					   'sourceId' => $sourceId,
					   'start' => $rs->fields("timepoint"),
					   'type' => $rs->fields("concepttype")
						
					);
					
					
					if($rs->fields("box_x")!="") $annotation['box_x'] = $rs->fields("box_x");
					if($rs->fields("box_y")!="") $annotation['box_y'] = $rs->fields("box_y");
					if($rs->fields("box_width")!="") $annotation['box_width'] = $rs->fields("box_width");
					if($rs->fields("box_height")!="") $annotation['box_height'] = $rs->fields("box_height");
					if($rs->fields("page")!="") $annotation['page'] = $rs->fields("page");
					
					$annotations_array[] = $annotation;
					
					$rs->MoveNext();
				}
			}
				
			
		
				$return_array = array(
					   'id' => 0,
					   'sourceId' => $sourceId,
					   'num' => ($rs->RecordCount()+1),
						'annotations' => $annotations_array
				    );
					
			

		

        return $return_array;
}


function getAnnotationsSearch($getAnnotationsSearch) {

		global $conn;
		//recupero i parametri in input
		
		$sourceId = $getAnnotationsSearch['sourceId'];
		$sessionId = $getAnnotationsSearch['sessionId'];
		$keyword = $getAnnotationsSearch['keyword'];
		$page = $getAnnotationsSearch['page'];
		$record_per_page = $getAnnotationsSearch['record_per_page'];
		$interval  = $getAnnotationsSearch['interval'];
		
		add_log('getAnnotationsSearch:search',"record_per_page: $record_per_page");

		
		//imposto il valore di default
		if(!is_numeric($record_per_page)) $record_per_page = 10;

		if(!is_numeric($page)) $page = 0;

		$start = $page * $record_per_page;
		
		$duration_ext = (is_numeric($interval)?(int)($interval/2):2500);

		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $sourceId . "' AND annotations.title = '".addslashes($keyword)."'  ";
		
			
	//scrivo la query
	//$querys = "SELECT id_annotations,annotations.title, starttime, endtime, scrubvalue,concepttype,id_users FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	//$querys = "SELECT id_annotations,annotations.title, timepoint, endpoint, concepttype,id_users, fps FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	
		$query = "SELECT SQL_CALC_FOUND_ROWS id_annotations,annotations.title, timepoint, endpoint, concepttype, annotations.id_users, fps, username, latitude, deleted FROM ".EW_CONN_DB_MEDIA.".media, ".EW_CONN_DB_MEDIA.".annotations, ".EW_CONN_DB_MEDIA.".users WHERE media.id_media = annotations.id_media and users.id_users = annotations.id_users and $cond  ORDER BY timepoint,endpoint  LIMIT $start,$record_per_page";
	//lancio della query
	
		add_log('getAnnotationsSearch:search',"Query: $query");

	
		$annotations_array = array();
		
		
		
		if ($rs = $conn->Execute($query)) {
			
			$totale = ew_ExecuteScalar("SELECT FOUND_ROWS()");
		
		add_log('getAnnotationsSearch:search',"Record trovati $totale");
			
			
			$rs->MoveFirst();
				while(!$rs->EOF) {
					
					//add_log('getAnnotationsSearch:search',"Entrato risultato");
					
					
					$timepoint = $rs->fields("timepoint");
					$start = $timepoint - $duration;
					$end = $timepoint + $duration;
		
		
		
		//correzione bug selezione video sul titolo
		$cond_pre  = " AND timepoint > $start AND timepoint < $timepoint ";
		$cond_post = " AND timepoint BETWEEN $timepoint AND $end ";
					
					$context_pre = ew_ExecuteScalar("SELECT GROUP_CONCAT(title) from ".EW_CONN_DB_MEDIA.".annotations where id_media = '" . $id_media . "'  AND id_annotations <> '" . $rs->fields("id_annotations") . "'  ".$cond_pre);
					
					$context_post = ew_ExecuteScalar("SELECT GROUP_CONCAT(title) from ".EW_CONN_DB_MEDIA.".annotations where id_media = '" . $id_media . "'  AND id_annotations <> '" . $rs->fields("id_annotations") . "'  ".$cond_post);
					
					
					
					
						
		
					add_log('getAnnotationsSearch:search',"query context_post $context_post: "."SELECT GROUP_CONCAT(title) from ".EW_CONN_DB_MEDIA.".annotations where id_media = '" . $id_media . "'  AND id_annotations <> '" . $rs->fields("id_annotations") . "'  ".$cond_post);
					
					$duration = $rs->fields("endpoint")-$rs->fields("timepoint");
					
					$annotations_array[] = array(
					 'agent' => "AnnotationToolV1.2",
					   'comment' => "",
					   'confidence' => $rs->fields("confidence"),
					   'created' => 0,
					   'duration' => $duration,
					   'id' => $rs->fields("id_annotations"),
					   'keyword' => "".$rs->fields("title"),
					   'modified' => 0,
					   'owner' => $rs->fields("username"),
					   'sourceId' => $sourceId,
					   'start' => $rs->fields("timepoint"),
					   'type' => $rs->fields("concepttype"),
					   'context_pre' => $context_pre,
					   'context_post' => $context_post
					);
					
					
					$rs->MoveNext();
					}
				}
				add_log('getAnnotationsSearch:search',"Terminato ciclo");
				$page_number = (int)(floor(($totale-1)/$record_per_page))+1;
				//$page_number = 0;

		
				$return_array = array(
					   'id' => 0,
					   'sourceId' => $sourceId,
					   'num' => $totale,
					   'page_number' => $page_number,
					   'page_loaded' => $page,
						'searchTerm' => $keyword,
						'annotations' => $annotations_array
				    );
					
			
				add_log('getAnnotationsSearch:search',"Inviata risposta");

		

        return $return_array;
}


// Define the method as a PHP function
function getTranscriptionsSearch($getTranscriptionsSearch) {

		global $conn;
		//recupero i parametri in input
		
		$sourceId = $getTranscriptionsSearch['sourceId'];
		$sessionId = $getTranscriptionsSearch['sessionId'];
		$keyword = $getTranscriptionsSearch['keyword'];
		$page = $getTranscriptionsSearch['page'];
		$record_per_page = $getTranscriptionsSearch['record_per_page'];
		
		add_log('getTranscriptionsSearch:search',"record_per_page: $record_per_page");

		
		//imposto il valore di default
		if(!is_numeric($record_per_page)) $record_per_page = 10;

		if(!is_numeric($page)) $page = 0;

		$start = $page * $record_per_page;
		

		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $id_media . "' AND transcriptions.title LIKE '%".addslashes($keyword)."%' ";
		
			
	//scrivo la query
	//$querys = "SELECT id_annotations,annotations.title, starttime, endtime, scrubvalue,concepttype,id_users FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	//$querys = "SELECT id_annotations,annotations.title, timepoint, endpoint, concepttype,id_users, fps FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	
		$query = "SELECT SQL_CALC_FOUND_ROWS idTranscription,transcriptions.title, timepoint, endpoint,  transcriptions.users_id_users, fps, username FROM ".EW_CONN_DB_MEDIA.".media, ".EW_CONN_DB_MEDIA.".transcriptions, ".EW_CONN_DB_MEDIA.".users WHERE media.id_media = transcriptions.media_id_media and users.id_users = transcriptions.users_id_users and $cond  ORDER BY timepoint,endpoint  LIMIT $start,$record_per_page";
	//lancio della query
	
		add_log('getTranscriptionsForRange:search',"Query: $query");

		
		
		$transcriptions_array = array();
		
		
		
		if ($rs = $conn->Execute($query)) {
			
			$totale = ew_ExecuteScalar("SELECT FOUND_ROWS()");
			
			$rs->MoveFirst();
				while(!$rs->EOF) {
					
				
					
					$duration = $rs->fields("endpoint")-$rs->fields("timepoint");
					
					
					//$title = str_replace($keyword,"<searchTerm>".$keyword."</searchTerm>",);
					
					$transcriptions_array[] = array(
					 'agent' => "transcriptionsToolV1.2",
					   'comment' => "",
					   'confidence' => '',
					   'created' => 0,
					   'duration' => $duration,
					   'id' => $rs->fields("idTranscription"),
					   'keyword' => "".$rs->fields("title"),
					   'modified' => 0,
					   'owner' => $rs->fields("username"),
					   'sourceId' => $sourceId,
					   'start' => $rs->fields("timepoint"),
					   'type' => ''
					);
					
					
					$rs->MoveNext();
					}
				}
				
			
			
				add_log('getTrascriptionsSearch:search',"Terminato ciclo");
				
				$page_number = (int)(floor(($totale-1)/$record_per_page))+1;
				//$page_number = 0;

		
				$return_array = array(
					   'id' => 0,
					   
					   'sourceId' => $sourceId,
					   'num' => $totale,
					   'page_number' => $page_number,
					   'page_loaded' => $page,
					   'searchTerm' => $keyword,
						'transcriptions' => $transcriptions_array
				    );
					
			
				add_log('getTranscriptionsSearch:search',"Inviata risposta");


        return $return_array;
}


// Define the method as a PHP function
function getTranscriptionsForRange($getTranscriptionsForRange) {

		global $conn;
		//recupero i parametri in input
		
		$sourceId = $getTranscriptionsForRange['sourceId'];
		$sessionId = $getTranscriptionsForRange['sessionId'];
		$timePoint = $getTranscriptionsForRange['timePoint'];
		$duration = $getTranscriptionsForRange['duration'];
		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		$start = $timePoint - $duration;
		$end = $timePoint + $duration;
		
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $id_media . "' AND timepoint BETWEEN $start AND $end ";
		
			
	//scrivo la query
	//$querys = "SELECT id_annotations,annotations.title, starttime, endtime, scrubvalue,concepttype,id_users FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	//$querys = "SELECT id_annotations,annotations.title, timepoint, endpoint, concepttype,id_users, fps FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	
		$query = "SELECT idTranscription,transcriptions.title, timepoint, endpoint,  transcriptions.users_id_users, fps, username FROM ".EW_CONN_DB_MEDIA.".media, ".EW_CONN_DB_MEDIA.".transcriptions, ".EW_CONN_DB_MEDIA.".users WHERE media.id_media = transcriptions.media_id_media and users.id_users = transcriptions.users_id_users and $cond  ORDER BY timepoint,endpoint";
	//lancio della query
	
		add_log('getTranscriptionsForRange:search',"Query: $query");

		
		
		$transcriptions_array = array();
		
		
		
		if ($rs = $conn->Execute($query)) {
			$rs->MoveFirst();
				while(!$rs->EOF) {
					
				
					
					$duration = $rs->fields("endpoint")-$rs->fields("timepoint");
					
					$transcriptions_array[] = array(
					 'agent' => "transcriptionsToolV1.2",
					   'comment' => "",
					   'confidence' => '',
					   'created' => 0,
					   'duration' => $duration,
					   'id' => $rs->fields("idTranscription"),
					   'keyword' => "".$rs->fields("title"),
					   'modified' => 0,
					   'owner' => $rs->fields("username"),
					   'sourceId' => $sourceId,
					   'start' => $rs->fields("timepoint"),
					   'type' => ''
					);
					
					
					$rs->MoveNext();
					}
				}
				
			

		
				$return_array = array(
					   'id' => 0,
					   'sourceId' => $sourceId,
					   'num' => ($rs->RecordCount()+1),
						'transcriptions' => $transcriptions_array
				    );
					
			

		

        return $return_array;
}



/*
// Define the method as a PHP function
function setAnnotations($setAnnotationsRequest) {

		global $conn;
		//recupero i parametri in input
		
		$ontology_default = "25";
		
		
		$sourceId = $setAnnotationsRequest['sourceId'];
		$sessionId = $setAnnotationsRequest['sessionId'];
		
		//value in millisecond
		$timePoint = $setAnnotationsRequest['timePoint'];
		$duration = $setAnnotationsRequest['duration'];
		
		
		$keyword = $setAnnotationsRequest['keyword'];
		$owner = $setAnnotationsRequest['owner'];
		
		
		$id_users = ew_ExecuteScalar("select id_users from ".EW_CONN_DB_MEDIA.".users WHERE  (id_users = '$owner' OR username = '$owner')");
		if($id_users=="") $id_users = "1";
		
		
		
		//duration default value
		if($duration=="" || $duration == null) $duration = 5000;
		
		$endPoint = $timePoint + $duration;
		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $id_media . "' ";
		
		
		//check if media already exists in the database
		$exists = ew_ExecuteScalar("select count(*) from ".EW_CONN_DB_MEDIA.".media WHERE  $cond ");
		if(!$exists){
		
		
		
			    $return_array = array(
								'message' 	=> 'Video with sourceId '.$sourceId.' not found ',
								'status'         => 'STATUS_ERROR'
				);
				return $return_array;
		
		}
		
		
		//check if concept already exists in concept table
		
		$id_concepts = ew_ExecuteScalar("SELECT id_concepts FROM ".EW_CONN_DB_MEDIA.".concepts WHERE concepts.name='".$keyword."'");
		if(!$id_concepts){
			
				//inserting new concept
				$insQuery = "INSERT INTO ".EW_CONN_DB_MEDIA.".concepts (name , id_ontologies) VALUES ( '" . $conceptname . "', '" . $$ontology_default . "')";
				
				$conn->Execute($insQuery);
				
				$id_concepts = $conn->Insert_ID();
		
		}

		
		$thumbnail_insert = "null";
		
		
		

		
		
		//creo la query di inserimento del concetto
		$querys = "INSERT INTO ".EW_CONN_DB_MEDIA.".annotations (id_media, title, timepoint, endpoint, id_users, id_annotations_types,thumbnail) VALUES ('" . $id_media . "', '" . addslashes($keyword) . "', '" . $timePoint . "', '" . $endPoint . "',  '" . $id_users . "', '2',$thumbnail_insert)";
		//echo $querys;
		
		$conn->Execute($querys);
		
		$id_annotations = $conn->Insert_ID();
		
		add_log('setAnnotations:search',"Query: $querys");
		
		
		
		$return_array = array(
								'id' 	=> $id_annotations,
								'concept' 	=> $keyword,
								'message' 	=> 'Annotation added',
								'status'         => 'STATUS_OK'
						);
		return $return_array;
		

}

*/

// Define the method as a PHP function
function setAnnotations($setAnnotationsRequest) {

		global $conn,$ontology_default;
		//recupero i parametri in input
		
		$ontology_default = "25";
		
		
		$sessionId = $setAnnotationsRequest['sessionId'];
		
		
		
		
		$annotations_array = array();
		
		$count_added = 0;
		$count_updated = 0;
				
		if(is_array($setAnnotationsRequest['annotations']['annotation'][0])) {
			
			
			add_log('setAnnotations:start',"Multi annotation - Total annotation: ".count($setAnnotationsRequest['annotations']['annotation'])." input: ".json_encode($setAnnotationsRequest));

				foreach($setAnnotationsRequest['annotations']['annotation'] as $annotation)
				{
					$new_annotation = insert_annotation($annotation);
					if($new_annotation) {
						$annotations_array[] = $new_annotation;
						if(@is_numeric($annotation['id']))
					 			$count_updated++;
								else $count_added++;
					}
				}
		
		} else {
			
			
			$new_annotation = insert_annotation($setAnnotationsRequest['annotations']['annotation']);
					if($new_annotation) {
						$annotations_array[] = $new_annotation;
						if(@is_numeric($setAnnotationsRequest['annotations']['annotation']['id']))
					 			$count_updated++;
								else $count_added++;
					}
			
			
			}
		
		
		add_log('setAnnotations:annotations add complete',"Added $count_added annotation | Updated $count_updated annotations");
		
		$return_array = array(
					   'message' 	=> 'Annotations added/updated',
					'status'         => 'STATUS_OK',
						'annotations' => $annotations_array
				    );
		
		return $return_array;
		

}




function delAnnotations($delAnnotationsRequest) {

		global $conn;
		//recupero i parametri in input
		
		$id = $delAnnotationsRequest['id'];
		$owner = $delAnnotationsRequest['owner'];
		

		//check if media already exists in the database
		$exists = ew_ExecuteScalar("select count(*) from ".EW_CONN_DB_MEDIA.".annotations WHERE id_annotations = '$id' ");
		if(!$exists){
		
		
		
			    $return_array = array(
								'message' 	=> 'Annotation with id '.$sourceId.' not found ',
								'status'         => 'STATUS_ERROR'
				);
				return $return_array;
		
		}
		
		$keyword = ew_ExecuteScalar("select title from ".EW_CONN_DB_MEDIA.".annotations WHERE id_annotations = '$id' ");
		
			
		//inserting new concept
		$delQuery = "DELETE FROM ".EW_CONN_DB_MEDIA.".annotations  WHERE id_annotations = '$id' ";
		
		$conn->Execute($delQuery);
		
		
		add_log('delAnnotations:complete',"Query: $delQuery");
		
		
		
		$return_array = array(
								'id' 		=> $sourceId,
								'concept' 	=> $keyword,
								'message' 	=> 'Annotation deleted',
								'status'		=> 'STATUS_OK'
						);
		return $return_array;
		

}


function editTranscriptions($editTranscriptionsRequest) {

		global $conn;
		//recupero i parametri in input
		
				add_log('ediTranscriptions:start',"Params ".json_encode($editTranscriptionsRequest));

		
		$id = $editTranscriptionsRequest['id'];
		$owner = $editTranscriptionsRequest['owner'];
		$sourceId = $editTranscriptionsRequest['sourceId'];
		$sessionId = $editTranscriptionsRequest['sessionId'];
		$keyword = $editTranscriptionsRequest['keyword'];
		
		
		$id_users = ew_ExecuteScalar("select id_users from ".EW_CONN_DB_MEDIA.".users WHERE  (id_users = '$owner' OR username = '$owner')");
		if($id_users=="") $id_users = "1";
		
		
		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		

		//check if media already exists in the database
		$query = "select * from ".EW_CONN_DB_MEDIA.".transcriptions WHERE idTranscription = '$id' ";
		
		
		$rs = $conn->Execute($query);
		
		add_log('ediTranscriptions:check',"Found ".$rs->RecordCount().". Query: $query");

			
		if($rs->RecordCount()==0){
			
			 	$return_array = array(
								'message' 	=> 'Transcription with id '.$id.' not found ',
								'status'         => 'STATUS_ERROR'
				);
				return $return_array;
		
		}
		
		$rs->MoveFirst();
		
		$timepoint =  (int)$rs->fields('timepoint');
		$duration =  (int)($rs->fields('endpoint')-$rs->fields('timepoint'));
		
			
		//inserting new concept
		$updQuery = "UPDATE ".EW_CONN_DB_MEDIA.".transcriptions  SET title = '" . addslashes($keyword) . "' WHERE idTranscription = '$id' ";
		$conn->Execute($updQuery);
		
		
		add_log('ediTranscriptions:complete',"Query: $updQuery");
		
		
		
		$return_array = array(
								'id' 		=> $id,
								'timePoint' 	=> $timepoint,
								'duration' 	=> $duration,
								'message' 	=> 'Transcription edited',
								'status'		=> 'STATUS_OK'
						);
		return $return_array;
		

}

function getAutoComplete($getAutoComplete) {

		global $conn;
		//recupero i parametri in input
		
		$sourceId = $getAutoComplete['sourceId'];
		$sessionId = $getAutoComplete['sessionId'];
		$keyword = $getAutoComplete['keyword'];
		
		add_log('getAutoComplete:search',"keyword: $keyword");

		
		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		
		//correzione bug selezione video sul titolo
		$cond = " annotations.id_media ='" . $sourceId . "' AND annotations.title LIKE '".addslashes($keyword)."%'  ";
		
			
	//scrivo la query
	//$querys = "SELECT id_annotations,annotations.title, starttime, endtime, scrubvalue,concepttype,id_users FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	//$querys = "SELECT id_annotations,annotations.title, timepoint, endpoint, concepttype,id_users, fps FROM media JOIN annotations ON media.id_media = annotations.id_media WHERE media.title = '" . $film . "'";
	
		$query = "SELECT SQL_CALC_FOUND_ROWS id_annotations,annotations.title FROM ".EW_CONN_DB_MEDIA.".annotations WHERE $cond GROUP BY title ORDER BY title ";
	//lancio della query
	
		add_log('getAutoComplete:search',"Query: $query");

	
		$annotations_array = array();
		
		
		
			if ($rs = $conn->Execute($query)) {
				
				$totale = ew_ExecuteScalar("SELECT FOUND_ROWS()");
			
			add_log('getAutoComplete:search',"Record trovati $totale");
				
				
				$rs->MoveFirst();
				while(!$rs->EOF) {
					
					$annotations_array[] = $rs->fields("title");
					$rs->MoveNext();
					
					}
				}
				
				add_log('getAutoComplete:search',"Terminato ciclo");
				
				//$page_number = (int)(round($totale/$record_per_page))+1;
				//$page_number = 0;

		
				$return_array = array(
								'keywords' 		=> $annotations_array
						);
					
			
				add_log('getAutoComplete:search',"Inviata risposta");

		

        return $return_array;
}


// Define the method as a PHP function
function getSimilarImages($input) {

		global $conn;
		//recupero i parametri in input
		
		$sourceId = $input['sourceId'];
		$sessionId = $input['sessionId'];
		$timePoint = $input['timePoint'];
		$page = $input['page'];
		$record_per_page = $input['record_per_page'];
		$exact_timepoint = $input['exact_timepoint'];
		$find_all = $input['find_all'];
		
		
		
		add_log('getSimilarImages:search',"record_per_page: $record_per_page");

		
		//imposto il valore di default
		if(!is_numeric($record_per_page)) $record_per_page = 10;

		if(!is_numeric($page)) $page = 0;

		$start = $page * $record_per_page;
		
		
		
		
		$query = "SELECT thumbnail,id_annotations,timepoint from ".EW_CONN_DB_MEDIA.".annotations WHERE id_media = '$sourceId' AND timepoint <= $timePoint AND thumbnail is not null ORDER BY timepoint DESC LIMIT 0,1";
	//lancio della query
	
		add_log('getSimilaImages:search',"Query: $query");
		
		
		if ($rs = $conn->Execute($query)) {
			
			$filename = $rs->fields('thumbnail');
			
		}
		
		else return;

	
		$annotations_array = array();
		
		
		
		
		
		//caso di immagine non trovata in db
		if($filename==""){
			$return_array = array(
					   
					   'num' => 0,
					   'page_number' => 0,
					   'page_loaded' => 0,
					   'images' => null
				    );
			
			add_log('searchSimilarImages:search',"immagine non trovata con filename $filename");

			return $return_array;
		}	
		
		
		$numResults = 50;
		
					// EXECUTING VIDEO ANALYZING PROCESS
					$runCall = "http://127.0.0.1:8080/daphnis/qbn?imageURI=local_".$filename."&mode=compact&numResults=$numResults";
					
					add_log('searchSimilarImages:search',"sourceId $sourceId | timepoint: $timePoint | numResults: $numResults | filename: $filename | query: $runCall");

					
					
					//$result = "only test<br>";	
					$result = file_get_contents($runCall);
					
					
					add_log('searchSimilarImages:result',json_encode($result));

					
					$result_parsed = $result;
					
					$num = 0;
					
					
							$xml = simplexml_load_string($result);
							
							//add_log('searchSimilarImages:result',"result found $num filename ".$item->filename);
							add_log('searchSimilarImages:parsing xml',json_encode($xml));
							
							add_log('searchSimilarImages:resultfound','Risultati trovati: '.count($xml->item));

							foreach($xml->item as $item)
							{
								
								add_log('searchSimilarImages:result',"result found $num filename ".$item->filename);
								
								$query = "SELECT id_media,timepoint from ".EW_CONN_DB_MEDIA.".annotations WHERE thumbnail = '".$item->filename."' ";
								
								
								if ($rs_app_image = $conn->Execute($query)) {
									
									$num++;
									
									$images[] = array(
									 	'sourceId' => $rs_app_image->fields('id_media'),
									     'filename' => $item->filename,
									   	'url' => EW_IMAGE_HTTP_URL.$item->filename,
									   	'timePoint' => $rs_app_image->fields('timepoint')
									);
			
										
								}
								
							}
										
					

			/*	
				$images[] = array(
						 'sourceId' => "00002",
						   'filename' => "test_inserita_manualmente.jpg",
						   'url' => 'http://www.sadfasdfsafas.it/sdfafds.jpg',
						   'timePoint' => 25353
						   
						);
				*/

		
				$return_array = array(
					   
					   'num' => $totale,
					   'page_number' => $page_number,
					   'page_loaded' => $page,
					   'images' => $images
				    );
					
			
				add_log('getSimilarImages:search',"Inviata risposta");


        return $return_array;
}


// Use the request to (try to) invoke the service
$post_data = file_get_contents("php://input");;
$server->service($post_data);

function insert_annotation($annotation){
	
	global $conn,$ontology_default;
	
	//value in milliseconds
		$sourceId = $annotation['sourceId'];
		$timePoint = (@is_numeric($annotation['timePoint'])?$annotation['timePoint']:$annotation['start']);
		$id = (@is_numeric($annotation['id'])?$annotation['id']:null);
		
		$duration = $annotation['duration'];
		add_log('setAnnotations:adding',"input annotation: ".json_encode($annotation));
		
		
		$keyword = $annotation['keyword'];
		$owner = $annotation['owner'];
		
		
		$id_users = ew_ExecuteScalar("select id_users from ".EW_CONN_DB_MEDIA.".users WHERE  (id_users = '$owner' OR username = '$owner')");
		if($id_users=="" || $id_users=="?") $id_users = "1";
		
		
		//duration default value
		if($duration=="" || $duration == null) $duration = 5000;
		
		$endPoint = $timePoint + $duration;
		
		//$id_media = Ew_ExecuteScalar("SELECT id_media FROM ".EW_CONN_DB_MEDIA.".media WHERE uri = '$sourceId'");
		$id_media = $sourceId;
		
		
		
		//correzione bug selezione video sul titolo
		$cond = " media.id_media ='" . $id_media . "' ";
		
		
		//check if media already exists in the database
		$exists = ew_ExecuteScalar("select count(*) from ".EW_CONN_DB_MEDIA.".media WHERE  $cond ");
		if(!$exists){
		
				return false;
			    
		
		}


		
		
		//check if concept already exists in concept table
		
		$id_concepts = ew_ExecuteScalar("SELECT id_concepts FROM ".EW_CONN_DB_MEDIA.".concepts WHERE concepts.name='".$keyword."'");
		if(!$id_concepts){
			
				//inserting new concept
				$insQuery = "INSERT INTO ".EW_CONN_DB_MEDIA.".concepts (name , id_ontologies) VALUES ( '" . $conceptname . "', '" . $ontology_default . "')";
				
				$conn->Execute($insQuery);
				
				$id_concepts = $conn->Insert_ID();
		
		}

		
		$thumbnail_insert = "null";
		
		
		
		/*$starttime_point = round($starttime*1000/$fps);
		$endtime_point = round($endtime*1000/$fps);
		
		$start_time_second = (int)round($starttime/$fps);
		
		fwrite($fh, "selezionato secondo esportazione : ".$start_time_second."\n");

		//estrazione della thumbnail
		$starttime_point_extraction = sec2hms($start_time_second);
		fwrite($fh, "selezionato timecode esportazione : ".$starttime_point_extraction."\n");

		$thumbnail_insert = "'$filename-$starttime.png'";
		
		$command = "ffmpeg -i ".EW_VIDEO_PATH."$filename -r 1 -t $starttime_point_extraction -f image2 ".EW_IMAGE_PATH."$thumbnail_name";
		
		fwrite($fh, "\neseguito: ".$command."\n");

		$result = exec($command);

		*/

		$table = EW_CONN_DB_MEDIA.".annotations";
		
		$insert_array = array(
		'id_media' => $id_media, 
		'title' => addslashes($keyword) ,
		'timepoint' => $timePoint,
		'endpoint' => $endPoint,
		'id_users' => $id_users,
		'id_annotations_types' => '2',
		'thumbnail' => $thumbnail_insert);
		
		if($id_concepts!="") $insert_array['id_concepts'] = $id_concepts;
		
		
		if($id!="") $insert_array['id_annotations'] = $id;
		

		
		
		if($annotation['box_x'] && $annotation['box_x']!="?") 	$insert_array['box_x'] 		= $annotation['box_x'];
		if($annotation['box_y'] && $annotation['box_y']!="?") 	$insert_array['box_y'] 		= $annotation['box_y'];
		if($annotation['box_width'] && $annotation['box_width']!="?") 	$insert_array['box_width'] 	= $annotation['box_width'];
		if($annotation['box_height'] && $annotation['box_height']!="?") $insert_array['box_height'] 	= $annotation['box_height'];
		if($annotation['page'] && $annotation['page']!="?") $insert_array['page'] 	= $annotation['page'];
		
		$task = "";
		if(isset($annotation['id']) &&  $annotation['id']!="" &&  $annotation['id']!="?"){ //caso di update
		$task = "update";
		
			$id_annotations = $annotation['id'];
			$querys = mysql_update_query($table, $insert_array,'id_annotations');
			$conn->Execute($querys);
		
	
		} else {//caso di inserimento
		$task = "insert";
			
			$querys = mysql_insert_query($table, $insert_array);
			$conn->Execute($querys);
			$id_annotations = $conn->Insert_ID();
		
			
			}
        //aggiorno il campo last_modified del media per consentirne l'indicizzazione
        $now = date('Y-m-d H:i:s');
        $querys = "UPDATE ".EW_CONN_DB_MEDIA.".media SET last_modified = '".$now."' WHERE id_media = '".$id_media."'";
        $conn->Execute($querys);

		
		$annotation_insert = array(
					   'agent' => "AnnotationToolV1.2",
					   'comment' => "",
					   'confidence' => 1,
					   'created' => 0,
					   'duration' => $duration,
					   'id' => $id_annotations,
					   'keyword' => "".$keyword,
					   'modified' => 0,
					   'owner' => $owner,
					   'sourceId' => $sourceId,
					   'start' => $timePoint,
					   'type' => '',
					   'context_pre' => "",
					   'context_post' => ""
					);
					
		
		if($annotation['box_x'] && $annotation['box_x']!="?") 	$annotation_insert['box_x'] 		= $annotation['box_x'];
		if($annotation['box_y'] && $annotation['box_y']!="?") 	$annotation_insert['box_y'] 		= $annotation['box_y'];
		if($annotation['box_width'] && $annotation['box_width']!="?") 	$annotation_insert['box_width'] 	= $annotation['box_width'];
		if($annotation['box_height'] && $annotation['box_height']!="?") $annotation_insert['box_height'] 	= $annotation['box_height'];
		if($annotation['page'] && $annotation['page']!="?") $annotation_insert['page'] 	= $annotation['page'];
		
	
		
					
		add_log('setAnnotations:annotation '.$task,"ID: $id_annotations Query: $querys annotation: ".json_encode($annotation_insert));

					
					
			return $annotation_insert;
					
		
		
	
	}
?>
