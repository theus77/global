<?php

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

		$url = $this->Html->url([
				'controller' => 'galleries',
				'action' => 'place',
				'language' => Configure::read('Config.language'),
				$temp['uuid']
		]);

		$temp['content'] = $this->element('thumb', array(
						"url" => $url,
						'count' => $temp['counter'],
						'name' => $temp['label'],
						'imageUuid' => $temp['imageUuid'],
						'lazy' => false
				));

		 $out[] = $temp;
	}

	if(count($aggregated) > 0){
		foreach ($aggregated["zoom1"]["buckets"] as $marker ){
			$temp = [
					'uuid' 		=> $marker["key"],
					'counter' 	=> $marker["doc_count"],
					'imageUuid' => $marker["icon"]["hits"]["hits"][0]["_id"],
					'lat' 		=> $marker["icon"]["hits"]["hits"][0]["_source"]["location"]["lat"],
					'lon' 		=> $marker["icon"]["hits"]["hits"][0]["_source"]["location"]["lon"],
					'label'		=> isset($marker["icon"]["hits"]["hits"][0]["_source"]["label"])?$marker["icon"]["hits"]["hits"][0]["_source"]["label"]:$marker["icon"]["hits"]["hits"][0]["_source"]["name"],
			];

			if($temp['counter'] > 1){
				$temp['label'] = __('%s et alentour', $temp['label']);				
			}
			
			$url = $this->Html->url([
					'controller' => 'galleries',
					'action' => 'geohash',
					'language' => Configure::read('Config.language'),
					$temp['uuid']
			]);

			$temp['content'] = $this->element('thumb', array(
					"url" => $url,
					'count' => $temp['counter'],
					'name' => $temp['label'],
					'imageUuid' => $temp['imageUuid'],
					'lazy' => false
			));

			$out[] = $temp;
		}
	}


	echo json_encode(['markers' => $out]);
