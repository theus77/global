<!-- Prepended checkbox -->
<div class="form-group">
  <label class="col-md-4 control-label" for="<?php echo $field?>"><?php echo $label?></label>
  <div class="col-md-8">
    <div class="input-group">
      <span class="input-group-addon">     
          <input type="checkbox">     
      </span>
      <?php echo $this->Form->input($field, array(
  		'label' => false,
  		'class' => 'form-control',
  		'placeholder' => $placeholder,
  		
  		));?>
  
    </div>
    <?php if (isset($help)): ?>
    <p class="help-block"><?php echo $help;?></p>
    <?php endif;?>
  </div>
</div>