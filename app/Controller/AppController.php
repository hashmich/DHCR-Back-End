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

App::uses('Controller', 'Controller');
App::uses('AppUser', 'Model');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array(
		'Users.DefaultAuth' => ['components' => ['Auth' => ['logoutRedirect' => '/users/login']]],
		'DebugKit.Toolbar',
		'Paginator',
		'Session',
		'Flash',
		'Cakeclient.Crud',
		'RequestHandler'
	);
	
	// paging defaults
	public $paginate = array(
		'limit' => 10,
		'maxLimit' => 200
	);
	
	public $uses = array(
		'AppUser'
	);
	
	public $shibUser = null;
	
	
	
	public function beforeFilter() {
		// maintain pagination settings
		if($paginate = $this->Session->read('Paginate'))
			$this->paginate = array_merge($this->paginate, $paginate);
		if(!empty($this->request->data['Pager'])) {
			$form = $this->request->data['Pager'];
			if(!empty($form['limit']) AND ctype_digit($form['limit'])) {
				$this->paginate['limit'] = $form['limit'];
				$this->Session->write('Paginate.limit', $form['limit']);
			}
		}

		// reset filter if moving somewhere else
        if( $this->request->controller != 'courses' AND $this->request->action != 'index'
        AND !$this->Auth->user()) {
            $this->_resetFilter();
        }
		
		
		if(isset($this->Security))	{
			$this->Security->requireSecure = array();
			$this->Security->unlockedFields[] = 'g-recaptcha-response';
		}
		
		$this->set('modelName', $this->modelClass);
		
		// for debugging purposes
		if(!$this->Auth->user() AND strpos(APP, 'xampp') !== false AND Configure::read('debug') > 0) {
			//$this->Auth->allow();
			//debug('allowed by debug settings');
		}
		
		if($this->Auth->user('user_role_id') AND $this->Auth->user('user_role_id') < 3) {
			// dynamically load the AclMenu Component
			if(!isset($this->AclMenu)) {
				$this->AclMenu = $this->Components->load('Cakeclient.AclMenu');
				// if not loaded before beforeFilter, we need to initialize manually
				$this->AclMenu->initialize($this);
				
				
			}
		}
		
		if(!empty($_SERVER['HTTP_EPPN'])) {
            $this->shibUser = new AppUser();
            if($this->shibUser->isShibUser()) {
                $this->set('shibUser', $this->shibUser->data);

                if($this->Auth->user() AND !$this->Auth->user('shib_eppn')
                AND	(!$this->Session->read('Users.block_eppn')
                    || $this->Session->read('Users.block_eppn') != $this->shibUser->data['shib_eppn'])
                ) {
                    $user = $this->shibUser->connectAccount();
                    $this->Auth->login($user);
                    $this->set('auth_user', $user);
                }
            }else{
                $this->shibUser = null;
            }
		}
	}
	
	
	
	
	public function beforeRedirect($url, $status = null, $exit = true) {
		if(	!empty($this->request->params['layout'])
		AND	$this->request->params['layout'] == 'iframe'
		AND	is_string($url) AND strpos($url, 'iframe') === false
		) {
			$url = (strpos($url, '/') === 0) ? '/iframe' . $url : '/iframe/' . $url;
			return array(
				'url' => $url,
				'status' => $status,
				'exit' => $exit
			);
		}
	}
	
	
	// reset filter function
	public function reset_filter($filter = null) {
		$this->_resetFilter($filter);
		$this->redirect(array('action' => 'index'));
	}

	protected function _resetFilter($filter = null) {
        if(!empty($filter)) {
            // Only remove a single filter key. As the filter keys contain find-conditions in "."-notation, Session::delete() doesn't handle it correctly
            $store = $this->Session->read('filter');
            unset($store[$filter]);
            $this->Session->write('filter', $store);
        }else{
            // remove all filters
            $this->Session->delete('filter');
        }
    }
	
	
	protected function _checkCaptcha(&$errors = array()) {
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) 
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		$data = array(
			'secret' => Configure::read('App.reCaptchaPrivateKey'),
			'response' => $this->request->data['g-recaptcha-response'],
			'remoteip' => $ip
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$result = curl_exec($ch);
		curl_close($ch);
		
		if(empty($result)) return false;
		$result = json_decode($result, true);
		if(!empty($result['error-codes'])) $errors = $result['error-codes'];
		if(!empty($result['success'])) return true;
		
		return false;
	}
	
}


