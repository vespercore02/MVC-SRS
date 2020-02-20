<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * Manage model
 *
 * PHP version 7.0
 */
class Manage extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function viewAccountRequest($sr_id)
    {
        $sql = 'SELECT * FROM account_request
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $sr_id, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function viewAccountData($id)
    {

        //$return_arr = array();
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT emp_info.full_name, emp_email.email_account, emp_cmms.cmms_pet_id, emp_seine2.seine2_pet_id
                FROM emp_info 
                left join emp_email
                ON emp_info.pet_id = emp_email.email_pet_id
                left join emp_cmms
                ON emp_email.email_pet_id = emp_cmms.cmms_pet_id
                left join emp_seine2
                ON emp_cmms.cmms_pet_id = emp_seine2.seine2_pet_id
                ORDER by emp_info.id ASC
                LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function viewAccountTotal()
    {

        $sql = 'SELECT emp_info.full_name, emp_email.email_account, emp_cmms.cmms_pet_id, emp_seine2.seine2_pet_id
                FROM emp_info 
                left join emp_email
                ON emp_info.pet_id = emp_email.email_pet_id
                left join emp_cmms
                ON emp_email.email_pet_id = emp_cmms.cmms_pet_id
                left join emp_seine2
                ON emp_cmms.cmms_pet_id = emp_seine2.seine2_pet_id
                ORDER by emp_info.id ASC';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function viewEmailAccountData($id)
    {

        //$return_arr = array();
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT emp_email.email_account, emp_info.full_name
                FROM  emp_email
                left join emp_info
                ON emp_email.email_pet_id = emp_info.pet_id
                WHERE emp_info.full_name is not null
                ORDER by emp_info.pet_id ASC
                LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function viewEmailAccountTotal()
    {

        $sql = 'SELECT emp_email.email_account, emp_info.full_name
                FROM  emp_email
                left join emp_info
                ON emp_email.email_pet_id = emp_info.pet_id
                WHERE emp_info.full_name is not null
                ORDER by emp_info.pet_id ASC';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function viewCmmsAccountData($id)
    {

        //$return_arr = array();
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT emp_info.full_name, emp_cmms.cmms_pet_id
                FROM emp_cmms 
                left join emp_info
                ON emp_cmms.cmms_pet_id = emp_info.pet_id  
                ORDER by emp_cmms.cmms_pet_id ASC
                LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function viewCmmsAccountTotal()
    {

        $sql = 'SELECT emp_info.full_name, emp_cmms.cmms_pet_id
                        FROM emp_cmms 
                        left join emp_info
                        ON emp_cmms.cmms_pet_id = emp_info.pet_id  
                        ORDER by emp_cmms.cmms_pet_id ASC';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function viewSeineAccountData($id)
    {

        //$return_arr = array();
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT emp_seine2.seine2_pet_id, emp_info.full_name
                FROM  emp_seine2
                left join emp_info
                ON  emp_seine2.seine2_pet_id = emp_info.pet_id
                ORDER by emp_seine2.seine2_pet_id ASC
                LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function viewSeineAccountTotal()
    {

        $sql = 'SELECT emp_seine2.seine2_pet_id, emp_info.full_name
                FROM  emp_seine2
                left join emp_info
                ON  emp_seine2.seine2_pet_id = emp_info.pet_id
                ORDER by emp_seine2.seine2_pet_id ASC';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }
    /**
     * Load Account with Role
     */
    public static function loadAccountName()
    {
        $sql = 'SELECT * FROM ';
    }

    /**
     * Save Request Name
     * 
     */

    public function saveRequestName()
    {
        $sql = 'INSERT INTO process_request (request_name)Values(:request_name)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':request_name', $this->request_name, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Load Request List
     * 
     * @return mixed
     */

    public static function loadRequestName()
    {
        $sql = 'SELECT request_name FROM process_request';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save Problem Name
     * 
     */

    public function saveProblemName()
    {
        $sql = 'INSERT INTO process_problem (problem_name)Values(:problem_name)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':problem_name', $this->problem_name, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Load Problem List
     * 
     * @return mixed
     */

    public static function loadProblemName()
    {
        $sql = 'SELECT problem_name FROM process_problem';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save File Server Name
     * 
     */

    public function saveFileServerName()
    {
        $sql = 'INSERT INTO process_server (server_name)Values(:server_name)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':server_name', $this->file_server_name, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Load File Server List
     * 
     * @return mixed
     */

    public static function loadFileServerName()
    {
        $sql = 'SELECT server_name FROM process_server';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save Request Config Name
     * 
     */

    public function saveRequestConfigName()
    {
        $sql = 'INSERT INTO process_request_config (request_name, site, estimate_days)Values(:request_name, :site, :estimate_days)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':request_name', $this->request_name, PDO::PARAM_STR);
        $stmt->bindValue(':site', $this->request_site, PDO::PARAM_STR);
        $stmt->bindValue(':estimate_days', $this->estimate_days, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Load Request Config List
     * 
     * @return mixed
     */

    public static function loadRequestConfigName()
    {
        $sql = 'SELECT * FROM process_request_config';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkRequestConfig()
    {
        $sql = 'SELECT request_name, site, estimate_days 
                FROM process_request_config
                WHERE request_name = :request_name
                and site = :site';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':request_name', $this->request, PDO::PARAM_STR);
        $stmt->bindValue(':site', $this->site, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
