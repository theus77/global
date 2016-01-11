<?php 

	$alreadyUsed = [];

?>
<div class="container library">
	<div class="row">
		<h2 class="col-lg-12 parallax"><?php echo __('Liste des mots clés')?> </h2>
	</div>
	<div class="row">
		<?php foreach ($keywords['keyword_name']['buckets'] as $keyword) : ?>
			
			<?php 
				
			
				$url = $this->Html->url([
						'controller' => 'galleries',
						'action' => 'keyword',
						'language' => Configure::read('Config.language'),
						$keyword['key']
				]);
				
				
				foreach ($keyword['keyword_to_version']['top_uuid_hits']['hits']['hits'] as $topHit){
					$imageUuid = $topHit['_id'];
					if(!in_array($imageUuid, $alreadyUsed)){
						$alreadyUsed[] = $imageUuid;
						break;
					}
				}
				
				
				echo $this->element('thumb', array(
						"url" => $url,
						'count' => $keyword['doc_count'],
						'name' => $keyword['key'],
						'imageUuid' => $imageUuid
				));

			?>
		
		
		<?php endforeach;?>
	</div>
</div>