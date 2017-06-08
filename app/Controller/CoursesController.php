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

class CoursesController extends AppController {
	
	
	public $filter = array();
	
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow(array('index', 'view', 'reset', 'statistic'));
		
		if($this->Auth->user()) {
			$this->Auth->allow(array('edit', 'add', 'delete', 'revalidate'));
		}
	}


	public function statistic() {
		$count = $this->Course->find('count', array(
			'conditions' => array(
				'Course.active' => 1,
				'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod'))
			)
		));

		$institutionsList = $this->Course->Institution->find('list', array(
			'conditions' => array('Institution.can_have_course' => true)
		));
		$institutions = array();
		foreach($institutionsList as $id => $label) {
			$c = $this->Course->find('count', array(
				'conditions' => array(
					'Course.active' => 1,
					'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod')),
					'Course.institution_id' => $id
				)
			));
			$institutions[] = array(
				'label' => $label,
				'count' => $c
			);
			//array_sort_by_key count
		}

		$countriesList = $this->Course->Country->find('list');
		$countries = array();
		foreach($countriesList as $id => $label) {
			$c = $this->Course->find('count', array(
				'conditions' => array(
					'Course.active' => 1,
					'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod')),
					'Course.country_id' => $id
				)
			));
			$countries[] = array(
				'label' => $label,
				'count' => $c
			);
			//array_sort_by_key count
		}
		
		$this->set(compact('count', 'institutions', 'countries'));
	}
	
	
	public function index() {
        // we need to keep this line in order to maintain the functionality of sorting 
        // with PaginatorHelper sort links
		$this->paginate();
		
		$findoptions = array('conditions' => $this->_getFilter());
		
		if(!empty($this->request->params['named'])) {
			$named = $this->request->params['named'];
			if(!empty($named['sort']) AND !empty($named['direction'])) 
				$findoptions['order'] = array($named['sort'] => $named['direction']);
		}
		
		$courses = $this->Course->find('all', $findoptions);
		
		if($this->Auth->user('is_admin')) $this->set('edit', true);
		
		// set results to view
		$this->set(compact('courses'));
    }
	
	
	public function view($id = null) {
		if(empty($id)) $this->redirect('index');
		$courses = $this->Course->find('all', array(
			'conditions' => array('Course.id' => $id)
		));
		if(	$this->Auth->user('id') == $courses[0]['Course']['user_id']
		||	$this->Auth->user('is_admin'))
			$this->set('edit', true);
		$this->set(compact('courses'));
		$this->render('index');
	}
	
	
	public function revalidate($id = null) {
		if(empty($id)) $this->redirect(array(
				'controller' => 'users',
				'action' => 'dashboard'
		));
		
		$admin = false;
		$conditions = array('Course.id' => $id);
		if($this->Auth->user('is_admin') OR $this->Auth->user('user_role_id') < 3) $admin = true;
		else $conditions['Course.user_id'] = $this->Auth->user('id');
		
		// check autorisation beforehand
		$course = $this->Course->find('first', array('conditions' => $conditions));
		
		if(!empty($course)) {
			// update timestamp
			unset($course['Course']['updated']);
			$this->Course->set($course);
			$this->Course->save();
			$this->Course->read();
			$this->Session->setFlash('The record has been revalidated with the current timestamp: '
					. $this->Course->data['Course']['updated']);
		}
		
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
	}
	
	
	public function edit($id = null) {
		if(empty($id)) $this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
		
		$admin = false;
		$conditions = array('Course.id' => $id);
		if($this->Auth->user('is_admin') OR $this->Auth->user('user_role_id') < 3) $admin = true;
		else $conditions['Course.user_id'] = $this->Auth->user('id');
		
		// check autorisation beforehand
		$course = $this->Course->find('first', array('conditions' => $conditions));
		if(empty($course)) $this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
		
		if(!empty($this->request->data['Course'])) {
			// check the ID has been autorized correctly
			$id = $this->Session->read('edit.Course.id');
			if(!$id) $this->redirect(array(
				'controller' => 'users',
				'action' => 'dashboard'
			));
			
			if(!$admin) {
				$this->request->data['Course']['user_id'] = $this->Auth->user('id');
				unset($this->request->data['Course']['created']);
				unset($this->request->data['Course']['updated']);
			}else{
				if(empty($this->request->data['Course']['update'])) {
					$this->request->data['Course']['updated'] = $course['Course']['updated'];
				}
			}
			$this->request->data['Course']['id'] = $id;
			if(!empty($this->request->data['Course']['skip_validation'])) {
				$this->Course->validator()->remove('url', 'status_ok');
				$this->Course->validator()->remove('guide_url', 'status_ok');
			}
			if($this->Course->validateAll($this->request->data)) {
				$this->request->data = $this->Course->data;		// callback beforeValidate manipulates data
				$this->request->data['Course']['id'] = $id;		// we need to set this again...
				if($this->Course->saveAll($this->request->data, array('validate' => false))) {
					$this->Session->delete('edit.Course.id');
					$this->redirect(array(
						'controller' => 'users',
						'action' => 'dashboard'
					));
				}
			}else{
				$this->set('errors', $this->Course->validationErrors);
			}
		}else{
			$this->request->data = $course;
			$this->Session->write('edit.Course.id', $id);
		}
		
		$this->_setOptions($admin);
		$this->render('form');
	}
	
	
	public function add() {
		$admin = ($this->Auth->user('is_admin') OR $this->Auth->user('user_role_id') < 3);
		if(!empty($this->request->data['Course'])) {
			if(!$admin) {
				$this->request->data['Course']['user_id'] = $this->Auth->user('id');
				unset($this->request->data['Course']['created']);
				unset($this->request->data['Course']['updated']);
			}
			if(!empty($this->request->data['Course']['skip_validation'])) {
				$this->Course->validator()->remove('url', 'status_ok');
				$this->Course->validator()->remove('guide_url', 'status_ok');
			}
			if($this->Course->validateAll($this->request->data)) {
				$this->request->data = $this->Course->data;		// callback beforeValidate manipulates data
				if($this->Course->saveAll($this->request->data, array('validate' => false))) {
					$this->redirect(array(
						'controller' => 'users',
						'action' => 'dashboard'
					));
				}
			}else{
				$this->set('errors', $this->Course->validationErrors);
			}
		}
		
		$this->_setOptions($admin);
		$this->render('form');
	}
	
	
	public function delete($id = null) {
		if(empty($id)) $this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
		
		$conditions = array('Course.id' => $id);
		if($this->Auth->user('is_admin') OR $this->Auth->user('user_role_id') < 3)
			$admin = true;
		else
			$conditions['Course.user_id'] = $this->Auth->user('id');
		
		$this->Course->deleteAll($conditions, $cascade = true);
		
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
	}
	
	
	protected function _setOptions($admin = false) {
		$users = array();
		if($admin) $rawUsers = $this->Course->AppUser->find('all', array(
			'contain' => array('Institution' => array('Country')),
			'conditions' => array(
				'AppUser.active' => 1
			),
			'order' => 'AppUser.last_name ASC'
		));
		if(!empty($rawUsers)) {
			$countries = array();
			foreach($rawUsers as $user) {
				$entry = array($user['AppUser']['id'] => $user['AppUser']['name']);
				if(empty($user['AppUser']['institution_id']) OR empty($user['Institution']['Country'])) {
					$users = $users + $entry;
				}else{
					$country = $user['Institution']['Country']['name'];
					if(isset($countries[$country])) $countries[$country] = $countries[$country] + $entry;
					else $countries[$country] = $entry;
				}
			}
			ksort($countries);
			$users = $users + $countries;
		}
		$institutions = $this->Course->Institution->find('list', array(
			'contain' => array('Country'),
			'fields' => array('Institution.id', 'Institution.name', 'Country.name'),
			'conditions' => array('Institution.can_have_course' => 1)
		));
		ksort($institutions);
		$institutionsLocations = $this->Course->Institution->find('all', array(
			'contain' => array(),
			'fields' => array('Institution.id', 'Institution.lon', 'Institution.lat'),
			'conditions' => array('Institution.can_have_course' => 1)
		));
		$locations = array();
		foreach($institutionsLocations as $record)
			$locations[$record['Institution']['id']] = $record['Institution'];
		$languages = $this->Course->Language->find('list');
		$courseTypes = $this->Course->CourseType->find('list', array(
			'contain' => array('CourseParentType'),
			'fields' => array('CourseType.id','CourseType.name','CourseParentType.name')
		));
		
		$this->_setTaxonomy();
		
		$this->set(compact(
			'users',
			'institutions',
			'locations',
			'languages',
			'courseTypes',
			'admin'
		));
	}
	
	
	protected function _setupFilter() {
		// check for previously set filters
		$this->filter = $this->Session->read('filter');
		// get/maintain filters
		$this->_postedFilters();
		$this->_getFilterOptions_validateFilters();
		
		$this->Session->write('filter', $this->filter);
		
		// don't store named and extended filters in the session, but set the named to the form!
		$this->_namedFilters();
		$this->_filterToForm();
		
		$this->_setJoins();
		
		return $this->filter;
	}
	
	
	protected function _getFilter() {
		$filter = $this->filter;
		// if the map is invoked by request action, then rebuild the filter
		if(empty($filter)) $filter = $this->_setupFilter();
		// set some filter properties that are NOT editable via the filter form - so $this->filter remains empty if no filter is set
		$filter['Course.active'] = 1;	// active will be used as an user-option to unpublish the record
		$filter['Course.updated >'] = date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod'));
		return $filter;
	}
	
	
	protected function _namedFilters() {
		// get filters from named URL parameters
		if(!empty($named = $this->request->named)) {
			// do some sanitization, prevent SQL injection on the filter keys - Cake takes care of escaping the filter values
			$namedKeys = preg_replace('/[^a-zA-Z0-9_-]/', '', array_keys($named));
			$columns = $this->Course->schema();
			foreach($namedKeys as $namedField) {
				if(!isset($named[$namedField])) continue;
				// don't pull in the pagination sort keys
				if(in_array(strtolower($namedField), array('sort','direction'))) continue;
				// if a named parameter is present, check if it is a valid fieldname
				if(isset($columns[$namedField]))
					$this->filter['Course.' . $namedField] = $named[$namedField];
			}
			
			if(isset($named['country'])) {
				if(!ctype_digit($named['country'])) {
					$value = $this->Course->Country->field('id', array('Country.name' => $named['country']));
				}
				$this->filter['Course.country_id'] = $named['country'];
				unset($this->filter['Course.country']);
			}
		}
	}
	
	// from ['Model']['field'] notation to Model.field notation...
	protected function _postedFilters() {
		// get filters from form data - mention all possible fields explicitly to avoid any trickery
		if(!empty($this->request->data)) {
			if(!empty($this->request->data['Course'])) {
				$form = $this->request->data['Course'];
				
				if(empty($form['country_id'])) unset($this->filter['Course.country_id']);
				else $this->filter['Course.country_id'] = $form['country_id'];
				
				if(empty($form['city_id'])) unset($this->filter['Course.city_id']);
				else $this->filter['Course.city_id'] = $form['city_id'];
				
				if(empty($form['institution_id'])) unset($this->filter['Course.institution_id']);
				else $this->filter['Course.institution_id'] = $form['institution_id'];
				
				if(empty($form['course_parent_type_id'])) unset($this->filter['Course.course_parent_type_id']);
				else $this->filter['Course.course_parent_type_id'] = $form['course_parent_type_id'];
				
				if(empty($form['course_type_id'])) unset($this->filter['Course.course_type_id']);
				else $this->filter['Course.course_type_id'] = $form['course_type_id'];
			}
			// the HABTM filters
			if(!empty($this->request->data['TadirahObject'])) {
				if(!empty($this->request->data['TadirahObject']['TadirahObject']))
					$this->filter['CoursesTadirahObject.tadirah_object_id'] = $this->request->data['TadirahObject']['TadirahObject'];
				else unset($this->filter['CoursesTadirahObject.tadirah_object_id']);
			}
			if(!empty($this->request->data['TadirahTechnique'])) {
				if(!empty($this->request->data['TadirahTechnique']['TadirahTechnique']))
					$this->filter['CoursesTadirahTechnique.tadirah_technique_id'] = $this->request->data['TadirahTechnique']['TadirahTechnique'];
				else unset($this->filter['CoursesTadirahTechnique.tadirah_technique_id']);
			}
			if(!empty($this->request->data['TadirahActivity'])) {
				if(!empty($this->request->data['TadirahActivity']['TadirahActivity']))
					$this->filter['CoursesTadirahActivity.tadirah_activity_id'] = $this->request->data['TadirahActivity']['TadirahActivity'];
				else unset($this->filter['CoursesTadirahActivity.tadirah_activity_id']);
			}
			if(!empty($this->request->data['Discipline'])) {
				if(!empty($this->request->data['Discipline']['Discipline']))
					$this->filter['CoursesDiscipline.discipline_id'] = $this->request->data['Discipline']['Discipline'];
				else unset($this->filter['CoursesDiscipline.discipline_id']);
			}
		}
	}
	
	// ... and back from Model.field notation to ['Model']['field'] notation
	protected function _filterToForm() {
		// bring the mangled filter variables back into the filter-form
		if(!empty($this->filter)) {
			foreach($this->filter as $key => $value) {
				$expl = explode('.', $key);
				$model = 'Course';
				$field = $expl[0];
				if(!empty($expl[1])) {
					$model = $expl[0];
					$field = $expl[1];
				}
				switch($model) {
					case 'CoursesTadirahObject':
						$model = $field = 'TadirahObject';
						break;
					case 'CoursesTadirahActivity':
						$model = $field = 'TadirahActivity';
						break;
					case 'CoursesTadirahTechnique':
						$model = $field = 'TadirahTechnique';
						break;
					case 'CoursesDiscipline':
						$model = $field = 'Discipline';
						break;
				}
				$this->request->data[$model][$field] = $value;
			}
		}
	}
	
	protected function _setJoins() {
		// set joins for HABTM queries during pagination
		if(!empty($this->filter['CoursesTadirahObject.tadirah_object_id'])) {
			$subquery = $this->Course->find('all', array(
				'joins' => array(
					array(
						'alias' => 'CoursesTadirahObject',
						'table' => 'courses_tadirah_objects',
						'type' => 'INNER',
						'conditions' => 'CoursesTadirahObject.course_id = Course.id'
					)
				),
				'conditions' => array(
					'CoursesTadirahObject.tadirah_object_id' => $this->filter['CoursesTadirahObject.tadirah_object_id']
				),
				'fields' => array('DISTINCT (CoursesTadirahObject.course_id) AS ids_filtered'),
				'contain' => array('CoursesTadirahObject')
			));
			$this->filter['Course.id'] = Set::extract('/CoursesTadirahObject/ids_filtered', $subquery);
			unset($this->filter['CoursesTadirahObject.tadirah_object_id']);
		}
		if(!empty($this->filter['CoursesTadirahTechnique.tadirah_technique_id'])) {
			$subquery = $this->Course->find('all', array(
				'joins' => array(
					array(
						'alias' => 'CoursesTadirahTechnique',
						'table' => 'courses_tadirah_techniques',
						'type' => 'INNER',
						'conditions' => 'CoursesTadirahTechnique.course_id = Course.id'
					)
				),
				'conditions' => array(
					'CoursesTadirahTechnique.tadirah_technique_id' => $this->filter['CoursesTadirahTechnique.tadirah_technique_id']
				),
				'fields' => array('DISTINCT (CoursesTadirahTechnique.course_id) AS ids_filtered'),
				'contain' => array('CoursesTadirahTechnique')
			));
			$this->filter['Course.id'] = Set::extract('/CoursesTadirahTechnique/ids_filtered', $subquery);
			unset($this->filter['CoursesTadirahTechnique.tadirah_technique_id']);
		}
		if(!empty($this->filter['CoursesTadirahActivity.tadirah_activity_id'])) {
			$subquery = $this->Course->find('all', array(
				'joins' => array(
					array(
						'alias' => 'CoursesTadirahActivity',
						'table' => 'courses_tadirah_activities',
						'type' => 'INNER',
						'conditions' => 'CoursesTadirahActivity.course_id = Course.id'
					)
				),
				'conditions' => array(
					'CoursesTadirahActivity.tadirah_activity_id' => $this->filter['CoursesTadirahActivity.tadirah_activity_id']
				),
				'fields' => array('DISTINCT (CoursesTadirahActivity.course_id) AS ids_filtered'),
				'contain' => array('CoursesTadirahActivity')
			));
			$this->filter['Course.id'] = Set::extract('/CoursesTadirahActivity/ids_filtered', $subquery);
			unset($this->filter['CoursesTadirahActivity.tadirah_activity_id']);
		}
		if(!empty($this->filter['CoursesDiscipline.discipline_id'])) {
			$subquery = $this->Course->find('all', array(
				'joins' => array(
					array(
						'alias' => 'CoursesDiscipline',
						'table' => 'courses_disciplines',
						'type' => 'INNER',
						'conditions' => 'CoursesDiscipline.course_id = Course.id'
					)
				),
				'conditions' => array(
					'CoursesDiscipline.discipline_id' => $this->filter['CoursesDiscipline.discipline_id']
				),
				'fields' => array('DISTINCT (CoursesDiscipline.course_id) AS ids_filtered'),
				'contain' => array('CoursesDiscipline')
			));
			$this->filter['Course.id'] = Set::extract('/CoursesDiscipline/ids_filtered', $subquery);
			unset($this->filter['CoursesDiscipline.discipline_id']);
		}
	}
	
	protected function _getFilterOptions_validateFilters() {
		// filter logic: if minor doesn't fit major, remove minor from filter
		// get option lists for the filter
		$courseParentTypes = $this->Course->CourseParentType->find('list');
		$conditions = (empty($this->filter['Course.course_parent_type_id'])) ? array() : array('CourseType.course_parent_type_id' => $this->filter['Course.course_parent_type_id']);
		$courseTypes = $this->Course->CourseType->find('list', array('conditions' => $conditions));
		if(!empty($this->filter['Course.course_type_id']) AND !isset($courseTypes[$this->filter['Course.course_type_id']])) unset($this->filter['Course.course_type_id']);
		$types = $this->Course->CourseType->find('list', array(
			'contain' => array('CourseParentType'),
			'fields' => array('CourseType.id', 'CourseType.name', 'CourseParentType.name'),
			'conditions' => $conditions
		));
		
		$countries = $this->Course->Country->find('list');
		$conditions = (empty($this->filter['Course.country_id'])) ? array() : array('City.country_id' => $this->filter['Course.country_id']);
		$cities = $this->Course->City->find('list', array('conditions' => $conditions));
		
		if(!empty($this->filter['Course.city_id']) AND !isset($cities[$this->filter['Course.city_id']])) unset($this->filter['Course.city_id']);
		// make a structured list
		$cities = $this->Course->City->find('list', array(
			'contain' => array('Country'),
			'fields' => array('City.id', 'City.name', 'Country.name'),
			'conditions' => $conditions
		));
		ksort($cities);
		
		// filter logic 2 - avoid redundant conditions
		if(!empty($this->filter['Course.city_id'])) unset($this->filter['Course.country_id']);
		if(!empty($this->filter['Course.course_type_id'])) unset($this->filter['Course.course_parent_type_id']);
		
		// child of country & city: university
		$conditions = array(
			'Institution.can_have_course' => true
		);
		if(!empty($this->filter['Course.country_id']))
			$conditions['Institution.country_id'] = $this->filter['Course.country_id'];
		if(!empty($this->filter['Course.city_id']))
			$conditions['Institution.city_id'] = $this->filter['Course.city_id'];
		$institutions = $this->Course->Institution->find('list', array('conditions' => $conditions));
		// filter logic 1
		if(!empty($this->filter['Course.institution_id']) AND !isset($institutions[$this->filter['Course.institution_id']])) unset($this->filter['Course.institution_id']);
		$institutions = $this->Course->Institution->find('list', array(
			'contain' => array('Country'),
			'fields' => array('Institution.id', 'Institution.name', 'Country.name'),
			'conditions' => $conditions
		));
		ksort($institutions);
		// filter logic 2
		if(!empty($this->filter['Course.institution_id'])) {
			unset($this->filter['Course.country_id']);
			unset($this->filter['Course.city_id']);
		}
		
		$this->_setTaxonomy();
		// set all option lists to view
		$this->set(compact(
			'countries',
			'cities',
			'courseParentTypes',
			'courseTypes',
			'institutions'
		));
	}
	
	protected function _setTaxonomy() {
		$tadirahObjects = $this->Course->TadirahObject->find('all', array('contain' => array()));
		$tadirahTechniques = $this->Course->TadirahTechnique->find('all', array('contain' => array()));
		$disciplines = $this->Course->Discipline->find('all', array(
			'contain' => array(),
			'order' => 'Discipline.name ASC'
		));
		
		$this->set(compact(
			'tadirahObjects',
			'tadirahTechniques',
			'disciplines'
		));
	}
	
	
	
}
?>
