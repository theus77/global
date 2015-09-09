<?php 
	echo $this->Form->create('Aperture', array('type' => 'get'));
	
	echo $this->Form->input('from_dt', array(
			'label' => __('Date de début'),
			'dateFormat' => 'D/M/Y',
			'minYear' => date('Y') - 70,
			'maxYear' => date('Y') - 18,
	));

	echo $this->Form->input('to_dt', array(
			'label' => __('Date de fin'),
			'dateFormat' => 'D/M/Y',
			'minYear' => date('Y') - 70,
			'maxYear' => date('Y') - 18,
	));

	echo $this->Form->end(__('Rechercher'));
	
	
	echo $this->Html->link('recherche avancée',
			array(
					'controller' => 'aperture', 
					'action' => 'gallery', 
					'keyword' => array('Hopital', 'Maison communale', 'Magasin de meuble', 'Bureau'), 
					'album' => 'TKBUP6N7RQegtytL+QNUaA',
					'place' => array('Forest (Vorst)', 'Anderlecht', 'Belgique'),
					'from' => 1294860361,
					'to' => 1349775467,
					'pattern' => 'Nikon',
						
					'language' => Configure::read('Config.language')),
			array('escape' => false));
?>
