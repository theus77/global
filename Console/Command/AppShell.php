<?php
use Elasticsearch\Common\Exceptions\Forbidden403Exception;
use Elasticsearch\ClientBuilder;
use Aws\ElasticsearchService\ElasticsearchServiceClient;
/**
 * AppShell file
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 */

App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
	
	public function initialize()
	{
		parent::initialize();
	}
	
	private function allowAccessToES(){
		$this->out("Checking IP....");
	
		$externalContent = file_get_contents('http://checkip.dyndns.com/');
		preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
		$externalIp = $m[1];
	
	
		$this->out("Check ES allow access to IP: $externalIp");
	
	
		$client  = new ElasticsearchServiceClient(Configure::read('aws.credentials'));
	
		$config = $client->updateElasticsearchDomainConfig([
				'DomainName' => Configure::read('awsESDomainName'),
		]);
	
		$accessPolicies = json_decode($config['DomainConfig']['AccessPolicies']['Options'], true);
	
		foreach ($accessPolicies['Statement'] as $statement){
			if( isset($statement['Condition']) &&
					isset($statement['Condition']['IpAddress']) &&
					isset($statement['Condition']['IpAddress']['aws:SourceIp']) &&
					strcmp($statement['Condition']['IpAddress']['aws:SourceIp'], $externalIp) == 0 ){
						$this->out("$externalIp has already access");
						return;
			}
		}
		;
	
		$accessPolicies['Statement'][] = [
				'Sid' => '',
				'Effect' => 'Allow',
				'Principal' => [ 'AWS' => '*' ],
				'Action' => 'es:*',
				'Condition' => ['IpAddress' => [ 'aws:SourceIp' => [$externalIp]]],
				'Resource' => $accessPolicies['Statement'][0]['Resource'],
	
		];
	
		$config = $client->updateElasticsearchDomainConfig([
				'DomainName' => Configure::read('awsESDomainName'),
				'AccessPolicies' => json_encode($accessPolicies),
		]);
	
		$this->out("Access policies updated");

		$this->out("$externalIp is now registered to AWS, It will take some times to be enabled (around 5 minutes)");
	}
	
	public function main()
	{
		$this->out('This script will register your internet IP to AWS');
		$this->allowAccessToES();
		$index = Configure::read('Config.apertureIndex');
		
		
		$this->client = ClientBuilder::create()			// Instantiate a new ClientBuilder
			->setHosts(Configure::read('awsESHosts'))	// Set the hosts
			->build();            						// Build the client object
		
		while(true) {
			try {
				$this->client->indices()->exists(['index' => $index]);
			}
			catch (Forbidden403Exception $e) {
				$this->out("Access forbidden to ES, try again in 3 minutes ...");
				sleep(180);
				continue;
			}
			break;
		}
		
	}
	

}
