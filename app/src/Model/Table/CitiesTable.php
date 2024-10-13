<?php namespace App\Model\Table;
class CitiesTable extends \Cake\ORM\Table {
    function initialize(array $config): void {
        parent::initialize($config);
        $this
            ->setTable("cities")
            ->addBehavior("Timestamp")
            ->setPrimaryKey("ci_id")
            ->setEntityClass('App\Model\Entity\City')
            ->belongsToMany("applicants")
        ;
    }
}