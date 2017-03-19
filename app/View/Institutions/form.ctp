<?php
unset($crudFieldlist['Institution.parent_id']);
unset($crudFieldlist['Institution.country_id']);
unset($crudFieldlist['Institution.course_count']);
$crudFieldlist['Institution.can_have_course']['formoptions']['type'] = 'hidden';

if(!empty($title_for_layout)) echo '<h2>'.$title_for_layout.'</h2>';

echo $this->element('Cakeclient.layout/actions', array());



if(isset($this->validationErrors[$modelName])) {
	if(	isset($this->validationErrors[$modelName]['lon'])
	OR	isset($this->validationErrors[$modelName]['lat'])) {
		unset($this->validationErrors[$modelName]['lon']);
		unset($this->validationErrors[$modelName]['lat']);
		$this->validationErrors[$modelName]['location'] = array("Please select a location");
	}
}
echo $this->element('Utils.validation_errors');

echo $this->element('Cakeclient.crud/form', array('crudFieldlist' => $crudFieldlist));


// picker is rendered using javascript, hiding the underlying lat,lon fields
echo $this->element('locationpicker');
?>





