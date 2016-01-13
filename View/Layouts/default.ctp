<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language'); ?>">
  <head>
    <?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
		echo $this->Html->meta('description', __('Site web GlobalView.be; photographies aériennes et reportages aériens tridiemsionnels et multi-angle sur-mesure. Accès à la photothèque de plusieurs millions de clichés en Belgique et au Grand Duché du Luxembourg et planning des prochains vols.'));
    	echo $this->Html->meta('icon'); 
    ?>
     
    <title><?php echo __('GlobalView'); ?>: <?php echo $title_for_layout; ?></title>
    <!-- Bootstrap core CSS -->
    <?php echo $this->Html->css('styles'); ?>

  </head>
  <body class="default">
    <div class="container-fluid">
  	<?php 
	  echo $this->element('main/header');
		echo $this->Html->tag('div', $this->Session->flash(), array('class' => 'container flash'));
    echo $this->Html->tag('div', $this->fetch('content'), array('class' => 'default')); 
    ?>
    </div>
    <?php	
		echo $this->element('main/footer'); 
	 ?>
  </body>
</html>
