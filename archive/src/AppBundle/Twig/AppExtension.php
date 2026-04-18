<?php
namespace AppBundle\Twig;

use Elasticsearch\Client;
use Symfony\Component\Routing\Router;

class AppExtension extends \Twig_Extension
{
	/**@var Client $client */
	private $client;
	/**@var Router $router*/
	private $router;
	/**@var \Twig_Environment $twig*/
	private $twig;
	private $prefix;
	private $environment;
	
	public function __construct(Client $client, Router $router, \Twig_Environment $twig, $prefix, $environment) {
		$this->client = $client;
		$this->router = $router;
		$this->twig = $twig;
		$this->prefix = $prefix;
		$this->environment = $environment;
	}

	public function getName() {
		return 'app_extension';
	}
	
	public function getFilters() {
		return array(						
				new \Twig_SimpleFilter('internal_links', array($this, 'internalLinks')),		
				
				
		);
	}

	function internalLinks($input, $locale) {
					
		$out = $input;
		$url = $this->router->generate('download', ['_locale' => $locale, 'id'=>'_id_']);
		$url = substr($url, 0, strlen($url)-4);
		$out = preg_replace('/ems:\/\/object:fichier:/i', $url, $out);
		$out = preg_replace('/ems:\/\/asset:fichier:/i', $url, $out);
		

		return $out;
	}
	
}