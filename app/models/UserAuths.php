<?php

namespace App\Models;

class UserAuths extends ModelBase
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
    public $authenticator_id;

    /**
     *
     * @var string
     */
    public $user_token;

    /**
     *
     * @var string
     */
    public $refresh_token;

    /**
     *
     * @var string
     */
    public $access_token;

    /**
     *
     * @var date time
     */
    public $expires;

    /**
     *
     * @var timestamp
     */
    public $date_created;




    public function initialize()
    {
        $this->belongsTo('user_id', 'App\Models\Users', 'id', array(
                'alias' => 'user'
            ));
        $this->belongsTo('authenticator_id', 'App\Models\Authenticators', 'id', array(
                'alias' => 'authenicator'
            ));
    }
    public function getSource()
    {
        return 'user_auths';
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'user_id' => 'user_id',
            'authenticator_id' => 'authenticator_id',
            'user_token' => 'user_token',
            'refresh_token' => 'refresh_token',
            'access_token' => 'access_token',
            'expires' => 'expires',
            'date_created' => 'date_created'
        );
    }

}
