<?php


namespace App\Controllers;

ini_set('max_execution_time', 300); //300 seconds = 5 minutes

use \Core\View;
use \App\Models\Daily;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use \App\Mail;


class Dailys
{

    public function memberReport()
    {
        $members = Daily::getMembers();
        $checker = Daily::getChecker();

        echo "<pre>";
        print_r($members);
        echo "</pre>";

        foreach ($members as $member_info) {
            # code...
            $pending = Daily::getReport($member_info['full_name']);

            //echo $member_info['full_name'] . " " . count($pending);

            //echo "<pre>";
            //print_r($pending);
            //echo "</pre>";

            if (count($pending) > 0) {
                $report = self::GenerateReport($member_info['full_name'], $pending);

                $checker_email = array();
                foreach ($checker as $checker_info) {
                    # code...
                    $checker_email[] = $checker_info['email_account'];
                }

                echo "<pre>";
                print_r($checker_email);
                echo "</pre>";

                $subject = "[SRS] " . $member_info['full_name'] . " Pending for " . date("Y-m-d H:i");

                $message = "Hello " . $member_info['first_name']  . ",<br>
                            <br>
                            Please see attached file for the list of your pending assigned request as of " . date("Y-m-d H:i") . "<br>
                            <br>Thank you,<br>
                        <b>S</b>ervice <b>R</b>equest <b>S</b>ystem";
                        
                //echo $report . "<br>";

                Mail::send("willem.leonardo@ph.yazaki.com", "", $subject, $message, $_SERVER['DOCUMENT_ROOT'].$report);
            }
        }
    }

    public static function GenerateReport($name, $data)
    {

        $objPHPExcel = new Spreadsheet();

        $row = 1;
        $col = 1;

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
        $center = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];
        $style = [
            'font' => [
                'size' => 12,
                'bold' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'startColor' => [
                    'argb' => 'FFFF66',
                ],
                'endColor' => [
                    'argb' => 'FFFF66',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
        ];

        $bold = array('font' => array('size' => 12, 'bold' => true));

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($bold);

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $name . " Delayed Request");
        $row++;
        $row++;

        $objPHPExcel->getActiveSheet()->getStyle("A$row:K$row")->applyFromArray($style);
        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Reference #");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Process");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Requestor");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Date Request");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Date Needed");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Date Done");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Site");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Category");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Details");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Status");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Assigned to");
        $row++;

        foreach ($data as $data_info) {
            # code...
            $col = 1;
            $objPHPExcel->getActiveSheet()->getStyle("A$row:E$row")->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_process'] . "-" . sprintf("%04d", $data_info['sr_number']) . "-" . $data_info['sr_year']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_process']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_rqstr']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_date_request']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_date_set']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_done_date']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_site']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_rqst']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_dtls']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_status']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data_info['sr_assigned_to']);
            $col++;
            $row++;
        }

        $objWriter = new Xlsx($objPHPExcel);

        $file = "../public/generated report/" . $name . " Pending for " . date('Y-m-d') . ".php";
        $d_file = "/generated report/" . $name . " Pending for " . date('Y-m-d') . ".php";

        $filename = str_replace('.php', '.xlsx', $file);
        $d_file = str_replace('.php', '.xlsx', $d_file);
        if (file_exists($filename)) {
            unlink($filename) or die("Couldn't delete file");
            // unlink($d_file) or die("Couldn't delete file");
        }
        clearstatcache();


        $objWriter->save(str_replace('.php', '.xlsx', $file));

        //return "generated report/report-".$site." ".$month." ".$year.".xlsx";
        //echo $file;
        return $d_file;
    }
}
