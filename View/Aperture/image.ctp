<?php
// 	print_r($version);

// echo mktime(0, 0, 0, 1, 1, 2001);
	
	echo $this->Html->tag('span', $this->Html->image('previews/'
			.($version['Version']['encodedUuid']).
			'/'.$version['Version']['name'].'.png'), array('class' => 'thumnail-image'));
	
// 	print_r($stack);
?>
	
	
	
<div>	
<?php 	
	foreach ($stack as $thumb){
		echo $this->Html->tag('span', $this->Html->image('thumbnails/'
				.$thumb['Version']['encodedUuid'].
				'/'.$thumb['Version']['name'].'.png', array('width' => '202', 'height' => '202')), array('class' => 'thumnail-image'));
	
	}
?>
</div>

<div><ul>
<li>
	<b>Image Date</b>:<?php echo date("Y-m-d H:i:s", $version['Version']['unixImageDate']).' ('.$version['Version']['unixImageDate'].')'; ?>
</li>
<li><b>Places</b>:

<?php 	
// print_r($places);
	foreach ($places as $place){
		echo ' &gt; '.$this->Html->link($place['PlaceName'][0]['description'],
				array('controller' => 'aperture', 'action' => 'gallery', 'place' => $place['Place']['defaultName'], 'language' => Configure::read('Config.language')),
				array('escape' => false));
	}
?>

</li>
<li><b>Keyword</b><ul>
<?php 	
	foreach ($version['Keyword'] as $keyword){

		echo $this->Html->tag('li', $this->Html->link($keyword['name'],
				array('controller' => 'aperture', 'action' => 'gallery', 'keyword' => $keyword['name'], 'language' => Configure::read('Config.language')),
				array('escape' => false)));

// 		echo '<li>'.$keyword['name'].'</li>';
	}
?>
</ul></li>
<?php 	
	foreach ($properties as $key => $groupe){
		echo '<li><b>'.$key.'</b><ul>';
		foreach ($groupe as $property){
			echo '<li>';
			echo '<b>'.$property[$key]['propertyKey'].'</b>: '.(isset($property['UniqueString'])?$property['UniqueString']['stringProperty']:$property[$key]['numberProperty']); 
			echo '</li>';
		}
		echo '</ul></li>';
	}
?>
</ul></div>