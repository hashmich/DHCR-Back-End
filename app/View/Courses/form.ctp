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
<h2><?php echo ucfirst($this->action); ?> Course</h2>

<?php
if($this->action == 'edit') {
	?>
	<p class="actions">
	<?php
	echo $this->Html->link('Delete this Course', '/courses/delete/' . $this->request->data['Course']['id'], array(
		'confirm' => "Are you sure? /n
			Courses not updated for " . round(Configure::read('App.CourseArchivalPeriod') / (60*60*24*365), 1) . " years will be 
			archived for later research and disappear from your dashboard automatically. \n
			Please only remove this entry, if it was only created by mistake. \n\n
			You can uncheck the 'publish' option if you do not want this entry to display in the registry any longer and 
			stop the update reminder email service."
	));
	?>
	</p>
	<?php
}


echo $this->element('Utils.validation_errors');


echo $this->Form->create('Course', array('novalidate' => 'novalidate'));
?>

<fieldset>
	<?php
	if($this->action == 'edit') {
		echo $this->Form->input('id', array('disabled' => true, 'type' => 'text'));
		?>
		
		<p>Courses are not displayed any more, if the "last-update" field's date is too old. </p>
		<p>To mark this record as up-to-date, you have to submit this form, even if the information did not change.</p>
		
		<?php
		if(!empty($admin)) {
			?>
			<p>
				Leave this box unchecked to <strong>not</strong> update the "last-update" field when saving your revisions.
			</p>
			<p>
				Owners of course records are emailed based on the date in the field "last-update" 
				to keep their entries alive.
				Please consider this if you are making changes to entries not maintained by yourself.
			</p>
			<?php
			echo $this->Form->input('update', array(
				'type' => 'checkbox',
				'label' => 'Update Timestamp',
				'checked' => true,
				'value' => 1
			));	// do or not update the timestamp 
		}

		echo $this->Form->input('user_id', array(
			'label' => 'Maintainer',
			'empty' => ' -- nobody -- '
		));
	}
	?>
</fieldset>
<fieldset>
	<?php
	echo $this->Form->input('name', array(
		'label' => 'Course Name'
	));
	echo $this->Form->input('description', array('type' => 'textarea'));
	echo $this->Form->input('course_type_id', array(
			'empty' => ' -- none -- ',
			'label' => 'Education Type'
	));
	echo $this->Form->input('language_id', array('empty' => ' -- none -- '));
	echo $this->Form->input('access_requirements');
	echo $this->Form->input('start_date', array('title' => 'One or many course start dates, format YYYY-MM-DD, separated by ";".'));
	echo $this->Form->input('recurring', array(
		'title' => 'Check box if the course begins every year at the same date. Uncheck if the course takes place only once.',
		'required' => false
	));
	?>
</fieldset>
<fieldset>
	<?php
	if(!empty($errors) OR ($modelName AND !empty($this->validationErrors[$modelName]))) {
		?>
		<div class="notice">
			<p>
				URL Validation has been set up to assist you providing valid content. <br />
				But sometimes you may have trouble to get URLs past validation, due to weird 
				http status codes (codes > 300) or restrictive server settings.
			</p>
			<p>
				Please check these boxes, if you thoroughly checked the URLs, 
				but repeatedly keep getting validation errors.
			</p>
			<?php
			echo $this->Form->input('skip_info_validation', array(
				'label' => 'Skip Info URL Validation',
				'type' => 'checkbox',
				'checked' => false,
				'value' => 1
			));
			echo $this->Form->input('skip_guide_validation', array(
				'label' => 'Skip Guide URL Validation',
				'type' => 'checkbox',
				'checked' => false,
				'value' => 1
			));
		echo '</div>';
	}
	
	echo $this->Form->input('info_url', array(
		'label' => 'Information URL',
		'title' => 'Course information URL.',
		'type' => 'text'
	));
	echo $this->Form->input('guide_url', array(
		'label' => 'Curriculum URL',
		'type' => 'text',
		'title' => 'URL of a course guide (eg a .pdf), that describes the course modules and structure.'
	));
	?>
</fieldset>
<fieldset>
	<?php
	echo $this->Form->input('ects', array('title' => 'Decimal numbers only. Optionally use the decimal point.'));
	echo $this->Form->input('contact_name');
	echo $this->Form->input('contact_mail');
	?>
</fieldset>
<fieldset>
	<?php
	$opts = array('empty' => ' -- none -- ');
	if($this->action === 'add' AND !empty($auth_user) AND !empty($auth_user['institution_id']))
		$opts = array('default' => $auth_user['institution_id']);
	echo $this->Form->input('institution_id', $opts);
	echo $this->Form->input('department');


    // picker is rendered using javascript, hiding the underlying lat,lon fields
    // need to render this before the bridging script from institution selector to map
    echo $this->element('locationpicker', array(
        'lonId' => '#CourseLon',
        'latId' => '#CourseLat'
    ));
	?>
	<p>
		Coordinates can be drawn in from the institution selector above. 
		If not applicable, adjust using the location picker. 
		Changing your selection from the institutions list above will 
		overwrite the current coordinate value.
	</p>
	
	<?php $this->Html->scriptStart(array('inline' => false)); ?>
	
	var selector = $('#CourseInstitutionId');
	var lon = $('#CourseLon');
	var lat = $('#CourseLat');
	var institution_id = null;
	var locations = <?php echo json_encode($locations); ?>

    function selectionToMap() {
        institution_id = selector.val();
        if(institution_id != '' && typeof(institution_id) != 'undefined') {
            lon.val(locations[institution_id].lon);
            lat.val(locations[institution_id].lat);
            setMarker(locationMap);
        }
    }

	selector.change(function() {
		selectionToMap();
	});

    jQuery(document).ready(function() {
        var lon = $('#CourseLon');
        var lat = $('#CourseLat');
        if(lat.val() == '' || lon.val() == '') {
            selectionToMap();
        }
    });

	<?php
	$this->Html->scriptEnd();

	echo $this->Form->input('lat', array('label' => 'Latitude'));
	echo $this->Form->input('lon', array('label' => 'Longitude'));
	?>
</fieldset>
<fieldset>
	<?php
	echo $this->element('taxonomy/selector', array('habtmModel' => 'Discipline', 'dropdown' => true, 'label' => 'Disciplines'));
	echo $this->element('taxonomy/selector', array('habtmModel' => 'TadirahTechnique', 'dropdown' => true));
	echo $this->element('taxonomy/selector', array('habtmModel' => 'TadirahObject', 'dropdown' => true));
	?>
</fieldset>

<fieldset>
    <p>
        If you leave this box unchecked, the course will not appear in the public listing,
        so you can complete editing later.
    </p>
    <?php
    echo $this->Form->input('active', array('label' => 'Publish'));
    ?>
</fieldset>

<?php
echo $this->Form->end('submit');
?>






