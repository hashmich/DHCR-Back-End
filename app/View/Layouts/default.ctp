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
	$logo = array(
		'file' => 'DARIAH-CLARIN-joint-logo.jpg',
		'alt' => 'CLARIN-DARIAIH joint Logo',
		'url' => '/',
		'width' => 115,
		'height' => 90
	);
	
	$file = '/img/logos/' . $logo['file'];
	$url = $logo['url'];
	unset($logo['file']);
	unset($logo['url']);
	echo $this->Html->link($this->Html->image($file, $logo), $url, array(
			'target' => '_blank',
			'escape' => false));

	
	
	?>
	<div>
		<h1>
			<a href="<?php echo Router::url('/'); ?>">
                <span id="h1">Digital Humanities</span><br>
                <span id="h2">Course</span><span id="h3">Registry</span>
            </a>
		</h1>
			
	</div>
</div>
<?php
$this->end();

// pass content to parent view
echo $this->fetch('content');
?>
