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
App::uses('User', 'Users.Model');

class AppUser extends User {
    
	public $name = 'AppUser';
	
    public $useTable = 'users';

    private $__isShibUser = false;

    public function isShibUser() {
    	return $this->__isShibUser;
	}
	
	// a set of validation rules, extending or overriding the given rules from the plugin
	public $validationRules = array(
		'institution_id' => array(
			'special' => array(
				'rule' => 'checkUniversity',
				'message' => 'Please either choose your university from this list or enter the name in the next field if it\'s not available.'
			)
		),
		'last_name' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please enter your last name.'
			)
		),
		'first_name' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please enter your first name.'
			)
		)
	);



	public static $shib_mapping = array(
		'HTTP_EPPN' => 'shib_eppn',
		'HTTP_GIVENNAME' => 'first_name',
		'HTTP_SN' => 'last_name',
		'HTTP_EMAIL' => 'email'
	);


	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array_merge($this->validate, $this->validationRules);

		// try retrieving the server variables previously set from the Shibboleth login system
		if(!empty($_SERVER['HTTP_EPPN'])) {
			foreach($_SERVER as $k => $v) {
				if(isset(self::$shib_mapping[$k]) AND !empty($v) AND $v != '(null)') {
					$this->data[self::$shib_mapping[$k]] = $v;
				}
			}
			// shibboleth might return strange empty-values...
			if(empty($this->data['shib_eppn']) OR $this->data['shib_eppn'] == '(null)') {
				$this->data = array();
			}else{
				$this->__isShibUser = true;
			}
		}
	}
	
				
	public function beforeSave($options = array()) {
		// quietly remove the eppn value, if already connected to a different account
		if(!empty($this->data[$this->alias]['shib_eppn'])) {
			$temp = $this->find('first', array(
				'contain' => array(),
				'conditions' => array(
					'shib_eppn' => $this->data[$this->alias]['shib_eppn'])));
			if($temp) {
				unset($this->data[$this->alias]['shib_eppn']);
			}
		}else{
			$this->data[$this->alias]['shib_eppn'] = null;
		}
		return true;
	}
	
	
	public function afterSave(boolean $created, $options = array()) {
		//check the email subscription
		if(isset($this->request->data[$this->modelClass]['mail_list'])) {
			$subsrcibe = (boolean)$this->request->data[$this->modelClass]['mail_list'];
			$this->handleUserDHMailList($this->request->data['AppUser']['email'], $subsrcibe);
		}
	}
	
	
	public $virtualFields = array(
		'name' => 'TRIM(CONCAT(AppUser.academic_title, " ", AppUser.first_name, " ", AppUser.last_name))'
	);
	
	
	
	public $belongsTo = array(
		'Institution' => array(
			'className' => 'Institution',
			'foreignKey' => 'institution_id'
		),
		'UserRole',
		'Country'
	);
	
	public $hasMany = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'user_id',
			'dependent' => false
		),
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'user_id',
			'dependent' => false
		)
	);
	
	
	// custom validation
	public function checkUniversity($check) {
		$universities = $this->Institution->find('list');
		
		if(	!empty($this->data[$this->alias]['institution_id'])
		AND	isset($universities[$this->data[$this->alias]['institution_id']])
		) {
			$this->Institution->id = $this->data[$this->alias]['institution_id'];
			$this->data[$this->alias]['country_id'] = $this->Institution->field('country_id');
			return true;
		}
		elseif(!empty($this->data[$this->alias]['university'])) {
			foreach($universities as $k => &$value) {
				$value = strtolower($value);
			}
			$pos = array_search(strtolower($this->data[$this->alias]['university']), $universities);
			if($pos !== false) {
				$this->data[$this->alias]['institution_id'] = $pos;
				$this->Institution->id = $pos;
				$this->data[$this->alias]['country_id'] = $this->Institution->field('country_id');
			}
			return true;
		}
		
		return false;
	}
	
	
	public function inviteRegister($data = array()) {
		$result = false;
		$this->set($data);
		if($this->validates(array('fieldList' => array('email', 'institution_id', 'first_name', 'last_name')))) {
			$token = $this->generateToken('password_token');
			$expiry = date('Y-m-d H:i:s', time() + $this->tokenExpirationTime);
			$this->data[$this->alias]['email_verified'] = 1;
			$this->data[$this->alias]['active'] = 1;
			$this->data[$this->alias]['approved'] = 1;
			$this->data[$this->alias]['password_token'] = $token;
			$this->data[$this->alias]['password_token_expires'] = $expiry;
			
			$result = $this->save($this->data, array('validate' => false));
			$result[$this->alias]['name'] = $result[$this->alias]['first_name'] . ' ' . $result[$this->alias]['last_name'];
		}
		return $result;
	}
	
	/**
	 * @param null $token
	 * @return array|bool|int|null
	 * overrides user plugin method
	 */
	public function checkPasswordToken($token = null) {
		if(empty($token)) return false;
		$user = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->alias . '.password_token' => $token
			)
		));
		if(empty($user)) return false;
		return $user;
	}
	
	
	public function approve($data = array()) {
		if(isset($data[$this->alias])) $data = $data[$this->alias];
		$this->data[$this->alias] = $data;
		
		$validator = $this->validator();
		unset($validator['email']);
		unset($validator['new_email']);
		unset($validator['institution_id']['special']);
		$validator['institution_id'] = array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'Institution may not be left blank.'
			)
		);
		$validator['city_id'] = array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'City may not be left blank.'
			)
		);
		$validator['country_id'] = array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'Country may not be left blank.'
			)
		);
		
		$this->data[$this->alias]['active'] = 1;
		$this->data[$this->alias]['approved'] = 1; // if approved is true, but active false, then the user is banned!
		$this->data[$this->alias]['approval_token'] = null;
		$this->data[$this->alias]['approval_token_expires'] = null;
		if($this->save($this->data, array('validate' => true))) {
			$this->recursive = -1;
			return $this->read();
		}
		return false;
	}
	
	
	function getModerators($country_id = null, $user_admin = true) {
		$admins = array();
		// try fetching the moderator in charge of the user's country,
		if(!empty($country_id)) {
			$admins = $this->find('all', array(
					'contain' => array(),
					'conditions' => array(
							'AppUser.country_id' => $country_id,
							'AppUser.user_role_id' => 2,	// moderators
							'AppUser.active' => 1
					)
			));
		}
		// then user_admin
		if(empty($admins) AND $user_admin) {
			$admins = $this->find('all', array(
					'contain' => array(),
					'conditions' => array(
							'AppUser.user_admin' => 1,
							'AppUser.active' => 1
					)
			));
		}
		// then admin
		if(empty($admins)) {
			$admins = $this->find('all', array(
					'contain' => array(),
					'conditions' => array(
							'AppUser.user_role_id' => 1,	// admins - do not check for the 'is_admin' flag, as it is currently also set for the mods
							'AppUser.active' => 1
					)
			));
		}
		
		return $admins;
	}
	
	/**
	 * @param $user
	 * @return array|bool|int|null
	 *
	 * The function will return false if no unique identification was possible.
	 * A parameter passed by reference will hold
	 */
	public function shibLogin(&$user = array()) {
		if($this->data AND $this->__isShibUser) {
			// find the matching user and log in
			if(!empty($this->data[$this->name]))
				$this->data = $this->data[$this->name];
			$user = $this->find('all', array(
				'contain' => array(),
				'conditions' => array(
					'or' => array(
						'AppUser.shib_eppn' => $this->data['shib_eppn'],
						'AppUser.email' => $this->data['shib_eppn']
					),
					//'AppUser.shib_eppn' => $this->data['email'],
					//'AppUser.email' => $this->data['email']
					'AppUser.active' => true
				)
			));
			
			if(!empty($user) AND count($user) == 1) {
				// unique identification
				return $user;
			}else{
				// try searching for persons with the same name, autocreate account...
				$user = $this->find('all', array(
					'contain' => array(),
					'conditions' => array(
						'and' => array(
							'AppUser.first_name' => $this->data['first_name'],
							'AppUser.last_name' => $this->data['last_name']
						),
						'AppUser.active' => true
					)
				));
			}
		}
		return false;
	}


	public function connectAccount($user = array()) {
		if(!empty($user)) {
			if(!empty($user[$this->name])) $user = $user[$this->name];
			foreach($user as $k => $v) {
				// do not allow overriding existing data
				if(empty($this->data[$k]) OR $k == 'id')
					$this->data[$k] = $v;
			}
			if(!empty($this->data['id'])) $this->id = $this->data['id'];
		}
		// save identity provider ID, if not already set
		if(!empty($this->id) AND !empty($this->data['shib_eppn'])) {
			$result = $this->save($this->data, false);
			$this->recursive = 0;
			$result = $this->read();
			if(!empty($result[$this->name])) $result = $result[$this->name];
			return $result;
		}
		return array();
	}
	
	
	/**
	 *
	 * @param string $email
	 * @param bool $subscribehandle the user un/subscription
	 */
	public function handleUserDHMailList($email, $subscribe = true) {
		$mailcfg = Configure::read('List');
		if($mailcfg['subscribeURL'] && $mailcfg['adminPwd'] && $mailcfg['unsubscribeURL']){
			if($subscribe) {
				$mailurl = $mailcfg['subscribeURL'];
			}else{
				$mailurl = $mailcfg['unsubscribeURL'];
			}
			$ch = curl_init($mailurl.$email);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch,CURLOPT_POSTFIELDS, "&adminpw=".$mailcfg['adminPwd']);
			$response = curl_exec($ch);
			curl_close($ch);
		}else{
			error_log('problem with the list config variables');
		}
	}
	
	
}
?>
