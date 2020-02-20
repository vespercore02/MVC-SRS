<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Request;
use \App\Controllers\RequestPool;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{
    protected $newly_created;
    protected $for_checking;
    protected $for_approval;
    protected $new_request;

    protected $assigned;
    protected $work_in_progress;
    protected $done;
    protected $closed;

    protected $total_request;
    protected $delay_request;
    protected $assigned_request;
    protected $wip_request;
    protected $done_request;
    protected $closed_request;

    protected function before()
    {

        $user = Auth::getUser();

        $this->assigned         = Request::inchargeRequest("Assigned",$user['full_name']);
        $this->work_in_progress = Request::inchargeRequest("Work in Progress",$user['full_name']);
        $this->done             = Request::inchargeRequest("Done",$user['full_name']);
        $this->closed           = Request::inchargeRequest("Closed",$user['full_name']);

        if ($this->route_params['action'] == "index") {

            $this->action = "";
        } elseif ($this->route_params['action'] == "WorkinProgress") {

            $this->action = "Work in Progress";
        } elseif ($this->route_params['action'] == "NewlyCreated") {

            $this->action = "Newly Created";
        } elseif ($this->route_params['action'] == "ForChecking") {

            $this->action = "For Checking";
        } elseif ($this->route_params['action'] == "ForApproval") {

            $this->action = "For Approval";
        } elseif ($this->route_params['action'] == "NewRequest") {

            $this->action = "New Request";
        } else {

            $this->action = $this->route_params['action'];
        }

        if (isset($this->route_params['id'])) {

            $this->id = $this->route_params['id'];
        } else {

            $this->id = 1;
        }

        $this->request_data = Request::inchgargerequestdata($this->id, $this->action, $user['full_name']);
        $this->request_page = Request::inchgargerequestdatapage($this->action, $user['full_name']);


        if (strpos($user['pet_id'],'-admin') !== false) {

            $this->total_request    = Request::getCuttOffRequest(RequestPool::cutOff(), $user['full_name']);
            $this->delay_request    = Request::getCuttOffDelay(RequestPool::cutOff(), $user['full_name']);
            $this->assigned_request = Request::getCuttOffRequest(RequestPool::cutOff(), "Assigned", $user['full_name']);
            $this->wip_request      = Request::getCuttOffRequest(RequestPool::cutOff(), "Work in Progress", $user['full_name']);
            $this->done_request     = Request::getCuttOffRequest(RequestPool::cutOff(), "Done", $user['full_name']);
            $this->closed_request   = Request::getCuttOffRequest(RequestPool::cutOff(), "Closed", $user['full_name']);

            View::renderTemplate('home/index-admin.html', [
                'status'            => $this->action,
                'request_data'      => $this->request_data,
                'request_page'      => $this->request_page,
                'current_page'      => $this->id,
                'assigned'          => $this->assigned,
                'workinprogress'    => $this->work_in_progress,
                'done'              => $this->done,
                'closed'            => $this->closed,
                'this_month_assigned' => $this->assigned_request,
                'this_month_wip'    => $this->wip_request,
                'this_month_done'   => $this->done_request,
                'this_month_closed' => $this->closed_request,
                'this_month_delay'  => $this->delay_request,
                'this_month_total'  => $this->total_request

            ]);

        } elseif (isset($user['full_name'])) {

            $this->newly_created    = Request::myrequestcount("Newly Created",$user['full_name']);
            $this->for_checking     = Request::myrequestcount("For Checking",$user['full_name']);
            $this->for_approval     = Request::myrequestcount("For Approval",$user['full_name']);
            $this->new_request      = Request::myrequestcount("New Request",$user['full_name']);
            $this->assigned         = Request::myrequestcount("Assigned",$user['full_name']);
            $this->work_in_progress = Request::myrequestcount("Work in Progress",$user['full_name']);
            $this->done             = Request::myrequestcount("Done",$user['full_name']);
            $this->closed           = Request::myrequestcount("Closed",$user['full_name']);
            $this->last5request     = Request::mylast5request($user['full_name']);

            $this->myrequestdata    = Request::myrequestdata($this->id, $this->action, $user['full_name']);
            $this->myrequestdatapage= Request::myrequestdatapage($this->action, $user['full_name']);

            View::renderTemplate('home/index-notadmin.html', [
                'status'            => $this->action,
                'request_data'      => $this->myrequestdata,
                'request_page'      => $this->myrequestdatapage,
                'current_page'      => $this->id,
                'newly_created'     => $this->newly_created,
                'for_checking'      => $this->for_checking,
                'for_approval'      => $this->for_approval,
                'new_request'       => $this->new_request,
                'assigned'          => $this->assigned,
                'workinprogress'    => $this->work_in_progress,
                'done'              => $this->done,
                'closed'            => $this->closed,
                'last5request'      => $this->last5request
            ]);

        }else {
            # code...
            View::renderTemplate('login/new.html', [
                //'user' => Auth::getUser()
            ]);
        }
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        
    }

    public function assignedAction()
    {
        
    }

    public function workinprogressAction()
    {
        
    }
    
    public function doneAction()
    {
        
    }

    public function closedAction()
    {
        
    }

    public function newlycreatedAction()
    {
        
    }
    
    public function forcheckingAction()
    {
        
    }
    
    public function forapprovalAction()
    {
        
    }
    
    public function newrequestAction()
    {
        
    }

}
