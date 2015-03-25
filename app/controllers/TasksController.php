<?php

namespace App\Controllers;

use App\Models\Projects;
use App\Models\Tasks;

class TasksController extends ControllerBase
{

    public function initialize()
    {
        //$this->session->destroy();
        parent::initialize();
        $this->tag->appendTitle(' | Tasks');

        if (!$this->session->has('user_id')) {
            $this->dispatcher->forward(array(
                'controller' => 'userauths',
                'action'     => 'index'));
        }

        $this->assets
            ->addCss('DataTables/media/css/jquery.dataTables.min.css')
            ->addCss('DataTables/media/css/bootstrap/dataTables.bootstrap.css')
            ->addCss('DataTables/extensions/Responsive/css/dataTables.responsive.css')
            ->addJs('DataTables/media/js/jquery.dataTables.min.js')
            ->addJs('DataTables/media/js/dataTables.bootstrap.js')
            ->addJs('DataTables/extensions/Responsive/js/dataTables.responsive.js');
    }
    public function indexAction($project_id)
    {

        if (!$project_id) {

            $tasks = Tasks::find(array(
                    'conditions' => "date_completed IS NULL AND user_id = " . $this->session->get('user_id'),
                    'order'      => 'due_date ASC'
                ));

        } else {
            $tasks = Tasks::find(array(
                    'conditions' => "date_completed IS NULL AND project_id = :project: AND user_id = " .
                        $this->session->get('user_id'),
                    'bind'      => array('project' => $project_id),
                    'order'      => 'due_date ASC'
                ));
        }
        if (isset($tasks[0])) {
            $this->view->setVar('tasks', $tasks);
        }
    }

    public function createAction($type)
    {

        $this->tag->appendTitle(' | Create');
        if ($this->request->isPost()){
            if ($this->session->get('security_token') == $this->request->getPost('token')) {
                //clear token session value to prevent resubmission
                $this->session->set('security_token', '');
                //if submit type is task
                switch ($type){
                    case 'task';

                        $task = new Tasks();
                        $task->task = $this->request->getPost('task_name');
                        $task->user_id = $this->request->getPost('user_id');
                        $task->priority = $this->request->getPost('task_priority');

                        if ($this->request->getPost('task_name')) {
                            $task->project_id = $this->request->getPost('task_project');
                        } else {

                            $task->project_id = null;
                        }

                        $task->due_date = $this->request->getPost('task_due_date');
                        $task->due_time = $this->request->getPost('task_due_time');

                        if (!$task->create()) {

                            foreach ($task->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        } else {
                            $this->flash->success('Task Created');
                        }
                    break;

                    case 'project':
                        $project = new Projects();
                        $project->name = $this->request->getPost('project_name');
                        $project->due_date = $this->request->getPost('project_due_date');
                        $project->user_id = $this->request->getPost('user_id');

                        if (!$project->create()) {
                            foreach ($project->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        } else {
                            $this->flash->success('Project ' . $project->name . ' Created');
                        }
                    break;

                    default:
                        $this->flash->error('Unknown event type');
                }

            } else {
                $this->flash->error('Token does not match is jacked up');
            }
        }

        $projects = Projects::find(array(
                'conditions' => "date_completed IS NULL AND user_id = " . $this->session->get('user_id')
            ));
        if (isset($projects[0])) {
        $this->view->setVar('projects', $projects);
        }
    }

    public function closeAction()
    {
        if ($this->request->isPost()) {
            $task_id = $this->request->getPost('data_id');

            $task = Tasks::findFirst($task_id);
            $task->date_completed = date('Y-m-d H:i:s');
            if ($task->update()) {
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

    public function updateFieldAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) {
            $field = $this->request->getPost('data_field');
            $task_id = $this->request->getPost('data_id');
            $new_val = $this->request->getPost('value');

            $task = Tasks::findFirst($task_id);
            $task->{$field} = $new_val;
            $task->update();
                if ($field == 'due_date') {
                    echo date('D M jS', strtotime($task->{$field}));
                } elseif ($field == 'due_time'){
                    echo date('g:i A', strtotime($task->{$field}));
                } else {
                    echo $task->{$field};
                }


        } else {

            $this->dispatcher->forward(array(
                    'controller' => 'tasks',
                    'action'     => 'index'
                ));
        }
    }

    public function getDatesAction()
    {

        $day_options = array();
        for ($i = 0; $i <= 100; $i++) {
            $day_options[date("Y-m-d", strtotime("+$i day"))] = date('D M jS', strtotime("+$i day"));
        }
        echo json_encode($day_options);
        $this->view->disable();
    }

    public function getTimesAction()
    {

        $time_options = array();
        $time = strtotime(date('2015-01-01 07:00:00'));
        for ($i = 0; $i <= 101; $i++) {
            $minutes = $i*15*60;
            $this_time = $time+$minutes;
            $time_options[date("H:i:s", $this_time)] =date("g:i A", $this_time);
        }
        echo json_encode($time_options);
        $this->view->disable();
    }


}

