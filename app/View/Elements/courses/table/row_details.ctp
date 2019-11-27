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
	$stateTitle = 'entry not revised for ' . round(Configure::read('App.CourseYellow')/(60*60*24*365), 1) . ' years';
}
if(	$record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseRed'))
OR	(!empty($edit) AND $record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
) {
	$state = 'Red';
	$stateTitle = 'entry not revised for more than ' . round(Configure::read('App.CourseRed')/(60*60*24*365), 1) . ' years';
}

if($state !== 'Green' AND !empty($edit)) {
	$remaining = strtotime($record['Course']['updated']) + Configure::read('App.CourseExpirationPeriod') - time();
	$stateTitle = '<span style="color:red">Entry will disappear in ' . round($remaining/(60*60*24*7), 0) . ' weeks.<br>Please update!</span>';
}
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
					<dt>Status <?php echo $state; ?></dt>
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
							}
							if(!empty($value) AND $value[0] != ' - ') $value[] = 'recurring';
						}
						$value = implode('<br />', $value);
						echo $value;
						?>
					</dd>
                    <dt>Duration</dt>
                    <dd>
                        <?php
						if(!empty($record['Course']['duration']) AND !empty($record['CourseDurationUnit']['name']))
                            echo $record['Course']['duration'] . ' ' . $record['CourseDurationUnit']['name'];
						else echo ' - ';
                        ?>
                    </dd>
                    <dt>Recurring</dt>
                    <dd>
                        <?php
						if(!empty($record['Course']['recurring']))
							echo 'yes';
						else echo 'no';
                        ?>
                    </dd>
                    <dt>Online</dt>
                    <dd>
						<?php
						if(!empty($record['Course']['online_course']))
							echo 'yes';
						else echo 'no';
						?>
                    </dd>
					<dt>ECTS</dt>
					<dd>
                        <?php echo (!empty($record['Course']['ects'])) ? $record['Course']['ects'] : ' - '; ?>
                    </dd>
					
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
					
					<dt>Link to Detail Page</dt>
					<dd>
						<?= $this->Html->link(
						    Configure::read('dhcr.baseUrl').'courses/view/'.$record['Course']['id'],
                            ['target' => '_blank']) ?>
					</dd>
					
					<?php
					if(!empty($edit) AND $auth_user['is_admin']) {
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
					
					<dt>Description</dt>
					<dd><?php echo (!empty($record['Course']['description'])) ? $record['Course']['description'] : ' - '; ?></dd>
					
					
					<?php
					$keywords = array();
					if(!empty($record['Discipline'])) {
						$cat = array();
						foreach($record['Discipline'] as $tag) $cat[] = trim($tag['name']);
						$keywords['Disciplines'] = $cat;
					}
					if(!empty($record['TadirahTechnique'])) {
						$cat = array();
						foreach($record['TadirahTechnique'] as $tag) $cat[] = trim($tag['name']);
						$keywords['Techniques'] = $cat;
					}
					if(!empty($record['TadirahObject'])) {
						$cat = array();
						foreach($record['TadirahObject'] as $tag) $cat[] = trim($tag['name']);
						$keywords['Objects'] = $cat;
					}
					if(!empty($keywords)) {
						?>
						<dt>Keywords</dt>
						<dd>
							<?php
							$kwlist = array();
							foreach($keywords as $cat => $entries)
								$kwlist[] = '<u>'.$cat.'</u>: ' . implode(', ', $entries);
							echo implode('<br />', $kwlist);
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
