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

We have noticed, that information URLs of one or more entries you maintain in 
the Digital Humanities Course Registry are not valid any more. 

Please have a look at the listed erroneous courses and update the information soon.
Log in before to find a handy edit link ("review") next to the linked course descriptions.
<?php
echo Router::url(array(
	'admin' => false,
	'plugin' => null,
	'controller' => 'users',
	'action' => 'login'
), $full = true);
echo "\n\n";

foreach($data as $id => $course) {
	if($id == 'maintainer') continue;
	
	echo "Course: \n".$course['Course']['name']."\n";
	echo Router::url(array(
		'admin' => false,
		'plugin' => null,
		'controller' => 'courses',
		'action' => 'view',
		$id
	), $full = true);
	echo "\n";
	foreach($course['errors'] as $field => $errors) {
		$fieldname = $field;
		$field = ($field == 'url') ? 'Information URL' : $field;
		$field = ($field == 'guide_url') ? 'Curriculum URL' : $field;
		echo $field . ": \n";
		foreach($errors as $error) {
			echo "\t" . $error . "\n";
		}
		echo "\tvalue: ".$course['Course'][$fieldname]."\n";
	}
	echo "\n";
}
?>

In rare cases, this checking algorithm reports errors where 
humanoid users can successfully visit the reported URLs. 
If you happen to find your carefully checked URL marked invalid by this 
email or by the form validation during review, please check the tickbox 
'Skip URL Validation', to let the form pass validation on submit. 
In case your course information has not changed and the data is still up to date, 
(state green) you may also ignore this email.    

Many thanks! 

