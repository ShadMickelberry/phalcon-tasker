<?php

namespace App\Models;

class Authenticators extends ModelBase
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
    public $name;

    /**
     *
     * @var string
     */
    public $client_key;

    /**
     *
     * @var string
     */
    public $secret_key;

    /**
     *
     * @var string
     */
    public $redirect_url;

    public $private_key;

    public function initialize()
    {
        $this->hasMany("id", 'App\Modles\UserAuths', "authenticator_id");
    }

    public function getSource()
    {
        return 'authenticators';
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'name' => 'name', 
            'client_key' => 'client_key', 
            'secret_key' => 'secret_key', 
            'redirect_url' => 'redirect_url',
            'private_key' => 'private_key'
        );
    }

}
