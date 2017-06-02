<?php
if(!empty($crudFieldlist)) {
	$this->append('script_bottom');
	?>
	$(document).ready(function() {
		$('#cakeclient-filter > .toggle').on('click', function() {
			if($(this).hasClass('off')) {
				$('#cakeclient-filterpane').show();
			}else{
				$('#cakeclient-filterpane').hide();
			}
			$(this).toggleClass('off');
			$('#cakeclient-filter > .toggle > span').toggleClass('glyphicon-menu-down');
			$('#cakeclient-filter > .toggle > span').toggleClass('glyphicon-menu-up');
		});
	});
	<?php
	$this->end();
	?>
	<div id="cakeclient-filter">
		<h3 class="toggle off"><span class="glyphicon glyphicon-menu-down"></span> Filter</h3>
		<div id="cakeclient-filterpane" style="display:none">
			<?php
			echo $this->Form->create('Filter', array('novalidate' => 'novalidate'));
			
			$options = array();
			foreach($crudFieldlist as $k => $field) {
				$options[$field['fieldname']] = $field['label'];
			}
			
			echo $this->Form->input('field', array(
					'label' => 'Field',
					'options' => $options,
					'type' => 'select',
					'empty' => '-- filter by field --'
			));
			
			$_options = array(
					'is','is not',
					'starts with','ends with','contains',
					'greater than','less than'
			);
			$options = array();
			foreach($_options as $v) $options[$v] = $v;
			
			echo $this->Form->input('operator', array('options' => $options, 'default' => 'is'));
			
			echo $this->Form->input('value');
			
			echo $this->Form->end('apply');
			
			
			
			if(!empty($filter)) {
				echo '<p>Active Filters: (' . $this->Html->link('reset', 'index') . ')</p>';
				echo '<ul>';
				foreach($filter as $fieldname => $value) {
					$fieldname = explode('.', $fieldname);
					if(!empty($fieldname[1])) $fieldname = $fieldname[1];
					echo '<li><span>' . $fieldname . ' </span>' . $value . '</li>';
				}
				echo '</ul>';
			}
			
			?>
		</div>
	</div>
	<?php
}	
?>
