<?php

class TestTask extends Shell {
	
	public $uses = array('Course');
	
	
	public function execute() {
		Configure::write('App.fullBaseUrl', Configure::read('App.consoleBaseUrl'));
		
		$collection = $this->Course->checkUrls();
		if(!empty($collection)) {
			
			if(1) {
				App::uses('CakeEmail', 'Network/Email');
	
				foreach($collection as $email => $data) {
					if($email == 'no_owner') continue;
						
					$Email = new CakeEmail('default');
					$subject_prefix = (Configure::read('App.EmailSubjectPrefix'))
					? trim(Configure::read('App.EmailSubjectPrefix')) . ' '
							: '';
								
					$options = array(
							'subject_prefix' => $subject_prefix,
							'subject' => 'Invalid URLs',
							'emailFormat' => 'text',
							'template' => 'invalid_urls',
							'layout' => 'default',
							'email' => $email,
							'data' => $data
					);
					if(is_string($options['email'])) {
						$Email->to(Configure::read('debugging.mail'));
						$Email->emailFormat($options['emailFormat']);
						$Email->subject($options['subject_prefix'] . $options['subject']);
						$Email->template($options['template'], $options['layout']);
						$Email->viewVars(array(
								'data' => $options['data']
						));
						
						$Email->send();
					}
					unset($Email);
				}
			}
		}
	}
	
	/*
	public function execute($to = null) {
		$this->out("Executing TestTask...");
		
		if($to == null) $to = Configure::read('debugging.mail');
		App::uses('CakeEmail', 'Network/Email');
		$Email = new CakeEmail('default');
		$subject_prefix = (Configure::read('App.EmailSubjectPrefix'))
			? trim(Configure::read('App.EmailSubjectPrefix')) . ' '
			: '';
		$options = array(
			'subject_prefix' => $subject_prefix,
			'subject' => 'Cron Testmail',
			'email' => $to
		);
		if(is_string($options['email'])) {
			$Email->to($options['email']);
			$Email->emailFormat('text');
			$Email->subject($options['subject_prefix'] . $options['subject']);
			$Email->send('If you recieve this mail, the CronShell successfully executed task "Test".');
		}
	}
	*/
	
}
?>