      <footer>
        <p class="pull-right"><a href="#"><?php echo __('Retour en haut');?></a></p>
        <p>
	        &copy; 2004-<?php echo date('Y').' '.__('Global View S.P.R.L.'); ?> 
	        &middot; <a href="tel:<?php echo __('+32472800900'); ?>"><?php echo __('+32/472.800.900'); ?></a> 
	        &middot; <a href="#"><?php echo __('conditions d\'utilisation'); ?></a>
	        &middot; <a href="https://www.facebook.com/globalview.be"><?php echo  $this->Html->image('facebook.png').'&nbsp;'.__('Rejoignez nous sur facebook'); ?></a>
	        &middot; <a href="mailto:simon@globalview.be"><?php echo __('Contactez-nous par email'); ?></a>
	    </p>
      </footer>

	<?php 
	
		if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'){
			echo $this->Html->tag('h3', __('Options d\'administration'));
			echo $this->Html->tag('ul', 
					$this->Html->tag('li',
							$this->Html->link(
									__('Gestion des textes'),
									array('controller' => 'wysiwygs', 'action' => 'text'))).

					$this->Html->tag('li',
							$this->Html->link(
									__('Gestion des traductions'),
									array('controller' => 'wysiwygs', 'action' => 'index'))).
										
					$this->Html->tag('li',
							$this->Html->link(
									__('Lister les albums aperture'),
									array('controller' => 'aperture', 'action' => 'albums'))).					
					$this->Html->tag('li',
							$this->Html->link(
									__('Lister les mots clés aperture'),
									array('controller' => 'aperture', 'action' => 'keywords'))));
		}
	
	
		echo $this->element('sql_dump');
		echo $this->Html->script('bower/jquery/dist/jquery');
	    echo $this->Html->script('bower/bootstrap-sass/assets/javascripts/bootstrap');
	    echo $this->Html->script('bower/matchHeight/jquery.matchHeight');
    	echo $this->Html->script('bower/jquery-ui/jquery-ui');
	    
	    if(AuthComponent::user()){
	    	echo $this->Html->script('bower/ckeditor/ckeditor');
	    	if(AuthComponent::user()['role'] === 'admin'){
		    	echo $this->Html->script('admin');
	    	}
	    }
	    
	    echo $this->Html->script('globalview');
	    
	    echo $this->fetch('script');
	    if(isset($jsIncludes)){
	    	foreach($jsIncludes as $js){
	    		echo  $this->Html->script($js);
	    	}
	    }    
    ?>
    
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script> -->
    
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    