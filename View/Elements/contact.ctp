<!-- Form Name -->

<?php echo $this->Form->create('Booking', ['class' => 'form-horizontal']); ?>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name"><?php echo __('Nom et prénom *')?></label>  
  <div class="col-md-8">
  
  <?php echo $this->Form->input('name', array(
  		'label' => false,
  		'class' => 'form-control input-md',
  		'placeholder' => __('nom et prénom'),
  		'required' => true
  		
  ));?>
    
  </div>
</div>

<!-- Prepended checkbox -->
<div class="form-group">
  <label class="col-md-4 control-label" for="vat"><?php echo __('Numéro de TVA')?></label>
  <div class="col-md-8">
    <div class="input-group">
      <span class="input-group-addon">     
          <input type="checkbox">     
      </span>
      <?php echo $this->Form->input('vat', array(
  		'label' => false,
  		'class' => 'form-control',
  		'placeholder' => __('numéro de TVA')
  		
  		));?>
  
    </div>
    <p class="help-block"><?php echo __('Si d\'application');?></p>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="email"><?php echo __('Adresse email *');?></label>  
  <div class="col-md-8">
  <?php echo $this->Form->input('email', array(
  		'label' => false,
  		'class' => 'form-control input-md',
  		'placeholder' => __('email'),
  		'required' => true
  		
  		));?>
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="phone"><?php echo __('Numéro de téléphone');?></label>  
  <div class="col-md-8">
  <?php echo $this->Form->input('phone', array(
  		'label' => false,
  		'class' => 'form-control input-md',
  		'placeholder' => __('téléphone')
  		
  		));?>
    
  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="comment"><?php echo __('Remarques');?></label>  
  <div class="col-md-8">
  <?php echo $this->Form->input('comment', array(
  		'label' => false,
  		'class' => 'form-control input-md',
  		'placeholder' => __('commentaires, remarques, questions, ...')
  		
  		));?>
    
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <div class="col-md-offset-4 col-md-8">
    <button id="send" name="send" class="btn btn-primary"><?php echo('Envoyer');?></button>
  </div>
</div>

</form>