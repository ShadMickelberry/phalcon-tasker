<?php

namespace App\Models;

class Projects extends ModelBase
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
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $due_date;

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
        $this->hasMany('id', 'App\Models\Tasks', 'project_id', array(
                'alias' => 'Tasks'
            ));

    }

    public function beforeValidationOnCreate()
    {
        $this->date_created = date('Y-m-d');
        $this->date_completed = null;
    }

    public function getSource()
    {
        return 'projects';
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'name' => 'name', 
            'due_date' => 'due_date', 
            'date_completed' => 'date_completed'
        );
    }

}
