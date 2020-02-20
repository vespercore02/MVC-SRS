<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Manage;
use \App\Models\Request;
use \App\Auth;
use \App\Flash;

/**
 * Manage Controller
 * 
 * PHP version 7.0
 */
class Manages extends Authenticated
{
    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        $user = Auth::getUser();

        if (strpos($user['pet_id'], '-admin') !== false) {

            if (isset($this->route_params['id'])) {

                $this->id = $this->route_params['id'];
            } else {

                $this->id = 1;
            }

            if ($this->route_params['action'] == "account") {

                $this->action = "User Account";

                $account_data = Manage::viewAccountData($this->id);
                $account_total = Manage::viewAccountTotal();
            } elseif ($this->route_params['action'] == "Email") {

                $this->action = "Email";

                $account_data = Manage::viewEmailAccountData($this->id);
                $account_total = Manage::viewEmailAccountTotal();
            } elseif ($this->route_params['action'] == "CMMS") {

                $this->action = "CMMS";

                $account_data = Manage::viewCmmsAccountData($this->id);
                $account_total = Manage::viewCmmsAccountTotal();
            } elseif ($this->route_params['action'] == "Seine") {

                $this->action = "Seine";

                $account_data = Manage::viewSeineAccountData($this->id);
                $account_total = Manage::viewSeineAccountTotal();
            }

            $account_list   = new User();
            $emp_list       = $account_list->getEmpList();
            $email_list     = $account_list->getEmailList();
            $cmms_list      = $account_list->getCmmsList();
            $seine2_list    = $account_list->getSeine2List();

            //echo count($email_list);

            View::renderTemplate('manage/account.html', [
                'account_data'  => $account_data,
                'account_total' => $account_total,
                'current_page'  => $this->id,
                'status'        => $this->action,
                'emp_list'      => $emp_list,
                'email_list'    => $email_list,
                'cmms_list'     => $cmms_list,
                'seine2_list'   => $seine2_list
            ]);

        } else {

            Flash::addMessage('Your not allowed to access the page.', Flash::WARNING);

            $this->redirect('/');
        }
    }

    /**
     * Show the Manage page
     *
     * @return void
     */
    public function manageAction()
    {

        View::renderTemplate('/manage/index.html');
    }

    /**
     * Show the view Account info page
     *
     * @return void
     */
    public function accountAction()
    {
        
    }

    public function accountrequestAction()
    {
        echo $this->route_params['id'] . "<br>";

        $ar_info = Manage::viewAccountRequest($this->route_params['id']);

        echo "<pre>";
        print_r($ar_info);
        echo "</pre>";


        $rqst_info = Request::requestDetails($this->route_params['id']);

        echo "<pre>";
        print_r($rqst_info);
        echo "</pre>";

        if ($rqst_info['sr_site'] == "HO") {

            $branch = "MNL";
        } elseif ($rqst_info['sr_site'] == "BO") {
            $branch = "ILO";
        }


        foreach ($ar_info as $ar_details) {
            # code...
            $request_status = "";
            echo $ar_details['pet_id'] . "<br>";

            Flash::addMessage('I.D number ' . $ar_details['pet_id'] . '', 'info');

            if ($ar_details['request'] == "Add") {

                if (strpos($ar_details['system_request'], 'Windows') !== false) {

                    $checkWindows = User::checkWindows($ar_details['pet_id']);

                    if ($checkWindows == 0) {
                        $addWindows = User::addWindows($ar_details['pet_id'], $ar_details['last_name'], $ar_details['first_name'], $ar_details['middle_initial'], $ar_details['department'], $branch);

                        if ($addWindows == 1) {
                            $request_status .= "Windows Account Created,";

                            Flash::addMessage('Windows Account Created');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "Windows Account already exist,";

                        Flash::addMessage('Windows Account already exist', 'warning');

                        //$this->redirect('/requests/view/'.$this->route_params['id']);
                    }
                }

                if (strpos($ar_details['system_request'], 'Email') !== false) {

                    $checkEmail = User::checkEmail($ar_details['pet_id']);

                    if ($checkEmail == 0) {
                        $addEmail = User::addEmail($ar_details['pet_id'], $ar_details['last_name'], $ar_details['first_name'], $ar_details['middle_initial'], $ar_details['department']);

                        if ($addEmail == 1) {
                            $request_status .= "Email address Created,";

                            Flash::addMessage('Email address Created ');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "Email Account already exist,";

                        Flash::addMessage('Email address already exist', 'warning');
                    }
                }

                if (strpos($ar_details['system_request'], 'Seine2') !== false) {

                    $checkSeine = User::checkSeine($ar_details['pet_id']);

                    if ($checkSeine == 0) {

                        $addSeine = User::addSeine($ar_details['pet_id'], $ar_details['last_name'], $ar_details['first_name'], $ar_details['middle_initial'], $ar_details['department']);

                        if ($addSeine == 1) {
                            $request_status .= "Seine2 account Created,";

                            Flash::addMessage('Seine2 account Created ');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "Seine2 Account already exist,";

                        Flash::addMessage('Seine2 Account already exist', 'warning');
                    }
                }

                if (strpos($ar_details['system_request'], 'CMMS') !== false) {

                    $checkCmms = User::checkCmms($ar_details['pet_id']);


                    if ($checkCmms == 0) {

                        $addCmms = User::addCmms($ar_details['pet_id'], $ar_details['last_name'], $ar_details['first_name'], $ar_details['middle_initial'], $ar_details['department']);

                        if ($addCmms == 1) {
                            $request_status .= "CMMS account Created,";

                            Flash::addMessage('CMMS account Created ');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "CMMS Account already exist,";

                        Flash::addMessage('CMMS Account already exist', 'warning');
                    }
                }
            }

            if ($ar_details['request'] == "Edit") {
                if (strpos($ar_details['system_request'], 'Windows') !== false) {
                    echo "Edit Windows ";
                }

                if (strpos($ar_details['system_request'], 'Email') !== false) {
                    echo "Edit Email ";
                }

                if (strpos($ar_details['system_request'], 'Seine') !== false) {
                    echo "Edit Seine ";
                }

                if (strpos($ar_details['system_request'], 'CMMS') !== false) {
                    echo "Edit CMMS ";
                }
            }

            if ($ar_details['request'] == "Delete") {
                if (strpos($ar_details['system_request'], 'Windows') !== false) {
                    $checkWindows = User::checkWindows($ar_details['pet_id']);

                    if ($checkWindows == 1) {
                        $deleteWindows = User::deleteWindows($ar_details['pet_id']);

                        if ($deleteWindows == 1) {
                            $request_status .= "Windows Account Deleted,";

                            Flash::addMessage('Windows Account Deleted');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "Windows Account already deleted,";

                        Flash::addMessage('Windows Account already deleted', 'info');
                    }
                }

                if (strpos($ar_details['system_request'], 'Email') !== false) {
                    $checkEmail = User::checkEmail($ar_details['pet_id']);

                    if ($checkEmail == 1) {
                        $deleteEmail = User::deleteEmail($ar_details['pet_id']);

                        if ($deleteEmail == 1) {
                            $request_status .= "Email address Deleted,";

                            Flash::addMessage('Email address Deleted');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "Email Account already deleted,";

                        Flash::addMessage('Email address already deleted', 'info');
                    }
                }

                if (strpos($ar_details['system_request'], 'Seine') !== false) {
                    $checkSeine = User::checkSeine($ar_details['pet_id']);

                    if ($checkEmail == 1) {
                        $deleteSeine = User::deleteSeine($ar_details['pet_id']);

                        if ($deleteSeine == 1) {
                            $request_status .= "Seine2 Account Deleted,";

                            Flash::addMessage('Seine2 Account Deleted');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "Seine2 Account already deleted,";

                        Flash::addMessage('Seine2 Account already deleted', 'info');
                    }
                }

                if (strpos($ar_details['system_request'], 'CMMS') !== false) {
                    $checkCmms = User::checkCmms($ar_details['pet_id']);

                    if ($checkEmail == 1) {
                        $deleteCmms = User::deleteCmms($ar_details['pet_id']);

                        if ($deleteCmms == 1) {
                            $request_status .= "CMMS address Deleted,";

                            Flash::addMessage('CMMS Account Deleted');
                        } else {
                            $request_status .= "Problem occured during creation of account,";

                            Flash::addMessage('Problem occured during creation of account', 'warning');
                        }
                    } else {
                        $request_status .= "CMMS Account already deleted,";

                        Flash::addMessage('CMMS Account already deleted', 'info');
                    }
                }
            }
            $_POST['id'] = $ar_details['id'];
            $_POST['rqst_status'] = $request_status;

            $account_details = new Request($_POST);

            $account_details->updateAccountRequest();

            echo $request_status;
            echo "<br>";
        }


        //echo count($ar_info);


        $manhour = Request::computeManHour($rqst_info['sr_date_request'], $rqst_info['sr_done_date']);

        //echo "<pre>";
        //print_r($manhour);
        //echo "</pre>";

        $this->redirect('/requests/view/' . $this->route_params['id']);
    }

    /**
     * Show the view Windows Account page
     *
     * @return void
     */
    public function windowsaccountAction()
    {
        $department = User::getDepartmentList();
        $role       = User::getRoleList();

        View::renderTemplate('manage/windows.html');
    }

    /**
     * Show the view Email Account page
     *
     * @return void
     */
    public function emailAction()
    {
        
    }


    /**
     * Show the view CMMS Account page
     *
     * @return void
     */
    public function cmmsAction()
    {
        
    }

    /**
     * Show the view Seine2 Account page
     *
     * @return void
     */
    public function seineAction()
    {
        
    }

    /**
     * seine2Data
     *
     * @return void
     */
    public function seineData()
    {
        $seine2Data = new User($_GET);
        echo json_encode($seine2Data->getSeine2Data());
    }

    public function seinePage()
    {
        $seine2Page = new User($_GET);
        echo json_encode($seine2Page->getSeine2List());
    }


    /**
     * Show the view Account Role page
     *
     * @return void
     */
    public function accountroleAction()
    {
        $department = User::getDepartmentList();
        $role       = User::getRoleList();

        View::renderTemplate('manage/role.html', [
            'department_list' => $department,
            'role_list'       => $role
        ]);
    }

    /**
     * Show the view Account Role page
     *
     * @return void
     */
    public function categoryAction()
    {
        $request_list = Manage::loadRequestName();
        $problem_list = Manage::loadProblemName();
        $fileserver_list = Manage::loadFileServerName();
        $request_config     = Manage::loadRequestConfigName();

        View::renderTemplate('manage/category.html', [
            'request' => $request_list,
            'problem' => $problem_list,
            'fileserver' => $fileserver_list,
            'request_config' => $request_config
        ]);
    }

    /**
     * Show the view Request page
     *
     * @return void
     */
    public function requestviewAction()
    {
        $request    = Manage::loadRequestName();

        View::renderTemplate('manage/request-view.html', [
            'request'   => $request
        ]);
    }

    /**
     * Add Request
     *
     * @return void
     */
    public function addrequestAction()
    {

        $process_request = new Manage($_POST);

        $process_request->saveRequestName();

        Flash::addMessage('Request successfully added');

        $this->redirect('/manages/category');
    }

    /**
     * Show the Config Request page
     *
     * @return void
     */
    public function requestconfigAction()
    {
        $request            = Manage::loadRequestName();
        $request_config     = Manage::loadRequestConfigName();
        View::renderTemplate('manage/request-config.html', [
            'request'   => $request,
            'request_config' => $request_config
        ]);
    }

    public function checkrequestconfig()
    {
        if (isset($_GET['request']) && isset($_GET['site'])) {

            $resquest = new Manage($_GET);
            echo json_encode($resquest->checkRequestConfig());
        }
    }

    /**
     * Add Request Config
     *
     * @return void
     */
    public function addrequestconfigAction()
    {
        /*
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        */
        $process_request = new Manage($_POST);

        $process_request->saveRequestConfigName();

        Flash::addMessage('Request configuration successfully added');

        $this->redirect('/manages/category');
    }

    /**
     * Show the Problem page
     *
     * @return void
     */
    public function problemAction()
    {
        $problem    = Manage::loadProblemName();

        View::renderTemplate('manage/problem.html', [
            'problem'   => $problem
        ]);
    }

    /**
     * Add Problem
     *
     * @return void
     */
    public function addproblemAction()
    {

        if ($_POST) {

            $process_problem = new Manage($_POST);

            $process_problem->saveProblemName();

            Flash::addMessage('Problem successfully added');

            $this->redirect('/manages/category');
        }
    }

    /**
     * Show the File Server page
     *
     * @return void
     */
    public function fileserverAction()
    {
        $file_server    = Manage::loadFileServerName();

        View::renderTemplate('manage/fileserver.html', [
            'file_server' => $file_server
        ]);
    }

    /**
     * Add File Server
     *
     * @return void
     */
    public function addfileserverAction()
    {

        if ($_POST) {

            $process_problem = new Manage($_POST);

            $process_problem->saveFileServerName();

            Flash::addMessage('Server successfully added');

            $this->redirect('/manages/category');
        }
    }
}
