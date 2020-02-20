<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\RequestPool;
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
class Endorsements extends Authenticated
{

    public function endorsement()
    {
        print_r($_POST);

        if (!empty($_POST['action']) and $_POST['action'] != "No Good") {

            if ($_POST['action'] == "Endorse to Checker") {

                $_POST['endorse_status'] = "For Checking";

                $endorsement = new Endorsement($_POST);
                $endorsement->endorseForChecking();
            }

            if ($_POST['action'] == "Endorse to Approver") {

                $_POST['endorse_status'] = "For Approval";

                $endorsement = new Endorsement($_POST);
                $endorsement->endorseForApproval();
            }

            if ($_POST['action'] == "Endorse to MIS") {

                $_POST['endorse_status'] = "New Request";



                $endorsement = new Endorsement($_POST);
                $endorsement->endorseNewRequest();

                $_POST['endorse_to'] = "MIS";
            }

            if ($_POST['action'] == "Endorse to Support") {

                $_POST['endorse_status'] = "Assigned";

                if ($_POST['process'] == "CSR") {


                    if ($_POST['sr_change_date_needed'] == "No") {
                        $_POST['sr_adjust_date_needed'] = "";
                    }



                    $endorsement = new Endorsement($_POST);
                    $endorsement->endorseAssignedCSR();
                }

                if ($_POST['process'] == "CPR") {

                    $endorsement = new Endorsement($_POST);
                    $endorsement->endorseAssignedCPR();
                }

                if ($_POST['process'] == "DRR") {

                    $endorsement = new Endorsement($_POST);
                    $endorsement->endorseAssignedDRR();
                }
            }

            if ($_POST['action'] == "Acknowledge") {

                $_POST['endorse_status'] = "Work in Progress";

                $endorsement = new Endorsement($_POST);
                $endorsement->endorseWorkinProgress();
            }

            if ($_POST['action'] == "Done") {

                $_POST['endorse_status'] = "Done";

                if ($_POST['process'] == "CSR") {

                    $format = 'm/d/Y';
                    $date_request_pre           = $_POST['date_done'];
                    $date_request_req           = DateTime::createFromFormat($format, $date_request_pre);
                    $_POST['date_done']       = $date_request_req->format('Y-m-d');

                    $_POST['time_done'] = date("H:i", strtotime($_POST['time_done']));
                    $done_datetime =  $_POST['date_done'] . " " . $_POST['time_done'];

                    //$_POST['sr_man_hour'] = Request::computeManHour($_POST['date_set'], $done_datetime);

                    $_POST['sr_man_hour'] = $_POST['hours'] + Request::manhour($_POST['sr_id']);

                    $_POST['sr_down_time'] = "";

                    if (!isset($_POST['sr_prif'])) {
                        $_POST['sr_prif'] = "";
                    }
                }

                if ($_POST['process'] == "CPR") {

                    $format = 'm/d/Y';
                    $date_request_pre           = $_POST['date_done'];
                    $date_request_req           = DateTime::createFromFormat($format, $date_request_pre);
                    $_POST['date_done']       = $date_request_req->format('Y-m-d');

                    $_POST['time_done'] = date("H:i", strtotime($_POST['time_done']));
                    $done_datetime =  $_POST['date_done'] . " " . $_POST['time_done'];

                    $_POST['sr_man_hour'] = $_POST['sr_man_hour'] = $_POST['hours'] + Request::manhour($_POST['sr_id']);
                    

                    $_POST['sr_down_time'] = Request::computeManHour($_POST['date_set'], $done_datetime) * $_POST['num_affected'] ;

                    if (!isset($_POST['sr_prif'])) {
                        $_POST['sr_prif'] = "";
                    }
                }

                if ($_POST['process'] == "DRR") {
                    $format = 'm/d/Y';
                    $date_request_pre           = $_POST['date_done'];
                    $date_request_req           = DateTime::createFromFormat($format, $date_request_pre);
                    $_POST['date_done']       = $date_request_req->format('Y-m-d');

                    $_POST['time_done'] = date("H:i", strtotime($_POST['time_done']));
                    $done_datetime =  $_POST['date_done'] . " " . $_POST['time_done'];

                    //$_POST['sr_man_hour'] = Request::computeManHour($_POST['date_set'], $done_datetime);

                    $_POST['sr_man_hour'] = $_POST['hours'] + Request::manhour($_POST['sr_id']);

                    $_POST['sr_down_time'] = "";

                    if (!isset($_POST['sr_prif'])) {
                        $_POST['sr_prif'] = "";
                    }
                }

                

                $endorsement = new Endorsement($_POST);

                $endorsement->endorseDone();
            }

            if ($_POST['action'] == "Confirmation") {

                if ($_POST['user_approval'] == "Confirmed") {

                    $_POST['endorse_status'] = "Done";
                }

                if ($_POST['user_approval'] == "Rejected") {

                    $_POST['endorse_status'] = "Assigned";
                }

                $endorsement = new Endorsement($_POST);

                $_POST['endorse_status'] = $_POST['user_approval'];

                $endorsement->userConfirmation();
            }

            if ($_POST['action'] == "Endorse to SRS Checker") {

                $_POST['endorse_status'] = "For Checking";

                $endorsement = new Endorsement($_POST);
                $endorsement->endorseAssesment();
            }

            if ($_POST['action'] == "Assessment") {

                if ($_POST['assessment'] == 'Closed') {

                    $_POST['endorse_status'] = "Closed";

                    $_POST['endorse_to'] = "";

                    $endorsement = new Endorsement($_POST);

                    $endorsement->endorseAssesment();
                }

                if ($_POST['assessment'] == 'Rejected') {

                    $_POST['endorse_status'] = "Rejected";

                    $_POST['srs_checker'] = "";

                    $endorsement = new Endorsement($_POST);

                    $endorsement->endorseAssesment();
                }
            }
        } else {

            $endorsement = new Endorsement($_POST);

            $endorsement->NoGood();

            //echo "Yeah";
        }
        if (empty($_POST['hours'])) {
            $_POST['hours'] = 0;
        }

        $worklog     = new Worklog($_POST);

        $worklog->addLog();



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
            $docu_path   = "http://$_SERVER[HTTP_HOST]/requests/" . $_POST['sr_process'] . "/" . $_POST['sr_id'];
        } else {
            # code...

            Flash::addMessage('Endorsement unsuccessful, please properly and try again', "warning");

            $this->redirect(Auth::getReturnToPage());
        }

