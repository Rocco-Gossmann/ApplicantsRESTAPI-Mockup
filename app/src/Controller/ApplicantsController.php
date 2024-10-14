<?php

namespace App\Controller;

use Cake\Database\Exception\QueryException;

class ApplicantsController extends \App\Controller\AppController
{
    /** @var array - When POSTing or PUTing data these fields can be passed in by the Client
     *  [ [API fieldname] => [ settings ] ]
     *
     * The following settings are possible:
     * table: the table, the data is stored in
     *
     * column: the field inside `table` the data is stored in
     *
     * required: if true, the field must not be empty
     *
     * default: value that is set if field is empty and `required` is false/not set
     *          overrides required if set
     *
     * type:  enum -> reads options from table column
     *        string -> raw string
     *        ref -> reference from other table
     *
     * options: [if `type' == 'enum' ] valid options, that can be inserted
     *
     * reftab: [if `type` == 'ref'] table that is referenced
     *
     * refcol: [if `type' == 'ref'] column inside `reftab` that is referenced
     */
    private static $aPOSTFieldMap = [
        "gender" => [
            'table' => "applicants",
            'column' => 'a_gender',
            'type' => 'enum',
            'default' => 'no_comment',
            'options' => ["male", "female", "diverse", "no_comment"]
        ],
        "title" => [
            'table' => "applicants",
            'column' => 'a_title',
        ],
        "firstname" => [
            'table' => "applicants",
            'column' => 'a_firstname',
            'required' => true,
        ],
        "lastname" => [
            'table' => "applicants",
            'column' => 'a_lastname',
            'required' => true,
        ],
        "addr_street" => [
            'table' => "applicants",
            'column' => 'a_city_street',
            'required' => true
        ],
        "addr_zip" => [
            'table' => "cities",
            'column' => 'ci_zip',
            'required' => true
        ],
        "addr_city" => [
            'table' => "cities",
            'column' => 'ci_name',
            'required' => true
        ],
        "country_id" => [
            'table' => "cities",
            'column' => 'co_id',
            'type' => 'ref',
            'reftab' => "countries",
            'refcol' => 'co_id',
            'required' => true
        ]
    ];

    /** @var array - Maps Database-Column-Names to API-Fieldnames
     * Columns, that are either referenced by the applicants Table
     * or are not contained in self::$aPOSTFieldMap
     * values from the self::$aPOSTFieldMap
     *
     * [
     *  [Database Table Name] => [
     *      [Column Name] => [API Fieldname] ]
     *  ]
     * ]
     */
    private static $aGETDBColumnMap = [
        "applicants" => [
            'a_id' => 'id',             // Not part of POST-Fields
            'ci_name' => 'addr_city',   // Reference to Cities Table
            'ci_zip' => 'addr_zip',     // Reference to Cities Table
            'co_id' => 'country_id',    // Reference to Countries Table in Cities
        ]
    ];

    /** @var array - Maps what API-Field corresponds to what Database Field for a Put-Reqeust
     *  content will be automatically derived from  self::$aPOSTFieldMap
     * [
     *  [Database Table Name] => [
     *      [Column Name] => [API Fieldname] ]
     *  ]
     * ]
     */
    private static $aPUTDBColumnMap = [];

    /** @var \App\Model\Table\ApplicantsTable */
    private static $_ApplicantsTable;

    /** @var \App\Model\Table\CitiesTable */
    private static $_CitiesTable;


    private static $bInitialized = false;
    private function _initClass()
    {
        if (self::$bInitialized)
            return;

        foreach (self::$aPOSTFieldMap as $sField => $aSettings) {

            // Replace defined Table names with ConnectionInterfaces
            if (isset($aSettings['table']))
                self::$aPOSTFieldMap[$sField]['table'] = $this->fetchTable($aSettings['table']);

            if (isset($aSettings['reftab']))
                self::$aPOSTFieldMap[$sField]['reftab'] = $this->fetchTable($aSettings['reftab']);

            // Hook  $aPOSTFieldMap into $aGETDBColumnMap
            self::$aGETDBColumnMap[$aSettings['table']] = self::$aGETDBColumnMap[$aSettings['table']] ?? [];
            self::$aGETDBColumnMap[$aSettings['table']][$aSettings['column']] = $sField;

            self::$aPUTDBColumnMap[$aSettings['table']] = self::$aPUTDBColumnMap[$aSettings['table']] ?? [];
            self::$aPUTDBColumnMap[$aSettings['table']][$sField] = $aSettings['column'];
        }

        // Load Common ConnectionInterfaces
        self::$_ApplicantsTable = $this->fetchTable("applicants");
        self::$_CitiesTable = $this->fetchTable("cities");

        self::$bInitialized = true;
    }

