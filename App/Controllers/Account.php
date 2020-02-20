<?php

namespace App\Controllers;

use \App\Models\User;
use \App\Flash;

/**
 * Account controller
 *
 * PHP version 7.0
 */
class Account extends \Core\Controller
{

    /**
     * Validate if email is available (AJAX) for a new signup.
     *
     * @return void
     */
    public function validateEmailAction()
    {
        //$is_valid = !User::emailExists($_GET['email']);

        header('Content-Type: application/json');
        //echo json_encode($is_valid);
    }

    /**
     *  Search Employee for Autocomplete
     * 
     */
    public function getName()
    {
        if (isset($_GET['term'])) {

            $return_arr = array();

            $resquest = new User($_GET);
            //echo json_encode($resquest->getEmployeeList());

            $employees = $resquest->getEmployeeList();
            foreach ($employees as $key => $value) {
                # code...
                $return_arr[] = $value['full_name'] . " - " . $value['pet_id'];
            }

            echo json_encode($return_arr);
        }
    }

    /**
     *  Get data of User Role
     * 
     */
    public function getroledata()
    {
        $resquest = new User($_GET);
        echo json_encode($resquest->getroledata());
    }

    /**
     *  Get page number data of User Role
     * 
     */
    public function getrolepage()
    {
        $resquest = new User($_GET);
        echo json_encode($resquest->getroledatapage());
    }

    /**
     * Add Role on selected Account
     * 
     */
    public function addAccountRole()
    {
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        if (strpos($_POST['emp_name'], " - ") !== false) {
            $pet_id = explode(" - ", $_POST['emp_name']);

            $_POST['emp_name'] = $pet_id[1];

            //echo $pet_id[1];
        }

        $addEmployeeRole = new User($_POST);
        //echo json_encode($resquest->getEmployeeList());
        //echo $addEmployeeRole->searchEmployeeRole();
        //echo $addEmployeeRole->addEmployeeRole();

        if ($addEmployeeRole->addEmployeeRole()) {
            Flash::addMessage('Role to ' . $_POST['emp_name'] . ' successfully added');
        } else {
            Flash::addMessage('Role to ' . $_POST['emp_name'] . ' already registered', Flash::INFO);
        }

        $this->redirect('/manages/account-role');
    }

}
