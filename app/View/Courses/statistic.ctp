<div class="statistic">

	<h2>Course Statistic</h2>
	
	<dl>
		<dt>Total count (active)</dt>
		<dd><?php echo $count; ?></dd>
	</dl>
	
	<h3 onclick="$('#countries').toggle()">By Country</h3>
	<div id="countries" style="display:none">
		<dl>
			<?php
			foreach($countries as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 onclick="$('#institutions').toggle()">By Institution</h3>
	<div id="institutions" style="display:none">
		<dl>
			<?php
			foreach($institutions as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 onclick="$('#disciplines').toggle()">By Discipline</h3>
	<div id="disciplines" style="display:none">
		<dl>
			<?php
			foreach($disciplines as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 onclick="$('#techniques').toggle()">By Technique</h3>
	<div id="techniques" style="display:none">
		<dl>
			<?php
			foreach($techniques as $item) {
				echo '<dt>' . $item['label'] . '</dt>';
				echo '<dd>' . $item['count'] . '</dd>';
			}
			?>
		</dl>
	</div>
	
	<h3 onclick="$('#objects').toggle()">By Object</h3>
	<div id="objects" style="display:none">
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