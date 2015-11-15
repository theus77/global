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
				<?php foreach ($versions as $idx => $version) : ?>
				<a href="#" class="thumbnail"  data-target="#GalerieCarousel" data-slide-to="<?php echo $idx; ?>"><?php
					echo $this->Html->image(
					($version['Version']['encodedUuid']).'/thumb.png',
					array('class' => 'img-responsive'));
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
						foreach ($versions as $idx => $version){
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
						foreach ($versions as $idx => $version){
							echo $this->Html->tag('div',
								//$this->Html->link(
									$this->Html->image(urlencode($version['Version']['encodedUuid']).'/preview', array('data-src' => '/img/'.urlencode($version['Version']['encodedUuid']).'/image', 'class' => 'lazy toLoad preview'))
									//$this->Html->image($version['Version']['encodedUuid'].'/preview.png'),
									//array('controller' => 'aperture', 'action' =>  'image', $version['Version']['encodedUuid'], 'language' => Configure::read('Config.language')),
									//array('escape' => false)),
									.$this->Html->tag('div', 'Chargement en cours...', array('class' => 'carousel-caption')),
								array('class' => $idx?'item':'item active', 'data-image-info' =>
									$this->Html->url(array(
										'controller' => 'aperture',
										'action' =>  'image',
										'language' => Configure::read('Config.language'),
										$version['Version']['encodedUuid'],
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
							<?php foreach ($versions as $idx => $version) : ?>
								<?php foreach ($version['Stack'] as $idxStack => $stack) : ?>
								<?php
									
									echo $this->Html->link(
											$this->Html->image(
												($version['Version']['encodedUuid']).'/thumb.png',
												array('class' => 'img-responsive lazy-thumb',
												'data-src' => $this->Html->url(
													'/img/'.urlencode($stack['Version']['encodedUuid']).'/thumb.png'))),
											'#',
											array(
																	'class' => 	'thumbnail',
												'onclick' => 'javascript: return loadStackImage(this);',
												'escape' => false,
												'data-stack-uuid' => $this->Html->url('/img/'.urlencode($stack['Version']['encodedUuid']).'/image'),
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
					<div id="galerie-info">
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
									<?php foreach ($versions as $idx => &$version) : ?>
									<div class="<?php echo $idx?'item':'item active'; ?>">
										
										
										<div class="wrapper-info">
											<div class="glyphicon glyphicon-move"></div>
											<h2 id="versionTitle">

											<?php
															if(isset($properties[$version['Version']['modelId']]['ObjectName'])){
																echo $properties[$version['Version']['modelId']]['ObjectName'];
															}
															else {
																echo $version['Version']['name'];
															}
											?>
											<span></span>
											</h2>
											
											
											<div class="infos">
												<?php if(count($version['keywords']) > 0) : ?>
												<h3>Mots clés</h3>
												<ul id="keywordList">
													<?php foreach ($version['keywords'] as $keywordId => $keyword) :?>
													<?php echo $this->Html->tag('li',
															$this->Html->link(
																$keyword,
																$this->App->getGalleryUrl($keywordId)
																											)
													); ?>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>
												
												<?php if(count($version['places']) > 0) : ?>
												<h3>Emplacements</h3>
												<ul id="placesList">
													<?php foreach ($version['places'] as $placeid => $place) :?>
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
																		if(isset($properties[$version['Version']['modelId']]['Byline'])){
																			echo $properties[$version['Version']['modelId']]['Byline'];
																		}
																		else {
																			echo __('Simon Schmitt');
																		}
													?></li>
													<li>Date: <time datetime="<?php echo date("Y-m-d H:m:s", $version['Version']['unixImageDate']); ?>"><?php echo date("d M Y", $version['Version']['unixImageDate']); ?></time></li>
													<li>Dimensions: <?php
																		if(isset($properties[$version['Version']['modelId']]['PixelSize'])){
																			echo $properties[$version['Version']['modelId']]['PixelSize'];
																		}
																		else {
																			echo __('Non défini');
																		}
													?></li>
												</ul>
												<div id="map-ctp">
													<div class="outer">
														<div class="inner map-div" id="map-canvas-<?php echo $idx;?>" data-lat="<?php echo $version['Version']['exifLatitude'];?>" data-lng="<?php echo $version['Version']['exifLongitude'];?>" >
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
															$version['Version']['encodedUuid']
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
										<hr>
										<h2><?php echo __('Administration');?></h2>
										<!-- Form Name -->
										<h2><?php echo __('Configuration de la galerie');?></h2>
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
										
										<?php endif;?>
										
									</div>
									<?php endforeach; ?>
								</div>
								</div><!-- /.carousel -->
							</div>
							</div><!-- /end infos -->
							</div><!-- / #galerie-filmstrip-->