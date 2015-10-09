
  <div class="navbar navbar-default " id="nav" data-spy="affix" data-offset-top="300">
  <!-- navbar-fixed-bottom-->
      <div class="container-fluid">
        <div class="navbar-header">
              <button id="navbar-toggle" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
					<?php 
						echo $this->Html->link(
							$this->Html->image('GlobalView.png', array(
								'alt' => $this->i18n->t('layout.logo-alt'),
								'class' => 'img-responsive'
						)),
						array('controller' => 'pages', 'action' => 'home', 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'navbar-brand')
						);
					?>
	            
             </div>
            <div id="navbar" class="navbar-collapse collapse pull-right">
			
			<!-- lang 
			<ul class="nav nav-pills">
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
						echo $this->Html->tag('li', $this->Html->link($this->Html->tag('abbr', $code, array('title' => $language, 'lang' => $code, 'role' => 'presentation')), $param, array('escape' => false, 'class' => 'btn btn-primary btn-sm')));
					}
				}
			?>
			</ul>
			/end lang -->
              <ul class="nav navbar-nav">
               <?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.services'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => '',
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-services')));?>
							
                <?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.technics'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => $this->i18n->t('our-service.technicsAnchor'),
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-technics')));?>
							
		        <?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.library'),
						array('controller' => 'galleries', 'action' => 'library', 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'menu-library')));?>
				<?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.map'),
						array('controller' => 'aperture', 'action' => 'map', $this->i18n->t('menu.belgium'), 'placeId' => 26, 'language' => Configure::read('Config.language')),
						array('escape' => false, 'class' => 'menu-map')));?> 
				
                <?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.prices'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => $this->i18n->t('our-service.pricesAnchor'),
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-prices')));?>	
							
		        <?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.flights'), array(
							'controller' => 'pages',
							'action' => 'home',
		            		'#' => $this->i18n->t('home.flightsAnchor'),
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-flights')));?>
	
		        <?php echo $this->Html->tag('li', $this->Html->link($this->i18n->t('menu.contact'), array(
							'controller' => 'pages',
							'action' => 'display',
							'our-services',
		            		'#' => $this->i18n->t('our-service.contactAnchor'),
							'language' => Configure::read('Config.language')),
							array('class' => 'menu-contact')));?>	
              </ul> 
              
              <form action="<?php 
		      	echo $this->Html->url(
					array(
						'controller' => 'galleries',
						'action' => 'search',
						'language' => Configure::read('Config.language'))); 
				?>" class="navbar-form navbar-right" role="search" method="GET">
				<span class="glyphicon glyphicon-search pull-right search-trigger"></span>
		        <div class="form-group search-toggle pull-right">

		          <input type="text" class="form-control QuickSearchInput" placeholder="<?php echo $this->i18n->t('menu.quickSearch'); ?>" name="q" data-search-url="<?php 
				      	echo $this->Html->url(
								array(
									'controller' => 'galleries',
									'action' => 'search',
									'language' => Configure::read('Config.language'),
									'ext'=> 'json'
				      	)); 
							?>">
				</div>
		       <!-- <button type="submit" class="btn btn-default" ><span class="glyphicon glyphicon-search"></span></button>-->
		      </form>
		
			  
		      <ul class="nav navbar-nav pull-right visible-xs">
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
			</div>
          </div>
        </div>