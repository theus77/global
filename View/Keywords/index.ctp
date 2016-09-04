<?php 

	$alreadyUsed = [];

?>

<!-- gallery -->
		<section id="gallery" class="section wow fadeInUp">
			<div class="container">	
			
				<div class="section-heading">
					<h2><?php echo __('Thèmes et mots clés')?></h2>
						
				</div>
				
				<div class="row">
														
			<?php foreach ($keywords['keyword_name']['buckets'] as $keyword) : ?>
			
			<?php 
				
			
				$url = $this->Html->url([
						'controller' => 'galleries',
						'action' => 'keyword',
						'language' => Configure::read('Config.language'),
						$keyword["by_uuid"]['buckets'][0]['key']
				]);
				
				
				foreach ($keyword["by_uuid"]['buckets'][0]['keyword_to_version']['top_uuid_hits']['hits']['hits'] as $topHit){
					$imageUuid = $topHit['_id'];
					if(!in_array($imageUuid, $alreadyUsed)){
						$alreadyUsed[] = $imageUuid;
						break;
					}
				}
				
				
				echo $this->element('thumb', array(
						"url" => $url,
						'count' => $keyword['doc_count'],
						'name' =>$keyword['key'],
						'imageUuid' => $imageUuid
				));
			?>
		
		
		<?php endforeach;?>
										
				</div><!-- ./row -->
																														
			</div>
		</section>
		<!-- ./gallery -->
