

<div class="css-columns-3">
    <h2>Lecturers &amp; Programme Directors</h2>
    <p>
        Lecturers or progamm administrators, who want to promote their own DH-related
        teaching activities or aim to attract and facilitate staff mobility and exchange
        need to sign in with an account.
    </p>
    <p>
        After registration, new courses can be added to the registry.
        In order to show only valuable, up to date content, we require all contributors to
        actively maintain their data.
        Maintenance obliges to the person that originally added the record.
        The system will regularly send out email reminders to them,
        whenever an entry is about to expire.
        Course data not revised for a year will be flagged yellow in the course listing.
        After one a half year, the course will be marked red and will disappear from the
        public listing short time after.
        The system also performs regular link checking on URLs provided within the course meta data.
        Maintainers will retrieve an email request to correct dead URLs in that case.
    </p>
    <p>
        Though we support a single sign-on process that is connected to most major institutions,
        we still require new users to register first and provide specific metadata,
        that we can not retrieve otherwise. <br>
        We are working on solutions to make this process more comfortable.
    </p>
    <p>
        The DH Course Registry is a joint effort of two European research
        infrastructures (ERICs),
        <?php echo $this->Html->link('CLARIN ERIC', 'https://www.clarin.eu/glossary#ERIC',
            array('target' => '_blank')); ?> and
        <?php echo $this->Html->link('DARIAH-EU', 'http://dariah.eu/',
            array('target' => '_blank')); ?>.
        In future, it will be further developed and maintained as a
        collaboration of the two ERICs. The Course Registry started as a
        selection of DH courses offered by European academic organizations. An
        extension of the registry content beyond Europe is currently going on.
        Students, lecturers and researchers can search the database on the basis of
        disciplines, topographical information (location), ECTS credits or the
        academic degrees that are awarded. In addition, courses can be searched for
        using TaDiRAH, a Taxonomy of Digital Research Activities in the Humanities
        (including labels for techniques and objects) and sub-disciplines from the
        Social Sciences and the Humanities.
    </p>

    <div style="display: inline-block">
        <h2>Introductory Video Lectures</h2>
        <p>
            Following videos give a short introduction about the Digital Humanities Course Registry.
        </p>
        <p>
            The first one gives an overview about the intention of the registry and
            the institutions involved,
            while the second shows the registration process for lecturers and other data contributors,
            such as programme directors at institutes and faculty departments.
            After that, a short introduction is given about how to add course data to the registry.
        </p>
    </div>
    <p>
        The registration process involves confirmation of your email address.
        Before you can use your account, a moderator has to approve your account.
        Our <?php echo $this->Html->link('contact page', '/contact/us'); ?> lists all moderators
        we have in our system.
        Please use the contact form, to have the system select the right moderator or administrator
        automatically for you.
        In case of problems, you may contact a user administrator directly.
    </p>
</div>
<hr>

<?php
if(empty($auth_user)) {
    ?>
    <div class="login_link">
        <ul>
            <li>
                <?php
                echo $this->Html->link('Login', array(
                    'controller' => 'users',
                    'action' => 'login'
                ));
                ?>
            </li>
            <?php
            if(is_null(Configure::read('Users.allowRegistration')) OR Configure::read('Users.allowRegistration')) {
                ?>
                <li>
                    <?php
                    echo $this->Html->link('Register', array(
                        'controller' => 'users',
                        'action' => 'register'
                    ));
                    ?>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <hr>
    <?php
}
?>

<div class="css-columns-2">
    <div class="iframe-container">
        <iframe src="https://www.youtube.com/embed/pvFKq67-21I?rel=0"
                allow="encrypted-media" allowfullscreen></iframe>
    </div>
    <div class="iframe-container">
        <iframe src="https://www.youtube.com/embed/p_X7K2b1D9g?rel=0"
                allow="encrypted-media" allowfullscreen></iframe>
    </div>
</div>





