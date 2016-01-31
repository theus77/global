<div class="col-xs-6 col-sm-4 col-md-4 col-lg-3 thumb">
	<a href="<?php echo $url; ?>" class="thumbnail thumbToMatch">
		<div>
			<?php
				echo $this->element('image', array(
						'lazy' => isset($lazy)?$lazy:true,
						'alt' => $name,
						'imageUuid' => $imageUuid,
						'route' => 'thumb.jpg',
						'class' => 'img-responsive',
				));	
			?>
			 	<div class="thumb-link clearfix"><?php echo $name;?><span class="badge"><?php echo $count;?></span></div>
		</div>
	</a>
</div>