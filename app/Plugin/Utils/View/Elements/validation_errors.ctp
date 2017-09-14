<?php
if(!empty($errors) OR ($modelName AND !empty($this->validationErrors[$modelName]))) {
	if(empty($errors)) {
		$errors = array();
		if($modelName AND !empty($this->validationErrors[$modelName]))
			$errors = $this->validationErrors[$modelName];
	}
	?>
	<div class="validation-errors">
		<h3>Validation Errors</h3>
		<dl>
			<?php
			foreach($errors as $field => $error) {
				?>
				<dt><?php echo Inflector::humanize($field); ?></dt>
				<dd><?php echo implode('<br />', $error); ?></dd>
				<?php
			}
			?>
		</dl>
	</div>
	<?php
}
?>
