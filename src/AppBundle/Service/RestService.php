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
	private $mailer;
	private $templating;
	
	private $emsApi;
	private $emsLogin;
	private $emsUser;
	private $emsPwd;
	
	public function __construct(Client $client, RestClient $rest, $mailer, $templating, $emsApi, $emsLogin, $emsUser, $emsPwd){
		$this->client = $client;
		$this->rest = $rest;
		$this->mailer = $mailer;
		$this->templating = $templating;

		$this->emsApi = $emsApi;
		$this->emsLogin = $emsLogin;
		$this->emsUser = $emsUser;
		$this->emsPwd = $emsPwd;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return Response
	 */
	public function createObject($data, $type) {
		
		/**@var Response $authKey*/
		$authKey = $this->rest->post($this->emsLogin, json_encode([
			'username' => $this->emsUser,
			'password' => $this->emsPwd,
		]));
			
		$authKey = json_decode($authKey->getContent(), true)['authToken'];

		$out = $this->rest->post($this->emsApi.'/'.$type.'/draft', json_encode($data), [
				CURLOPT_HTTPHEADER => array('Content-type: text/plain', 'X-Auth-Token: '.$authKey),
		]);
		$revision_id = json_decode($out->getContent(), true)['revision_id'];
		$out = $this->rest->post($this->emsApi.'/'.$type.'/finalize/'.$revision_id, '', [
			CURLOPT_HTTPHEADER => array('Content-type: text/plain', 'X-Auth-Token: '.$authKey),
		]);
		$response = json_decode($out->getContent(), true);
		
		$message = \Swift_Message::newInstance()
		->setSubject('New '.$type.' on globalview')
		->setFrom('simon.globalview@gmail.com')
		->setTo('simon@globalview.be')
		->setBody(
				$this->templating->render(
						// app/Resources/views/Emails/registration.html.twig
						'emails/'.$type.'.html.twig',
						array('data' => $data, 'response' => $response)
						),
				'text/html'
				)
				/*
				 * If you also want to include a plaintext version of the message
		->addPart(
				$this->renderView(
						'Emails/registration.txt.twig',
						array('name' => $name)
						),
				'text/plain'
				)
		*/
		;
		$this->mailer->send($message);
		
		return $out;
	}
	

	
}