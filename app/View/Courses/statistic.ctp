
<h2>Courses Statistic</h2>

<dl>
	<dt>Total count (active)</dt>
	<dd><?php echo $count; ?></dd>
</dl>

<h3>By Country</h3>
<dl>
	<?php
	foreach($countries as $item) {
		echo '<dt>' . $item['label'] . '</dt>';
		echo '<dd>' . $item['count'] . '</dd>';
	}
	?>
</dl>

<h3>By Institution</h3>
<dl>
	<?php
	foreach($institutions as $item) {
		echo '<dt>' . $item['label'] . '</dt>';
		echo '<dd>' . $item['count'] . '</dd>';
	}
	?>
</dl>
