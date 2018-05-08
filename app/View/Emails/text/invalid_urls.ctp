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
Dear <?php echo $name; ?>.

We have noticed, that information URLs of one or more entries you maintain in 
the Digital Humanities Course Registry may not be valid any more.  
Due to the returned http status code, we suspect there might be something wrong.  

As we want to prevent showing dead links to our audience, 
please have a look at the listed erroneous records and update the information soon.
Please review all data thoroughly and submit the form. The validation logic will
highlight all found errors. Additional options will appear, to get an
URL past the validation, that you proved to be functional.

In rare cases, this checking algorithm reports errors where
human users can still visit the reported URLs.
If you happen to find your carefully checked URL reported invalid by this
email, please submit the form, find the reported errors and make sure to tick
the "skip url validation" tickboxes. This choice will be remembered until the
course record's valid date has expired.

If you are not logged in to the DH-Course Registry, you will be redirected to
the login form before accessing the according course edit form.

Many thanks!



<?php
foreach($data as $id => $course) {
	if($id == 'maintainer') continue;
	
	echo "Course: \n".$course['Course']['name']."\n";
	echo Router::url('/edit/' . $id, $full = true);
	echo "\n";
	foreach($course['errors'] as $field => $errors) {
		$fieldname = $field;
		$field = ($field == 'info_url') ? 'Information URL' : $field;
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
