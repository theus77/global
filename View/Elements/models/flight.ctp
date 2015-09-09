<?php 
	echo $this->Form->create('Flight', array('class' => 'form-horizontal')); 
	echo $this->Form->input('id');
?>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][name]"><?php echo __('Nom du vol en français');?></label>  
	  <div class="col-md-7">
	     <?php 
			echo $this->Form->input('name', array(
					'label' => false,
					'placeholder' => __("nom"),
					'class' => 'form-control input-md',
					'required' => true
			));
		?> 
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][name]"><?php echo __('Nom du vol en néerlandais');?></label>  
	  <div class="col-md-7">
	     <?php 
			echo $this->Form->input('nameTranslation.1.content', array(
					'label' => false,
					'placeholder' => __("nom"),
					'class' => 'form-control input-md',
					'required' => true
			));
		?> 
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][name]"><?php echo __('Nom du vol en anglais');?></label>  
	  <div class="col-md-7">
	     <?php 
			echo $this->Form->input('nameTranslation.2.content', array(
					'label' => false,
					'placeholder' => __("nom"),
					'class' => 'form-control input-md',
					'required' => true
			));
		?> 
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][name]"><?php echo __('placeId');?></label>  
	  <div class="col-md-7">
	     <?php 
			echo $this->Form->input('placeId', array(
					'label' => false,
					'placeholder' => __("placeId"),
					'class' => 'form-control input-md',
					'required' => true
			));
		?> 
	  </div>
	</div>

	<!-- Textarea -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[valueTranslation][content]"><?php echo __('Description en français');?></label>
	  <div class="col-md-7">    
	  	<?php echo $this->Form->input('body', array(
			    'label' => false,
				'type' => 'textarea',
				'class' => 'form-control ckeditor',
			));?> 
	  </div>
	</div>

	<!-- Textarea -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][body]"><?php echo __('Description en néerlandais');?></label>
	  <div class="col-md-7">    
	  	<?php echo $this->Form->input('bodyTranslation.1.content', array(
			    'label' => false,
				'type' => 'textarea',
				'class' => 'form-control ckeditor',
			));?> 
	  </div>
	</div>

	<!-- Textarea -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][body]"><?php echo __('Description en anglais');?>s</label>
	  <div class="col-md-7">    
	  	<?php echo $this->Form->input('bodyTranslation.2.content', array(
			    'label' => false,
				'type' => 'textarea',
				'class' => 'form-control ckeditor',
			));?> 
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="textinput"><?php echo __('Coût de départ');?></label>  
	  <div class="col-md-7">
	  	<?php echo $this->Form->input('cost', array(
			    'label' => false,
				'class' => 'form-control input-md',
	  			'required' => true,
	  			'placeholder' => __('coût')
	  			
	  	 	));?>     
	  </div>
	</div>


		<!-- Multiple Radios (inline) -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="data[Flight][published]">Publier le vol</label>
	  <div class="col-md-7"> 
	<?php 	
		$options = array('0' => __('Non'), '1' => __('Oui'));
		$attributes = array(
				'class' => '',
				'label' => false,
				'type' => 'radio',
				'default'=> 0,
				'legend' => false,
				'before' => '<label class="radio-inline">',
				'after' => '</label>',
				'separator' => '</label><label class="radio-inline">',
				'options' => $options
		);
		
		echo $this->Form->input('published', $attributes);
	?>
	    </div>
	</div>

	<!-- Hidden fileds -->
  	<?php 
		echo $this->Form->hidden('weight', array('default' => 99 ));  
	?> 

	<!-- Button (Double) -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="button1id"><?php echo __('Confirmer'); ?></label>
	  <div class="col-md-8">
		  <div class="btn-group btn-group" role="group">
		  	<?php 
		  		echo $this->Form->button($submitLabel, ['type' => 'submit', 'class' => 'btn btn-success']); 
		  		echo $this->Html->link(__('Annuler'), array('controller'=>'pages', 'action'=>'home'), array('class'=>'btn btn-info'));
		  	?>
		  </div>
		  
	  </div>
	</div>
 <?php 
	echo $this->Form->end();
 ?>