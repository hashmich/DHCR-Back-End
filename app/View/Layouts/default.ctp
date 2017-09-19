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

$this->extend('/Layouts/base');

$DODH = false;

$this->start('header');
?>
<div id="header">
	<?php
	$logo1 = array(
		'file' => 'clarin-small.png',
		'alt' => 'CLARIN Logo',
		'url' => 'https://www.clarin.eu/',
		'width' => 67,
		'height' => 55,
		'style' => 'margin-left: 25px;'
	);
	$logo2 = array(
		'file' => 'dariah-small.png',
		'alt' => 'DARIAH Logo',
		'url' => 'http://dariah.eu/',
		'width' => 133,
		'height' => 55,
		'style' => 'margin-left: 10px;'
	);
	
	$file = '/img/logos/' . $logo1['file'];
	$url = $logo1['url'];
	unset($logo1['file']);
	unset($logo1['url']);
	echo $this->Html->link($this->Html->image($file, $logo1), $url, array(
			'target' => '_blank',
			'escape' => false));
	
	$file = '/img/logos/' . $logo2['file'];
	$url = $logo2['url'];
	unset($logo2['url']);
	unset($logo2['file']);
	echo $this->Html->link($this->Html->image($file, $logo2), $url, array(
			'target' => '_blank',
			'escape' => false));
	
	
	?>
	<div>
		<h1>
			<?php
			$title1 = 'Digital Humanities Registry';
			$title = $this->fetch('title');
			if(!empty($title) AND $title == 'Courses') {
				$title1 = 'Digital Humanities Registry - Courses';
				$title = null;
			}
			echo $this->Html->link($title1, '/');
			if(!empty($title)) echo ' - ' . $title;
			?>
		</h1>
		
		<p>
			Courseregistry <strong>2.1</strong> |
			<?php echo $this->Html->link('About', '/pages/about'); ?>
		</p>
			
	</div>
</div>
<?php
$this->end();

// pass content to parent view
echo $this->fetch('content');
?>
