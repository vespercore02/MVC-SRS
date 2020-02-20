<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\Endorsements;
use \App\Models\Worklog;
use \App\Models\Endorsement;
use \App\Models\Request;
use \App\Models\User;
use \App\Models\Dss;
use \App\Models\Manage;
use \App\Flash;
use \App\Auth;
use \App\Mail;
use DateTime;

/**
 * Forms controller
 *
 * PHP version 7.0
 */
class Requests extends Authenticated
{
    public function csr()
    {

        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    }
    /**
     * Save filled up Form
     * 
     */
    public function createrequestAction()
    {

        $crtrqst = new Request($_POST);

        if ($crtrqst->getLastNumber()) {

            $autoinc = sprintf("%04d",  $crtrqst->getLastNumber() + 1);
        } else {

            $autoinc = sprintf("%04d", "1");
        }

        if ($_POST['sr_process'] == "CSR") {
            $format = 'm/d/Y';
            $date_request_pre           = $_POST['sr_date_set'];
            $date_request_req           = DateTime::createFromFormat($format, $date_request_pre);
            $_POST['sr_date_set']       = $date_request_req->format('Y-m-d');
        }

        if ($_POST['sr_process'] == "CPR") {
            $format = 'm/d/Y';
            $date_set_pre           = $_POST['sr_date_set_cpr'];
            $date_set_req           = DateTime::createFromFormat($format, $date_set_pre);
            $_POST['sr_date_set']   = $date_set_req->format('Y-m-d');

            $_POST['sr_date_set'] = $_POST['sr_date_set'] . " " . $_POST['sr_time_request'];
        }

        if ($_POST['sr_process'] == "DRR") {
            $format = 'm/d/Y';
            $date_set_pre           = $_POST['sr_date_set_drr'];
            $date_set_req           = DateTime::createFromFormat($format, $date_set_pre);
            $_POST['sr_date_set']   = $date_set_req->format('Y-m-d');

            $_POST['sr_dtls'] = $_POST['sr_filename'] . ";" . $_POST['sr_filelocation'] . ";" . $_POST['sr_restoreto'] . ";" . $_POST['sr_overwrite'] . ";" . $_POST['sr_reason'];
        }


        $_POST['sr_number'] = $autoinc;
        $crtrqst = new Request($_POST);

        $_POST['sr_id'] = $crtrqst->save();

        if ($_POST['sr_rqst'] == "Account Request") {

            $account_no = count($_POST['account_system']);

            for ($i = 0; $i < $account_no; $i++) {

                //$_POST['sr_id'] = $sr_id;
                $_POST['pet_id'] = $_POST['account_id_no'][$i];
                $_POST['first_name'] = $_POST['account_fname'][$i];
                $_POST['middle_initial'] = $_POST['account_minitial'][$i];
                $_POST['last_name'] = $_POST['account_lname'][$i];
                $_POST['department'] = $_POST['account_department'][$i];
                $_POST['request'] = $_POST['account_request'][$i];
                //$_POST['system_request'] = $_POST['account_system'][$i];
                $system_request = "";
                for ($a = 0; $a < count($_POST['account_system'][$i]); $a++) {
                    $system_request .= $_POST['account_system'][$i][$a] . ", ";
                }
                $_POST['system_request'] = $system_request;
                $crtrqst = new Request($_POST);

                if (
                    !empty($_POST['pet_id']) and
                    !empty($_POST['first_name']) and
                    !empty($_POST['last_name']) and
                    !empty($_POST['department']) and
                    !empty($_POST['request']) and
                    !empty($_POST['department']) and
                    !empty($_POST['system_request'])
                ) {

                    $crtrqst->saveAccountRequest();
                } else {
                }
            }
        }

        if ($_POST['sr_rqst'] == "Access Request") {

            $account_no = count($_POST['access_request']);

            echo $account_no;

            for ($i = 0; $i < $account_no; $i++) {

                $user_info = explode(" - ", $_POST['access_name'][$i]);

                //$_POST['sr_id'] = $sr_id;
                $_POST['pet_id'] = $user_info[1];
                $_POST['name'] = $user_info[0];
                $_POST['request'] = $_POST['access_request'][$i];
                $_POST['path_request'] = $_POST['access_path'][$i];

                $crtrqst = new Request($_POST);


                if (
                    !empty($_POST['pet_id']) and
                    !empty($_POST['name']) and
                    !empty($_POST['request']) and
                    !empty($_POST['path_request'])
                ) {

                    $crtrqst->saveAccessRequest();
                }
            }
        }

        Flash::addMessage('Request created is successful.');

        $this->redirect('/');
    }


