<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\RequestPool as RequestPools;
use \App\Auth;
use \App\Flash;


class RequestPool extends Authenticated
{
    protected $newreq;
    protected $newreq_csr;
    protected $newreq_cpr;
    protected $newreq_drr;

    protected $assigned;
    protected $assigned_csr;
    protected $assigned_cpr;
    protected $assigned_drr;

    protected $wip;
    protected $wip_csr;
    protected $wip_cpr;
    protected $wip_drr;

    protected $total_request;
    protected $delay_request;
    protected $done_request;

    protected function before()
    {
        $user = Auth::getUser();

        if (strpos($user['pet_id'], '-admin') == false) {

            # code...
            Flash::addMessage('Your not allowed to access.', "warning");

            $this->redirect('/');
        } else {

            # code...
            $this->newreq     = RequestPools::status_count("New Request");
            $this->newreq_csr = RequestPools::process_status_count("New Request", "CSR");
            $this->newreq_cpr = RequestPools::process_status_count("New Request", "CPR");
            $this->newreq_drr = RequestPools::process_status_count("New Request", "DRR");

            $this->assigned     = RequestPools::status_count("Assigned");
            $this->assigned_csr = RequestPools::process_status_count("Assigned", "CSR");
            $this->assigned_cpr = RequestPools::process_status_count("Assigned", "CPR");
            $this->assigned_drr = RequestPools::process_status_count("Assigned", "DRR");

            $this->wip     = RequestPools::status_count("Work in Progress");
            $this->wip_csr = RequestPools::process_status_count("Work in Progress", "CSR");
            $this->wip_cpr = RequestPools::process_status_count("Work in Progress", "CPR");
            $this->wip_drr = RequestPools::process_status_count("Work in Progress", "DRR");

            $this->total_request = RequestPools::getCuttOffRequest(self::cutOff());
            $this->delay_request = RequestPools::getCuttOffDelay(self::cutOff());
            $this->done_request = RequestPools::getCuttOffDone(self::cutOff());

            if ($this->route_params['action'] == "index") {

                $this->action = "New Request";
            } elseif ($this->route_params['action'] == "WorkinProgress") {

                $this->action = "Work in Progress";
            } else {

                $this->action = $this->route_params['action'];
            }

            if (isset($this->route_params['id'])) {

                $this->id = $this->route_params['id'];
            } else {

                $this->id = 1;
            }

            $this->request_data = RequestPools::requestPoolData($this->id, $this->action);
            $this->request_page = RequestPools::requestPoolPage($this->action);

            View::renderTemplate('request/requestpool.html', [
                'status' => $this->action,
                'request_data' => $this->request_data,
                'request_page' => $this->request_page,
                'current_page' => $this->id,
                'newreq' => $this->newreq,
                'assigned' => $this->assigned,
                'wip' => $this->wip,
                'total_request' => $this->total_request,
                'delay_request' => $this->delay_request,
                'done_request' => $this->done_request
            ]);
        }
    }

    public function indexAction()
    {

        
    }

    public function assignedAction()
    {
        
    }

    public function workinprogressAction()
    {
        
    }

    public function getRequestPoolCountNotification()
    {
        if (isset($_GET['name'])) {

            $RequestPoolCoun = new RequestPools($_GET);
            echo json_encode($RequestPoolCoun->getRequestPoolCountNotification());
        }
    }
    /*
    public function requestPoolData()
    {
        if (isset($_GET['id']) && isset($_GET['start']) && isset($_GET['limit']) && isset($_GET['filter_name'])) {

            $resquest_log = new RequestPools($_GET);
            //echo json_encode($resquest_log->requestPoolData());
        }
    }

    public function requestPoolPage()
    {
        if (isset($_GET['filter_name'])) {

            $resquest_log = new RequestPools($_GET);
            echo json_encode($resquest_log->requestPoolPage());
        }
    }
    */
    public static function cutOff()
    {
        //$date_now   = date("Y"."-05-05");
        $date_now   = date("Y-m-d");
        $year       = date("Y");
        $prev       =  $year - 1;


        if ($date_now > "$prev-12-16" and "$year-01-15" > $date_now) {

            return array("$prev-12-16", "$year-01-15");
        }

        if ($date_now > "$year-01-16" and "$year-02-15" > $date_now) {

            return array("$year-01-16", "$year-02-15");
        }

        if ($date_now > "$year-02-16" and "$year-03-15" > $date_now) {

            return array("$year-02-16", "$year-03-15");
        }

        if ($date_now > "$year-03-16" and "$year-04-15" > $date_now) {

            return array("$year-03-16", "$year-04-15");
        }

        if ($date_now > "$year-04-16" and "$year-05-15" > $date_now) {

            return array("$year-04-16", "$year-05-15");
        }

        if ($date_now > "$year-05-16" and "$year-06-15" > $date_now) {

            return array("$year-05-16", "$year-06-15");
        }

        if ($date_now > "$year-06-16" and "$year-07-15" > $date_now) {

            return array("$year-06-16", "$year-07-15");
        }

        if ($date_now > "$year-07-16" and "$year-08-15" > $date_now) {

            return array("$year-07-16", "$year-08-15");
        }

        if ($date_now > "$year-08-16" and "$year-09-15" > $date_now) {

            return array("$year-08-16", "$year-09-15");
        }

        if ($date_now > "$year-09-16" and "$year-10-15" > $date_now) {

            return array("$year-09-16", "$year-10-15");
        }

        if ($date_now > "$year-10-16" and "$year-11-15" > $date_now) {

            return array("$year-10-16", "$year-11-15");
        }

        if ($date_now > "$year-11-16" and "$year-12-15" > $date_now) {

            return array("$year-11-16", "$year-12-15");
        }
    }
}
