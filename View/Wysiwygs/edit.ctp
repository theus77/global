<div class="wysiwygs form container">
		<div class="wysiwygs form">
			<?php echo $this->Form->create('Wysiwyg'); ?>
					<h2><?php echo __('Edit Wysiwyg'); ?></h2>
				<?php
					echo $this->Form->input('id');
					echo $this->Form->input('key');
					echo $this->Form->checkbox('singleline');
					echo $this->Form->input('value', array(
					    'label' => 'Texte en français',
						'type' => 'textarea',
						'class' => $this->Form->value('Wysiwyg.singleline')?'':'ckeditor',
					));
					echo $this->Form->input('valueTranslation.1.content', array(
					    'label' => 'Traduction en néerlandais',
						'type' => 'textarea',
						'class' => $this->Form->value('Wysiwyg.singleline')?'':'ckeditor',
					));
					echo $this->Form->input('valueTranslation.2.content', array(
					    'label' => 'Traduction en anglais',
						'type' => 'textarea',
						'class' => $this->Form->value('Wysiwyg.singleline')?'':'ckeditor',
					));
				?>
			<?php echo $this->Form->end(__('Submit')); ?>
			<h3><?php echo __('Actions'); ?></h3>
			<ul>

				<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Wysiwyg.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Wysiwyg.id'))); ?></li>
				<li><?php echo $this->Html->link(__('List Wysiwygs'), array('action' => 'index')); ?></li>
			</ul>
		</div>
</div>
