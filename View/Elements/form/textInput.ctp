<div class="form-group">
  <label class="col-md-4 control-label" for="<?php echo $field?>"><?php echo $label?></label>  
  <div class="col-md-8">
  <?php echo $this->Form->input($field, array(
  		'label' => false,
  		'class' => 'form-control input-md',
  		'placeholder' => __($placeholder),
  		'required' => isset($required)?$required:false,
  		));?>
    
    <?php if (isset($help)): ?>
    <p class="help-block"><?php echo $help;?></p>
    <?php endif;?>
  </div>
</div>