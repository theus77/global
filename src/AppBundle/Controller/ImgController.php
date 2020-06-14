<?php

namespace AppBundle\Controller;

use AppBundle\Service\ImageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImgController extends Controller
{
	/**
	 * @return ImageService
	 */
	private function service(){
		return $this->get('app.image');
	}
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/thumb.jpg",
     *   defaults={"_locale": "fr"},
	 *   name="thumbImage")
	 */
	public function thumbImageAction($ouuid, Request $request)  {
		return $this->service()->thumbImageAction($ouuid, $request);
    }
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/image.jpg",
     *   defaults={"_locale": "fr"},
	 *   name="imageImage")
	 */
	public function imageImageAction($ouuid, Request $request)  {
		return $this->service()->imageImageAction($ouuid, $request);
    }
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/download.jpg",
     *   defaults={"_locale": "fr"},
	 *   name="downloadImage")
	 */
	public function downloadImageAction($ouuid, Request $request)  {
		return $this->service()->downloadImageAction($ouuid, $request);
    }
	
	
	/**
	 * @Route("/{_locale}/img/{ouuid}/preview.jpg",
     *   defaults={"_locale": "fr"},
	 *   name="previewImage")
	 */
	public function previewImageAction($ouuid, Request $request)  {
		return $this->service()->previewImageAction($ouuid, $request);
    }
    
    
    
}
