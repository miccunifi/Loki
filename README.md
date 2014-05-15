##Loki 
Loki is a framework for the creation of web-based interfaces for search, annotation and presentation of multimedia data.
The framework provides tools to ingest, transcoder, present, annotate and index different types of media such as images, videos, audio files and textual documents. The fronted is compliant with the latest HTML5 standards, while the backend allows users to create processing pipelines that can be adapted for different tasks and purposes.

Author: Media Integration and Communication Center) http://www.micc.unifi.it

More info: http://www.micc.unifi.it/vim/opensource/loki-a-cross-media-search-engine/

---
###How to install


Requirements:
Lamp
Tomcat 
Solr


Copy all files from Loki / web /  folder into your document root:

Create a database and restore the dump from:
/install/db/micc_interface_empty.sql.gz


Modify the following files with your server configuration
-app/config.php
-app/js/config.js
-app/js/im3include.js
-service/config/db-default.php

Create a new 

Solr
update db params and path into the solr dig configuration file:
Loki / install / solr / data-config.xml

copy all files from Loki / install / solr /  to your solr installation


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
