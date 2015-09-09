<?php 


	$output = array();
	
	
	foreach ($versions as &$version){
		
		$new = $version['Version'];
		
		$output[] = $new;
		
	}




	echo json_encode($output);

	//http://global.theus.be/img/thumbnails/zubvhsaUT7aWLoIxYU8tuQ/thumbs.png
	//http://gv.local       /img/thumbnails/znrfUkaOTU2GAlB7uHUI0g/thumbs.png
	