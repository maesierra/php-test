<?php
require_once '../src/programmeFinder.php';
class ProgrammeFinderServiceTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test to check error processing in case of URL loading errors 
	 */
    public function _testSearchBrandError()
    {
        $service = new ProgrammeFinderService();
        //Point to a non existing URL
        $url = ProgrammeFinderService::getServiceURL(); 
        ProgrammeFinderService::setServiceURL("http://gg.jlkjk/ff/");
        $count = 0;
        try {
        	$service->searchBrand("q", 1, $count);
        	$this->fail("No exception thrown"); //If we reach here the exception wasn't thrown
        } catch (Exception $e) {
        	$this->assertEquals("External service error", $e->getMessage());
        }
        ProgrammeFinderService::setServiceURL($url); //restore the original URL 
    }
    
    public function testSearchBrand() {
    	$count = 0;
    	$service = new ProgrammeFinderService();
    	$service->setURLLoader(function($url) {
    		$doc = new DOMDocument();
    		$doc->load("sample.xml");
    		echo "eo";
    		return $doc;
    	});
    	$service->searchBrand("yyjkk", 1, $count);
    }
}
?>