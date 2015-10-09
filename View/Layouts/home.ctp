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
	<!-- vegas css -->
	<?php echo $this->Html->css('vegas'); ?>
    <!-- Bootstrap core CSS -->
    <?php echo $this->Html->css('styles'); ?>

  </head>
  
  <body id="homepage">

	<?php echo $this->element('main/header'); ?>
  <div class="container-fluid">
  
  
<!--  parallax -->
<a id="home.flightsAnchor"></a>
<div class="spacer s1">
	<div class="box1">
      <div class="row" id="sortableFlights">
		<div class="flight-wrapper">
			<h1 class="col-lg-12 homepage" id="vols"><?php echo $this->i18n->t('home.nextflights'); ?></h1>
		<?php  
		//print_r($flights);
		foreach($flights as $idx => $flight):{
			echo '<div class="box-home col-md-4">';
				$content = $this->Html->tag(
						'h2', 
						$flight['Flight']['name']);
				$content .= $this->Html->tag(
						'div', 
						$flight['Flight']['body'], 
						array(
								'class' => 'text-wrapper'
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
										
								), array('class' => 'btn btn-default btn-primary pull-right', 'role'=>'button')),
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
					)), array('class' => 'form-group'));
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
			<?php //echo $this->element('main/nav'); ?>

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
	</div>
</div>
<div class="spacer s0"></div>
<div id="parallax1" class="parallaxParent">
	<div></div>
</div>
<div class="spacer s2">
	<div class="box2">
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
	</div>
</div>
<div class="spacer s0"></div>
<div id="parallax2" class="parallaxParent">
	<div></div>
</div>
<div class="spacer s3">
<div class="box3">
		
<div class="row" id="gallerySection">
		<h2 class="col-lg-12 parallax"><?php echo $this->i18n->t('home.galerySelection'); ?></h2>
	</div>	
	    
	<div class="row" id="sortableGallery">
			<?php echo $this->element('galleries', array('galleries', $galleries)); ?>
	</div>
	</div>
</div>





	<!-- /parallax -->
	    <div class="row">
	        <h2 class="col-lg-12 parallax"><?php echo $this->i18n->t('home.mojo'); ?></h2>
	    </div>

	<?php echo $this->element('main/footer'); ?>
	</div> <!-- /.constainer-fluid -->
</body>
</html>
