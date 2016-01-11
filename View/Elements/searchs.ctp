<div class="col-md-4 geographic-search">
	<?php echo $this->i18n->w('home.geographic-search'); ?>
	<p>
		<?php echo $this->Html->link(__('Recherche géographique'),
				array(
						'controller' => 'map', 
						'action' => 'index', 
						'language' => Configure::read('Config.language')),
				array(
						'escape' => false, 
						'class' => 'btn btn-default btn-primary hvr-underline-from-center', 
						'role' => 'button'));?>
	</p>	
</div><!-- /.col-md-4 -->

<div class="col-md-4 text-search">
	<?php echo $this->i18n->w('home.free-text-search'); ?>
	<?php $searchUrl = $this->Html->url(
							array(
								'controller' => 'galleries',
								'action' => 'search',
								'language' => Configure::read('Config.language')))?>
								
	<form action="<?php echo $searchUrl; ?>" class="navbar-form" role="search" method="GET">
		<div class="form-group">
			<input type="text" class="form-control QuickSearchInput" placeholder="<?php echo __('Recherche rapide'); ?>" name="q">
		</div>
		<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
	</form>
</div><!-- /.col-md-4 -->

<div class="col-md-4 thematic-search">
	<?php echo $this->i18n->w('home.thematic-search'); ?>
	<?php echo $this->Html->link(__('Recherche thématique'), [
			'controller' => 'keywords', 
			'language' => Configure::read('Config.language')
		],[
			'escape' => false, 
			'class' => 'btn btn-default btn-primary hvr-underline-from-center', 
			'role' => 'button']
		);
	?>
</div><!-- /.col-md-4 -->
