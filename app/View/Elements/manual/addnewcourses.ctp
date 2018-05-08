<!-- 
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
-->

<h2>Add new course</h2> 
<hr>
<hr>
<h3>
	<?php echo $this->Html->image('manual/addcourses.png', array(
		'width' => 511,
		'height' => 294,
		'alt' => 'find the form'
	)); ?>
	Find the form
</h3>
<p>
    If you would like to add a new course please select „New course“ on the start screen 
	after you have logged in (on the dashboard). There you can find the form to submit the information.<br>
	To go there directly, click <?php echo $this->Html->link('here', '/courses/add'); ?>.
</p>
<hr>

<div>
	<h3>
		<?php echo $this->Html->image('manual/Filloutform.png', array(
			'width' => 511,
			'height' => 294,
			'alt' => 'filling the form'
		)); ?>
		Fill out the form
	</h3>
	<p>
		If you are missing anything in the drop-down list, please contact us so we are able to add the needed information.
		Most of the fields in the form are self-explaining. Hopefully all the others are explained in the following:
	</p>
	<h4>Coursetype Id</h4>
    <p>
		
	</p>
	<h4>URL</h4>
    <p>
		There is not only a field to add the URL but also a checkbox "Skip URL Validation". 
		The reason is simple: in some cases the validation of the URL is not working. 
		Therefore you can check the box and nevertheless enter your URL. Please be careful and check the URL 
		before you check the box. 
	</p>
	<h4>Lon and Lat</h4>
    <p>
		For showing the courses on the map the system needs the geocoordinates of the places. 
		There are many tools for finding out the right numbers. 
		This is one of them: <a href="http://itouchmap.com/latlong.html">itouchmap</a>
	</p>
    <h4>Tadirah</h4>
    <p>
		<a href="http://tadirah.dariah.eu/vocab/index.php">Tadirah</a> is a taxonomy for the Digital Humanities. 
		Please provide at least one keyword for Activity, Technique and Object of your course. 
	</p>
</div>
<hr>

<div>
	<h3>
		<?php echo $this->Html->image('manual/submitproblems.png', array(
			'width' => 511,
			'height' => 294,
			'alt' => 'problems while submitting the form'
		)); ?>
		Submit the form
	</h3>
	<p>
		If you click the button "submit" your information will be sent to the database. 
		Before the system saves your information, the fields are being validated. If you got validation problems, 
		please read the advice of sections „Fill out the form“ and FAQ.
	</p>
	<hr>
</div>
<hr>

<div>
    <h3>
		<img src="/img/FAQ.png" width="411" height="294" align="right" vspace="10" hspace="20" alt="Text?">
		FAQ
	</h3>
    <h4>The needed information is not in the list.</h4>
    <p>
		We are sorry - Please contact us to add the needed information.
	</p>
	<h4>My courses only appear on the dashboard but not in the registry.</h4>
    <p>
		You have to publish your course. You can do so if you check the box "publish" in the form. 
		If you havent looked for your courses more than a year they will first turn to red and finally 
		dissapear until you have check the information.
	</p>
	<h4>I don’t know Lat and Lon.</h4>
    <p>
		please use a tool like <a href="http://itouchmap.com/latlong.html">itouchmap</a> to find out the geo coordinates.
	</p>
	
	<h4>My url is not valid but everything is right.</h4>
    <p>
		Please check the box "Skip URL Validation".
	</p>
</div>
<hr>
<hr>




