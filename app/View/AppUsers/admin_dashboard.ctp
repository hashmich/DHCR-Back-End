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
<h2>Moderation Dashboard</h2>

<div class="actions">
	<ul>
		<li>
			<?php
			echo $this->Html->link('Invite a lecturer or contributor', array(
				'controller' => 'users',
				'action' => 'invite',
				'plugin' => null
			));
			?>
		</li>
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

<?php echo $this->element('dashboard/shibboleth_link'); ?>


<div id="accordeon">
    <?php
	if(!empty($unapproved)) {
		?>
        <div class="accordeon-item" id="account-requests">
            <h3><span>New Account Requests</span></h3>
            <div class="item-content">
                <?php echo $this->element('dashboard/admin_account_requests'); ?>
            </div>
        </div>
		<?php
	}
	if(!empty($invited)) {
		?>
        <div class="accordeon-item" id="invited">
            <h3><span>Pending Invitations</span></h3>
            <div class="item-content">
                <div class="actions">
                    <ul>
                        <li>
                            <?php
                            echo $this->Html->link('Remind All!', array(
                                'controller' => 'users',
                                'action' => 'invite',
                                'plugin' => null,
                                'all'
                            ), array('confirm' => 'Confirm to send out an invitation reminder email to *ALL* users listed here.'));
                            ?>
                        </li>
                    </ul>
                </div>
                <?php echo $this->element('dashboard/admin_invited_users'); ?>
            </div>
        </div>
		<?php
	}
	if(!empty($new_courses)) {
        ?>
        <div class="accordeon-item" id="new-courses">
            <h3><span>New Courses</span></h3>
            <div class="item-content">
                <p>These courses have been published, but should be reviewed for meeting the DH Course Registry standards.</p>
                <?php
                $this->set('edit', true);	// displays the "Actions" column in all subsequent elements
                echo $this->element('courses/index', array('courses' => $new_courses, 'varname' => 'newCourses'));
                ?>
            </div>
        </div>
        <?php
    }
	if(!empty($moderated)) {
		?>
        <div class="accordeon-item" id="moderated">
            <h3><span>Moderated Courses</span></h3>
            <div class="item-content">
                <p>As a national moderator, you find an overview of courses in your country here.</p>
				<?php
                $this->set('edit', true);	// displays the "Actions" column in all subsequent elements
				echo $this->element('courses/index', array('courses' => $moderated, 'varname' => 'moderatedCourses'));
				?>
            </div>
        </div>
		<?php
	}
	if(!empty($courses)) {
		?>
        <div class="accordeon-item" id="your-courses">
            <h3><span>Your Courses</span></h3>
            <div class="item-content">
                <div class="share-and-feature">
					<?= $this->Html->image('dhcr-feature-badge-300.png', [
						'url' => '/img/dhcr-feature-badge-300.png',
						'target' => '_blank',
						'width' => 150,
						'height' => 67]) ?>
                    <p>
                        Please mind contributing to the DHCR project by sharing your courses on social media
                        or placing the DHCR-featured badge on institutional websites.
                    </p>
                </div>
                <?php
				$this->set('edit', true);	// displays the "Actions" column in all subsequent elements
				echo $this->element('courses/index', ['varname' => 'yourCourses']);
				?>
            </div>
        </div>
		<?php
	}
    ?>
</div>


<?= $this->element('svg_icons') ?>


<?php
$this->Html->script(['sharing','modal','accordeon'], ['inline' => false]);
$this->Html->scriptStart(array('inline' => false));

echo 'var BASE_URL = "' . Configure::read('dhcr.baseUrl') . '";';

$jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
if(!empty($courses))
    echo 'var yourCourses = ' . json_encode($courses, $jsonOptions). ';';
if(!empty($moderated))
	echo 'var moderatedCourses = ' . json_encode($moderated, $jsonOptions). ';';
if(!empty($new_courses))
	echo 'var newCourses = ' . json_encode($new_courses, $jsonOptions). ';';
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
