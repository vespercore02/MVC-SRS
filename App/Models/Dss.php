<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class Dss extends \Core\Model
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

    public function save($name, $id, $department, $reference, $docu_path)
    {
        
        $table = "ds_" . date("Y");
        $sql = 'INSERT INTO ' . $table . "(ds_no)VALUES('')";

        $db = static::getDB4();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $ds_id   =  $db->lastInsertId();
        $autoinc = sprintf("%05d", $ds_id);
        $ds_no   = "DS-" . $autoinc . date("-" . 'y');

        $sql = "UPDATE $table set ds_no = :ds_no  where ds_id = :ds_id";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':ds_id', $ds_id);
        $stmt->bindValue(':ds_no', $ds_no);

        $stmt->execute();

        $sql = "INSERT INTO ds_info (ds_crtr, ds_crtr_id, ds_crtr_dept, ds_no, ds_crtd_date, ds_ip_address, ds_docu_name, ds_file_validation, ds_file_path)
        Values(:ds_crtr, :ds_crtr_id, :ds_crtr_dept, :ds_no, :ds_crtd_date, :ds_ip_address, :ds_docu_name, :ds_file_validation, :ds_file_path)";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':ds_crtr', $name, PDO::PARAM_STR);
        $stmt->bindValue(':ds_crtr_id', $id, PDO::PARAM_STR);
        $stmt->bindValue(':ds_crtr_dept', $department, PDO::PARAM_STR);
        $stmt->bindValue(':ds_no', $ds_no, PDO::PARAM_STR);
        $stmt->bindValue(':ds_crtd_date', date("Y-m-d H:i:s"));
        $stmt->bindValue(':ds_ip_address', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $stmt->bindValue(':ds_docu_name', $reference);
        $stmt->bindValue(':ds_file_validation', "Valid", PDO::PARAM_STR);
        $stmt->bindValue(':ds_file_path', $docu_path, PDO::PARAM_STR);
        $stmt->execute();

        return $ds_no;
    }

    public static function loadSignature($id, $name)
    {

        $sql = 'SELECT * FROM ds_info WHERE ds_docu_name=:ds_docu_name AND ds_crtr = :ds_crtr ';

        $db = static::getDB4();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':ds_docu_name', $id);
        $stmt->bindValue(':ds_crtr', $name);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    public static function loadStamp($id, $name)
    {
        $sql = 'SELECT * FROM ds_info WHERE ds_docu_name=:ds_docu_name AND ds_crtr = :ds_crtr ';

        $db = static::getDB4();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':ds_docu_name', $id);
        $stmt->bindValue(':ds_crtr', $name);

        $stmt->execute();

        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {
            $dept   = $results['ds_crtr_dept'];
            $date   = $results['ds_crtd_date'];
            $name   = self::stampName($results['ds_crtr']);
            $ds_no  = $results['ds_no'];

            return array('ds_crtr_dept' => $dept, 'ds_crtd_date' => $date, 'ds_crtr' => $name, 'ds_no' => $ds_no,);
        }
    }

    public static function stampName($ds_name)
    {

        if (strpos($ds_name, '.') !== false) {
            // code...
            $name       = explode(".", $ds_name);
        } else {

            if (strpos($ds_name, '  ') !== false) {
                $name       = explode("  ", $ds_name);
            } else {
                $name       = explode(" ", $ds_name);
            }
        }

        //var_dump($name);
        //echo $name[1]."<br>";
        if (count($name) > 2) {
            // code...
            $l_name = $name[2];
        } else {

            $l_name = $name[1];
        }

        return $ds_name[0] . "." . $l_name;
    }
}
