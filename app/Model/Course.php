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

App::uses('AppModel', 'Model');
/**
 * Course Model
 *
 * @property Country $Country
 * @property City $City
 * @property Institution $Institution
 * @property ParentType $ParentType
 * @property Type $Type
 * @property Language $Language
 */
class Course extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	
	public $order = 'Course.updated DESC';
	
	
	private $maxHttpCode = 400;
	
	
	

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide a meaningful name for your course.',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'institution_id' => array(
			'from_list' => array(
				'rule' => array('checkList', 'Institution'),
				'message' => 'Only the provided options are allowed.',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'course_type_id' => array(
			'from_list' => array(
				'rule' => array('checkList', 'CourseType'),
				'message' => 'Only the provided options are allowed.',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'language_id' => array(
			'from_list' => array(
				'rule' => array('checkList', 'Language'),
				'message' => 'Only the provided options are allowed.',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'info_url' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide an URL of further information on the course.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			),
			'url' => array(
				'rule' => array('urlFormat'),
				'message' => 'URLs must begin with "http://" or "https://".'
			),
			'status_ok' => array(
				'rule' => array('urlCheckStatus'),
				'message' => 'The http server response code of the provided URL is not okay.'
			)
		),
		'guide_url' => array(
			'url' => array(
				'rule' => array('urlFormat'),
				'message' => 'URLs must begin with "http://" or "https://".',
				'allowEmpty' => true,
				'required' => false
			),
			'status_ok' => array(
				'rule' => array('urlCheckStatus'),
				'message' => 'The http server response code of the provided URL is not okay.'
			)
		),
		'start_date' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter one or many start dates of the course.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			),
			'multi_date' => array(
				'rule' => 'multiDate',
				'message' => 'Dates must follow the format YYYY-MM-DD, many dates separated by ";".'
			)
		),
		'recurring' => array(
			'bool' => array(
				'rule' => 'boolean',
				'message' => 'Only the provided options are allowed.'
			)
		),
		'ects' => array(
			'decimal' => array(
				'rule' => array('decimal'),
				'message' => 'Only numbers and floats.',
				'allowEmpty' => true,
				'required' => false
			),
		),
		'contact_mail' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'This address is not valid.',
				'allowEmpty' => true,
				'required' => false
			),
		),
		'lon' => array(
			'notEmpty' => array(
				'rule' => array('notBlank')
			)/*
			'decimal' => array(
				'rule' => array('decimal', 6),
				'message' => 'Enter a decimal with 6 digits after the decimal point.',
				'allowEmpty' => false,
				'required' => true
			),
			'range' => array(
				'rule' => array('range', -180, 180),
				'message' => 'This does not look not like a proper coordinate.'
			)*/
		),
		'lat' => array(
			'notEmpty' => array(
				'rule' => array('notBlank')
			)/*
			'decimal' => array(
				'rule' => array('decimal', 6),
				'message' => 'Enter a decimal with 6 digits after the decimal point.',
				'allowEmpty' => false,
				'required' => true
			),
			'range' => array(
				'rule' => array('range', -90, 90),
				'message' => 'This does not look not like a proper coordinate.'
			)*/
		),
		'department' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter the name of the department.',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'user_id' => array(
			'from_list' => array(
				'rule' => array('checkList', 'AppUser'),
				'message' => 'Choose an owner from the list. Add a new user if necessary.',
				'allowEmpty' => false,
				'required' => true
			),
		)
	);
	
	
	public function beforeSave($options = array()) {
		$lon = $this->data['Course']['lon'];
		$lat = $this->data['Course']['lat'];
	
		$lon = substr($lon, 0, strpos($lon, '.') + 7);
		$lat = substr($lat, 0, strpos($lat, '.') + 7);
	
		$this->data['Course']['lon'] = floatval($lon);
		$this->data['Course']['lat'] = floatval($lat);
	
		return true;
	}

	// custom validation
	public function checkList($check, $listModel = null) {
		if(empty($listModel) OR !isset($this->{$listModel})) return false;
		$list = $this->{$listModel}->find('list');
		$keyname = Inflector::underscore($listModel) . '_id';
		if($listModel == 'AppUser') $keyname = 'user_id';
		if(	!empty($this->data[$this->alias][$keyname])
		AND	isset($list[$this->data[$this->alias][$keyname]])
		) return true;
		return false;
	}
	
	
	public function multiDate($check) {
		$check = explode(';', $check[key($check)]);
		foreach($check as $k => $date) {
			if(!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date))
				return false;
		}
		return true;
	}
	
	
	public function urlFormat($check) {
		$url = (is_array($check)) ? $check[key($check)] : $check;
		if(is_string($url)) {
			if(strpos($url, 'http://') === 0) return true;
			if(strpos($url, 'https://') === 0) return true;
		}
		return false;
	}
	
	
	public function urlCheckStatus($check) {
		$url = (is_array($check)) ? $check[key($check)] : $check;
		$status = $this->http_status($url);
		if(!empty($status) AND $status < $this->maxHttpCode) {
			return true;
		}
		elseif(!empty($status)) {
			$this->validator()
			->getField(key($check))->getRule('status_ok')
			->message = 'The server response code of the provided URL is not okay. HTTP status code: '.$status;
		}
		
		return false;
	}
	
	
	private function http_status($url){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HEADER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0');
	    curl_exec($ch);
	    
	    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    return $status;
	}
	
	
	

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id'
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id'
		),
		'Institution' => array(
			'className' => 'Institution',
			'foreignKey' => 'institution_id',
			'counterCache' => true
		),
		'CourseParentType' => array(
			'className' => 'CourseParentType',
			'foreignKey' => 'course_parent_type_id'
		),
		'CourseType' => array(
			'className' => 'CourseType',
			'foreignKey' => 'course_type_id'
		),
		'Language' => array(
			'className' => 'Language',
			'foreignKey' => 'language_id'
		),
		'AppUser' => array(
			'className' => 'AppUser',
			'foreignKey' => 'user_id'
		)
	);
	
	
