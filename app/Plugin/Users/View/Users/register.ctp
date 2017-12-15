<div class="auth">
	<h2>User Registration</h2>
	
	<ul>
		<li>
			<?php
			echo $this->Html->link('Resend verification mail', array(
					'action' => 'resend_email_verification',
					'controller' => 'users'
				), array(
					'title' => 'If you already registered, but have not verified your email address, please click here.'));
			?>
		</li>
	</ul>
	
	<?php
	echo $this->Form->create($modelName);
	
	if(Configure::read('Users.username')) {
		echo $this->Form->input('username', array(
			'label' => 'Username',
			'autocomplete' => 'off'
		));
	}
	echo $this->Form->input('email', array(
		'label' => 'E-mail',
		'autocomplete' => 'off'
	));
	
	echo $this->Form->input('password', array(
		'label' => 'Password',
		'type' => 'password',
		'autocomplete' => 'off'
	));
	
	echo $this->Form->end('Submit');
	?>
	
</div>