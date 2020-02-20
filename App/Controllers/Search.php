<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Manage;
use \App\Models\Request;

class Search extends Authenticated
{
    public function indexAction()
    {
        View::renderTemplate('/search/index.html');
    }

    public function search()
    {
        echo $_SERVER['QUERY_STRING'];
        echo "<pre>";
        print_r($_GET);
        echo "<pre>";

        $_GET['id'] = 1;

        $resquest_log = new Request($_GET);

        echo "<pre>";
        print_r($resquest_log->searchData());
        echo "<pre>";

        echo $resquest_log->searchPage();

    }
}
