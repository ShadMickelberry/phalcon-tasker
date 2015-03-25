<?php

namespace App\Models;

class Tasks extends ModelBase
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $project_id;

    /**
     *
     * @var string
     */
    public $task;

    /**
     *
     * @var string
     */
    public $priority;

    /**
     *
     * @var string
     */
    public $date_created;

    /**
     *
     * @var string
     */
    public $due_date;

    /**
     *
     * @var string
     */
    public $due_time;

    /**
     *
     * @var string
     */
    public $date_completed;

    public function initialize()
    {
        $this->belongsTo('user_id', 'App\Models\Users', 'id', array(
                'alias' => 'user'
            ));
        $this->belongsTo('project_id', 'App\Models\Projects', 'id', array(
                'alias' => 'project'
            ));
    }

    public function beforeValidationOnCreate()
    {
        $this->date_created = date('Y-m-d H:i:s');
        $this->date_completed = null;
    }

    public function getSource()
    {
        return 'tasks';
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'project_id' => 'project_id', 
            'task' => 'task', 
            'priority' => 'priority', 
            'date_created' => 'date_created', 
            'due_date' => 'due_date', 
            'due_time' => 'due_time', 
            'date_completed' => 'date_completed'
        );
    }

}
