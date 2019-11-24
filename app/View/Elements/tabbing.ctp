
<?php
$items = array(
    array('DHCR Homepage', Configure::read('dhcr.baseUrl'))
);
if(empty($auth_user)) $items[] = array('Login', '/users/login');

$here = $this->params->here;
if(!empty($this->params->base)) {
    $here = substr_replace($this->params->here, '', 0, strlen($this->params->base));
}
?>


<div id="tabbing">
    <?php
    foreach($items as $item) {
		$classname = 'tab';
		if($here == $item[1]) $classname .= ' active';
        echo $this->Html->link($item[0], $item[1], ['class' => $classname]);
    }
    ?>
</div>