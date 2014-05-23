##Loki 
Loki is a framework for the creation of web-based interfaces for search, annotation and presentation of multimedia data.
The framework provides tools to ingest, transcoder, present, annotate and index different types of media such as images, videos, audio files and textual documents. The fronted is compliant with the latest HTML5 standards, while the backend allows users to create processing pipelines that can be adapted for different tasks and purposes.

Author: Media Integration and Communication Center - http://www.micc.unifi.it

More info: http://www.micc.unifi.it/vim/opensource/loki-a-cross-media-search-engine/

---
###How to install


Requirements:
xAMP (Apache HTTP Server + MySQL)
Tomcat 
Solr
ImageMagick (http://www.imagemagick.org)
pdf2svg (http://www.cityinthesky.co.uk/opensource/pdf2svg/)
pdftk (http://www.pdflabs.com/tools/pdftk-server/)
ffmpeg (http://www.ffmpeg.org/)


Copy all files from 
Loki / web /  
folder into your document root.

Create a database and create the required schema restoring the SQL dump from:
/install/db/micc_interface_empty.sql.gz

Download the latest version of Solr from:
http://apache.fastbull.org/lucene/solr/4.7.1/solr-4.7.1.tgz

Un-tar the file and deploy Solr under Tomcat following this tutorial:
https://wiki.apache.org/solr/SolrTomcat#Installing_Solr_instances_under_Tomcat

Copy all the lib JARs from
/install/solr/lib
into the Solr installation and link them from solr-config.xml files.

Alternatively you can copy all these files under the Tomcat web app lib/ folder (e.g. ….//WEB-INF/lib/) this is not the best way but it is very consistent.

Copy in the same folder the logging configuration file: from
 solr/example/resources/log4j.properties to Tomcat web app lib folder (e.g. ….//WEB-INF/lib/)


In solr-config.xml check if this include lines are correctly linking to the jars
```
<lib dir="../../../contrib/dataimporthandler/lib" regex=".*\.jar" />
<lib dir="../../../dist/" regex="solr-dataimporthandler-\d.*\.jar" />

<lib dir="../../../dist/solrj-lib" regex=".*\.jar" />
```
In solr-config.xml check if it is defined the mysql data import handler
```
<requestHandler name="/dataimport" class="org.apache.solr.handler.dataimport.DataImportHandler">
    <lst name="defaults">
      <str name="config">data-config.xml</str>
    </lst>
</requestHandler>
```

Modify the following files with your server database, Solr and Tomcat configuration
- app/config.php
- app/js/config.js
- app/js/serviceInclude.js
- service/config/db-default.php


Solr

Update DB params and path in the Solr DataImporterHandle (DIH) configuration file:
Loki/install/solr/conf/data-config.xml

Copy all the files from Loki/install/solr/conf/ to your Solr config folder


---
###License

Copyright 2014 Micc (Media Integration and Communication Center) http://www.micc.unifi.it

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
