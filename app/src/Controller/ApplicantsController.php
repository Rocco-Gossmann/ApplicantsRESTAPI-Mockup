<?php

namespace App\Controller;

use Cake\Datasource\ConnectionManager;

class ApplicantsController extends \App\Controller\AppController
{
    /** @var array
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
    private static $aFieldMap = [
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

    /**
     * Automatically filled in throug $aFieldMap
     * @var array
     */
    private static $aDBColumnMap = ["applicants" => [
        'a_id' => 'id',
        'ci_name' => 'addr_city',
        'ci_zip' => 'addr_zip',
        'co_id' => 'country_id',
    ]];

    /** @var \App\Model\Table\ApplicantsTable */
    private static $_ApplicantsTable;

    /** @var \App\Model\Table\CitiesTable */
    private static $_CitiesTable;


    private static $bInitialized = false;
    private function _initClass()
    {
        if (self::$bInitialized)
            return;

        foreach (self::$aFieldMap as $sField => $aSettings) {
            if (isset($aSettings['table']))
                self::$aFieldMap[$sField]['table'] = $this->fetchTable($aSettings['table']);

            if (isset($aSettings['reftab']))
                self::$aFieldMap[$sField]['reftab'] = $this->fetchTable($aSettings['reftab']);

            self::$aDBColumnMap[$aSettings['table']] = self::$aDBColumnMap[$aSettings['table']] ?? [];
            self::$aDBColumnMap[$aSettings['table']][$aSettings['column']] = $sField;
        }

        self::$_ApplicantsTable = $this->fetchTable("applicants");
        self::$_CitiesTable = $this->fetchTable("cities");

        self::$bInitialized = true;
    }

    public function viewClasses(): array
    {
        return [\Cake\View\JsonView::class];
    }

    public function getApplicants()
    {
        $this->_initClass();
        $this->_json_response($this->_getApplicantsArray());
    }

    public function postApplicants()
    {
        $this->_initClass();

        $aList = $this->request->getData();

        // Validate Request-Data
        if (empty($aList))
            return $this->_status_response(400 /* Bad Request */ , "Request enthielt keine Daten");

        if (!is_array($aList))
            return $this->_status_response(400 /* Bad Request */ , "Request-Body ist kein Array. Bitte übergeben sie ein Array mit anzulegenden Einträgen.");

        if (count(array_filter(array_keys($aList), fn($e) => !is_numeric($e))))
            return $this->_status_response(400 /* Bad Request */ , "Request-Body muss eine Liste mit Elementen sein, nicht das Element selbst");


        /** @var \Cake\Datasource\ConnectionManager $oDB */
        $oDB = ConnectionManager::get("default");
        $oDB->execute("START TRANSACTION")->execute();

        /** @var int[] */
        $aRegisteredApplicants = [];

        try {
            foreach ($aList as $iIDX => $aRequestApplicant) {
                $sErrPrefix = "Applicant {$iIDX}";
                // Validate the Insert Object
                if (!$this->_validateRequestObject($aRequestApplicant, $sErrPrefix))
                    throw new \Exception("invalid request object");

                // Process City
                $sNormalizedZip = trim(strtolower($aRequestApplicant['addr_zip']));

                $oCity = self::$_CitiesTable->find()
                    ->where([
                        'ci_zip = ' => $sNormalizedZip,
                        'co_id = ' => $aRequestApplicant['country_id']
                    ])->first();

                // If that particular City was not registered yet, Register it
                if (empty($oCity)) {
                    $oCity = self::$_CitiesTable->newEmptyEntity();
                    $oCity->co_id = (int) $aRequestApplicant['country_id'];
                    $oCity->ci_zip = $sNormalizedZip;
                    $oCity->ci_name = trim($aRequestApplicant['addr_city']);

                    $oCity = self::$_CitiesTable->save($oCity);
                    if (empty($oCity)) {
                        $this->_status_response(500 /* Internal Error */ , "{$sErrPrefix} => Failed to register city");
                        throw new \Exception("failed to created city in db");
                    }
                }

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

                $aRegisteredApplicants[] = $oApplicant->a_id;
            }

            $oDB->execute("COMMIT")->execute();
        } catch (\Exception $ex) {
            $oDB->execute("ROLLBACK")->execute();
            return;
        }

        xdebug_break();
        $this->_json_response( $this->_getApplicantsArray($aRegisteredApplicants));

    }


    // BM: Private Helpers
    //===========================================================================

    private function _getApplicantsArray(array $aIdsOnly = []) : array {

        // no idea, why this is not fetching data for the joined Cities Table :-(
        // $aApplicants = self::$_ApplicantsTable->find('all')
        //     ->contains("cities")
        //     ->execute()->fetchAll("assoc");
        //
        // I know this works at least.
        $sSQLWhere = empty($aIdsOnly) 
            ? "" 
            : " WHERE a_id IN(".implode(",", array_map('intval', $aIdsOnly)).")" 
        ;
        return array_map(
            fn($e) => $this->_convertDBArrToAPIArray("applicants", $e), 
            ConnectionManager::get("default")
                ->execute("SELECT " 
                    . implode(",",array_keys(self::$aDBColumnMap['applicants']))
                    . " FROM applicants LEFT JOIN cities USING(ci_id) " 
                    . $sSQLWhere
                    . " ORDER BY a_id ASC "
                )
                ->fetchAll("assoc")
        );

    }

    private function _validateRequestObject(array $aRequestApplicant, string $sErrPrefix): bool
    {
        foreach (self::$aFieldMap as $sReqField => $aFieldSettings) {

            if (empty(trim($aRequestApplicant[$sReqField] ?? ""))) {
                if (!empty($aFieldSettings['default'])) {
                    $aApplicant[$sReqField] = $aFieldSettings['default'];

                } elseif (!empty($aFieldSettings['required'])) {
                    return $this->_status_response(
                        400 /* Bad Request */ ,
                        "{$sErrPrefix} => missing required field '{$sReqField}'"
                    );
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
                        return $this->_status_response(
                            400 /* Bad Request */ ,
                            "{$sErrPrefix} => Field '{$sReqField}' has a none available value"
                        );
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
                        return $this->_status_response(
                            400 /* Bad Request */ ,
                            "{$sErrPrefix} => Field '{$sReqField}' has a none available value"
                        );
                    }

                    break;
            }

        }

        return true;
    }

    private function _convertDBArrToAPIArray(string $sTable, array $aDBArr): array {

        $aConfig = self::$aDBColumnMap[$sTable]??[];

        $aResponse = [];
        foreach($aConfig as $sDBField=>$sAPIField) {
            if(isset($aDBArr[$sDBField]))
                $aResponse[$sAPIField] = $aDBArr[$sDBField];
        }

        return $aResponse;
    }

    private function _json_response(mixed $aResponse)
    {
        $this->viewBuilder()
            ->setLayout("ajax")
            ->setTemplate("json_output")
        ;
        $this->response = $this->response->withType("application/json");
        $this->set("aResponse", $aResponse);
    }
 
    private function _status_response($iStatus, $sResponse): bool
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

        return false;
    }
}

