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

App::uses('UsersController', 'Users.Controller');

/**	Extending the plugin's UsersController.
*	This is not neccessary to override plugin views on app-level, but if you want to extend the plugin views. 
*	
*	The Users plugin is a reinforced/refactored version of the CakeDC Users plugin.
*	documentation: https://github.com/CakeDC/users/blob/master/Docs/Documentation/Extending-the-Plugin.md
*/

class AppUsersController extends UsersController {
    
	public $name = 'AppUsers';
	
	// if using the plugin's model:
	//public $modelClass = 'Users.User';
	//public $uses = array('Users.User');
	
	public $modelClass = 'AppUser';
	
	public $uses = array('AppUser');
	
	public $shibUser = array();
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		if($this->Auth->user('user_role_id') < 3) $this->Auth->allow(array('invite'));
		
		$shibLogin = !empty($_SERVER['HTTP_EPPN']);
		if($shibLogin) {
			// eppn: shib-'ID' (Matej said), givenName, surname, mail
			$shibVars = array(
				'HTTP_EPPN' => 'shib_eppn',
				'HTTP_GIVENNAME' => 'first_name',
				'HTTP_SN' => 'last_name',
				'HTTP_EMAIL' => 'email');
			foreach($_SERVER as $k => $v) {
				if(isset($shibVars[$k]) AND !empty($v) AND $v != '(null)') {
					$this->shibUser[$shibVars[$k]] = $v;
				}
			}
			if(empty($this->shibUser['shib_eppn']) OR $this->shibUser['shib_eppn'] == '(null)')
				$this->shibUser = array();
			$this->set('shibUser', $this->shibUser);
		}
		
		if(	$this->Auth->user()
		AND	empty($this->Auth->user('shib_eppn'))
		AND	$this->shibUser
		AND	(!$this->Session->read('Users.block_eppn') 
			|| $this->Session->read('Users.block_eppn') != $this->shibUser['shib_eppn'])
		) {
			// make connection, if not already set
			$this->{$this->modelClass}->recursive = 0;
			$this->{$this->modelClass}->id = $this->Auth->user('id');
			$this->{$this->modelClass}->saveField('shib_eppn', $this->shibUser['shib_eppn'], true);
			$user = $this->{$this->modelClass}->read()[$this->modelClass];
			$this->Auth->login();
			$this->set('auth_user', $user);
		}
		
		if($this->Auth->user()) {
			if($this->DefaultAuth->isAdmin()) {
				$this->Auth->allow(array('delete'));
			}
			$this->Auth->allow(array('delete_identity'));
		}
		
