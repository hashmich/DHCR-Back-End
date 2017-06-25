<?php

class TestTask extends Shell {
	
	public $uses = array();
	
	
	public function execute($to = null) {
		$this->out("Executing TestTask...");
		
		if($to == null) $to = 'mail@hendrikschmeer.de';
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
	
}
?>