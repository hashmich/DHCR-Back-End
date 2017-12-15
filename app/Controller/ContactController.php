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

class ContactController extends AppController {
	
	
	public $uses = array(
		'Country',
		'AppUser'
	);
	
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Security->unlockedFields[] = 'g-recaptcha-response';
		$this->Auth->allow(array('us'));
		$this->set('title_for_layout', 'Contact');
	}
	
	public function us() {
		if(!empty($this->request->data['Contact'])) {
			if(!$this->_checkCaptcha()) {
				$this->Flash->set('You did not succeed the CAPTCHA test. Please make sure you are human and try again.');
				$this->redirect('/');
			}
			
			$data = $this->request->data['Contact'];
			
			// try fetching the moderator in charge of the user's country
			$country_id = (empty($data['country_id'])) ? null : $data['country_id'];
			if($country_id == null) {
				$country_id = $this->AppUser->Country->getCountryFromEmail($data['email']);
			}
			$admins = $this->AppUser->getModerators($data['country_id'], $user_admin = true);
			
			if($admins) {
				App::uses('CakeEmail', 'Network/Email');
				foreach($admins as $admin) {
					// email logic
					$Email = new CakeEmail();
					$Email->replyTo($this->request->data['Contact']['email'])
					->sender($this->request->data['Contact']['email'], trim(
							$this->request->data['Contact']['first_name'].' '
							.$this->request->data['Contact']['last_name']))
					->to($admin['AppUser']['email'])
					->cc(Configure::read('App.defaultCc'))
					->subject('[DH-Registry Contact-Form] New Question')
					->send($this->request->data['Contact']['message']);
					$Email->addCc($this->request->data['Contact']['email']);
				}
				$this->Flash->set('Your message has been sent.');
				$this->redirect('/');
			}else{
				$this->Flahs->set('Error: No Admin could be found!');
			}
		}
		
		$moderators = $this->AppUser->find('all', array(
			'contain' => array('Country'),
			'conditions' => array('AppUser.user_role_id' => 2),
			'order' => array('Country.name' => 'asc')
		));
		$userAdmins = $this->AppUser->find('all', array(
				'contain' => array(),
				'conditions' => array('AppUser.user_admin' => 1)
		));
		$country_ids = array();
		if($moderators) foreach($moderators as $mod) {
			if(!empty($mod['AppUser']['country_id']))
				$country_ids[] = $mod['AppUser']['country_id'];
		}
		$countries = $this->Country->find('list', array(
			'order' => 'Country.name ASC',
			'conditions' => array('Country.id' => $country_ids)
		));
		$this->set(compact('countries', 'moderators', 'userAdmins'));
	}
}
?>











