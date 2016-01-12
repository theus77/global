<?php
	var_dump($markers["by_level"]["by_coordinate"]["by_location"]["buckets"]);
	

	$out = [];
	foreach ( $markers["by_level"]["by_coordinate"]["by_location"]["buckets"] as $marker ){
		$temp = [
			'uuid' 		=> $marker["key"],
			'counter' 	=> $marker["doc_count"],
			'imageUuid' => $marker["top_version"]["hits"]["hits"][0]["_id"],
			'lat' 		=> $marker["top_version"]["hits"]["hits"][0]["_source"]["location"]["lat"],
			'lon' 		=> $marker["top_version"]["hits"]["hits"][0]["_source"]["location"]["lon"],
			'label'		=> $marker["top_version"]["hits"]["hits"][0]["_source"]["name_".Configure::read('Config.language')],
		];
		$temp['content'] = $this->html->link(
			$this->html->image('thumbnails/'.Configure::read('Config.language').'/'.$temp['uuid'].'/thumbs.png', array('alt' => $temp['label'])).
			$this->html->tag('div',
				$temp['label'].
				$this->html->tag('span', $temp['counter'], array('class' => 'badge pull-right'))		
			),
			array(
				'controller' => 'aperture',
				'action' =>  'gallery',
				'language' => Configure::read('Config.language'),
				'uuid' => $marker['key'],
			),
			array(
				'class' => array('thumbnail'),
				'escape' => false,
			)
		);
		
		 $out[] = $temp;
	}

	var_dump($out);