<?php
class AclMenuComponent extends Component {
	
	public $menuModelName = 'CcConfigMenu';
	
	public $tableModelName = 'CcConfigTable';
	
	public $actionModelName = 'CcConfigAction';
	

	public $aclLookupModelName = 'CcConfigAcosAro';	// this is not yet the actual ARO table, but the connection table
	
	public $aroKeyName = 'AppUser.user_role_id';
	
	public $aroKeyValue = null;
	
	
	// the menu currently loaded for the user - needs to be stored, as we will have to do ACL checking against this structure - or second db query?
	protected $currentMenu = array();
	
	// the model where the permissions are bound against 
	//public $aro_model = 'UserRole';		// 'User'
	
	// acf - the access controling field
	//public $acf = 'user_role_id';		// 'id'
	
	// the field value that enables admin function (allow all for admin group UserRole.id = 1)
	//public $acf_adminValue = 1;			// 42 (id of the admin account)
	
	// the currently requesting object ID (null = anonymous) - aka a role, or user.
	//public $aro_id = null;
	
	
	/**
	 * The menus we want to generate if no other menu is available for the current ARO.
	 * If require_super_user is not set or false, the menu will be public to all, 
	 * who can access the application level showing this menu!!!
	 */
	public $defaultMenus = array(
		array(
			'name' => 'Config',
			'table_prefix' => 'cc_config_',		// table name prefix
			'data_source' => 'default',
			'require_super_user' => true,			// to be checked against DefaultAuth.isAdmin
			'layout_block' => 'cakeclient_navbar'
			// #ToDo: implement this (default false):
			//'classPrefix' => false
			// what about class prefixes for different sources?
			// what about prefixing during ACO generation?
		),
		array(
			'name' => 'Tables',
			'dataSource' => 'default',
			'require_super_user' => true,
			'layout_block' => 'cakeclient_navbar'
			// no table name prefix - gather all tables without prefix
		)
		// extend with further prefixed (plugin) table groups);
	);
	
	
	public $settings = array();
	
	public $controller = null;
	
	public $request = null;
	
	
	
	
	
	
	
