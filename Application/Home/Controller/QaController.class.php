<?php

namespace Home\Controller;
use Common\Controller\FrontbaseController;

class QaController extends FrontbaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  院校筛选 
     */
    public function index()
    {

        $this->display();
    }

}