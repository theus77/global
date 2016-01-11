<?php
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Aws\S3\S3Client;
use Aws\Credentials\CredentialProvider;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Aws\S3\Exception\S3Exception;

App::uses('ImgController', 'ApertureConnector.Controller');


class ImgController extends AppController {
	
	private function getParam($name, $default){
		$out = $default;
		if(isset($this->params['named'][$name]))
			$out = $this->params['named'][$name];
		if(isset($this->params[$name]))
			$out = $this->params[$name];
		if ( strrpos($name, 'mobile_', -strlen($name) ) === FALSE && $this->RequestHandler->isMobile() ) {
			$out = $this->getParam('mobile_'.$name, $out);
		}
		return $out;
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('s3');
	}
	

	private function isDownload(){
		return $this->getParam('download', false);
	}
	private function getName(){
		return $this->getParam('name', "image");
	}
	private function getRoute(){
		return $this->getParam('route', "image.".($this->getQuality()?'jpg':'png'));
	}
	private function getResize(){
		return $this->getParam('resize', false);
	}
	private function getWidth(){
		return $this->getParam('width', '*');
	}
	private function getQuality(){
		return $this->getParam('quality', false);
	}
	private function getHeight(){
		return $this->getParam('height', '*');
	}
	private function getGravity(){
		return $this->getParam('gravity', 'center');
	}
	private function getRadius(){
		return $this->getParam('radius', false);
	}
	private function getBackground(){
		return $this->getParam('background', 'FFFFFF');
	}
	private function getRadiusGeometry(){
		return $this->getParam('radius_geometry', 'topleft-topright-bottomright-bottomleft');
	}
	private function getWatermark(){
		return $this->getParam('watermark', false);
	}
	
