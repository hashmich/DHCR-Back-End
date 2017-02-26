<?php
unset($crudFieldlist['Institution.parent_id']);
unset($crudFieldlist['Institution.country_id']);
unset($crudFieldlist['Institution.course_count']);
$crudFieldlist['Institution.can_have_course']['formoptions']['type'] = 'hidden';

if(!empty($title_for_layout)) echo '<h2>'.$title_for_layout.'</h2>';
echo $this->element('layout/actions', array(), array('plugin' => 'Cakeclient'));

echo $this->element('crud/form', array('crudFieldlist' => $crudFieldlist), array('plugin' => 'Cakeclient'));
?>