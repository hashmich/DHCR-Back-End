<?php
$menus = Configure::read('Cakeclient.topNav');
?>
<div id="cakeclient_top_nav">
	<?php
	foreach($menus as $menuName) {
		if(!empty($$menuName)) {
			$menu = $$menuName;
			echo $menu['label'];
			echo $this->element('layout/menu', array('menu' => $menu['list']), array('plugin' => 'Cakeclient'));
		}
	}
	?>
</div>