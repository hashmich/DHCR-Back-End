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
		if(Configure::read('debug') > 0) $to = Configure::read('debugging.mail');
		$this->out('Debug level: ' . Configure::read('debug'));
		$this->out('Alternative addressee (debug): ' . $to);
		if(!empty($collection)) {

			if($out !== false) {
				App::uses('CakeEmail', 'Network/Email');

				$this->out('Sending emails to:');
				foreach($collection as $email => $data) {
					if($email == 'no_owner') continue;

					$this->out($email . ': ' . $data['maintainer']);

					$ccMod = false;
					$country_id = null;
					foreach($data as $id => $record) {
						if($id == 'maintainer') continue;

						// set the last reminder timestamp only, if it is the first mailing after last update
						if(	$record['Course']['last_reminder'] < $record['Course']['updated']) {
							$save = array(
								'id' => $id,
								'last_reminder' => date('Y-m-d H:i:s'),
								'modified' => false
							);
							$this->Course->save($save, array('validate' => false));
						}

						// cc moderator only, if there were subsequent reminders after last update
						if(	$record['Course']['last_reminder'] > $record['Course']['updated']
						AND	$record['Course']['last_reminder'] < date('Y-m-d H:i:s', time() - 60*60*24*60)) {
							$ccMod = true;
							$country_id = $record['Course']['country_id'];
						}
					}

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
						if($ccMod AND !Configure::read('debug')) {
							$mods = $this->Course->AppUser->getModerators($country_id);
							if($mods)
								$Email->cc($mods[0]['AppUser']['email']);
						}
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
