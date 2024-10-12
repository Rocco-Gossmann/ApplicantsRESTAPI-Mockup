<?php namespace App\Controller;

class ApplicantsController extends \App\Controller\AppController {

    public function viewClasses(): array {
        return [ \Cake\View\JsonView::class ];
    }

    public function getApplicants() {

        $this->set("applicants", $this->paginate());
        $this->viewBuilder()->setOption("serialize", "applicants");
        $this->viewBuilder()->setTemplate("unsupported");

    }

    public function initialize(): void {
        parent::initialize();

    }
}