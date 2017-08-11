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
class SendRemindersTask extends Shell {
	
	public $uses = array('Course');
	
	
	public function execute($out = null, $to = null) {
		Configure::write('App.fullBaseUrl', Configure::read('App.consoleBaseUrl'));
		
		$collection = $this->Course->getReminderCollection();
		if(Configure::read('debug') > 0) $to = 'mail@hendrikschmeer.de';
		if(!empty($collection)) {
			if($out !== null) {
				$this->out('I found the following outdated records: ');
				print_r($collection);
			}
			if($out !== false) {
				App::uses('CakeEmail', 'Network/Email');
				
				foreach($collection as $email => $data) {
					if($email == 'no_owner') continue;
					
					$Email = new CakeEmail('default');
					$subject_prefix = (Configure::read('App.EmailSubjectPrefix'))
						? trim(Configure::read('App.EmailSubjectPrefix')) . ' '
						: '';
					
					if(!empty($to)) $email = $to;
					$options = array(
						'subject_prefix' => $subject_prefix,
						'subject' => 'Update Reminder',
						'emailFormat' => 'text',
						'template' => 'reminder',
						'layout' => 'default',
						'email' => $email,
						'data' => $data
					);
					if(is_string($options['email'])) {
						$Email->to($options['email']);
						$Email->emailFormat($options['emailFormat']);
						$Email->subject($options['subject_prefix'] . $options['subject']);
						$Email->template($options['template'], $options['layout']);
						$Email->viewVars(array(
							'data' => $options['data']
						));
						if(Configure::read('debug') > 0 AND empty($to)) {
							$Email->transport('Debug');
						}
						$Email->send();
					}
					unset($Email);
				} 
			}
		}
	}
	
	
}
?>