<?php 
	
	$params = $this->request->params['named'];
	$params['controller'] = 'aperture';
	$params['action'] = 'gallery';
	$params['language'] = Configure::read('Config.language');
		
	//print_r($params);
	
	
	echo $this->Form->create('Gallery', array(
			'class' => 'form-horizontal',
			'url' => array('controller' => 'galleries', 'action' => $create?'add':'edit', '?' => $params)
	)); 
	echo $this->Form->input('id');
?>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="nameTranslation0Content"><?php echo __('Nom de la galerie en français');?></label>  
	  <div class="col-md-7">
	   <?php 
			
			$attributes = array(
					'label' => false,
					'placeholder' => __("nom"),
					'class' => 'form-control input-md',
					'required' => true
			);
			
			echo $this->Form->input('nameTranslation.0.content', $attributes);
		?> 
	  </div>
	</div>


	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="nameTranslation1Content"><?php echo __('Nom de la galerie en neerlandais');?></label>  
	  <div class="col-md-7">
	   <?php 
			
			$attributes = array(
					'label' => false,
					'placeholder' => __("nom"),
					'class' => 'form-control input-md',
					'required' => true
			);
			
			echo $this->Form->input('nameTranslation.1.content', $attributes);
		?> 
	  </div>
	</div>



	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="nameTranslation2Content"><?php echo __('Nom de la galerie en anglais');?></label>  
	  <div class="col-md-7">
	   <?php 
			
			$attributes = array(
					'label' => false,
					'placeholder' => __("nom"),
					'class' => 'form-control input-md',
					'required' => true
			);
			
			echo $this->Form->input('nameTranslation.2.content', $attributes);
		?> 
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="GalleryZip"><?php echo __('Clé de recherche');?></label>  
	  <div class="col-md-7">
	   <?php 
			
			$attributes = array(
					'label' => false,
					'placeholder' => __("p.ex. code postal"),
					'class' => 'form-control input-md',
			);
			
			echo $this->Form->input('zip', $attributes);
		?> 
	  </div>
	</div>


	<!-- Multiple Checkboxes -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="checkboxes"><?php echo __('Publication');?></label>
	  <div class="col-md-7" id="checkboxes">
	  
	  	<div class="checkbox">
	    <label for="GalleryHomepage">
	      <?php 
	      	echo $this->Form->checkbox('homepage', array(
	      			//'hiddenField' => false,
	      			'label' => false
	      	));
	      	echo __('Sur la page d\'accueil'); 	
	      	?>
	    </label>
		</div>
		
  	  	<div class="checkbox">
	    <label for="GalleryKeyword">
	      <?php 
	      	echo $this->Form->checkbox('keyword', array(
	      			//'hiddenField' => false,
	      			'label' => false
	      	));
	      	echo __('Sur la page des mots clés');    	
	      	?>
	    </label>
		</div>
		
	  </div>
	</div>
	
	
		<!-- Text input-->
		<div class="form-group">
		  <label class="col-md-4 control-label" for="data[Gallery][thumbUuid]"><?php echo __('Icône');?></label>  
		  <div class="col-md-7">
		<?php 
			
			$attributes = array(
					'value' => $defaultThumbUuid
			);

			echo $this->Form->hidden('thumbUuid', $attributes);
			echo $this->Form->hidden('generated', array(
					'value' => false
			));
			echo $this->Html->image(
					$defaultThumbUuid.'/thumb.png',
					array('class' => 'img-responsive'));
		?>
		  </div>
		</div>
	<!-- Hidden fileds -->
  	<?php 
		echo $this->Form->hidden('name', array('default' => 'default'));
		echo $this->Form->hidden('url', array('value' => $defaultUrl));
		echo $this->Form->hidden('weight', array('default' => 99 ));  
		echo $this->Form->hidden('counter', array(
				'default' => isset($defaultCounter)?$defaultCounter:0
		));
		echo $this->Form->hidden('published', array('value' => true ));
	?> 
	<!-- Button (Double) -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="buttonUpdateGallery"><?php echo __('Confirmer'); ?></label>
	  <div class="col-md-8">
		  <div class="btn-group btn-group" role="group">
		  	<?php 
		  		echo $this->Form->button($submitLabel, ['type' => 'submit', 'class' => 'btn btn-success', 'id' => 'buttonUpdateGallery']); 
		  		//echo $this->Html->link(__('Annuler'), array('controller'=>'pages', 'action'=>'home'), array('class'=>'btn btn-info'));
		  	?>
		  </div>
		  
	  </div>
	</div>

 <?php 
	echo $this->Form->end();
 ?>
