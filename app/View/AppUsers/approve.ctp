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
<div class="users_form">
	<h2>Approve User</h2>
	
	<?php 
	if(empty($auth_user)) {
		echo '<p>You need to log in to add missing institutions or promote the new user to advanced roles.</p>';
	}
	?>

	<?php
	echo $this->element('Utils.validation_errors');
	
	echo $this->Form->create($modelName);
	
	echo '<fieldset>';
	echo $this->Form->input('id', array('disabled' => true, 'type' => 'text'));
	echo $this->Form->input('email', array('disabled' => true, 'required' => false));
	echo $this->Form->input('academic_title');
	echo $this->Form->input('first_name');
	echo $this->Form->input('last_name');
	echo '</fieldset>';
	
	echo '<fieldset>';
	$modSettingOptions = array('disabled' => true, 'type' => 'text');
	if(!empty($auth_user['is_admin'])) {
		echo $this->Form->input('is_admin');
		echo '<p>As a moderator, the user must be assigned to a country from the list.</p>';
		echo $this->Form->input('user_role_id');
		echo '<p>As UserAdmin, the user will recieve emails that are not being catched by the national moderators.</p>';
		echo $this->Form->input('user_admin');	// get the emails not catched by the national mods
		
		$modSettingOptions = array();
	}
	echo '</fieldset>';
	
	echo '<fieldset>';
	if(!empty($this->data[$modelName]['university'])) {
	    echo '<p class="strong">The user could not find this institution on the list: you have to add it:</p>';
	    echo $this->Form->input('university', array(
			'label' => 'New Institution',
			'type' => 'text',
			'disabled' => true
		));
	}
	echo '<p class="strong">The following categories possibly have to be extended in this order:</p>';
	echo '<p>1. If the country doesn\'t exist, please go to "'
			.$this->Html->link('Add Country', '/db-webclient/countries/add').'".</p>';
	echo $this->Form->input('country_id', array(
		'required' => 'required',
		'empty' => '-- choose country --',
		'div' => array('class' => 'input select required')
	));
	echo '<p>2. If the city doesn\'t exist, please go to "'
			.$this->Html->link('Add City', '/db-webclient/cities/add').'".</p>';
	echo $this->Form->input('city_id', array(
		'required' => 'required',
		'empty' => '-- choose city --',
		'div' => array('class' => 'input select required')
	));
	echo '<p>3. If the institution doesn\'t exist, please go to "'
			.$this->Html->link('Add Institution', '/db-webclient/institutions/add').'".</p>';
	echo $this->Form->input('institution_id', array(
		'required' => 'required',
		'empty' => '-- choose institution --',
		'div' => array('class' => 'input select required')
	));
	echo '</fieldset>';
	
	
	
	echo '<fieldset>';
	echo $this->Form->input('about', array(
		'type' => 'textarea',
		'label' => 'About',
		'required' => false
	));
	echo '</fieldset>';
	echo $this->Form->end('Submit');
	?>
	
</div>
