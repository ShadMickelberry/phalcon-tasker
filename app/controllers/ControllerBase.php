<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    public function initialize()
    {
        $this->tag->setTitle('Tasker');

        //and some local javascript resources

        $this->assets
//            ->addCss('DataTables/media/css/jquery.dataTables.min.css')
//            ->addCss('DataTables/media/css/bootstrap/dataTables.bootstrap.css')
//            ->addCss('DataTables/extensions/Responsive/css/dataTables.responsive.css')
//            ->addJs('DataTables/media/js/jquery.dataTables.min.js')
//            ->addJs('DataTables/media/js/dataTables.bootstrap.js')
//            ->addJs('DataTables/extensions/Responsive/js/dataTables.responsive.js')
            ->addJs('js/jeditable.min.js');

    }
}