    public function cprrequestAction()
    {
        
        $crtrqst = new Request($_POST);

        if ($crtrqst->getLastNumber()) {

            $autoinc = sprintf("%04d",  $crtrqst->getLastNumber() + 1);
        } else {

            $autoinc = sprintf("%04d", "1");
        }

        $format = 'm/d/Y';
        $date_set_pre           = $_POST['sr_date_set_cpr'];
        $date_set_req           = DateTime::createFromFormat($format, $date_set_pre);
        $_POST['sr_date_set']   = $date_set_req->format('Y-m-d');

        $_POST['sr_date_set'] = $_POST['sr_date_set'] . " " . $_POST['sr_time_request'];

        $_POST['sr_number'] = $autoinc;

        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        $crtrqst = new Request($_POST);

        $_POST['sr_id'] = $crtrqst->CreateCPR();

        $stamp = new Dss($_POST);

        $docu_path   = "http://$_SERVER[HTTP_HOST]/requests/view/" . $_POST['sr_id'];

        $reference  = $_POST['sr_process'] . "-" . sprintf("%04d", $_POST['sr_number']) . "-" . $_POST['sr_year'];

        $stamp->save($_POST['sr_rqstr'], $_POST['sr_rqstr_id'], $_POST['sr_rqstr_dprtmnt'], $reference, $docu_path);

        Endorsements::composeMail('MIS', array($_POST['sr_rqstr'], $_POST['checker'], $_POST['approver']), 'New Request', $_POST['sr_id'], $_POST['sr_process'] . "-" . $_POST['sr_number'] . "-" . date('y'));

        Flash::addMessage('Request created is successful.');

        $this->redirect('/');
    }


