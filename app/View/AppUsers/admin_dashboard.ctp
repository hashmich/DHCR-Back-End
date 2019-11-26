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
<h2>Admin Dashboard</h2>

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
                echo $this->element('courses/index', array('courses' => $new_courses));
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
				echo $this->element('courses/index', array('courses' => $moderated));
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
                <?php
				$this->set('edit', true);	// displays the "Actions" column in all subsequent elements
				echo $this->element('courses/index');
				?>
            </div>
        </div>
		<?php
	}
    ?>
    
</div>


<?php
$this->Html->script('accordeon', array('inline' => false));
$this->Html->scriptStart(array('inline' => false));
?>
$(document).ready( function() {
    let accordeon = new Accordeon('accordeon');
});
<?php $this->Html->scriptEnd(); ?>
