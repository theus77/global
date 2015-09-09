  	<header class="hidden-xs row">
  		<div class="col-xs-2">
  		<?php 
  			echo $this->Html->link(
  					$this->Html->image('GlobalView.png', array(
  							'alt' => $this->i18n->t('layout.logo-alt'),
  							'class' => 'img-responsive'
  					)),
  					array('controller' => 'pages', 'action' => 'home', 'language' => Configure::read('Config.language')),
  					array('escape' => false)
  			);
  		?>
  		</div>
		<h1 class="col-xs-8 text-center"><?php echo $this->i18n->w('layout.title');  ?></h1>
	   	<nav class="col-xs-2">
	   	<ul class="nav nav-pills">
	   	<?php 
			foreach(Configure::read('Config.languages') as $code => $language) { // show links for translated version
				if(strcmp(Configure::read('Config.language'), $code)!==0){
					$param = array(
						'language' => $code,
						'controller' => $this->params->params['controller'],
						'action' => $this->params->params['action']);
					foreach($this->params->params['pass'] as $pass){
						$param[] = $pass;
					}
					$param['language'] = $code;		
					echo $this->Html->tag('li', $this->Html->link($this->Html->tag('abbr', $code, array('title' => $language, 'lang' => $code, 'role' => 'presentation')), $param, array('escape' => false, 'class' => 'btn btn-primary btn-sm')));
				}
			}
		?>
        </ul>
        </nav>
  	</header>