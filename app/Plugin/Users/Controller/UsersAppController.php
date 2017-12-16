<?php
/**
 * Copyright 2010 - 2014, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2014, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Users App Controller
 *
 * @package users
 * @subpackage users.controllers
 */

App::uses('AppController', 'Controller');

class UsersAppController extends AppController {
	
	public $components = array(
		// you need to include this component also on app-level, in order to have Auth available for all controllers 
		'Users.DefaultAuth'
	);
	
	protected function _getMailInstance() {
		App::uses('CakeEmail', 'Network/Email');
		$emailConfig = Configure::read('Users.emailConfig');
		if($emailConfig AND $emailConfig != 'default') {
			return new CakeEmail($emailConfig);
		}else{
			return new CakeEmail();
		}
	}
	
	public function blackHole($type = null) {
		/** if this method needs to be overridden (e.g. SERVER_NAME resolves to the wrong name), 
		* define a differently named function in AppController and register in app users configuration like this:
		Configure::write('Users.securitySettings', array(
			'blackHoleCallback' => 'blueHole',
			'csrfCheck' => true
		));
		*/
		switch($type) {
			case 'secure':
				if($this->action != 'blackHole') {
					return $this->redirect('https://' . env('SERVER_NAME') . $this->here);
				}
			default:
				throw new BadRequestException(__d('cake_dev', 
						'The security token on the form you submitted is expired. 
						To prevent cross request forgery (CRF), your request has been blocked.
						Please reload the page and try again.'));
		}
	}
	
	
	
	
	
	
	
}
?>