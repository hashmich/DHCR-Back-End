<div class="auth">
	<h2>Login</h2>
	
	<?php
	echo $this->Session->flash('auth');
	
	echo $this->Form->create($modelName);
	echo $this->Form->input(Configure::read('Users.loginName'), array('required' => false, 'autocomplete' => 'off'));
	echo $this->Form->input('password', array('required' => false, 'autocomplete' => 'off'));
	echo $this->Form->end('Log on');
	?>
	
	
	<ul>
		<li>
			<?php
			if($this->Session->check('Users.verification') OR !empty($usersVerification)) {
				echo $this->Html->link('Resend Email Verification', array(
					'action' => 'request_email_verification',
					'controller' => 'users'
				));
			}else{
				echo $this->Html->link('I forgot my password', array(
					'action' => 'request_new_password',
					'controller' => 'users'
				));
			}
			?>
		</li>
		
		<?php
		if(empty($shibUser)) {
			$url = urlencode(Router::url('/users/login', $full = true));
			?>
			<li>
                <a href="<?php echo Configure::read('shib.idpSelect') . $url; ?>"
				title="You will be redirected to an external service">
					Login via Single Sign-On
				</a>	
			</li>
			<?php
		}
		?>
		
		<?php
		if(is_null(Configure::read('Users.allowRegistration')) OR Configure::read('Users.allowRegistration')) {
			?>
			<li>
				<?php
				echo $this->Html->link('Register', array(
					'controller' => 'users',
					'action' => 'register'
				));
				?>
			</li>
			<?php
		}
		?>
	</ul>
</div>