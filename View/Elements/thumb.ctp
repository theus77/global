<div class="col-md-4 col-sm-4 thumb col-lg-3 col-xs-6">
	<div class="image-box">
		<a href="<?php echo $url; ?>">
			<figure>
				<?php
				echo $this->element('image', array(
						'lazy' => isset($lazy)?$lazy:true,
						'alt' => $name,
						'imageUuid' => $imageUuid,
						'route' => 'thumb.jpg',
						'class' => 'img-responsive',
				));	
			?>
			</figure>
		</a>
		<div class="caption">
			<?php echo $name;?> <span class="badge"><?php echo $count;?></span>
		</div>	
	</div>
</div>