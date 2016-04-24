<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language'); ?>">
	<head>
		<?php echo $this->Html->charset(); ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php
			echo $this->Html->meta('description', __('Site web GlobalView.be; photographies aériennes et reportages aériens tridimensionnels et multi-angle sur-mesure. Accès à la photothèque de plusieurs millions de clichés en Belgique et au Grand Duché du Luxembourg et planning des prochains vols.'));
			echo $this->Html->meta('icon');
		?>

		<title><?php echo __('Bienvenue, GlobalView reportage aérien sur mesure'); ?></title>
		<!-- fontawesome css -->
		<?php echo $this->Html->css('font-awesome/font-awesome.min'); ?>
		<!-- animate css -->
		<?php echo $this->Html->css('animate/animate.min'); ?>

		<!-- google font -->
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<!-- Bootstrap core CSS -->
		<?php echo $this->Html->css('styles'); ?>
	</head>

	<body id="homepage" class="slider-background">
		<!-- Preloader 	-->
		<div id="preloader"
			<div id="status">&nbsp;</div>
		</div>
		<!-- ./Preloader -->

		<!-- pattern -->
		<div id="bg_pattern"></div>
		<!-- ./pattern -->

		<!-- scrollToTop -->	
		<a href="#" class="scrollToTop">
			<i class="fa fa-angle-up fa-2x"></i>
		</a>
		<!-- ./scrollToTop -->
		<?php echo $this->element('main/header', array('home' => $home)); ?>

		<!-- vols -->
		<section id="flights" class="section wow fadeInUp">
			<div class="container">	
				<div class="section-heading">
					<h2><?php echo __('Les avantages de l\'hélico, le tarif du drone.'); ?></h2>
						<?php
						//print_r($flights);
						foreach($flights as $idx => $flight):{
							echo '<div class="col-md-4 "><div class="box">';
									$content = $this->Html->tag(
											'h3',					
											$flight['Flight']['name']);
									$content .= $this->Html->tag(
											'div',
											$flight['Flight']['body'],
											array(
													'class' => 'text-wrapper'
											));
									$content .= $this->Html->tag(
											'div',
											$this->Html->link(__(
													'A partir de %s',
													$this->Number->currency($flight['Flight']['cost'], __('EUR'))),
													array(
															'controller' => 'flights',
															'action' => 'book',
															'language' => Configure::read('Config.language'),
															$flight['Flight']['id']

													), array('class' => 'btn btn-custom btn-lg', 'role'=>'button')),
											array('class'=>'form-group'));


									$divOptions = array(
											'class'=>'groupToMatch clearfix',
											);

									if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'){

										$divOptions['data-object'] = json_encode($flight);
										$divOptions['data-url'] = $this->Html->url(array(
													'controller' => 'Flights',
													'action' => 'update',
													'ext' => 'json',
													$flight['Flight']['id']
											));

										$links = $this->Html->link(__('Editer'), array(
												'controller' => 'flights',
												'action' => 'edit',
												'language' => Configure::read('Config.language'),
												$flight['Flight']['id'],
												'?' => array(
														'controller' => 'pages',
														'action' => 'home',
														'language' => Configure::read('Config.language')
												)
										), array(
												'class' => 'btn btn-default btn-primary',
												'role' => 'button',
										));
										$links .= $this->Form->postLink(__('Publier'), array(
												'controller' => 'flights',
												'action' => 'publish',
												'language' => Configure::read('Config.language'),
												$flight['Flight']['id'],
												'?' => array(
														'controller' => 'pages',
														'action' => 'home',
														'language' => Configure::read('Config.language')
												)
										), array(
												'class' => ('btn btn-default btn-primary'.($flight['Flight']['published']?' hidden':'')),
												'role' => 'button',
										));
										$links .= $this->Form->postLink(__('Dépublier'), array(
												'controller' => 'flights',
												'action' => 'publish',
												'language' => Configure::read('Config.language'),
												$flight['Flight']['id'],
												'?' => array(
														'controller' => 'pages',
														'action' => 'home',
														'language' => Configure::read('Config.language')
												)
										), array(
												'class' => ('btn btn-default btn-primary'.($flight['Flight']['published']?'':' hidden')),
												'role' => 'button',
										));
										$links .= $this->Form->postLink(__('Effacer'), array(
												'controller' => 'flights',
												'action' => 'delete',
												'language' => Configure::read('Config.language'),
												'?' => array(
														'controller' => 'pages',
														'action' => 'home',
														'language' => Configure::read('Config.language')
												),
												$flight['Flight']['id']
										), array(
												'class' => 'btn btn-danger btn-primary',
												'role' => 'button',
										), __('Etes vous sûr de vouloir effacer la galerie %s?', $flight['Flight']['name']));

										$content .= $this->Html->tag('div', $this->Html->tag('div', $links, array(
												'class' => 'btn-group btn-group-xs',
												'role' => 'group',
										)), array('class' => 'form-group admin'));
									}

									echo $this->Html->tag('div', $content, $divOptions);
								}
								echo '</div></div>';
							endforeach;
							?>
							<?php echo $this->Session->flash(); ?>
							<?php echo $this->fetch('content'); ?>

				</div>
			</div>
		</section>

		<!-- services -->
		<section id="services" class="section wow fadeInUp">
			<div class="container">	
				<div class="section-heading">
					<h2><?php echo __('Notre photothèque: %s photos réparties dans %s séries', $this->Number->format($photoCount, [
											'places' => 0,
										    'before' => '',
										    'escape' => false,
										    'decimals' => ',',
										    'thousands' => '.']),
										$this->Number->format($serieCount, [
											'places' => 0,
										    'before' => '',
										    'escape' => false,
										    'decimals' => ',',
										    'thousands' => '.'])); ?></h2>
						
				</div>
				<?php echo $this->element('searchs'); ?>
			</div>
		</section>	
		<!-- ./services -->
		<!-- gallery -->
		<section id="gallery" class="section wow fadeInUp">
			<div class="container">	
			
				<div class="section-heading">
					<h2><?php echo __('Collections à la Une'); ?></h2>					
				</div>			
				<div class="row">										
					<?php echo $this->element('galleries', array('galleries', $galleries)); ?>	
				</div><!-- ./row -->																												
			</div>
		</section>
		<!-- ./gallery -->

		<!-- technics -->
		<section id="technics" class="section wow fadeInUp">
			<div class="container">			
				<div class="section-heading">
					<h2><?php echo __('La technique'); ?></h2>					
				</div>				
				<div class="row">
					<div class="col-md-12">
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingOne">
					      <h4 class="panel-title">
					        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#technicsOne" aria-expanded="true" aria-controls="technicsOne">
					          <?php echo __('Le photographe'); ?>
					        </a>
					      </h4>
					    </div>
					    <div id="technicsOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
					      <div class="panel-body">
					        <?php echo $this->i18n->w('our-service.techniques.photo');  ?>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingTwo">
					      <h4 class="panel-title">
					        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#technicsTwo" aria-expanded="false" aria-controls="technicsTwo">
					          <?php echo __("L'hélicoptère"); ?>
					        </a>
					      </h4>
					    </div>
					    <div id="technicsTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
					      <div class="panel-body">
					       <?php echo $this->i18n->w('our-service.techniques.helico');  ?>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingThree">
					      <h4 class="panel-title">
					        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#technicsThree" aria-expanded="false" aria-controls="technicsThree">
					          <?php echo __("La météo"); ?>
					        </a>
					      </h4>
					    </div>
					    <div id="technicsThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
					      <div class="panel-body">
					        <?php echo $this->i18n->w('our-service.techniques.meteo');  ?>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingThree">
					      <h4 class="panel-title">
					        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#technicsFour" aria-expanded="false" aria-controls="technicsFour">
					          <?php echo __("Les délais"); ?>
					        </a>
					      </h4>
					    </div>
					    <div id="technicsFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
					      <div class="panel-body">
					        <?php echo $this->i18n->w('our-service.techniques.delais');  ?>
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingThree">
					      <h4 class="panel-title">
					        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#technicsFive" aria-expanded="false" aria-controls="technicsFive">
					          <?php echo __("La vidéo aérienne"); ?>
					        </a>
					      </h4>
					    </div>
					    <div id="technicsFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
					      <div class="panel-body">
					        <?php echo $this->i18n->w('our-service.techniques.video');  ?>
					      </div>
					    </div>
					  </div>
					</div>

					</div>
				</div><!-- ./row -->
					
			</div>
	
		</section>
		<!-- ./pricing -->

		<!-- pricing -->
		<section id="pricing" class="section wow fadeInUp">
			<div class="container">			
				<div class="section-heading">
					<h2><?php echo __('Les tarifs'); ?></h2>					
				</div>				
				<div class="row">
					<div class="col-md-12">
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingOne">
					      <h4 class="panel-title">
					        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					          <?php echo $this->i18n->w('our-service.priceTitle1');  ?>
					        </a>
					      </h4>
					    </div>
					    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
					      <div class="panel-body">
					        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingTwo">
					      <h4 class="panel-title">
					        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
					          <?php echo $this->i18n->w('our-service.priceTitle2');  ?>
					        </a>
					      </h4>
					    </div>
					    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
					      <div class="panel-body">
					        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
					      </div>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingThree">
					      <h4 class="panel-title">
					        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
					          <?php echo $this->i18n->w('our-service.priceTitle3');  ?>
					        </a>
					      </h4>
					    </div>
					    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
					      <div class="panel-body">
					        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
					      </div>
					    </div>
					  </div>
					</div>

					</div>
				</div><!-- ./row -->
					
			</div>
	
		</section>
		<!-- ./pricing -->

		<!-- contact -->
		<section id="contact" class="section wow fadeInUp">
			<div class="container">
			
				<div class="section-heading">
					<h2><?php echo __('Contactez-nous'); ?></h2>
						
				</div>
				
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
					<?php echo $this->element('contact'); ?>		
										
					</div>
				</div>			
			</div>	
		</section>
		<!-- ./contact -->
		<?php echo $this->element('main/footer'); ?>
	</body>
</html>


		