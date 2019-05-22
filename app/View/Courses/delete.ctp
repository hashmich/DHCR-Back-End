

<h2>Delete Course</h2>

<p class="actions">
	<?php
	echo $this->Html->link('Back', '/courses/edit/' . $this->request->data['Course']['id']);
	?>
</p>


<?php

echo $this->element('Utils.validation_errors');

echo $this->Form->create('Course', array('novalidate' => 'novalidate'));

?>


<fieldset>
	<?php
	echo $this->Form->input('id', array('disabled' => true, 'type' => 'text'));
	echo $this->Form->input('name', array('disabled' => true, 'type' => 'text'));
	echo $this->Form->input('deletion_reason_id', array(
		'empty' => ' -- none -- '
	));
	?>
</fieldset>


<?php
echo $this->Form->end('delete');
?>