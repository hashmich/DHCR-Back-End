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

if(!function_exists('getOpts')) {
	function getOpts($modelName, $habtmModel, $optionEntry, $formData, $noHabtm = false) {
		$crossModel = Inflector::pluralize($modelName).$habtmModel;
		$label = array();
		if(!empty($optionEntry[$habtmModel]['description']))
			$label['title'] = $optionEntry[$habtmModel]['description'];
		if(!empty($optionEntry[$habtmModel]['name']))
			$label['text'] = $optionEntry[$habtmModel]['name'];
		$opts = array(
			'empty' => false,
			'required' => false,
			'onchange' => false,
			'type' => 'checkbox',
			'value' => $optionEntry[$habtmModel]['id'],
			'label' => $label,
			'name' => "data[$habtmModel][$habtmModel][]",
			'div' => array('class' => 'checkbox'),
			'hiddenField' => false,
			//'datapath' => "$HabtmModel.$CrossTableModel.$habtm_model_id",
			// the js code is aware of the crossTable by the datarelation and starts iteration on the existing records
			'datapath' => $habtmModel.'.'.$crossModel.'.'.Inflector::underscore($habtmModel).'_id',
			'datarelation' => $modelName.'.habtm.'.$habtmModel
		);
		
		// short data path for usage in filter form
		if($noHabtm) $opts['name'] = "data[$habtmModel][]";
		
		
		// the request data array changes format after form submission!
		if(!empty($formData[$habtmModel][$habtmModel])) {
			// after submission with validation errors
		    if(in_array($optionEntry[$habtmModel]['id'], $formData[$habtmModel][$habtmModel])) {
				$opts['checked'] = true;
			}
		}elseif(!empty($formData[$habtmModel])) {
			// first call, array format as is from database
		    foreach($formData[$habtmModel] as $selection) {
				if(!empty($selection['id']) AND $optionEntry[$habtmModel]['id'] == $selection['id']) {
					$opts['checked'] = true;
					break;
				}
			}
		}
		return $opts;
	}
}

$classes = (!empty($errors) AND !empty($habtmModel) AND !empty($errors[$habtmModel])) ? ' error' : '';
$classes .= (!empty($dropdown)) ? ' dropdown_checklist' : '';
?>

<div class="input taxonomy select required<?php echo $classes; ?>">
	<label for="<?php echo $habtmModel . $habtmModel; ?>">
		<?php
		if(empty($label)) 
		echo Inflector::humanize(Inflector::underscore(Inflector::pluralize($habtmModel)));
		else echo $label;
		?>
	</label>
	<div class="wrapper">
		<?php
		if(!empty($dropdown)) {
			?>
			<div id="<?php echo $habtmModel . '_toggle'; ?>" class="checklist_toggle">
				<span class="display">-- none selected --</span>
				<span class="caret"> </span>
			</div>
			<?php
		}
		?>
		<div id="<?php echo $habtmModel . '_checklist'; ?>"
			class="checklist"
			<?php if(!empty($dropdown)) echo ' style="display:none;"'; ?>>
			<input id="<?php echo $habtmModel . $habtmModel; ?>"
				type="hidden" value=""
				name="data[<?php echo $habtmModel . '][' . $habtmModel; ?>]">
			
			<?php
			if(!empty($buttons)) {
				echo $this->Form->button('Deselect all', array(
					'onclick' => "deselectList('#".$habtmModel."_checklist');",
					'type' => 'button',
					'style' => 'margin-bottom:8px;'
				));
			}
			
			// iterate the options of $habtmModel
			$varname = Inflector::variable(Inflector::pluralize($habtmModel));
			foreach($$varname as $entry) {
				$opts = getOpts($modelName, $habtmModel, $entry, $this->request->data, !empty($noHabtm));
				echo $this->Form->input($habtmModel . '.' . $habtmModel . $entry[$habtmModel]['id'], $opts);
			}
			?>
		</div>
	</div>
</div>

<?php
if(!empty($dropdown) AND empty($dropdownScript)) {
	$this->set('dropdownScript', true);
	$this->Html->scriptStart(array('inline' => false));
	?>
	if(!dropdownScript) {
		var dropdownScript = 1;
		
		jQuery(document).ready(function() {
			var toggle = $('.checklist_toggle');
			var checklist = $('.checklist');
			toggle.each(function() {
				$(this).on('click', function() {
					$(this).next('.checklist').toggle();
				});
			});
			
			toggle.each(function(index) {
				var checklist = $(this).next('.checklist');
				dc_writeDisplay(this, checklist);
				var currentToggle = this;
				
				// rewrite the display on-change
				var inputlist = checklist.find('input[type=checkbox]');
				inputlist.each(function(key) {
					$(this).on('change', function() {
						<?php
						if(!empty($autosubmit)) echo '$(this).closest("form").submit();';
						else echo 'dc_writeDisplay(currentToggle, checklist);';
						?>
					});
				});
			});
		});
		
		// dc - namespace for dropdown-checklist
		function dc_writeDisplay(toggle, checklist) {
			var selected = checklist.find('input[type=checkbox]:checked');
			var values = [];
			selected.each(function(key) {
				values.push($(this).next('label').text());
			});
			var display = values.join(', ');
			if(!display) display = '-- none selected --';
			$(toggle).find('.display').text(display);
		}
		
		function closeList(selector) {
			$(selector).toggle();
		}
		
		function deselectList(selector) {
			$(selector + ' :checkbox').prop('checked', false);
			<?php
			if(!empty($autosubmit)) echo '$(selector + " :checkbox").closest("form").submit();';
			else echo 'dc_writeDisplay($(selector).prev(".checklist_toggle"), $(selector));';
			?>
		}
	}
	<?php
	$this->Html->scriptEnd();
}
?>


