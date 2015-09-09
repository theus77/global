<div class="row">
    <div id="login" class="col-xs-offset-1 col-xs-10 col-sm-offset-3 col-sm-6 text-center col-md-offset-4 col-md-4 text-center">
		<h2 class="form-signin-heading"><?php echo $this->i18n->t('login.title'); ?></h2>
    <!--        
    
		<?php echo $this->Form->create('User', array('class' => 'form-form-horizontal')); ?>
	        
	        <p><?php echo $this->Session->flash('auth'); ?></p>
	
	
	        <label for="UserUsername" class="sr-only"><?php echo $this->i18n->t('login.email-label'); ?></label>
    	    <?php echo $this->Form->text('username', array('class' => 'form-control', 'placeholder' => $this->i18n->t('login.email-place-holder'), 'required', 'autofocus')); ?>
        	<label for="inputPassword" class="sr-only"><?php echo $this->i18n->w('login.password'); ?></label>
        	<?php echo $this->Form->password('password', array('class' => 'form-control', 'placeholder' => $this->i18n->t('login.password-place-holder'), 'required')); ?>
        	<button class="btn btn-primary" type="submit"><?php echo $this->i18n->t('login.login'); ?></button>        	
		<?php echo $this->Form->end(); ?>
-->
		
		<?php echo $this->Form->create('User', array('class' => 'form-horizontal')); ?>
		

		
			<!-- Text input-->
			<div class="form-group">
			  <?php echo $this->Form->text('username', array('class' => 'form-control input-md', 'placeholder' => $this->i18n->t('login.email-place-holder'), 'required', 'autofocus')); ?>
			</div>
			
			<!-- Password input-->
			<div class="form-group">
			    <?php echo $this->Form->password('password', array('class' => 'form-control input-md', 'placeholder' => $this->i18n->t('login.password-place-holder'), 'required')); ?>
			</div>
			
			<!-- Button -->
			<div class="form-group">
			    <button id="login" name="login" class="btn btn-primary"><?php echo $this->i18n->t('login.login'); ?></button>
			</div>

		</form>
				
		
    </div>
</div>
