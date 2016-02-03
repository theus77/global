<div class="container">
	<div id="price-ctp">
			<h1>
				<?php echo __('Demande de prix pour un cliché');  ?>
			</h1>
		<div class="row">
			<div class="col-md-12">
			<?php echo $this->i18n->w('price.intro');  ?>
			</div>
		</div>
	</div>

	<!-- Form Name -->

	<?php echo $this->Form->create('PricingRequest', ['class' => 'form-horizontal']); ?>

	<!-- Text input-->
	<div class="form-group">
		<div class="col-md-5">
			<div class="btn-group" data-toggle="buttons">
				<?php foreach ($version["_source"]["Stack"] as $imageUuid):?>
				  <label class="col-md-6 col-lg-3">
				    <input type="radio" name="data[PricingRequest][bestUuid]" id="data[PricingRequest][bestUuid]" autocomplete="off" value="<?php  echo $imageUuid?>"> 
				      	<?php 
				      	echo $this->element('image', array(
				      			'lazy' => false,
				      			'alt' => $imageUuid,
				      			'imageUuid' => $imageUuid,
				      			'route' => 'thumb.jpg',
				      			'class' => 'img-responsive',
				      	));
				      	?>
				  </label>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="col-md-7">
		      	<?php 
	echo $this->element('form/textInput', [
			'field' => 'name',
			'label' => __('Nom et prénom *'),
			'placeholder' => __('nom et prénom'),
	  		'required' => true
	]);
	echo $this->element('form/prependedCheckbox', [
			'field' => 'vat',
			'label' => __('Numéro de TVA'),
			'placeholder' => __('numéro de TVA'),
			'help' => __('Si d\'application')
	]);
	echo $this->element('form/textInput', [
			'field' => 'email',
			'label' => __('Adresse email *'),
			'placeholder' => __('email'),
	  		'required' => true
	]);
	echo $this->element('form/textInput', [
			'field' => 'phone',
			'label' => __('Numéro de téléphone'),
			'placeholder' => __('téléphone')
	]);
	echo $this->element('form/textInput', [
			'field' => 'media',
			'label' => __('Support'),
			'placeholder' => __('supports sur lesquels l’œuvre est reproduite'),
			'help' => __('Internet, cartes postals, dossiers de presse, télévision, ...'),
	]);
	echo $this->element('form/textInput', [
			'field' => 'printRun',
			'label' => __('Tirage'),
			'placeholder' => __('tirage en nombre d\'exemplaire'),
			'help' => __('Si d\'application'),
	]);
	echo $this->element('form/textInput', [
			'field' => 'maxFormat',
			'label' => __('Format maximum'),
			'placeholder' => __('format maximum du support en cm'),
			'help' => __('En cm: 10X15, 20x30, ...'),
	]);
	echo $this->element('form/textInput', [
			'field' => 'duration',
			'label' => __('Durée'),
			'placeholder' => __('durée de représentation de l’œuvre dans un film…'),
			'help' => __('Si d\'application'),
	]);
	echo $this->element('form/textInput', [
			'field' => 'special',
			'label' => __('Critères spéciaux'),
			'placeholder' => __('page de couverture, utilisation non-commerciale…'),
			'help' => __('Si d\'application'),
	]);

	echo $this->element('form/textInput', [
			'field' => 'comment',
			'label' => __('Remarques'),
			'placeholder' => __('commentaires, remarques, questions, ...')
	]);
	echo $this->element('form/sendButton', [
	]);
	      	?>	
		</div>
	</div>

	</form>
</div>