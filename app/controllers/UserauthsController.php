<?php

namespace App\Controllers;

use App\Library\AppLaunchkey;
use App\Models\Authenticators;
use App\Models\UserAuths;
use App\Models\Users;

class UserAuthsController extends ControllerBase

{

    public function indexAction()
    {
        $authenticator1 = Authenticators::findFirstByName('launchkey');

        $private_key = file_get_contents('/../'.$authenticator1->private_key);

        $launchkey = new \LaunchKey_OAuth($authenticator1->client_key, $authenticator1->secret_key,
            'http://tasks.shad.me/userauths/login/launchkey');


        $this->flash->notice($launchkey->login("blue", "large"));

    }

    public function loginAction($authenticator_name)
    {

        $authenticator = Authenticators::findFirstByName($authenticator_name);

        //set up procedure for different authenticators
        $validator = $authenticator->name;
        switch ($validator) {
            case 'launchkey':
                $lk = new AppLaunchkey();
                $response = $lk->launchKeyLogin($authenticator);
                    if ($response['success']) {
                    $this->flash->success($response['msg']);
                    } else {
                        $this->flash->error($response['msg']);
                    }
                break;

        }

    }


    public function selectAction()
    {

    }

    public function registerAction($authenticator_name)
    {
        //check for user_email session to register
        if (!$this->session->has('user_email') ) {
            $this->dispatcher->forward(array(
                    'controller' => 'userauths',
                    'action'     => 'set_email'
                ));
            return false;
        }
        $user = Users::findFirstByEmail($this->session->get('user_email'));

            if (!$user) {
                $this->flash->error('Could not find that email. Perhaps you need to register');
                return false;
            }

        $this->session->set('user_id', $user->id);

        $authenticator = Authenticators::findFirstByName($authenticator_name);

        if (!$authenticator) {
            $this->flash->error('Could not find that authenticator');
            return false;
        }

        $oauth = new UserAuths();
        $oauth->authenticator_id = $authenticator->id;
        $oauth->user_id = $user->id;
        $oauth->create();

        $this->session->set('oauth_id', $oauth->id);

        $private_key = file_get_contents('/../'.$authenticator->private_key);

        $launchkey = new \LaunchKey_OAuth($authenticator->client_key, $authenticator->secret_key,
            'http://tasks.shad.me/userauths/validate/'.$authenticator->name .'/'. $this->session->get
                ('oauth_id'));


        $this->flash->success($launchkey->login('dark', 'long','long', 'en'));



    }
    public function set_emailAction()
    {
        if ($this->request->isPost()) {
            $email = strtolower($this->request->getPost('email'));
            $this->session->set('user_email', $email);

            $this->dispatcher->forward(array(
                    'controller' => 'userauths',
                    'action'     => 'register'
                ));
        }
    }

    public function validateAction($authenticator_name, $oauth_session)
    {
        $authenticator = Authenticators::findFirstByName($authenticator_name);
        $user_auth = UserAuths::findFirst($oauth_session);

        $launchkey = new \LaunchKey_OAuth($authenticator->client_key, $authenticator->secret_key, 'http://tasks.shad.me/userauths/validate/'. $this->session->get('oauth_id'));

        if ($this->request->get('code')) {
            $code = $this->request->get('code');
            $callback = $launchkey->callback($code);

            $user_auth->access_token = $callback['access_token'];
            $user_auth->user_token = $callback['user'];
            $user_auth->refresh_token = $callback['refresh_token'];
            $user_auth->update();

            /*
            Array ( [access_token] => 4w9vZaRaq0uQfUBhUTZZ8WXy0FkBpYwkQej1yNQ4jdQG8hE0JLfyBxSh8R0VSp9w
            [token_type] => Bearer [expires_in] => 3600
            [user] => iRqjVtVf7FWC0RuA8gsiJCbqPlCK5Za6Noen0PsPsb0
            [refresh_token] => K1zUtN1lht1ud9YVn47Wm5B4tZR1nhuTTWozdnkKQTWiiqhoKHNsOeaKHrGJuOgh )
            https://github.com/LaunchKey/launchkey-oauth-php/blob/master/example.php
1
            */
        }
    }

    public function checkSessionAction($access_token)
    {
//
//        $auth = UserAuths::findFirstByAccessToken($access_token);
//        //check if the token has expired
//        if (($this->session->get('expires') > time()) || (empty($auth->refresh_token))) {
//
//            $auth = UserAuths::findFirstByAccessToken($access_token);
//
//            if (!$auth) {
//                $response = array('success' => 0, 'msg' => 'Token Revoked login again');
//                return $response;
//
//            } else {
//                //@todo try to refresh if okay reply success and update session/db
//                //@todo if not relogin
//                $authenticator = $auth->authenticator;
//
//                switch ($authenticator->name) {
//                    case 'launchkey':
//                        $lk = new AppLaunchkey();
//                        $request = $lk->launchKeyRefresh($access_token, $authenticator);
//                        if (!$request) {
//                            $this->session->destroy();
//                            $this->response->redirect('userauths/index');
//                        } else {
//                            $this->flash->success('Updated Token');
//                        }
//                        break;
//                }
//
//            }
//        } else {
//            $response = array('success' => 1, 'msg' => 'token okay');
//            return $response;
//        }
//        $this->view->disable();
    }


}

/*
 *    public function launchKeyLogin($authenticator)
    {
        $launchkey = new \LaunchKey_OAuth($authenticator->client_key, $authenticator->secret_key, 'http://tasker.dev/userauths/');

        //check for error callback

        if ($this->request->get('code')) {
            $code = $this->request->get('code');
            $callback = $launchkey->callback($code);
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
                $this->flash->error(
                    'Can not find that token register it' . '<hr/>' .
                    $callback['user']
                );
            }
            //set expiration to + 1 hour
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $user_auth->access_token = $callback['access_token'];
            $user_auth->refresh_token = $callback['refresh_token'];
            $user_auth->expires = $expires;

            if ($user_auth->update()) {
                $this->session->set('user_token', $user_auth->user_token);
                $this->session->set('access', $user_auth->access_token);
                $this->session->set('token_expiration', strtotime($expires));
                $this->flash->success('Logged in');
            }

        } elseif ($this->request->get('error')) {

            //@todo review error response in launchkey doc
            $this->flash->error('Something went wrong with login');

        } else {

            $this->flash->error('Could not get response');

        }


    }



 */

