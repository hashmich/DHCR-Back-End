<?php
// check for an override
if(file_exists(APP . 'Model' . DS . pathinfo(__FILE__, PATHINFO_BASENAME))) {
	require_once(APP . 'Model' . DS . pathinfo(__FILE__, PATHINFO_BASENAME));
	return;
}

class CcConfigAco extends CakeclientAppModel {
	
	
		
	public function getAco($controller, $action = null, $plugin = null) {
		if(empty($action)) $action = 'index';
		if(empty($controller)) return false;
		$conditions = array('controller' => $controller, 'action' => $action);
		if(!empty($plugin)) $conditions['plugin'] = $plugin;
		return $this->find('first', array('conditions' => $conditions));
	}
	
	
	public function getDefaultAco($tableName, $method, $plugin = null) {
		if(empty($plugin) OR $plugin === true) $plugin = null;
		return array(
			// id
			// plugin # TODO
			'action' => $method,
			'controller' => $tableName,
			'plugin' => $plugin
		);
	}
	
	
	public function getDefaultAcosTableTree($tableName = null, $tablePrefix = null) {
		$acos = array();
		// default CRUD actions
		$methods = array('index','add','view','edit','delete');
		// access the model's behaviors, if it uses Sortable, add the method "reset_order"
		$modelName = Inflector::classify($tableName);
		$$modelName = ClassRegistry::init($modelName);
		if($$modelName->Behaviors->loaded('Sortable')) {
			$methods[] = 'reset_order';
		}
		
		// method to identify existing controller methods in plugin's AppModel
		$union = $this->getControllerMethods($tableName, $plugin = false, $pluginAppOverride, $methods);
		
		
		foreach($union as $method) {
			$acos[] = $this->getDefaultAco($tableName, $method, $plugin);
			
		}
	
		return $acos;
	}
}
?>	