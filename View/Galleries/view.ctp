<?php


function cmpKeyword($a, $b) {
    return strcmp($a['name_'.Configure::read('Config.language')], $b['name_'.Configure::read('Config.language')]);
}

function cmpLocation($a, $b) {
    return $a['level'] > $b['level'];
}


?>
<script type="text/javascript" src="<?php echo 'https://maps.googleapis.com/maps/api/js?key='.Configure::read('googleApiKey');?>"></script>
<?php
	$this->assign('title', $title);
?>
				
<div id="galerie-filmstrip" class="clearfix">
	<div id="galerie-thumb-bro">
		<div class="col-md-12 galerie-thumb">
			<h2 id="intro" class="pull-left"><?php echo $title; ?> 
			

<?php if($pageMax > 1):?>
<nav class="pull-right">
  <ul class="pagination  pagination-sm">
    
    <?php 
    
    
    
    echo $this->Html->tag('li',
    		$this->Html->link('&laquo;', $page == 0 ? '#': $this->request->params['pass'], ['escape' => false]),
    		[
    				'class' => ($page <= 0) ? 'disabled' : ''
    		]
    		);
    echo $this->Html->tag('li',
    		$this->Html->link('&lt;', $page == 0 ? '#': $page-1 > 1 ? array_merge($this->request->params['pass'], ['?' => ['page'=>$page-1]]) : [$this->request->params['pass']], ['escape' => false]),
    		[
    				'class' => ($page <= 0) ? 'disabled' : ''
    		]
    );   
        
    
    if($pageMax < 5){
    	$start = 0;
    	$end = $pageMax;
    }
    else if($page - 2 <= 0) {
    	$start = 0;
    	$end = 5;
    }
    else if ($page + 3 >= $pageMax) {
    	$start = $pageMax-5;
    	$end = $pageMax;
    }
    else {    	
	    $start = $page - 2;
	    $end = $page + 3;
    }
    
    
    for ($i = $start; $i < $end ; ++$i){
    
    	echo $this->Html->tag('li',
    		$this->Html->link($i+1, $i? array_merge($this->request->params['pass'], ['?' => ['page'=>$i]]):$this->request->params['pass'], []),
    		[
    			'class' => ($i == $page) ? 'active' : ''
    		]
    	);
    	

    }
    
    echo $this->Html->tag('li',
    		$this->Html->link('&gt;', $page+1 == $pageMax ? '#': array_merge($this->request->params['pass'], ['?' => ['page'=>$page+1]]), ['escape' => false]),
    		[
    				'class' => ($page+1 >= $pageMax) ? 'disabled' : ''
    		]
    		);
    
    echo $this->Html->tag('li',
    		$this->Html->link('&raquo;', $page+1 == $pageMax ? '#': array_merge($this->request->params['pass'], ['?' => ['page'=>$pageMax-1]]), ['escape' => false]),
    		[
    				'class' => ($page+1 >= $pageMax) ? 'disabled' : ''
    		]
    		);
    
    ?>
    
  </ul>
</nav>
<?php endif;?>		

			<span class="badge"><?php
				echo $versions['hits']['total'];

// 			if(isset($prev)){
// 				$url = [];
// 				if($prev > 0){
// 					$url = [
// 						'?' => [
// 							'from' => $prev		
// 						]
// 					];
// 				}
				
// 				echo $this->Html->link(__('«'), $url)." ";
// 			}
			
// 			if($versions['hits']['total'] != count($versions['hits']['hits'])){
// 				echo __("%s", count($versions['hits']['hits']), $versions['hits']['total']);
// 			}
// 			else {
// 				echo __n("1 résultat", "%s résultats", $versions['hits']['total'], $versions['hits']['total']);
// 			}
			
// 			if(isset($next)){
// 				echo " ".$this->Html->link(__('»'), [
// 						'?' => [
// 								'from' => $next
// 						]
// 				]);
// 			}



			?></span>			
			</h2>


			<div class="galerie-thumb-scroll groupToMatch">
				<?php foreach ($versions['hits']['hits'] as $idx => $version) : ?>
				<a href="#" class="thumbnail"  data-target="#GalerieCarousel" data-slide-to="<?php echo $idx; ?>"><?php
					echo $this->element('image', array(
								'lazy' => true,
								'alt' => isset($version['_source']['label'])?$version['_source']['label']:$version['_source']['name'],
								'imageUuid' => $version['_source']['uuid'],
								'route' => 'preview.jpg',
								'class' => 'lazy img-responsive'
						));