        $stamp = new Dss($_POST);

        if (!empty($_POST['action']) and $_POST['action'] != "No Good") {

            $stamp->save($name, $id, $department, $reference, $docu_path);
        }


        if (!empty($_POST['endorse_to'])) {

            echo $_POST['endorse_to'];

            $recipient = User::getEmail($_POST['endorse_to']);
            $addCC      = User::getEmail($_POST['sr_rqstr']);

            self::composeMail($_POST['endorse_to'], $_POST['sr_rqstr'], $_POST['endorse_status'], $_POST['sr_id'], $_POST['sr_process'] . "-" . sprintf("%04d", $_POST['sr_number']) . "-" . $_POST['sr_year']);

            Flash::addMessage('Request endorsement is successful.');

            $this->redirect('/requests/view/' . $_POST['sr_id']);
        } else {
            # code...
            $this->redirect('/requests/view/' . $_POST['sr_id']);
        }
    }

    public function warrayAction()
    {
        $group_name = array('Aaron Jet N. Sarreal', 'Jasper Labenia', 'Willem R. Leonardo');

        if (is_array($group_name)) {

            $addCC['email_account'] = array();

            foreach ($group_name as $name) {
                $email = User::getEmail($name);

                $addCC['email_account'][] = $email['email_account'];
            }
        } else {
            $addCC      = User::getEmail($group_name);
        }

        //echo $addCC['email_account'];

        print_r($addCC['email_account']);
        $to      = 'smb_srs.pet@ph.yazaki.com';

        $cc      = $addCC['email_account'];

        $subject = "SRS Update Testing";

        $message = 'Hello<br>
		                <br>
		               Mother Father Sister Brother <br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';

        Mail::send($to, $cc, $subject, $message);
    }

    public static function composeMail($recipient, $requestor, $status, $id, $reference)
    {
        echo $recipient;

        $recipient = User::getEmail($recipient);

        if (is_array($requestor)) {

            $addCC['email_account'] = array();

            foreach ($requestor as $name) {

                if (!empty($name)) {

                    $email = User::getEmail($name);

                    $addCC['email_account'][] = $email['email_account'];
                }
            }
        } else {
            $addCC      = User::getEmail($requestor);
        }


        if ($status == "For Checking") {

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Endorsement [$status] $reference";

            $message = 'Hello ' . $recipient['full_name'] . '<br>
		                <br>
		                This request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> endorse to you, For your checking.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "For Approval") {

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Endorsement [$status] $reference";

            $message = 'Hello ' . $recipient['full_name'] . '<br>
		                <br>
		                This request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> endorse to you, For your approval.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "New Request") {

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Endorsement [$status] $reference";

            $message = 'Hello ' . $recipient['full_name'] . '<br>
		                <br>
		                New Request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> is sent for endorsement .<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "Assigned") {

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Endorsement [$status] $reference";

            $message = 'Hello ' . $recipient['full_name'] . '<br>
		                <br>
		                This request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> is assigned to you.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "Work in Progress") {

            $to      = $addCC['email_account'];

            $cc      = $recipient['email_account'];

            $subject = "SRS Update [$status] $reference";

            $message = 'Hello ' . $addCC['full_name'] . '<br>
		                <br>
		                Your request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> is now Work in Progress.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "Done") {

            $to      = $addCC['email_account'];

            $cc      = $recipient['email_account'];

            $subject = "SRS Update [$status] $reference";

            $message = 'Hello ' . $addCC['full_name'] . '<br>
		                <br>
		                Your request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> is now Done for your Confirmation.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "Rejected" || $status == "Confirmed" || $status == "Completed") {

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Update [$status] $reference";

            $message = 'Hello ' . $recipient['full_name'] . '<br>
		                <br>
		                Your Done request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> is ' . $status . ' by the requestor please check.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        if ($status == "No Good") {

            $to      = $recipient['email_account'];

            $cc      = $addCC['email_account'];

            $subject = "SRS Update [$status] $reference";

            $message = 'Hello ' . $recipient['full_name'] . '<br>
		                <br>
		                Your request <a href="srs.mis.pet/requests/view/' . $id . '">' . $reference . '</a> is ' . $status . ' please check.<br>
		                <br>
		                Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem';
        }

        //echo "<br>";
        //echo $to . "<br>";
        //echo $cc . "<br>";
        //echo $subject . "<br>";
        //echo $message;

        Mail::send($to, $cc, $subject, $message);
    }
}
