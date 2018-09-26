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
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php
		$page = $this->fetch('title');
		$title = 'DH Registry';
		if($page == 'Courses') $title = 'DH Course Registry';
		elseif($page == 'Projects') $title = 'DH Project Registry';
		echo $title;
		?>
	</title>
	<?php
	if(Configure::read('debug') > 0) {
		echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex'));
		echo $this->Html->meta(array('name' => 'robots', 'content' => 'nofollow'));
	}else{
		echo $this->Html->meta(array('name' => 'robots', 'content' => 'index'));
		echo $this->Html->meta(array('name' => 'robots', 'content' => 'follow'));
	}
	echo $this->Html->meta('keywords', 'digital humanities, research, programs, courses');
	echo $this->Html->meta('description', 'European platform for digital humanity related research, courses and programs.');
	echo $this->fetch('meta');
	
	echo $this->Html->meta('icon');
	
	if(Configure::read('debug') > 0) echo $this->Html->css('cake_debugging.css');
	
	// TODO: streamline the styles with bootstrap?
	/*echo $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css', array(
		'integrity' => 'sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7',
		'crossorigin' => 'anonymous'));*/
	
	// custom CSS
	echo $this->Html->css('styles.css');
	echo $this->fetch('css');
	?>
	
</head>


<body>
	<div id="container">

        <div id="header">
            <?php
            $logo = array(
                'alt' => 'CLARIN-DARIAIH joint Logo',
                'width' => 115,
                'height' => 90);
            
            $file = '/img/logos/DARIAH-CLARIN-joint-logo.jpg';
            echo $this->Html->link($this->Html->image($file, $logo), '/', array(
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
		
		<div class="columns">
			<div id="menu">
				<?php
				echo $this->element('login_info');
				?>
				<div class="login_link">
					<ul>
						<li>
							<?php echo $this->Html->link('Home', '/'); ?>
						</li><li>
							<?php echo $this->Html->link('Contact', '/contact/us'); ?>
						</li><li>
							<?php echo $this->Html->link('About', '/pages/about'); ?>
						</li><li>
							<?php echo $this->Html->link('Statistic', '/statistic'); ?>
						</li>
					</ul>
				</div>
				<ul>
				<?php
				if($this->request->params['controller'] != 'courses')
					echo '<li>' . $this->Html->link('Courses', array('controller' => 'courses', 'action' => 'index', 'plugin' => null)) . '</li>';
				
				echo $this->fetch('menu');
				?>
				</ul>
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
	
	<!-- Piwik -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  // tracker methods like "setCustomDimension" should be called before "trackPageView" 
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
	    var u="//piwik.apollo.arz.oeaw.ac.at/";
	    _paq.push(['setTrackerUrl', u+'piwik.php']);
	    _paq.push(['setSiteId', '21']);
	    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<!-- End Piwik Code -->
</body>
</html>
