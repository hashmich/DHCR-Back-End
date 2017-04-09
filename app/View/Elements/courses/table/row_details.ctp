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
$toggle = ($showDetails) ? ''
	: 'style="display:none" onclick="toggleRow(event, \'record-details-' . $record['Course']['id'] . '\');" onmouseover="siblingHover(this, \'prev\');" onmouseout="siblingHover(this, \'prev\')"';

$state = 'Green';
$stateTitle = 'entry actively maintained';
if($record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseYellow'))) {
	$state = 'Yellow';
	$stateTitle = 'entry not revised since one year';
}
if(	$record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseRed'))
OR	(!empty($edit) AND $record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
) {
	$state = 'Red';
	$stateTitle = 'entry not revised since > 1,5 year';
}

if($state !== 'Green' AND !empty($edit))
	$stateTitle = '<span style="color:red">Entry will disappear after 2 years.<br>Please update!</span>';
?>

<tr <?php echo $toggle; ?>
	id="record-details-<?php echo $record['Course']['id']; ?>" 
	class="<?php echo $classname; ?>"
	data-id="<?php echo $record['Course']['id']; ?>"
	>
	<td colspan="<?php echo $colspan; ?>">
		<p class="strong">Details</p>
		<div class="record_details">
			<div class="left narrow">
				<dl>
					<dt>State <?php echo $state; ?></dt>
					<dd><?php echo $stateTitle; ?></dd>
					<dt>Language</dt>
					<dd><?php echo (!empty($record['Language']['name'])) ? $record['Language']['name'] : ' - '; ?></dd>
					<dt>Start Date</dt>
					<dd>
						<?php
						$value = (!empty($record['Course']['start_date'])) ? $record['Course']['start_date'] : ' - ';
						$value = explode(';', $value);
						if($record['Course']['recurring']) {
							foreach($value as &$date) {
								$date = trim($date);
								// check if it's a valid date
								if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
									$date = substr($date, 5);
								}
							}
							if(!empty($value) AND $value[0] != ' - ') $value[] = 'recurring';
						}
						$value = implode('<br />', $value);
						echo $value;
						?>
					</dd>
					<dt>ECTS</dt>
					<dd><?php echo (!empty($record['Course']['ects'])) ? $record['Course']['ects'] : ' - '; ?></dd>
					
					<dt>Lecturer</dt>
					<dd>
						<?php
						$lecturer = $name = $mail = null;
						if(!empty($record['Course']['contact_mail'])) $mail = $name = $record['Course']['contact_mail'];
						if(!empty($record['Course']['contact_name'])) $name = $record['Course']['contact_name'];
						if(!empty($name) AND !empty($mail))
							$lecturer = $this->Html->link($record['Course']['contact_name'], 'mailto:' . $record['Course']['contact_mail']);
						if(empty($mail) AND !empty($name)) $lecturer = $name;
						echo (!empty($lecturer)) ? $lecturer : ' - ';
						?>
					</dd>
					
					<dt>Permalink</dt>
					<dd>
						<?php
						$url = array(
							'controller' => 'courses',
							'action' => 'view',
							$record['Course']['id']);
						echo $this->Html->link(Router::url($url, true), $url);
						?>
					</dd>
					
					<?php
					if(!empty($edit)) {
						?>
						<dt>Maintainer</dt>
						<dd>
							<?php
							if(empty($record['Course']['user_id'])) {
								echo 'No maintainer assigned';
							}else{
								echo $record['AppUser']['first_name'].' '.$record['AppUser']['last_name'].'<br>';
								echo $this->Html->link($record['AppUser']['email'], 'mailto:'.$record['AppUser']['email'], array(
									'target' => '_blank'));
									echo '<br>';
								echo $this->Html->link('View maintainer data', '/moderator/users/view/'.$record['Course']['user_id']);
							}
							?>
						</dd>
						<?php
					}
					?>
				</dl>
			</div>
			<div class="left wide">
				<dl>
					<dt>Access Requirements</dt>
					<dd><?php echo (!empty($record['Course']['access_requirements'])) ? $record['Course']['access_requirements'] : ' - '; ?></dd>
					<?php
					$keywords = array();
					if(!empty($record['Discipline'])) {
						foreach($record['Discipline'] as $tag) $cat[] = trim($tag['name']);
						$keywords['Disciplines'] = $cat;
					}
					if(!empty($record['TadirahTechnique'])) {
						foreach($record['TadirahTechnique'] as $tag) $cat[] = trim($tag['name']);
						$keywords['Techniques'] = $cat;
					}
					if(!empty($record['TadirahObject'])) {
						foreach($record['TadirahObject'] as $tag) $cat[] = trim($tag['name']);
						$keywords['Objects'] = $cat;
					}
					if(!empty($keywords)) {
						?>
						<dt>Keywords</dt>
						<dd>
							<?php
							foreach($keywords as $cat => &$entries)
								$entries = $cat . ': ' . implode(', ', $entries);
							echo implode('<br />', $keywords);
							?>
						</dd>
						<?php
					}
					?>
				</dl>
			</div>
		</div>
	</td>
</tr>
