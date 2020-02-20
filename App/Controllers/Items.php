<?php

namespace App\Controllers;

use \Core\View;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
class Items extends Authenticated
{

    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    /*
    protected function before()
    {
        $this->requireLogin();
    }
    */

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Items/index.html');
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }

    public function testAction()
    {
        $set_date = "2020-01-31";

        $items = array('2020-01-15', '2020-01-31', '2020-02-15', '2020-02-28');

        $item_count = count($items);

        $key = array_search($set_date, $items);

        //echo $key;

        for ($i=$key; $i < $item_count; $i++) { 
            # code...
            echo $items[$i]."<br>";
        }

    }
}
