<?php namespace App\Model\Table;

class CountriesTable extends \Cake\ORM\Table {
    function initialize(array $config): void {
        parent::initialize($config);
        $this
            ->setTable("countries")
            ->setPrimaryKey("co_id")
            ->setEntityClass('App\Model\Entity\Country')
        ;
    }
}