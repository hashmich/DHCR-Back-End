<?php
// check for an override
if(file_exists(APP . 'Model' . DS . pathinfo(__FILE__, PATHINFO_BASENAME))) {
	require_once(APP . 'Model' . DS . pathinfo(__FILE__, PATHINFO_BASENAME));
	return;
}


/**
 * 
 */

class CcConfigAcosAro extends CakeclientAppModel {
	
	// there's an complex key on the combination (aro_key_name, aro_key_value)
	
	/* Virtually - yes. 
	public $belongsTo = array(
		'UserRole' => array(
			'className' => 'UserRole'
		)
	);
	*/
	
	public $belongsTo = array(
		'CcConfigMenu' => array(
			'className' => 'CcConfigMenu'
		)
		// AroModel
	);
	
	public $aro_key_name = 'User.user_role_id';
	
	public $aro_key_value = null;
	
	// TODO: how to dynamically make a model binding on find and return related results?
	
	
	
	
	
	
	
	
	
	
	public function updateTree($source = array()) {
		if(empty($sources)) $sources = array('default');
		
		$data['CcConfigAcosAro'] = $this->getDefaultAco();
		$data['CcConfigTable'] = $this->CcConfigTable->getDefaultAcoTableTree($sources);
		
		$result = $this->saveAll($data, array('validate' => false, 'deep' => true));
	}
	
	
	public function getDefaultAco() {
		return array(
			//'id',
			//'cc_config_menu_id',
			'aro_key_name' => $this->aro_key_name,
			'aro_key_value' => $this->aro_key_value
		);
	}
	
	
}
?>