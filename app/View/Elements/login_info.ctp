
	<?php
	if(!empty($auth_user)) {
		?>
        <div class="info-text">
            <p>
                <?php
                $name = (empty($auth_user['name']))
                    ? $auth_user[Configure::read('Users.loginName')]
                    : $auth_user['name'];
                echo 'Hello ' . $name;
                ?>
            </p>
            <?php
            if(!empty($auth_user['is_admin'])) {
                echo '<p>You are Admin</p>';
            }
            if(	!empty($auth_user[Configure::read('Users.roleModel')])
            AND !empty($auth_user[Configure::read('Users.roleModel')]['name'])) {
                echo '<p>Role: ' . $auth_user[Configure::read('Users.roleModel')]['name'];
                if(	$auth_user['user_role_id'] == 2
                AND	!empty($auth_user['country_id'])) {
                    echo ',<br>' . $auth_user['Country']['name'];
                }
                echo '</p>';
            }

            ?>
            <ul>
                <li>
                    <?php
                    echo $this->Html->link('Dashboard', array(
                        'controller' => 'users',
                        'action' => 'dashboard',
                        'plugin' => false
                    ));
                    ?>
                </li>
                <li>
                    <?php
                    echo $this->Html->link('Profile', array(
                        'controller' => 'users',
                        'action' => 'profile',
                        'plugin' => false
                    ));
                    ?>
                </li>
                <li>
                    <?php
                    echo $this->Html->link('Log Out', array(
                        'controller' => 'users',
                        'action' => 'logout',
                        'plugin' => false
                    ));
                    ?>
                </li>
            </ul>
        </div>
		<?php
	}
	?>


