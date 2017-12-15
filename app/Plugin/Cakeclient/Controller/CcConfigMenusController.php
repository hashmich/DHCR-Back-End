<?php

class CcConfigMenusController extends CakeclientAppController {
	
	public $components = array(
		'Cakeclient.AclMenu'
	);
	
	public $uses = array('CcConfigMenu');
	
	
	
	/**
	 * This method will save freshly created default menu trees to the database.
	 * These trees are not connected to any ARO in first place and will therefore not show up
	 * as menus in the application unless you connect the menu groups to any ARO. 
	 * Edit the menu tree structures as required for that ARO type beforehand. 
	 */
	public function create_default_trees() {
		// get the available prefixes to populate the form options
		$route_prefix = Configure::read('Cakeclient.prefixes');
		
		if(!empty($this->request->params['CcConfigMenu']) OR is_string($route_prefix)) {
			if(!is_string($route_prefix))
				$route_prefix = $this->request->params['CcConfigMenu']['route_prefix'];
			
			$this->CcConfigMenu->createDefaultTrees($route_prefix, $this->AclMenu->defaultMenus);
			$this->Flash->set("Menu trees have been created based on default configuration, 
					existing controller functions  and database structure");
			// TODO: maintain route prefix
			// current prefix?
			$this->redirect(array(
				'action' => 'index',
				'controller' => $this->request->params['controller'],
				'plugin' => $this->request->params['plugin']
			));
		}
		$this->set('route_prefixes', Configure::read('Cakeclient.prefixes'));
	}
	
	
}
?>