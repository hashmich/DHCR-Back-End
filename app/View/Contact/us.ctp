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

<h2>Contact</h2>
<p>Contact us if you have questions or problems.</p>
<p>
	Found a bug or error?<br>
	Please file a bug report
	<?php echo $this->Html->link('here', 'https://github.com/hashmich/DH-Registry/issues'); ?>.
</p>
<p>
	Please use our contact form to automatically address 
	your responsible moderator or admin. 
</p>



<div style="margin-bottom: 2em;" class="clearfix">
	<h3>Board of Moderators</h3>
	
	<div class="left half">
		<h4>National Coordinators</h4>
		<dl>
			<?php
			$last_country = null;
			foreach($moderators as $i => $mod) {
				if(empty($mod['AppUser']['country_id'])) continue;
				if($mod['AppUser']['country_id'] == $last_country) {
					echo ', ';
				}else{
					if($i > 0) echo '</dd>'; 
					echo '<dt>' . $mod['Country']['name'] . '</dt>';
					echo '<dd>';
				}
				$last_country = $mod['AppUser']['country_id'];
				
					echo $this->Html->link(
							$mod['AppUser']['first_name'] . ' ' . $mod['AppUser']['last_name'],
							'mailto:' . $mod['AppUser']['email']);
				
			}
			echo '</dd>';
			?>
		</dl>
	</div>
	
	
	<div class="left half">
		<h4>User Administrators</h4>
		<p>Responsible for general user inquiries and not yet moderated countries.</p>
		<p>
			<?php
			foreach($userAdmins as $i => $mod) {
				echo $this->Html->link(
						$mod['AppUser']['first_name'] . ' ' . $mod['AppUser']['last_name'],
						'mailto:' . $mod['AppUser']['email']);
				echo '<br>';
			}
			?>
		</p>
	</div>
	
</div>

<br>


<div class="users_form">
	<h3>Contact Form</h3>
	
	<?php
	echo $this->Form->create('Contact', array('novalidate' => true));
	
	echo $this->Form->input('email', array(
		'label' => 'E-mail',
		'autocomplete' => 'off'
	));
	
	echo '<p>If available, please choose a country to assign your message to a national moderator.</p>';
	echo $this->Form->input('country_id', array(
		'empty' => '-- choose country --'
	));
	
	echo $this->Form->input('first_name');
	
	echo $this->Form->input('last_name');
	
	echo $this->Form->input('telephone', array(
		'type' => 'text'
	));
	
	echo $this->Form->input('message', array(
		'type' => 'textarea',
	));
	
	echo $this->Form->end('Submit');
	?>
	
	
	
</div>
