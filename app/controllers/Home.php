<?php

class Home extends Controller
{

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    public function index()
    {
        $db = DB::get_instance();
        dnd($db->findFirst('test', [
            'conditions' => ['test_name = ?'],
            'bind' => ['test'],
            'order' => 'test_name',
            'limit' => 2
        ]));
        $this->view->render('home/index');
    }
}