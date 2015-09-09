
<div class="row text-center">
	<h2><?php echo __('Traductions');?></h2>
</div>
<div class="row">
<div class="">
<table class="table table-striped">
  <tr>
	  <th colspan="5" class="text-right" >
	  	<?php echo $this->Html->link('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> '.__('Ajouter'), array('action' => 'add'), array('class'=>'btn btn-primary btn-xs', 'type'=>'button', 'escape'=>false)); ?>
	  </th>
  </tr>  	<tr>
	  <th><?php echo __('Clé')?></th>
	  <th class="col-md-4"><?php echo __('Traduction en français')?></th>
	  <th class="col-md-4"><?php echo __('Traduction en néerlandais')?></th>
	  <th class="col-md-4"><?php echo __('Traduction en anglais')?></th>
	  <th><?php echo __('Effacer')?></th>
  </tr>  
  <?php foreach ($wysiwygs as $idx =>  $wysiwyg): ?>
	<tr>
		<td><?php echo h($wysiwyg['Wysiwyg']['key']); ?>&nbsp;</td>
		<td><?php 
			$url = $this->Html->url(array(
					'controller' => 'wysiwygs',
					'action' => 'update',
					'slug' => $wysiwyg['Wysiwyg']['key'],
					'ext' => 'json',
					'language' => 'fr'
			));
			if($wysiwyg['Wysiwyg']['singleline']){
				echo $this->Form->text('content-'+$idx, array(
						'class' => 'singleline',
						'value' => $wysiwyg['valueTranslation'][0]['content'],
						'data-update-url' => $url,
				));
			}
			else{
				echo $this->Html->tag(
						'div',
						$wysiwyg['valueTranslation'][0]['content'],
						array(
								'class'=>'wysiwyg',
								//'contenteditable'=>'true',
								'data-update-url' => $url,
						)
				);
			}
		?></td>
		<td><?php 
			$url = $this->Html->url(array(
					'controller' => 'wysiwygs',
					'action' => 'update',
					'slug' => $wysiwyg['Wysiwyg']['key'],
					'ext' => 'json',
					'language' => 'nl'
			));
			if($wysiwyg['Wysiwyg']['singleline']){
				echo $this->Form->text('content-'+$idx, array(
						'class' => 'singleline',
						'value' => isset($wysiwyg['valueTranslation'][1])?$wysiwyg['valueTranslation'][1]['content']:'',
						'data-update-url' => $url,
				));
			}
			else{
				echo $this->Html->tag(
						'div',
						$wysiwyg['valueTranslation'][1]['content'],
						array(
								'class'=>'wysiwyg',
								'contenteditable'=>'true',
								'data-update-url' => $url,
						)
				);
			}
		?></td>
		<td><?php 
			$url = $this->Html->url(array(
					'controller' => 'wysiwygs',
					'action' => 'update',
					'slug' => $wysiwyg['Wysiwyg']['key'],
					'ext' => 'json',
					'language' => 'en'
			));
			if($wysiwyg['Wysiwyg']['singleline']){
				echo $this->Form->text('content-'+$idx, array(
						'class' => 'singleline',
						'value' => isset($wysiwyg['valueTranslation'][1])?$wysiwyg['valueTranslation'][2]['content']:'',
						'data-update-url' => $url,
				));
			}
			else{
				echo $this->Html->tag(
						'div',
						$wysiwyg['valueTranslation'][2]['content'],
						array(
								'class'=>'wysiwyg',
								'contenteditable'=>'true',
								'data-update-url' => $url,
						)
				);
			}
		?></td>
		<td>
			<?php echo $this->Html->link(__('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>'), array('action' => 'edit', 'language' => Configure::read('Config.language'), $wysiwyg['Wysiwyg']['id']), array('escape'=>false)); ?>
			<?php echo $this->Form->postLink(__('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>'), array('action' => 'delete', 'language' => Configure::read('Config.language'), $wysiwyg['Wysiwyg']['id']), array('escape'=>false), __('Effacer la traduction %s?', $wysiwyg['Wysiwyg']['key'])); ?>
		</td>
	</tr>
  <?php endforeach; ?>
  <tfoot>
    <tr>
      <td colspan="2"><?php
		echo $this->Paginator->counter(array(
			'format' => __('Page {:page} sur {:pages}, {:current} traductions sur un total de {:count}, de {:start} à {:end}')
		));
	?></td>
      <td class="text-right" colspan="3">

      
		<nav>
		  <ul class="pagination">
			<?php echo $this->Paginator->numbers(array(
					'tag' => 'li',
					'separator' => '',
					'currentClass' => 'active',
					'first' => '«',
					'last' => '»',
					'currentTag' => 'a'
					
			)); ?>
		  </ul>
		</nav>
      
      </td>
    </tr>
  </tfoot>
</table>
</div>
</div>
