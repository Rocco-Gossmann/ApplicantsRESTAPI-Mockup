<?php
namespace App\Model\Table;

class ApplicantsTable extends \Cake\ORM\Table
{
    function initialize(array $config): void
    {
        parent::initialize($config);
        $this
            ->setTable("applicants")
            ->addBehavior("Timestamp")
            ->setPrimaryKey("a_id")
            ->setEntityClass('App\Model\Entity\Applicant')
        ;
        $this->hasOne("cities")
            ->setForeignKey("ci_id")
        ;
    }
}