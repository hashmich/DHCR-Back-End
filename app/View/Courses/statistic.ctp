<?php $this->Html->scriptStart(array('inline' => false)); ?>
	$(document).ready(function() {
		$('.statistic > .toggle').on('click', function() {
			if($(this).hasClass('off')) {
				$(this).next('div').show();
			}else{
				$(this).next('div').hide();
			}
			$(this).toggleClass('off');
			$(this).find('span').toggleClass('glyphicon-menu-down');
			$(this).find('span').toggleClass('glyphicon-menu-up');
		});
	});
<?php $this->Html->scriptEnd(); ?>

<div class="statistic">

	<h2>Course Statistic</h2>
	
	<dl>
		<dt>Total count (active)</dt>
		<dd><?php echo $count; ?></dd>
	</dl>
	
	<h3 class="toggle off"><span class="glyphicon glyphicon-menu-down"></span> By Country</h3>
	<div style="display:none">
		<dl>
			<?php
			foreach($countries as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 class="toggle off"><span class="glyphicon glyphicon-menu-down"></span> By Institution</h3>
	<div style="display:none">
		<dl>
			<?php
			foreach($institutions as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 class="toggle off"><span class="glyphicon glyphicon-menu-down"></span> By Discipline</h3>
	<div style="display:none">
		<dl>
			<?php
			foreach($disciplines as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 class="toggle off"><span class="glyphicon glyphicon-menu-down"></span> By Technique</h3>
	<div style="display:none">
		<dl>
			<?php
			foreach($techniques as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 class="toggle off"><span class="glyphicon glyphicon-menu-down"></span> By Object</h3>
	<div style="display:none">
		<dl>
			<?php
			foreach($objects as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>

</div>