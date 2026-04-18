<?php
namespace AppBundle\Service;

use Elasticsearch\Client;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class WarmingService implements CacheWarmerInterface {

	const PAGE_SIZE = 100;
	
	/** @var Client $client*/
	private $client;
	private $prefix;
	private $environment;
	private $apertureIndex;
	/**@var ImageService $imageSrv*/
	private $imageSrv;
	
	public function __construct(Client $client, $prefix, $environment, $apertureIndex, ImageService $imageSrv){
		$this->client = $client;
		$this->environment = $environment;
		$this->prefix = $prefix;
		$this->apertureIndex = $apertureIndex;
		$this->imageSrv = $imageSrv;
	}
	
	public function isOptional(){
		return true;
	}
	

	public function warmUp($cacheDir){
		$page = 0;
		$continue = true;
		$progress = null;
		echo "Generate cached images";
		while($continue) {
			$result = $this->client->search([
					'index' => $this->apertureIndex,
					'size' => $this::PAGE_SIZE,
					'from' => $page*WarmingService::PAGE_SIZE,
					'type' => 'version',
					'preference' => '_primary', //http://stackoverflow.com/questions/10836142/elasticsearch-duplicate-results-with-paging
			]);
			if(empty($progress)){
				$output = new ConsoleOutput();
				$progress = new ProgressBar($output, $result['hits']['total']);
				// start and displays the progress bar
				$progress->start();
			}
			
			foreach ($result['hits']['hits'] as $version){
				try { 
					$this->imageSrv->imageImageAction($version['_id']);
					$this->imageSrv->previewImageAction($version['_id']);
					$this->imageSrv->thumbImageAction($version['_id']);	
					foreach ($version['_source']['Stack'] as $revId) {
						if($revId !== $version['_id']){
							$this->imageSrv->previewImageAction($revId);
						}
					}
				}
				catch (\Exception $e) {
					echo " Error with ".$version['_id'].": ".$e->getMessage().PHP_EOL;
				}
				// advance the progress bar 1 unit
				$progress->advance();
			}
			$continue = count($result['hits']['hits']) == WarmingService::PAGE_SIZE;
			++$page;
		}
		// ensure that the progress bar is at 100%
		$progress->finish();
		echo PHP_EOL;
	}
	
	
	
}