<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Manage;
use \App\Models\User;
/**
 * Forms controller
 *
 * PHP version 7.0
 */
class Forms extends Authenticated
{
    
    /**
     * Show the CSR Form page
     *
     * @return void
     */
    public function csrAction()
    {
        $request    = Manage::loadRequestName();
        
        View::renderTemplate('form/csr.html',[
            'request'   => $request
        ]);
    }


    /**
     * Show the CPR Form page
     *
     * @return void
     */
    public function cprAction()
    {
        $problem    = Manage::loadProblemName();

        $checker_list = User::getEndorseList($_SESSION['department'], "Checker");
        $approver_list = User::getEndorseList($_SESSION['department'], "Approver");

        //echo "<pre>";
        //print_r($approver_list);
        //echo "</pre>";

        View::renderTemplate('form/cpr.html',[
            'problem'   => $problem,
            'checker_list'   => $checker_list,
            'approver_list'  => $approver_list
        ]);
    }


    /**
     * Show the DRR Form page
     *
     * @return void
     */
    public function drrAction()
    {
        $file_server    = Manage::loadFileServerName();

        View::renderTemplate('form/drr.html',[
            'file_server'   => $file_server
        ]);
    }

    
}
