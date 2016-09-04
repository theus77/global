<?php
namespace AppBundle\Translation;

use Elasticsearch\Client;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class ElasticLoader implements LoaderInterface
{
	/** @var Client $client*/
	protected $client;
	protected $config;
	
	public function __construct(Client $client, $config){
		$this->client = $client;
		$this->config = $config;
	}
	
	
	public function load($resource, $locale, $domain = 'messages'){
		
		$catalogue = new MessageCatalogue($locale);
		
// 		echo 'Load translation for domain: '.$domain.' and language '.$locale.PHP_EOL;
	
		if(isset($this->config[$domain])){
			$pageSize = 10;
			
			$param = [
				'preference' => '_primary', //http://stackoverflow.com/questions/10836142/elasticsearch-duplicate-results-with-paging
				'from' => 0,
				'size' => 0,
				'index' => $this->config[$domain]['alias'],
				'type' => $this->config[$domain]['type']
			];
			
	
			$result = $this->client->search($param);
			$total = $result["hits"]["total"];
// 			dump($total);
			
			$param['size'] = $pageSize;
			while($param['from'] < $total){
				$result = $this->client->search($param);
				$messages = [];
				foreach ($result["hits"]["hits"] as $data){
					if(isset($data['_source']['label_'.$locale])){
						$messages[$data['_source']['key']] = $data['_source']['label_'.$locale];				
					}
				}
				$catalogue->add($messages, $domain);
				$param['from'] += $pageSize;
			}
		}
		else {
// 			echo 'Config is missing for translation domain '.$domain.PHP_EOL;
		}
		return $catalogue;
	}
}