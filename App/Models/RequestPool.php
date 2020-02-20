<?php


namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \App\Models\User;
use DateTime;
use DatePeriod;
use DateInterval;


class RequestPool extends \Core\Model
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

    public function getRequestPoolCountNotification()
    {

        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                WHERE sr_status = :request';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':request', $this->name);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function requestPoolData($id, $status)
    {
        $return_arr = array();
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT * FROM srvcrqst where sr_status=:status LIMIT ' . $start . ', ' . $limit . '';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':status', $status, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $count = $stmt->rowCount();
            $rows   = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for ($s = 0; $s < $count; $s++) {
                # code...
                $date = date_create($rows[$s]['sr_date_set']);
                $date = date_format($date, "m/d/y ");

                $return_arr[] = array(
                    $rows[$s]['sr_id'],
                    $rows[$s]['sr_process'],
                    $rows[$s]['sr_number'],
                    $rows[$s]['sr_year'],
                    $date,
                    $rows[$s]['sr_rqstr'],
                    $rows[$s]['sr_rqst'],
                    $rows[$s]['sr_status']
                );
            }


            //$return_arr1[] = $stmt->rowCount();

            return $return_arr;
        } else {

            //$return_arr1[] = "<tr><td></td><td>Not found</td></tr>";
            return false;
        }
    }

    public static function requestPoolPage($status)
    {

        $sql = 'SELECT * FROM srvcrqst where sr_status=:status ';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':status', $status, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function status_count($status)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                WHERE sr_status = :status';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':status', $status);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function process_status_count($status, $process)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                WHERE sr_status = :status
                AND sr_process = :process';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':process', $process);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function getCuttOffRequest($date)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                Where sr_date_request between :date1 and :date2';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':date1', $date[0]);
        $stmt->bindValue(':date2', $date[1]);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function getCuttOffDelay($date)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                Where sr_date_request between :date1 and :date2
                and sr_date_set < sr_done_date';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':date1', $date[0]);
        $stmt->bindValue(':date2', $date[1]);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function getCuttOffDone($date)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                Where sr_date_request between :date1 and :date2
                and sr_status in ("Done", "Closed")';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':date1', $date[0]);
        $stmt->bindValue(':date2', $date[1]);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }
}
