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

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	var $actsAs = array(
		'Containable'
	);
	
	
	public function beforeSave($options = array()) {
		if(!empty($this->data[$this->alias])) {
			foreach($this->data[$this->alias] as $field => &$value) {
				if(!is_array($value)) $value = trim(strip_tags($value));
			}
		}
		
		return true;
	}


    public function generateToken($fieldname = null, $length = 16) {
        $time = substr((string)time(), -6, 6);
        $possible = '0123456789abcdefghijklmnopqrstuvwxyz';
        // create an unique token
        for($c = 1; $c > 0; ) {
            $token = '';
            for($i = 0; $i < $length - 6; $i++) {
                $token .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            }
            $token = $time . $token;
            if(empty($fieldname)) break;
            $c = $this->find('count', array('conditions' => array(
                $this->alias . '.' . $fieldname => $token
            )));
        }
        return $token;
    }
	
	
}
