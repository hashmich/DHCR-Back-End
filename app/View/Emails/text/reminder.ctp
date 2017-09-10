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
<?php $name = (!empty($data['maintainer'])) ? $data['maintainer'] : 'User'; ?>
Dear <?php echo $name; ?>!

We noticed, that one or more of your entries in 
the Digital Humanities Course Registry have not been updated for a longer time. 

To prevent the registry from showing outdated information, 
please review the listed records. 
Please note, that you must submit the edit form, even if the information does not change.  
This will update the 'last-modification-date' of your record. 

If you have many courses whose information definitely did not change, you may alternatively 
make use of the 'revalidate' button on each course row on your dashboard. 
Using this feature saves you from unneccessarily loading the course edit form, if 
your data remains the same.  

Information older than <?php echo round(Configure::read('App.CourseExpirationPeriod') / (60*60*24*365), 1); ?> years will be automatically removed 
from the registry. 

Please review the linked courses:
<?php
foreach($data as $id => $record) {
	if($id == 'maintainer') continue;
	echo "Course: \n" . $record['Course']['name'] . "\n";
	echo Router::url('/edit/' . $id, $full = true);
	echo "\n\n";
}
?>

Many thanks! 

