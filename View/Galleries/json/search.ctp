<?php  
 //print_r($galleries);

 	$out = array();
	foreach ($galleries as &$gallery){
		$out[] =  $gallery['Gallery']['name'];//.( strlen($gallery['Gallery']['zip'])?' ('.$gallery['Gallery']['zip'].')':'');
	}


 	
 	echo json_encode($out);
 //print_r($galleries);
?>