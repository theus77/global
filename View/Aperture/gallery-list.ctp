
<div class="row">
	<h2><?php echo __('Liste des mots clés Aperture');?></h2>
</div>
<div class="row">
<div class="">
<table class="table table-striped">
	<tr>
	  <th><?php echo __('Nom Aperture')?></th>
	  <th><?php echo __('Traduction en français')?></th>
	  <th><?php echo __('Traduction en néerlandais')?></th>
	  <th><?php echo __('Traduction en anglais')?></th>
	  <th><?php echo __('Compteur')?></th>
	  <th><?php echo __('Mot clé')?></th>
	  <th><?php echo __('Homepage')?></th>
  </tr>
  <?php foreach ($models as $id => &$keyword): ?>
			<tr>
				<?php echo $this->Html->tag(
						'td',
						$this->Html->link(
							$this->Html->tag('div', $keyword),
							array(
								'action' => 'gallery',
								$model => 	$id				
							),
							array('escape'=>false)
						),
						array('class'=>""));
				
						if(isset($galleries[$id])){
							echo $this->Html->tag(
									'td',
										$galleries[$id]['nameTranslation'][0]['content']
									);
							echo $this->Html->tag(
									'td',
										$galleries[$id]['nameTranslation'][1]['content']
									);
							echo $this->Html->tag(
									'td',
										$galleries[$id]['nameTranslation'][2]['content']
									);
							echo $this->Html->tag(
									'td',
										$galleries[$id]['Gallery']['counter']
									);
							echo $this->Html->tag(
									'td',
										$galleries[$id]['Gallery']['keyword']
									);
							echo $this->Html->tag(
									'td',
										$galleries[$id]['Gallery']['homepage']
									);
						}
						else{
							echo $this->Html->tag(
									'td',
									' ',
									array('colspan'=>6)
							);
						}
				?>
			</tr>
	<?php endforeach; ?>
</table>
</div>
</div>