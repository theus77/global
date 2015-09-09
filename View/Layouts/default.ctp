<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language'); ?>">
  <head>
    <?php echo $this->Html->charset(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php 
    	echo $this->Html->meta('description', $this->i18n->t('site.description'));
    	echo $this->Html->meta('author', $this->i18n->t('site.author'));
    	echo $this->Html->meta('icon'); 
    ?>
     
    <title><?php echo $this->i18n->t('site.title').' - '.$this->i18n->t('home.title'); ?></title>

    <!-- Bootstrap core CSS -->
    <?php echo $this->Html->css('styles'); ?>

  </head>
  <body>
  	<?php 
	  	echo $this->element('main/header');
	  	echo $this->element('main/nav');
		echo $this->Html->tag('div', $this->Session->flash(), array('class' => 'container flash'));
	  	echo $this->fetch('content');
		echo $this->element('main/footer'); 
	?>
  </body>
</html>