    // BM: Route Handlers
    //==========================================================================
    /** GET /api/applicants  - Route
     * @return void
     */
    public function getApplicants()
    {
        $this->_initClass();
        $this->_json_response($this->_getApplicantsArray());
    }

    /**
     * POST /api/applicants  - Route
     * @return void
     */
    public function postApplicants()
    {
        $this->_initClass();

        $aList = $this->request->getData();

        // Validate Request-Data
        if (empty($aList))
            return $this->_status_response(400 /* Bad Request */ , "Request has no data");

        if (!is_array($aList))
            return $this->_status_response(400 /* Bad Request */ , "Request-Body is not an array.");

        if (count(array_filter(array_keys($aList), fn($e) => !is_numeric($e))))
            return $this->_status_response(400 /* Bad Request */ , "Request-Body must be a list of elements, not the element itself");

        // Start Writing Data to DB
        self::$_ApplicantsTable->getConnection()->begin();

        /** @var int[] - keep track of what Applicants have been created for output later*/
        $aRegisteredApplicants = [];

        try {
            // Foreach Applicant
            foreach ($aList as $iIDX => $aRequestApplicant) {
                $sErrPrefix = "Applicant {$iIDX}";
                // Validate the Insert Object
                if (!$this->_validateRequestObject($aRequestApplicant, $sErrPrefix))
                    throw new \Exception("invalid request object");

                $oCity = $this->_getCity(
                    $aRequestApplicant['addr_zip'],
                    $aRequestApplicant['addr_city'],
                    (int) $aRequestApplicant['country_id']
                );

                // Check if an Applicant may already exists
                $oApplicant = self::$_ApplicantsTable->find()->select("a_id")->where([
                    "a_firstname" => $aRequestApplicant['firstname'],
                    "a_lastname" => $aRequestApplicant['lastname'],
                    "ci_id" => (int) $oCity->ci_id,
                ])->first();


                if(empty($oApplicant)) {
                    $oApplicant = self::$_ApplicantsTable->newEmptyEntity();
                    $oApplicant->a_gender = $aRequestApplicant['gender'];
                    $oApplicant->a_title = $aRequestApplicant['title'];
                    $oApplicant->a_firstname = $aRequestApplicant['firstname'];
                    $oApplicant->a_lastname = $aRequestApplicant['lastname'];
                    $oApplicant->a_city_street = $aRequestApplicant['addr_street'];
                    $oApplicant->ci_id = (int) $oCity->ci_id;

                    self::$_ApplicantsTable->save($oApplicant);
                    if (empty($oApplicant)) {
                        $this->_status_response(500 /* Internal Error */ , "{$sErrPrefix} => Failed to register applicant");
                        throw new \Exception("failed to created applicant in db");
                    }
                }

                $aRegisteredApplicants[] = $oApplicant->a_id;
            }

            self::$_ApplicantsTable->getConnection()->commit();

        } catch (\Exception $ex) {
            self::$_ApplicantsTable->getConnection()->rollback();
            return $this->_status_response(500, $ex->getMessage());
        }

        // Respond with an  Array of all newly registered Applicants
        $this->_json_response($this->_getApplicantsArray($aRegisteredApplicants));

    }

    /**
     * GET /api/applicants/:id - Route
     * @return void
     */
    public function getApplicant()
    {
        $this->_initClass();

        if (empty($iApplicantID = (int) $this->request->getParam("id")))
            return $this->_status_response(400 /* Bad Request */ , "no applicant id given ");

        $aApplicants = $this->_getApplicantsArray([$iApplicantID]);
        if(empty($aApplicants))
            return $this->_status_response(404 /* Not Found */, "applicant not found");

        return $this->_json_response($aApplicants[0] ?? []);
    }

