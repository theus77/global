<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

if (!Configure::read('debug')):
	throw new NotFoundException();
endif;

App::uses('Debugger', 'Utility');
?>

<div class="row">
	<div id="intro" class="col-xs-12 col-sm-8">
		<?php printf($this->requestAction('wysiwygs/slug/accueil'), $this->Html->url(array('controller' => 'aperture', 'action' => 'library', 'language' => Configure::read('Config.language'))), $this->requestAction('aperture/count')); ?>
	</div>	      	
	<div id="intro" class="col-xs-12 col-sm-4">
		<h2>Prochains vols</h2>
		<ul>
			<li>toto</li>
			<li>toto</li>
			<li>toto</li>
			<li>toto</li>
		</ul>
	</div>	      	
</div>




