<?php

namespace App\Test\TestCase\Controller;

require_once __DIR__ . "/vendor/rogoss/Curl/Curl.php";

//use Cake\Core\Configure;
//use Cake\TestSuite\Constraint\Response\StatusCode;
//use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use \rogoss\Curl\Curl;

/**
 * PagesControllerTest class
 */
class ApplicantsAPITest extends TestCase
{

    private array $aCleanupApplicants = [];

    // GET /api/applicants
    public function testGetApplicants(): void
    {
        $oResult = Curl::GET()->url("http://localhost:8081/api/applicants")
            ->debug(true)
            ->exec();

        $this->_assertCurlSuccess($oResult);

        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody);

    }
    // TODO: Implement Test to make sure Accept Header gets ignored
    //       Right now id does not

    // POST /api/applicants
    public function testPostApplicantsSuccess(): void
    {
        $oResult = Curl::POST()->url("http://localhost:8081/api/applicants")
            ->header("content-type", "application/json")
            ->body(json_encode([
                [
                    "gender" => "female",
                    "title" => "",
                    "firstname" => "Maria",
                    "lastname" => "Mustermann",
                    "addr_street" => "Musterstr. 123",
                    "addr_zip" => "00000",
                    "addr_city" => "Musterburg",
                    "country_id" => 63 /* Germany */
                ],

                [
                    "gender" => "male",
                    "title" => "Dr. ",
                    "firstname" => "John",
                    "lastname" => "Doe",
                    "addr_street" => "Unknown Str. 78",
                    "addr_zip" => "12D89",
                    "addr_city" => "Somewhere",
                    "country_id" => 186 /* USA */
                ],
            ]))
            ->exec();

        $this->_assertCurlSuccess($oResult);
        $this->assertNotEmpty($oResult->body);

        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody);
        $this->assertEquals(2, count($aBody), "Expected two elements in respone");

        foreach($aBody as $aApplicant)  {
            $this->assertArrayHasKey("id", $aApplicant, "response was supposed to return the id of the created applicant");
            $this->aCleanupApplicants[] = $aApplicant['id'];
        }

    }

    // BM: Private Helpers
    //==========================================================================
    private function _assertCurlSuccess(mixed $oResult): void
    {
        $this->assertNotEmpty($oResult, "failed to fetch from: locahost:8081");
        $this->assertInstanceOf('\rogoss\Curl\FetchResult', $oResult, "failed to fetch from: locahost:8081");
        $this->assertEquals(200, $oResult->status);
    }
}
