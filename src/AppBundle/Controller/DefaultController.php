<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\GlobalService;
use AppBundle\Form\ContactType;
use AppBundle\Service\RestService;
use Symfony\Component\HttpFoundation\Response;
use Elasticsearch\Client;
use AppBundle\Service\SearchService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function GuzzleHttp\json_decode;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends Controller
{
	/**
	 * @return GlobalService
	 */
	private function service(){
		return $this->get('app.global');
	}
	/**
	 * @return RestService
	 */
	private function rest(){
		return $this->get('app.rest');
	}
	/**
	 * @return Client
	 */
	private function client(){
		return $this->get('app.elasticsearch');
	}
	/**
	 * @return SearchService
	 */
	private function search(){
		return $this->get('app.search');
	}


	/**
	 * @Route("/n/bl/",
	 *   defaults={"_locale": "fr"},
	 *   name="unregister_email")
	 */
	public function unregisterEmailAction($_locale, Request $request)
	{
		//?e=email&c=nomduvol
		$data = [
			'email' => $request->query->get('e', null),
			'flight' => $request->query->get('e', null),
		];
		
		if(!empty($data['email'])){
			$response = $this->rest()->createObject($data, 'unregister');
			$this->addFlash('notice', 'unregister.sucessfull');
			
		}
		else{
			$this->addFlash('notice', 'unregister.failed');			
		}

		return $this->redirectToRoute('homepage');
	}

	/**
	 * @Route("/{_locale}/ask_price",
	 *   defaults={"_locale": "fr"},
	 *   name="ask_price")
	 */
	public function askPricePageAction($_locale, Request $request)
	{
		if($request->getMethod() == Request::METHOD_POST){
			$data = $request->request->all()['data']['PricingRequest'];
			$data['locale'] = $_locale;
			
			try {
				$date = new \DateTime();
		
				$data['date'] = $date->format('Y/m/d');
				
				if(isset($data['bestUuid'])){
					$data['bestUuid'] = array_values($data['bestUuid']);					
				}
				else {
					$data['bestUuid'] = null;
				}
		
				$response = $this->rest()->createObject($data, 'price_request');
		
				if($response->getStatusCode() != Response::HTTP_CREATED && $response->getStatusCode() != Response::HTTP_OK){
					$this->addFlash('warning', 'form.error.askprive_not_send');
				}
				else {
					$this->addFlash('notice', 'form.askprive_send');
				}
			}
			catch (\Exception $e) {
				$this->addFlash('warning', 'form.error.askprive_not_send');
			}
			
			$route = $request->query->get('_route', null);
			if(!empty($route)){
				return $this->redirect($route);
			}
			
			return $this->redirectToRoute('homepage');
		}

		$ouuid = $request->query->get('ouuid', null);
		
		if(empty($ouuid)) {
			throw new NotFoundHttpException('Ouuid is missing');
		}
		
		$source = $this->client()->get([
				'index' => $this->getParameter('aperture_index'),
				'type' => 'version',
				'id' => $ouuid,
		]);
		
		if(empty($source)) {
			throw new NotFoundHttpException('Version not found');
		}
		
		
		
		return $this->render('pages/askprice.html.twig', [
				'textes' => $this->service()->getTexts('ask_price'),
				'source' => $source,
				'body_id' => 'default',
		]);
	}
	
	

	/**
	 * @Route("/{_locale}/cc",
	 *   defaults={"_locale": "fr"},
	 *   name="clearcache")
	 */
	public function clearCacheeAction(Request $request) {
		$fs = new Filesystem();
		$fs->remove($this->container->getParameter('kernel.cache_dir'));
		$this->addFlash('notice', 'kernel.cache.clear');
		return $this->redirectToRoute('homepage');
	}
	
	
	/**
	 * @Route("/{_locale}",
     *   defaults={"_locale": "fr"},
	 *   name="homepage")
	 */
	public function homePageAction(Request $request)
    {
    	$form = $this->createForm(ContactType::class, [], [
    			'attr' => [
    					'class' => 'form-horizontal',
    					'id' => 'BookingHomeForm',
    			],
    	]);
    	
    	$form->handleRequest($request);
    	
    	if($form->isSubmitted()){
    		$message = $form->getData();
    		
    		try {
    			
    			if(empty($message['honeypot']) && !empty($request->request->get('g-recaptcha-response')) && $this->rest()->reCaptcha($request->request->get('g-recaptcha-response'))){
		    		$date = new \DateTime();
		    		
		    		$message['date'] = $date->format('Y/m/d');
		    		$message['language'] = $request->getLocale();
		    		
		    		$response = $this->rest()->createObject($message, 'contact');
		    		
		    		if($response->getStatusCode() != Response::HTTP_CREATED && $response->getStatusCode() != Response::HTTP_OK){
	    				$this->addFlash('warning', 'form.error.contact_not_send');
		    		}
		    		else {
	    				$this->addFlash('notice', 'form.contact_send');
		    		}    				
    			}
    			else {

    				$this->addFlash('warning', 'form.honeypot.detected');
    			}
    		}
    		catch (\Exception $e) {
    			$this->addFlash('warning', 'form.error.contact_not_send');
    		}
    		return $this->redirectToRoute('homepage');
    	}
    	
    	
        return $this->render('pages/homepage.html.twig', [
        		'flights' => $this->service()->nextFlights(),
        		'textes' => $this->service()->getTexts('homepage'),
        		'galleries' => $this->service()->getGalleriesOnTop(),
				'form' => $form->createView(),
				'stats' => $this->service()->getStats(),
        		'body_id' => 'homepage',
        ]);
    }
	
	
    
    /**
     * @Route("/{_locale}/downloads/{id}", name="download")
     */
    public function downloadAction($_locale, $id, Request $request) {
//         try{
            $result = $this->service()->getAsset($id);
            if($result['found']){
                $sha1 = $result['_source']['file']['sha1'];
                $path = $this->getParameter('storage_path').'/'.substr($sha1, 0, 3).'/'.$sha1;
                $response = new BinaryFileResponse($path);
                $response->headers->set('Content-Type', $result['_source']['file']['mimetype']);
                $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $result['_source']['file']['filename']);
        
                return $response;
            }
            else{
                throw new NotFoundHttpException('File not found');
            }
//         }
//         catch(\Exception $e){
//             echo $e->getMessage();
//             throw new NotFoundHttpException('File not found');
//         }
    }
	
	
	/**
	 * @Route("/{_locale}/usage",
     *   defaults={"_locale": "fr"},
	 *   name="usage")
	 */
	public function usagePageAction(Request $request)
    {
        return $this->render('pages/usage.html.twig', [
        		'textes' => $this->service()->getTexts('usage'),
        		'body_id' => 'default',
        ]);
    }


    /**
     * @Route("/{_locale}/louvain-la-haut",
     *   defaults={"_locale": "fr"},
     *   name="louvain_la_haut")
     */
    public function louvaiHaHautAction(Request $request)
    {
        return $this->render('pages/louvain-la-haut.html.twig', [
            'textes' => $this->service()->getTexts('louvain-la-haut'),
            'body_id' => 'default',
        ]);
    }
	
	
	/**
	 * @Route("/{_locale}/map",
     *   defaults={"_locale": "fr"},
	 *   name="map")
	 */
	public function mapPageAction(Request $request)
    {
        return $this->render('pages/map.html.twig', [
        		'textes' => $this->service()->getTexts('map'),
        		'body_id' => 'map',
        ]);
    }
	
	
	/**
	 * @Route("/{_locale}/markers.json",
     *   defaults={"_locale": "fr", "_format": "json"},
	 *   name="markers")
	 */
	public function markersAction($_format, Request $request) {
		$config = [
				'zoom' => $request->query->get('zoom', '7'),
				'north' => $request->query->get('north', '51.989306167159135'),
				'south' => $request->query->get('south', '48.992143061271385'),
				'east' => $request->query->get('east', '9.50024638231946'),
				'west' => $request->query->get('west', '-0.5522438520555397'),
		];
		
		$data = $this->service()->getMarkers($config);
		return $this->render('map/markers.json.twig', [
				'markers' => $data['markers'],
				'aggregated' => $data['aggregated'],
		]);
	}

	
	
	/**
	 * @Route("/{_locale}/keywords",
     *   defaults={"_locale": "fr"},
	 *   name="keywords")
	 */
	public function keywordsPageAction(Request $request)
    {
        return $this->render('pages/keywords.html.twig', [
        		'textes' => $this->service()->getTexts('keywords'),
        		'body_id' => 'default',
        		'keywords' => $this->service()->getKeywords($request->getLocale()),
        ]);
    }

   
    /**
     * @Route("/{_locale}/book/{id}",
     *   defaults={"_locale": "fr"},
     *   name="book")
     */
    public function bookPageAction($id, Request $request)
    {
    	$flight = $this->service()->getFlight($id);
    	$place = $this->service()->getPlace(explode(':', $flight['_source']['place'])[1]);
    	
    	if($request->getMethod() === Request::METHOD_POST ){
	    	$message = $request->request->get('data')['Booking'];
	    	
	    	try {
	    		$date = new \DateTime();
	    		 
	    		$message['date'] = $date->format('Y/m/d');
	    		$message['language'] = $request->getLocale();
	    		$message['flight'] = 'flight:'.$id;

	    		$response = $this->rest()->createObject($message, 'booking');
	    		 
	    		if($response->getStatusCode() != Response::HTTP_CREATED && $response->getStatusCode() != Response::HTTP_OK){
	    			$this->addFlash('warning', 'form.error.book_not_send');
	    		}
	    		else {
	    			$this->addFlash('notice', 'form.book_send');
	    		}
	    	}
	    	catch (\Exception $e) {
	    		$this->addFlash('warning', 'form.error.book_not_send');
	    	}
	    	return $this->redirectToRoute('homepage');
    	}
    	
    	return $this->render('pages/book.html.twig', [
    			'textes' => $this->service()->getTexts('book'),
    			'flight' => $flight,
    			'place' => $place,
    			'body_id' => 'default',
    	]);
    }
    
    
	/**
	 * @Route("/{_locale}/hint.json",
	 *   defaults={"_locale": "fr", "_format": "json"},
	 *   name="search_ajax")
	 */
	public function searchAjaxPageAction(Request $request)
	{
		return $this->render('pages/gallery.html.twig', [
				'textes' => $this->service()->getTexts('gallery'),
				'body_id' => 'gallery',
		]);
	}
    
	/**
	 * @Route("/{_locale}/search.{_format}",
	 *   defaults={"_locale": "fr", "_format": "html"},
	 *   name="search")
	 */
	public function searchGalleryPageAction($_format, $_locale, Request $request)
	{
		$q = $request->query->get('q', null);
		$p = $request->query->get('page', 0);
		return $this->render('pages/gallery.'.$_format.'.twig', [
				'textes' => $this->service()->getTexts('gallery'),
				'versions' => $this->search()->freeSearch($q, $p, $_locale),
				'title' => 'gallery.freesearch.title',
				'title_arg' => $q,
				'body_id' => 'default',
				'stats' => $this->service()->getStats(),
        		'from' => $p*100,
		]);
	}
    
	
	/**
	 * @Route("/{_locale}/place/{ouuid}.{_format}",
     *   defaults={"_locale": "fr", "_format": "html"},
	 *   name="place")
	 */
	public function placeGalleryPageAction($ouuid, $_locale, $_format, Request $request)
    {
		$p = $request->query->get('p', null);
		$title_arg = 'not found';
    	$versions = $this->search()->placeSearch($ouuid, $p);
    	$level = 0;
    	if(!empty($versions['hits']['hits'])) {
//     		dump($versions['hits']['hits'][0]);
    		foreach ($versions['hits']['hits'][0]['_source']['locations'] as $place){
    			if($place['uuid'] == $ouuid){
    				$title_arg = $place['name_'.$_locale];
    				$level = $place['level'];
    				break;
    			}
    		}
    	}
    	    	
		$p = $request->query->get('page', 0);
        return $this->render('pages/gallery.'.$_format.'.twig', [
				'textes' => $this->service()->getTexts('gallery'),
				'versions' => $this->search()->placeSearch($ouuid, $p),
				'title' => 'gallery.place.'.$level.'.title',
				'title_arg' => $title_arg,
				'body_id' => 'gallery',
        		'from' => $p*100,
        ]);
    }
	
	/**
	 * @Route("/{_locale}/keyword/{key}.{_format}",
     *   defaults={"_locale": "fr", "_format": "html"},
	 *   name="keyword")
	 */
	public function keywordGalleryPageAction($key, $_locale, $_format, Request $request)
    {
		$p = $request->query->get('p', null);
		$title_arg = 'not found';
    	$versions = $this->search()->keywordSearch($key, $p);
    	if(!empty($versions['hits']['hits'])) {
//     		dump($versions['hits']['hits'][0]);
    		foreach ($versions['hits']['hits'][0]['_source']['Keywords'] as $keyword){
    			if($keyword['uuid'] == $key){
    				$title_arg = $keyword['name_'.$_locale];
    				break;
    			}
    		}
    	}
		$p = $request->query->get('page', 0);
        return $this->render('pages/gallery.'.$_format.'.twig', [
				'textes' => $this->service()->getTexts('gallery'),
				'versions' => $versions,
				'title' => 'gallery.keyword.title',
				'title_arg' => $title_arg,
				'body_id' => 'gallery',
        		'from' => $p*100,
        ]);
    }
	
	/**
	 * @Route("/{_locale}/info.json",
     *   defaults={"_locale": "fr", "_format": "json"},
	 *   name="info")
	 */
	public function infoAction($_locale, Request $request)
    {
		$ouuid = $request->query->get('ouuid', '');
		
        return $this->render('pages/info.json.twig', [
				'info' => $this->service()->getImageInfo($ouuid),
        ]);
    }
	
	/**
	 * @Route("/{_locale}/gallery/{slug}.{_format}",
     *   defaults={"_locale": "fr", "_format": "html"},
	 *   name="gallery")
	 */
	public function galleryPageAction($slug, $_locale, $_format, Request $request)
    {
		$result = $this->client()->search([
				'index' => $this->getParameter('prefix').$this->getParameter('environment'),
				'type' => 'gallery',
				'body' => [
					'query' => [
						'term' =>[ 
							'slug_'.$_locale => [
								'value'=> $slug
							]
						]
					]
				]
		]);
		
		if( empty($result) || empty($result['hits']['hits']) ){
			throw new NotFoundHttpException('Gallery not found');
		}
		
		$p = $request->query->get('page', 0);
        return $this->render('pages/gallery.'.$_format.'.twig', [
				'textes' => $this->service()->getTexts('gallery'),
				'versions' => $this->search()->execute(json_decode($result['hits']['hits'][0]['_source']['query'], true), $p),
				'title' => 'gallery.gallerie.title',
				'title_arg' => $result['hits']['hits'][0]['_source']['label_'.$_locale],
        		'translateArgs' => [
        				'fr' => ['slug' => $result['hits']['hits'][0]['_source']['slug_fr']],
        				'nl' => ['slug' => $result['hits']['hits'][0]['_source']['slug_nl']],
        				'en' => ['slug' => $result['hits']['hits'][0]['_source']['slug_en']],
        		],
				'body_id' => 'gallery',
        		'from' => $p*100,
        ]);
    }
	
	/**
	 * @Route("/{_locale}/geohash/{hash}.{_format}",
     *   defaults={"_locale": "fr", "_format": "html"},
	 *   name="geohash")
	 */
	public function geohashGalleryPageAction($hash, $_locale, $_format, Request $request)
    {
		$p = $request->query->get('p', null);
		$title_arg = null;
    	$versions = $this->search()->geohashSearch($hash, $p);
    	if(!empty($versions['hits']['hits'])) {
    		if(isset($versions['hits']['hits'][0]['_source']['label'])){
	    		$title_arg = $versions['hits']['hits'][0]['_source']['label'];			
    		}
    		else {
	    		$title_arg = $versions['hits']['hits'][0]['_source']['name'];			
    		}
    	}
    	
		$p = $request->query->get('page', 0);
        return $this->render('pages/gallery.'.$_format.'.twig', [
				'textes' => $this->service()->getTexts('gallery'),
				'versions' => $versions,
				'title' => 'gallery.geohash.singular.title',
        		'pluralTitle' => 'gallery.geohash.plural.title',
				'title_arg' => $title_arg,
				'body_id' => 'default',
        		'from' => $p*100,
        ]);
    }
	
    
    
    
}