/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'TadirahTechnique' => array(
			'className' => 'TadirahTechnique',
			'joinTable' => 'courses_tadirah_techniques',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'tadirah_technique_id',
			'unique' => 'keepExisting'
		),
		'TadirahObject' => array(
			'className' => 'TadirahObject',
			'joinTable' => 'courses_tadirah_objects',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'tadirah_object_id',
			'unique' => 'keepExisting'
		),
		'Discipline' => array(
			'className' => 'Discipline',
			'joinTable' => 'courses_disciplines',
			'foreignKey' => 'course_id',
			'associationForeignKey' => 'discipline_id',
			'unique' => 'keepExisting'
		)
	);
	
	
	
	
	public function beforeValidate($options = array()) {
		unset($this->data['Course']['course_parent_type_id']);
		unset($this->data['Course']['city_id']);
		unset($this->data['Course']['country_id']);
		if(!empty($this->data['Course']['course_type_id'])) {
			$this->data['Course']['course_parent_type_id'] = $this->CourseType->field('course_parent_type_id', array(
				'CourseType.id' => $this->data['Course']['course_type_id']));
		}
		if(!empty($this->data['Course']['institution_id'])) {
			$university = $this->Institution->find('first', array(
				'contain' => array(),
				'conditions' => array('Institution.id' => $this->data['Course']['institution_id'])
			));
			$this->data['Course']['city_id'] = $university['Institution']['city_id'];
			$this->data['Course']['country_id'] = $university['Institution']['country_id'];
		}
		if(!empty($this->data['Course']['start_date'])) {
			$dates = trim($this->data['Course']['start_date']);
			$dates = explode(';', $dates);
			if(isset($dates[1])) {
				foreach($dates as $k => &$date) {
					$date = trim($date);
					if(empty($date)) unset($dates[$k]);
				}
			}
			$this->data['Course']['start_date'] = implode(';', $dates);
		}
		
		return true;
	}
	
	
	public function validateAll($data) {
		$this->Discipline->set($data);
		$errors1 = $this->Discipline->invalidFields();
		$this->TadirahTechnique->set($data);
		$errors2 = $this->TadirahTechnique->invalidFields();
		$this->TadirahObject->set($data);
		$errors3 = $this->TadirahObject->invalidFields();
		$errors = array_merge($errors1, $errors2, $errors3);
		$this->set($data);
		if($this->validates() AND empty($errors)) return true;
		$this->validationErrors = array_merge($this->validationErrors, $errors);
		return false;
	}
	
	
	public function checkUrls() {
		$courses = $this->find('all', array(
			'contain' => array('AppUser'),
			'conditions' => array(
				'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseExpirationPeriod')),
				'Course.active' => 1
			)
		));
		// collect email addresses of owners of courses with invalid URLs
		$collection = array();
		if(!empty($courses)) {
			$this->maxHttpCode = 300;
			foreach($courses as $k => $record) {
				$fieldlist = array();
				if($record['Course']['skip_info_url'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
					$fieldlist[] = 'info_url';
				if($record['Course']['skip_guide_url'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))
						$fieldlist[] = 'guide_url';
					
				$this->set($record);
				if(!$this->validates(array('fieldList' => $fieldlist))) {
					$errors = $this->validationErrors;
					if(!empty($record['AppUser']) AND !empty($record['AppUser']['email'])) {
						$email = $record['AppUser']['email'];
						$collection[$email][$record['Course']['id']] = $record;
						$collection[$email][$record['Course']['id']]['errors'] = $errors;
						$collection[$email]['maintainer'] = $record['AppUser']['name'];
					}else{
						//$email = 'no_owner';
						$mods = $this->AppUser->getModerators($record['Course']['country_id'], $user_admin = false);
						if($mods) {
							foreach($mods as $mod) {
								$email = $mod['AppUser']['email'];
								$collection[$email][$record['Course']['id']] = $record;
								$collection[$email][$record['Course']['id']]['errors'] = $errors;
								$collection[$email]['maintainer'] = $mod['AppUser']['name'];
							}
						}
					}
				}
			}
			
		}
		return $collection;
	}
	
	// collect email addresses of owners of courses that haven't updated their course for at least one year
	public function getReminderCollection() {
		$courses = $this->find('all', array(
			'contain' => array('AppUser'),
			'conditions' => array(
				'Course.updated <' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')),
				'Course.updated >' => date('Y-m-d H:i:s', time() - Configure::read('App.CourseArchivalPeriod')),
				'Course.active' => 1
			)
		));
		$collection = array();
		$userName = 'User';
		if(!empty($courses)) {
			foreach($courses as $k => $record) {
				if(!empty($record['AppUser']) AND !empty($record['AppUser']['email'])) {
					$email = $record['AppUser']['email'];
					$collection[$email][$record['Course']['id']] = $record;
					$collection[$email][$record['Course']['id']]['name'] = $record['Course']['name'];
					$collection[$email]['maintainer'] = $record['AppUser']['name'];
				}else{
					//$email = 'no_owner';
					$mods = $this->AppUser->getModerators($record['Course']['country_id'], $user_admin = false);
					if($mods) {
						
						foreach($mods as $mod) {
							$email = $mod['AppUser']['email'];
							$collection[$email][$record['Course']['id']] = $errors;
							$collection[$email][$record['Course']['id']]['name'] = $record['Course']['name'];
							$collection[$email]['maintainer'] = $mod['AppUser']['name'];
						}
					}
				}
			}
		}
		return $collection;
	}
	
	
	
	
	
	
	
	
	
}
