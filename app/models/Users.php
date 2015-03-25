<?php

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends ModelBase
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $date_created;

    /**
     *
     * @var string
     */
    public $last_login;

    /**
     *
     * @var string
     */
    public $first_name;

    /**
     *
     * @var string
     */
    public $last_name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var integer
     */
    public $verified;

    public function initialize()
    {
        $this->hasMany('id', 'App\Models\UserAuths', "user_id", array(
                'alias' => 'UserAuths'
            ));
        $this->hasMany('id', 'App\Models\Tasks', 'user_id', array(
                'alias' => 'Tasks'
            ));
        $this->hasMany('id', 'App\Models\Projects', 'user_id', array(
                'alias' => 'Projects'
            ));
    }

    public function beforeValidationOnCreate()
    {
        $this->date_created = date('Y-m-d');
    }


    public function checkForEmail($email)
    {
        ///all emails are stored in lower case
        $email = strtolower($email);

        $count = Users::count(array(
               'conditions' => 'email = :email:',
                'bind' => array('email' => $email)
            ));

        return $count;

    }
    /**
     * Validations and business logic
     */
    public function validation()
    {

        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    public function getSource()
    {
        return 'users';
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'date_created' => 'date_created', 
            'last_login' => 'last_login', 
            'first_name' => 'first_name', 
            'last_name' => 'last_name', 
            'email' => 'email', 
            'verified' => 'verified'
        );
    }

}
