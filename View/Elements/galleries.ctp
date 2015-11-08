		
		<?php foreach($galleries as $idx => $gallery):{
				$url = $this->App->getGalleryUrl($gallery['Gallery']['url']);
				
				$content = $this->Html->link(
						$this->Html->image('thumbnails/'.Configure::read('Config.language').'/'.$gallery['Gallery']['thumbUuid'].'/thumbs.png', array('alt'=>$gallery['Gallery']['name'])).
							$this->Html->tag(
								'div',
								$gallery['Gallery']['name'].//( strlen($gallery['Gallery']['zip'])?' ('.$gallery['Gallery']['zip'].')':'').
								' '.
								$this->Html->tag(
										'span',
										$gallery['Gallery']['counter'],
										array('class'=>"badge")
								)),
						$url,
						array('escape' => false, 'class' => 'thumbnail'));
						

				echo $this->Html->tag('div', 
					$content, 
					array(
							'class' => 'col-sm-6 col-md-4 col-lg-3 ui-state-default groupToMatch',
							'data-url' => $this->Html->url(array(
									'controller' => 'Galleries',
									'action' => 'update',
									'ext' => 'json',
									$gallery['Gallery']['id']
							)),
							'data-object' => json_encode($gallery),
				));
			}	 
			endforeach; ?>