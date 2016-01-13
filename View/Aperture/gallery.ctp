<script type="text/javascript" src="<?php echo 'https://maps.googleapis.com/maps/api/js?key='.Configure::read('googleApiKey');?>"></script>
<?php
	$this->assign('title', $title);
?>
<div id="galerie-filmstrip" class="clearfix">
	<div id="galerie-thumb-bro">
		<div class="col-md-12 galerie-thumb">
			<h2 id="intro" class="pull-left"><?php echo $title; ?></h2>
			<div>
				<nav>
					<ul class="pagination pull-right">
						<?php echo $this->Paginator->numbers(array(
								'tag' => 'li',
								'separator' => '',
								'currentClass' => 'active',
								'first' => '«',
								'last' => '»',
								'currentTag' => 'a'
								
						)); ?>
					</ul>
				</nav>
			</div>
			<div class="galerie-thumb-scroll groupToMatch">
				<?php foreach ($versions['hits']['hits'] as $idx => $version) : ?>
				<a href="#" class="thumbnail"  data-target="#GalerieCarousel" data-slide-to="<?php echo $idx; ?>"><?php
					echo $this->Html->image(
					($version['_source']['uuid']).'/thumb',
					array('class' => 'img-responsive', 'escape' => false));
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
				<!-- Indicators -->
				<ol class="carousel-indicators">
					<?php
						foreach ($versions['hits']['hits'] as $idx => $version){
							echo $this->Html->tag('li', '', array(
									'data-target' => '#GalerieCarousel',
									'data-slide-to' => $idx,
									'class' => $idx?'':'active',
							));
						}
					?>
				</ol>
				<div class="carousel-inner" role="listbox">
					<?php
						foreach ($versions['hits']['hits'] as $idx => $version){
							echo $this->Html->tag('div',
								//$this->Html->link(
									$this->Html->image($version['_source']['uuid'].'/preview', array('data-src' => '/img/'.$version['_source']['uuid'].'/image', 'class' => 'lazy toLoad preview'))
									//$this->Html->image($version['Version']['encodedUuid'].'/preview.png'),
									//array('controller' => 'aperture', 'action' =>  'image', $version['Version']['encodedUuid'], 'language' => Configure::read('Config.language')),
									//array('escape' => false)),
									.$this->Html->tag('div', 'Chargement en cours...', array('class' => 'carousel-caption')),
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
								<?php foreach ($version['_source']['Stack'] as $idxStack => $stack) : ?>
								<?php
									
									echo $this->Html->link(
											$this->Html->image(
												($version['_source']['uuid']).'/thumb',
												array('class' => 'img-responsive lazy-thumb',
												'data-src' => $this->Html->url(
													'/img/'.$stack['uuid']).'/thumb')),
											'#',
											array(
																	'class' => 	'thumbnail',
												'onclick' => 'javascript: return loadStackImage(this);',
												'escape' => false,
												'data-stack-uuid' => $this->Html->url('/img/'.$stack['uuid'].'/image'),
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
															if(isset($version['_source']['Properties']['ObjectName'])){
																echo $version['_source']['Properties']['ObjectName'];
															}
															else {
																echo $version['_source']['name'];
															}
											?>
											<span></span>
											</h2>
											
											
											<div class="infos">
												<?php if(count($version['_source']['Keywords']) > 0) : ?>
												<h3>Mots clés</h3>
												<ul id="keywordList">
													<?php foreach ($version['_source']['Keywords'] as $keywordId => $keyword) :?>
													<?php echo $this->Html->tag('li',
															$this->Html->link(
																$keyword,
																$this->App->getGalleryUrl($keywordId)
																											)
													); ?>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>
												
												<?php if(count($version['_source']['Locations']) > 0) : ?>
												<h3>Emplacements</h3>
												<ul id="placesList">
													<?php foreach ($version['_source']['Locations'] as $placeid => $place) :?>
													<?php echo $this->Html->tag('li',
															$this->Html->link(
																$place,
																$this->App->getGalleryUrl($placeid)
																											)
													); ?>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>
												
												<h3>Détails</h3>
												<ul id="detailList">
													<li>Photographe: <?php
																		if(isset($version['_source']['Properties']['Byline'])){
																			echo $version['_source']['Properties']['Byline'];
																		}
																		else {
																			echo __('Simon Schmitt');
																		}
													?></li>
													<li>Date: <time datetime="<?php echo date("Y-m-d H:m:s", $version['_source']['date']); ?>"><?php echo date("d M Y", $version['_source']['date']); ?></time></li>
													<li>Dimensions: <?php
																		if(isset($version['_source']['Properties']['PixelSize'])){
																			echo $version['_source']['Properties']['PixelSize'];
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
										<?php if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'):?>
										<div class="admin-galery">
										<h2><?php echo __('Administration') . " : " . __('Configuration de la galerie');?></h2>
										<?php
										$url = '';
										foreach ($this->params['named'] as $id => $param){
											if(strcmp($id, 'page') != 0 && strcmp($id, 'language')){
												$url .= '/'.$id.':'.$param;
											}
										}
										
										echo $this->element('models/gallery', array(
												'submitLabel' => __('Mettre à jour la galerie'),
												'create' => !isset($this->data['Gallery']),
												'defaultCounter' => $this->Paginator->counter(array('format' => __('{:count}'))),
												'defaultUrl' => $url,
												'defaultThumbUuid' => $version['Version']['encodedUuid']
										)); ?>
										</div>
										<?php endif;?>
										
									</div>
									<?php endforeach; ?>
								</div>
								</div><!-- /.carousel -->
							</div>
							</div><!-- /end infos -->
							</div><!-- / #galerie-filmstrip-->