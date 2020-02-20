<?php 

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Report as Reports;
use \App\Models\RequestPool;
use \App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use \App\Flash;

class Report extends Authenticated
{

    public function indexAction(){

        if (!empty($_POST)) {
            print_r($_POST);
        } else {
            
        View::renderTemplate('report/report.html');
        }

    }

    public function getIndirect()
    {

        if (!empty($_POST)) {

            $site = $_POST['site'];

            if ($_POST['month'] == "12-16") {

                $pre  =   $_POST['year'] - 1;
                $_POST['date1'] = $pre."-".$_POST['month'];

                $date1 = $_POST['date1'];
                   
            }else {
    
                $year = $_POST['year'];
                $_POST['date1'] = $year."-".$_POST['month'];

                $date1 = $_POST['date1'];
                
            }
    
            $_POST['date2'] = $_POST['year']."-".date("m-d", strtotime($_POST['date1'].' +1 months -1Day'));
    
            $date2 = $_POST['date2'];

            $month = date("F",strtotime($_POST['date2']));

            $year   = $_POST['year'];   
            
            $cpr_category_list = Reports::getMonthlySiteRequestListAndManhour('CPR', $_POST['site'],$_POST['date1'],$_POST['date2'], 50);
            /*
            echo "<pre>";
            print_r($cpr_category_list);
            echo "</pre>";
            */
            View::renderTemplate('report/report.html',[
                'indirect_month' => $month,
                'cpr_list' => $cpr_category_list,
                'year' => $year,
                'site' => $site,
                'date1' => $date1,
                'date2' => $date2
            ]);
        
        }else {
            
            Flash::addMessage('Please input Month, Year and Site.', "warning");

            $this->redirect('/report/index');
        }
        
    }

