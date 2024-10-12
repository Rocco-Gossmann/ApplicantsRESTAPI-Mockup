<?php namespace App\Test\TestCase\Controller;

require_once __DIR__ . "/vendor/rogoss/Curl/Curl.php";

//use Cake\Core\Configure;
//use Cake\TestSuite\Constraint\Response\StatusCode;
//use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use \rogoss\Curl\Curl;

/**
 * PagesControllerTest class
 */
class ApplicantsAPITest extends TestCase {

    private $iTotalApplicantsCnt = 0;

    public function testGetApplicants() {
        $oResult = Curl::Get()->url("http://localhost:8081/api/applicants")
            ->header("accept", "application/json")
            ->exec()
        ;

        $this->_assertCurlSuccess($oResult);

        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody);

        $this->iTotalApplicantsCnt = count($aBody);
    }

    

    
    // BM: Private Helpers
    //==========================================================================
    private function _assertCurlSuccess($oResult) {
        $this->assertNotEmpty($oResult, "failed to fetch from: locahost:8081");
        $this->assertInstanceOf('\rogoss\Curl\FetchResult', $oResult, "failed to fetch from: locahost:8081");
        $this->assertEquals(200, $oResult->status);
    }
}