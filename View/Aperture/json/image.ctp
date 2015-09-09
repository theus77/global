<?php 

foreach ($stack as &$image){
	$image['Version']['encodedUuid'] = urlencode($image['Version']['encodedUuid']);
}

$version['Version']['formatedDate'] = date("d M Y", $version['Version']['unixImageDate']);


$prop = array();
foreach ($properties['ExifNumberProperty'] as $property){
	$prop[$property['ExifNumberProperty']['propertyKey']] = $property['ExifNumberProperty']['numberProperty'];
}
foreach ($properties['ExifStringProperty'] as $property){
	$prop[$property['ExifStringProperty']['propertyKey']] = $property['UniqueString']['stringProperty'];
}
foreach ($properties['IptcProperty'] as $property){
	$prop[$property['IptcProperty']['propertyKey']] = $property['UniqueString']['stringProperty'];
}
foreach ($properties['OtherProperty'] as $property){
	$prop[$property['OtherProperty']['propertyKey']] = $property['UniqueString']['stringProperty'];
}

foreach ($places as &$place){
	$place['link'] = $this->html->link(
			isset($place['PlaceName'][0])?$place['PlaceName'][0]['description']:$place['Place']['defaultName'],
			array(
					'controller' => 'aperture',
					'action' =>  'gallery',
					'language' => Configure::read('Config.language'),
					'placeId' => $place['Place']['modelId'],
			));

}

foreach ($version['Keyword'] as &$keyword){
	$keyword['link'] = $this->html->link(
			$keyword['name'],
			array(
					'controller' => 'aperture',
					'action' =>  'gallery',
					'language' => Configure::read('Config.language'),
					'keywordId' => $keyword['modelId'],
			));

}

echo json_encode(compact('stack', 'places', 'prop', 'version'));