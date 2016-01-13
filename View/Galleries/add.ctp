<div class="row">
<div class="col-md-2 "></div>
<div class="col-md-8">
	<?php echo $this->Form->create('Gallery'); ?>
	
	<!-- Form Name -->
	<legend><?php echo __('Créer une gallerie');?></legend>
	
	<div class="panel panel-default">
  		<div class="panel-heading"><?php echo __('Noms de la gallerie')?></div>
  		<div class="panel-body">
			<?php 
				$counter = 0;
				foreach(Configure::read('Config.languages') as $code => $language): ?>
				<!-- Text input-->
				<label for="basic-url"><?php echo $language; ?></label>
				<div class="input-group">
				 <span class="input-group-addon" id="basic-addon3"></span>
				  <?php echo $this->Form->input('nameTranslation.'.$counter.'.content', array(
				  		'label' => false,
				  		'class' => 'form-control'
				  ));?>
				</div>
			<?php 
					++ $counter;
				endforeach; 
			?>
		</div>
	</div>	
	
	<div class="panel panel-default">
  		<div class="panel-heading"><?php echo __('URLs parlantes')?></div>
  		<div class="panel-body">
			<?php 
				$counter = 0;
				foreach(Configure::read('Config.languages') as $code => $language): ?>
				<!-- Text input-->
				<label for="basic-url"><?php echo $language; ?></label>
				<div class="input-group">
			  		<span class="input-group-addon" id="basic-addon3"><?php 
				  		echo $this->Html->url(array(
				  				"controller" => "galleries",
				  				"action" => "view",
				  				'language' => $code
				  		), true);
			  		?>/</span>
				  <?php echo $this->Form->input('slugTranslation.'.$counter.'.content', array(
				  		'label' => false,
				  		'class' => 'form-control'
				  ));?>
				</div>
			<?php 
					++ $counter;
				endforeach; 
			?>
		</div>
	</div>
	
	<div class="panel panel-default">
  		<div class="panel-heading"><?php echo __('Requête elasticsearch')?></div>
  		<div class="panel-body">
			 <?php echo $this->Form->textarea('query', array(
			  		'label' => false,
			  		'class' => 'form-control'
			  ));?>
		</div>
	</div>	
	
	<div class="panel panel-default">
  		<div class="panel-heading"><?php echo __('Options de la gallerie')?></div>
  		<div class="panel-body">

			 <div class="input-group">
			  		<span class="input-group-addon" id="basic-addon3"><?php 
				  		echo __('Icône')
			  		?></span>
				  <?php echo $this->Form->input('thumbUuid', array(
				  		'label' => false,
				  		'class' => 'form-control'
				  ));?>
			</div>

			<div>&nbsp;</div>

			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-info">
				<?php echo $this->Form->input('homepage', array(
				  		'label' => false,
				  		'autocomplete' => 'off',
						'hiddenField' => false,
       					'div' => false
				  )).__('Visible sur la page d\'accueil') ;?>
			  </label>
				<label class="btn btn-info">
				<?php echo $this->Form->input('published', array(
				  		'label' => false,
				  		'autocomplete' => 'off',
						'hiddenField' => false,
       					'div' => false
				  )).__('Gallerie publiée') ;?>
			  </label>
			</div>
		</div>
	</div>
	
	<div class="btn-group pull-right">
	<?php echo $this->Form->end([
			'label'=> __('Créer la gallerie'),
			'class' => 'btn btn-success',
			'div' => false
			
		]); ?>
	</div>
	</div>
</div>