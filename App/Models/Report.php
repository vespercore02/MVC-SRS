<?php

namespace App\Models;

use PDO;
use \App\Token;

class Report extends \Core\Model
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

    public static function getMonthlySiteMembers($site, $date1, $date2)
    {
        $sql = 'SELECT sr_assigned_to
                FROM srvcrqst
                WHERE sr_site = :site
                AND sr_assigned_to != ""
                AND sr_date_request BETWEEN :date1 AND :date2
                GROUP BY sr_assigned_to';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        }

        return 0;
    }

    public static function getMonthlySiteMembersStatus($member, $status, $site, $date1, $date2)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                WHERE sr_site = :site
                AND sr_status = :status
                AND sr_assigned_to = :member
                AND sr_date_request BETWEEN :date1 AND :date2
                GROUP BY sr_assigned_to';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':member', $member);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

            return $rows['total'];
        }

        return 0;
    }

    public static function getMonthlySiteStatus($status, $site, $date1, $date2)
    {

        $sql = 'SELECT sr_status, count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_process != "QA"
        AND sr_status = :status
        AND sr_date_request between :date1 and :date2
        GROUP BY sr_status';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

            return $rows['total'];
        }

        return 0;
    }

    public static function getMonthlySiteStatusProcess($status, $process, $site, $date1, $date2)
    {

        $sql = 'SELECT count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_status = :status
        AND sr_process = :process
        AND sr_date_request between :date1 and :date2
        GROUP BY sr_status';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();
        //$rows = $stmt->fetchall(PDO::FETCH_ASSOC);


        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $badge = $rows['total'];

            return $badge;
        }

        return 0;
    }

    public static function getMonthlySiteProcessOngoing($process, $site, $date1, $date2)
    {

        $current_date = date('y-m-d');

        $sql = 'SELECT count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_process = :process
        AND sr_status in ("Assigned", "Work in Progress")
        AND sr_date_request between :date1 and :date2';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();
        //$rows = $stmt->fetchall(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $badge = $rows['total'];

            return $badge;
        }

        return 0;
    }

    public static function getMonthlySiteProcessOngoingCSR($process, $site, $date1, $date2)
    {

        $current_date = date('y-m-d');

        $sql = 'SELECT count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_process = :process
        AND sr_status in ("Assigned", "Work in Progress")
        AND sr_date_request between :date1 and :date2
        AND sr_date_set > :current_date';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);
        $stmt->bindValue(':current_date', $current_date);

        $stmt->execute();
        //$rows = $stmt->fetchall(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $badge = $rows['total'];

            return $badge;
        }

        return 0;
    }

    public static function getMonthlySiteProcessDelay($process, $site, $date1, $date2)
    {

        $current_date = date('y-m-d');

        $sql = 'SELECT count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_process = :process
        AND sr_status in ("Assigned", "Work in Progress")
        AND sr_date_request between :date1 and :date2
        AND sr_date_set < :current_date';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);
        $stmt->bindValue(':current_date', $current_date);

        $stmt->execute();
        //$rows = $stmt->fetchall(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $badge = $rows['total'];

            return $badge;
        }

        return 0;
    }

    public static function getMonthlySiteProcessTotal($process, $site, $date1, $date2)
    {

        $sql = 'SELECT count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_process = :process
        AND sr_status in ("New Request","Assigned", "Work in Progress", "Done", "Closed")
        AND sr_date_request between :date1 and :date2';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();
        //$rows = $stmt->fetchall(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            $badge = $rows['total'];

            return $badge;
        }

        return 0;
    }

    public function getMonthlySiteDelay()
    {

        $sql = 'SELECT sr_status, count(sr_id) as total 
        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_date_request between :date1 and :date2
        and sr_date_set < sr_done_date';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':site', $this->site);
        $stmt->bindValue(':date1', $this->date1);
        $stmt->bindValue(':date2', $this->date2);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

            return $rows['total'];
        }

        return 0;
    }

    public static function getMonthlySiteRequest($site, $date1, $date2)
    {

        $sql = 'SELECT  sr_id, sr_process, sr_number, sr_year, 
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

        FROM srvcrqst
        WHERE sr_site = :site
        AND sr_status in ("New Request","Assigned", "Work in Progress", "Done", "Closed")
        AND sr_date_request between :date1 and :date2';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        }

        return 0;
    }

    public static function getMonthlySiteRequestList($process, $site, $date1, $date2, $limit_no)
    {
        $sql = 'SELECT sr_rqst, COUNT(sr_rqst) as Total
                FROM srvcrqst
                WHERE sr_site = :site
                AND sr_process = :process
                AND sr_status in ("New Request","Assigned", "Work in Progress", "Done", "Closed")
                AND sr_date_request between :date1 and :date2
                GROUP BY sr_rqst ORDER BY `Total` DESC limit '.$limit_no;

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        }

        return 0;
    }

    public static function getMonthlySiteRequestListAndManhour($process, $site, $date1, $date2, $limit_no)
    {
        $sql = 'SELECT sr_rqst, SUM(sr_man_hour) as man_hour_total
                FROM srvcrqst
                WHERE sr_site = :site
                AND sr_process = :process
                AND sr_status in ("New Request","Assigned", "Work in Progress", "Done", "Closed")
                AND sr_date_request between :date1 and :date2
                GROUP BY sr_rqst Order By man_hour_total';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':site', $site);
        $stmt->bindValue(':process', $process);
        $stmt->bindValue(':date1', $date1);
        $stmt->bindValue(':date2', $date2);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        }

        return 0;
    }
}
