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
    	//Force returning the sample file
    	$service->setURLLoader(function($url) {
    		$doc = new DOMDocument();
    		$doc->load("sample.xml");
    		return $doc;
    	});
    	$results = $service->searchBrand("yyjkk", 1, $count);
    	$expected = array("b006wkqb" => array("The Chris Moyles Show", array(
							array("The Chris Moyles Show: 10/09/2012", "03:30:00"),
							array("The Chris Moyles Show: 06/09/2012", "03:30:00"),
							array("The Chris Moyles Show: Danny From The Script And Robbie Savage: 10 Shows To Go ...", "03:30:00"),
							array("The Chris Moyles Show: Jimmy Carr: 9 Shows To Go...", "03:30:00"),
							array("The Chris Moyles Show: Keith Lemon, John Bishop & Longman return: 6 Shows To Go...", "03:30:00"),
							array("The Chris Moyles Show: Stacey Solomon & Jon Culshaw: 8 Shows To Go...", "03:30:00"))),     
						"b00p2d9w" => array("The Chris Evans Breakfast Show", array(
							array("The Chris Evans Breakfast Show: Robbie Williams joins us for Breakfast", "02:59:00"),
							array("The Chris Evans Breakfast Show: Disappointing Childhood Toys", "02:59:00"),
							array("The Chris Evans Breakfast Show: The Wurzels", "02:59:00"),
							array("The Chris Evans Breakfast Show: James Nesbitt joins us for Breakfast", "02:59:00"))),
						"b0079gqk" => array("Chris Needs", array(
							array("Chris Needs: 09/09/2012", "02:00:00"),
							array("Chris Needs: 02/09/2012", "02:00:00"),
							array("Chris Needs: 10/09/2012", "03:00:00"),
							array("Chris Needs: 07/09/2012", "03:00:00"))),
						"p001d7dc" => array("Chris South", array(
							array("Chris South: 09/09/2012", "02:00:00"),
							array("Chris South: 02/09/2012", "02:00:00"))),      
						"p001htlt" => array("Chris Baxter", array(
							array("Chris Baxter: 10/09/2012", "03:00:00"),
							array("Chris Baxter: 07/09/2012", "03:00:00"))),
						"p004dfxm" => array("Chris Goreham At Breakfast", array(
							array("Chris Goreham At Breakfast: 10/09/2012", "02:30:00"),
							array("Chris Goreham At Breakfast: Pensioner's council tax fight", "02:30:00"))),      
						"b0072l8y" => array("Chris Hawkins", array(
							array("Chris Hawkins: 10/09/2012", "02:00:00"),
							array("Chris Hawkins: 09/09/2012", "02:00:00"),
							array("Chris Hawkins: 08/09/2012", "02:00:00"))),
						"p00d6ccb" => array("Chris Stone", array(
							array("Chris Stone: 09/09/2012", "03:00:00"),
							array("Chris Stone: 08/09/2012", "03:00:00"))));
		foreach ($results as $brand) {
			$brandId = $brand->getId();
			echo "\nAsserting $brandId\n";
			$this->assertEquals($expected[$brandId][0], $brand->getName());
			$pos = 0;
			foreach ($brand->getProgrammes() as $programme) {
				echo "\nAsserting ".$programme->getName()."\n";
				$this->assertEquals($expected[$brandId][1][$pos][0], $programme->getName());
				$this->assertEquals($expected[$brandId][1][$pos][1], $programme->getDuration(true));
				$pos++;
			}
		}
    }
}
?>