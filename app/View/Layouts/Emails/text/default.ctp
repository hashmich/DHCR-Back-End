<?php
/**
 * Copyright 2014 Hendrik Schmeer on behalf of DARIAH-EU, VCC2 and DARIAH-DE,
 * Credits to Erasmus University Rotterdam, University of Cologne, PIREH / University Paris 1
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<?php
echo $this->fetch('content');
echo "\n\n\n";
echo "--\n\n";
echo "The Digital Humanities Course Registry: \n";
// because emails are being sent from commandline, Router::baseURL would be 'localhost'!
if(Configure::read('App.consoleBaseUrl')) echo Configure::read('App.consoleBaseUrl');
else echo Router::url('/', true);
?>


