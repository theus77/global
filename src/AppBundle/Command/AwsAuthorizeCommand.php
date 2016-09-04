<?php

// src/AppBundle/Command/GreetCommand.php
namespace AppBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Elasticsearch\Client;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aws\ElasticsearchService\ElasticsearchServiceClient;

class AwsAuthorizeCommand extends ContainerAwareCommand
{
	/**@var Client $client*/
	protected  $client;
	protected $awsCredentials;
	protected $awsESDomainName;
	
	public function __construct(Client $client, $awsCredentials, $awsESDomainName)
	{
		$this->client = $client;
		$this->awsCredentials = $awsCredentials;
		$this->awsESDomainName = $awsESDomainName;
		parent::__construct();
	}


	protected function configure()
	{
		$this->setName('globalview:access:authorize')
			->setDescription('Allow access to the ES cluster from current IP')
			->addArgument(
				'IP',
				InputArgument::OPTIONAL,
				'IP to authorize'
			);
	}
	
	protected function geCurrentIP()
    {		
		$externalContent = file_get_contents('http://checkip.dyndns.com/');
		preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
		$externalIp = $m[1];
		return $externalIp;
	
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    	$ip = $input->getArgument('IP', null);
    	if(empty($ip)){
    		$output->writeln('Getting IP..');
    		$ip = $this->geCurrentIP();
    	}
		
		$output->writeln("Check AWS allow access to IP: $ip");
		
		
		$client  = new ElasticsearchServiceClient($this->awsCredentials);
		
		$config = $client->updateElasticsearchDomainConfig([
				'DomainName' => $this->awsESDomainName,
		]);
		
		$accessPolicies = json_decode($config['DomainConfig']['AccessPolicies']['Options'], true);
		
		foreach ($accessPolicies['Statement'] as $statement){
			if( isset($statement['Condition']) &&
					isset($statement['Condition']['IpAddress']) &&
					isset($statement['Condition']['IpAddress']['aws:SourceIp']) &&
					strcmp($statement['Condition']['IpAddress']['aws:SourceIp'], $ip) == 0 ){
						$output->writeln("$ip has already access");
						return;
			}
		}
		;
		
		$accessPolicies['Statement'][] = [
				'Sid' => '',
				'Effect' => 'Allow',
				'Principal' => [ 'AWS' => '*' ],
				'Action' => 'es:*',
				'Condition' => ['IpAddress' => [ 'aws:SourceIp' => [$ip]]],
				'Resource' => $accessPolicies['Statement'][0]['Resource'],
		
		];
		
		$config = $client->updateElasticsearchDomainConfig([
				'DomainName' => Configure::read('awsESDomainName'),
				'AccessPolicies' => json_encode($accessPolicies),
		]);
		
		$output->writeln("Access policies updated");
		
		$output->writeln("$ip is now registered to AWS, It will take some times to be enabled (around 20 minutes)");
		
    }
    


}