<?php

namespace App\Models;

use PDO;
use \App\Token;


class Daily extends \Core\Model
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

    public static function getMembers()
    {
        $sql = 'SELECT emp_info.full_name, emp_info.first_name, emp_info.pet_id, emp_srs.role, emp_email.email_account 
        From emp_info LEFT JOIN emp_srs on emp_info.pet_id = emp_srs.pet_id 
        LEFT JOIN emp_email on emp_info.pet_id = emp_email.email_pet_id 
        where emp_info.pet_id LIKE "%-admin%"';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getChecker()
    {
        $sql = 'SELECT emp_info.full_name, emp_info.first_name, emp_info.pet_id, emp_email.email_account 
                From emp_info LEFT JOIN emp_srs on emp_info.pet_id = emp_srs.pet_id 
                LEFT JOIN emp_email on emp_info.pet_id = emp_email.email_pet_id  
                where emp_info.pet_id like "%-admin" and emp_srs.role not in ("Support")';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getReport($name)
    {
        $date_now = date('Y-m-d');

        $sql = 'SELECT sr_id, sr_process, sr_number, sr_year, 
                        sr_date_request, 
                        sr_date_set, 
                        sr_rqstr, 
                        sr_rqstr_dprtmnt, 
                        sr_site,
                        sr_rqst,
                        sr_dtls,
                        sr_status,
                        sr_assigned_date,
                        sr_acknowledged_date,
                        sr_done_date,
                        sr_assigned_to 
                from srvcrqst where sr_status not in ("Done", "Closed", "Cancelled", "Cancelled by User", "Rejected") 
                and sr_assigned_to= :name 
                and sr_in_charge= :name  
                
                and :date_now> sr_date_set';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':date_now', $date_now);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
