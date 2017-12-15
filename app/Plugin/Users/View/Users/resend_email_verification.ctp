<div class="auth">
	<h2>Resend Email Verification</h2>
	<?php
	echo $this->Session->flash();
	
	echo $this->Form->create($modelName);
	echo $this->Form->input('email', array('required' => false, 'autocomplete' => 'off'));
	echo $this->Form->end('Submit');
	?>
</div>