
			  <div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
				  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapse-navbar" aria-expanded="false">
					<span class="sr-only"><?php echo __('Passer le menu de navigation'); ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </button>
				  <?php 
						echo $this->Html->link(
							$this->Html->image('logo.png', array(
								'alt' => __('Retour à la page d\'accueil')
						)),
						array('controller' => 'pages', 'action' => 'home', 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'navbar-brand')
						);
					?>
					<!-- lang switcher -->
					<ul class="nav navbar-nav lang pull-left visible-md visible-lg">
					<?php 
						foreach(Configure::read('Config.languages') as $code => $language) { // show links for translated version
							if(strcmp(Configure::read('Config.language'), $code)!==0){
								$param = array(
									'language' => $code,
									'controller' => $this->params->params['controller'],
									'action' => $this->params->params['action']);
								foreach($this->params->params['pass'] as $pass){
									$param[] = $pass;
								}
								$param['language'] = $code;		
								echo $this->Html->tag('li', $this->Html->link($this->Html->tag('abbr', $code, array('title' => $language, 'lang' => $code, 'role' => 'presentation')), $param, array('escape' => false, 'class' => '')));
							}
						}
					?>
				</ul>
				<!-- / lang switcher -->
				</div>
				<div class="collapse navbar-collapse" id="collapse-navbar">
				 <!-- menu nav -->
				  <ul class="nav navbar-nav navbar-right">
					<?php 
						if (isset($home)) { 
							$scroll = true;
						} else {
							$scroll = false;
						}
					?>
					<li class="dropdown">
                		<a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle menu-services" data="data-sroll" href="#"><?php echo __('Services'); ?><span class="caret"></span></a>
                		<ul class="dropdown-menu">
                		<?php echo $this->Html->tag('li', $this->Html->link(__('Services'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => 'services',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-services')));?>
                  		<?php echo $this->Html->tag('li', $this->Html->link(__('Techniques'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => 'technics',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-technics')));?>
                  		<?php echo $this->Html->tag('li', $this->Html->link(__('Prix'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => 'pricing',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-prices', 'data-scroll'=>$scroll)));?>	
                		</ul>
		              </li>
						<li class="dropdown">
		                <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><?php echo __('Photothèque'); ?><span class="caret"></span></a>
		                <ul class="dropdown-menu">
		               <?php echo $this->Html->tag('li', $this->Html->link(__('Mots-clés'),
								array('controller' => 'keywords', 'language' => Configure::read('Config.language')),
								array('escape' => false, 'class' => 'menu-library')));?>
						
						<?php echo $this->Html->tag('li', $this->Html->link(__('Carte'),
								array('controller' => 'map', 'action' => 'index', 'language' => Configure::read('Config.language')),
								array('escape' => false, 'class' => 'menu-map')));?> 	
		                </ul>
		              </li>
              			<?php echo $this->Html->tag('li', $this->Html->link(__('Vols'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => 'flights',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-flights', 'data-scroll'=>$scroll)));?>
	
		        		<?php echo $this->Html->tag('li', $this->Html->link(__('Contact'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => 'contact',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-contact', 'data-scroll'=>$scroll)));?>	
							<li class="search-trigger hidden-xs hidden-sm"><span class="fa fa-search"></span><a href="#" class="sr-only"><?php echo __('Recherche'); ?></a>
						</li>				
				  	</ul>
				  	<!-- /menu nav -->
				  <!-- lang desktop -->
				  <ul class="nav navbar-nav visible-xs visible-sm">
			      <?php 
						foreach(Configure::read('Config.languages') as $code => $language) { // show links for translated version
							if(strcmp($code, Configure::read('Config.language')) !==0 ){
								$param = array(
									'language' => $code,
									'controller' => $this->params->params['controller'],
									'action' => $this->params->params['action']);
								foreach($this->params->params['pass'] as $pass){
									$param[] = $pass;
								}
								$param['language'] = $code;		
								echo $this->Html->tag('li', $this->Html->link($language, $param));
							}
						}
				?>
		      	</ul>
		      	<!-- /lang desktop -->
		      	<!-- search mobile -->
		      <div class="visible-xs-block visible-sm-block">
		      <form action="<?php 
			      	echo $this->Html->url(
						array(
							'controller' => 'galleries',
							'action' => 'search',
							'language' => Configure::read('Config.language'))); 
					?>" class="navbar-form" method="GET">
			        <div class="form-group search-toggle">
			          <input type="text" class="form-control QuickSearchInput" placeholder="<?php echo __('Recherche rapide'); ?>" name="q" data-search-url="<?php 
					      	echo $this->Html->url(
									array(
										'controller' => 'galleries',
										'action' => 'search',
										'language' => Configure::read('Config.language'),
										'ext'=> 'json'
					      	)); 
								?>">
					</div>
			       <button type="submit" class="btn btn-default" ><span class="glyphicon glyphicon-search"></span></button>
			  </form>
			  </div>	
			  <!-- /search mobile -->
			</div>
			<!-- search mobile -->
			<div class="search-form hidden-xs-block">
	              <form action="<?php 
			      	echo $this->Html->url(
						array(
							'controller' => 'galleries',
							'action' => 'search',
							'language' => Configure::read('Config.language'))); 
					?>" class="navbar-form" method="GET">
			        <div class="form-group search-toggle">
			          <input type="text" class="form-control QuickSearchInput" placeholder="<?php echo __('Recherche rapide'); ?>" name="q" data-search-url="<?php 
					      	echo $this->Html->url(
									array(
										'controller' => 'galleries',
										'action' => 'search',
										'language' => Configure::read('Config.language'),
										'ext'=> 'json'
					      	)); 
								?>">
					</div>
			       <button type="submit" class="btn btn-default" ><span class="fa fa-search"></span></button>
			      </form>	
		      	</div>
		      	<!-- /search mobile -->	  
			  </div><!-- /.container -->
	