
<div class="container library">
	<div class="row">

	<?php foreach ($galleries as $gallery):
		$mainDiv = $this->Html->tag('div', 
				$this->html->image('thumbnails/'.Configure::read('Config.language').'/'. $gallery['Gallery']['thumbUuid'].'/thumbs.png', array('alt' => $gallery['Gallery']['name'], 
					'class' => 'img-responsive')).
				$this->Html->tag('div', $gallery['Gallery']['name'].
				$this->Html->tag('span', $gallery['Gallery']['counter'], array('class'=>'badge'))
				));
		echo  $this->Html->tag('div', 
		 $this->Html->link($mainDiv,
				$this->App->getGalleryUrl($gallery['Gallery']['url'])
		, array('escape'=>false, 'class' => 'thumbnail')), array( 'class' => 'col-xs-12 col-sm-6 col-md-4 col-lg-3'));
	?>

	<?php endforeach;?>

	</div>
</div>