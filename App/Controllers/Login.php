<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

    /**
     * Show the login page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Log in a user
     *
     * @return void
     */
    public function createAction()
    {
        $account = User::authenticate($_POST['username'], $_POST['password']);
        
        if ($account) {
            
            Auth::login($_POST['username']);

            Flash::addMessage('Login successful');

            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage('Login unsuccessful, please try again', "warning");

            View::renderTemplate('login/new.html',[
                'username' => $_POST['username']
            ]);
        }
    }

    /**
     * Log out a user
     *
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout successful');

        $this->redirect('/');
    }

    public function searchAction()
    {
        if (isset($_GET['term'])) {
            # code...
            $return_arr = array();

            $employees = User::getEmployee($_GET['term']);

            foreach ($employees as $key => $value) {
                # code...
                $return_arr[] = $value['full_name'] ." - ". $value['pet_id'];
            }

            echo json_encode($return_arr);
        }
    }

    public function loginasAction()
    {
        if (isset($_POST['login_as_pet'])) {

            $account_info = explode(" - ",$_POST['loginas']);

            Auth::login($account_info[1]);

            Flash::addMessage('Login successful');

            $this->redirect(Auth::getReturnToPage());
        }
            
    }
}
