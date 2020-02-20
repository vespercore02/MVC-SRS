<?php

namespace App\Models;

use App\Controllers\RequestPool;
use PDO;
use \App\Token;
use \App\Mail;
use \App\Models\User;
use DateTime;
use DatePeriod;
use DateInterval;

/**
 * User model
 *
 * PHP version 7.0
 */
class Request extends \Core\Model
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
     * Construct File and File path
     * 
     */
    public function saveFile()
    {
        if (isset($_FILES['file'])) {
            foreach ($_FILES['file']['name'] as $size => $val) {
                $tmp_size = $_FILES['file']['name'][$size];
                //echo $tmp_size;
            }
            if (!empty($tmp_size)) {
                $tmp_path = $_FILES['file']['tmp_name'];
                $file_name = $_FILES['file']['name'];
            } else {
                $tmp_path = "";
                $file_name = "";
            }
        }
    }

    /**
     * Save for IC number
     * 
     * @return mixed Auto increment ic with IC on first and and Year in the end,
     */
    public function saveIc()
    {
        $this->ic_year    = "ic_" . date("Y");

        $sql = 'INSERT INTO $this->ic_year (ic_no, prcss_no)Values("", "")';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        $this->ic_id = $db->lastInsertId();
        $autoinc = sprintf("%04d", $this->ic_id);
        $ic_no =  "IC-" . $autoinc . date("-" . 'y');

        $this->ic_no = $ic_no;
    }

    /**
     * Update IC number
     * 
     * @param string $name
     */
    public function updateIc()
    {

        $sql = 'UPDATE $ic_year SET ic_no = :ic_no, prcss_no = :prcss_no WHERE srvc_rqst_id =:ic_id';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':ic_no', $this->ic_no, PDO::PARAM_STR);
        $stmt->bindValue(':prcss_no', $this->process, PDO::PARAM_STR);
        $stmt->bindValue(':ic_id', $this->ic_no, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Save Service Request
     * 
     * 
     */
    public function saveServiceRequest()
    {
        $this->status = "Newly Created";

        if (!empty($this->file_name)) {
            $this->dir_name = "attachments/" . $this->ic_no;
            if (file_exists($this->dir_name)) {
            } else {
                mkdir("$this->dir_name");
                $this->uploads_dir = "$this->dir_name";

                $this->upload_file = "";
                foreach ($this->file_name as $key => $val) {
                    $this->upload_file_path = $this->tmp_path[$key];
                    $this->upload_file_name = $this->file_name[$key];
                    move_uploaded_file($this->upload_file_path, "$this->uploads_dir/$this->upload_file_name");
                    $this->upload_file .= $this->uploads_dir . "/" . $this->upload_file_name . ",";
                }
            }
        } else {

            $upload_file = "No attached file";
        }

        $sql = 'INSERT INTO srvcrqst (ic_no, ic_rqstr_id, ic_rqstr, ic_rqstr_dprtmnt, ic_crtd_date, ic_rqst_date, prcss_no, ic_site, ic_local, ic_mode, ic_ipadd, ic_rqst, ic_dtls, ic_attachment, ic_status)
        VALUES(:ic_no, :ic_rqstr_id, :ic_rqstr, :ic_rqstr_dprtmnt, :ic_crtd_date, :ic_rqst_date, :prcss_no, :ic_site, :ic_local, :ic_mode, :ipadd, :request, :ic_dtls, :ic_attachment, :status)';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':ic_no', $this->ic_no, PDO::PARAM_STR);
    }


    public function save()
    {
        $this->sr_status = "Newly Created";

        if (!empty($this->file_name)) {
            $this->dir_name = "attachments/" . $this->ic_no;
            if (file_exists($this->dir_name)) {
            } else {
                mkdir("$this->dir_name");
                $this->uploads_dir = "$this->dir_name";

                $this->upload_file = "";
                foreach ($this->file_name as $key => $val) {
                    $this->upload_file_path = $this->tmp_path[$key];
                    $this->upload_file_name = $this->file_name[$key];
                    move_uploaded_file($this->upload_file_path, "$this->uploads_dir/$this->upload_file_name");
                    $this->upload_file .= $this->uploads_dir . "/" . $this->upload_file_name . ",";
                }
            }
        } else {

            $this->upload_file = "No attached file";
        }

        $sql = 'INSERT INTO srvcrqst (sr_process, sr_number, sr_year, sr_date_request, sr_date_set, sr_rqstr, sr_rqstr_id, sr_rqstr_dprtmnt, sr_site, sr_local, sr_mode, sr_ipadd, sr_rqst, sr_dtls, sr_attachment, sr_status, sr_prepared)
        VALUES(:sr_process, :sr_number, :sr_year, :sr_date_request, :sr_date_set, :sr_rqstr, :sr_rqstr_id, :sr_rqstr_dprtmnt, :sr_site, :sr_local, :sr_mode, :sr_ipadd, :sr_rqst, :sr_dtls, :sr_attachment, :sr_status, :sr_prepared)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_process', $this->sr_process, PDO::PARAM_STR);
        $stmt->bindValue(':sr_number', $this->sr_number, PDO::PARAM_INT);
        $stmt->bindValue(':sr_year', $this->sr_year, PDO::PARAM_INT);
        $stmt->bindValue(':sr_date_request', $this->sr_date_request, PDO::PARAM_STR);
        $stmt->bindValue(':sr_date_set', $this->sr_date_set, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr', $this->sr_rqstr, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr_id', $this->sr_rqstr_id, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr_dprtmnt', $this->sr_rqstr_dprtmnt, PDO::PARAM_STR);
        $stmt->bindValue(':sr_site', $this->sr_site, PDO::PARAM_STR);
        $stmt->bindValue(':sr_local', $this->sr_local, PDO::PARAM_STR);
        $stmt->bindValue(':sr_mode', $this->sr_mode, PDO::PARAM_STR);
        $stmt->bindValue(':sr_ipadd', $this->sr_ipadd = $this->sr_ipadd ?? NULL, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqst', $this->sr_rqst, PDO::PARAM_STR);
        $stmt->bindValue(':sr_dtls', $this->sr_dtls, PDO::PARAM_STR);
        $stmt->bindValue(':sr_attachment', $this->upload_file, PDO::PARAM_STR);
        $stmt->bindValue(':sr_status', $this->sr_status, PDO::PARAM_STR);
        $stmt->bindValue(':sr_prepared', $this->sr_rqstr);
        $stmt->execute();

        return $db->lastInsertId();
    }

    public function CreateCPR()
    {
        $this->sr_status = "New Request";

        $sql = 'INSERT INTO srvcrqst (sr_process, sr_number, sr_year, sr_date_request, sr_date_set, sr_rqstr, sr_rqstr_id, sr_rqstr_dprtmnt, sr_site, sr_local, sr_mode, sr_ipadd, sr_rqst, sr_dtls, sr_status, sr_prepared, sr_checker, sr_approver)
        VALUES(:sr_process, :sr_number, :sr_year, :sr_date_request, :sr_date_set, :sr_rqstr, :sr_rqstr_id, :sr_rqstr_dprtmnt, :sr_site, :sr_local, :sr_mode, :sr_ipadd, :sr_rqst, :sr_dtls, :sr_status, :sr_prepared, :sr_checker, :sr_approver)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_process', $this->sr_process, PDO::PARAM_STR);
        $stmt->bindValue(':sr_number', $this->sr_number, PDO::PARAM_INT);
        $stmt->bindValue(':sr_year', $this->sr_year, PDO::PARAM_INT);
        $stmt->bindValue(':sr_date_request', $this->sr_date_request, PDO::PARAM_STR);
        $stmt->bindValue(':sr_date_set', $this->sr_date_set, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr', $this->sr_rqstr, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr_id', $this->sr_rqstr_id, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr_dprtmnt', $this->sr_rqstr_dprtmnt, PDO::PARAM_STR);
        $stmt->bindValue(':sr_site', $this->sr_site, PDO::PARAM_STR);
        $stmt->bindValue(':sr_local', $this->sr_local, PDO::PARAM_STR);
        $stmt->bindValue(':sr_mode', $this->sr_mode, PDO::PARAM_STR);
        $stmt->bindValue(':sr_ipadd', $this->sr_ipadd = $this->sr_ipadd ?? NULL, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqst', $this->sr_rqst, PDO::PARAM_STR);
        $stmt->bindValue(':sr_dtls', $this->sr_dtls, PDO::PARAM_STR);
        $stmt->bindValue(':sr_status', $this->sr_status, PDO::PARAM_STR);
        $stmt->bindValue(':sr_prepared', $this->sr_rqstr);
        $stmt->bindValue(':sr_checker', $this->checker);
        $stmt->bindValue(':sr_approver', $this->approver);
        $stmt->execute();

        return $db->lastInsertId();
    }

    public function getLastNumber()
    {
        $sql = 'SELECT MAX(sr_number) as Process_number FROM srvcrqst where sr_process like :sr_process and sr_year LIKE :sr_year';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_process', $this->sr_process, PDO::PARAM_STR);
        $stmt->bindValue(':sr_year', $this->sr_year);

        $stmt->execute();

        $prcss_number = $stmt->fetch(PDO::FETCH_ASSOC);

        return $prcss_number['Process_number'];
    }

    public function edit()
    {
        if (!empty($this->file_name)) {
            $this->dir_name = "attachments/" . $this->ic_no;
            if (file_exists($this->dir_name)) {
            } else {
                mkdir("$this->dir_name");
                $this->uploads_dir = "$this->dir_name";

                $this->upload_file = "";
                foreach ($this->file_name as $key => $val) {
                    $this->upload_file_path = $this->tmp_path[$key];
                    $this->upload_file_name = $this->file_name[$key];
                    move_uploaded_file($this->upload_file_path, "$this->uploads_dir/$this->upload_file_name");
                    $this->upload_file .= $this->uploads_dir . "/" . $this->upload_file_name . ",";
                }
            }
        } else {

            $this->upload_file = "No attached file";
        }


        $sql = 'UPDATE srvcrqst SET
        sr_process = :sr_process, 
        sr_number = :sr_number, 
        sr_year = :sr_year, 
        sr_date_request = :sr_date_request, 
        sr_date_set = :sr_date_set, 
        sr_rqstr = :sr_rqstr, 
        sr_rqstr_id = :sr_rqstr_id, 
        sr_rqstr_dprtmnt = :sr_rqstr_dprtmnt, 
        sr_site = :sr_site, 
        sr_local = :sr_local, 
        sr_ipadd = :sr_ipadd, 
        sr_rqst = :sr_rqst, 
        sr_dtls = :sr_dtls, 
        sr_attachment = :sr_attachment, 
        sr_status = :sr_status, 
        sr_in_charge = :sr_in_charge
        WHERE sr_id = :sr_id';


        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_process', $this->sr_process, PDO::PARAM_STR);
        $stmt->bindValue(':sr_number', $this->sr_number, PDO::PARAM_INT);
        $stmt->bindValue(':sr_year', $this->sr_year, PDO::PARAM_INT);
        $stmt->bindValue(':sr_date_request', $this->sr_date_request, PDO::PARAM_STR);
        $stmt->bindValue(':sr_date_set', $this->sr_date_set, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr', $this->sr_rqstr, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr_id', $this->sr_rqstr_id, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqstr_dprtmnt', $this->sr_rqstr_dprtmnt, PDO::PARAM_STR);
        $stmt->bindValue(':sr_site', $this->sr_site, PDO::PARAM_STR);
        $stmt->bindValue(':sr_local', $this->sr_local, PDO::PARAM_STR);
        $stmt->bindValue(':sr_ipadd', $this->sr_ipadd = $this->sr_ipadd ?? NULL, PDO::PARAM_STR);
        $stmt->bindValue(':sr_rqst', $this->sr_rqst, PDO::PARAM_STR);
        $stmt->bindValue(':sr_dtls', $this->sr_dtls, PDO::PARAM_STR);
        $stmt->bindValue(':sr_attachment', $this->upload_file, PDO::PARAM_STR);
        $stmt->bindValue(':sr_status', $this->sr_status, PDO::PARAM_STR);
        $stmt->bindValue(':sr_in_charge', $this->sr_in_charge, PDO::PARAM_STR);
        $stmt->bindValue(':sr_id', $this->sr_id);


        return $stmt->execute();;
    }

    public function delete()
    {
        $sql = 'DELETE FROM srvcrqst where sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);


        return $stmt->execute();
    }

    public function saveAccountRequest()
    {
        $sql = 'INSERT INTO account_request 
        ( sr_id, pet_id, first_name, middle_initial, last_name, department, request, system_request)
        VALUES( :sr_id, :pet_id, :first_name, :middle_initial, :last_name, :department, :request, :system_request)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':pet_id', $this->pet_id);
        $stmt->bindValue(':first_name', $this->first_name);
        $stmt->bindValue(':middle_initial', $this->middle_initial);
        $stmt->bindValue(':last_name', $this->last_name);
        $stmt->bindValue(':department', $this->department);
        $stmt->bindValue(':request', $this->request);
        $stmt->bindValue(':system_request', $this->system_request);


        return $stmt->execute();
    }

    public function updateAccountRequest()
    {
        $sql = 'UPDATE account_request 
                SET rqst_status = :rqst_status
                WHERE id = :id';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':id', $this->id);
        $stmt->bindValue(':rqst_status', $this->rqst_status);


        return $stmt->execute();
    }

    public function saveAccessRequest()
    {
        $sql = 'INSERT INTO access_request 
        ( sr_id, pet_id, name, request, path_request)
        VALUES( :sr_id, :pet_id, :name, :request, :path_request)';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $this->sr_id);
        $stmt->bindValue(':pet_id', $this->pet_id);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':request', $this->request);
        $stmt->bindValue(':path_request', $this->path_request);


        return $stmt->execute();
    }

    /**
     * Get Request Details 
     *
     * @param string $id
     * @return void
     */
    public static function requestDetails($id)
    {
        $sql = 'SELECT * FROM srvcrqst WHERE sr_id = :sr_id';
        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    public static function AccountRequest($id)
    {
        $sql = 'SELECT * FROM account_request WHERE sr_id = :sr_id';
        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function AccessRequest($id)
    {
        $sql = 'SELECT * FROM access_request WHERE sr_id = :sr_id';
        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Get in charge number of assigned status
     *
     * @param string $status
     * @param string $name
     * @return void
     */
    public static function inchargeRequest($status, $name)
    {
        $sql = 'SELECT sr_status 
                FROM srvcrqst
                WHERE sr_in_charge = :sr_in_charge
                AND sr_status = :sr_status';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_in_charge', $name);
        $stmt->bindValue(':sr_status', $status);

        $stmt->execute();

        return $stmt->rowCount();
    }



    public static function mylast5request($name)
    {
        //$sql = 'SELECT sr_id, sr_process, sr_number, sr_year FROM srvcrqst where sr_rqstr = :requestor ORDER BY sr_id DESC limit 5';

        $sql = 'SELECT srvcrqst.sr_id, srvcrqst.sr_process, srvcrqst.sr_number, srvcrqst.sr_year, srvcrqst_wrklg.wrklg_date 
                FROM srvcrqst 
                LEFT join srvcrqst_wrklg 
                on srvcrqst.sr_id = srvcrqst_wrklg.sr_id 
                WHERE srvcrqst.sr_rqstr = :requestor 
                ORDER BY srvcrqst_wrklg.wrklg_date desc limit 5';
        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':requestor', $name);

        $stmt->execute();

        $sr_array = array();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetchall(PDO::FETCH_ASSOC);

            /*
            for ($i = 0; $i < $stmt->rowCount(); $i++) {
                # code...

                $lastLog_date = self::getLastLog($row[$i]['sr_id']);

                $sr_reference = $row[$i]['sr_process'] . "-" . sprintf("%04d", $row[$i]['sr_number']) . "-" . $row[$i]['sr_year'];

                $sr_array[] = array($row[$i]['sr_id'], $sr_reference, $lastLog_date);
            }
            */

            return $row;
        }
    }

    public function getDataNotification()
    {

        $sql = 'SELECT * 
                FROM srvcrqst
                WHERE sr_status NOT IN ("Work in Progress", "Closed", "Cancelled")
                AND sr_in_charge = :in_charge';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':in_charge', $this->name);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetchall(PDO::FETCH_ASSOC);

            return $row;
        }
    }

    public function getCountNotification()
    {

        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                WHERE sr_status NOT IN ("Work in Progress", "Closed", "Cancelled")
                AND sr_in_charge = :in_charge';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':in_charge', $this->name);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public function get10DataNotification()
    {
        $sql = 'SELECT sr_id, sr_process, sr_number, sr_year, sr_status, sr_user_approval 
        FROM srvcrqst
        WHERE sr_status NOT IN ("Work in Progress", "Closed", "Cancelled")
        AND sr_in_charge = :in_charge ORDER BY sr_id DESC limit 10';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':in_charge', $this->name);

        $stmt->execute();

        $last10Data = array();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetchall(PDO::FETCH_ASSOC);

            for ($i = 0; $i < $stmt->rowCount(); $i++) {
                # code...
                $last10Data[] = array($row[$i]['sr_id'], $row[$i]['sr_process'], $row[$i]['sr_number'], $row[$i]['sr_year'], $row[$i]['sr_status'], $row[$i]['sr_user_approval']);
            }

            return $last10Data;
        }
    }

    public static function myrequestcount($status, $user)
    {
        $sql = 'SELECT * FROM srvcrqst where sr_rqstr=:requestor 
                AND sr_status = :status';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':requestor', $user, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }


    /**
     * Get My request Data
     * 
     */
    public static function myrequestdata($id, $status, $user)
    {
        $return_arr = array();

        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT * FROM srvcrqst where sr_rqstr=:requestor
        AND sr_status = :status 
        ORDER BY sr_id desc  LIMIT ' . $start . ', ' . $limit . '';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':requestor', $user, PDO::PARAM_STR);
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
                    $rows[$s]['sr_dtls'],
                    $rows[$s]['sr_status'],
                    $rows[$s]['sr_in_charge']
                );
            }


            //$return_arr1[] = $stmt->rowCount();

            return $return_arr;
        } else {

            //$return_arr1[] = "<tr><td></td><td>Not found</td></tr>";
            return false;
        }
    }

    /**
     * Get My request Data page
     * 
     */
    public static function myrequestdatapage($status, $user)
    {

        $sql = 'SELECT * FROM srvcrqst where sr_rqstr=:requestor
        AND sr_status = :status  ';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':requestor', $user, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Get Incharge request Data
     * 
     */
    public static function inchgargerequestdata($id, $status, $name)
    {
        $return_arr = array();
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        $sql = 'SELECT * FROM srvcrqst where sr_in_charge=:requestor 
                AND sr_status = :sr_status 
                ORDER BY sr_date_request 
                DESC LIMIT ' . $start . ', ' . $limit . '';


        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':requestor', $name, PDO::PARAM_STR);

        $stmt->bindValue(':sr_status', $status, PDO::PARAM_STR);


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

            return $return_arr;
        } else {

            return false;
        }
    }

    /**
     * Get Incharge request Data page
     * 
     */
    public static function inchgargerequestdatapage($status, $name)
    {

        $sql = 'SELECT * FROM srvcrqst where sr_in_charge=:requestor 
        AND sr_status = :sr_status ';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':requestor', $name, PDO::PARAM_STR);

        $stmt->bindValue(':sr_status', $status, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getNearDeadline($date, $in_charge)
    {
        $sql = 'SELECT * FROM srvcrqst
                Where sr_date_request between :date1 and :date2
                and sr_status in ("Assigned", "Work in Progress")
                and sr_date_set ';
    }

    public static function getCuttOffRequest($date, $sr_in_charge)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                Where sr_date_request between :date1 and :date2
                and sr_in_charge = :sr_in_charge';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':date1', $date[0]);
        $stmt->bindValue(':date2', $date[1]);
        $stmt->bindValue(':sr_in_charge', $sr_in_charge);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function getCuttOffDelay($date, $sr_in_charge)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                Where sr_date_request between :date1 and :date2
                and sr_date_set < sr_done_date
                and sr_in_charge = :sr_in_charge';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':date1', $date[0]);
        $stmt->bindValue(':date2', $date[1]);
        $stmt->bindValue(':sr_in_charge', $sr_in_charge);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }

    public static function getCuttOffStatus($date, $sr_status, $sr_in_charge)
    {
        $sql = 'SELECT count(sr_id) as total 
                FROM srvcrqst
                Where sr_date_request between :date1 and :date2
                and sr_status = :sr_status
                and sr_in_charge = :sr_in_charge';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':date1', $date[0]);
        $stmt->bindValue(':date2', $date[1]);
        $stmt->bindValue(':sr_status', $sr_status);
        $stmt->bindValue(':sr_in_charge', $sr_in_charge);

        $stmt->execute();
        $row = $stmt->fetch();
        $badge = $row['total'];

        return $badge;
    }


    public function searchData()
    {
        $return_arr = array();

        if (empty($this->id)) {

            $id         = 1;
        } else {
            $id         = $this->id;
        }
        $limit      = 10;

        $start  = ($id - 1) * $limit;

        if (!empty($this->name)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_rqstr like "%' . $this->name . '%"';

            if (!empty($this->department)) {
                $sql .= 'AND sr_rqstr_dprtmnt like "%' . $this->name . '%" ';
            }

            if (!empty($this->control_no)) {

                if (strpos($this->control_no, '-')) {

                    $control_no = explode("-", $this->control_no);

                    if (count($control_no) == 3) {
                        $sql .= 'AND sr_process = "' . $control_no[0] . '" and sr_number ="' . $control_no[1] . '" and sr_year like "' . $control_no[2] . '" ';
                    } elseif (count($control_no) == 2) {
                        $sql .= 'AND sr_process = "' . $control_no[0] . '" and sr_number = "' . $control_no[1] . '" ';
                    } else {
                        $sql .= 'AND sr_id like "%' . $control_no[0] . '%" OR sr_number like "%' . $control_no[0] . '%" ';
                    }
                } else {
                    $sql .= 'AND sr_id like "%' . $this->control_no . '%" OR sr_number like "%' . $this->control_no . '%" ';
                }
            }

            if (!empty($this->assigned_mis)) {
                $sql .= 'AND sr_assigned_to like "%' . $this->assigned_mis . '%" ';
            }

            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->control_no)) {

            if (!empty($this->control_no)) {

                if (strpos($this->control_no, '-')) {

                    $control_no = explode("-", $this->control_no);

                    if (count($control_no) == 3) {
                        $sql = 'SELECT * FROM srvcrqst WHERE sr_process = "' . $control_no[0] . '" and sr_number = "' . $control_no[1] . '" and sr_year = "' . $control_no[2] . '" ';
                    } elseif (count($control_no) == 2) {
                        $sql = 'SELECT * FROM srvcrqst WHERE  sr_process = "' . $control_no[0] . '" and sr_number = "' . $control_no[1] . '" ';
                    } else {
                        $sql = 'SELECT * FROM srvcrqst WHERE sr_id like "%' . $control_no[0] . '%" and sr_number = "%' . $control_no[1] . '%" ';
                    }
                } else {
                    $sql = 'SELECT * FROM srvcrqst WHERE  sr_id like "%' . $this->control_no . '%" OR sr_number like "%' . $this->control_no . '%" ';
                }
            }

            if (!empty($this->assigned_mis)) {
                $sql .= 'AND sr_assigned_to like "%' . $this->assigned_mis . '%" ';
            }

            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->department)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_process like :sr_process AND sr_number like :sr_number AND sr_year like :sr_year ';


            if (!empty($this->assigned_mis)) {
                $sql .= 'AND sr_assigned_to like "%' . $this->assigned_mis . '%" ';
            }

            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->assigned_mis)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_assigned_to like "%' . $this->assigned_mis . '%" ';


            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->details)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_dtls like "%' . $this->details . '%" ';

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->pirf_no)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_prif like "%' . $this->pirf_no . '%" ';
        }



        $sql .= ' order by sr_id DESC LIMIT ' . $start . ', ' . $limit . '';


        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

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


    public function searchPage()
    {

        if (!empty($this->name)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_rqstr like "%' . $this->name . '%"';

            if (!empty($this->department)) {
                $sql .= 'AND sr_rqstr_dprtmnt like "%' . $this->name . '%" ';
            }

            if (!empty($this->control_no)) {

                if (strpos($this->control_no, '-')) {

                    $control_no = explode("-", $this->control_no);

                    if (count($control_no) == 3) {
                        $sql .= 'AND sr_process = "' . $control_no[0] . '" and sr_number ="' . $control_no[1] . '" and sr_year like "' . $control_no[2] . '" ';
                    } elseif (count($control_no) == 2) {
                        $sql .= 'AND sr_process = "' . $control_no[0] . '" and sr_number = "' . $control_no[1] . '" ';
                    } else {
                        $sql .= 'AND sr_id like "%' . $control_no[0] . '%" OR sr_number like "%' . $control_no[0] . '%" ';
                    }
                } else {
                    $sql .= 'AND sr_id like "%' . $this->control_no . '%" OR sr_number like "%' . $this->control_no . '%" ';
                }
            }

            if (!empty($this->assigned_mis)) {
                $sql .= 'AND sr_assigned_to like "%' . $this->assigned_mis . '%" ';
            }

            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->control_no)) {

            if (!empty($this->control_no)) {

                if (strpos($this->control_no, '-')) {

                    $control_no = explode("-", $this->control_no);

                    if (count($control_no) == 3) {
                        $sql = 'SELECT * FROM srvcrqst WHERE sr_process = "' . $control_no[0] . '" and sr_number = "' . $control_no[1] . '" and sr_year = "' . $control_no[2] . '" ';
                    } elseif (count($control_no) == 2) {
                        $sql = 'SELECT * FROM srvcrqst WHERE  sr_process = "' . $control_no[0] . '" and sr_number = "' . $control_no[1] . '" ';
                    } else {
                        $sql = 'SELECT * FROM srvcrqst WHERE sr_id like "%' . $control_no[0] . '%" and sr_number = "%' . $control_no[1] . '%" ';
                    }
                } else {
                    $sql = 'SELECT * FROM srvcrqst WHERE  sr_id like "%' . $this->control_no . '%" OR sr_number like "%' . $this->control_no . '%" ';
                }
            }

            if (!empty($this->assigned_mis)) {
                $sql .= 'AND sr_assigned_to like "%' . $this->assigned_mis . '%" ';
            }

            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->department)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_process like :sr_process AND sr_number like :sr_number AND sr_year like :sr_year ';


            if (!empty($this->assigned_mis)) {
                $sql .= 'AND sr_assigned_to like "%' . $this->assigned_mis . '%" ';
            }

            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->assigned_mis)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_assigned_to like "%' . $this->assigned_mis . '%" ';


            if (!empty($this->details)) {
                $sql .= 'AND sr_dtls like "%' . $this->details . '%" ';
            }

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->details)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_dtls like "%' . $this->details . '%" ';

            if (!empty($this->pirf_no)) {
                $sql .= 'AND sr_prif like "%' . $this->pirf_no . '%" ';
            }
        } elseif (!empty($this->pirf_no)) {

            $sql = 'SELECT * FROM srvcrqst WHERE sr_prif like "%' . $this->pirf_no . '%" ';
        }



        $sql .= ' order by sr_id DESC';


        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function transferData()
    {
        ini_set('max_execution_time', 5000);
        $sql = 'SELECT * FROM srvcrqst ';

        $db     = static::getDB3();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $count = $stmt->rowCount();
            $rows   = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for ($s = 0; $s < $count; $s++) {

                if (strpos($rows[$s]['prcss_no'], "-")) {

                    $prcss = explode("-", $rows[$s]['prcss_no']);

                    //echo "$prcss[0]-$prcss[1]-$prcss[2] ";

                    $ic_id      = $rows[$s]['ic_no'];
                    $sr_process = $prcss[0];
                    $sr_number  = $prcss[1];
                    $sr_year    = $prcss[2];

                    $sr_date_request = $rows[$s]['ic_crtd_date'];
                    $sr_date_set = $rows[$s]['ic_rqst_date'];
                    $sr_rqstr = $rows[$s]['ic_rqstr'];
                    $sr_rqstr_id = $rows[$s]['ic_rqstr_id'];
                    $sr_rqstr_dprtmnt = $rows[$s]['ic_rqstr_dprtmnt'];
                    $sr_site = $rows[$s]['ic_site'];
                    $sr_local = $rows[$s]['ic_local'];
                    $sr_mode = $rows[$s]['ic_mode'];
                    $sr_ipadd = $rows[$s]['ic_ipadd'];
                    $sr_rqst = $rows[$s]['ic_rqst'];
                    $sr_dtls = $rows[$s]['ic_dtls'];
                    $sr_status = $rows[$s]['ic_status'];
                    $sr_prepared = $rows[$s]['ic_prepared'];
                    $sr_checker = $rows[$s]['ic_checker'];
                    $sr_approver = $rows[$s]['ic_approver'];
                    $sr_received_by = $rows[$s]['received_by'];
                    $sr_received_date = $rows[$s]['received_date'];
                    $sr_assigned_to = $rows[$s]['assigned_to'];
                    $sr_assigned_date = $rows[$s]['assigned_date'];
                    $sr_acknowledged_date = $rows[$s]['acknowledged_date'];
                    $sr_done_date = $rows[$s]['done_date'];
                    $sr_user_approval = $rows[$s]['user_approval'];
                    $sr_mis_checker = $rows[$s]['checker'];
                    $sr_man_hour = $rows[$s]['man_hour'];
                    $sr_response = $rows[$s]['response'];
                    $sr_in_charge = $rows[$s]['in_charge'];

                    $sql = 'SELECT * FROM srvcrqst
                            WHERE sr_process = :sr_process
                            AND sr_number = :sr_number
                            AND sr_year = :sr_year';

                    $db     = static::getDB1();
                    $stmt   = $db->prepare($sql);

                    $stmt->bindValue(':sr_process', $sr_process);
                    $stmt->bindValue(':sr_number', $sr_number);
                    $stmt->bindValue(':sr_year', $sr_year);

                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        /*
                        $sql = 'UPDATE srvcrqst SET sr_status = :sr_status
                                WHERE sr_process = :sr_process
                                AND sr_number = :sr_number
                                AND sr_year = :sr_year';

                        $db     = static::getDB1();
                        $stmt   = $db->prepare($sql);

                        $stmt->bindValue(':sr_status', $sr_status);
                        $stmt->bindValue(':sr_process', $sr_process);
                        $stmt->bindValue(':sr_number', $sr_number);
                        $stmt->bindValue(':sr_year', $sr_year);

                        $stmt->execute();
                        */
                        //echo "Meron Na " . $sr_process . "-" . $sr_number . "-" . $sr_year . " <br>";
                    } else {
                        echo "Wala Pa " . $sr_process . "-" . $sr_number . "-" . $sr_year;

                        $sql = 'INSERT INTO srvcrqst (sr_process, sr_number, sr_year, sr_date_request, sr_date_set, sr_rqstr, sr_rqstr_id, sr_rqstr_dprtmnt, sr_site, sr_local, sr_mode, sr_ipadd, sr_rqst, sr_dtls, sr_status, sr_prepared, sr_checker, sr_approver, sr_received_by, sr_received_date, sr_assigned_to, sr_assigned_date, sr_acknowledged_date, sr_done_date, sr_user_approval, sr_mis_checker, sr_man_hour, sr_response, sr_in_charge)
                    VALUES(:sr_process, :sr_number, :sr_year, :sr_date_request, :sr_date_set, :sr_rqstr, :sr_rqstr_id, :sr_rqstr_dprtmnt, :sr_site, :sr_local, :sr_mode, :sr_ipadd, :sr_rqst, :sr_dtls, :sr_status, :sr_prepared, :sr_checker, :sr_approver, :sr_received_by, :sr_received_date, :sr_assigned_to, :sr_assigned_date, :sr_acknowledged_date, :sr_done_date, :sr_user_approval, :sr_mis_checker, :sr_man_hour, :sr_response, :sr_in_charge) ';

                        $db     = static::getDB1();
                        $stmt   = $db->prepare($sql);

                        $stmt->bindValue(':sr_process', $sr_process);
                        $stmt->bindValue(':sr_number', $sr_number);
                        $stmt->bindValue(':sr_year', $sr_year);
                        $stmt->bindValue(':sr_date_request', $sr_date_request);
                        $stmt->bindValue(':sr_date_set', $sr_date_set);
                        $stmt->bindValue(':sr_rqstr', $sr_rqstr);
                        $stmt->bindValue(':sr_rqstr_id', $sr_rqstr_id);
                        $stmt->bindValue(':sr_rqstr_dprtmnt', $sr_rqstr_dprtmnt);
                        $stmt->bindValue(':sr_site', $sr_site);
                        $stmt->bindValue(':sr_local', $sr_local);

                        $stmt->bindValue(':sr_mode', $sr_mode);
                        $stmt->bindValue(':sr_ipadd', $sr_ipadd);
                        $stmt->bindValue(':sr_rqst', $sr_rqst);
                        $stmt->bindValue(':sr_dtls', $sr_dtls);
                        $stmt->bindValue(':sr_status', $sr_status);
                        $stmt->bindValue(':sr_prepared', $sr_prepared);
                        $stmt->bindValue(':sr_checker', $sr_checker);
                        $stmt->bindValue(':sr_approver', $sr_approver);
                        $stmt->bindValue(':sr_received_by', $sr_received_by);
                        $stmt->bindValue(':sr_received_date', $sr_received_date);

                        $stmt->bindValue(':sr_assigned_to', $sr_assigned_to);
                        $stmt->bindValue(':sr_assigned_date', $sr_assigned_date);
                        $stmt->bindValue(':sr_acknowledged_date', $sr_acknowledged_date);
                        $stmt->bindValue(':sr_done_date', $sr_done_date);
                        $stmt->bindValue(':sr_user_approval', $sr_user_approval);
                        $stmt->bindValue(':sr_mis_checker', $sr_mis_checker);
                        $stmt->bindValue(':sr_man_hour', $sr_man_hour);
                        $stmt->bindValue(':sr_response', $sr_response);
                        $stmt->bindValue(':sr_in_charge', $sr_in_charge);

                        $stmt->execute();

                        $sr_id = $db->lastInsertId();

                        echo $sr_id . " on process registratrion <br>";

                        $sql = 'SELECT * FROM wrklg where ic_id = :ic_id';

                        $db     = static::getDB3();
                        $stmt1   = $db->prepare($sql);

                        $stmt1->bindValue(':ic_id', $ic_id);

                        $stmt1->execute();

                        if ($stmt1->rowCount() > 0) {
                            $count_wrklg    = $stmt1->rowCount();
                            $row_log        = $stmt1->fetchAll(PDO::FETCH_ASSOC);

                            for ($a = 0; $a < $count_wrklg; $a++) {

                                $wrklg_date         = $row_log[$a]['wrklg_date'];
                                $wrklg_status       = $row_log[$a]['wrklg_status'];
                                $wrklg_dtls         = $row_log[$a]['wrklg_dtls'];
                                $wrklg_personnel    = $row_log[$a]['wrklg_personnel'];
                                $endorse_to         = $row_log[$a]['endorse_to'];

                                $sql = 'INSERT INTO srvcrqst_wrklg(sr_id, wrklg_date, wrklg_status, wrklg_dtls, wrklg_personnel, endorse_to)
                                VALUES(:sr_id, :wrklg_date, :wrklg_status, :wrklg_dtls, :wrklg_personnel, :endorse_to)';

                                $db     = static::getDB1();
                                $stmt2  = $db->prepare($sql);

                                $stmt2->bindValue(':sr_id', $sr_id);
                                $stmt2->bindValue(':wrklg_date', $wrklg_date);
                                $stmt2->bindValue(':wrklg_status', $wrklg_status);
                                $stmt2->bindValue(':wrklg_dtls', $wrklg_dtls);
                                $stmt2->bindValue(':wrklg_personnel', $wrklg_personnel);
                                $stmt2->bindValue(':endorse_to', $endorse_to);

                                $stmt2->execute();
                            }
                            echo " $count_wrklg <br>";
                        } else {

                            echo "<br>";
                        }
                    }
                    /*
                    
                    */
                }
            }
        }
    }

    public static function getManHour($sr_id)
    {

        $sql = 'SELECT sr_process, sr_date_set, sr_done_date, sr_acknowledged_date 
                FROM srvcrqst
                WHERE sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $sr_id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['sr_process'] == "CSR") {

            $start_date = $row['sr_date_set'];
            $done_date  = $row['sr_done_date'];
        }

        if ($row['sr_process'] == "CPR") {

            $start_date = $row['sr_date_set'];
            $done_date  = $row['sr_done_date'];
        }

        if ($row['sr_process'] == "DRR") {

            $start_date = $row['sr_date_set'];
            $done_date  = $row['sr_done_date'];
        }

        $manhour = self::computeManHour($start_date, $done_date);

        if ($manhour < 0) {

            $start_date = $row['sr_acknowledged_date'];
            $done_date  = $row['sr_done_date'];

            $manhour = self::computeManHour($start_date, $done_date);
        }

        return $manhour;
    }


    public static function manHour($sr_id)
    {
        $sql = 'SELECT SUM(wrklg_hours) from srvcrqst_wrklg where sr_id = :sr_id';

        $db     = static::getDB1();
        $stmt  = $db->prepare($sql);

        $stmt->bindValue(':sr_id', $sr_id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return  $row['SUM(wrklg_hours)'];
    }


    public static function computeManHour($start_date, $done_date)
    {
        $minutes = 3600;

        $datetime1 = $start_date;
        $datetime2 = $done_date;
        $date_create1 = date_create($datetime1);
        $date_create2 = date_create($datetime2);
        $interval = ($date_create2->getTimestamp() - $date_create1->getTimestamp()) / $minutes;
        //echo substr($datetime1, 0,10);
        //echo $interval->format(' %h.%i');
        $difference = $interval;
        //echo $difference." ";
        if (substr($datetime1, 0, 10) != substr($datetime2, 0, 10)) {
            // code...
            $start = new DateTime(substr($datetime1, 0, 10));
            $end = new DateTime(substr($datetime2, 0, 10));
            //Define our holidays
            //New Years Day
            $holidays = array(
                '2017-08-21',
                '2017-08-27',
                '2017-11-01',
                '2017-11-02',
                '2017-11-30',
                '2017-12-25',
                '2017-12-26',
                '2017-12-27',
                '2017-12-28',
                '2017-12-29',
                '2018-01-01',
                '2018-02-16',
                '2018-03-29',
                '2018-03-30',
                '2018-04-09',
                '2018-05-01',
                '2018-06-12',
                '2018-08-21',
                '2018-08-27',
                '2018-11-01',
                '2018-11-02',
                '2018-11-30',
                '2018-12-24',
                '2018-12-25',
                '2018-12-26',
                '2018-12-27',
                '2018-12-28',
                '2018-12-29',
                '2018-12-30',
                '2018-12-31',
                '2019-01-01',
                '2019-02-05',
                '2019-02-25',
                '2019-04-09',
                '2019-04-18',
                '2019-04-19',
                '2019-05-01',
                '2019-06-12',
                '2019-08-21',
                '2019-08-26',
                '2019-11-01',
                '2019-12-23',
                '2019-12-24',
                '2019-12-25',
                '2019-12-26',
                '2019-12-27',
                '2019-12-30',
                '2019-12-31',
            );
            //Create a DatePeriod with date intervals of 1 day between start and end dates

            $interval = new DateInterval('P1D');

            $period = new DatePeriod($start, $interval, $end);

            //Holds valid DateTime objects of valid dates
            $days = array();
            $offdays = array();
            //iterate over the period by day
            foreach ($period as $day) {
                //print_r($day);
                //If the day of the week is not a weekend
                $dayOfWeek = $day->format('N');

                if ($dayOfWeek < 6) {
                    //If the day of the week is not a pre-defined holiday
                    $format = $day->format('Y-m-d');
                    if (false === in_array($format, $holidays)) {
                        //Add the valid day to our days array
                        //This could also just be a counter if that is all that is necessary
                        //echo " ".$dayOfWeek."<br>";
                        $days[] = $day;
                    } else {
                        $offdays[] = $day;
                    }
                } else {
                    $format = $day->format('Y-m-d');
                    $offdays[] = $day;
                }
            }
            $rest_multiplier = count($days);
            //echo $rest_multiplier."<br>";
            $off_count = count($offdays);
            //echo $off_count."<br>";
            $rest = 9; // 9hrs off Work
            //$difference = $rest_multiplier * 24;
            $overall_rest = ($rest_multiplier * $rest) + ($off_count * 24);
            /*
                        $hrs_per_day = (24 * $rest_multiplier);
                        $total_hours  = (($hrs_per_day + $difference) - $overall_rest);
                        */
            $total_hours  = ($difference - $overall_rest);
            //echo $total_hours;
            //$hour = (0.0625 * $total_hours);
            //echo number_format((float)$hour, 2, '.', '');
            $hour   =   $total_hours;
        } else {
            //$hour = (0.0625 * $difference);
            //echo number_format((float)$hour, 2, '.', '');
            $hour   =   $difference;
        }

        //echo number_format((float)$hour, 2, '.', '')."<br>";
        $manhour = (number_format((float) $hour, 2, '.', ''));

        return $manhour;
    }
}
