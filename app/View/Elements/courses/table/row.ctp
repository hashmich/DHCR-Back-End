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
	: 'onclick="toggleRow(event, \'record-details-' . $record['Course']['id'] . '\');" onmouseover="siblingHover(this, \'next\');" onmouseout="siblingHover(this, \'next\')"';

$outdated = null;
if($record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseYellow'))) {
	$outdated = ' yellow';
}
if(	$record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseRed'))
OR	(!empty($edit) AND $record['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
) {
	$outdated = ' red';
}
?>
<tr <?php echo $toggle; ?>
	class="<?php echo $classname; ?>"
	data-id="<?php echo $record['Course']['id']; ?>"
	>
	<?php
	if(!empty($edit)) {
		echo '<td class="actions">';
		
		echo $this->Html->link('share',
            Configure::read('dhcr.baseUrl').'courses/view/'.$record['Course']['id'], [
                'target' => '_blank',
                'class' => 'sharing button',
                'data-varname' => $varname,
                'data-id' => $record['Course']['id']
            ]);
		
		if($auth_user['user_role_id'] < 3 AND !$record['Course']['approved'])
            echo $this->Html->link('approve', '/courses/approve/'.$record['Course']['approval_token']);
		
        echo $this->Html->link('edit', array(
            'controller' => 'courses',
            'action' => 'edit',
            $record['Course']['id']
        ));
        if($auth_user['user_role_id'] < 3 OR $record['Course']['approved'])
            echo $this->Html->link('revalidate', array(
                    'controller' => 'courses',
                    'action' => 'revalidate',
                    $record['Course']['id']
            ));
		echo '</td>';
	}
	?>
	<td class="state<?php echo $outdated; ?>">
		<div class="ribbon">
			<span>last revised<br>
			<?php echo substr($record['Course']['updated'], 0, 10); ?></span>
		</div>
	</td>
	<?php
	$modelName = 'Course';
	foreach($fieldlist as $key => $fieldDef) {
		$expl = explode('.', $key);
		$fieldModelName = $modelName;
		$fieldname = $expl[0];
		if(isset($expl[1])) {
			$fieldModelName = $expl[0];
			$fieldname = $expl[1];
		}
		
		$value = (!empty($record[$fieldModelName][$fieldname])) ? $record[$fieldModelName][$fieldname] : ' - ';
		$classname = '';
		switch($key) {
			case 'Course.name':
				$classname = ' class="strong"';
				break;
			case 'CourseType.name':
				$value = $record['CourseParentType']['name'] . ': ' . $value;
				break;
			case 'Course.info_url':
				if($value != ' - ' AND !empty($value)) {
					$value = $this->Html->link('Info', $record[$fieldModelName][$fieldname], array(
						'target' => '_blank',
						'title' => 'external information link (new tab)'
					));
				}
				break;
			case 'Course.guide_url':
				if($value != ' - ' AND !empty($value)) {
					$value = $this->Html->link('Guide', $record[$fieldModelName][$fieldname], array(
						'target' => '_blank',
						'title' => 'external information link (new tab)'
					));
				}
		}
		echo '<td' . $classname . '>' . $value . '</td>';
	}
	?>
</tr>