    public function viewAction()
    {

        $details        = Request::requestDetails($this->route_params['id']);
        $prepared_sig   = Dss::loadStamp($details['sr_process'] . "-" . sprintf("%04d", $details['sr_number']) . "-" . $details['sr_year'], $details['sr_prepared']);
        $checker_sig   = Dss::loadStamp($details['sr_process'] . "-" . sprintf("%04d", $details['sr_number']) . "-" . $details['sr_year'], $details['sr_checker']);
        $approver_sig   = Dss::loadStamp($details['sr_process'] . "-" . sprintf("%04d", $details['sr_number']) . "-" . $details['sr_year'], $details['sr_approver']);
        $request        = Manage::loadRequestName();

        if ($details['sr_rqst'] == "Account Request") {
            $account_request = Request::AccountRequest($this->route_params['id']);
            $access_request = "";
        } else if ($details['sr_rqst'] == "Access Request") {
            $access_request = Request::AccessRequest($this->route_params['id']);
            $account_request = "";
        } else {
            $account_request = "";
            $access_request = "";
        }


        if (!empty($details['sr_approver'])) {
            $status = "For Approval";
            $in_charge = $details['sr_approver'];
        } else {
            $status = "For Checking";
            $in_charge = $details['sr_checker'];
        }

        //echo $_SESSION['full_name']." ".$details['sr_in_charge'];

        if ($details['sr_rqstr'] == $_SESSION['full_name'] || $details['sr_in_charge'] == $_SESSION['full_name']) {

            //echo $_SESSION['full_name']."<br>";
            //echo $details['sr_status']."<br>";
            //echo $details['sr_in_charge']."<br>";
            //echo $details['sr_assigned_to']."<br>";
            //echo $details['sr_user_approval']."<br>";

            if (strpos($_SESSION['role'], "Member") !== false && $details['sr_status'] == "Newly Created") {

                $endorse_list = User::getEndorseList($_SESSION['department'], "Checker");

                $endorse_to = "Checker";

                $endorse_status = "For Checking";

                $button = "Endorse to Checker";
            } else if (strpos($_SESSION['role'], "Checker") !== false && $details['sr_status'] == "Newly Created") {

                $endorse_list = User::getEndorseList($_SESSION['department'], "Approver");

                $endorse_to = "Approver";

                $button = "Endorse to Approver";
            } else if (strpos($_SESSION['role'], "Checker") !== false && $details['sr_status'] == "For Checking" && $details['sr_checker'] == $_SESSION['full_name']) {

                $endorse_list = User::getEndorseList($_SESSION['department'], "Approver");

                $endorse_to = "Approver";

                $endorse_status = "For Approval";

                $button = "Endorse to Approver";
            } else if (strpos($_SESSION['role'], "Approver") !== false && $details['sr_status'] == "Newly Created") {

                $endorse_list = array(array('full_name' => "MIS"));

                $endorse_to = "MIS";

                $endorse_status = "New Request";

                $button = "Endorse to MIS";
            } else if (strpos($_SESSION['role'], "Approver") !== false && $details['sr_status'] == "For Approval" && $details['sr_approver'] == $_SESSION['full_name']) {

                $endorse_list = array(array('full_name' => "MIS"));

                $endorse_to = "MIS";

                $endorse_status = "New Request";

                $button = "Endorse to MIS";
            } else if (strpos($_SESSION['role'], "Support") !== false && $details['sr_status'] == "Assigned") {

                $endorse_list =  "";

                $endorse_to = "Acknowledge";

                $endorse_status = "Work in Progress";

                $button = "Acknowledge";
            } else if (strpos($_SESSION['role'], "SRS Checker") !== false && $details['sr_status'] == "Assigned") {

                $endorse_list =  "";

                $endorse_to = "Acknowledge";

                $endorse_status = "Work in Progress";

                $button = "Acknowledge";
            } else if (strpos($_SESSION['role'], "Support") !== false && $details['sr_status'] == "Work in Progress") {

                $endorse_list =  "";

                $endorse_to = "";

                $endorse_status = "Done";

                $button = "Done";
            } else if (strpos($_SESSION['role'], "SRS Checker") !== false && $details['sr_status'] == "Work in Progress") {

                $endorse_list =  "";

                $endorse_to = "";

                $endorse_status = "Done";

                $button = "Done";
            }else if (strpos($_SESSION['role'], "SRS Checker") !== false && $details['sr_status'] == "Work in Progress") {

                $endorse_list =  "";

                $endorse_to = "";

                $endorse_status = "Done";

                $button = "Done";
            } else if ($details['sr_status'] == "Done" && $details['sr_in_charge'] == $_SESSION['full_name'] && $details['sr_user_approval'] == "") {

                $endorse_list =  "";

                $endorse_to = "";

                $endorse_status = "Confirmation";

                $button = "Confirmation";
            } else if ($details['sr_status'] == "Done" && $details['sr_in_charge'] == $_SESSION['full_name'] && $details['sr_user_approval'] == "Rejected") {

                $endorse_list =  "";

                $endorse_to = "";

                $endorse_status = "Confirmation";

                $button = "Confirmation";
            } else if ($details['sr_status'] == "Done" && $details['sr_in_charge'] == $_SESSION['full_name'] && $details['sr_assigned_to'] == $_SESSION['full_name'] && $details['sr_user_approval'] == "Confirmed" || $details['sr_user_approval'] == "Completed") {

                $endorse_list = User::getEndorseList($_SESSION['department'], "SRS Checker");

                $endorse_to = "Checker";

                $endorse_status = "Done";

                $button = "Endorse to SRS Checker";
            } else if ($details['sr_status'] == "Rejected" && $details['sr_in_charge'] == $_SESSION['full_name'] && $details['sr_assigned_to'] == $_SESSION['full_name'] && $details['sr_user_approval'] == "Confirmed") {

                $endorse_list = User::getEndorseList($_SESSION['department'], "SRS Checker");

                $endorse_to = "Checker";

                $endorse_status = "Done";

                $button = "Endorse to SRS Checker";
            } else if ($details['sr_status'] == "Done" && $details['sr_in_charge'] == $_SESSION['full_name'] && $details['sr_assigned_to'] != $_SESSION['full_name'] && $details['sr_user_approval'] == "Confirmed") {

                $endorse_list = "";

                $endorse_to = "";

                $endorse_status = "";

                $button = "Assessment";
            } else if ($details['sr_status'] == "For Checking" && $details['sr_in_charge'] == $_SESSION['full_name'] && $details['sr_assigned_to'] != $_SESSION['full_name'] && $details['sr_user_approval'] == "Confirmed") {

                $endorse_list = "";

                $endorse_to = "";

                $endorse_status = "";

                $button = "Assessment";
            } else if ($details['sr_status'] == "No Good" && $details['sr_in_charge'] == $_SESSION['full_name']) {

                $endorse_list = "";

                $endorse_to = "";

                $endorse_status = "Newly Created";

                $button = "Modify";
            } else {
                # code...

                $endorse_list =  "";

                $endorse_to = "";

                $endorse_status = "";

                $button = "";
            }
        } else if (strpos($_SESSION['role'], "Service Desk") !== false && $details['sr_status'] == "New Request") {

            $endorse_list = User::getMISEndorseList($_SESSION['department']);

            $endorse_to = "Support";

            $endorse_status = "Assigned";

            $button = "Endorse to Support";
        } else if (strpos($_SESSION['role'], "SRS Checker") !== false && $details['sr_status'] == "New Request") {

            $endorse_list = User::getMISEndorseList($_SESSION['department']);

            $endorse_to = "Support";

            $endorse_status = "Assigned";

            $button = "Endorse to Support";
        } else if (strpos($_SESSION['role'], "Support") !== false && $details['sr_status'] == "Assigned") {
            $endorse_list = "";

            $endorse_to = "";

            $endorse_status = "";

            $button = "";
        } else if (strpos($_SESSION['role'], "Support") !== false && $details['sr_status'] == "Work in Progress") {
            $endorse_list = "";

            $endorse_to = "";

            $endorse_status = "";

            $button = "";
        } else if (strpos($_SESSION['role'], "SRS Checker") !== false && $details['sr_status'] == "Done") {

            $endorse_list = "";

            $endorse_to = "";

            $endorse_status = "";

            $button = "";
        } else {
            # code...
            //echo $_SESSION['role'];
            $endorse_list =  "";

            $endorse_to = "";

            $endorse_status = "";

            $button = "";
        }

        //echo $details['sr_status'];
        //var_dump($prepared_sig);
        /*
        echo "<pre>";
        print_r($prepared_sig);
        echo "</pre>";
        */
        View::renderTemplate('request/' . $details['sr_process'] . '.html', [
            'logged_user' => $_SESSION['full_name'],
            'details'   => $details,
            'prepared_sig'   => $prepared_sig,
            'checker_sig'   => $checker_sig,
            'approver_sig'   => $approver_sig,
            'button' => $button,
            'endorse_to' => $endorse_to,
            'endorse_status' => $endorse_status,
            'endorse_list' => $endorse_list,
            'request'   => $request,
            'status'   => $status,
            'in_charge'   => $in_charge,
            'account_request'   => $account_request,
            'access_request'   => $access_request
        ]);
    }