    /** PUT /api/applicants/:id - Route
     * @return void
     */
    public function putApplicant()
    {
        $this->_initClass();

        if (empty($iApplicantID = (int) $this->request->getParam("id")))
            return $this->_status_response(400 /* Bad Request */ , "no applicant id given ");

        if (empty($oApplicant = self::$_ApplicantsTable->find()->where(['a_id' => (int) $iApplicantID])->first()))
            return $this->_status_response(404 /* Not Found */ , "applicant does not exist");

        if (empty($aBody = $this->request->getData()))
            return $this->_status_response(400 /* Bad Request */ , "you did not set any changed fields");

        // Check for Address changes
        $iAddrChangeFlag =
            (!empty(trim($aBody['addr_zip'] ?? "")) ? 1 : 0) +
            (!empty(trim($aBody['addr_city'] ?? "")) ? 2 : 0) +
            (!empty(trim($aBody['country_id'] ?? "")) ? 4 : 0)
        ;

        $bChangeCity = false;
        switch ($iAddrChangeFlag) {
            case 7:  // All required Adress-changing fields are set => change adress
                $oCity = $this->_getCity(
                    $aBody['addr_zip'],
                    $aBody['addr_city'],
                    (int) $aBody['country_id']
                );
                $bChangeCity = true;

            case 0: // No Adress-changing field is set => no change  required
                break;

            default: // only some Adress-changing fields are set => error
                return $this->_status_response(400 /* Bad Request */ , "to change the adress, you must provide addr_zip, addr_city and country_id together");
        }

        if ($bChangeCity)
            $oApplicant->ci_id = $oCity->ci_id;

        // Check for all other changes
        foreach (self::$aPUTDBColumnMap['applicants'] as $sAPIField => $sDBColumn) {
            // If no change requested for that field => check next field
            if (!isset($aBody[$sAPIField]))
                continue;

            $aSettings = self::$aPOSTFieldMap[$sAPIField];

            // if even one of the changed fields is invalid => end execution
            // (_validateRequestObjectField takes care of writing the response message)
            if (!$this->_validateRequestObjectField($aBody, $sAPIField, $aSettings))
                return;

            $oApplicant[$sDBColumn] = $aBody[$sAPIField];
        }

        $oExistingApplicant = self::$_ApplicantsTable->find()->select("a_id")->where([
            "a_firstname" => $oApplicant->a_firstname,
            "a_lastname" => $oApplicant->a_lastname,
            "ci_id" => $oApplicant->ci_id
        ])->first();

        if(!empty($oExistingApplicant) && $oExistingApplicant->a_id != $oApplicant->a_id)
            return $this->_status_response(409 /* Conflict */, "found different applicant with the same name in the same city");

        $oApplicant = self::$_ApplicantsTable->save($oApplicant);

        $this->_json_response($this->_getApplicantsArray([$iApplicantID])[0]);

    }

    /**
     * DELETE /api/applicants/:id - Route
     * @return void
     */
    public function deleteApplicant()
    {
        $this->_initClass();

        if (empty($iApplicantID = (int) $this->request->getParam("id")))
            return $this->_status_response(400 /* Bad Request */ , "no applicant id given ");

        $oApplicant = self::$_ApplicantsTable->find("all")
            ->where(['a_id = ' => $iApplicantID])
            ->first()
        ;

        if (empty($oApplicant))
            return $this->_status_response(200 /* OK */ , "already deleted");

        if (self::$_ApplicantsTable->delete($oApplicant)) {
            return $this->_status_response(200 /* OK */ , "deleted");
        } else {
            return $this->_status_response(500 /* Not Implemented */ , "failed to delete applicant");
        }

    }



    // BM: Private Helpers
    //===========================================================================

    private function _getCity(string $sZipCode, string $sCityName, int $iCountryId, string $sErrPrefix = ""): ?\App\Model\Entity\City
    {
        $sNormalizedZip = trim(strtolower($sZipCode));

        $oCity = self::$_CitiesTable->find()
            ->where([
                'ci_zip = ' => $sNormalizedZip,
                'co_id = ' => $iCountryId
            ])->first();

        // If that particular City was not registered yet, Register it
        if (empty($oCity)) {
            $oCity = self::$_CitiesTable->newEmptyEntity();
            $oCity->co_id = $iCountryId;
            $oCity->ci_zip = $sNormalizedZip;
            $oCity->ci_name = trim($sCityName);

            $oCity = self::$_CitiesTable->save($oCity);

            if (empty($oCity)) {
                $this->_status_response(500 /* Internal Error */ , "{$sErrPrefix} => Failed to register city");
                return null;
            }
        }

        return $oCity;
    }

