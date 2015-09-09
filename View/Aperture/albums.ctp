
<div class="row text-center">
	<h2><?php echo __('Liste des albums Aperture');?></h2>
</div>
<div class="row">
<div class="col-md-2 "></div>
<div class="col-md-8">
	<ul class="list-group">
		<?php 
			foreach ($albums as &$album){
				echo $this->Html->tag(
						'li',
						$this->Html->link(
							$album['Album']['name'],
							array(
								'action' => 'gallery',
								'album' => 	$album['Album']['encodedUuid']				
							),
							array('escape'=>false)
						).
						$this->Html->tag('span', $album['Album']['versioncount'], array('class'=>'badge')),
						array('class'=>"list-group-item"));
			}
		?>
	</ul>
</div>
<div class="col-md-2 "></div>
</div>