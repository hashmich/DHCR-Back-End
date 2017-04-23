
<div class="filter">
<h3>Filter</h3>
<?php
if(!empty($crudFieldlist)) {
	
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
	
	echo $this->Form->submit();
}

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