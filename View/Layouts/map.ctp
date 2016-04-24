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

    <title><?php echo __('Bienvenue, GlobalView reportage aérien sur-mesure'); ?></title>
    <!-- fontawesome css -->
    <?php echo $this->Html->css('font-awesome/font-awesome.min'); ?>
    <!-- animate css -->
    <?php echo $this->Html->css('animate/animate.min'); ?>
    <!-- jquery scrollpane -->
    <?php echo $this->Html->css('jquery.scrollpane'); ?>

    <!-- google font -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Bootstrap core CSS -->
    <?php echo $this->Html->css('styles'); ?>
  </head>

  <body id="map">
    <!-- scrollToTop -->  
    <a href="#" class="scrollToTop">
      <i class="fa fa-angle-up fa-2x"></i>
    </a>
    <!-- ./scrollToTop -->
    <?php 
    echo $this->element('main/header');
    ?> 
    <?php 
    echo $this->Html->tag('div', $this->Session->flash(), array('class' => 'container flash'));
    echo $this->Html->tag('div', $this->fetch('content'), array('class' => 'default')); 
    ?>        
    <?php	
		echo $this->element('main/footer'); 
	 ?>
  </body>
</html>
