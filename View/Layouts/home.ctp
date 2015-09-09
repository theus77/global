<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language'); ?>">
  <head>
    <?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    	echo $this->Html->meta('description', $this->i18n->t('site.description'));
    	echo $this->Html->meta('author', $this->i18n->t('site.author'));
    	echo $this->Html->meta('icon'); 
    ?>
     
    <title><?php echo $this->i18n->t('site.title').' - '.$this->i18n->t('home.title'); ?></title>

    <!-- Bootstrap core CSS -->
    <?php echo $this->Html->css('styles'); ?>

  </head>
  
  <body id="homepage">
	<?php echo $this->element('main/header'); ?>
  	
  	
	    <div class="row">
	        <h2 class="col-lg-12 parallax"><?php echo $this->i18n->t('home.mojo'); ?></h2>
	    </div>
  	    <div class="row">
	        <h3 class="col-lg-12 homepage" id="vols"><?php echo $this->i18n->t('home.nextflights'); ?></h3>
	    </div>
  		    
      <!-- Three columns of text below the carousel -->
      <div class="row" id="sortableFlights">
      
		<?php  
		//print_r($flights);
		foreach($flights as $idx => $flight):{
				$content = $this->Html->tag(
						'h3', 
						$flight['Flight']['name']);
				$content .= $this->Html->tag(
						'div', 
						$flight['Flight']['body'], 
						array(
								//'class' => 'text-justify'
						));
				$content .= $this->Html->tag(
						'div', 
						$this->Html->link($this->i18n->t(
								'home.flightFrom', 
								$flight['Flight']['cost']), 
								array(
										'controller' => 'flights', 
										'action' => 'book', 
										'language' => Configure::read('Config.language'), 
										$flight['Flight']['id']
										
								), array('class' => 'btn btn-default btn-primary', 'role'=>'button')),
						array('class'=>'form-group'));
				
				
				$divOptions = array(
						'class'=>'col-md-4 text-center groupToMatch jumbotron',
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
					)), array('class' => 'form-group'));
				}
				
				echo $this->Html->tag('div', $content, $divOptions);
				
				
				/*
				
				
				$content = $this->Html->link(
						$this->Html->image('thumbnails/'.$gallery['Gallery']['thumbUuid'].'/thumbs.png', array('alt'=>$gallery['Gallery']['name'])).
							$this->Html->tag(
								'div',
								$gallery['Gallery']['name'].' '.
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
							'class' => 'col-sm-6 col-md-4 col-lg-3 ui-state-default',
							'data-url' => $this->Html->url(array(
									'controller' => 'Galleries',
									'action' => 'edit',
									'ext' => 'json',
									$gallery['Gallery']['id']
							)),
							'data-object' => json_encode($gallery),
				));*/
			}	 
			endforeach; ?>      
      
    
      </div><!-- /.row -->  	
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
			<?php echo $this->element('main/nav'); ?>

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>

	  	<div class="row">
	        <h2 class="col-lg-12 parallax"><?php echo $this->i18n->t('home.searchInLibrary'); ?></h2>
	    </div>	

      <div class="row">
        <div class="col-md-4">
        	<?php echo $this->i18n->w('home.geographic-search'); ?>
			<?php echo $this->Html->tag('p', $this->Html->link($this->i18n->t('home.geographicSearch'),
								array('controller' => 'aperture', 'action' => 'map', 'language' => Configure::read('Config.language')),
								array('escape' => false, 'class' => 'btn btn-default btn-primary', 'role' => 'button')));?>
        </div><!-- /.col-md-4 -->
       <div class="col-md-4">
        <?php echo $this->i18n->w('home.free-text-search'); ?>  
       	<form action="<?php 
		      	echo $this->Html->url(
					array(
						'controller' => 'galleries',
						'action' => 'search',
						'language' => Configure::read('Config.language'))); 
				?>" class="navbar-form navbar-right" role="search" method="GET">
		        <div class="form-group">
		          <input type="text" class="form-control QuickSearchInput" placeholder="<?php echo $this->i18n->t('home.quickSearch'); ?>" name="q" data-search-url="<?php 
				      	echo $this->Html->url(
								array(
									'controller' => 'galleries',
									'action' => 'search',
									'language' => Configure::read('Config.language'),
									'ext'=> 'json'
				      	)); 
							?>">
		        </div>
		        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
		      </form>
        </div><!-- /.col-md-4 -->
       <div class="col-md-4">
	       <?php echo $this->i18n->w('home.thematic-search'); ?>   
	       <?php echo $this->Html->link($this->i18n->w('home.thematic-search-button'),
							array('controller' => 'galleries', 'action' => 'library', 'language' => Configure::read('Config.language')),
							array('escape' => false, 'class' => 'btn btn-default btn-primary', 'role' => 'button'));?></p>
        </div><!-- /.col-md-4 -->
        
      </div><!-- /.row -->  	
	    
	    
	<div class="row" id="gallerySection">
		<h2 class="col-lg-12 parallax"><?php echo $this->i18n->t('home.galerySelection'); ?></h2>
	</div>	
	    
	<div class="row" id="sortableGallery">
			<?php echo $this->element('galleries', array('galleries', $galleries)); ?>
	</div>

	<?php echo $this->element('main/footer'); ?>
	
</body>
</html>
