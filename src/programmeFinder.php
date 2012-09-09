<?php
/**
 * BBC iPlayer radio programme finder client
 * @author María-Eugenia Sierra
 *
 */
class ProgrammeFinderService {
	
	/**
	 * URL for the REST service
	 * @var String
	 */
	private static $SERVICE_URL="http://www.bbc.co.uk/iplayer/ion/";

	/**
	 * Number of brands shown per page
	 * @var integer
	 */
	private static $PAGE_SIZE = 25;
	
	/**
	 * Closure to set a custom URL loader
	 * @var Closure
	 */
	private $urlLoader; 
	
	/**
	 * Creates and sends a REST request to the service
	 * @param String $operation operation name
	 * @param associative array $params
	 * @returns  SimpleXMLElement
	 */
	private function sendRequest($operation, $params) {
		$url = self::$SERVICE_URL.$operation;
		foreach ($params as $name=>$value) {
			$url .= "/$name/$value";
		}
		//use the url loader if present
		if (isset($this->urlLoader)) {
			call_user_func_array($this->urlLoader,array($url));
		}
		$doc = new DOMDocument();
		$doc->load($url);
		return $doc;
			
	}
	
	/**
	 * Search the service for programmes matching the query
	 * @param string $query search query
	 * @param integer $page search page (1 by default)
	 * @param integer $count reference to store the total count
	 * @return array matching results or a empty array, upto 
	 */
	public function searchBrand($query, $page=1, &$count) {
		$params = array(
			"search_availability" => "iplayer",
			"service_type" => "radio",
			"format" => "xml",
			"page" => $page,
			"perpage" => self::$PAGE_SIZE,
			"q" => $query				
		);
		try {
		$doc = $this->sendRequest("searchextended", $params);
		} catch (Exception $e) {
			echo $e->getMessage();			
			throw new Exception("External service error");
		}
		$xpath = new DOMXPath( $doc );
		$xpath->registerNamespace("ion", "http://bbc.co.uk/2008/iplayer/ion");
		$nodelist = $xpath->query( "//ion:brand_id"); //Search for all the brand ids
		$brands = array();
		foreach ($nodelist as $n){
			$brandId = $n->nodeValue;
			if (isset($brands[$brandId])) {
				$brands[$brandId][] = count($brands[$brandId]) + 1;
			} else {
				$brands[$brandId] =  array(1);
			}			
    		
		} 
		print_r($brands);
			 
	}
	/**
	 * For testing purposes, allows to set the service URL
	 * @param String $url
	 */
	public static function setServiceURL($url)  {
		self::$SERVICE_URL = $url;
	}
	/**
	 * Returns the current service URL
	 * @return string 
	 */
	public static function getServiceURL() {
		return self::$SERVICE_URL;
	}
	
	/**
	 * sets an url loader to force custom content and allow testing
	 * @param Closure $urlLoader
	 */
	public function setURLLoader($urlLoader) {
		$this->urlLoader = $urlLoader;
	}
	
}  