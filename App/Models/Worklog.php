<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \App\Models\User;
use DateTime;
use DatePeriod;
use DateInterval;

class Worklog extends \Core\Model
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

    
    /**
     * Insert Work Log
     *
     * @return void
     */
    public function addLog()
    {
        $sql = 'INSERT INTO srvcrqst_wrklg (sr_id, wrklg_date, wrklg_status, wrklg_dtls, wrklg_personnel, endorse_to, wrklg_hours)
        VALUES(:sr_id, :wrklg_date, :wrklg_status, :wrklg_dtls, :wrklg_personnel, :endorse_to, :wrklg_hours)';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':wrklg_date', date("Y-m-d H:i"));
        $stmt->bindValue(':wrklg_status', $this->endorse_status);
        $stmt->bindValue(':wrklg_dtls', htmlspecialchars($this->endorse_msg));
        $stmt->bindValue(':wrklg_personnel', $this->endorse_by);
        $stmt->bindValue(':endorse_to', $this->endorse_to);
        $stmt->bindValue(':wrklg_hours', $this->hours);

        return $stmt->execute();
    }

    /**
     * Delete Work log
     *
     * @return void
     */
    public function deleteLog()
    {
        $sql = 'DELETE FROM srvcrqst_wrklg where sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);


        return $stmt->execute();
    }

    public function viewLogData()
    {
        $id     =   $this->id;
        $limit  =   $this->limit;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT * FROM srvcrqst_wrklg WHERE sr_id = :sr_id
        ORDER BY wrklg_id DESC LIMIT ' . $start . ', ' . $limit . '';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->filter_name);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {


            $count = $stmt->rowCount();
            $rows   = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for ($s = 0; $s < $count; $s++) {
                # code...
                $return_arr1[] = array(
                    $rows[$s]['wrklg_date'],
                    $rows[$s]['wrklg_dtls'],
                    $rows[$s]['wrklg_personnel']
                );
            }

            return $return_arr1;
        } else {
            return false;
        }
    }

    public function viewLogPage()
    {
        $sql = 'SELECT * FROM srvcrqst_wrklg WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->filter_name);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function getLastLog($sr_id)
    {
        $sql = 'SELECT wrklg_date FROM srvcrqst_wrklg WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $sr_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetchall(PDO::FETCH_ASSOC);

            return $row[0]['wrklg_date'];
        }
    }
}

?>