    public function getReport(){

        if (!empty($_POST)) {

            
            $site = $_POST['site'];

            if ($_POST['month'] == "12-16") {

                $pre  =   $_POST['year'] - 1;
                $_POST['date1'] = $pre."-".$_POST['month'];

                $date1 = $_POST['date1'];
                   
            }else {
    
                $year = $_POST['year'];
                $_POST['date1'] = $year."-".$_POST['month'];

                $date1 = $_POST['date1'];
                
            }
    
            $_POST['date2'] = $_POST['year']."-".date("m-d", strtotime($_POST['date1'].' +1 months -1Day'));
    
            $date2 = $_POST['date2'];

            $month = date("F",strtotime($_POST['date2']));

            $year   = $_POST['year'];   
            
            $monthlyRequest = Reports::getMonthlySiteRequest($_POST['site'],$_POST['date1'],$_POST['date2']);
            $members        = Reports::getMonthlySiteMembers($_POST['site'],$_POST['date1'],$_POST['date2']);
            
            if ($members == 0) {
                Flash::addMessage('No Data for '.$month." yet.", "warning");

                $this->redirect('/report/index');
            }

            $member_report = array();
            $members_report = array();

            foreach ($members as $key => $value) {
                # code...
                $name = $value['sr_assigned_to'];
                $assigned = Reports::getMonthlySiteMembersStatus($members[$key]['sr_assigned_to'], "Assigned", $_POST['site'],$_POST['date1'],$_POST['date2']);
                $workinprogress = Reports::getMonthlySiteMembersStatus($members[$key]['sr_assigned_to'], "Work in Progress", $_POST['site'],$_POST['date1'],$_POST['date2']);
                $done = Reports::getMonthlySiteMembersStatus($members[$key]['sr_assigned_to'], "Done", $_POST['site'],$_POST['date1'],$_POST['date2']);
                $closed = Reports::getMonthlySiteMembersStatus($members[$key]['sr_assigned_to'], "Closed", $_POST['site'],$_POST['date1'],$_POST['date2']);

                $members_report[] = array('name' => $name, 
                                            'assigned' => $assigned, 
                                            'workinprogress' => $workinprogress,
                                            'done' => $done,
                                            'closed' => $closed);
            }

            $month_nr       = Reports::getMonthlySiteStatus('New Request', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $month_assigned = Reports::getMonthlySiteStatus('Assigned', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $month_wip      = Reports::getMonthlySiteStatus('Work in Progress', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $month_done     = Reports::getMonthlySiteStatus('Done', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $month_closed   = Reports::getMonthlySiteStatus('Closed', $_POST['site'],$_POST['date1'],$_POST['date2'] );

            $csr_newrequest = Reports::getMonthlySiteStatusProcess('New Request','CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_assigned   = Reports::getMonthlySiteStatusProcess('Assigned','CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_wip        = Reports::getMonthlySiteStatusProcess('Work in Progress','CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_done       = Reports::getMonthlySiteStatusProcess('Done','CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_closed     = Reports::getMonthlySiteStatusProcess('Closed','CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_ongoing    = Reports::getMonthlySiteProcessOngoingCSR('CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_delay      = Reports::getMonthlySiteProcessDelay('CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $csr_total      = Reports::getMonthlySiteProcessTotal('CSR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            
            $csr_completion_result = ((($csr_done + $csr_closed) / $csr_total) * 100);

            $csr_completion_percentage   = (number_format((float) $csr_completion_result, 2, '.', ''));

            $csr_ontime_result = (($csr_done + $csr_closed) - $csr_delay) / ($csr_done + $csr_closed) * 100;

            $csr_ontime_percentage = (number_format((float) $csr_ontime_result, 2, '.', ''));

            $cpr_newrequest = Reports::getMonthlySiteStatusProcess('New Request','CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_assigned   = Reports::getMonthlySiteStatusProcess('Assigned','CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_wip        = Reports::getMonthlySiteStatusProcess('Work in Progress','CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_done       = Reports::getMonthlySiteStatusProcess('Done','CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_closed     = Reports::getMonthlySiteStatusProcess('Closed','CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_delay      = Reports::getMonthlySiteProcessDelay('CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_ongoing    = Reports::getMonthlySiteProcessOngoing('CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $cpr_total      = Reports::getMonthlySiteProcessTotal('CPR', $_POST['site'],$_POST['date1'],$_POST['date2'] );


            $drr_newrequest = Reports::getMonthlySiteStatusProcess('New Request','DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_assigned   = Reports::getMonthlySiteStatusProcess('Assigned','DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_wip        = Reports::getMonthlySiteStatusProcess('Work in Progress','DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_done       = Reports::getMonthlySiteStatusProcess('Done','DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_closed     = Reports::getMonthlySiteStatusProcess('Closed','DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_delay      = Reports::getMonthlySiteProcessDelay('DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_ongoing    = Reports::getMonthlySiteProcessOngoing('DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );
            $drr_total      = Reports::getMonthlySiteProcessTotal('DRR', $_POST['site'],$_POST['date1'],$_POST['date2'] );

            $csr_category_list = Reports::getMonthlySiteRequestList('CSR', $_POST['site'],$_POST['date1'],$_POST['date2'], 5);
            $cpr_category_list = Reports::getMonthlySiteRequestList('CPR', $_POST['site'],$_POST['date1'],$_POST['date2'], 5);
            $drr_category_list = Reports::getMonthlySiteRequestList('DRR', $_POST['site'],$_POST['date1'],$_POST['date2'], 5);
            
            $report_url = self::generateMonthly($month, $site, $year, $month_nr, $month_assigned, $month_wip, $month_done, $month_closed,
                                $csr_wip,
                                $csr_done,
                                $csr_closed,
                                $csr_ongoing,
                                $csr_delay,
                                $csr_total,
                                $cpr_wip,
                                $cpr_done,
                                $cpr_closed,
                                $cpr_ongoing,
                                $cpr_delay,
                                $cpr_total,
                                $drr_wip,
                                $drr_done,
                                $drr_closed,
                                $drr_ongoing,
                                $drr_delay,
                                $drr_total,
                                $csr_completion_percentage,
                                $csr_ontime_percentage,
                                $members_report,
                                $monthlyRequest);


            View::renderTemplate('report/report.html',[
                'month' => $month,
                'year' => $year,
                'site' => $site,
                'date1' => $date1,
                'date2' => $date2,
                'members_report' => $members_report,
                'new_request' => $month_nr,
                'assigned' => $month_assigned,
                'work_in_progress' => $month_wip,
                'done' => $month_done,
                'closed' => $month_closed,

                /*
                'csr_newrequest' => $csr_newrequest,
                'csr_assigned' => $csr_assigned,
                */
                'csr_wip' => $csr_wip,
                'csr_done' => $csr_done,
                'csr_closed' => $csr_closed,
                'csr_ongoing' => $csr_ongoing,
                'csr_delay' => $csr_delay,
                'csr_total' => $csr_total,
                'csr_completion_percentage' => $csr_completion_percentage,
                'csr_ontime_percentage' => $csr_ontime_percentage,
                'csr_category_list' => $csr_category_list,
                /*
                'cpr_newrequest' => $cpr_newrequest,
                'cpr_assigned' => $cpr_assigned,
                */
                'cpr_wip' => $cpr_wip,
                'cpr_done' => $cpr_done,
                'cpr_closed' => $cpr_closed,
                'cpr_delay' => $cpr_delay,
                'cpr_ongoing' => $cpr_ongoing,
                'cpr_total' => $cpr_total,
                'cpr_category_list' => $cpr_category_list,
                /*
                'drr_newrequest' => $drr_newrequest,
                'drr_assigned' => $drr_assigned,
                */
                'drr_wip' => $drr_wip,
                'drr_done' => $drr_done,
                'drr_closed' => $drr_closed,
                'drr_ongoing' => $drr_ongoing,
                'drr_delay' => $drr_delay,
                'drr_total' => $drr_total,
                'drr_category_list' => $drr_category_list,
                'monthlyRequest' => $monthlyRequest,
                'report_url' =>$report_url
            ]);

            //View::render($report_url);
            //echo file_exists($report_url);
            
            //echo $report_url;
            
             //var_dump(pathinfo($report_url));
            //print file_get_contents($report_url);

            //Flash::addMessage('Download this file ');

        }else {
            
            Flash::addMessage('Please input Month, Year and Site.', "warning");

            $this->redirect('/report/index');
        }



    }

    public static function generateMonthly($month, $site, $year, $new_request, $assigned, $wip, $done, $closed,
                                            $csr_wip,
                                            $csr_done,
                                            $csr_closed,
                                            $csr_ongoing,
                                            $csr_delay,
                                            $csr_total,
                                            $cpr_wip,
                                            $cpr_done,
                                            $cpr_closed,
                                            $cpr_ongoing,
                                            $cpr_delay,
                                            $cpr_total,
                                            $drr_wip,
                                            $drr_done,
                                            $drr_closed,
                                            $drr_ongoing,
                                            $drr_delay,
                                            $drr_total,
                                            $csr_completion_percentage,
                                            $csr_ontime_percentage,
                                            $members_report,
                                            $monthlyRequest)
    {
        $objPHPExcel = new Spreadsheet();
        
        $row = 1;
        $col = 1;
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
        $center = [
            'alignment' =>[
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
                    'alignment' =>[
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ],
                ];
                
        $bold = array('font' => array('size' => 12,'bold' => true));
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($bold);
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $site." ".$month." ".$year);
        $row++;

        $objPHPExcel->getActiveSheet()->getStyle('A2:F3')->applyFromArray($center);
        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "New Request");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Assigned");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Work in Progress");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Done");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Closed");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Total");
        $row++;
        // Status Values
        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $new_request);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $assigned);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $wip);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $done);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $closed);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ($new_request + $assigned + $wip + $done + $closed));
        $row++;

        $row++;

        $objPHPExcel->getActiveSheet()->getStyle('A5:G8')->applyFromArray($center);
        $objPHPExcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($bold);
        $col = 2;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Completed");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "On-going");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Delay");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Total Request");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Completion %");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "On Time %");
        $row++;

        
        $objPHPExcel->getActiveSheet()->getStyle('A6:A8')->applyFromArray($bold);
        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "CSR");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $csr_done + $csr_closed);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $csr_ongoing);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $csr_delay);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $csr_total);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $csr_completion_percentage);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $csr_ontime_percentage);
        $row++;
        
        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "CPR");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cpr_done + $cpr_closed);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cpr_ongoing);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 0);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cpr_total);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "N/A");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "N/A");
        $row++;

        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "DRR");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $drr_done + $drr_closed);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $drr_ongoing);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 0);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $drr_total);
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "N/A");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "N/A");
        $row++;

        $row++;


        $col = 1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Member");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Assigned");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Work in Progress");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Done");
        $col++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, "Closed");
        $row++;
        
        
        $objPHPExcel->getActiveSheet()->getStyle("A10:F10")->applyFromArray($center);
        $objPHPExcel->getActiveSheet()->getStyle('A10:F10')->applyFromArray($bold);

        foreach ($members_report as $key => $value) {
        
            $col = 1;
            $objPHPExcel->getActiveSheet()->getStyle("A$row:E$row")->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['name']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['assigned']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['workinprogress']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['done']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['closed']);
            $col++;
            $row++;
            //$total[] = $value[5];
        }
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

        foreach ($monthlyRequest as $key => $value) {
        
            $col = 1;
            $objPHPExcel->getActiveSheet()->getStyle("A$row:E$row")->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_process']."-".sprintf("%04d", $value['sr_number'])."-".$value['sr_year']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_process']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_rqstr']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_date_request']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_date_set']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_done_date']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_site']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_rqst']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_dtls']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_status']);
            $col++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value['sr_assigned_to']);
            $col++;
            $row++;
            //$total[] = $value[5];
        }

        $objWriter = new Xlsx($objPHPExcel);


        if (!empty($site)) {
            
            $file = "../public/generated report/report-".$site." ".$month." ".$year.".php";
            $d_file = "../generated report/report-".$site." ".$month." ".$year.".php";

        }else{

            $file = "../public/generated report/report-".$year."-".$month.".php";
            $d_file = "../generated report/report-".$year."-".$month.".php";

        }


        $filename = str_replace('.php', '.xlsx', $file);
        $d_file = str_replace('.php', '.xlsx', $d_file);
        if (file_exists($filename)) {
            unlink($filename) or die("Couldn't delete file");
           // unlink($d_file) or die("Couldn't delete file");
        }
        clearstatcache();
        

        $objWriter->save(str_replace('.php', '.xlsx', $file));

        //return "generated report/report-".$site." ".$month." ".$year.".xlsx";

        return $d_file;

    }

    public function dailyReport(){

        $mis_support     = User::getEndorseList('MIS','Support');
        $mis_servicedesk = User::getEndorseList('MIS','Service Desk');
        $mis_srsChecker  = User::getEndorseList('MIS','SRS Checker');
        $mis_srsApprover = User::getEndorseList('MIS','SRS Approver');

        
        echo "<pre>";
        print_r($mis_support);
        echo "</pre>";
        


        for ($i=0; $i < count($mis_support); $i++) { 
            # code...
            $mis_email = User::getEmail($mis_support[$i]['full_name']);

            echo $mis_email['email_account']."<br>";

            //print_r($mis_email);

        }

    }

}
