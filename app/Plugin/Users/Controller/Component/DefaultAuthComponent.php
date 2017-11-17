<?php

App::uses('Component', 'Controller');

class DefaultAuthComponent extends Component {
	
	
	public $settings = array();
	
	public $components = array();
	
	private $controller = null;
	
	
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);

		$this->settings = Hash::merge($this->_defaults(), $settings);
		foreach($this->settings as $key => $value)
			$this->{$key} = $value;
	}
	
	private function _defaults() {
		return array(
			'components' => array(
				'Auth' => array(
					'priority' => 2,
					'loginAction' => array(
						'controller' => 'users',
						'action' => 'login',
						'plugin' => 'users',
						'admin' => false
					),
					'authError' => 'Please log in to access this location.',
					'authenticate' => array(
						'Form' => array(
							'fields' => array(
								'username' => Configure::read('Users.loginName'),
								'password' => 'password'
							),
							'userModel' => Configure::read('Users.userModel'),
							'scope' => array(
								Configure::read('Users.userModel') . '.active' => 1,
								Configure::read('Users.userModel') . '.email_verified' => 1
							)
						)
					),
					'authorize' => array(
						'Users.AllowedActions'
					),
					'loginRedirect' => array('action' => 'dashboard','controller' => 'users','plugin' => 'users'),
					'logoutRedirect' => '/'
				)
			)
		);
	}
	
	
	public function initialize(Controller $controller) {
		if(Configure::read('Users.disableDefaultAuth') === true) return;
		
		$this->controller = $controller;
		// load all components
		foreach($this->components as $component => $settings) {
			$controller->components[$component] = $settings;
			$controller->{$component} = $controller->Components->load($component, $settings);
			$controller->{$component}->initialize($controller);
		}
		
		$controller->set('auth_user', $controller->Auth->user());
		
		if(Configure::read('Users.DefaultAuthAllowAll') === true) {
			// since Cakephp 2.1, provide no arguments to allow all:
			$controller->Auth->allow();
		}
	}
	
	/**
	 * SuperUser checking against the definition given in Configure class: Users.SuperUserDefinition
	 * @return unknown|boolean
	 */
	public function isAdmin($user = null) {
		if(method_exists($this->controller, 'isAdmin')) {
			return $this->controller->isAdmin($user);
		}
		
		$definition = Configure::read('Users.superUserDefinition');
		if(!$user) $user = $this->controller->Auth->user();
		if($user) {
			if(isset($user[$this->userModel])) $user = $user[$this->userModel];
			if($definition AND is_array($definition)) foreach($definition as $key => $value) {
				if(isset($user[$key]) AND $user[$key] === $value) {
					return true;
				}
				elseif(is_array($value)) {
					foreach($value as $v) {
						if($user[$key] === $v) return true;
					}
				}
			}
		}
		
		return false;
	}
	
}
?>