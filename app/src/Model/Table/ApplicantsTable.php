<?php namespace App\Model\Table;

class ApplicantsTable extends \Cake\ORM\Table {
    function initialize(array $config): void {
        parent::initialize($config);
        $this->addBehavior("Timestamp");
    }
}