    public function modifyAction()
    {
        $request        = Manage::loadRequestName();
        $details        = Request::requestDetails($this->route_params['id']);

        if (!empty($details['sr_approver'])) {
            $status = "For Approval";
            $in_charge = $details['sr_approver'];
        } else {
            $status = "For Checking";
            $in_charge = $details['sr_checker'];
        }

        View::renderTemplate('modify/' . $details['sr_process'] . '.html', [
            'details'   => $details,
            'request'   => $request,
            'status'   => $status,
            'in_charge'   => $in_charge
        ]);
    }

    public function updaterequestAction()
    {

        if ($_POST['modify'] == "update") {

            print_r($_POST);

            if ($_POST['sr_process'] == "CSR") {
                $format = 'm/d/Y';
                $date_set_pre                   = $_POST['sr_date_set'];
                $date_set_req                   = DateTime::createFromFormat($format, $date_set_pre);
                $_POST['sr_date_set']           = $date_set_req->format('Y-m-d');
            }


            if ($_POST['sr_process'] == "CPR") {

                $_POST['sr_date_request'] = $_POST['sr_date_request'] . " " . $_POST['sr_time_request'];
            }

            if ($_POST['sr_process'] == "DRR") {

                $_POST['sr_dtls'] = $_POST['sr_filename'] . ";" . $_POST['sr_filelocation'] . ";" . $_POST['sr_restoreto'] . ";" . $_POST['sr_overwrite'] . ";" . $_POST['sr_reason'];
            }

            $_POST['endorse_status'] = $_POST['sr_status'];

            $_POST['endorse_msg'] = "Request Updated";

            $_POST['endorse_by'] = $_POST['sr_rqstr'];

            $_POST['endorse_to'] = $_POST['sr_in_charge'];

            $updaterqst = new Request($_POST);

            $updaterqst->edit();

            $worklog = new Worklog($_POST);

            $worklog->addLog();

            $recipient = User::getEmail($_POST['sr_in_charge']);

            $addCC      = User::getEmail($_POST['sr_rqstr']);

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Endorsement [" . $_POST['sr_status'] . "] " . $_POST['sr_process'] . "-" . $_POST['sr_number'] . "-" . $_POST['sr_year'];

            $message = 'Hello ' . $recipient['full_name'] . '<br>
                            <br>
                            This request <a href="srs.mis.pet/requests/view/' . $_POST['sr_id'] . '">' . $_POST['sr_process'] . "-" . $_POST['sr_number'] . "-" . $_POST['sr_year'] . '</a> is now edited and endorse back to you, ' . $_POST['sr_status'] . '.<br>
                            <br>
                            Thank you,<br>
                            <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';

            Mail::send($to, $cc, $subject, $message);

            Flash::addMessage('Update successful.');

            $this->redirect('/requests/view/' . $_POST['sr_id']);
        } else {

            $updaterqst->deleteLog();

            $updaterqst->delete();

            Flash::addMessage('Request successfully deleted.');

            $this->redirect('/');
        }
    }


