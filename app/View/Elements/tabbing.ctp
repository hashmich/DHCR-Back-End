
<?php
$items = array(
    array('Home', '/'),
    array('Students', '/pages/students'),
    array('Lecturers', '/pages/lecturers'),
    array('Downloads', '/pages/downloads'),
    //array('About', '/pages/about'),
    array('Contact', '/contact/us'),
    array('Statistics', '/statistic'),
    array('Login', '/users/login')
);

$here = $this->params->here;
if(!empty($this->params->base)) {
    $here = substr_replace($this->params->here, '', 0, strlen($this->params->base));
}
?>


<div id="tabbing">
    <?php
    foreach($items as $item) {
        echo '<a href="'.Router::url($item[1]).'" class="tab';
        if($here == $item[1]) echo ' active';
        echo '">'.$item[0].'</a>';
    }
    ?>
</div>