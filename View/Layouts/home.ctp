<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language'); ?>">
	<head>
		<?php echo $this->Html->charset(); ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php
			echo $this->Html->meta('description', __('Site web GlobalView.be; photographies aériennes et reportages aériens tridiemsionnels et multi-angle sur-mesure. Accès à la photothèque de plusieurs millions de clichés en Belgique et au Grand Duché du Luxembourg et planning des prochains vols.'));
			echo $this->Html->meta('icon');
		?>
		
		<title><?php echo __('Bienvenu, GlobalView reportage aérien sur-mesure'); ?></title>
		<!-- vegas css -->
		<?php echo $this->Html->css('vegas'); ?>
		<!-- Bootstrap core CSS -->
		<?php echo $this->Html->css('styles'); ?>
	</head>
	
	<body id="homepage">
		<?php echo $this->element('main/header'); ?>
		<div class="container-fluid">
			
			
			<!--  parallax -->
			<a id="flights"></a>
			<div class="spacer">
				<div class="box1 box">
					<div class="row" id="sortableFlights">
						<div class="flight-wrapper container">
							<h1 class="col-lg-12 homepage" id="vols"><?php echo __('Nos prochains vols'); ?></h1>
							<?php
							//print_r($flights);
							foreach($flights as $idx => $flight):{
								echo '<div class="box-home col-md-4 col-sm-6">';
										$content = $this->Html->tag(
												'h2',											
												$this->Html->tag(
												'span',
												$flight['Flight']['name']));
										$content .= $this->Html->tag(
												'div',
												$flight['Flight']['body'],
												array(
														'class' => 'text-wrapper'
												));
										$content .= $this->Html->tag(
												'div',
												$this->Html->link(__(
														'Ce vol déjà à partir de %s',
														$this->Number->currency($flight['Flight']['cost'], __('EUR'))),
														array(
																'controller' => 'flights',
																'action' => 'book',
																'language' => Configure::read('Config.language'),
																$flight['Flight']['id']
																
														), array('class' => 'btn btn-default btn-primary pull-right hvr-underline-from-center', 'role'=>'button')),
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
									echo '</div>';
								endforeach;
							?>
							
							</div> <!-- /row -->
							</div> <!-- /.flight-wrapper -->
							<div class="row">
								<?php
										if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'){
											echo $this->Html->tag('div',
												$this->Html->link(__('Ajouter un vol'), array(
													'controller' => 'flights',
													'action' => 'add',
													'language' => Configure::read('Config.language'),
													'?' => array(
															'controller' => 'pages',
															'action' => 'home',
															'language' => Configure::read('Config.language')
													)
												), array(
													'class' => 'btn btn-default btn-primary btn-xs',
													'role' => 'button',
												)),
												array(
														'class' => 'col-sm-12 form-group'
												));
												
												}
										
								?>
								</div><!-- /.row -->
								<?php echo $this->Session->flash(); ?>
								<?php echo $this->fetch('content'); ?>
							</div>
						</div>
						
						<div class="spacer s1">
							<div class="box2 box container">
								<div class="row">
									<h2 class="col-lg-12 parallax"><?php echo __('Notre photothèque: %s photos réparties dans %s séries', $this->Number->format($photoCount, [
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
								<div class="row">
											<?php echo $this->element('searchs'); ?>
								</div><!-- /.row -->
							</div>
						</div>
									<div id="parallax1" class="parallaxParent hidden-xs">
										<div></div>
									</div>
									<div class="spacer s1 s2">
										<div class="box3 box container">
											
											<div class="row" id="gallerySection">
												<h2 class="col-lg-12 parallax"><?php echo __('Notre selection de galeries'); ?></h2>
											</div>
											
											<div class="row" id="sortableGallery">
												<?php echo $this->element('galleries', array('galleries', $galleries)); ?>
											</div>
										</div>
									</div>
									<div id="parallax2" class="parallaxParent hidden-xs">
										<div></div>
									</div>
									<!-- /parallax -->
									<?php echo $this->element('main/footer'); ?>
									</div> <!-- /.constainer-fluid -->
								</body>
							</html>