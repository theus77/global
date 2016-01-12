<?php 
/**
 * Input parameters:
 * 		$lazy: boolean set to true it will generate a lazy ready html (see the lazy_loading jquery plugin)
 * 		$imageUuid: id of the image (used to identified the file on S3)
 * 		$alt alt attribute
 */


	$url = Configure::read('Config.language').'/'.urlencode($imageUuid).'/'.$route;
	
	$urlArray = Router::parse('img/'.$url);
	
	$resolvedUrl = $this->Html->url('/img/'.$url);

	$cluster = Configure::read('Config.cluster');
	$count = sizeof($cluster);

	if($count > 0) {
		$crc = crc32($imageUuid);
		$resolvedUrl = $cluster[$crc % $count].$resolvedUrl;
	}

?>
<?php $lazyClass = ''; ?>
<?php if(isset($lazy) && $lazy): ?>
	<?php $lazyClass = ' lazy'; ?>
	<noscript>
		<?php 
			echo $this->Html->image(
					$resolvedUrl,
					[
						'alt' => $alt
					]
			);
		?>
	</noscript>
<?php endif; ?>
<?php 
	echo $this->Html->image(
			(isset($lazy) && $lazy)?Configure::read('Config.language').'/loading/'.$route:$resolvedUrl,
			[
				'alt' 			=> $alt,
				'data-original'	=> $resolvedUrl,
				'class' 		=> $class.$lazyClass,
				'width' 		=> isset($urlArray['width'])?$urlArray['width']:'',
				'height' 		=> isset($urlArray['height'])?$urlArray['height']:'',
			]
	);
?>