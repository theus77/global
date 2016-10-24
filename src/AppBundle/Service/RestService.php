<?php
namespace AppBundle\Service;

use Elasticsearch\Client;
use Circle\RestClientBundle\Services\RestClient;
use Symfony\Component\HttpFoundation\Response;

class RestService {
	
	/**@var Client $client*/
	private $client;
	/**@var RestClient $rest*/
	private $rest;
	private $emsRestEndPoint;
	private $apiKey;
	
	public function __construct(Client $client, RestClient $rest, $emsRestEndPoint, $apiKey){
		$this->client = $client;
		$this->rest = $rest;
		$this->emsRestEndPoint = $emsRestEndPoint;
		$this->apiKey = $apiKey;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return Response
	 */
	public function createObject($data, $type) {
		dump(json_encode($data));
		return $this->rest->post($this->emsRestEndPoint.'/'.$type.'/draft?apikey='.$this->apiKey, json_encode($data));	
	}
	

	
}