	<nav class="navbar navbar-default" id="nav">
      <div class="container-fluid">
        <div class="navbar-header">
              <button id="navbar-toggle" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only"><?php echo __('Passer le menu de naviguation'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
					<?php 
						echo $this->Html->link(
							$this->Html->image('logo-global-view.png', array(
								'alt' => __('Retour à la page d\'accueil'),
								'class' => 'img-responsive'
						)),
						array('controller' => 'pages', 'action' => 'home', 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'navbar-brand')
						);
					?>
	            
             </div>
             <ul class="nav navbar-nav lang pull-left visible-sm visible-md visible-lg">
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
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
				<li class="dropdown">
                <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle hvr-sweep-to-top" href="#"><?php echo __('Services'); ?><span class="caret"></span></a>
                <ul class="dropdown-menu">
                <?php echo $this->Html->tag('li', $this->Html->link(__('Services'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => 'services',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-services')));?>
                  <?php echo $this->Html->tag('li', $this->Html->link(__('Techniques'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => 'technics',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-technics')));?>
                  <?php echo $this->Html->tag('li', $this->Html->link(__('Prix'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => 'prices',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-prices')));?>	
                </ul>
              </li>
			<li class="dropdown">
                <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle hvr-sweep-to-top" href="#"><?php echo __('Photothèque'); ?><span class="caret"></span></a>
                <ul class="dropdown-menu">
               <?php echo $this->Html->tag('li', $this->Html->link(__('Mots-clés'),
						array('controller' => 'keywords', 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'menu-library hvr-sweep-to-top')));?>
				
				<?php echo $this->Html->tag('li', $this->Html->link(__('Carte'),
						array('controller' => 'map', 'action' => 'index', 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'menu-map hvr-sweep-to-top')));?> 	
                </ul>
              </li>
							
		        
				
		        <?php echo $this->Html->tag('li', $this->Html->link(__('Vols'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => 'flights',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-flights hvr-sweep-to-top')));?>
	
		        <?php echo $this->Html->tag('li', $this->Html->link(__('Contact'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => 'contact',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-contact hvr-sweep-to-top')));?>	
              </ul> 
		      <ul class="nav navbar-nav visible-xs">
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
		      <div class="visible-xs-block">
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
		      <div class="glyphicon glyphicon-search pull-right search-trigger hidden-xs "></div>
			</div>
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
			       <button type="submit" class="btn btn-default" ><span class="glyphicon glyphicon-search"></span></button>
			      </form>	
		      </div>	  
          </div>
        </nav>