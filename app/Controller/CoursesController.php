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
		
		$this->Auth->allow(array('index', 'view', 'reset_filter', 'statistic', 'approve'));
		
		if($this->Auth->user()) {
			$this->Auth->allow(array('edit', 'add', 'delete', 'revalidate', 'unpublish'));
		}
	}



	public function statistic() {
		$count = $this->Course->find('count', array(
			'conditions' => array(
				'Course.active' => 1,
				'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod'))
			)
		));

		$institutionsList = $this->Course->Institution->find('list');
		$institutions = array();
		foreach($institutionsList as $id => $label) {
			$c = $this->Course->getCount('institution_id', $id);
			if($c > 0)
				$institutions[] = array(
					'label' => $label,
					'count' => $c
				);
		}
		uasort($institutions, array('CoursesController', '_cmp'));

		$countriesList = $this->Course->Country->find('list');
		$countries = array();
		foreach($countriesList as $id => $label) {
			$c = $this->Course->getCount('country_id', $id);
			if($c > 0)
				$countries[] = array(
					'label' => $label,
					'count' => $c
				);
		}
		uasort($countries, array('CoursesController', '_cmp'));
		
		$disciplinesList = $this->Course->Discipline->find('list');
		$disciplines = array();
		foreach($disciplinesList as $id => $label) {
			$c = $this->Course->getCount('CoursesDiscipline.discipline_id', $id);
			if($c > 0)
				$disciplines[] = array(
					'label' => $label,
					'count' => $c
				);
		}
		uasort($disciplines, array('CoursesController', '_cmp'));
		
		$techniquesList = $this->Course->TadirahTechnique->find('list');
		$techniques = array();
		foreach($techniquesList as $id => $label) {
            $c = $this->Course->getCount('CoursesTadirahTechnique.tadirah_technique_id', $id);
			if($c > 0)
				$techniques[] = array(
					'label' => $label,
					'count' => $c
				);
		}
		uasort($techniques, array('CoursesController', '_cmp'));
		
		$objectsList = $this->Course->TadirahObject->find('list');
		$objects = array();
		foreach($objectsList as $id => $label) {
            $c = $this->Course->getCount('CoursesTadirahObject.tadirah_object_id', $id);
			if($c > 0)
				$objects[] = array(
					'label' => $label,
					'count' => $c
				);
		}
		uasort($objects, array('CoursesController', '_cmp'));
		
		$this->set(compact(
			'count',
			'institutions',
			'countries',
			'disciplines',
			'techniques',
			'objects'
		));
	}
	
	private static function _cmp($a, $b) {
		if ($a['count'] == $b['count']) return 0;
		return ($a['count'] < $b['count']) ? 1 : -1;
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
		if(	isset($this->request->params['ext']) 
		AND	in_array($this->request->params['ext'], array('json', 'xml'))) {
			// remove sensible data first
			if($courses) foreach($courses as &$course) {
				unset($course['AppUser']);
			}
			$this->set('_serialize', array('courses'));
		}
		$this->set(compact('courses'));
    }
	
	
	public function view($id = null) {
		if(empty($id)) $this->redirect('index');
		$course = $this->Course->find('first', array(
			'conditions' => array(
				'Course.id' => $id,
				'Course.deleted' => false)
		));
		if(empty($course)) {
			$this->Flash->set('The course could not be found.');
			$this->redirect(array(
				'controller' => 'users',
				'action' => 'dashboard'
			));
		}
		if(	$this->Auth->user('id') == $course['Course']['user_id']
		||	$this->Auth->user('is_admin'))
			$this->set('edit', true);
		
			
		if(	isset($this->request->params['ext'])
		AND	in_array($this->request->params['ext'], array('json', 'xml'))) {
			// remove sensible data first
			unset($course['AppUser']);
			$this->set('_serialize', array('course'));
		}
		$this->set(compact('course'));
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
			$this->Flash->set('The record has been revalidated with the current timestamp: '
					. $this->Course->data['Course']['updated']);
		}
		
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
	}


	public function approve($token = null) {
       // admins retrieve a link in their notification email to approve directly
		$course = $this->Course->find('first', array(
			'contain' => array(),
			'conditions' => array(
				'Course.approval_token' => $token,
				'Course.approved' => 0
			)
		));

        if($course) {
			$course['Course']['approved'] = true;
            $course['Course']['approval_token'] = null;
            $this->Course->save($course, array('validate' => false));
            $this->Flash->set('The record has been marked as approved!');
		}else{
			$this->Flash->set('The record was already approved.');
		}
		if($this->Auth->user()) $this->redirect('/users/dashboard');
		else $this->redirect('/');
	}


	public function unpublish($id = null) {
        if(empty($id)) $this->redirect(array(
            'controller' => 'users',
            'action' => 'dashboard'
        ));

        $conditions = array('Course.id' => $id);
		if($this->Auth->user('user_role_id') >= 3)
			$conditions['Course.user_id'] = $this->Auth->user('id');

        $this->Course->updateAll(
        	array(
        		'Course.active' => false,
				'Course.mod_mailed' => false,
				'Course.approved' => false,
				'Course.approval_token' => null
			),
			$conditions
		);

        $this->Flash->set('The record has been unpublished');

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
		$conditions = array(
			'Course.id' => $id,
			'Course.deleted' => false);
		if($this->Auth->user('is_admin') OR $this->Auth->user('user_role_id') < 3) $admin = true;
		else $conditions['Course.user_id'] = $this->Auth->user('id');
		
		// check autorisation beforehand
		$course = $this->Course->find('first', array('conditions' => $conditions));
		if(empty($course)) {
			$this->Flash->set('The course could not be found.');
			$this->redirect(array(
				'controller' => 'users',
				'action' => 'dashboard'
			));
		}
		
		if(!empty($this->request->data['Course'])) {
			// check the ID has been autorized correctly
			if(!$admin) {
				$this->request->data['Course']['user_id'] = $this->Auth->user('id');
				unset($this->request->data['Course']['created']);
				unset($this->request->data['Course']['updated']);
			}else{
				// admin only: do not change the updated timestamp if requested (update is not a typo)
				if(empty($this->request->data['Course']['update'])) {
					$this->request->data['Course']['updated'] = $course['Course']['updated'];
				}
			}
			$this->request->data['Course']['id'] = $id;
			
			if(!empty($this->request->data['Course']['skip_info_validation'])) {
				$this->request->data['Course']['skip_info_url'] = date('Y-m-d H:i:s');
				$this->Course->validator()->remove('info_url', 'status_ok');
			}
			if(!empty($this->request->data['Course']['skip_guide_validation'])) {
				$this->request->data['Course']['skip_guide_url'] = date('Y-m-d H:i:s');
				$this->Course->validator()->remove('guide_url', 'status_ok');
			}
			// do not revalidate URL, if skip is already set
			if($course['Course']['skip_info_url'] > date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
				$this->Course->validator()->remove('info_url', 'status_ok');
			if($course['Course']['skip_guide_url'] > date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
				$this->Course->validator()->remove('guide_url', 'status_ok');
			
			if($this->Course->validateAll($this->request->data)) {
				$this->request->data = $this->Course->data;		// callback beforeValidate manipulates data
				$this->request->data['Course']['id'] = $id;		// we need to set this again...
				if($this->Course->saveAll($this->request->data, array('validate' => false, 'deep' => true))) {
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
			if(!empty($this->request->data['Course']['skip_info_validation'])) {
				$this->request->data['Course']['skip_info_url'] = date('Y-m-d H:i:s');
				$this->Course->validator()->remove('info_url', 'status_ok');
			}
			if(!empty($this->request->data['Course']['skip_guide_validation'])) {
				$this->request->data['Course']['skip_guide_url'] = date('Y-m-d H:i:s');
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
		if(!$this->Auth->user('is_admin') AND $this->Auth->user('user_role_id') > 2)
			$conditions['Course.user_id'] = $this->Auth->user('id');
		
		$course = $this->Course->find('first', array(
			'conditions' => $conditions,
			'contain' => array()));
		
		if(empty($course)) $this->redirect(array(
			'controller' => 'users',
			'action' => 'dashboard'
		));
		
		if(	!empty($this->request->data)
		AND	!empty($this->request->data['Course']['deletion_reason_id'])) {
			
			if($this->request->data['Course']['deletion_reason_id'] >= 4) {
				$message = 'The course has been moved to archive';
				$this->Course->id = $id;
				$this->Course->save(array(
					'deleted' => true,
					'deletion_reason_id' => $this->request->data['Course']['deletion_reason_id']
				), array('validate' => false));
			}else{
				$message = 'The course has been deleted.';
				$this->Course->delete($id, $cascade = true);
			}
			
			$this->Flash->set($message);
			
			$this->redirect(array(
				'controller' => 'users',
				'action' => 'dashboard'
			));
			
		}elseif(!empty($this->request->data)) {
			$message = 'You must provide a reason for deleting this course.';
			$this->Flash->set($message);
		}
		
		$this->request->data = $course;
		$deletionReasons = $this->Course->DeletionReason->find('list');
		$this->set(compact('deletionReasons'));
		// render form
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
			'fields' => array('Institution.id', 'Institution.name', 'Country.name')
		));
		ksort($institutions);
		
		// this is needed to inherit geo-locations from selected institution to course in form
		$institutionsLocations = $this->Course->Institution->find('all', array(
			'contain' => array(),
			'fields' => array('Institution.id', 'Institution.lon', 'Institution.lat')
		));
		$locations = array();
		foreach($institutionsLocations as $record)
			$locations[$record['Institution']['id']] = $record['Institution'];
		$languages = $this->Course->Language->find('list');
		$courseTypes = $this->Course->CourseType->find('list', array(
			'contain' => array('CourseParentType'),
			'fields' => array('CourseType.id','CourseType.name','CourseParentType.name')
		));
		$courseDurationUnits = $this->Course->CourseDurationUnit->find('list');
		
		$this->_setTaxonomy();
		
		$this->set(compact(
			'users',
			'institutions',
			'locations',
			'languages',
			'courseTypes',
			'admin',
			'courseDurationUnits'
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
		$filter['Course.deleted'] = false;
		$filter['Course.updated >'] = date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod'));
		return $filter;
	}
	
	
	protected function _namedFilters() {
		// get filters from named URL parameters
		if(!$named = $this->request->named) {
			// do some sanitization, prevent SQL injection on the filter keys - Cake takes care of escaping the filter values
			$namedKeys = preg_replace('/[^a-zA-Z0-9_-]/', '', array_keys($named));
			$columns = $this->Course->schema();
			foreach($namedKeys as $namedField) {
				// if it's not a valid field name after the replacement any more:
				if(!isset($named[$namedField])) continue;
				// don't pull in the pagination sort keys
				if(in_array(strtolower($namedField), array('sort','direction'))) continue;
				// if a named parameter is present, check if it is a valid fieldname
				if(isset($columns[$namedField])) {
                    $this->filter['Course.' . $namedField] = $named[$namedField];
                }else{
                    // the HABTM filters
                    if($namedField == 'tadirah_object_id') {
                        $this->filter['CoursesTadirahObject.tadirah_object_id'][] = $named[$namedField];
                        //$this->request->data['TadirahObject']['TadirahObject'] = $named[$namedField];
                    }
                    elseif($namedField == 'tadirah_technique_id') {
                        $this->filter['CoursesTadirahTechnique.tadirah_technique_id'][] = $named[$namedField];
                        //$this->request->data['TadirahTechnique']['TadirahTechnique'] = $named[$namedField];
                    }
                    elseif($namedField == 'discipline_id') {
                       	$this->filter['CoursesDiscipline.discipline_id'][] = $named[$namedField];
                        //$this->request->data['Discipline']['Discipline'] = $named[$namedField];
                    }
				}
			}
			
			if(isset($named['country'])) {
				if(!ctype_digit($named['country'])) {
					$value = $this->Course->Country->field('id', array('Country.name' => $named['country']));
				}
				$this->filter['Course.country_id'] = $value;
				unset($this->filter['Course.country']);
			}
		}
		$this->request->named = null;
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
				
				if(empty($form['course_type_id'])) unset($this->filter['Course.course_type_id']);
				else $this->filter['Course.course_type_id'] = $form['course_type_id'];
			}
			
			// the HABTM filters
			if(!empty($this->request->data['TadirahObject'])) {
				$this->filter['CoursesTadirahObject.tadirah_object_id'] = $this->request->data['TadirahObject'];
			}else{
				unset($this->filter['CoursesTadirahObject.tadirah_object_id']);
			}
			
			if(!empty($this->request->data['TadirahTechnique'])) {
				$this->filter['CoursesTadirahTechnique.tadirah_technique_id'] = $this->request->data['TadirahTechnique'];
			}else{
				unset($this->filter['CoursesTadirahTechnique.tadirah_technique_id']);
			}
			
			if(!empty($this->request->data['Discipline'])) {
				$this->filter['CoursesDiscipline.discipline_id'] = $this->request->data['Discipline'];
			}else{
				unset($this->filter['CoursesDiscipline.discipline_id']);
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
		$order = $this->Course->order;
		$this->Course->order = null;
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
		// reset ordering
		$this->Course->order = $order;
	}
	
	protected function _getFilterOptions_validateFilters() {
		// filter logic: if minor doesn't fit major, remove minor from filter
		// get option lists for the filter
		$courseTypes = $this->Course->CourseType->find('list');
		foreach($courseTypes as $k => &$v) {
			$v .= ' ('.$this->Course->getCount('course_type_id', $k).')';
		}
		unset($v);

		if(!empty($this->filter['Course.course_type_id']) AND !isset($courseTypes[$this->filter['Course.course_type_id']]))
			unset($this->filter['Course.course_type_id']);
		
		$countries = $this->Course->Country->find('list');
		foreach($countries as $k => &$v) {
            $c = $this->Course->getCount('country_id', $k);
			$v .= ' ('.$c.')';
			if($c == 0) unset($countries[$k]);
		}
		unset($v);

		$conditions = (empty($this->filter['Course.country_id']))
			? array()
			: array('City.country_id' => $this->filter['Course.country_id']);
		$cities = $this->Course->City->find('list', array('conditions' => $conditions));
		if(!empty($this->filter['Course.city_id']) AND !isset($cities[$this->filter['Course.city_id']]))
			unset($this->filter['Course.city_id']);

		// make a structured list
		$cities = $this->Course->City->find('list', array(
			'contain' => array('Country'),
			'fields' => array('City.id', 'City.name', 'Country.name'),
			'conditions' => $conditions
		));
        ksort($cities);
		foreach($cities as $country => &$cs) {
            foreach ($cs as $k => &$v) {
            	$c = $this->Course->getCount('city_id', $k);
                $v .= ' ('.$c.')';
            	if($c == 0) unset($cities[$country][$k]);
			}
			if(empty($cities[$country])) unset($cities[$country]);
            unset($v);
		}
		unset($cs);

		
		// filter logic 2 - avoid redundant conditions
		if(!empty($this->filter['Course.city_id'])) unset($this->filter['Course.country_id']);
		if(!empty($this->filter['Course.course_type_id'])) unset($this->filter['Course.course_parent_type_id']);
		
		// child of country & city: university
		$conditions = array();
		if(!empty($this->filter['Course.country_id']))
			$conditions['Institution.country_id'] = $this->filter['Course.country_id'];
		if(!empty($this->filter['Course.city_id']))
			$conditions['Institution.city_id'] = $this->filter['Course.city_id'];
		$institutions = $this->Course->Institution->find('list', array('conditions' => $conditions));
		// filter logic 1
		if(!empty($this->filter['Course.institution_id']) AND !isset($institutions[$this->filter['Course.institution_id']]))
			unset($this->filter['Course.institution_id']);


		$institutions = $this->Course->Institution->find('list', array(
			'contain' => array('Country'),
			'fields' => array('Institution.id', 'Institution.name', 'Country.name'),
			'conditions' => $conditions
		));
		ksort($institutions);
        foreach($institutions as $country => &$is) {
            foreach ($is as $k => &$v) {
                $c = $this->Course->getCount('institution_id', $k);
                $v .= ' ('.$c.')';
            	if($c == 0) unset($institutions[$country][$k]);
            }
            unset($v);
            if(empty($institutions[$country])) unset($institutions[$country]);
        }
        unset($is);

		// filter logic 2
		if(!empty($this->filter['Course.institution_id'])) {
			unset($this->filter['Course.country_id']);
			unset($this->filter['Course.city_id']);
		}


        $tadirahObjects = $this->Course->TadirahObject->find('all', array(
        	'contain' => array(),
			'order' => 'TadirahObject.name ASC'));
        $tadirahTechniques = $this->Course->TadirahTechnique->find('all', array(
        	'contain' => array(),
			'order' => 'TadirahTechnique.name ASC'));
        $disciplines = $this->Course->Discipline->find('all', array(
            'contain' => array(),
            'order' => 'Discipline.name ASC'
        ));
        foreach($disciplines as $k => &$v) {
            $id = $v['Discipline']['id'];
            $c = $this->Course->getCount('CoursesDiscipline.discipline_id', $id);
            $v['Discipline']['name'] .= ' ('.$c.')';
            if($c == 0) unset($disciplines[$k]);
		}
        unset($v);
        foreach($tadirahObjects as $k => &$v) {
            $id = $v['TadirahObject']['id'];
        	$c = $this->Course->getCount('CoursesTadirahObject.tadirah_object_id', $id);
            $v['TadirahObject']['name'] .= ' ('.$c.')';
            if($c == 0) unset($tadirahObjects[$k]);
        }
        unset($v);
        foreach($tadirahTechniques as $k => &$v) {
            $id = $v['TadirahTechnique']['id'];
        	$c = $this->Course->getCount('CoursesTadirahTechnique.tadirah_technique_id', $id);
            $v['TadirahTechnique']['name'] .= ' ('.$c.')';
            if($c == 0) unset($tadirahTechniques[$k]);
        }
        unset($v);

		// set all option lists to view
		$this->set(compact(
			'countries',
			'cities',
			'courseTypes',
			'institutions',
			'tadirahObjects',
			'tadirahTechniques',
			'disciplines'
		));
	}
	
	protected function _setTaxonomy() {
		$tadirahObjects = $this->Course->TadirahObject->find('all', array(
			'contain' => array(),
			'order' => 'TadirahObject.name ASC'));
		$tadirahTechniques = $this->Course->TadirahTechnique->find('all', array(
			'contain' => array(),
			'order' => 'TadirahTechnique.name ASC'));
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
