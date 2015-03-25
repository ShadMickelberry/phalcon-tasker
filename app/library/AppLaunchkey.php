<?php
/**
 * Created by PhpStorm.
 * User: shad
 * Date: 2/26/15
 * Time: 3:14 PM
 */

namespace App\Library;

use App\Models\UserAuths;
use Phalcon\Mvc\User\Component;

class AppLaunchkey extends Component {

    public function launchKeyLogin($authenticator)
    {
        //use launchkey oauth to login

        $launchkey = new \LaunchKey_OAuth($authenticator->client_key, $authenticator->secret_key, 'http://tasks.shad.me/userauths/');

        //check for ?&code= in request
        if ($this->request->get('code')) {

            $code = $this->request->get('code');
            $callback = $launchkey->callback($code);

            //query the user_auth object from the user token in callback
            $user_auth = UserAuths::findFirst(
                array(
                    "conditions" => "user_token = :token:",
                    'bind' => array('token' => $callback['user']),
                )
            );

            //flash error if we can't find the token in db
            if (!$user_auth) {

                //if can't find the user token move to register
                //@todo (forward to register
                $response = array('success' => 0, 'msg' => 'Can not find that token register it');
                return $response;
            }

            //set expiration to + 1 hour
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $user_auth->access_token = $callback['access_token'];
            $user_auth->refresh_token = $callback['refresh_token'];
            $user_auth->expires = $expires;

            //update db
            if ($user_auth->update()) {

                //get user object
                $user = $user_auth->user;

                //set session variables
                $this->session->set('user_first_name', $user->first_name);
                $this->session->set('user_id', $user->id);
                $this->session->set('user_token', $user_auth->user_token);
                $this->session->set('access_token', $user_auth->access_token);
                $this->session->set('token_expiration', strtotime($expires));

                $response = array('success' => 1, 'msg' => 'Logged In');
                return $response;
            }

        } elseif ($this->request->get('error')) {

            // review error response in launchkey doc
            $response = array('success' => 0, 'msg' => 'Something went wrong with the login');
            return $response;

        } else {

            $response = array('success' => 0, 'msg' => 'Could not get response');
            return $response;

        }


    }

    public function launchKeyRefresh($access_token, $authenticator)
    {
        $launchkey = new \LaunchKey_OAuth($authenticator->client_key, $authenticator->secret_key,
        'http://tasks.shad.me/userauths/');

        $auth = UserAuths::findFirstByAccessToken($access_token);

       // $refresh = $launchkey->refresh($auth->refresh_token);

        if(isset($refresh['access_token']) && isset($refresh['refresh_token'])) {

            $auth->access_token = $refresh['access_token'];
            $auth->refresh_token = $refresh['refresh_token'];
            $expires = time() + 14400;
            $auth->expires = date('Y-m-d H:i:s', $expires);
            $auth->update();

            $user = $auth->user;

            $this->session->set('user_first_name', $user->first_name);
            $this->session->set('access_token', $refresh['access_token']);
            $this->session->set('refresh_token', $refresh['refresh_token']);
            $this->session->set('expres', $refresh['expires_in']);

            $response = array('success' => 1, 'msg' => 'Updated Token');
            return $response;

        } else {
            $response = array('success' => 0, 'msg' => 'Could not update token');
            return $response;
        }

    }

} 