<?php
require_once("Brand.php");

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
			return call_user_func_array($this->urlLoader,array($url));
		}
		$doc = new DOMDocument();
		$doc->load($url);
		return $doc;
			
	}
	
	/**
	 * Returns the node value for a child with the name $property
	 * @param DOMElement $node node 
	 * @param String $property child's name
	 * @param DOMXPath $xpath xpath instance for the node's document
	 * @return String property value
	 * @throws Exception if no value found
	 */
	private function getPropertyValue($property, $node, $xpath) {
		$entries = $xpath->evaluate("ion:$property", $node);
		if ($entries->length == 0) {
			throw new Exception("External service parsing error");
		}
		return $entries->item(0)->nodeValue;
			
	}
	
	/**
	 * Creates a new brand object using the node information
	 * @return Brand
	 */
	private function createBrand($brandId, $node, $xpath) {
		return new Brand($brandId, $this->getPropertyValue("brand_title", $node, $xpath));
			
	}
	
	/**
	 * Creates a new Programme using the node information
	 * @return Programme
	 */
	private function createProgramme($node, $xpath) {
		return new Programme($this->getPropertyValue("complete_title", $node, $xpath), $this->getPropertyValue("duration", $node, $xpath));
	}
	
	/**
	 * Search the service for programmes matching the query
	 * @param string $query search query
	 * @param integer $page search page (1 by default)
	 * @param boolean $more reference that will be set to true if there are more results
	 * @return array matching results or a empty array, upto 
	 */
	public function searchBrand($query, $page=1, &$more) {
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
			//Check if the brandId is already in the array
			if (!isset($brands[$brandId])) {
				//Create the brand object
				$brands[$brandId] =  $this->createBrand($brandId, $n->parentNode, $xpath);
			}			
			//Create programme info and add it to the brand
			$programme = $this->createProgramme($n->parentNode, $xpath);
			$brands[$brandId]->addProgramme($programme);
		}		
		//count the total and check if we reached the end
		$total = $xpath->query( "//ion:total_count")->item(0)->nodeValue; 
		$more = ($total > self::$PAGE_SIZE * $page); 
		return $brands; 
			 
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