// 					$this->Html->image(
// 					($version['_source']['uuid']).'/thumb',
// 					array('class' => 'img-responsive', 'escape' => false));
				?></a>
				<?php endforeach;?>
			</div>
			<div class="trigger-expand top">
		<a href="#">
          <span class="glyphicon glyphicon-chevron-down"></span>
        </a>
        </div>
		</div>

		</div> <!-- / row -->
		<div class="galerie-carousel groupToMatch">
			<!-- Carousel
			================================================== -->
			<div id="GalerieCarousel" class="carousel slide" data-ride="carousel">

				<div class="carousel-inner" role="listbox">
					<?php
						foreach ($versions['hits']['hits'] as $idx => $version){
							echo $this->Html->tag('div',
								//$this->Html->link(


									//$this->Html->image($version['_source']['uuid'].'/preview', array('data-src' => '/img/'.$version['_source']['uuid'].'/image', 'class' => 'lazy toLoad preview'))

									$this->element('image', array(
											'lazy' => true,
											'alt' => isset($version['_source']['label'])?$version['_source']['label']:$version['_source']['name'],
											'imageUuid' => $version['_source']['uuid'],
											'route' => 'image.jpg',
											'class' => 'lazy preview'
									))


									//$this->Html->image($version['Version']['encodedUuid'].'/preview.png'),
									//array('controller' => 'aperture', 'action' =>  'image', $version['Version']['encodedUuid'], 'language' => Configure::read('Config.language')),
									//array('escape' => false)),
									//.$this->Html->tag('div', 'Chargement en cours...', array('class' => 'carousel-caption'))

									,
								array('class' => $idx?'item':'item active', 'data-image-info' =>
									$this->Html->url(array(
										'controller' => 'aperture',
										'action' =>  'image',
										'language' => Configure::read('Config.language'),
										$version['_source']['uuid'],
										'ext' => 'json',
									))
								)
														);
						} // end foreach
					?>
				</div>
				<a class="left carousel-control" href="#GalerieCarousel" role="button" data-slide="prev">
					<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="right carousel-control" href="#GalerieCarousel" role="button" data-slide="next">
					<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
				</div><!-- /.carousel -->
				</div> <!-- /.gallerie-carousel -->
				<div id="galerie-thumb-child">
					<div class="col-md-12 galerie-thumb">
						<div class="galerie-thumb-scroll groupToMatch">
							<?php foreach ($versions['hits']['hits'] as $idx => $version) : ?>
								<?php foreach ($version['_source']['Stack'] as $stack) : ?>
								<?php

									echo $this->Html->link(
											$this->element('image', array(
													'lazy' => true,
													'alt' => isset($version['_source']['label'])?$version['_source']['label']:$version['_source']['name'],
													'imageUuid' => $stack,
													'route' => 'preview.jpg',
													'class' => 'img-responsive'
											)),
											'#',
											array(
												'class' => 	'thumbnail',
												'onclick' => 'javascript: return loadStackImage(this);',
												'escape' => false,
												'data-stack-uuid' => $this->Html->url('/img/'.Configure::read('Config.language').'/'.$stack.'/image.jpg'),
											));

								?>
								<?php endforeach;?>
							<?php endforeach;?>
						</div>
						<div class="trigger-expand bottom">
							<a href="#">
					          <span class="glyphicon glyphicon-chevron-up"></span>
					        </a>
					    </div>
					</div>
					</div> <!-- / row -->
					<!-- infos -->
					<div id="galerie-info" class="<?php if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'):?>admin-galery-wrapper<?php endif;?>">
						<div class="galerie-carousel groupToMatch">
							<!-- Carousel
							================================================== -->
							<div id="infoCarousel" class="carousel slide" data-ride="carousel">
								<!-- Indicators -->
								<!--       <ol class="carousel-indicators"> -->
								<!--         <li data-target="#GalerieCarousel" data-slide-to="0" class="active"></li> -->
								<!--         <li data-target="#GalerieCarousel" data-slide-to="1"></li> -->
								<!--         <li data-target="#GalerieCarousel" data-slide-to="2"></li> -->
								<!--       </ol> -->
								<div class="carousel-inner" role="listbox">
									<?php foreach ($versions['hits']['hits'] as $idx => &$version) : ?>
									<div class="<?php echo $idx?'item':'item active'; ?>">


										<div class="wrapper-info" >
											<div class="glyphicon glyphicon-move"></div>
											<h2 id="versionTitle">

											<?php
															if(isset($version['_source']['label'])){
																echo $version['_source']['label'];
															}
															else {
																echo $version['_source']['name'];
															}
											?>
											<span></span>
											</h2>


											<div class="infos">
												<?php if(count($version['_source']['Keywords']) > 0) : ?>
												<h3><?php echo __('Mots clés'); ?></h3>
												<ul id="keywordList">
													<?php
													$keywords = $version['_source']['Keywords'];
													uasort($keywords, 'cmpKeyword');

													foreach ($keywords as $keyword) :?>
													<?php echo $this->Html->tag('li',
															$this->Html->link(
																$keyword['name_'.Configure::read('Config.language')],
																[
																		'action' => 'keyword',
																		$keyword['uuid'],
																		'language' => Configure::read('Config.language')
													]
																											)
													); ?>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>

												<?php if(count($version['_source']['locations']) > 0) :

                          $locations = $version['_source']['locations'];
                          uasort($locations, 'cmpLocation');

                          ?>
												<h3><?php echo __('Emplacements'); ?></h3>
												<ul id="placesList">
													<?php foreach ($locations as $place) :?>
													<?php echo $this->Html->tag('li',
															$this->Html->link(
																(isset($place['name_'.Configure::read('Config.language')])? $place['name_'.Configure::read('Config.language')] : $place['name']),
																[
																	'action' => 'place',
																	$place['uuid'],
																	'language' => Configure::read('Config.language'),
																]
																											)
													); ?>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>

												<h3>Détails</h3>
												<ul id="detailList">
													<li>Photographe: <?php
																		if(isset($version['_source']['artist'])){
																			echo $version['_source']['artist'];
																		}
																		else {
																			echo __('Simon Schmitt');
																		}
													?></li>
													<li><?php echo __('Date:')?> <time datetime="<?php echo date("Y-m-d H:m:s", strtotime($version['_source']['date'])); ?>"><?php echo date("d M Y",strtotime($version['_source']['date'])); ?></time></li>
													<l><?php echo __('Dimensions:')?> <?php
																		if(isset($version['_source']['pixel_size'])){
																			echo $version['_source']['pixel_size'];
																		}
																		else {
																			echo __('Non défini');
																		}
													?></li>
												</ul>
												<div id="map-ctp">
													<div class="outer">
														<div class="inner map-div" id="map-canvas-<?php echo $idx;?>" data-lat="<?php echo $version['_source']['location']['lat'];?>" data-lng="<?php echo $version['_source']['location']['lon'];?>" >
															<?php echo __('Connection à Google Map en cours ...'); ?>
														</div>
														<img src="data:image/gif;base64,R0lGODlhEAAJAIAAAP///wAAACH5BAEAAAAALAAAAAAQAAkAAAIKhI+py+0Po5yUFQA7" class="scaling-image" /> <!-- don't specify height and width so browser resizes it appropriately -->
													</div>
												</div>
												<div id="price-ask">
												<?php echo $this->Html->link(
														'Demande de prix',
														array(
															'action' => 'price',
															$version['_source']['uuid']
														),
														array(
															'role' => 'button',
															'class' => 'btn btn-default btn-primary hvr-underline-from-center',
												));
												?>
												</div>
											</div>
										</div>


									</div>
									<?php endforeach; ?>
								</div>
								</div><!-- /.carousel -->
							</div>
							</div><!-- /end infos -->
							</div><!-- / #galerie-filmstrip-->
