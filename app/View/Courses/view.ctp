
<h2>Course Details</h2>

<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('list', array('controller' => 'courses', 'action' => 'index')); ?></li>
		
		<?php
		if(!empty($edit)) {
			echo '<li>';
			echo $this->Html->link('review', array(
					'controller' => 'courses',
					'action' => 'edit',
				$course['Course']['id']
			));
			echo '</li>';
			echo '<li>';
			echo $this->Html->link('revalidate', array(
					'controller' => 'courses',
					'action' => 'revalidate',
					$course['Course']['id']
			));
			echo '</li>';
		}
		?>
	</ul>
</div>


<dl>
<?php
$fieldlist = array(
		'Course.name' => array('label' => 'Course Name'),
		'CourseType.name' => array('label' => 'Course Type'),
		'Institution.name' => array('label' => 'Institution'),
		'Course.department' => array('label' => 'Department'),
		'Course.info_url' => array('label' => 'Information'),
		'Course.guide_url' => array('label' => 'Curriculum'),
		'Course.status' => array(),
		'Language.name' => array('label' => 'Course Language'),
		'Course.start_date' => array(),
		'Course.ects' => array('label' => 'ECTS'),
		'Course.lecturer' => array(),
		'Course.pid' => array('label' => 'Link to Detail Page'),
		'Course.access_requirements' => array(),
		'Course.description' => array(),
		'Course.keywords' => array(),
);

foreach($fieldlist as $key => $options) {
	$expl = explode('.', $key);
	$fieldModelName = $modelName;
	$fieldname = $expl[0];
	if(isset($expl[1])) {
		$fieldModelName = $expl[0];
		$fieldname = $expl[1];
	}
	
	$value = (!empty($course[$fieldModelName][$fieldname])) ? $course[$fieldModelName][$fieldname] : ' - ';
	
	$label = Inflector::humanize($fieldname);
	if(!empty($options['label'])) $label = $options['label'];
	
	
	
			
	
	echo '<dt>' . $label . '</dt>';
	
	switch($key) {
		case 'Course.info_url':
			if(trim($value) != '-') $value = $this->Html->link($value, $value);
			break;
		case 'Course.guide_url':
			if($course['Course']['info_url'] == $course['Course']['guide_url']) $value = '-';
		    if(trim($value) != '-') $value = $this->Html->link($value, $value);
			break;
		case 'Course.status':
			$value = 'record actively maintained';
			if($course['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseYellow'))) {
				$value = 'entry not revised since ' . round(Configure::read('App.CourseYellow')/(60*60*24*365), 1) . ' years';
			}
			if(	$course['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseRed'))
			OR	(!empty($edit) AND $course['Course']['updated'] < date('Y-m-d H:i:s', time() - Configure::read('App.CourseWarnPeriod')))) {
				$value = 'record not revised for more than ' . round(Configure::read('App.CourseRed')/(60*60*24*365), 1) . ' years';
			}
			break;
		case 'Course.start_date': 
			$value = (!empty($course['Course']['start_date'])) ? $course['Course']['start_date'] : ' - ';
			$value = explode(';', $value);
			if($course['Course']['recurring']) {
				foreach($value as &$date) {
					$date = trim($date);
				}
				if(!empty($value) AND $value[0] != ' - ') $value[] = 'recurring';
			}
			$value = implode('<br />', $value);
			break;
		case 'Course.lecturer':
			$value = $name = $mail = null;
			if(!empty($course['Course']['contact_mail'])) $mail = $name = $course['Course']['contact_mail'];
			if(!empty($course['Course']['contact_name'])) $name = $course['Course']['contact_name'];
			if(!empty($name) AND !empty($mail))
				$value = $this->Html->link($course['Course']['contact_name'], 'mailto:' . $course['Course']['contact_mail']);
				if(empty($mail) AND !empty($name)) $value = $name;
				$value = (!empty($value)) ? $value : ' - ';
			break;
		case 'Course.pid':
			$url = array(
					'controller' => 'courses',
					'action' => 'view',
					$course['Course']['id']);
			$value = $this->Html->link(Router::url($url, true), $url);
			break;
		case 'Course.keywords':
			$keywords = array();
			if(!empty($course['Discipline'])) {
				$cat = array();
				foreach($course['Discipline'] as $tag) $cat[] = trim($tag['name']);
				$keywords['Disciplines'] = $cat;
			}
			if(!empty($course['TadirahTechnique'])) {
				$cat = array();
				foreach($course['TadirahTechnique'] as $tag) $cat[] = trim($tag['name']);
				$keywords['Techniques'] = $cat;
			}
			if(!empty($course['TadirahObject'])) {
				$cat = array();
				foreach($course['TadirahObject'] as $tag) $cat[] = trim($tag['name']);
				$keywords['Objects'] = $cat;
			}
			if(!empty($keywords)) {
				$kwlist = array();
				foreach($keywords as $cat => $entries)
					$kwlist[] = '<u>'.$cat.'</u>: ' . implode(', ', $entries);
				$value = implode('<br />', $kwlist);
			}
	}
	
	echo '<dd>' . $value . '</dd>';
}


if(!empty($edit) AND !empty($auth_user) AND $auth_user['is_admin']) {
	?>
	<dt>Maintainer</dt>
	<dd>
		<?php
		if(empty($course['Course']['user_id'])) {
			echo 'No maintainer assigned';
		}else{
			echo $course['AppUser']['first_name'].' '.$course['AppUser']['last_name'].'<br>';
			echo $this->Html->link($course['AppUser']['email'], 'mailto:'.$course['AppUser']['email'], array(
				'target' => '_blank'));
				echo '<br>';
			echo $this->Html->link('View maintainer data', '/moderator/users/view/'.$course['Course']['user_id']);
		}
		?>
	</dd>
	<?php
}
?>
</dl>