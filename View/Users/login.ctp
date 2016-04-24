<div class="row">
    <div id="login" class="col-xs-offset-1 col-xs-10 col-sm-offset-3 col-sm-6 col-md-offset-4 col-md-4">
		<h2 class="form-signin-heading"><?php echo __('Identifiez-vous'); ?></h2>

		
		<?php echo $this->Form->create('User', array('class' => 'form-horizontal')); ?>
		

		
			<!-- Text input-->
			<div class="form-group">
			  <?php echo $this->Form->text('username', array('class' => 'form-control input-md', 'placeholder' => __('Identifiant'), 'required', 'autofocus')); ?>
			</div>
			
			<!-- Password input-->
			<div class="form-group">
			    <?php echo $this->Form->password('password', array('class' => 'form-control input-md', 'placeholder' => __('Mot de passe'), 'required')); ?>
			</div>
			
			<!-- Button -->
			<div class="form-group">
			    <button id="login" name="login" class="btn btn-primary"><?php echo __('S\'identifier'); ?></button>
			</div>

		</form>
				
		
    </div>
</div>
