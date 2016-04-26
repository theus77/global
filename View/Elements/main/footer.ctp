
		<footer id="footer">
			<div class="container">
				<ul class="list-inline">
					<li><a href="tel:<?php echo __('+32472800900'); ?>"><i class="fa fa-phone"></i> <?php echo __('+32/472.800.900'); ?> </a></li>
					<li><a href="https://www.facebook.com/globalview.be"><i class="fa fa-facebook"></i></a></li>
					<li><a href="mailto:simon@globalview.be"><i class="fa fa-envelope-o"></i></a></li>
					<li><a href="#"><i class="fa fa-camera"></i> <?php echo __('conditions d\'utilisation'); ?></a></li>
				</ul>
			</div>
		</footer>
		<!-- ./footer -->


	<?php 
	
		if(AuthComponent::user() && AuthComponent::user()['role'] === 'admin'){
			echo '<nav class="navbar navbar-inverse"><div class="container-fluid">';
			echo $this->Html->tag('h4', __('Options d\'administration'));
			echo $this->Html->tag('div', 
					$this->Html->tag('ul', 

						$this->Html->tag('li',
								$this->Html->link(
										__('Gestion des traductions'),
										array('controller' => 'wysiwygs', 'action' => 'index'))).
											
						$this->Html->tag('li',
								$this->Html->link(
										__('Gestion des galleries'),
										array('controller' => 'galleries', 'action' => 'index'))).
											
						$this->Html->tag('li',
						$this->Html->link(__('Ajouter un vol'), array(
														'controller' => 'flights',
														'action' => 'add',
														'language' => Configure::read('Config.language'),
														'?' => array(
																'controller' => 'pages',
																'action' => 'home',
																'language' => Configure::read('Config.language')
														)
													))).
						$this->Html->tag('li',
								$this->Html->link(
										__('Logout'),
										array('controller' => 'users', 'action' => 'logout')))
					, array('class' => 'nav navbar-nav'))
					, array('class' => 'admin-menu'));
			echo '</div></nav>';
		}
		?>
	
	<?php
		//echo $this->element('sql_dump');
		echo $this->Html->script('bower/jquery/dist/jquery.min');
	    echo $this->Html->script('bower/bootstrap-sass/assets/javascripts/bootstrap'); 
	    echo $this->Html->script('bower/matchHeight/jquery.matchHeight');
		// lazy loading
		echo $this->Html->script('bower/jquery.lazyload/jquery.lazyload');	
    	echo $this->Html->script('bower/jquery-ui/jquery-ui');
	    // simpler sidebar
	    echo $this->Html->script('bower/simpler-sidebar/dist/jquery.simpler-sidebar.min');
	    // smooth-scroll
		echo $this->Html->script('bower/smooth-scroll/dist/js/smooth-scroll.min');
		// Jicescroll
		echo $this->Html->script('bower/jquery.nicescroll/dist/jquery.nicescroll.min');
		// backstretch
		echo $this->Html->script('bower/jquery-backstretch/src/jquery.backstretch');
		// wowjs
		echo $this->Html->script('bower/wowjs/dist/wow.min');
?>
	
<?php
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