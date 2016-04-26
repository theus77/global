<div class="container library">
	<div class="row">
		<h2 class="col-lg-12 parallax"><?php echo __('Désolé, aucun résultat n\'a été généré pour la recherche "%s"', $query); ?> </h2>
	</div>
	<div class="row ">
		<div class="col-md-12">
			<?php echo $this->I18n->w('galery.empty');  ?>
		</div>
	</div>
	<div class="row">
				<?php echo $this->element('searchs'); ?>
	</div><!-- /.row -->
</div>