    public function searchAction()
    {
        View::renderTemplate('/request/search.html');
    }

    public function searchdata()
    {
        if (isset($_GET['id'])) {

            $id = $_GET['id'];
            $resquest_log = new Request($_GET);
            $search_result = $resquest_log->searchData();
            $search_result_page = $resquest_log->searchPage();
        } else {
            $id = 1;
            $resquest_log = new Request($_GET);
            $search_result = $resquest_log->searchData();
            $search_result_page = $resquest_log->searchPage();
        }

        //echo "<pre>";
        //print_r($search_result);
        //echo "</pre>";

        View::renderTemplate('/request/search.html', [
            'name' => $_GET['name'],
            'department' => $_GET['department'],
            'control_no' => $_GET['control_no'],
            'assigned_mis' => $_GET['assigned_mis'],
            'details' => $_GET['details'],
            'pirf_no' => $_GET['pirf_no'],
            'current_page' => $id,
            'search_result' => $search_result,
            'search_result_page' => $search_result_page
        ]);
    }

    public function getSearchData()
    {

        //echo json_encode($_GET['id']);
        if (isset($_GET['id'])) {

            $resquest_log = new Request($_GET);
            echo json_encode($resquest_log->searchData());
        }
    }

    public function getSearchPage()
    {

        //echo json_encode($_GET['id']);

        $resquest_log = new Request($_GET);
        echo json_encode($resquest_log->searchPage());
    }

