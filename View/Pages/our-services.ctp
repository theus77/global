<div id="our-services-ctp">
	<!--
	<div class="spacer s1" id="services">
		<div class="box2 box container">
		</div>
	</div>
		<div class="spacer"></div>
	-->
	<div id="parallax1" class="parallaxParent">
		<div></div>
	</div>
	<div class="spacer s1" id="technics">
		<div class="box2 box container">
			<div class="row ">
				<div class="col-md-12">
					<?php echo $this->I18n->w('our-service.techniques');  ?>
				</div>
			</div>
		</div>
	</div>

	<div class="spacer"></div>
	<div id="parallax2" class="parallaxParent">
		<div></div>
	</div>
	<div class="spacer s1" id="library">
		<div class="box2 box container">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="parallax"><?php echo __('Nos services'); ?></h2>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<?php echo $this->I18n->w('our-service.library');  ?>
				</div>
			</div>

		</div>
	</div>
	<div class="spacer"></div>
	<div id="parallax3" class="parallaxParent">
		<div></div>
	</div>
	<div class="spacer s1" id="prices">
		<div class="box3 box container">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="parallax"><?php echo __('Les tarifs'); ?></h2>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<?php echo $this->I18n->w('our-service.prices');  ?>
				</div>

				<div>
					<?php
					echo $this->Html->link(__('Continuer'),
							array(
									'controller' => 'flights',
									'action' => 'book',
									'language' => Configure::read('Config.language'),
									7

							), array('class' => 'btn btn-default btn-primary pull-right hvr-underline-from-center', 'role'=>'button'))

					 ?>
				</div>
			</div>

		</div>
	</div>
	<div class="spacer"></div>
	<div id="parallax4" class="parallaxParent">
		<div></div>
	</div>
	<div class="spacer s1" id="contact">
		<div class="box4 box container">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="parallax"><?php echo __('Contactez-nous'); ?></h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?php echo $this->I18n->w('our-service.contact-us');  ?>
				</div>
				<div class="col-md-6">
					<?php echo $this->element('contact'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
