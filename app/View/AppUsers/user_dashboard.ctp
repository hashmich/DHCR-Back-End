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
?>

 
<h2>Your Courses</h2>

<div class="actions">
	<ul>
		<li>
			<?php
			echo $this->Html->link('Add new Course', array(
				'controller' => 'courses',
				'action' => 'add'
			));
			?>
		</li>
	</ul>
</div>


<?php
echo $this->element('dashboard/shibboleth_link');


if(empty($courses)) {
	echo '<p>You have no courses in the registry, please add one :)</p>';
}else{
	$this->set('edit', true);	// displays the "Actions" column in all subsequent elements
	?>
    <p class="share-and-feature">
        Please mind contributing to the DHCR project by sharing your courses on social media
        or placing the DHCR-featured badge on institutional websites.
    </p>
    <?php
    echo $this->element('courses/index', ['varname' => 'yourCourses']);
}
?>


<?= $this->element('svg_icons') ?>


<?php
$this->Html->script(['sharing','modal'], ['inline' => false]);
$this->Html->scriptStart(array('inline' => false));

echo 'var BASE_URL = "' . Configure::read('dhcr.baseUrl') . '";';

$jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
if(!empty($courses))
	echo 'var yourCourses = ' . json_encode($courses, $jsonOptions). ';';

?>

$(document).ready( function() {
    let accordeon = new Accordeon('accordeon');
    $('.sharing.button').on('click', function(e) {
        e.preventDefault();
        let varname = $(e.target).attr('data-varname');
        let id = $(e.target).attr('data-id');
        let data = window[varname];
        new Sharing(data, id);
    });
});
<?php $this->Html->scriptEnd(); ?>