    public function endorsement()
    {
        //print_r($_POST);

        if (!empty($_POST['endorse_by'])) {
            $name       = $_POST['endorse_by'];
        } else {
            # code...

            Flash::addMessage('Endorsement unsuccessful, please properly and try again', "warning");

            $this->redirect(Auth::getReturnToPage());
        }

        if (!empty($_POST['endorse_by_pet_id'])) {
            $id         = $_POST['endorse_by_pet_id'];
        } else {
            # code...

            Flash::addMessage('Endorsement unsuccessful, please properly and try again', "warning");

            $this->redirect(Auth::getReturnToPage());
        }

        if (!empty($_POST['endorse_by_department'])) {
            $department = $_POST['endorse_by_department'];
        } else {
            # code...

            Flash::addMessage('Endorsement unsuccessful, please properly and try again', "warning");

            $this->redirect(Auth::getReturnToPage());
        }

        if (!empty($_POST['endorse_by']) && !empty($_POST['sr_number']) && !empty($_POST['sr_year'])) {
            $reference  = $_POST['sr_process'] . "-" . sprintf("%04d", $_POST['sr_number']) . "-" . $_POST['sr_year'];
        } else {
            # code...

            Flash::addMessage('Endorsement unsuccessful, please properly and try again', "warning");

            $this->redirect(Auth::getReturnToPage());
        }

        if (!empty($_POST['sr_process']) && !empty($_POST['sr_id'])) {
            $docu_path   = "http://$_SERVER[HTTP_HOST]/request" . $_POST['sr_process'] . "/" . $_POST['sr_id'];
        } else {
            # code...

            Flash::addMessage('Endorsement unsuccessful, please properly and try again', "warning");

            $this->redirect(Auth::getReturnToPage());
        }

        if (!empty($_POST['sr_adjust_date_needed'])) {
            $format = 'm/d/Y';
            $date_request_pre                   = $_POST['sr_adjust_date_needed'];
            $date_request_req                   = DateTime::createFromFormat($format, $date_request_pre);
            $_POST['sr_adjust_date_needed']     = $date_request_req->format('Y-m-d');
        }

        if ($_POST['sr_process'] == "CPR" and $_POST['endorse_status'] == "Done") {
            if (empty($_POST['sr_man_hour']) or !isset($_POST['sr_man_hour'])) {

                $format = 'm/d/Y';
                $date_request_pre           = $_POST['date_done'];
                $date_request_req           = DateTime::createFromFormat($format, $date_request_pre);
                $_POST['date_done']       = $date_request_req->format('Y-m-d');

                $_POST['time_done'] = date("H:i", strtotime($_POST['time_done']));
                $done_datetime =  $_POST['date_done'] . " " . $_POST['time_done'];

                $_POST['sr_down_time'] = Request::computeManHour($_POST['date_set'], $done_datetime);

                $_POST['sr_man_hour'] = $_POST['sr_down_time'] * $_POST['num_affected'];
            }
        }

        if ($_POST['sr_process'] == "DRR" and $_POST['endorse_status'] == "Done") {

            $format = 'm/d/Y';
            $date_request_pre           = $_POST['date_done'];
            $date_request_req           = DateTime::createFromFormat($format, $date_request_pre);
            $_POST['date_done']       = $date_request_req->format('Y-m-d');

            $_POST['time_done'] = date("H:i", strtotime($_POST['time_done']));
            $done_datetime =  $_POST['date_done'] . " " . $_POST['time_done'];

            $_POST['sr_man_hour'] = Request::computeManHour($_POST['date_set'], $done_datetime);
        }

        $endorsement = new Endorsement($_POST);
        $worklog     = new Worklog($_POST);
        /*

        if ($endorsement->EndorseRequest()) {

            $worklog->addLog();

            $stamp = new Dss($_POST);

            if (!empty($_POST['action']) and $_POST['action'] != "No Good") {

                $stamp->save($name, $id, $department, $reference, $docu_path);
            }


            if (!empty($_POST['endorse_to'])) {

                echo $_POST['endorse_to'];

                $recipient = User::getEmail($_POST['endorse_to']);
                $addCC      = User::getEmail($_POST['sr_rqstr']);

                self::composeMail($_POST['endorse_to'], $_POST['sr_rqstr'], $_POST['endorse_status'], $_POST['sr_id'], $_POST['sr_process'] . "-" . $_POST['sr_number'] . "-" . $_POST['sr_year']);

                Flash::addMessage('Request endorsement is successful.');

                $this->redirect('/requests/view/' . $_POST['sr_id']);
            }
        }
        */
    }

    /*
    public function sentMail()
    {
        self::composeMail("MIS", "Willem R. Leonardo", "Confirmed", "1", "CSR-1234-56");
    }
    */



    public function addLog()
    {
        $addlog = new Worklog($_POST);

        $addlog->addLog();

        Flash::addMessage('Add Log is successful.');

        $this->redirect('/requests/view/' . $_POST['sr_id']);
    }


    public function viewLogData()
    {
        if (isset($_GET['id']) && isset($_GET['start']) && isset($_GET['limit']) && isset($_GET['filter_name'])) {

            $resquest_log = new Worklog($_GET);
            echo json_encode($resquest_log->viewLogData());
        }
    }

    public function viewLogPage()
    {
        if (isset($_GET['filter_name'])) {

            $resquest_log = new Worklog($_GET);
            echo json_encode($resquest_log->viewLogPage());
        }
    }


    public function getCountNotification()
    {
        if (isset($_GET['name'])) {

            $CountNotif = new Request($_GET);
            echo json_encode($CountNotif->getCountNotification());
        }
    }


    public function notificationAction()
    {



        View::renderTemplate('request/notification.html');
    }

    public function getDataNotification()
    {
        if (isset($_GET['name'])) {

            $CountNotif = new Request($_GET);
            echo json_encode($CountNotif->get10DataNotification());
        }
    }

    public function getFullDataNotification()
    {
        if (isset($_GET['name'])) {

            $CountNotif = new Request($_GET);
            echo json_encode($CountNotif->getDataNotification());
        }
    }




    /**
     * Transfer data from old to new database
     *
     * @return void
     */
    public function transferData()
    {
        $resquest = new Request();
        echo $resquest->transferData();
    }

    public function manhour()
    {
        //echo $_GET['id'];
        $_POST['sr_id'] = $_GET['sr_id'];
        $addlog = new Request($_POST);

        echo $addlog->getManHour($_GET['sr_id']);
    }
}