	private function getLastUpdateDate($uuid){
		$out = 0;//epoch time
		$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))      // Set the hosts
			->build();              // Build the client object
		if($client->indices()->exists(['index' => 'aperture'])) {
				
			$search = $client->search([
					'index' => 'aperture',
					'type' => 'version',
					'body' => [
							'query' => [
									'match' => [
											'Stack' => $uuid
									]
							]
					]
			]);
			
			
// 			echo json_encode([
// 					'index' => 'aperture',
// 					'type' => 'version',
// 					'body' => [
// 							'query' => [
// 									'match' => [
// 											'Stack' => $uuid
// 									]
// 							]
// 					]
// 			]);
			
// 			var_dump($uuid);
// 			var_dump($search);
// 			exit;
			
			foreach ($search['hits']["hits"] as &$version){
				if($out < $version['_source']['lastUpdateDate']){
					$out = strtotime($version['_source']['lastUpdateDate']);
					break;
				}
			}
		}
		if($out <= 0){
			//throw new NotFoundException();
			$this->notFound();
		}
		return $out;
	}
	
	private function notFound(){
		$this->redirect('/img/'.Configure::read('Config.language').'/404/'.$this->getRoute());		
	}
	
	private function getCachedFilePath($uuid){
		return CACHE.'views'.DS.
			($this->getResize()?$this->getResize().'_'.(($this->getResize() == 'fillArea')?$this->getGravity().'_':'').
			$this->getWidth().'x'.$this->getHeight().'_':'').
			($this->getRadius()?'r'.$this->getRadius().'_'.$this->getBackground().'_':'').
			$uuid.($this->getQuality()?'-'.$this->getQuality().'.jpeg':'.png');
	}
	
	private function applyWatermark($image, $width, $height, $watermark){
		
// 		echo $width.'-';
// 		echo $height;
// 		echo $watermark;
// 		exit;
		
		$stamp = imagecreatefrompng($watermark);
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);
		imagecopy($image, $stamp, ($width - $sx) /2, ($height - $sy)/2, 0, 0, $sx, $sy);
		return $image;
	}
	
	private function applyCorner($source_image, $source_width, $source_height, $radius, $radius_geometry, $colour){
		$corner_image = imagecreatetruecolor(
				$radius,
				$radius
				);
	
	
		$clear_colour = imagecolorallocate(
				$corner_image,
				0,
				0,
				0
				);
	
	
		$solid_colour = imagecolorallocate(
				$corner_image,
				hexdec(substr($colour, 0, 2)),
				hexdec(substr($colour, 2, 2)),
				hexdec(substr($colour, 4, 2))
				);
	
		imagecolortransparent(
				$corner_image,
				$clear_colour
				);
	
		imagefill(
				$corner_image,
				0,
				0,
				$solid_colour
				);
	
		imagefilledellipse(
				$corner_image,
				$radius,
				$radius,
				$radius * 2,
				$radius * 2,
				$clear_colour
				);
	
		/*
		 * render the top-left, bottom-left, bottom-right, top-right corners by rotating and copying the mask
			*/
	
		if(stripos($radius_geometry, "topleft") !== FALSE){
			imagecopymerge(
					$source_image,
					$corner_image,
					0,
					0,
					0,
					0,
					$radius,
					$radius,
					100
					);
		}
	
		$corner_image = imagerotate($corner_image, 90, 0);
	
		if(stripos($radius_geometry, "bottomleft") !== FALSE){
			imagecopymerge(
					$source_image,
					$corner_image,
					0,
					$source_height - $radius,
					0,
					0,
					$radius,
					$radius,
					100
					);
		}
	
		$corner_image = imagerotate($corner_image, 90, 0);
	
		if(stripos($radius_geometry, "bottomright") !== FALSE){
			imagecopymerge(
					$source_image,
					$corner_image,
					$source_width - $radius,
					$source_height - $radius,
					0,
					0,
					$radius,
					$radius,
					100
					);
		}
	
		$corner_image = imagerotate($corner_image, 90, 0);
	
		if(stripos($radius_geometry, "topright") !== FALSE){
			imagecopymerge(
					$source_image,
					$corner_image,
					$source_width - $radius,
					0,
					0,
					0,
					$radius,
					$radius,
					100
					);
		}
	
		$tansparent_colour = imagecolorallocate(
				$source_image,
				hexdec(substr($colour, 0, 2)),
				hexdec(substr($colour, 2, 2)),
				hexdec(substr($colour, 4, 2))
				);
			
		imagecolortransparent(
				$source_image,
				$tansparent_colour
				);
	
		// 		header("Content-type:image/png");
		// 		echo imagepng($source_image) ;
		// 		exit();
		return $source_image;
	
	}
	
	private function applyResize($resize, $image, $width, $height, $size, $background, $gravity){
		if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
			$resizeFunction = 'imagecopyresampled';
		} else {
			$temp = imagecreate($width, $height);
			$resizeFunction = 'imagecopyresized';
		}
		
		
		$solid_colour = imagecolorallocate(
				$temp,
				hexdec(substr($background, 0, 2)),
				hexdec(substr($background, 2, 2)),
				hexdec(substr($background, 4, 2))
				);
		
		imagefill(
				$temp,
				0,
				0,
				$solid_colour
				);
		
		
		if($resize == 'fillArea'){
			if(($size[1]/$height) < ($size[0]/$width)){
				$cal_width = $size[1] * $width / $height;
				if(stripos($gravity, 'west') !== false)
				{
					call_user_func($resizeFunction, $temp, $image, 0, 0, $size[0]-$cal_width, 0, $width, $height, $cal_width, $size[1]);
				}
				else if(stripos($gravity, 'est') !== false)
				{
					call_user_func($resizeFunction, $temp, $image, 0, 0, 0, 0, $width, $height, $cal_width, $size[1]);
				}
				else{
					call_user_func($resizeFunction, $temp, $image, 0, 0, ($size[0]-$cal_width)/2, 0, $width, $height, $cal_width, $size[1]);
				}
			}
			else{
				$cal_height = $size[0] / $width * $height;
				if(stripos($gravity, 'north') !== false)
				{
					call_user_func($resizeFunction, $temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $cal_height);
				}
				else if(stripos($gravity, 'south') !== false)
				{
					call_user_func($resizeFunction, $temp, $image, 0, 0, 0, $size[1]-$cal_height, $width, $height, $size[0], $cal_height);
				}
				else{
					call_user_func($resizeFunction, $temp, $image, 0, 0, 0, ($size[1]-$cal_height)/2, $width, $height, $size[0], $cal_height);
				}
			}
		}
		else if($resize == 'fill'){
			if(($size[1]/$height) < ($size[0]/$width)){
		
				$thumb_height = $width*$size[1]/$size[0];
				call_user_func($resizeFunction, $temp, $image, 0, ($height-$thumb_height)/2, 0, 0, $width, $thumb_height, $size[0], $size[1]);
			}
			else {
				$thumb_width = ($size[0]*$height)/$size[1];
				call_user_func($resizeFunction, $temp, $image, ($width-$thumb_width)/2, 0, 0, 0, $thumb_width, $height, $size[0], $size[1]);
			}
				
		}
		else{
			call_user_func($resizeFunction, $temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		}
		//imagedestroy($image);
		return $temp;		
	}
	
	private function generateImage($uuid, $path){
		$s3 = new S3Client(Configure::read('aws.credentials'));
		
		try{
			$result = $s3->getObject([
					'Bucket' => 'global-previews',
					'Key'    => $uuid
			]);			
		}
		catch (S3Exception $e){
// 			$this->notFound();
		}
		
		$size	= getimagesizefromstring($result['Body']);
		$image = imagecreatefromstring($result['Body']);
		$width = $this->getWidth();
		$height = $this->getHeight();
		
		//adjuste width or height in case of ratio resize
		if($this->getResize() && $this->getResize() == 'ratio'){
			// if either width or height is an asterix
			if($width == '*' || $height == '*') {
				if($height == '*') {
					// recalculate height
					$height = ceil($width / ($size[0]/$size[1]));
				} else {
					// recalculate width
					$width = ceil(($size[0]/$size[1]) * $height);
				}
			} else {
				if (($size[1]/$height) > ($size[0]/$width)) {
					$width = ceil(($size[0]/$size[1]) * $height);
				} else {
					$height = ceil($width / ($size[0]/$size[1]));
				}
			}
		}	
		

		if($this->getResize()){
			$image = $this->applyResize($this->getResize(), $image, $width, $height, $size, $this->getBackground(), $this->getGravity());
		}
		if($this->getRadius()){
			$image = $this->applyCorner($image, $width, $height, $this->getRadius(), $this->getRadiusGeometry(), $this->getBackground());
		}
		if($this->getWatermark()){
			$image = $this->applyWatermark($image, $width, $height, $this->getWatermark());
		}
		
		//convert into jpeg or png
		if($this->getQuality()){
			imagejpeg($image, $path, $this->getQuality());
		}
		else {
			imagepng($image, $path);
		}
		imagedestroy($image);	
	}

	
	public function s3($uuid) {
		$this->fixUuid($uuid);

		
		$lastUpdateDate = $this->getLastUpdateDate($uuid);
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
				strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastUpdateDate) {
			//http://stackoverflow.com/questions/10847157/handling-if-modified-since-header-in-a-php-script
			header('HTTP/1.0 304 Not Modified');
			exit;
		}

		$cachedFilePath = $this->getCachedFilePath($uuid);
		if( Configure::read('debug') > 2 || ! file_exists($cachedFilePath) || @filemtime($cachedFilePath) < $lastUpdateDate) {
			$this->generateImage($uuid, $cachedFilePath);
		}
		else {
			header('X-from-cache: true');
		}
		$data = file_get_contents($cachedFilePath);
		
		// set headers and output image
		if($lastUpdateDate)	{
			header("Last-Modified:".date('D, d M Y H:i:s ', $lastUpdateDate)."GMT");
		}
		if($this->isDownload()){
			header("Content-Transfer-Encoding: binary");
			header("Content-Disposition: attachment; filename=".$this->getName().($this->getQuality()?".jpg":".png"));
		}
		if($this->getQuality()){
			$mime = 'image/jpeg';
		}
		else{
			$mime = 'image/png';
		}		
		header("Content-type:$mime");
		header('Content-Length: ' . strlen($data));
		echo $data;
		exit;

	}
	
}