		$this->set('title_for_layout', 'User Management');
	}
	
	
	public function login() {
		if(!$this->request->is('post') AND !$this->Auth->user()) {
			
			if($this->shibUser) {
				// find the matching user and log in
				$user = $this->{$this->modelClass}->find('first', array(
					'contain' => array(),
					'conditions' => array(
						'or' => array(
							$this->modelClass . '.shib_eppn' => $this->shibUser['shib_eppn'],
							$this->modelClass . '.email' => $this->shibUser['shib_eppn']),
						$this->modelClass . '.active' => true
					)
				));
				if(!empty($user)) {
					if(!empty($user[$this->modelClass]))
						$user = $user[$this->modelClass];
					if($this->Auth->login($user)) {
						$this->Flash->set('You successfully logged in via external identity.');
						$this->redirect($this->Auth->loginRedirect);
					}
					// else: handle every other login errors in parent login method
				}else{
					// account has not yet been linked to the DHCR
					$this->Auth->flash('You have been successfully verified by your identity provider (IDP), 
							but your Courseregistry account has not been linked to that external service, yet. 
							Please login using your DH-Courseregistry account to connect.');
				}
			}
		}
		
		parent::login();
	}
	
	
	// when calling render from this controller, wee need to check for existing plugin views,
	// which only partially are overridden on app level
	public function render($view = null, $layout = null) {
		if(is_null($view)) {
			$view = $this->action;
		}
		$viewPath = substr(get_class($this), 0, strlen(get_class($this)) - 10);
		clearstatcache();
		if(!file_exists(APP . 'View' . DS . $viewPath . DS . $view . '.ctp')) {
			$this->viewPath = $this->plugin = 'Users';
		}else{
			$this->viewPath = $viewPath;
		}
		return parent::render($view, $layout);
	}
	
	
	
	protected function _setOptions() {
		$institutions = $this->AppUser->Institution->find('list', array(
			'contain' => array('Country'),
			'fields' => array('Institution.id', 'Institution.name', 'Country.name')
		));
		ksort($institutions);
		$countries = $this->AppUser->Country->find('list', array('order' => 'Country.name ASC'));
		$userRoles = $this->AppUser->UserRole->find('list');
		$cities = $this->AppUser->Institution->City->find('list', array(
			'contain' => array('Country'),
			'fields' => array('City.id', 'City.name', 'Country.name')
		));
		$this->set(compact('institutions','countries','userRoles','cities'));
	}
	
	
	public function register() {
		if(!empty($this->request->data[$this->modelClass]['university'])) {
			$this->request->data[$this->modelClass]['institution_id'] = null;
		}
		$this->{$this->modelClass}->validate = array_merge(
			$this->{$this->modelClass}->validate,
			array(
				'about' => array(
					'required' => array(
						'rule' => 'notBlank',
						'message' => 'For verification of your involvement, please provide any further information.'
					)
				)
			)
		);
		parent::register();
		$this->_setOptions();
	}
	
	
	public function delete_identity() {
		$eppn = $this->Auth->user('shib_eppn');
		$this->{$this->modelClass}->id = $this->Auth->user('id');
		$this->{$this->modelClass}->saveField('shib_eppn', null, false);
		$this->Auth->login($this->{$this->modelClass}->read()[$this->modelClass]);
		$this->Session->write('Users.block_eppn', $eppn);
		$this->redirect('/users/profile');
	}
	
	
	protected function _newUserAdminNotification($user = array()) {
		if(empty($user)) return false;
		$result = true;
		$admins = array();
		$mailOpts = array(
			'template' => 'Users.admin_new_user',
			'subject' => 'New Account Request',
			'data' => $user,
			'cc' => Configure::read('App.defaultCc')
		);
		
		// try fetching the moderator in charge of the user's country, 
		$country_id = (!empty($user[$this->modelClass]['country_id'])) 
			? $user[$this->modelClass]['country_id'] : null;
		// if country is not set, try retrieving it from the institution of the user
		if(empty($country_id) AND !empty($user[$this->modelClass]['institution_id'])) {
			$institution = $this->{$this->modelClass}->Institution->find('first', array(
				'contain' => array(),
				'conditions' => array(
					'Institution.id' => $user[$this->modelClass]['institution_id']
				)
			));
			if($institution AND !empty($institution['Institution']['country_id']))
				$country_id = $institution['Institution']['country_id'];
		}
		if(empty($country_id) AND !empty($user[$this->modelClass]['email'])) {
			$country_id = $this->AppUser->Country->getCountryFromEmail($user[$this->modelClass]['email']);
		}
		if(empty($country_id) AND !empty($user[$this->modelClass]['university'])) {
			$country_id = $this->AppUser->Country->getCountryFromText($user[$this->modelClass]['university']);
		}
		
		// find the moderators in charge
		if(!empty($country_id)) {
			$admins = $this->{$this->modelClass}->find('all', array(
				'contain' => array(),
				'conditions' => array(
					$this->modelClass.'.country_id' => $country_id,
					$this->modelClass.'.user_role_id' => 2,	// moderators
					$this->modelClass . '.active' => 1
				)
			));
		}
		
		// if no country or then escalate to user_admin
		if(empty($country_id) OR empty($admins)) {
			$admins = $this->{$this->modelClass}->find('all', array(
				'contain' => array(),
				'conditions' => array(
					$this->modelClass . '.user_admin' => 1,
					$this->modelClass . '.active' => 1
				)
			));
		}
		if($admins) {
			foreach($admins as $admin) {
				$mailOpts['email'] = $admin[$this->modelClass]['email']; 
				if(!$this->_sendUserManagementMail($mailOpts)) {
					$result = false;
				}
			}
		}
		
		return $result;
	}
	
	
	public function approve($id = null) {
		$success = $proceed = false;
		if( ($this->Auth->user() AND $this->Auth->user('user_role_id') < 3)
		AND !empty($id) AND ctype_digit($id)) {
			$user = $this->{$this->modelClass}->find('first', array(
				'contain' => array(),
				'conditions' => array(
					$this->modelClass . '.id' => $id,
					$this->modelClass . '.approved' => 0
				)
			));
			if($user) {
				$proceed = true;
			}
		}else{
			// not authenticated!
			// admins retrieve a link in their notification email to approve directly
			$user = $this->{$this->modelClass}->find('first', array(
				'contain' => array(),
				'conditions' => array(
					$this->modelClass . '.approval_token' => $id,
					$this->modelClass . '.approved' => 0
				)
			));
			if($user) {
				$proceed = true;
			}
		}
		
		if($proceed) {
			if(!empty($this->request->data[$this->modelClass])) {
				// the admin submitted additional data
				$user[$this->modelClass] = array_merge($user[$this->modelClass], $this->request->data[$this->modelClass]);
			}
			
			if($this->{$this->modelClass}->approve($user)) {
				$this->_sendUserManagementMail(array(
					'template' => 'Users.account_approved',
					'subject' => 'Account approved',
					'email' => $user[$this->modelClass]['email'],
					'data' => $user
				));
				$this->Flash->set('The account has been approved successfully.');
				$success = true;
			}else{
				$this->Flash->set('The user data did not pass validation. Please check the details.');
				if(empty($this->Auth->user())) {
					$this->Flash->set('Further user details need to be amended. Please log in first.');
				}
				if(!$this->Auth->user()) {
					$this->Auth->redirectUrl('/' . $this->request->url);
					$this->redirect('/users/login');
				}
			}
			
			if($success) {
				if($this->Auth->user()) $this->redirect(array(
					'plugin' => null,
					'controller' => 'users',
					'action' => 'dashboard'
				));
				$this->redirect('/');
			}
			
			$this->request->data = $user;
			$this->_setOptions();
		}else{
			$this->redirect('/');
		}
		// render the form...
	}
	
	
	public function profile($id = null) {
		$this->_setOptions();
		parent::profile($id);
	}
	
	
	public function dashboard($user_id = null) {
		$assist = true;
		if(empty($user_id) OR !$this->DefaultAuth->isAdmin()) {
			$user_id = $this->Auth->user('id');
			$assist = false;
		}
		$moderated = array();
		$courses = $this->AppUser->Course->find('all', array(
			'conditions' => array(
				'Course.user_id' => $user_id,
				'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseArchivalPeriod'))
			)
		));
		// moderators
		if($this->Auth->user('user_role_id') == 2 AND !empty($this->Auth->user('country_id'))) {
			$moderated = $this->AppUser->Course->find('all', array(
				'conditions' => array(
					'Course.country_id' => $this->Auth->user('country_id'),
					'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseArchivalPeriod'))
				),
				'order' => array(
						'Course.updated' => 'ASC'
				)
			));
		}
		$this->set(compact('courses', 'moderated'));
		
		if($this->DefaultAuth->isAdmin() OR $this->Auth->user('user_role_id') == 2) {
			$conditionsUnapproved = array($this->modelClass . '.approved' => 0);
			$conditionsInvited = array(
				'OR' => array(
					$this->modelClass . '.password IS NULL',
					$this->modelClass . '.password' => ''
				),
				$this->modelClass . '.active' => 1
			);
			if($this->Auth->user('user_role_id') == 2) {
				$conditionsUnapproved[$this->modelClass.'.country_id'] = $this->Auth->user('country_id');
			}
			if(!$assist) {
				// admin dashboard
				$unapproved = $this->AppUser->find('all', array(
					'contain' => array('Institution'),
					'conditions' => $conditionsUnapproved
				));
				
				$invited = $this->AppUser->find('all', array(
					'contain' => array('Institution' => array(
						'conditions' => array('Institution.country_id' => $this->Auth->user('country_id'))
					)),
					'conditions' => $conditionsInvited
				));
				foreach($invited as $k => $record) if(empty($record['Institution']['id'])) unset($invited[$k]);
				
				$this->set(compact('unapproved', 'invited'));
				$this->render('admin_dashboard');
				
			}else{
				$this->set('notice', 'You are viewing the dashboard of User '.$user_id);
				$this->render('user_dashboard');
			}
			
		}else{
			// user dashboard
			$this->render('user_dashboard');
		}
		
		
	}
	
	
	// technically, this is a admin-triggered password reset - thus the email template reads somewhat different
	public function invite($param = null) {
		$mailOpts = array(
			'template' => 'invite_user',
			'subject' => 'Join the Digital Humanities Course Registry',
			'bcc' => $this->Auth->user('email'),
			'cc' => Configure::read('App.defaultCc'),
			'sender' => Configure::read('App.defaultEmail'),
			'replyTo' => $this->Auth->user('email')
		);
		if(Configure::read('debug') > 0) $mailOpts['transport'] = 'Debug';
		
		if(!empty($param)) {
			if(ctype_digit($param)) {
				// invite individual user - $param == $id
				$user = $this->{$this->modelClass}->find('first', array(
					'contain' => array(),
					'conditions' => array(
						$this->modelClass . '.id' => $param,
						$this->modelClass . '.active' => 1
					)
				));
				if($user AND !empty($user[$this->modelClass]['email'])) {
					$mailOpts['email'] = $user[$this->modelClass]['email'];
					$mailOpts['data'] = $user;
					$this->_sendUserManagementMail($mailOpts);
					$this->Flash->set('User will receive a reminder email.');
				}
				
			}elseif($param === 'all') {
				$users = $this->{$this->modelClass}->find('all', array(
					'contain' => array(),
					'conditions' => array(
						$this->modelClass . '.active' => 1,
						$this->modelClass . '.password' => array(null, '')
					)
				));
				if(!empty($users)) {
					foreach($users as $user) {
						if($user AND !empty($user[$this->modelClass]['email'])) {
							$mailOpts['email'] = $user[$this->modelClass]['email'];
							$mailOpts['data'] = $user;
							$this->_sendUserManagementMail($mailOpts);
						}
					}
					$this->Flash->set('Users will receive a reminder email.');
				}
			}
			$this->redirect('/users/dashboard');
			
		}else{
			// add a new user
			if(!empty($this->request->data[$this->modelClass])) {
				if($user = $this->{$this->modelClass}->inviteRegister($this->request->data)) {
					if(!empty($user[$this->modelClass]['email'])) {
						$mailOpts['email'] = $user[$this->modelClass]['email'];
						$mailOpts['data'] = $user;
						$this->_sendUserManagementMail($mailOpts);
						$this->Flash->set('User successfully invited and emailed.');
					}
					$this->redirect('/users/dashboard');
				}
			}
			$this->_setOptions();
		}
	}
	
	
	
	
	
	
}
?>
