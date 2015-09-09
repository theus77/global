
<div class="row text-center">
	<h2><?php echo __('Liste des galeries');?></h2>
</div>
<div class="row">
<div class="col-md-2 "></div>
<div class="col-md-8">
	<ul class="list-group">
		<?php 
			foreach ($galleries as $gallery){
				
				$links = $this->Html->link(__('Editer'), array(
						'controller' => 'galleries',
						'action' => 'edit',
						'language' => Configure::read('Config.language'),
						$gallery['Gallery']['id'],
						'?' => array(
								'action' => 'index',
								'language' => Configure::read('Config.language')
						)
				), array(
						'class' => 'btn btn-default btn-primary',
						'role' => 'button',
				));
				$links .= $this->Form->postLink(($gallery['Gallery']['published']?__('Dépublier'):__('Publier')), array(
						'controller' => 'galleries',
						'action' => 'publish',
						'language' => Configure::read('Config.language'),
						$gallery['Gallery']['id'],
						'?' => array(
								'action' => 'index',
								'language' => Configure::read('Config.language')
						)
				), array(
						'class' => 'btn btn-default btn-primary',
						'role' => 'button',
				));
				$links .= $this->Form->postLink(__('Effacer'), array(
						'controller' => 'galleries',
						'action' => 'delete',
						'language' => Configure::read('Config.language'),
						$gallery['Gallery']['id'],
						'?' => array(
								'action' => 'index',
								'language' => Configure::read('Config.language')
						)
				), array(
						'class' => 'btn btn-danger btn-primary',
						'role' => 'button',
				), __('Etes vous sûr de vouloir effacer la galerie %s?', $gallery['Gallery']['name']));				
				
				$url = $this->App->getGalleryUrl($gallery['Gallery']['url']);
				echo $this->Html->tag(
						'li',
						$this->Html->link(
							$gallery['Gallery']['name'],
							$url,
							array('escape'=>false)
						).
						$this->Html->tag('div', $links, array(
								'class' => 'btn-group btn-group-xs',
								'role' => 'group',
						)).
						$this->Html->tag('span', $gallery['Gallery']['counter'], array('class'=>'badge')).
						$this->Html->tag('span', 'H', array('class'=>'badge')),
						array('class'=>"list-group-item"));
			}
		?>
	</ul>
</div>
<div class="col-md-2 "></div>
</div>