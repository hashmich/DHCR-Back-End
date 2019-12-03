<!--
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
-->

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
	<?php echo $this->Html->charset(); ?>
	<title>DHCR Back End</title>
	<?php
	echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex'));
	echo $this->Html->meta(array('name' => 'robots', 'content' => 'nofollow'));
	
	echo $this->Html->meta('icon');
	
	if(Configure::read('debug') > 0) echo $this->Html->css('cake_debugging.css');
	
	// custom CSS
	echo $this->Html->css('styles.css');
	echo $this->fetch('css');
	?>
	
</head>

<?php
if(!empty($auth_user)) {
	echo '<body class="authenticated">';
}else{
    echo '<body>';
}
?>

	<div id="container">
        <div id="header">
            
            <a class="blue back button" href="<?= Configure::read('dhcr.baseUrl') ?>">Go to Start</a>
            
            <div id="logo">
                <?php
                $logo = array(
                    'alt' => 'CLARIN-DARIAIH joint Logo',
                    'width' => 115,
                    'height' => 90);

                $file = '/img/logos/DARIAH-CLARIN-joint-logo.jpg';
                echo $this->Html->link($this->Html->image($file, $logo), '/users/dashboard', array(
                    'escape' => false));
                ?>
                <div class="title">
                    <h1>
                        <a href="<?php echo Router::url('/users/dashboard'); ?>">
                            <span id="h1">Digital Humanities</span><br>
                            <span id="h2">Course</span><span id="h3">Registry</span>
                        </a>
                    </h1>

                </div>
            </div>
            
        </div>
		
		<div class="columns">
			<div id="left">
				<?php
				echo $this->element('login_info');
                ?>

			</div>
			
			<div id="content">
				<?php
				echo $this->Session->flash();
				echo $this->fetch('content');
				?>
			</div>
		</div>
		
		<div id="footer">
			<?php echo $this->element('footer'); ?>
		</div>
	</div>
	
	
	
	<script src="https://code.jquery.com/jquery-1.12.4.min.js"
		integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
		crossorigin="anonymous">
	</script>
	<script type="text/javascript">
		window.jQuery || document.write('<script type="text/javascript" src="<?php echo $this->Html->url('/js/jquery-1.12.4.min.js', true); ?>"><\/script>')
	</script>
	
	
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" 
		integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" 
		crossorigin="anonymous">
	</script>
	<script type="text/javascript">
		(typeof $().modal == 'function') || document.write('<script type="text/javascript" src="<?php echo $this->Html->url('/js/bootstrap.min.js', true); ?>"><\/script>')
	</script>
	
	
	<?php echo $this->fetch('script'); ?>

    <!-- Matomo (before was Piwik) -->
    <script type="text/javascript">
        var _paq = _paq || [];
        // tracker methods like "setCustomDimension" should be called before "trackPageView"
        <?php
        if (!empty($auth_user)) {
            echo sprintf("_paq.push(['setUserId', '%s']);", Security::hash($auth_user['id']), null, true);
        }
        ?>
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        
        (function() {
            var u="//matomo.acdh.oeaw.ac.at/";
            _paq.push(['setTrackerUrl', u+'piwik.php']);
            _paq.push(['setSiteId', '21']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <!-- End Piwik Code -->
</body>
</html>
