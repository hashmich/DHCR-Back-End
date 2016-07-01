<?php
class CakeclientAppController extends AppController {
	
	var $components = array(
		'Cakeclient.Crud',
		'Session',
		'Paginator'
	);
	
	var $helpers = array(
		'Cakeclient.Display',
		'Cakeclient.Asset'
	);
	
	var $overrideController = null;
	
	// paging defaults
	public $paginate = array(
		'limit' => 10,
		'maxLimit' => 200
	);
	
	
	
	
	
	
	function beforeFilter() {
		// check with main application AppController, if we got permission to proceed
		parent::beforeFilter();
		// require Auth or 
		if(Configure::read('Cakeclient.AclChecking')) {
			if(!isset($this->Auth)) {
				$this->Auth = $this->Components->load(Configure::read('Cakeclient.AuthComponent'));
				// if not loaded before beforeFilter, we need to initialize manually
				$this->Auth->initialize($this);
			}
			// yet we do not have permission from the app-level controller
			if(!$this->Auth->isAuthorized()) {
				if(!isset($this->AclMenu)) {
					$this->AclMenu = $this->Components->load('Cakeclient.AclMenu');
					// if not loaded before beforeFilter, we need to initialize manually
					$this->AclMenu->initialize($this);
				}
				if($this->AclMenu->check()) {
					// allow the requested action
					$this->Auth->allow();
				}
			
			}
		}
		
		// we're on a CRUD route - set all the CRUD relevant variables (actions, menu, view, fieldlist, relations)
		if(!in_array(strtolower($this->request->params['action']), array('delete','fix_order')))
			$this->Crud->setCRUDviewVars();
		
		// maintain pagination settings
		if($paginate = $this->Session->read('Paginate')) $this->paginate = $paginate;
		if(!empty($this->request->data['Pager'])) {
			$form = $this->request->data['Pager'];
			if(!empty($form['limit']) AND ctype_digit($form['limit'])) {
				$this->paginate['limit'] = $form['limit'];
				$this->Session->write('Paginate.limit', $form['limit']);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	/** 
	* Check if a method is defined in the AppController and pass it's return value by reference.
	* This is...odd:
	* We're using the hierarchically higher AppController to override the functions in here. 
	* As this is only a generic plugin, that makes sense. 
	* CakeclientAppController is only in effect, if we're on a CRUD route, however. 
	*/
	function _appControllerOverride($method = null, &$return) {
		if(!empty($method) AND is_string($method)) {
			// Do not use $this as the current object's reference - $this' parent class is already extended by $this!
			// Naming $this by it's string classname checks the parent statically. 
			if(method_exists(get_parent_class('CakeclientAppController'), $method)) {
				$args = func_get_args();
				// The first argument is the method name, second the return value(s). We don't want to pass them. 
				array_shift($args);
				array_shift($args);
				$return = parent::$method($args);
				return true;
			}
		}
		return false;
	}
	
	/**
	* This basically does the same as 
	* Router::url($action) with $action['url']['base'] = false. 
	* But as router may swallow arguments due to active routes (/posts/index -> /posts), 
	* set the URL arguments explicitly. 
	* If more advanced logic is required, this method may be 
	* overridden in the application's AppController. 
	*/
	function _normalizePath($action = array()) {
		$return = array();
		if($this->_appControllerOverride('normalizePath', $return, $action)) return $return;
		
		$normalizedPath = '';
		if(!empty($action['plugin'])) {
			$normalizedPath = '/' . $action['plugin'];
			unset($action['plugin']);
		}
		if(!empty($action['controller'])) {
			$normalizedPath .= '/' . $action['controller'];
			unset($action['controller']);
		}
		if(!empty($action['action'])) {
			$normalizedPath .= '/' . $action['action'];
			unset($action['action']);
		}
		if(!empty($action)) {
			foreach($action as $k => $v) {
				if(ctype_digit($k) AND !is_string($k)) {
					$normalizedPath .= '/' . $v;
				} 
			}
		}
		return $normalizedPath;
	}
	
	
	
	
	
	
	
	
	
	function index() {
		$this->Crud->index();
	}
	
	function add() {
		$this->Crud->add();
	}
	
	function edit($id = null) {
		$this->Crud->edit($id);
	}
	
	function view($id = null) {
		$this->Crud->view($id);
	}
	
	function delete($id = null) {
		$this->Crud->delete($id);
	}
	
	function fix_order() {
		$this->Crud->fix_order();
	}
}