	public function getModel($modelName = null) {
		if(!isset($this->controller->{$modelName}))
			$this->controller->loadModel($modelName);
		return $this->controller->{$modelName};
	}
	
	
	
	
	// require Auth
	// optional DefaultAuth (admin check)
	
	
	
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->settings = $settings;
	}
	
	
	public function isAdmin() {
		if(	(method_exists($this->controller, 'isAdmin')
			AND $this->controller->isAdmin())
		OR	(isset($this->controller->DefaultAuth)
			AND $this->controller->DefaultAuth->isAdmin())) return true;
		return false;
	}
	
	
	/*
	* don't do anything about authorisation at this stage, 
	* the component might be used to generate a menu alone
	*/
	public function initialize(Controller $controller) {
		$this->controller = $controller;
		
		foreach($this->settings as $key => $value)
			$this->{$key} = $value;
		
		$this->request = $controller->request;
		
		
		if(isset($this->controller->Auth))
			if(!$this->aro_id = $this->controller->Auth->user($this->acf))
				// just to make sure the value is not (bool)false, as NULL is a valid aro_id for the public user
				$this->aro_id = null;
	}
	
	
	/**
	 * The passed ARO will be used to lookup matching menu trees. 
	 * Permissions granted to an individual object will always extend the inherited 
	 * permissions  bound to any parent object (eg UserRole).
	 * 
	 * Thus, the algorithm will first check for matches of this aroKeyName pattern, as specified in the settings: 
	 * <AroModelName>.<parent_model_name>_id
* TODO: move this key name to some configuration file!!!
	 * Then, it will check following pattern, if there's no match and the firstly configured pattern is not the primary key of the AroModelName: 
	 * <AroModelName>.id for the passed object.  (id doesn't need to be configured, as it is the model's primary key by convention)
	 * 
	 * @param array $arObject, optional
	 * @return boolean
	 */
	public function check($aroObject = array()) {
		// TODO: check against the menu tree for a given user/ARO
		if($this->isAdmin())
			return true;
		return false;
	}
	
	/*
	public function getAcl($aro_key_value = null, $aro_key_name = null) {
		$model = $this->getModel($this->aclLookupModelName);
		return $model->find('all', array(
			'contain' => array(
				$this->tableModelName => array(
					'conditions' => array('name' => $this->request->params['controller']),
					$this->actionModelName
				)
			),
			'conditions' => array(
				'foreign_key' => $aro_key_value,
				'model' => $this->aro_model
			)
		));
	}
	*/
	
	/*
	* requires: DefaultAuthComponent (includes AuthComponent)
	* doing ACL authorisation in this method
	*/
	/*
	//public function checkPermission($aro_key_name = null, $aro_key_value = null) {
	public function check($aro_key_value = null, $aro_key_name = null) {
		if(empty($aro_key_value)) $aro_key_value = $this->aro_id;
		$params = $this->controller->request->params;
		if(!empty($params['pass'])) foreach($params['pass'] as $arg) {
			$params[] = $arg;
		}
		unset($params['pass']);
		$checkPath = str_replace(
			$this->request->base, '',
			Router::url($params)
		);
		// give way for the admin
		if($this->isAdmin()) {
			return true;
		}else{
			
			// #ToDo: make a quicker check, that doesn't iterate over the entire tree - use joins
			
			// now authorize against the list! (if any)
			$acl = $this->getAcl($aro_key_value);
			
			if(!empty($acl)) {
				foreach($acl as $i => $menu) {
					foreach($menu[$this->tableModelName] as $t => $table) {
						if($table['name'] == $this->request->params['controller']) {
							if(!empty($table['allow_all'])) return true;
							
							if(!empty($table[$this->actionModelName])) {
								foreach($table[$this->actionModelName] as $a => $action) {
									if($action['name'] == $this->request->params['action']) {
										if(!empty($action['url'])) {
											/* Can't imagine a situation where 
											* additional parameters are allowed, 
											* but not the action without parameter...
											* AS LONG THE ACTION IS MENTIONED IN THE PATH!!!
											* But we might have a prefixed URL, like /admin routing
											*
											if(strpos($checkPath, $action['url']) === 0) return true;
										}else{
											return true;
										}
									}
								}
								break;
							}
						}
					}
				}
			}
		}
		
		return false;
	}
	*/
	
	/**
	 * 
	 * @param String $routePrefix	the current (or only prefix) to create default menus
	 */
	public function setMenu($routePrefix = null) {
		$cakeclientMenu = $this->getMenu($routePrefix);
		
		if(!$this->request->is('requested') AND Configure::read('Cakeclient.navbar')) {
			// load the AssetHelper which appends the top_nav Menu to whichever layout
			if(	!in_array('Cakeclient.Asset', $this->controller->helpers)
			AND	!isset($this->controller->helpers['Cakeclient.Asset']))
				$this->controller->helpers[] = 'Cakeclient.Asset';
		}
		
		$this->controller->set(compact('cakeclientMenu'));
	}
	
	/**
	 * 
	 * @param String $routePrefix	the current (or only) prefix to create a default menu
	 */
	public function getMenu($routePrefix = null) {
		$menu = array();
		//$menuName = $this->acf.'_'.$aro_id.'_menu';
		//$menu = Cache::read($menuName, 'cakeclient');
		if(empty($menu)) {
			
			// try reading from the cc_config_tables tables
			//$menu = $this->getAcl($aro_id);
			//$menu = $this->getAcl(2);
			
			// only if demanded or admin: get defaults if no menu available
			if(empty($menu)) {
				$menuModel = $this->getModel($this->menuModelName);
				$menu = $menuModel->getDefaultMenuTree($routePrefix, $this->isAdmin(), $this->defaultMenus, 'menu');
			}	
			
			//Cache::write($menuName, $menu, 'cakeclient');
		}
		
		return $menu;
	}
	
	public function getDefaultActions($args = array()) {
		if(empty($args)) $args = $this->__getActionConditions();
		foreach($args as $key => $value) $$key = $value;
		
		$actionModel = $this->getModel($this->actionModelName);
		return $actionModel->getDefaultActions($tableName, $tablePrefix, $viewName, $urlPrefix);
	}
	
	public function getActions() {
		$args = $this->__getActionConditions();
		//foreach($args as $key => $value) $$key = $value;
		$actions = array();
		// #ToDo: try reading from config
		
		if(empty($actions)) {
			// only if demanded or admin: get defaults if no menu available
			if(empty($actions) AND $this->isAdmin()) {
				$actions = $this->getDefaultActions($args);
			}
		}
		
		return $actions;
	}
	
	private function __getActionConditions() {
		$aro_id = $this->aroKeyValue;
		$aro_model = $this->aroKeyName;
		$tableName = $this->request->params['controller'];
		$viewName = $this->request->params['action'];
		$urlPrefix = null;
		$role = $this->getRole();
		if(!empty($role)) {
			$urlPrefix = (isset($role['cakeclient_prefix'])) ? $role['cakeclient_prefix'] : null;
		}
		// determine the table prefix
		$tablePrefixes = Hash::extract($this->defaultMenus, '{n}.prefix');
		$tablePrefix = null;
		foreach($tablePrefixes as $prefix) if(strpos($prefix, $tableName) === 0) $tablePrefix = $prefix;
		return array(
				'tableName' => $this->request->params['controller'],
				'viewName' => $this->request->params['action'],
				'urlPrefix' => $urlPrefix,
				'tablePrefix' => $tablePrefix
		);
	}
	
	/*
	public function getActions($table = null, $view = null) {
		if(empty($view))
			$view = $this->request->params['action'];
		if(empty($table))
			$table = $this->request->params['controller'];
		
		$prefix = Configure::read('Cakeclient.prefix');
		if(!$prefix) $prefix = false;
		
		$modelName = $this->controller->modelClass;
		
		
		
		$model = $this->getModel($this->tableModelName);
		$currentAction = $model->find('first', array(
			'contain' => array(
				$this->actionModelName => array(
					'conditions' => array($this->actionModelName.'.name' => $view),
					'CcConfigActionsView' => array(
						'order' => 'CcConfigActionsView.position'
					)
				)
			),
			'conditions' => array($this->tableModelName.'.name' => $table)
		));
		/** The menu / context-menu templates filter for the "contextual" property.
		*	"index" views display many records and thus must check if an action belongs into a records context. 
		*	Actions that are contextual (edit, view) don't have to check for the menu-action's context, 
		*	as the context is already set by the record's ID.
		
		$current_action_must_check_context = true;
		if($view != 'index') $current_action_must_check_context = false;
		if(isset($currentAction[$this->actionModelName][0]['contextual']))
			$current_action_must_check_context = !$currentAction[$this->actionModelName][0]['contextual'];
		
		// check for the actions linked to the current view first, then all actions except the current one, then default list
		if(!empty($currentAction[$this->actionModelName][0]['CcConfigActionsViewsAction'])) {
			$actions = $currentAction[$this->actionModelName][0]['CcConfigActionsViewsAction'];
		}else{
			$actions = $this->controller->{$this->tableModelName}->find('first', array(
				'contain' => array(
					$this->actionModelName => array(
						'conditions' => array($this->actionModelName.'.name !=' => $view)
					)
				),
				'conditions' => array($this->tableModelName.'.name' => $table)
			));
			if(!empty($actions[$this->actionModelName])) {
				$actions = $actions[$this->actionModelName];
			}else{
				$actions = array();
				// get the default list
				$actionsName = $view . 'Actions';
				if(isset($this->$actionsName)) {
					$actions = $this->$actionsName;
				}
				if(strtolower($view) == 'index') { 
					$tableModel = Inflector::classify($table);
					$$tableModel = ClassRegistry::init($tableModel);
					// access the model's behaviors and add a special method if Sortable is loaded
					if($$tableModel->Behaviors->loaded('Sortable')) {
						$actions[] = 'reset_order';
					}
				}
			}
		}
		
		$returnActions = array();
		if(!empty($actions)) {
			foreach($actions as $k => $action) {
				$action_id = null;
				if(is_array($action)) {
					if(isset($action['show']) AND !$action['show']) continue;
					if(!empty($action['label'])) {
						$label = $action['label'];
					}else{
						$label = Inflector::humanize(Inflector::underscore($action['name']));
					}
					$actionName = $action['name'];
					if(!empty($action['id'])) $action_id = $action['id'];
				}else{
					// mangling the default lists
					$label = Inflector::humanize(Inflector::underscore($action));
					switch($action) {
						case 'add': $label .= ' '.$modelName; break;
						case 'index': $label = 'List '.$this->virtualController; break;
					}
					$actionName = $action;
				}
				// set the route prefix to be the plugin element of the url, as this will appear in front of it all, and not named "plugin"
				$_action = array(
					'label' => $label,
					'action_id' => $action_id,
					'url' => array(
						'action' => $actionName,
						'plugin' => $prefix
					)
				);
				if(is_array($action) AND !empty($action['controller'])) {
					$_action['url']['controller'] = $action['controller'];
				}else{
					$_action['url']['controller'] = $table;
				}
				
				// handle appending record id's or appearance in index tables
				$_action['contextual'] = $_action['append_id'] = false;
				if(!in_array($actionName, array('add', 'index', 'reset_order'))) {
					$_action['contextual'] = $_action['append_id'] = true;
				}
				if(is_array($action)) {
					$_action['contextual'] = $_action['append_id'] = (bool)$action['contextual'];
					$_action['bulk_processing'] = (bool)$action['bulk_processing'];
				}
				// if currently not in an index view, put all actions in the top menu - set contextual to false.
				if(!$current_action_must_check_context) {
					$_action['contextual'] = false;
					// note: we're still appending the id, if the action previously was contextual
				}
				
				// check wether we're on a prefix route (consider it as some kind of access control)
				$routes = Configure::read('Routing.prefixes');
				$add = true;
				$prefixed = false;
				if(!empty($routes) AND is_array($routes)) {
					foreach($routes as $route) {
						$add = true;
						$prefixed = false;
						if(strpos($actionName, $route . '_') === 0) {
							if($route !== $prefix) {
								// we're not on the route of the prefix the action has
								$add = false;
							}else{
								// remove the prefix, as this will be added via the URL prefix again
								$_action['url']['action'] = substr($actionName, strlen($route) + 1);
								$prefixed = true;
							}
						}
					}
				}
				
				if(!empty($routes) AND !$prefixed AND !in_array($actionName, array('index', 'view', 'edit', 'add', 'delete'))) {
					// we're leaving a prefix route here, otherwise cake would not find a non-prefixed method outside the plugin - the downside of prefix routing!
					$_action['url']['plugin'] = null;
				}
				
				if($add) {
					$returnActions[$k] = $_action;
				}
			}
		}
		
		return $returnActions;
	}
	*/
	
	function setActions() {
		$actions = $this->getActions();
		$this->controller->set('crudActions', $actions);
		return $actions;
	}
	
	
	public function getRole() {
		$split = explode('.', $this->aroKeyName);
		$aro_model = $split[0];
		$model = $this->getModel($aro_model);
		$role = $model->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->aroKeyName => $this->aroKeyValue
			)
		));
		if(!empty($role) AND !empty($role[$aro_model])) return $role[$aro_model];
		return array();
	}
	
	
	
}
?>