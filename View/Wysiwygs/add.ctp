<div class="wysiwygs form">
<?php echo $this->Form->create('Wysiwyg'); ?>
	<fieldset>
		<legend><?php echo __('Add Wysiwyg'); ?></legend>
	<?php
		echo $this->Form->input('key');
		echo $this->Form->checkbox('singleline');
		/*echo $this->Form->input('value', array(
		    'label' => 'Texte en français',
			'type' => 'textarea',
			'class' => 'withCk',
		));*/
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Wysiwygs'), array('action' => 'index')); ?></li>
	</ul>
</div>
<script>

	// This call can be placed at any point after the
	// <textarea>, or inside a <head><script> in a
	// window.onload event handler.

	// Replace the <textarea id="editor"> with an CKEditor
	// instance, using default configurations.
	CKEDITOR.replaceClass = 'withCk';
	CKEDITOR.replace();

</script>