<?php
namespace AppBundle\Service;

use AppBundle\Tools\Helper;
use Aws\S3\S3Client;
use Elasticsearch\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ImageService {

	/**@var Client $client*/
	private $client;
	/**@var S3Client $s3*/
	private $s3;
	
	private $cacheFolder;
	private $webRoot;
	private $apertureIndex;
	
	private $config;

	public function __construct(Client $client, S3Client $s3, $cacheFolder, $rootDir, $apertureIndex){
		$this->client = $client;
		$this->s3 = $s3;
		$this->cacheFolder = realpath($cacheFolder.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'images');
		$this->webRoot = realpath($cacheFolder.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web');
		$this->apertureIndex = $apertureIndex;
	}
	
	private function getLastUpdateDate($uuid){
		$out = 0;//epoch time
		
		if($this->client->indices()->exists(['index' => $this->apertureIndex])) {
	
			$search = $this->client->search([
					'index' => $this->apertureIndex,
					'type' => 'version',
					'body' => [
							'query' => [
									'match' => [
											'Stack' => $uuid
									]
							]
					]
			]);
				
			foreach ($search['hits']["hits"] as &$version){
				if($out < $version['_source']['lastUpdateDate']){
					$out = strtotime($version['_source']['lastUpdateDate']);
					break;
				}
			}
		}
		if($out <= 0){
			throw new NotFoundHttpException();
		}
		return $out;
	}



	private function getCachedFilePath($uuid){
		if(!file_exists($this->cacheFolder.DIRECTORY_SEPARATOR.substr($uuid, 0, 6))){
			mkdir($this->cacheFolder.DIRECTORY_SEPARATOR.substr($uuid, 0, 6), 0777, true);
		}
		return $this->cacheFolder.DIRECTORY_SEPARATOR.substr($uuid, 0, 6).DIRECTORY_SEPARATOR.
		($this->config['resize']?$this->config['resize'].'_'.(($this->config['resize'] == 'fillArea')?$this->config['gravity'].'_':'').
				$this->config['width'].'x'.$this->config['height'].'_':'').
				($this->config['radius']?'r'.$this->config['radius'].'_'.$this->config['background'].'_':'').
				$uuid.($this->config['quality']?'-'.$this->config['quality'].'.jpeg':'.png');
	}

	private function applyWatermark($image, $width, $height, $watermark){
	
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

	public function getOriginal($uuid)
    {
        $result = $this->s3->getObject([
            'Bucket' => 'global-previews',
            'Key'    => $uuid
        ]);
        $response = new Response();
        $filename = 'image.jpg';

//        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'image/jpeg' );
//        $response->headers->set('Content-Disposition', 'attachment; filename="image.jpg";');
        $response->headers->set('Content-length',  strlen($result['Body']));

        $response->setContent( $result['Body'] );
        return $response;
    }
	
	private function generateImage($uuid, $path){
	
		try{
			$result = $this->s3->getObject([
					'Bucket' => 'global-previews',
					'Key'    => $uuid
			]);
		}
		catch (\Exception $e){
			throw new NotFoundHttpException('Image not found in remote store');
			// 			$this->notFound();
		}
	
		$size	= getimagesizefromstring($result['Body']);
		$image = imagecreatefromstring($result['Body']);
		$width = $this->config['width'];
		$height = $this->config['height'];
	
		//adjuste width or height in case of ratio resize
		if($this->config['resize'] && $this->config['resize'] == 'ratio'){
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
	
	
		if($this->config['resize']){
			$image = $this->applyResize($this->config['resize'], $image, $width, $height, $size, $this->config['background'], $this->config['gravity']);
		}
		if($this->config['radius']){
			$image = $this->applyCorner($image, $width, $height, $this->config['radius'], $this->config['radius_geometry'], $this->config['background']);
		}
		if($this->config['watermark']){
			$image = $this->applyWatermark($image, $width, $height, $this->config['watermark']);
		}
	
		//convert into jpeg or png
		if($this->config['quality']){
			imagejpeg($image, $path, $this->config['quality']);
		}
		else {
			imagepng($image, $path);
		}
		imagedestroy($image);
	}
	
	
	
	
	
	/**
	 *
	 * @param array $config
	 * @return Response
	 */
	protected function getImage($uuid, Request $request=null, array $config=[]) {		
		Helper::fixUuid($uuid);
		$lastUpdateDate = $this->getLastUpdateDate($uuid);
		if($request) {
			$versionInBrowser = strtotime($request->headers->get('if-modified-since', 'Wed, 09 Feb 1977 16:00:00 GMT'));			
		}
		else {
			$versionInBrowser = strtotime('Wed, 09 Feb 1977 16:00:00 GMT');
		}

		if ($versionInBrowser >= $lastUpdateDate) {
			//http://stackoverflow.com/questions/10847157/handling-if-modified-since-header-in-a-php-script
			$response = new Response();
			$response->setNotModified();
			return $response;
		}
		
		$this->config = array_merge([
				'download' => false,
				'name' => "image",
				'resize' => false,
				'width' => '*',
				'quality' => false,
				'height' => '*',
				'gravity' => 'center',
				'radius' => false,
				'background' => 'FFFFFF',
				'radius_geometry' => 'topleft-topright-bottomright-bottomleft',
				'watermark' => false ], $config);

		$cachedFilePath = $this->getCachedFilePath($uuid);
		
		$cachedFile = true;
		if( !file_exists($cachedFilePath) || @filemtime($cachedFilePath) < $lastUpdateDate) {
			$this->generateImage($uuid, $cachedFilePath);
			$cachedFile = false;
		}
		
		$response = new BinaryFileResponse($cachedFilePath);

		// set headers and output image
		$response->headers->set('X-GV-CACHED', $cachedFile);

		
		if($lastUpdateDate)	{
			$date = new \DateTime();
			$date->setTimestamp($lastUpdateDate);
			// Set cache settings in one call
			$response->setCache(array(
					'etag'          => $uuid,
					'last_modified' => $date,
					'max_age'       => 10,
					's_maxage'      => 10,
					'public'        => true,
					// 'private'    => true,
			));
		}
		
		if($this->config['download']){
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $this->config['name'].($this->config['quality']?".jpg":".png"));
		}

		return $response;

	}
	

	/**
	 * @Route("/{_locale}/img/{ouuid}/thumb.jpg",
	 *   defaults={"_locale": "fr"},
	 *   name="thumbImage")
	 */
	public function thumbImageAction($ouuid, Request $request = null)  {
		return $this->getImage($ouuid, $request, [
				'resize' => 'fill',
				'width' => 320,
				'quality' => 50,
				'height' => 320,
				'background' => 'ffffff'
		]);
	}
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/image.jpg",
	 *   defaults={"_locale": "fr"},
	 *   name="imageImage")
	 */
	public function imageImageAction($ouuid, Request $request = null)  {
		
		return $this->getImage($ouuid, $request, [
				'resize' => 'fill',
				'height' => '800',
				'width' => '1200',
				'watermark' => $this->webRoot.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'watermark.png',
				'quality' => 75,
				'background' => '000000'
		]);
	}
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/download.jpg",
	 *   defaults={"_locale": "fr"},
	 *   name="downloadImage")
	 */
	public function downloadImageAction($ouuid, Request $request)  {
		return $this->getImage($ouuid, $request, [
				'resize' => 'fill',
				'height' => '800',
				'width' => '1200',
				'watermark' => $this->webRoot.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'watermark.png',
				'quality' => 75,
				'background' => '000000',
				'name' => 'download',
				'download' => true
		]);
	}
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/preview.jpg",
	 *   defaults={"_locale": "fr"},
	 *   name="previewImage")
	 */
	public function previewImageAction($ouuid, Request $request = null)  {
		return $this->getImage($ouuid, $request, [
				'resize' => 'fillArea',
				'height' => '200',
				'width' => '300',
				'quality' => 50,
		]);
	}



}