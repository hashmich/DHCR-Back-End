<?php
if(!empty($title_for_layout)) echo '<h2>'.$title_for_layout.'</h2>';
echo $this->element('layout/actions', array(), array('plugin' => 'Cakeclient'));

echo $this->element('relations/parent_models', array(), array('plugin' => 'Cakeclient'));
echo $this->element('relations/child_models', array(), array('plugin' => 'Cakeclient'));
echo $this->element('relations/habtm_models', array(), array('plugin' => 'Cakeclient'));

echo $this->element('crud/view', array(), array('plugin' => 'Cakeclient'));
?>