<?php 
if($modelName AND !empty($this->validationErrors[$modelName])) {
	?>
	<div class="validation-errors">
		<h3>Validation Errors</h3>
		<dl>
			<?php
			foreach($this->validationErrors[$modelName] as $field => $error) {
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