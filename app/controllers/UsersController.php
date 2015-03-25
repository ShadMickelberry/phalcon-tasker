<?php

namespace App\Controllers;

use App\Library\Mail;
use App\Models\Users;

class UsersController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function createAction()
    {

    }

    public function registerAction()
    {
        if ($this->request->isPost()) {
           try {
               //Grab values from post and check email
               $user = new Users();
               $user->first_name = $this->request->getPost('first_name');
               $user->last_name = $this->request->getPost('last_name');
               $user->email = strtolower($this->request->getPost('email'));
               $user->verified = 0;

               //check if email address is registered
               $email_count = $user->checkForEmail($user->email);

               if ($email_count != 0) {
                   $error = 'Email Already Exists';
                   $this->view->setVars(array(
                           'error' => 1,
                           'error_msg' => $error
                       ));

               } else {
                    //fi successfully created user send email to verify
                   if ($user->create()) {
                       $html = \Phalcon\Tag::linkTo(array('tasker.dev/users/validate/'.$user->id , 'Validate Email'));
                       $mail = new Mail();
                       $result = $mail->sendMail('shad@shad.me', $user->first_name, 'Validate', $html, 'Validate text');
                        echo $this->flash->success('check you email');
                   } else {
                       die(print_r($user->getMessages()));
                   }
               }
           } catch (\Exception $e) {
                $error = $e->getMessage();
                die($error);
           }
        }
    }

    public function validateAction($user_id)
    {
        $new_obj = new Users();
        $new_user = $new_obj->findFirst($user_id);

        if ($new_user) {
            $new_user->verified = 1;
            $new_user->last_login = date('Y-m-d H:i:s');
            if ($new_user->update()) {

                $this->view->setVars(array('validated' => 1));
            } else {

                //can't validate
            }

        }

    }



}

