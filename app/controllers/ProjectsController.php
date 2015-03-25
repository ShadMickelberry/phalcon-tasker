<?php

namespace App\Controllers;

use App\Models\Projects;

class ProjectsController extends ControllerBase
{

    public function indexAction()
    {
        //$this->session->destroy();
        parent::initialize();
        $this->tag->appendTitle(' | Projects');

        if (!$this->session->has('user_id')) {
            $this->dispatcher->forward(array(
                    'controller' => 'userauths',
                    'action'     => 'index'));
        }

        $projects = Projects::find(array(
                'conditions' => "date_completed IS NULL AND user_id = " . $this->session->get('user_id'),
            ));
        if (isset($projects[0])) {
            $this->view->setVar('projects', $projects);
        }
    }

    public function createAction()
    {
        $this->dispatcher->forward(array(
                'controller' => 'tasks',
                'action'     => 'create'
            ));
    }

    public function closeAction()
    {
        if ($this->request->isPost()) {
            $project_id = $this->request->getPost('data_id');

            $project = Projects::findFirst($project_id);
            $project->date_completed = date('Y-m-d H:i:s');
                foreach ($project->getTasks() as $task) {
                    $task->date_completed = date('Y-m-d H:i:s');
                    $task->update();
                }
            if ($project->update()) {
                $response = array('success' => 'true');
                echo json_encode($response);
            }
            $this->view->disable();
        } else {

            $this->dispatcher->forward(array(
                    'controller' => 'tasks',
                    'action'     => 'index'
                ));
        }
    }

}

