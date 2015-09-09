
<div class="row">


<?php foreach ($galleries as $gallery):
	$mainDiv = $this->Html->tag('div', 
			$this->html->image('thumbnails/'.Configure::read('Config.language').'/'. $gallery['Gallery']['thumbUuid'].'/thumbs.png', array('alt' => $gallery['Gallery']['name'])).
			$this->Html->tag('div', $gallery['Gallery']['name'], array('class'=>'col-xs-10')).
			'<div class="col-xs-2 text-center">'.
			$this->Html->tag('span', $gallery['Gallery']['counter'], array('class'=>'badge')).
			'</div>', array(
			'class'=>'well col-xs-12 col-sm-6 col-md-4 col-lg-3'
	));
	
	echo $this->Html->link($mainDiv,
			$this->App->getGalleryUrl($gallery['Gallery']['url'])
	, array('escape'=>false));
?>

<?php endforeach;?>


</div>