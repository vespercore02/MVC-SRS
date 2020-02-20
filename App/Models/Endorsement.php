<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \App\Models\User;
use DateTime;
use DatePeriod;
use DateInterval;

class Endorsement extends \Core\Model
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
   

    public function endorseForChecking()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_checker = :sr_checker,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);
        $stmt->bindValue(':sr_checker', $this->endorse_to);

        return $stmt->execute();
    }

    public function endorseForApproval()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_approver = :sr_approver,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);
        $stmt->bindValue(':sr_approver', $this->endorse_to);

        return $stmt->execute();
    }

    public function endorseNewRequest()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);

        return $stmt->execute();
    }

    public function endorseAssigned()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_received_by = :sr_received_by, 
                sr_received_date = :sr_received_date, 
                sr_assigned_to = :sr_assigned_to, 
                sr_assigned_date = :sr_assigned_date,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);

        $stmt->bindValue(':sr_received_by', $this->endorse_by);
        $stmt->bindValue(':sr_received_date', date("Y-m-d H:i"));
        $stmt->bindValue(':sr_assigned_to', $this->endorse_to);
        $stmt->bindValue(':sr_assigned_date', date("Y-m-d H:i"));

        return $stmt->execute();
    }

    public function endorseAssignedCSR()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_received_by = :sr_received_by, 
                sr_received_date = :sr_received_date, 
                sr_assigned_to = :sr_assigned_to, 
                sr_assigned_date = :sr_assigned_date, 
                sr_change_date = :sr_change_date, 
                sr_adjusted_date = :sr_adjusted_date,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);

        $stmt->bindValue(':sr_received_by', $this->endorse_by);
        $stmt->bindValue(':sr_received_date', date("Y-m-d H:i"));
        $stmt->bindValue(':sr_assigned_to', $this->endorse_to);
        $stmt->bindValue(':sr_assigned_date', date("Y-m-d H:i"));

        $stmt->bindValue(':sr_change_date', $this->sr_change_date_needed);
        $stmt->bindValue(':sr_adjusted_date', $this->sr_adjust_date_needed);

        return $stmt->execute();
    }

    public function endorseAssignedCPR()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_received_by = :sr_received_by, 
                sr_received_date = :sr_received_date, 
                sr_assigned_to = :sr_assigned_to, 
                sr_assigned_date = :sr_assigned_date, 
                sr_num_affected = :sr_num_affected,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);

        $stmt->bindValue(':sr_received_by', $this->endorse_by);
        $stmt->bindValue(':sr_received_date', date("Y-m-d H:i"));
        $stmt->bindValue(':sr_assigned_to', $this->endorse_to);
        $stmt->bindValue(':sr_assigned_date', date("Y-m-d H:i"));

        $stmt->bindValue(':sr_num_affected', $this->sr_num_affected);

        return $stmt->execute();
    }

    public function endorseAssignedDRR()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_received_by = :sr_received_by, 
                sr_received_date = :sr_received_date, 
                sr_assigned_to = :sr_assigned_to, 
                sr_assigned_date = :sr_assigned_date
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);

        $stmt->bindValue(':sr_received_by', $this->endorse_by);
        $stmt->bindValue(':sr_received_date', date("Y-m-d H:i"));
        $stmt->bindValue(':sr_assigned_to', $this->endorse_to);
        $stmt->bindValue(':sr_assigned_date', date("Y-m-d H:i"));

        return $stmt->execute();
    }

    public function endorseWorkinProgress()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status,  
                sr_acknowledged_date = :sr_acknowledged_date
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);

        $stmt->bindValue(':sr_acknowledged_date',  date("Y-m-d H:i"));

        return $stmt->execute();
    }

    public function endorseDone()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_done_date = :sr_done_date, 
                sr_man_hour = :sr_man_hour, 
                sr_down_time = :sr_down_time,
                sr_in_charge = :sr_in_charge,
                sr_prif = :sr_prif
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);

        $stmt->bindValue(':sr_done_date', $this->date_done);
        $stmt->bindValue(':sr_man_hour', $this->sr_man_hour);

        $stmt->bindValue(':sr_prif', $this->sr_prif);
        $stmt->bindValue(':sr_down_time', $this->sr_down_time);

        return $stmt->execute();
    }

    public function userConfirmation()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_user_approval = :sr_user_approval,
                sr_in_charge = :sr_in_charge 
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);
        $stmt->bindValue(':sr_user_approval', $this->user_approval);

        return $stmt->execute();
    }

    public function endorseAssesment()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_in_charge = :sr_in_charge,
                 sr_mis_checker = :sr_mis_checker
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);
        $stmt->bindValue(':sr_in_charge', $this->endorse_to);
        $stmt->bindValue(':sr_mis_checker', $this->endorse_to);

        return $stmt->execute();
    }

    public function endorseForClosing()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status,
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->endorse_status);

        return $stmt->execute();
    }

    public function NoGood()
    {
        $sql = 'UPDATE srvcrqst
                SET sr_status = :sr_status, 
                sr_in_charge = :sr_in_charge
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':sr_status', $this->action);
        $stmt->bindValue(':sr_in_charge', $this->sr_rqstr);

        return $stmt->execute();
    }
}
