<?php

namespace App\Test\TestCase\Controller;

require_once __DIR__ . "/vendor/rogoss/Curl/Curl.php";

use Cake\TestSuite\TestCase;
use \rogoss\Curl\Curl;

/**
 * PagesControllerTest class
 */
class ApplicantsAPITest extends TestCase
{
    public static int $iUniqueID;

    public static array $aCleanupApplicants = [];
    public static array $aCleanupCities = [];

    public static function setUpBeforeClass(): void
    { self::$iUniqueID = time(); }

    // GET /api/applicants
    public function testGetApplicants(): void
    {
        $oResult = Curl::GET()->url("http://localhost:8081/api/applicants")
            ->exec();

        $this->_assertCurlSuccess($oResult);

        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody);

    }
    public function testGetApplicantsWithAcceptHeader(): void
    {
        $oResult = Curl::GET()->url("http://localhost:8081/api/applicants")
            ->header("accept", "application/json")
            ->exec();

        $this->_assertCurlSuccess($oResult);
        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody);

    }


    // POST /api/applicants
    public function testPostApplicantsSuccess()
    {
        $oResult = Curl::POST()->url("http://localhost:8081/api/applicants")
            ->header("content-type", "application/json")
            ->body(json_encode([
                [
                    "gender" => "female",
                    "title" => "",
                    "firstname" => "Maria " . self::$iUniqueID,
                    "lastname" => "Mustermann",
                    "addr_street" => "Musterstr. 123",
                    "addr_zip" => "00000",
                    "addr_city" => "Musterburg",
                    "country_id" => 63 /* Germany */
                ],

                [
                    "gender" => "male",
                    "title" => "Dr. ",
                    "firstname" => "John " . self::$iUniqueID,
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

        foreach ($aBody as $aApplicant) {
            $this->assertArrayHasKey("id", $aApplicant, "response was supposed to return the id of the created applicant");
            self::$aCleanupApplicants[] = $aApplicant['id'];
        }

    }

    public function testPostApplicantWithSameNameAndLocationTwice()
    {
        $oResult = Curl::POST()->url("http://localhost:8081/api/applicants")
            ->header("content-type", "application/json")
            ->body(json_encode([
                [
                    "gender" => "female",
                    "title" => "",
                    "firstname" => "Maria " . self::$iUniqueID,
                    "lastname" => "Mustermann",
                    "addr_street" => "Musterstr. 123",
                    "addr_zip" => "00000",
                    "addr_city" => "Musterburg",
                    "country_id" => 63 /* Germany */
                ],
            ]))
            ->exec();

        $this->_assertCurlSuccess($oResult);
        $this->assertNotEmpty($oResult->body);

        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody);
        $this->assertEquals(1, count($aBody), "Body should have returned exactly 1 element here");

        $this->assertArrayHasKey('id', $aBody[0]);
        $this->assertEquals(self::$aCleanupApplicants[0], $aBody[0]['id'], "this should have returned the already exsiting applicants id, but it did not");
    }

    public function testGetApplicant()
    {
        $this->assertGreaterThan(0, count(self::$aCleanupApplicants));

        $oResult = Curl::GET()->url("http://localhost:8081/api/applicants/" . self::$aCleanupApplicants[0])
            ->exec();

        $this->_assertCurlSuccess($oResult);
        $this->assertNotEmpty($oResult->body);

    }

    public function testPutApplicant()
    {

        $this->assertGreaterThan(1, count(self::$aCleanupApplicants));

        $aReqBody = [
            "gender" => "diverse",
            "title" => "",
            "firstname" => "Jane",
            "addr_street" => "Dummy Str. 13",
            "addr_zip" => " 1E58Z",
            "addr_city" => "Somewhere Else",
            "country_id" => 186 /* USA */
        ];

        $oResult = Curl::PUT()->url("http://localhost:8081/api/applicants/" . self::$aCleanupApplicants[1])
            ->header("content-type", "application/json")
            ->body(json_encode($aReqBody))
            ->exec();

        $this->_assertCurlSuccess($oResult);
        $this->assertNotEmpty($oResult->body);

        $aBody = json_decode($oResult->body, true);
        $this->assertIsArray($aBody, "did expecte and object to be returned");

        // Ids must match
        $this->assertEquals(self::$aCleanupApplicants[1], $aBody['id'], "id of requested element and changed element did not match.!!!");

        // check if zip-code was propperly normalized
        $this->assertEquals('1e58z', $aBody['addr_zip'], 'API was expected to lowercase + trim the zip-code');

        $this->_assertEqualProp($aReqBody, $aBody, 'gender');
        $this->_assertEqualProp($aReqBody, $aBody, 'title');
        $this->_assertEqualProp($aReqBody, $aBody, 'firstname');
        $this->_assertEqualProp($aReqBody, $aBody, 'addr_street');
        $this->_assertEqualProp($aReqBody, $aBody, 'country_id');

    }

    public function testDeleteApplicant()
    {
        $this->assertGreaterThan(0, count(self::$aCleanupApplicants));

        foreach (self::$aCleanupApplicants as $iApplicant) {
            $oResult = Curl::DELETE()->url("http://localhost:8081/api/applicants/" . $iApplicant)
                ->exec();

            $this->_assertCurlSuccess($oResult);
            $this->assertEquals("deleted", $oResult->body);
        }
    }

    public function testDeleteADeletedApplicant()
    {
        $oResult = Curl::DELETE()->url("http://localhost:8081/api/applicants/" . self::$aCleanupApplicants[0])
            ->exec();

        $this->_assertCurlSuccess($oResult);
        $this->assertEquals("already deleted", $oResult->body);

        self::$aCleanupApplicants = [];
    }


    // BM: Private Helpers
    //==========================================================================
    private function _assertCurlSuccess(mixed $oResult): void
    {
        $this->assertNotEmpty($oResult, "failed to fetch from: locahost:8081");
        $this->assertInstanceOf('\rogoss\Curl\FetchResult', $oResult, "failed to fetch from: locahost:8081");
        $this->assertEquals(200, $oResult->status);
    }

    private function _assertEqualProp($arr1, $arr2, $sProp)
    {
        $this->assertEquals($arr1[$sProp], $arr2[$sProp], "requested change  and made change did not match.!!!");
    }
}
