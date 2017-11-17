<?php
// check for an override
if(file_exists(APP . 'Model' . DS . pathinfo(__FILE__, PATHINFO_BASENAME))) {
	require_once(APP . 'Model' . DS . pathinfo(__FILE__, PATHINFO_BASENAME));
	return;
}

class CcConfigMenu extends CakeclientAppModel {
	
	public $actsAs = array(
		'Utils.Sortable' => array(
			'parentId' => 'model.foreign_key'
		)
	);
	
	
	public $hasMany = array(
		'CcConfigTable' => array(
			'className' => 'CcConfigTable'
		),
		'CcConfigAcosAro' => array(
			'className' => 'CcConfigAcosAro'
		)
	);
	
	/* virtually:
	public $hasAndBelongsToMany = array(
		AroModel (User or UserRole via CcConfigAcosAro)
	);
	*/
	
	
	
	
	protected  function getDefaultMenu($group = array(), $k = null) {
		
		$source = (!empty($group['data_source'])) ? $group['data_source'] : 'default';
		$prefix = (!empty($group['table_prefix'])) ? $group['table_prefix'] : null;
		$name = (!empty($group['name'])) ? $group['name'] : 'Menu '.$k;
		
		return array(
			//'id',
			'label' => $name,
			'position' => $k,
			'layout_block' => 'cakeclient_navbar'
		);
	}
	
	public function getDefaultMenuTree($routePrefix = null, $isAdmin = false, $menuGroups = array()) {
		$menu = array();
		if(!empty($menuGroups)) {
			$tablePrefixes = Hash::extract($menuGroups, '{n}.table_prefix');
			foreach($menuGroups as $k => $group) {
				if(!$isAdmin AND !empty($group['require_super_user']))
					continue;
				$menu[$k]['CcConfigMenu'] = $this->getDefaultMenu($group, $k + 1);
				$source = (!empty($group['data_source'])) ? $group['data_source'] : 'default';
				$tableTree = $this->CcConfigTable->getDefaultMenuTableTree($routePrefix, $group, $tablePrefixes, $source);
				if(!empty($tableTree['CcConfigTable'])) $tableTree['CcConfigTable'];
				$menu[$k]['CcConfigMenu']['CcConfigTable'] = $tableTree;
			}
		}
		return $menu;
	}
	
	
	/* Copied over from the former configuration model
	*  
	function add($clone = null, $data = array()) {
		if(!Configure::read('Cakeclient.config_id') OR $clone == 'new') {
			$configuration = array(
				'default' => 1,
				'disable_cache_checking' => 1,
				'robots' => 'noindex,nofollow'
			);
			if(Configure::read('Cakeclient.config_id')) {
				$configuration['default'] = 0;
			}
			$this->create();
			if($configuration = $this->save($configuration, false)) {
				Configure::write('Cakeclient', $configuration['CcConfigConfiguration']);
			}
			
		}else{
			// $clone possible values: empty, 'current', [ID]
			$clone_id = Configure::read('Cakeclient.config_id');
			if(!empty($clone) AND ctype_digit($clone)) {
				$clone_id = $clone;
			}
			$data = $this->find('first', array(
				'conditions' => array('CcConfigConfiguration.id' => $clone_id),
				'contain' => array(
					'CcConfigTable' => array(
						'order' => array(
							'CcConfigTable.cc_config_configuration_id' => 'ASC',
							'CcConfigTable.position' => 'ASC'
						),
						'CcConfigFielddefinition' => array(
							'order' => array(
								'CcConfigFielddefinition.cc_config_table_id' => 'ASC',
								'CcConfigFielddefinition.position' => 'ASC'
							),
							'conditions' => array('CcConfigFielddefinition.cc_config_action_id' => null)
						),
						'CcConfigAction' => array(
							'order' => array(
								'CcConfigAction.cc_config_table_id' => 'ASC',
								'CcConfigAction.position' => 'ASC'
							),
							'CcConfigFielddefinition' => array(
								'order' => array(
									'CcConfigFielddefinition.cc_config_action_id' => 'ASC',
									'CcConfigFielddefinition.position' => 'ASC'
								)
							)
						)
					)
				)
			));
			$data['CcConfigConfiguration']['default'] = 0;
			unset($data['CcConfigConfiguration']['id']);
			foreach($data['CcConfigTable'] as $i => $table) {
				unset($data['CcConfigTable'][$i]['id']);
				unset($data['CcConfigTable'][$i]['cc_config_configuration_id']);
				foreach($table['CcConfigAction'] as $k => $action) {
					unset($data['CcConfigTable'][$i]['CcConfigAction'][$k]['id']);
					unset($data['CcConfigTable'][$i]['CcConfigAction'][$k]['cc_config_table_id']);
					foreach($action['CcConfigFielddefinition'] as $m => $fielddef) {
						unset($data['CcConfigTable'][$i]['CcConfigAction'][$k]['CcConfigFielddefinition'][$m]['id']);
						unset($data['CcConfigTable'][$i]['CcConfigAction'][$k]['CcConfigFielddefinition'][$m]['cc_config_table_id']);
						unset($data['CcConfigTable'][$i]['CcConfigAction'][$k]['CcConfigFielddefinition'][$m]['cc_config_action_id']);
					}
				}
			}
			
			$this->saveAll($data, array(
				'validate' => false,
				'deep' => true
			));
		}
	}
	*/
}
?>