    /**
     * get An array with response-Read Applicants data
     * @param array $aIdsOnly - limit response to applicats with the given IDs
     * @return array
     */
    private function _getApplicantsArray(array $aIdsOnly = []): array
    {
        // no idea, why this is not fetching data for the joined Cities Table :-(
        // $aApplicants = self::$_ApplicantsTable->find('all')
        //     ->contains("cities")
        //     ->execute()->fetchAll("assoc");
        //
        // I know this works at least.
        $sSQLWhere = empty($aIdsOnly)
            ? ""
            : " WHERE a_id IN(" . implode(",", array_map('intval', $aIdsOnly)) . ")"
        ;

        return array_map(
            fn($e) => $this->_convertDBArrToAPIArray("applicants", $e),
            self::$_ApplicantsTable->getConnection()
                ->execute(
                    "SELECT "
                    . implode(",", array_keys(self::$aGETDBColumnMap['applicants']))
                    . " FROM applicants LEFT JOIN cities USING(ci_id) "
                    . $sSQLWhere
                    . " ORDER BY a_id ASC "
                )
                ->fetchAll("assoc")
        );

    }

    private function _validateRequestObject(array $aRequestApplicant, string $sErrPrefix): bool
    {
        foreach (self::$aPOSTFieldMap as $sReqField => $aFieldSettings) {
            if (!$this->_validateRequestObjectField($aRequestApplicant, $sReqField, $aFieldSettings, $sErrPrefix))
                return false;
        }

        return true;
    }

    private function _validateRequestObjectField(array $aRequestApplicant, string $sReqField, array $aFieldSettings, string $sErrPrefix = ""): bool
    {

        if (empty(trim($aRequestApplicant[$sReqField] ?? ""))) {
            if (!empty($aFieldSettings['default'])) {
                $aApplicant[$sReqField] = $aFieldSettings['default'];

            } elseif (!empty($aFieldSettings['required'])) {
                $this->_status_response(
                    400 /* Bad Request */ ,
                    "{$sErrPrefix} => missing required field '{$sReqField}'"
                );
                return false;
            }
        }

        switch ($aFieldSettings['type'] ?? "string") {
            case "enum":
                if (
                    !in_array(
                        $aRequestApplicant[$sReqField],
                        $aFieldSettings['options'] ?? []
                    )
                ) {
                    $this->_status_response(
                        400 /* Bad Request */ ,
                        "{$sErrPrefix} => Field '{$sReqField}' has a none available value"
                    );
                    return false;
                }
                break;

            case "ref":
                /** @var \Cake\ORM\Table $oRefTable */
                $oRefTable = $aFieldSettings['reftab'];

                $oResult = $oRefTable->find(
                    "all",
                    conditions: ["`" . $aFieldSettings['refcol'] . "` = " => $aRequestApplicant[$sReqField]],
                    limit: 1
                )->execute()->fetch();

                if ($oResult === false) {
                    $this->_status_response(
                        400 /* Bad Request */ ,
                        "{$sErrPrefix} => Field '{$sReqField}' has a none available value"
                    );
                    return false;
                }

                break;
        }

        return true;
    }

    private function _convertDBArrToAPIArray(string $sTable, array $aDBArr): array
    {
        $aConfig = self::$aGETDBColumnMap[$sTable] ?? [];

        $aResponse = [];
        foreach ($aConfig as $sDBField => $sAPIField) {
            if (isset($aDBArr[$sDBField]))
                $aResponse[$sAPIField] = $aDBArr[$sDBField];
        }

        return $aResponse;
    }

    private function _json_response(mixed $aResponse): void
    {
        $this->viewBuilder()
            ->setLayout("ajax")
            ->setTemplate("json_output")
        ;
        $this->response = $this->response->withType("application/json");
        $this->set("aResponse", $aResponse);
    }

    private function _status_response($iStatus, $sResponse): void
    {
        $this->viewBuilder()
            ->setLayout("ajax")
            ->setTemplate("text_output")
        ;

        $this->response = $this->response
            ->withType("text/plain")
            ->withStatus($iStatus)
        ;

        $this->set("sResponse", $sResponse);

    }
}

