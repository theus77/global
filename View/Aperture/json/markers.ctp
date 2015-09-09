<?php 
	foreach ($markers as $id => &$marker){
		if($marker['counter'] == 0){
			unset($markers[$id]);
		}
		else {
			$marker['uri'] = $this->html->url(array(
				'controller' => 'aperture',
				'action' =>  'gallery',
				'language' => Configure::read('Config.language'),
				$marker['type'] => $marker['id'],
			));
			$marker['content'] = $this->html->link(
				$this->html->image('thumbnails/'.Configure::read('Config.language').'/'.$marker['uuid'].'/thumbs.png', array('alt' => $marker['label'])).
				$this->html->tag('div',
					$marker['label'].
					$this->html->tag('span', $marker['counter'], array('class' => 'badge pull-right'))		
				),
				array(
					'controller' => 'aperture',
					'action' =>  'gallery',
					'language' => Configure::read('Config.language'),
					$marker['type'] => $marker['id'],
				),
				array(
					'class' => array('thumbnail'),
					'escape' => false,
				)
			);
		}
	}


	echo json_encode(compact('console', 'markers'));

	//http://global.theus.be/img/thumbnails/zubvhsaUT7aWLoIxYU8tuQ/thumbs.png
	//http://gv.local       /img/thumbnails/znrfUkaOTU2GAlB7uHUI0g/thumbs.png
	
?>