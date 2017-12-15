<?php

class CcConfigAcosController extends CakeclientAppController {
	
	public $components = array(
		'Cakeclient.AclMenu'
	);
	
	
	
	
	public function updateTree($aroKeyName = null, $aroKeyValue = null) {
		
		// #ToDo: set up a form or any thelike to set aro_model, id & name
		
		
		$this->CcConfigAco->updateTree($aroKeyName, $aroKeyValue);
		
		$this->redirect('index');
	}
	
	
}
?>