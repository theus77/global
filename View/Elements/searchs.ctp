			
				<div class="row">
					<div class="col-md-4 col-sm-4">
						<div class="services style2">
							<div class="fa fa-map-marker">
								<?php echo $this->Html->link( 
										'<i class="sr-only">' . __('Recherche géographique') . '</i>', 
										array(
										'controller' => 'map', 
										'action' => 'index',
										'language' => Configure::read('Config.language')), 
										array('escape'=>false));?>
							</div>
						<h4><?php echo $this->Html->link(__('Recherche géographique'),
							array(
							'controller' => 'map', 
							'action' => 'index', 
							'language' => Configure::read('Config.language')));?>
						</h4>
						</div>
					</div>
					<div class="col-md-4 col-sm-4">
						<div class="services style2">
							<div class="fa fa-search">
							<a role="button" data-toggle="collapse" href="#searchCollapse" aria-expanded="false" aria-controls="searchCollapse"><i class="sr-only"><?php echo __('Recherche rapide'); ?></i></a>
							<?php 
							/*echo  '<h4>' .
							$this->Html->link(__('Recherche rapide'),
							array(
							'controller' => 'galleries',
							'action' => 'search', 
							'language' => Configure::read('Config.language')), array('class' => '', 'role' => 'button', 'data-toggle' => 'collapse', 'aria-expanded' => 'false', 'aria-controls' => 'searchCollapse'));?></h4>
							*/
							?>
							<?php $searchUrl = $this->Html->url(
							array(
								'controller' => 'galleries',
								'action' => 'search',
								'language' => Configure::read('Config.language'))) ?>
							</div>
							<h4><a role="button" data-toggle="collapse" href="#searchCollapse" aria-expanded="false" aria-controls="searchCollapse"><?php echo __('Recherche libre'); ?></a>
							<div class="collapse" id="searchCollapse">
								<form action="<?php echo $searchUrl; ?>" class="navbar-form" role="search" method="GET">
									<div class="form-group">
										<input type="text" class="form-control QuickSearchInput" placeholder="<?php echo __('Nom, code postal, ...'); ?>" name="q">
									</div>
									<button type="submit" class="btn btn-default"><span class="fa fa-search"></span></button>
								</form>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4">
						<div class="services style2">
							<div class="fa fa-tags">
								<?php echo $this->Html->link( 
										'<i class="sr-only">' . __('Recherche thématique') . '</i>', 
										array(
											'controller' => 'keywords', 
											'escape' => false,
											'language' => Configure::read('Config.language')), 
											array('escape'=>false));?>

							</div>
							<h4><?php echo $this->Html->link(__('Recherche thématique'), [
									'controller' => 'keywords', 
									'language' => Configure::read('Config.language')
								]);
								?>
							</h4>
						</div>
					</div>
				</div><!-- /.row -->

