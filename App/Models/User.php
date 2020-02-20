<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
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
     * Authenticate a user by login and password.
     *
     * @param string $username windows login
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($username, $password)
    {
        $adServer = "ldap://petsvr1100.petcad1100:389";

        $ldaphost = "petsvr1100.petcad1100";  // your ldap servers
        $ldapport = 389;                 // your ldap server's port number


        $ldap = ldap_connect($adServer) or die("Could not connect to $ldaphost");
        $ldaprdn = 'petcad1100' . "\\" . $username;

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        if ($ldap) {
            $bind = @ldap_bind($ldap, $ldaprdn, $password);

            if ($bind) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        //SELECT emp_info.full_name, emp_srs.pet_id, GROUP_CONCAT(emp_srs.role) as role FROM emp_srs left join emp_info on emp_srs.pet_id = emp_info.pet_id WHERE emp_srs.pet_id =:pet_id;
        //$sql = 'SELECT * FROM emp_info WHERE pet_id = :pet_id';

        $sql = 'SELECT emp_info.full_name, emp_info.department, emp_srs.pet_id, GROUP_CONCAT(emp_srs.role) as role 
                FROM emp_srs left join emp_info on emp_srs.pet_id = emp_info.pet_id 
                WHERE emp_srs.pet_id =:pet_id';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);
        $stmt->bindValue(':pet_id', $id, PDO::PARAM_STR);

        //$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        //$expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB1();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        //$stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * User Role data
     * 
     */
    public function getroledata()
    {
        $return_arr1 = array();

        $full_name  = $this->filter_name;
        $limit  = 10;
        $start  = ($this->id - 1) * $limit;

        $sql    = 'SELECT emp_info.full_name, emp_srs.role, emp_srs.department 
                    FROM emp_info 
                    left join emp_srs on emp_info.pet_id = emp_srs.pet_id 
                    where emp_info.full_name like :full_name LIMIT ' . $start . ', ' . $limit . '';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {


            $count = $stmt->rowCount();
            $rows   = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for ($s = 0; $s < $count; $s++) {
                # code...
                $return_arr1[] = array($rows[$s]['full_name'], $rows[$s]['role'], $rows[$s]['department']);
            }


            //$return_arr1[] = $stmt->rowCount();

            return $return_arr1;
        } else {

            //$return_arr1[] = "<tr><td></td><td>Not found</td></tr>";
            return false;
        }
    }

    /**
     * User Role data page
     * 
     */
    public function getroledatapage()
    {

        if (isset($this->filter_name)) {
            $full_name  = $this->filter_name;
        } else {
            $full_name  = "";
        }

        $sql    = 'SELECT emp_info.full_name, emp_srs.role, emp_srs.department 
                    FROM emp_info left join emp_srs on emp_info.pet_id = emp_srs.pet_id 
                    where emp_info.full_name like :full_name ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * GET Department list
     * 
     */
    public static function getDepartmentList()
    {
        $sql    = 'SELECT * FROM emp_department_list ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * GET Role list
     * 
     * @return  mixed all Role list
     */
    public static function getRoleList()
    {
        $sql    = 'SELECT * FROM srvcrqst_roles ';

        $db     = static::getDB1();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET Employee list
     * 
     * @return  mixed all Role list
     */
    public function getEmployeeList()
    {
        $sql    = 'SELECT full_name, pet_id FROM emp_info where full_name like :full_name';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $this->term . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add Employee Role
     * 
     */
    public function addEmployeeRole()
    {
        if ($this->searchEmployeeRole() == 0) {


            $sql = 'INSERT INTO emp_srs (pet_id, role, department)VALUES(:pet_id, :role, :department)';

            $db     = static::getDB2();
            $stmt   = $db->prepare($sql);

            $stmt->bindValue(':pet_id', $this->emp_name, PDO::PARAM_STR);
            $stmt->bindValue(':role', $this->emp_role, PDO::PARAM_STR);
            $stmt->bindValue(':department', $this->emp_dept, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Search Employee Role
     * 
     */
    public function searchEmployeeRole()
    {
        $sql = 'SELECT * FROM emp_srs WHERE pet_id=:pet_id and role=:role and department=:department';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $this->emp_name, PDO::PARAM_STR);
        $stmt->bindValue(':role', $this->emp_role, PDO::PARAM_STR);
        $stmt->bindValue(':department', $this->emp_dept, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Search Employee Role
     * 
     */
    public function checkEmployeeRepresentative()
    {
        $sql = 'SELECT * FROM emp_srs WHERE pet_id=:pet_id and role="Representative"';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $this->emp_name, PDO::PARAM_STR);
        $stmt->bindValue(':role', $this->emp_role, PDO::PARAM_STR);
        $stmt->bindValue(':department', $this->emp_dept, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Get Department Endorse list by Role
     *
     * @return void
     */
    public static function getEndorseList($department, $role)
    {

        $sql = 'SELECT emp_info.full_name, emp_srs.role, emp_srs.department 
        FROM emp_info left join emp_srs on emp_info.pet_id = emp_srs.pet_id 
        where emp_srs.department=:department and emp_srs.role in (:role) ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':department', $department, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get MIS Endorse list by Role
     *
     * @return void
     */
    public static function getMISEndorseList($department)
    {

        $sql = 'SELECT emp_info.full_name, emp_srs.role, emp_srs.department 
        FROM emp_info left join emp_srs on emp_info.pet_id = emp_srs.pet_id 
        where emp_srs.department=:department and emp_info.full_name like :full_name ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%admin%', PDO::PARAM_STR);
        $stmt->bindValue(':department', $department, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getEmail($full_name)
    {

        if ($full_name == "MIS") {

            return array('email_account' => "smb_mis.pet@ph.yazaki.com", 'full_name' => "MIS Service Desk");
        }

        $sql = 'SELECT emp_email.email_account, emp_info.pet_id, emp_info.full_name 
                FROM emp_info LEFT join emp_email on emp_info.pet_id = emp_email.email_pet_id
                WHERE emp_info.full_name = :full_name';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', $full_name, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $rows = $stmt->fetch(PDO::FETCH_ASSOC);

            return $rows;
        }
    }

    /**
     * GET Employee list
     * 
     * @return  mixed all Role list
     */
    public function getEmpList()
    {
        $sql    = 'SELECT full_name FROM emp_info ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET Email list
     * 
     * @return  mixed all Role list
     */
    public function getEmailList()
    {
        if (isset($this->filter_name)) {
            $full_name  = $this->filter_name;
        } else {
            $full_name  = "";
        }

        $sql    = 'SELECT emp_info.pet_id, emp_info.full_name, emp_email.email_account FROM emp_email 
                    LEFT JOIN emp_info on emp_email.email_pet_id = emp_info.pet_id
                    WHERE emp_info.pet_id is not Null and
                    emp_info.full_name like :full_name';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET Email Data
     * 
     * @return  mixed all Role list
     */
    public function getEmaildata()
    {
        $full_name  = $this->filter_name;

        $limit  = 10;
        $start  = ($this->id - 1) * $limit;

        $sql    = 'SELECT emp_info.pet_id, emp_info.full_name, emp_email.email_account FROM emp_email 
                    LEFT JOIN emp_info on emp_email.email_pet_id = emp_info.pet_id
                    WHERE emp_info.pet_id is not Null and
                    emp_info.full_name like :full_name
                    LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET Cmms list
     * 
     * @return  mixed all Role list
     */
    public function getCmmsList()
    {
        if (isset($this->filter_name)) {
            $full_name  = $this->filter_name;
        } else {
            $full_name  = "";
        }

        $sql    = 'SELECT emp_info.pet_id, emp_info.full_name, emp_cmms.cmms_pet_id FROM emp_cmms
                LEFT JOIN emp_info on emp_cmms.cmms_pet_id = emp_info.pet_id
                    WHERE emp_info.pet_id is not Null and
                    emp_info.full_name like :full_name';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET Cmms Data
     * 
     * @return  mixed all Role list
     */
    public function getCmmsData()
    {
        $full_name  = $this->filter_name;

        $limit  = 10;
        $start  = ($this->id - 1) * $limit;

        $sql    = 'SELECT emp_info.pet_id, emp_info.full_name, emp_cmms.cmms_pet_id FROM emp_cmms
                LEFT JOIN emp_info on emp_cmms.cmms_pet_id = emp_info.pet_id
                    WHERE emp_info.pet_id is not Null and
                    emp_info.full_name like :full_name
                    LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET seine2 list
     * 
     * @return  mixed all Role list
     */
    public function getSeine2List()
    {
        if (isset($this->filter_name)) {
            $full_name  = $this->filter_name;
        } else {
            $full_name  = "";
        }

        $sql    = 'SELECT emp_info.pet_id, emp_info.full_name, emp_seine2.seine2_pet_id FROM emp_seine2
                    LEFT JOIN emp_info on emp_seine2.seine2_pet_id = emp_info.pet_id
                    WHERE emp_info.pet_id is not Null  and
                    emp_info.full_name like :full_name';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * GET seine2 Data
     * 
     * @return  mixed all Role list
     */
    public function getSeine2Data()
    {
        $full_name  = $this->filter_name;

        $limit  = 10;
        $start  = ($this->id - 1) * $limit;

        $sql    = 'SELECT emp_info.pet_id, emp_info.full_name, emp_seine2.seine2_pet_id FROM emp_seine2
                    LEFT JOIN emp_info on emp_seine2.seine2_pet_id = emp_info.pet_id
                    WHERE emp_info.pet_id is not Null and
                    emp_info.full_name like :full_name
                    LIMIT ' . $start . ', ' . $limit . ' ';

        $db     = static::getDB2();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':full_name', '%' . $full_name . '%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 
     * Check Account on System
     * 
     */

    public static function checkWindows($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'SELECT * FROM emp_info WHERE pet_id = :pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function checkEmail($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'SELECT * FROM emp_email WHERE email_pet_id = :pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function checkSeine($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'SELECT * FROM emp_seine2 WHERE seine2_pet_id = :pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public static function checkCmms($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'SELECT * FROM emp_cmms WHERE cmms_pet_id = :pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * 
     * INSERT System Account
     * 
     */

    public static function addWindows($id, $last_name, $first_name, $middle_initial, $department, $branch)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $full_name = $first_name . " " . $middle_initial . ". " . $last_name;

        $sql = 'INSERT INTO emp_info (pet_id, last_name, first_name, middle_initial, full_name, department, branch, date_registered)
                VALUES (:pet_id, :last_name, :first_name, :middle_initial, :full_name, :department, :branch, :date_registered)';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindValue(':middle_initial', $middle_initial, PDO::PARAM_STR);
        $stmt->bindValue(':full_name', $full_name, PDO::PARAM_STR);
        $stmt->bindValue(':branch', $branch, PDO::PARAM_STR);
        $stmt->bindValue(':department', $department, PDO::PARAM_STR);
        $stmt->bindValue(':date_registered', date('Y-m-d'));

        return $stmt->execute();
    }

    public static function addEmail($id, $last_name, $first_name, $middle_initial, $department)
    {

        $email_address = $first_name . "." . $last_name . "@ph.yazaki.com";

        $sql = 'INSERT INTO emp_email (email_pet_id, email_account, date_registered)
        VALUES (:email_pet_id, :email_account, :date_registered)';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':email_pet_id', "pet" . sprintf('%04d', $id), PDO::PARAM_STR);
        $stmt->bindValue(':email_account', $email_address, PDO::PARAM_STR);

        $stmt->bindValue(':date_registered', date('Y-m-d'), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function addSeine($id, $last_name, $first_name, $middle_initial, $department)
    {
        $sql = 'INSERT INTO emp_seine2 (seine2_pet_id, seine2_status, date_registered)
        VALUES (:seine2_pet_id, :seine2_status, :date_registered)';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':seine2_pet_id', "pet" . sprintf('%04d', $id), PDO::PARAM_STR);
        $stmt->bindValue(':seine2_status', "Active", PDO::PARAM_STR);

        $stmt->bindValue(':date_registered', date('Y-m-d'), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function addCmms($id, $last_name, $first_name, $middle_initial, $department)
    {
        $sql = 'INSERT INTO emp_cmms (cmms_pet_id, cmms_status, date_registered)
        VALUES (:cmms_pet_id, :cmms_status, :date_registered)';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':cmms_pet_id', "pet" . sprintf('%04d', $id), PDO::PARAM_STR);
        $stmt->bindValue(':cmms_status', "Active", PDO::PARAM_STR);

        $stmt->bindValue(':date_registered', date('Y-m-d'), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * 
     * Delete System Account
     * 
     */

    public static function deleteWindows($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'DELETE FROM emp_info WHERE pet_id = :pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':pet_id', $pet_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function deleteEmail($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'DELETE FROM emp_email WHERE email_pet_id = :email_pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':email_pet_id', $pet_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function deleteSeine($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'DELETE FROM emp_seine2 WHERE seine2_pet_id = :seine2_pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':seine2_pet_id', $pet_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function deleteCmms($id)
    {
        $pet_id = "pet" . sprintf('%04d', $id);

        $sql = 'DELETE FROM emp_cmms WHERE cmms_pet_id = :cmms_pet_id';

        $db     = static::getDB5();
        $stmt   = $db->prepare($sql);

        $stmt->bindValue(':cmms_pet_id', $pet_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
