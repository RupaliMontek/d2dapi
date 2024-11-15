<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		@parent::__construct();
    	$this->load->model('admin/Common_model');
        $this->load->dbforge();
	}

	public function check_login()
	{
		if( ($this->session->userdata('user_id')=='') || ($this->session->userdata('user_name')=='') || ($this->session->userdata('user_email')=='' || $this->session->userdata('user_role')==''))
		{
			redirect('login');
		}
	}

	public function index()
	{       
	/******************************************************************Start aa_dfia_licence_details***************************************************************************************/

           $query = "SELECT aa_dfia_licence_details.*, ship_bill_summary.sbs_id, ship_bill_summary.iec,item_details.invoice_id,item_details.item_id FROM aa_dfia_licence_details LEFT JOIN item_details ON aa_dfia_licence_details.item_id = item_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id LEFT JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id";
            $statement = $this->db->query($query);
            $iecwise_data=array();
            $result =$statement->result_array();
            //print_r($result);
        
        foreach($result as $str){
                    echo $iec=$str['iec'];
                    $sql = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec'";
                    $iecwise = $this->db->query($sql);
                    $iecwise_data =$iecwise->result_array();
                    $db1=$this->database_connection($iecwise_data[0]['lucrative_users_id']);
                    
                   $sql_insert = "INSERT INTO `aa_dfia_licence_details`( `item_id`, `inv_s_no`, `item_s_no_`, `licence_no`, `descn_of_export_item`, `exp_s_no`, `expqty`, `uqc_aa`, `fob_value`, `sion`, `descn_of_import_item`, `imp_s_no`, `impqt`, `uqc_`, `indig_imp`, `created_at`) VALUES ('".$str['item_id']."','".$str['inv_s_no']."','".$str['item_s_no_']."','".$str['licence_no']."','".$str['descn_of_export_item']."','".$str['exp_s_no']."','".$str['expqty']."','".$str['uqc_aa']."','".$str['fob_value']."','".$str['sion']."','".$str['descn_of_import_item']."','".$str['imp_s_no']."','".$str['impqt']."','".$str['uqc_']."','".$str['indig_imp']."','".$str['created_at']."')"; 
                    $copy = $db1->query($sql_insert);
               //print_r($db1->last_query());exit(); 
                }
	/******************************************************************End aa_dfia_licence_details***************************************************************************************/
	   
	/******************************************************************Start bill_of_entry_summary***************************************************************************************/

            $query1 = "SELECT  * FROM bill_of_entry_summary ";
            $statement1 = $this->db->query($query1);
            $iecwise_data1=array();
            $result1 =$statement1->result_array();
            //print_r($result);
        
        foreach($result1 as $str1){
                     $iec1=$str1['iec_no'];
                    $sql = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec1'";
                    $iecwise1 = $this->db->query($sql);
                    $iecwise_data1 =$iecwise1->result_array();
                    $db1=$this->database_connection($iecwise_data1[0]['lucrative_users_id']);
                    
                    $port=$str1['port'];
                    $sql_insert1 = "INSERT INTO `bill_of_entry_summary`( `boe_file_status_id`, `invoice_title`, `port`, `port_code`, `be_no`, `be_date`, `be_type`, `iec_br`, `iec_no`, `br`, `gstin_type`, `cb_code`, `nos`, `pkg`, `item`, `g_wt_kgs`, `cont`, `be_status`, `mode`, `def_be`, `kacha`, `sec_48`, `reimp`, `adv_be`, `assess`, `exam`, `hss`, `first_check`, `prov_final`, `country_of_origin`, `country_of_consignment`, `port_of_loading`, `port_of_shipment`, `importer_name_and_address`, `ad_code`, `cb_name`, `aeo`, `ucr`, `bcd`, `acd`, `sws`, `nccd`, `add`, `cvd`, `igst`, `g_cess`, `sg`, `saed`, `gsia`, `tta`, `health`, `total_duty`, `int`, `pnlty`, `fine`, `tot_ass_val`, `tot_amount`, `wbe_no`, `wbe_date`, `wbe_site`, `wh_code`, `submission_date`, `assessment_date`, `examination_date`, `ooc_date`, `submission_time`, `assessment_time`, `examination_time`, `ooc_time`, `submission_exchange_rate`, `assessment_exchange_rate`, `ooc_no`, `ooc_date_`, `created_at`, `examination_exchange_rate`, `ooc_exchange_rate`) 
                    VALUES ('".$str1['boe_file_status_id']."','".$str1['invoice_title']."','.$port.','".$str1['port_code']."','".$str1['be_no']."','".$str1['be_date']."','".$str1['be_type']."','".$str1['iec_br']."', '".$str1['iec_no']."', '".$str1['br']."', '".$str1['gstin_type']."','".$str1['cb_code']."', '".$str1['nos']."','".$str1['pkg']."','".$str1['item']."','".$str1['g_wt_kgs']."','".$str1['cont']."','".$str1['be_status']."','".$str1['mode']."','".$str1['def_be']."','".$str1['kacha']."','".$str1['sec_48']."','".$str1['reimp']."','".$str1['adv_be']."','".$str1['assess']."','".$str1['exam']."', '".$str1['hss']."','".$str1['first_check']."', '".$str1['prov_final']."', '".$str1['country_of_origin']."', '".$str1['country_of_consignment']."', '".$str1['port_of_loading']."', '".$str1['port_of_shipment']."', '".$str1['importer_name_and_address']."', '".$str1['ad_code']."', '".$str1['cb_name']."', '".$str1['aeo']."', '".$str1['ucr']."','".$str1['bcd']."', '".$str1['acd']."', '".$str1['sws']."', '".$str1['nccd']."', '".$str1['add']."','".$str1['cvd']."', '".$str1['igst']."','".$str1['g_cess']."','".$str1['sg']."', '".$str1['saed']."','".$str1['gsia']."', '".$str1['tta']."','".$str1['health']."','".$str1['total_duty']."','".$str1['int']."', '".$str1['pnlty']."','".$str1['fine']."', '".$str1['tot_ass_val']."', '".$str1['tot_amount']."','".$str1['wbe_no']."', '".$str1['wbe_date']."', '".$str1['wbe_site']."','".$str1['wh_code']."', '".$str1['submission_date']."', '".$str1['assessment_date']."', '".$str1['examination_date']."', '".$str1['ooc_date']."', '".$str1['submission_time']."','".$str1['assessment_time']."','".$str1['examination_time']."','".$str1['ooc_time']."','".$str1['submission_exchange_rate']."', '".$str1['assessment_exchange_rate']."','".$str1['ooc_no']."', '".$str1['ooc_date_']."','".$str1['created_at']."','".$str1['examination_exchange_rate']."', '".$str1['ooc_exchange_rate']."')";
                    
                    $copy_bill_of_entry_summary = $db1->query($sql_insert1);
                    //  print_r($db1->last_query());
                }
	/******************************************************************End bill_of_entry_summary***************************************************************************************/
        
	/******************************************************************Start boe_delete_logs***************************************************************************************/
	
            $query_boe_delete_logs = "SELECT  * FROM boe_delete_logs";
            $statement_boe_delete_logs = $this->db->query($query_boe_delete_logs);
            $iecwise_data_boe_delete_logs=array();
            $result_boe_delete_logs =$statement_boe_delete_logs->result_array();
            //print_r($result);
        
        foreach($result_boe_delete_logs as $str_boe_delete_logs){
                     $iec_boe_delete_logs=$str_boe_delete_logs['iec_no'];
                    $sql_boe_delete_logs = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_boe_delete_logs'";
                    $iecwise_boe_delete_logs = $this->db->query($sql_boe_delete_logs);
                    $iecwise_data_boe_delete_logs =$iecwise_boe_delete_logs->result_array();
                    $db1_boe_delete_logs=$this->database_connection($iecwise_data_boe_delete_logs[0]['lucrative_users_id']);
                    
                   // $port=$str['port'];
                   	$sql_insert_boe_delete_logs = "INSERT INTO `boe_delete_logs` (`filename`, `be_no`, `be_date`, `iec_no`, `br`, `fullname`, `email`, `mobile`, `deleted_at`) VALUES('".$str_boe_delete_logs['filename']."','".$str_boe_delete_logs['be_no']."','".$str_boe_delete_logs['be_date']."','".$str_boe_delete_logs['iec_no']."','".$str_boe_delete_logs['br']."','".$str_boe_delete_logs['fullname']."','".$str_boe_delete_logs['email']."','".$str_boe_delete_logs['mobile']."','".$str_boe_delete_logs['deleted_at']."','')";
   
                    $copy_bill_of_entry_summary_boe_delete_logs = $db1->query($sql_insert_boe_delete_logs);
                    //  print_r($db1->last_query());
                }
	
	
	/******************************************************************Start boe_delete_logs***************************************************************************************/
	
	
		/******************************************************************Start bill_payment_details***************************************************************************************/
	
            $query_bill_payment_details = "SELECT  bill_payment_details.* , bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM bill_payment_details  LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_payment_details.boe_id";
            $statement_bill_payment_details = $this->db->query($query_bill_payment_details);
            $iecwise_data_bill_payment_details=array();
            $result_bill_payment_details =$statement_bill_payment_details->result_array();
            //print_r($result);
        
        foreach($result_bill_payment_details as $str_bill_payment_details){
                     $iec_bill_payment_details=$str_bill_payment_details['iec_no'];
                    $sql_bill_payment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_bill_payment_details'";
                    $iecwise_bill_payment_details = $this->db->query($sql_bill_payment_details);
                    $iecwise_data_bill_payment_details =$iecwise_bill_payment_details->result_array();
                    $db1_bill_payment_details=$this->database_connection($iecwise_data_bill_payment_details[0]['lucrative_users_id']);
                    
                    //$port=$str['port'];
	                $sql_insert_bill_payment_details = "INSERT INTO `bill_payment_details` (`payment_details_id`, `sr_no`, `challan_no`, `paid_on`, `amount`, `created_at`) VALUES('".$str_bill_payment_details['payment_details_id']."','".$str_bill_payment_details['sr_no']."','".$str_bill_payment_details['challan_no']."','".$str_bill_payment_details['paid_on']."','".$str_bill_payment_details['amount']."','".$str_bill_payment_details['created_at']."')";
   
                    $copy_bill_of_entry_summary_bill_payment_details = $db1->query($sql_insert_bill_payment_details);
                    //  print_r($db1->last_query());
                }
	
	
	/******************************************************************Start bill_payment_details***************************************************************************************/
	
	
/******************************************************************Start bill_bond_details***************************************************************************************/
	
            $query_bill_bond_details = "SELECT bill_bond_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM bill_bond_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_bond_details.boe_id";
            $statement_bill_bond_details = $this->db->query($query_bill_bond_details);
            $iecwise_data_bill_bond_details=array();
            $result_bill_bond_details =$statement_bill_bond_details->result_array();
            //print_r($result);
        
        foreach($result_bill_bond_details as $str_bill_bond_details){
                     $iec_bill_bond_details=$str_bill_bond_details['iec_no'];
                    $sql_bill_bond_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_bill_bond_details'";
                    $iecwise_bill_bond_details = $this->db->query($sql_bill_bond_details);
                    $iecwise_data_bill_bond_details =$iecwise_bill_bond_details->result_array();
                    $db1_bill_bond_details=$this->database_connection($iecwise_data_bill_bond_details[0]['lucrative_users_id']);
                 
                $sql_insert_bill_bond_details = "INSERT INTO `bill_bond_details` (`bond_details_id`, `bond_no`, `port`, `bond_cd`, `debt_amt`, `bg_amt`,`created_at`) VALUES('".$str_bill_bond_details['bond_details_id']."','".$str_bill_bond_details['bond_no']."','".$str_bill_bond_details['port']."','".$str_bill_bond_details['bond_cd']."','".$str_bill_bond_details['debt_amt']."','".$str_bill_bond_details['bg_amt']."','".$str_bill_bond_details['created_at']."')";   
                    $copy_bill_bond_details = $db1->query($sql_insert_bill_bond_details);
                    //  print_r($db1->last_query());
                }
	
	
	/******************************************************************Start bill_bond_details***************************************************************************************/
	
/******************************************************************Start bill_container_details***************************************************************************************/
	
            $query_bill_container_details = "SELECT bill_container_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM bill_container_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_container_details.boe_id";
            $statement_bill_container_details = $this->db->query($query_bill_container_details);
            $iecwise_data_bill_container_details=array();
            $result_bill_container_details =$statement_bill_container_details->result_array();
            //print_r($result);
        
        foreach($result_bill_container_details as $str_bill_container_details){
                     $iec_bill_container_details=$str_bill_container_details['iec_no'];
                    $sql_bill_container_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_bill_container_details'";
                    $iecwise_bill_container_details = $this->db->query($sql_bill_container_details);
                    $iecwise_data_bill_container_details =$iecwise_bill_container_details->result_array();
                    $db1_bill_container_details=$this->database_connection($iecwise_data_bill_container_details[0]['lucrative_users_id']);
                  $sql_insert_bill_container_details = "INSERT INTO `bill_container_details` (`container_details_id`, `sno`, `lcl_fcl`, `truck`, `seal`, `container_number`,`created_at`) VALUES('".$str_bill_container_details['container_details_id']."','".$str_bill_container_details['sno']."','".$str_bill_container_details['lcl_fcl']."','".$str_bill_container_details['truck']."','".$str_bill_container_details['seal']."','".$str_bill_container_details['container_number']."','".$str_bill_container_details['created_at']."')";                    
                $copy_bill_container_details = $db1->query($sql_insert_bill_container_details);
                    //  print_r($db1->last_query());
                }
	
	
	/******************************************************************Start bill_container_details***************************************************************************************/
		
/******************************************************************Start bill_licence_details***************************************************************************************/
	
            $query_bill_licence_details = "SELECT n1.boe_id, n1.iec_no, bill_licence_details.* FROM (select * from bill_of_entry_summary order by boe_id ) n1 JOIN invoice_and_valuation_details ON n1.boe_id = invoice_and_valuation_details.boe_id JOIN duties_and_additional_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id JOIN bill_licence_details ON duties_and_additional_details.duties_id = bill_licence_details.duties_id and bill_licence_details.invsno != ''";
            $statement_bill_licence_details = $this->db->query($query_bill_licence_details);
            $iecwise_data_bill_licence_details=array();
            $result_bill_licence_details =$statement_bill_licence_details->result_array();
            //print_r($result);
        
        foreach($result_bill_licence_details as $str_bill_licence_details){
                     $iec_bill_licence_details=$str_bill_licence_details['iec_no'];
                    $sql_bill_licence_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec'";
                    $iecwise_bill_licence_details = $this->db->query($sql_bill_licence_details);
                    $iecwise_data_bill_licence_details =$iecwise_bill_licence_details->result_array();
                    $db1_bill_licence_details=$this->database_connection($iecwise_data_bill_licence_details[0]['lucrative_users_id']);
                $sql_insert_bill_licence_details = "INSERT INTO `bill_licence_details` (`duties_id`, `invsno`, `itemsn`, `lic_slno`, `lic_no`, `lic_date`,`code`,`port`,`debit_value`,`qty`,`uqc_lc_d`,`debit_duty`,`created_at`) VALUES('".$str_bill_licence_details['duties_id']."','".$str_bill_licence_details['invsno']."','".$str_bill_licence_details['itemsn']."','".$str_bill_licence_details['lic_slno']."','".$str_bill_licence_details['lic_no']."','".$str_bill_licence_details['lic_date']."','".$str_bill_licence_details['code']."','".$str_bill_licence_details['port']."','".$str_bill_licence_details['debit_value']."','".$str_bill_licence_details['qty']."','".$str_bill_licence_details['uqc_lc_d']."','".$str_bill_licence_details['debit_duty']."','".$str_bill_licence_details['created_at']."')";

                  $copy_bill_licence_details = $db1->query($sql_insert_bill_licence_details);
                }
	
	
	/******************************************************************Start bill_licence_details***************************************************************************************/

/******************************************************************Start boe_file_status***************************************************************************************/
	
            $query_boe_file_status = "SELECT * FROM public.boe_file_status";
            $statement_boe_file_status = $this->db->query($query_boe_file_status);
            $iecwise_data_boe_file_status=array();
            $result_boe_file_status =$statement_boe_file_status->result_array();
            //print_r($result);
        
        foreach($result_boe_file_status as $str_boe_file_status){
                     $iec_boe_file_status=$str_boe_file_status['user_iec_no'];
                    $sql_boe_file_status = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_boe_file_status'";
                    $iecwise_boe_file_status = $this->db->query($sql_boe_file_status);
                    $iecwise_data_boe_file_status =$iecwise_boe_file_status->result_array();
                    $db1_boe_file_status=$this->database_connection($iecwise_data_boe_file_status[0]['lucrative_users_id']);
            
$sql_insert_boe_file_status = "INSERT INTO `boe_file_status` (`pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`,`br`,`be_no`,`stage`,`status`,`remarks`,`created_at`) VALUES('".$str_boe_file_status['pdf_filepath']."','".$str_boe_file_status['pdf_filename']."','".$str_boe_file_status['user_iec_no']."','".$str_boe_file_status['lucrative_users_id']."','".$str_boe_file_status['file_iec_no']."','".$str_boe_file_status['br']."','".$str_boe_file_status['be_no']."','".$str_boe_file_status['stage']."','".$str_boe_file_status['status']."','".$str_boe_file_status['remarks']."','".$str_boe_file_status['created_at']."')";                    //  print_r($db1->last_query());
                
         $copy_insert_boe_file_status = $db1_boe_file_status->query($sql_insert_boe_file_status);    
            
        }
	
	
	/******************************************************************Start boe_file_status***************************************************************************************/

/******************************************************************Start bill_manifest_details***************************************************************************************/
	
            $query_bill_manifest_details = "SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM bill_manifest_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id";
            $statement_bill_manifest_details = $this->db->query($query_bill_manifest_details);
            $iecwise_data_bill_manifest_details=array();
            $result_bill_manifest_details =$statement_bill_manifest_details->result_array();
            //print_r($result);
        
        foreach($result_bill_manifest_details as $str_bill_manifest_details){
                     $iec_bill_manifest_details=$str_bill_manifest_details['iec_no'];
                    $sql_bill_manifest_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_bill_manifest_details'";
                    $iecwise_bill_manifest_details = $this->db->query($sql_bill_manifest_details);
                    $iecwise_data_bill_manifest_details =$iecwise_bill_manifest_details->result_array();
                    $db1_bill_manifest_details=$this->database_connection($iecwise_data_bill_manifest_details[0]['lucrative_users_id']);
            
        $sql_insert_bill_manifest_details = "INSERT INTO `bill_manifest_details` (`boe_id`, `igm_no`, `igm_date`, `inw_date`, `gigmno`, `gigmdt`,`mawb_no`,`mawb_date`,`hawb_no`,`hawb_date`,`pkg`,`gw`,`created_at`) VALUES('".$str_bill_manifest_details['boe_id']."','".$str_bill_manifest_details['igm_no']."','".$str_bill_manifest_details['igm_date']."','".$str_bill_manifest_details['inw_date']."','".$str_bill_manifest_details['gigmno']."','".$str_bill_manifest_details['gigmdt']."','".$str_bill_manifest_details['mawb_no']."','".$str_bill_manifest_details['mawb_date']."','".$str_bill_manifest_details['hawb_no']."','".$str_bill_manifest_details['hawb_date']."','".$str_bill_manifest_details['pkg']."','".$str_bill_manifest_details['gw']."','".$str_bill_manifest_details['created_at']."')";                
         $copy_insert_bill_manifest_details = $db1_bill_manifest_details->query($sql_insert_bill_manifest_details);    
            
        }
	
	
	/******************************************************************Start bill_manifest_details***************************************************************************************/
				
/******************************************************************Start challan_details***************************************************************************************/
	
            $query_challan_details = "SELECT challan_details.*,ship_bill_summary.sbs_id,ship_bill_summary.iec FROM challan_details LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=challan_details.sbs_id";
            $statement_challan_details = $this->db->query($query_challan_details);
            $iecwise_challan_details=array();
            $result_challan_details =$statement_challan_details->result_array();
            //print_r($result);
        
        foreach($result_challan_details as $str_challan_details){
                     $iec_challan_details=$str_challan_details['iec'];
                    $sql_challan_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_challan_details'";
                    $iecwise_challan_details = $this->db->query($sql_challan_details);
                    $iecwise_data_challan_details =$iecwise_challan_details->result_array();
                    $db1_challan_details=$this->database_connection($iecwise_data_challan_details[0]['lucrative_users_id']);
            
$sql_insert_challan_details = "INSERT INTO `challan_details` (`sbs_id`, `sr_no`, `challan_no`, `paymt_dt`, `amount`, `created_at`) VALUES('".$str_challan_details['sbs_id']."','".$str_challan_details['sr_no']."','".$str_challan_details['challan_no']."','".$str_challan_details['paymt_dt']."','".$str_challan_details['amount']."','".$str_challan_details['created_at']."')";         
   
             $copy_insert_challan_details = $db1_challan_details->query($sql_insert_challan_details);    
        
        }
	
	
	/******************************************************************Start challan_details***************************************************************************************/

	/******************************************************************Start drawback_details***************************************************************************************/
	
            $query_drawback_details = "SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br,iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* FROM (select * from ship_bill_summary order by sbs_id desc) n1 JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id JOIN drawback_details ON drawback_details.item_id = item_details.item_id";
            $statement_drawback_details = $this->db->query($query_drawback_details);
            $iecwise_drawback_details=array();
            $result_drawback_details =$statement_drawback_details->result_array();
            //print_r($result);
        
        foreach($result_drawback_details as $str_drawback_details){
                     $iec_drawback_details=$str_drawback_details['iec'];
                    $sql_drawback_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_drawback_details'";
                    $iecwise_drawback_details = $this->db->query($sql_drawback_details);
                    $iecwis_drawback_details =$iecwise_drawback_details->result_array();
                    $db1_drawback_details=$this->database_connection($iecwis_drawback_details[0]['lucrative_users_id']);
            
$sql_insert_drawback_details = "INSERT INTO `drawback_details` (`item_id`, `inv_sno`, `item_sno`, `dbk_sno`, `qty_wt`, `value`,`dbk_amt`,`stalev`,`cenlev`,`rosctl_amt`,`created_at`,`rate`,`rebate`,`amount`,`dbk_rosl`) VALUES('".$str_drawback_details['item_id']."','".$str_drawback_details['inv_sno']."','".$str_drawback_details['item_sno']."','".$str_drawback_details['dbk_sno']."','".$str_drawback_details['qty_wt']."','".$str_drawback_details['value']."','".$str_drawback_details['dbk_amt']."','".$str_drawback_details['stalev']."','".$str_drawback_details['cenlev']."','".$str_drawback_details['rosctl_amt']."','".$str_drawback_details['created_at']."','".$str_drawback_details['rate']."','".$str_drawback_details['rebate']."','".$str_drawback_details['amount']."','".$str_drawback_details['dbk_rosl']."')"; 
$copy_insert_drawback_details = $db1_drawback_details->query($sql_insert_drawback_details);    
            
        }
	
	
	/******************************************************************Start drawback_details***************************************************************************************/

	/******************************************************************Start cb_file_status***************************************************************************************/
	
            $query_cb_file_status = "SELECT * FROM cb_file_status";
            $statement_cb_file_status = $this->db->query($query_cb_file_status);
            $iecwise_cb_file_status=array();
            $result_cb_file_status =$statement_cb_file_status->result_array();
            //print_r($result);
        
        foreach($result_cb_file_status as $str_cb_file_status){
                     $iec_cb_file_status=$str_cb_file_status['iec'];
                    $sql_cb_file_status = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_cb_file_status'";
                    $iecwise_cb_file_status = $this->db->query($sql_cb_file_status);
                    $iecwise_data_cb_file_status =$iecwise_cb_file_status->result_array();
                    $db1_cb_file_status=$this->database_connection($iecwise_data_cb_file_status[0]['lucrative_users_id']);
            
$sql_insert_cb_file_status = "INSERT INTO `cb_file_status` (`pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`, `cb_no`, `cb_date`, `stage`, `status`, `remarks`, `created_at`, `br`, `is_processed`) 
VALUES('".$str_cb_file_status['pdf_filepath']."','".$str_cb_file_status['pdf_filename']."','".$str_cb_file_status['user_iec_no']."','".$str_cb_file_status['lucrative_users_id']."','".$str_cb_file_status['file_iec_no']."','".$str_cb_file_status['cb_no']."','".$str_cb_file_status['cb_date']."','".$str_cb_file_status['stage']."','".$str_cb_file_status['status']."','".$str_cb_file_status['remarks']."','".$str_cb_file_status['created_at']."','".$str_cb_file_status['br']."','".$str_cb_file_status['is_processed']."')";      
$copy_insert_cb_file_status = $db1_insert_cb_file_status->query($sql_insert_cb_file_status);    
            
        }
	
	
	/******************************************************************Start cb_file_status***************************************************************************************/



	/******************************************************************Start courier_bill_bond_details***************************************************************************************/
	
            $query_courier_bill_bond_details = "SELECT courier_bill_bond_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_bond_details LEFT JOIN cb_file_status ON courier_bill_bond_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id";
            $statement_courier_bill_bond_details = $this->db->query($query_courier_bill_bond_details);
            $iecwise_courier_bill_bond_details=array();
            $result_courier_bill_bond_details =$statement_courier_bill_bond_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_bond_details as $str_courier_bill_bond_details){
                     $iec_courier_bill_bond_details=$str_courier_bill_bond_details['user_iec_no'];
                    $sql_courier_bill_bond_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_bond_details'";
                    $iecwise_courier_bill_bond_details= $this->db->query($sql_courier_bill_bond_details);
                    $iecwise_data_courier_bill_bond_details =$iecwise_courier_bill_bond_details->result_array();
                    $db1_courier_bill_bond_details=$this->database_connection($iecwise_data_courier_bill_bond_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_bond_details = "INSERT INTO `courier_bill_bond_details` (`bond_details_id`, `bond_details_srno`, `bond_type`, `bond_number`, `clearance_of_imported_goods_bond_already_registered_customs`, `created_at`) 
VALUES('".$str_courier_bill_bond_details['bond_details_id']."','".$str_courier_bill_bond_details['bond_details_srno']."','".$str_courier_bill_bond_details['bond_type']."','".$str_courier_bill_bond_details['bond_number']."','".$str_courier_bill_bond_details['clearance_of_imported_goods_bond_already_registered_customs']."','".$str_cb_file_status['created_at']."')";      
$copy_insert_courier_bill_bond_details = $db1_courier_bill_bond_details->query($sql_insert_courier_bill_bond_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_bond_details***************************************************************************************/
	
	
	/******************************************************************Start courier_bill_container_details***************************************************************************************/
	
            $query_courier_bill_container_details = "SELECT courier_bill_container_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_container_details LEFT JOIN cb_file_status ON courier_bill_container_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id";
            $statement_courier_bill_container_details = $this->db->query($query_courier_bill_container_details);
            $iecwise_courier_bill_container_details=array();
            $result_courier_bill_container_details =$statement_courier_bill_container_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_container_details as $str_courier_bill_container_details){
                     $iec_courier_bill_container_details=$str_courier_bill_container_details['user_iec_no'];
                    $sql_courier_bill_container_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_container_details'";
                    $iecwise_courier_bill_bond_details= $this->db->query($sql_courier_bill_container_details);
                    $iecwise_data_courier_bill_container_details =$iecwise_courier_bill_container_details->result_array();
                    $db1_insert_courier_bill_container_details=$this->database_connection($iecwise_data_courier_bill_container_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_container_details = "INSERT INTO `courier_bill_container_details` (`bond_details_id`, `bond_details_srno`, `bond_type`, `bond_number`, `clearance_of_imported_goods_bond_already_registered_customs`, `created_at`) 
VALUES('".$str_courier_bill_container_details['bond_details_id']."','".$str_courier_bill_container_details['bond_details_srno']."','".$str_courier_bill_container_details['bond_type']."','".$str_courier_bill_container_details['bond_number']."','".$str_courier_bill_container_details['clearance_of_imported_goods_bond_already_registered_customs']."','".$str_courier_bill_container_details['created_at']."')";      
$copy_insert_courier_bill_container_details = $db1_insert_courier_bill_container_details->query($sql_insert_courier_bill_container_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_container_details***************************************************************************************/


	/******************************************************************Start courier_bill_duty_details***************************************************************************************/
	
         $query_courier_bill_duty_details = "SELECT courier_bill_duty_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no,courier_bill_items_details.items_detail_id,courier_bill_items_details.courier_bill_of_entry_id FROM courier_bill_duty_details LEFT JOIN courier_bill_items_details ON courier_bill_items_details.items_detail_id=courier_bill_duty_details.items_detail_id LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id";
            $statement_courier_bill_duty_details = $this->db->query($query_courier_bill_duty_details);
            $iecwise_courier_bill_duty_details=array();
            $result_courier_bill_duty_details =$statement_courier_bill_duty_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_duty_details as $str_courier_bill_duty_details){
                     $iec_courier_bill_duty_details=$str_courier_bill_duty_details['user_iec_no'];
                    $sql_courier_bill_duty_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_duty_details'";
                    $iecwise_courier_bill_duty_details= $this->db->query($sql_courier_bill_duty_details);
                    $iecwise_data_courier_bill_duty_details =$iecwise_courier_bill_duty_details->result_array();
                    $db1_courier_bill_duty_details=$this->database_connection($iecwise_data_courier_bill_duty_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_duty_details = "INSERT INTO `courier_bill_duty_details`( `duty_details_id`, `bcd_duty_head`, `bcd_ad_valorem`, `bcd_specific_rate`, `bcd_duty_forgone`, `bcd_duty_amount`, `aidc_duty_head`, `aidc_ad_valorem`, `aidc_specific_rate`, `aidc_duty_forgone`, `aidc_duty_amount`, `sw_srchrg_duty_head`, `sw_srchrg_ad_valorem`, `sw_srchrg_specific_rate`, `sw_srchrg_duty_forgone`, `sw_srchrg_duty_amount`, `igst_duty_head`, `igst_ad_valorem`, `igst_specific_rate`, `igst_duty_forgone`, `igst_duty_amount`, `cmpnstry_duty_head`, `cmpnstry_ad_valorem`, `cmpnstry_specific_rate`, `cmpnstry_duty_forgone`, `cmpnstry_duty_amount`, `dummy5_duty_head`, `dummy5_ad_valorem`, `dummy5_specific_rate`, `dummy5_duty_forgone`, `dummy5_duty_amount`, `dummy6_duty_head`, `dummy6_ad_valorem`, `dummy6_specific_rate`, `dummy6_duty_forgone`, `dummy6_duty_amount`, `dummy7_duty_head`, `dummy7_ad_valorem`, `dummy7_specific_rate`, `dummy7_duty_forgone`, `dummy7_duty_amount`, `dummy8_duty_head`, `dummy8_ad_valorem`, `dummy8_specific_rate`, `dummy8_duty_forgone`, `dummy8_duty_amount`, `dummy9_duty_head`, `dummy9_ad_valorem`, `dummy9_specific_rate`, `dummy9_duty_forgone`, `dummy9_duty_amount`, `dummy10_duty_head`, `dummy10_ad_valorem`, `dummy10_specific_rate`, `dummy10_duty_forgone`, `dummy10_duty_amount`, `dummy11_duty_head`, `dummy11_ad_valorem`, `dummy11_specific_rate`, `dummy11_duty_forgone`, `dummy11_duty_amount`, `created_at`)
 VALUES ('".$str_courier_bill_duty_details['duty_details_id']."',
 '".$str_courier_bill_duty_details['bcd_duty_head']."',
 '".$str_courier_bill_duty_details['bcd_ad_valorem']."',
 '".$str_courier_bill_duty_details['bcd_specific_rate']."',
 '".$str_courier_bill_duty_details['bcd_duty_forgone']."',
 '".$str_courier_bill_duty_details['bcd_duty_amount']."',
 '".$str_courier_bill_duty_details['sw_srchrg_duty_head']."',
 '".$str_courier_bill_duty_details['aidc_ad_valorem']."',
 '".$str_courier_bill_duty_details['bcd_specific_rate']."',
 '".$str_courier_bill_duty_details['aidc_duty_forgone']."',
'".$str_courier_bill_duty_details['aidc_duty_amount']."',
'".$str_courier_bill_duty_details['sw_srchrg_duty_head']."',
'".$str_courier_bill_duty_details['sw_srchrg_ad_valorem']."',
'".$str_courier_bill_duty_details['sw_srchrg_specific_rate']."',
'".$str_courier_bill_duty_details['sw_srchrg_duty_forgone']."',
'".$str_courier_bill_duty_details['sw_srchrg_duty_amount']."',
'".$str_courier_bill_duty_details['igst_duty_head']."',
'".$str_courier_bill_duty_details['igst_ad_valorem']."',
'".$str_courier_bill_duty_details['igst_specific_rate']."',
'".$str_courier_bill_duty_details['igst_duty_forgone']."',
'".$str_courier_bill_duty_details['igst_duty_amount']."',
'".$str_courier_bill_duty_details['cmpnstry_duty_head']."',
'".$str_courier_bill_duty_details['cmpnstry_ad_valorem']."',
'".$str_courier_bill_duty_details['cmpnstry_specific_rate']."',
'".$str_courier_bill_duty_details['cmpnstry_duty_forgone']."',
'".$str_courier_bill_duty_details['ccmpnstry_duty_amount']."',
'".$str_courier_bill_duty_details['dummy5_duty_head']."',
'".$str_courier_bill_duty_details['dummy5_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy5_specific_rate']."',
'".$str_courier_bill_duty_details['dummy5_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy5_duty_amount']."',
'".$str_courier_bill_duty_details['dummy6_duty_head']."',
'".$str_courier_bill_duty_details['dummy6_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy6_specific_rate']."',
'".$str_courier_bill_duty_details['dummy6_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy6_duty_amount']."',
'".$str_courier_bill_duty_details['dummy7_duty_head']."',
'".$str_courier_bill_duty_details['dummy7_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy7_specific_rate']."',
'".$str_courier_bill_duty_details['dummy7_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy7_duty_amount']."',
'".$str_courier_bill_duty_details['dummy8_duty_head']."',
'".$str_courier_bill_duty_details['dummy8_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy8_specific_rate']."',
'".$str_courier_bill_duty_details['dummy8_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy8_duty_amount']."',
'".$str_courier_bill_duty_details['dummy9_duty_head']."',
'".$str_courier_bill_duty_details['dummy9_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy9_specific_rate']."',
'".$str_courier_bill_duty_details['dummy9_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy9_duty_amount']."',
'".$str_courier_bill_duty_details['dummy10_duty_head']."',
'".$str_courier_bill_duty_details['dummy10_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy10_specific_rate']."',
'".$str_courier_bill_duty_details['dummy10_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy10_duty_amount']."',
'".$str_courier_bill_duty_details['dummy11_duty_head']."',
'".$str_courier_bill_duty_details['dummy11_ad_valorem']."',
'".$str_courier_bill_duty_details['dummy11_specific_rate']."',
'".$str_courier_bill_duty_details['dummy11_duty_forgone']."',
'".$str_courier_bill_duty_details['dummy11_duty_amount']."',
'".$str_courier_bill_duty_details['created_at']."'))";      
$copy_insert_courier_bill_duty_details = $db1_courier_bill_duty_details->query($sql_insert_courier_bill_duty_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_duty_details***************************************************************************************/
			

	/******************************************************************Start courier_bill_igm_details***************************************************************************************/
	
            $query_courier_bill_igm_details = "SELECT courier_bill_igm_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_igm_details LEFT JOIN cb_file_status ON courier_bill_igm_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id";
            $statement_courier_bill_igm_details = $this->db->query($query_courier_bill_igm_details);
            $iecwise_courier_bill_igm_details=array();
            $result_courier_bill_igm_details =$statement_courier_bill_igm_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_igm_details as $str_courier_bill_igm_details){
                     $iec_courier_bill_igm_details=$str_courier_bill_igm_details['user_iec_no'];
                    $sql_courier_bill_igm_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_igm_details'";
                    $iecwise_courier_bill_igm_details= $this->db->query($sql_courier_bill_igm_details);
                    $iecwise_data_courier_bill_igm_details =$iecwise_courier_bill_igm_details->result_array();
                    $db1_courier_bill_igm_details=$this->database_connection($iecwise_data_courier_bill_igm_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_igm_details = "INSERT INTO `courier_bill_igm_details` (`igm_details_id`, `airlines`, `flight_no`, `airport_of_arrival`, `date_of_arrival`, `created_at`) 
VALUES('".$str_courier_bill_igm_details['igm_details_id']."','".$str_courier_bill_igm_details['airlines']."','".$str_courier_bill_igm_details['flight_no']."','".$str_courier_bill_igm_details['airport_of_arrival']."','".$str_courier_bill_igm_details['date_of_arrival']."','".$str_courier_bill_igm_details['created_at']."')";      
$copy_insert_courier_bill_igm_details = $db1_insert_courier_bill_igm_details->query($sql_insert_courier_bill_igm_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_igm_details***************************************************************************************/
							
	/******************************************************************Start courier_bill_invoice_details***************************************************************************************/
	
            $query_courier_bill_invoice_details = "SELECT courier_bill_invoice_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_invoice_details LEFT JOIN cb_file_status ON courier_bill_invoice_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id";
            $statement_courier_bill_invoice_details = $this->db->query($query_courier_bill_invoice_details);
            $iecwise_courier_bill_invoice_details=array();
            $result_courier_bill_invoice_details =$statement_courier_bill_invoice_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_invoice_details as $str_courier_bill_invoice_details){
                     $iec_courier_bill_invoice_details=$str_courier_bill_invoice_details['user_iec_no'];
                    $sql_courier_bill_invoice_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_invoice_details'";
                    $iecwise_courier_bill_invoice_details= $this->db->query($sql_courier_bill_invoice_details);
                    $iecwise_data_courier_bill_invoice_details =$iecwise_courier_bill_invoice_details->result_array();
                    $db1_courier_bill_invoice_details=$this->database_connection($iecwise_data_courier_bill_invoice_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_invoice_details = "INSERT INTO `courier_bill_invoice_details` 
(`invoice_detail_id`, `invoice_number`, `date_of_invoice`, `purchase_order_number`, `date_of_purchase_order`, `contract_number`, `date_of_contract`, `letter_of_credit`, `date_of_letter_of_credit`, `supplier_details_name`, `supplier_details_address`, `if_supplier_is_not_the_seller_name`, `if_supplier_is_not_the_seller_address`, `broker_agent_details_name`, `broker_agent_details_address`, `nature_of_transaction`, `if_others`, `terms_of_payment`, `conditions_or_restrictions_if_any_attached_to_sale`, `method_of_valuation`, `terms_of_invoice`, `invoice_value`, `currency`, `freight_rate`, `freight_amount`, `freight_currency`, `insurance_rate`, `insurance_amount`, `insurance_currency`, `loading_unloading_and_handling_charges_rule_rate`, `loading_unloading_and_handling_charges_rule_amount`, `loading_unloading_and_handling_charges_rule_currency`, `other_charges_related_to_the_carriage_of_goods_rate`, `other_charges_related_to_the_carriage_of_goods_amount`, `other_charges_related_to_the_carriage_of_goods_currency`, `brokerage_and_commission_rate`, `brokerage_and_commission_amount`, `brokerage_and_commission_currency`, `cost_of_containers_rate`, `cost_of_containers_amount`, `cost_of_containers_currency`, `cost_of_packing_rate`, `cost_of_packing_amount`, `cost_of_packing_currency`, `dismantling_transport_handling_in_country_export_rate`, `dismantling_transport_handling_in_country_export_amount`, `dismantling_transport_handling_in_country_export_currency`, `cost_of_goods_and_ser_vices_supplied_by_buyer_rate`, `cost_of_goods_and_ser_vices_supplied_by_buyer_amount`, `cost_of_goods_and_ser_vices_supplied_by_buyer_currency`, `documentation_rate`, `documentation_amount`, `documentation_currency`, `country_of_origin_certificate_rate`, `country_of_origin_certificate_amount`, `country_of_origin_certificate_currency`, `royalty_and_license_fees_rate`, `royalty_and_license_fees_amount`, `royalty_and_license_fees_currency`, `value_of_proceeds_which_accrue_to_seller_rate`, `value_of_proceeds_which_accrue_to_seller_amount`, `value_of_proceeds_which_accrue_to_seller_currency`, `cost_warranty_service_if_any_provided_seller_rate`, `cost_warranty_service_if_any_provided_seller_amount`, `cost_warranty_service_if_any_provided_seller_currency`, `other_payments_satisfy_obligation_rate`, `other_payments_satisfy_obligation_amount`, `other_payments_satisfy_obligation_currency`, `other_charges_and_payments_if_any_rate`, `other_charges_and_payments_if_any_amount`, `other_charges_and_payments_if_any_currency`, `discount_amount`, `discount_currency`, `rate`, `amount`, `any_other_information_which_has_a_bearing_on_value`, `are_the_buyer_and_seller_related`, `if_the_buyer_seller_has_the_relationship_examined_earlier_svb`, `svb_reference_number`, `svb_date`, `indication_for_provisional_final`, `created_at`) 
VALUES('".$str_courier_bill_invoice_details['invoice_detail_id']."',
'".$str_courier_bill_invoice_details['invoice_number']."',
'".$str_courier_bill_invoice_details['date_of_invoice']."',
'".$str_courier_bill_invoice_details['purchase_order_number']."',
'".$str_courier_bill_invoice_details['date_of_purchase_order']."',
'".$str_courier_bill_invoice_details['contract_number']."',
'".$str_courier_bill_invoice_details['date_of_contract']."',
'".$str_courier_bill_invoice_details['letter_of_credit']."';,
'".$str_courier_bill_invoice_details['date_of_letter_of_credit']."',
'".$str_courier_bill_invoice_details['supplier_details_name'].",
'".$str_courier_bill_invoice_details['supplier_details_address']."',
'".$str_courier_bill_invoice_details['if_supplier_is_not_the_seller_name']."',
'".$str_courier_bill_invoice_details['if_supplier_is_not_the_seller_address']."',
'".$str_courier_bill_invoice_details['broker_agent_details_name']."',
'".$str_courier_bill_invoice_details['broker_agent_details_address']."',
'".$str_courier_bill_invoice_details['nature_of_transaction']."',
'".$str_courier_bill_invoice_details['if_others']."',
'".$str_courier_bill_invoice_details['terms_of_payment']."',
'".$str_courier_bill_invoice_details['conditions_or_restrictions_if_any_attached_to_sale']."',
'".$str_courier_bill_invoice_details['method_of_valuation']."',
'".$str_courier_bill_invoice_details['terms_of_invoice']."', 
'".$str_courier_bill_invoice_details['invoice_value']."',
'".$str_courier_bill_invoice_details['currency']."',
'".$str_courier_bill_invoice_details['freight_rate']."',
'".$str_courier_bill_invoice_details['freight_amount'].",
'".$str_courier_bill_invoice_details['freight_currency']."', 
'".$str_courier_bill_invoice_details['insurance_rate']."',
'".$str_courier_bill_invoice_details['insurance_amount']."',
'".$str_courier_bill_invoice_details['insurance_currency']."',
'".$str_courier_bill_invoice_details['loading_unloading_and_handling_charges_rule_rate']."',
'".$str_courier_bill_invoice_details['loading_unloading_and_handling_charges_rule_amount']."',
'".$str_courier_bill_invoice_details['loading_unloading_and_handling_charges_rule_currency']."',
'".$str_courier_bill_invoice_details['other_charges_related_to_the_carriage_of_goods_rate']."',
'".$str_courier_bill_invoice_details['other_charges_related_to_the_carriage_of_goods_amount']."',
'".$str_courier_bill_invoice_details['other_charges_related_to_the_carriage_of_goods_currency']."',
'".$str_courier_bill_invoice_details['brokerage_and_commission_rate']."',
'".$str_courier_bill_invoice_details['brokerage_and_commission_amount']."',
'".$str_courier_bill_invoice_details['brokerage_and_commission_currency']."',
'".$str_courier_bill_invoice_details['cost_of_containers_rate']."',
'".$str_courier_bill_invoice_details['cost_of_containers_amount']."',
'".$str_courier_bill_invoice_details['cost_of_containers_currency']."',
'".$str_courier_bill_invoice_details['cost_of_packing_rate']."',
'".$str_courier_bill_invoice_details['cost_of_packing_amount']."',
'".$str_courier_bill_invoice_details['cost_of_packing_currency']."',
'".$str_courier_bill_invoice_details['dismantling_transport_handling_in_country_export_rate']."',
'".$str_courier_bill_invoice_details['dismantling_transport_handling_in_country_export_amount']."', 
'".$str_courier_bill_invoice_details['dismantling_transport_handling_in_country_export_currency']."',
'".$str_courier_bill_invoice_details['cost_of_goods_and_ser_vices_supplied_by_buyer_rate']."', 
'".$str_courier_bill_invoice_details['cost_of_goods_and_ser_vices_supplied_by_buyer_amount']."',
'".$str_courier_bill_invoice_details['cost_of_goods_and_ser_vices_supplied_by_buyer_currency']."',
'".$str_courier_bill_invoice_details['documentation_rate']."',
'".$str_courier_bill_invoice_details['documentation_amount']."',
'".$str_courier_bill_invoice_details['documentation_currency']."',
'".$str_courier_bill_invoice_details['country_of_origin_certificate_rate']."',
'".$str_courier_bill_invoice_details['country_of_origin_certificate_amount']."', 
'".$str_courier_bill_invoice_details['country_of_origin_certificate_currency']."',
'".$str_courier_bill_invoice_details['royalty_and_license_fees_rate']."',
'".$str_courier_bill_invoice_details['royalty_and_license_fees_amount']."',
'".$str_courier_bill_invoice_details['royalty_and_license_fees_currency']."',
'".$str_courier_bill_invoice_details['value_of_proceeds_which_accrue_to_seller_rate']."',
'".$str_courier_bill_invoice_details['value_of_proceeds_which_accrue_to_seller_amount']."',
'".$str_courier_bill_invoice_details['value_of_proceeds_which_accrue_to_seller_currency']."',
'".$str_courier_bill_invoice_details['cost_warranty_service_if_any_provided_seller_rate']."',
'".$str_courier_bill_invoice_details['cost_warranty_service_if_any_provided_seller_amount']."',
'".$str_courier_bill_invoice_details['cost_warranty_service_if_any_provided_seller_currency']."',
'".$str_courier_bill_invoice_details['other_payments_satisfy_obligation_rate']."',
'".$str_courier_bill_invoice_details['other_payments_satisfy_obligation_amount']."',
'".$str_courier_bill_invoice_details['other_payments_satisfy_obligation_currency']."',
'".$str_courier_bill_invoice_details['other_charges_and_payments_if_any_rate']."',
'".$str_courier_bill_invoice_details['other_charges_and_payments_if_any_amount']."',   
'".$str_courier_bill_invoice_details['other_charges_and_payments_if_any_currency']."',
'".$str_courier_bill_invoice_details['discount_amount`, `discount_currency']."', 
'".$str_courier_bill_invoice_details['rate']."',
'".$str_courier_bill_invoice_details['amount']."',
'".$str_courier_bill_invoice_details['any_other_information_which_has_a_bearing_on_value']."',
'".$str_courier_bill_invoice_details['are_the_buyer_and_seller_related']."',
'".$str_courier_bill_invoice_details['if_the_buyer_seller_has_the_relationship_examined_earlier_svb']."', 
'".$str_courier_bill_invoice_details['svb_reference_number']."',
'".$str_courier_bill_invoice_details['svb_date']."',
'".$str_courier_bill_invoice_details['indication_for_provisional_final']."',
'".$str_courier_bill_invoice_details['created_at']."')";    
$copy_insert_courier_bill_invoice_details = $db1_courier_bill_invoice_details->query($sql_insert_courier_bill_invoice_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_invoice_details***************************************************************************************/
			
	/******************************************************************Start courier_bill_items_details***************************************************************************************/
	
            $query_courier_bill_items_details = "SELECT courier_bill_items_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_items_details LEFT JOIN cb_file_status ON courier_bill_items_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id";
            $statement_courier_bill_items_details = $this->db->query($query_courier_bill_items_details);
            $iecwise_courier_bill_items_details=array();
            $result_courier_bill_items_details =$statement_courier_bill_items_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_items_details as $str_courier_bill_items_details){
                     $iec_courier_bill_items_details=$str_courier_bill_items_details['user_iec_no'];
                    $sql_courier_bill_items_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_items_details'";
                    $iecwise_courier_bill_items_details= $this->db->query($sql_courier_bill_items_details);
                    $iecwise_data_courier_bill_items_details =$iecwise_courier_bill_items_details->result_array();
                    $db1_courier_bill_items_details=$this->database_connection($iecwise_data_courier_bill_items_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_items_details = "INSERT INTO `courier_bill_items_details`
 (`items_detail_id`, `case_for_reimport`, `import_against_license`, `serial_number_in_invoice`, `item_description`, 
 `general_description`, `currency_for_unit_price`, `unit_price`, `unit_of_measure`, `quantity`, `rate_of_exchange`,
 `accessories_if_any`, `name_of_manufacturer`, `brand`, `model`, `grade`,
 `specification`, `end_use_of_item`, `items_details_country_of_origin`, `bill_of_entry_number`,
 `details_in_case_of_previous_imports_date`, `details_in_case_previous_imports_currency`, `unit_value`,
 `customs_house`, `ritc`, `ctsh`, `cetsh`, `currency_for_rsp`, `retail_sales_price_per_unit`, `exim_scheme_code_if_any`,
 `para_noyear_of_exim_policy`, `items_details_are_the_buyer_and_seller_related`, `if_the_buyer_and_seller_relation_examined_earlier_by_svb`,
 `items_details_svb_reference_number`, `items_details_svb_date`, `items_details_indication_for_provisional_final`, `shipping_bill_number`,
 `shipping_bill_date`, `port_of_export`, `invoice_number_of_shipping_bill`, `item_serial_number_in_shipping_bill`, `freight`, 
 `insurance`, `total_repair_cost_including_cost_of_materials`, `additional_duty_exemption_requested`, `items_details_notification_number`, 
 `serial_number_in_notification`, `license_registration_number`, `license_registration_date`, `debit_value_rs`, 
 `unit_of_measure_for_quantity_to_be_debited`, `debit_quantity`, `item_serial_number_in_license`, `assessable_value`, `created_at`) 
VALUES('".$str_courier_bill_items_details['items_detail_id']."',
'".$str_courier_bill_items_details['case_for_reimport']."',
'".$str_courier_bill_items_details['import_against_license']."',
'".$str_courier_bill_items_details['serial_number_in_invoice']."',
'".$str_courier_bill_items_details['item_description']."',
'".$str_courier_bill_items_details['general_description']."',
'".$str_courier_bill_items_details['currency_for_unit_price']."',
'".$str_courier_bill_items_details['unit_price']."',
'".$str_courier_bill_items_details['unit_of_measure']."',
'".$str_courier_bill_items_details['quantity']."',
'".$str_courier_bill_items_details['rate_of_exchange']."',
'".$str_courier_bill_items_details['accessories_if_any']."',
'".$str_courier_bill_items_details['name_of_manufacturer']."',
'".$str_courier_bill_items_details['brand']."',
'".$str_courier_bill_items_details['model']."',
'".$str_courier_bill_items_details['grade']."',
'".$str_courier_bill_items_details['specification']."',
'".$str_courier_bill_items_details['end_use_of_item']."',
'".$str_courier_bill_items_details['items_details_country_of_origin']."',
'".$str_courier_bill_items_details['bill_of_entry_number']."',
'".$str_courier_bill_items_details['details_in_case_of_previous_imports_date']."',
'".$str_courier_bill_items_details['details_in_case_previous_imports_currency']."',
'".$str_courier_bill_items_details['unit_value']."',
'".$str_courier_bill_items_details['customs_house']."',
'".$str_courier_bill_items_details['ritc']."',
'".$str_courier_bill_items_details['ctsh']."',
'".$str_courier_bill_items_details['cetsh']."',
'".$str_courier_bill_items_details['currency_for_rsp']."',
'".$str_courier_bill_items_details['retail_sales_price_per_unit']."',
'".$str_courier_bill_items_details['exim_scheme_code_if_any']."',
'".$str_courier_bill_items_details['para_noyear_of_exim_policy']."',
'".$str_courier_bill_items_details['items_details_are_the_buyer_and_seller_related']."',
'".$str_courier_bill_items_details['if_the_buyer_and_seller_relation_examined_earlier_by_svb']."',
'".$str_courier_bill_items_details['items_details_svb_reference_number']."',
'".$str_courier_bill_items_details['items_details_svb_date']."',
'".$str_courier_bill_items_details['items_details_indication_for_provisional_final']."',
'".$str_courier_bill_items_details['shipping_bill_number']."',
'".$str_courier_bill_items_details['shipping_bill_date']."',
'".$str_courier_bill_items_details['port_of_export']."',
'".$str_courier_bill_items_details['invoice_number_of_shipping_bill']."',
'".$str_courier_bill_items_details['item_serial_number_in_shipping_bill']."',
'".$str_courier_bill_items_details['freight']."',
'".$str_courier_bill_items_details['insurance']."',
'".$str_courier_bill_items_details['total_repair_cost_including_cost_of_materials']."',
'".$str_courier_bill_items_details['additional_duty_exemption_requested']."',
'".$str_courier_bill_items_details['items_details_notification_number']."',
'".$str_courier_bill_items_details['serial_number_in_notification']."',
'".$str_courier_bill_items_details['license_registration_number']."',
'".$str_courier_bill_items_details['license_registration_date']."',
'".$str_courier_bill_items_details['debit_value_rs']."',
'".$str_courier_bill_items_details['unit_of_measure_for_quantity_to_be_debited']."',
'".$str_courier_bill_items_details['debit_quantity']."',
'".$str_courier_bill_items_details['item_serial_number_in_license']."',
'".$str_courier_bill_items_details['assessable_value']."',
'".$str_courier_bill_items_details['assessable_value']."',
'".$str_courier_bill_items_details['created_at']."')";      
$copy_insert_courier_bill_items_details = $db1_insert_courier_bill_items_details->query($sql_insert_courier_bill_items_details);          
        }
	
	
	/******************************************************************Start courier_bill_items_details***************************************************************************************/
				
	/******************************************************************Start courier_bill_manifest_details***************************************************************************************/
	
            $query_courier_bill_manifest_details = "SELECT courier_bill_manifest_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_manifest_details LEFT JOIN cb_file_status ON courier_bill_manifest_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id";
            $statement_courier_bill_manifest_details = $this->db->query($query_courier_bill_manifest_details);
            $iecwise_courier_bill_manifest_details=array();
            $result_courier_bill_manifest_details =$statement_courier_bill_manifest_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_manifest_details as $str_courier_bill_manifest_details){
                     $iec_courier_bill_manifest_details=$str_courier_bill_manifest_details['user_iec_no'];
                    $sql_courier_bill_manifest_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_manifest_details'";
                    $iecwise_courier_bill_manifest_details= $this->db->query($sql_courier_bill_manifest_details);
                    $iecwise_data_courier_bill_manifest_details =$iecwise_courier_bill_manifest_details->result_array();
                    $db1_courier_bill_manifest_details=$this->database_connection($iecwise_data_courier_bill_manifest_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_manifest_details = "INSERT INTO `courier_bill_manifest_details` ( `manifest_details_id`, `import_general_manifest_igm_number`, `date_of_entry_inward`, `master_airway_bill_mawb_number`, `date_of_mawb`, `house_airway_bill_hawb_number`, `date_of_hawb`, `marks_and_numbers`, `number_of_packages`, `type_of_packages`, `interest_amount`, `unit_of_measure_for_gross_weight`, `gross_weight`, `created_at`)
VALUES('".$str_courier_bill_manifest_details['manifest_details_id']."','".$str_courier_bill_manifest_details['import_general_manifest_igm_number']."','".$str_courier_bill_manifest_details['date_of_entry_inward']."','".$str_courier_bill_manifest_details['master_airway_bill_mawb_number']."','".$str_courier_bill_manifest_details['date_of_mawb']."','".$str_courier_bill_manifest_details['house_airway_bill_hawb_number']."','".$str_courier_bill_manifest_details['date_of_hawb']."','".$str_courier_bill_manifest_details['marks_and_numbers']."','".$str_courier_bill_manifest_details['number_of_packages']."','".$str_courier_bill_manifest_details['type_of_packages']."','".$str_courier_bill_manifest_details['interest_amount']."','".$str_courier_bill_manifest_details['unit_of_measure_for_gross_weight']."','".$str_courier_bill_manifest_details['gross_weight']."','".$str_courier_bill_manifest_details['created_at']."')";      
$copy_insert_courier_bill_manifest_details = $db1_insert_courier_bill_manifest_details->query($sql_insert_courier_bill_igm_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_manifest_details***************************************************************************************/


/******************************************************************Start courier_bill_manifest_details***************************************************************************************/
	
            $query_courier_bill_manifest_details = "SELECT courier_bill_manifest_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_manifest_details LEFT JOIN cb_file_status ON courier_bill_manifest_details.courier_bill_of_entry_id = cb_file_status.cb_file_status_id ";
            $statement_courier_bill_manifest_details = $this->db->query($query_courier_bill_manifest_details);
            $iecwise_courier_bill_manifest_details=array();
            $result_courier_bill_manifest_details =$statement_courier_bill_manifest_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_manifest_details as $str_courier_bill_manifest_details){
                     $iec_courier_bill_manifest_details=$str_courier_bill_manifest_details['user_iec_no'];
                    $sql_courier_bill_manifest_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_manifest_details'";
                    $iecwise_courier_bill_manifest_details= $this->db->query($sql_courier_bill_manifest_details);
                    $iecwise_data_courier_bill_manifest_details =$iecwise_courier_bill_manifest_details->result_array();
                    $db1_courier_bill_manifest_details=$this->database_connection($iecwise_data_courier_bill_manifest_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_manifest_details = "INSERT INTO `courier_bill_manifest_details` ( `manifest_details_id`, `import_general_manifest_igm_number`, `date_of_entry_inward`, `master_airway_bill_mawb_number`, `date_of_mawb`, `house_airway_bill_hawb_number`, `date_of_hawb`, `marks_and_numbers`, `number_of_packages`, `type_of_packages`, `interest_amount`, `unit_of_measure_for_gross_weight`, `gross_weight`, `created_at`)
VALUES('".$str_courier_bill_manifest_details['manifest_details_id']."','".$str_courier_bill_manifest_details['import_general_manifest_igm_number']."','".$str_courier_bill_manifest_details['date_of_entry_inward']."','".$str_courier_bill_manifest_details['master_airway_bill_mawb_number']."','".$str_courier_bill_manifest_details['date_of_mawb']."','".$str_courier_bill_manifest_details['house_airway_bill_hawb_number']."','".$str_courier_bill_manifest_details['date_of_hawb']."','".$str_courier_bill_manifest_details['marks_and_numbers']."','".$str_courier_bill_manifest_details['number_of_packages']."','".$str_courier_bill_manifest_details['type_of_packages']."','".$str_courier_bill_manifest_details['interest_amount']."','".$str_courier_bill_manifest_details['unit_of_measure_for_gross_weight']."','".$str_courier_bill_manifest_details['gross_weight']."','".$str_courier_bill_manifest_details['created_at']."')";      
$copy_insert_courier_bill_manifest_details = $db1_insert_courier_bill_manifest_details->query($sql_insert_courier_bill_igm_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_manifest_details***************************************************************************************/


/******************************************************************Start courier_bill_notification_used_for_items***************************************************************************************/
	
            $query_courier_bill_notification_used_for_items = "SELECT courier_bill_notification_used_for_items.*,courier_bill_items_details.items_detail_id,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_notification_used_for_items LEFT JOIN courier_bill_items_details ON courier_bill_notification_used_for_items.items_detail_id=courier_bill_items_details.items_detail_id LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id";
            $statement_courier_bill_notification_used_for_items = $this->db->query($query_courier_bill_notification_used_for_items);
            $iecwise_courier_bill_notification_used_for_items=array();
            $result_courier_bill_notification_used_for_items =$statement_courier_bill_notification_used_for_items->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_notification_used_for_items as $str_courier_bill_notification_used_for_items){
                     $iec_courier_bill_notification_used_for_items=$str_courier_bill_notification_used_for_items['user_iec_no'];
                    $sql_courier_bill_notification_used_for_items = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_notification_used_for_items'";
                    $iecwise_courier_bill_notification_used_for_items= $this->db->query($sql_courier_bill_notification_used_for_items);
                    $iecwise_data_courier_bill_notification_used_for_items =$iecwise_courier_bill_notification_used_for_items->result_array();
                    $db1_courier_bill_notification_used_for_items=$this->database_connection($iecwise_data_courier_bill_notification_used_for_items[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_notification_used_for_items = "INSERT INTO `courier_bill_notification_used_for_items` ( `items_detail_id`, `notification_item_srno`, `notification_number`, `serial_number_of_notification`, `created_at`)
VALUES('".$str_courier_bill_notification_used_for_items['items_detail_id']."','".$str_courier_bill_notification_used_for_items['notification_item_srno']."','".$str_courier_bill_notification_used_for_items['notification_number']."','".$str_courier_bill_notification_used_for_items['serial_number_of_notification']."','".$str_courier_bill_notification_used_for_items['created_at']."')";      
$copy_insert_courier_bill_notification_used_for_items = $db1_insert_courier_bill_notification_used_for_items->query($sql_insert_courier_bill_igm_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_notification_used_for_items***************************************************************************************/
		
	/******************************************************************Start courier_bill_payment_details***************************************************************************************/
	
            $query_courier_bill_payment_details = "SELECT courier_bill_payment_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_payment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_payment_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id ";
            $statement_courier_bill_payment_details = $this->db->query($query_courier_bill_payment_details);
            $iecwise_courier_bill_payment_details=array();
            $result_courier_bill_payment_details =$statement_courier_bill_payment_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_payment_details as $str_courier_bill_payment_details){
                     $iec_courier_bill_payment_details=$str_courier_bill_payment_details['user_iec_no'];
                    $sql_courier_bill_payment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_payment_details'";
                    $iecwise_courier_bill_payment_details= $this->db->query($sql_courier_bill_payment_details);
                    $iecwise_data_courier_bill_payment_details =$iecwise_courier_bill_payment_details->result_array();
                    $db1_courier_bill_payment_details=$this->database_connection($iecwise_data_courier_bill_payment_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_payment_details = "INSERT INTO `courier_bill_payment_details` (`courier_bill_of_entry_id`, `payment_details_id`, `payment_details_srno`, `tr6_challan_number`, `total_amount`, `challan_date`, `created_at`)
VALUES('".$str_courier_bill_payment_details['courier_bill_of_entry_id']."','".$str_courier_bill_payment_details['payment_details_id']."','".$str_courier_bill_payment_details['payment_details_srno']."','".$str_courier_bill_payment_details['tr6_challan_number']."','".$str_courier_bill_payment_details['total_amount']."','".$str_courier_bill_payment_details['challan_date']."','".$str_courier_bill_payment_details['created_at']."')";      
$copy_insert_courier_bill_payment_details = $db1_insert_courier_bill_payment_details->query($sql_insert_courier_bill_igm_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_payment_details***************************************************************************************/


			
	/******************************************************************Start courier_bill_procurment_details***************************************************************************************/
	
            $query_courier_bill_procurment_details = "SELECT courier_bill_procurment_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_procurment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_procurment_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id ";
            $statement_courier_bill_procurment_details = $this->db->query($query_courier_bill_procurment_details);
            $iecwise_courier_bill_procurment_details=array();
            $result_courier_bill_procurment_details =$statement_courier_bill_procurment_details->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_procurment_details as $str_courier_bill_procurment_details){
                     $iec_courier_bill_procurment_details=$str_courier_bill_procurment_details['user_iec_no'];
                    $sql_courier_bill_procurment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_procurment_details'";
                    $iecwise_courier_bill_procurment_details= $this->db->query($sql_courier_bill_procurment_details);
                    $iecwise_data_courier_bill_procurment_details =$iecwise_courier_bill_procurment_details->result_array();
                    $db1_courier_bill_procurment_details=$this->database_connection($iecwise_data_courier_bill_procurment_details[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_procurment_details = "INSERT INTO `courier_bill_procurment_details`(`courier_bill_of_entry_id`,`procurment_details_id`, `procurement_under_3696_cus`, `procurement_certificate_number`,`date_of_issuance_of_certificate`, `location_code_of_the_cent_ral_excise_office_issuing_the_certifi`, `commissione_rate`,`division`, `range`, `import_under_multiple_in_voices`,`created_at`)
 VALUES (''".$str_courier_bill_procurment_details['courier_bill_of_entry_id']."',
 '".$str_courier_bill_procurment_details['procurment_details_id']."',
 '".$str_courier_bill_procurment_details['procurement_under_3696_cus']."',
 '".$str_courier_bill_procurment_details['procurement_certificate_number']."',
 '".$str_courier_bill_procurment_details['date_of_issuance_of_certificate']."',
 '".$str_courier_bill_procurment_details['location_code_of_the_cent_ral_excise_office_issuing_the_certifi']."',
 '".$str_courier_bill_procurment_details['commissione_rate']."',
 '".$str_courier_bill_procurment_details['division']."',
 '".$str_courier_bill_procurment_details['range']."',
 '".$str_courier_bill_procurment_details['import_under_multiple_in_voices']."',
 '".$str_courier_bill_procurment_details['created_at']."')";      
$copy_insert_courier_bill_procurment_details = $db1_insert_courier_bill_procurment_details->query($sql_insert_courier_bill_igm_details);    
            
        }
	
	
	/******************************************************************Start courier_bill_procurment_details***************************************************************************************/

	/******************************************************************Start courier_bill_summary***************************************************************************************/
	
            $query_courier_bill_summary = "SELECT courier_bill_summary.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_summary LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id";
            $statement_courier_bill_summary = $this->db->query($query_courier_bill_summary);
            $iecwise_courier_bill_summary=array();
            $result_courier_bill_summary =$statement_courier_bill_summary->result_array();
            //print_r($result);
        
        foreach($result_courier_bill_summary as $str_courier_bill_summary){
                     $iec_courier_bill_summary=$str_courier_bill_summary['user_iec_no'];
                    $sql_courier_bill_summary = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_courier_bill_summary'";
                    $iecwise_courier_bill_summary= $this->db->query($sql_courier_bill_summary);
                    $iecwise_data_courier_bill_summary =$iecwise_courier_bill_summary->result_array();
                    $db1_courier_bill_summary=$this->database_connection($iecwise_data_courier_bill_summary[0]['lucrative_users_id']);
            
$sql_insert_courier_bill_summary = "INSERT INTO `courier_bill_summary`(`courier_bill_of_entry_id`, `cb_file_status_id`, `current_status_of_the_cbe`, `cbexiv_number`, `courier_registration_number`, `name_of_the_authorized_courier`, `address_of_authorized_courier`, `particulars_customs_house_agent_name`, `particulars_customs_house_agent_licence_no`, `particulars_customs_house_agent_address`, 
`import_export_code`, `import_export_branch_code`, `particulars_of_the_importer_name`, `particulars_of_the_importer_address`, 
`category_of_importer`, `type_of_importer`, `in_case_of_other_importer`, `authorised_dealer_code_of_bank`, `class_code`, `cb_no`, `cb_date`,
 `category_of_boe`, `type_of_boe`, `kyc_document`, `kyc_id`, `state_code`, `high_sea_sale`, `ie_code_of_hss`, `ie_branch_code_of_hss`, 
 `particulars_high_sea_seller_name`, `particulars_high_sea_seller_address`, `use_of_the_first_proviso_under_section_461customs_act1962`,
 `request_for_first_check`, `request_for_urgent_clear_ance_against_temporary_documentation`, `request_for_extension_of_time_limit_as_per_section_48customs_ac`, 
 `reason_in_case_extension_of_time_limit_is_requested`, `country_of_origin`, `country_of_consignment`, `name_of_gateway_port`, `gateway_igm_number`, 
 `date_of_entry_inwards_of_gateway_port`, `case_of_crn`, `number_of_invoices`, `total_freight`, `total_insurance`, `created_at`)
 VALUES (''".$str_courier_bill_summary['courier_bill_of_entry_id']."',
 '".$str_courier_bill_summary['cb_file_status_id']."',
 '".$str_courier_bill_summary['current_status_of_the_cbe']."',
 '".$str_courier_bill_summary['cbexiv_number']."',
 '".$str_courier_bill_summary['courier_registration_number']."',
 '".$str_courier_bill_summary['name_of_the_authorized_courier']."',
 '".$str_courier_bill_summary['address_of_authorized_courier']."',
 '".$str_courier_bill_summary['particulars_customs_house_agent_name']."',
 '".$str_courier_bill_summary['particulars_customs_house_agent_licence_no']."',
 '".$str_courier_bill_summary['particulars_customs_house_agent_address']."',
 '".$str_courier_bill_summary['import_export_code']."',
 '".$str_courier_bill_summary['import_export_branch_code']."',
 '".$str_courier_bill_summary['particulars_of_the_importer_name']."',
 '".$str_courier_bill_summary['particulars_of_the_importer_address']."',
 '".$str_courier_bill_summary['category_of_importer']."',
 '".$str_courier_bill_summary['type_of_importer']."',
'".$str_courier_bill_summary['in_case_of_other_importer']."',
'".$str_courier_bill_summary['authorised_dealer_code_of_bank']."',
'".$str_courier_bill_summary['class_code']."',
'".$str_courier_bill_summary['cb_no']."',
'".$str_courier_bill_summary['cb_date']."',
'".$str_courier_bill_summary['type_of_boe']."',
'".$str_courier_bill_summary['kyc_document']."',
'".$str_courier_bill_summary['kyc_id']."',
'".$str_courier_bill_summary['state_code']."',
'".$str_courier_bill_summary['high_sea_sale']."',
'".$str_courier_bill_summary['ie_code_of_hss']."',
'".$str_courier_bill_summary['particulars_high_sea_seller_name']."',
'".$str_courier_bill_summary['particulars_high_sea_seller_address']."',
'".$str_courier_bill_summary['use_of_the_first_proviso_under_section_461customs_act1962']."',
'".$str_courier_bill_summary['request_for_first_check']."',
'".$str_courier_bill_summary['request_for_urgent_clear_ance_against_temporary_documentation']."',
'".$str_courier_bill_summary['request_for_extension_of_time_limit_as_per_section_48customs_ac']."',
".$str_courier_bill_summary['reason_in_case_extension_of_time_limit_is_requested']."',
".$str_courier_bill_summary['country_of_origin']."',
".$str_courier_bill_summary['country_of_consignment']."',
".$str_courier_bill_summary['name_of_gateway_port']."',
".$str_courier_bill_summary['gateway_igm_number']."',
".$str_courier_bill_summary['date_of_entry_inwards_of_gateway_port']."',
".$str_courier_bill_summary['case_of_crn']."',
".$str_courier_bill_summary['number_of_invoices']."',
".$str_courier_bill_summary['total_freight']."',
".$str_courier_bill_summary['total_insurance']."',
'".$str_courier_bill_summary['created_at']."')";       
$copy_insert_courier_bill_summary = $db1_insert_courier_bill_summary->query($sql_insert_courier_bill_summary);    
            
        }
	
	
	/******************************************************************Start courier_bill_summary***************************************************************************************/
		
	/******************************************************************Start item_manufacturer_details***************************************************************************************/
	
            /*$query_item_manufacturer_details = "SELECT item_manufacturer_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM item_manufacturer_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=item_manufacturer_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id WHERE date(item_manufacturer_details.created_at) = CURRENT_DATE LIMIT 5";
            $statement_item_manufacturer_details = $this->db->query($query_item_manufacturer_details);
            $iecwise_item_manufacturer_details=array();
            $result_item_manufacturer_details =$statement_item_manufacturer_details->result_array();
            //print_r($result);
        
        foreach($result_item_manufacturer_details as $str_item_manufacturer_details){
                     $iec_item_manufacturer_details=$str_item_manufacturer_details['user_iec_no'];
                    $sql_item_manufacturer_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_item_manufacturer_details'";
                    $iecwise_item_manufacturer_details= $this->db->query($sql_item_manufacturer_details);
                    $iecwise_data_item_manufacturer_details =$iecwise_item_manufacturer_details->result_array();
                    $db1_item_manufacturer_details=$this->database_connection($iecwise_data_item_manufacturer_details[0]['lucrative_users_id']);
            
$sql_insert_item_manufacturer_details = "INSERT INTO `item_manufacturer_details` (`courier_bill_of_entry_id`, `payment_details_id`, `payment_details_srno`, `tr6_challan_number`, `total_amount`, `challan_date`, `created_at`)
VALUES('".$str_item_manufacturer_details['courier_bill_of_entry_id']."','".$str_item_manufacturer_details['payment_details_id']."','".$str_item_manufacturer_details['payment_details_srno']."','".$str_item_manufacturer_details['tr6_challan_number']."','".$str_item_manufacturer_details['total_amount']."','".$str_item_manufacturer_details['challan_date']."','".$str_item_manufacturer_details['created_at']."')";      
$copy_insert_item_manufacturer_details = $db1_insert_item_manufacturer_details->query($sql_insert_item_manufacturer_details);    
            
        }*/
	
	
	/******************************************************************Start item_manufacturer_details***************************************************************************************/


	/******************************************************************Start item_details***************************************************************************************/
	
            $query_item_details = "SELECT item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
            $statement_item_details = $this->db->query($query_item_details);
            $iecwise_item_details=array();
            $result_item_details =$statement_item_details->result_array();
            //print_r($result);
        
        foreach($result_item_details as $str_item_details){
                     $iec_item_details=$str_item_details['iec'];
                    $sql_item_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_item_details'";
                    $iecwise_item_details= $this->db->query($sql_item_details);
                    $iecwise_data_item_details =$iecwise_item_details->result_array();
                    $db1_item_details=$this->database_connection($iecwise_data_item_details[0]['lucrative_users_id']);
            
$sql_insert_item_details = "INSERT INTO `item_details`(`item_id`, `invoice_id`, `invsn`, `item_s_no`, `hs_cd`, `description`, `quantity`, `uqc`, `rate`, `value_f_c`, `fob_inr`, `pmv`, `duty_amt`, `cess_rt`,`cesamt`, `dbkclmd`,
 `igststat`,
 `igst_value_item`,
 `igst_amount`,
 `schcod`, 
 `scheme_description`,
 `sqc_msr`,
 `sqc_uqc`,
 `state_of_origin_i`, 
 `district_of_origin`,
 `pt_abroad`,
 `comp_cess`, 
 `end_use`, 
 `fta_benefit_availed`, 
 `reward_benefit`, 
 `third_party_item`, 
 `created_at`) 
 VALUES ('".$str_item_details['item_id']."',
 '".$str_item_details['invoice_id']."',
 '".$str_item_details['invsn']."',
 '".$str_item_details['item_s_no']."',
 '".$str_item_details['hs_cd']."',
 '".$str_item_details['description']."',
 '".$str_item_details['quantity']."',,
 '".$str_item_details['uqc']."',,
 '".$str_item_details['rate']."',,
 '".$str_item_details['value_f_c']."',
 '".$str_item_details['fob_inr']."',
 '".$str_item_details['pmv']."',
 '".$str_item_details['duty_amt']."',
 '".$str_item_details['cess_rt']."',
 '".$str_item_details['cesamt']."',
 '".$str_item_details['dbkclmd']."',
 '".$str_item_details['igststat']."',
 '".$str_item_details['igst_value_item']."',
 '".$str_item_details['igst_amount']."',
 '".$str_item_details['schcod']."',
 '".$str_item_details['scheme_description']."',
 '".$str_item_details['sqc_msr']."',
 '".$str_item_details['sqc_uqc']."',
 '".$str_item_details['state_of_origin_i']."',
 '".$str_item_details['district_of_origin']."',
 '".$str_item_details['pt_abroad']."',
 '".$str_item_details['comp_cess']."',
 '".$str_item_details['end_use']."',
 '".$str_item_details['fta_benefit_availed']."',
 '".$str_item_details['reward_benefit']."',
 '".$str_item_details['third_party_item']."'.,
 '".$str_item_details['created_at']."')";      
$copy_insert_item_details = $db1_item_details->query($sql_insert_item_details);    
            
        }
	
	
	/******************************************************************Start item_details***************************************************************************************/
	
	/******************************************************************Start rodtep_details***************************************************************************************/
	
            $query_rodtep_details = "SELECT rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
            $statement_rodtep_details = $this->db->query($query_rodtep_details);
            $iecwise_rodtep_details=array();
            $result_rodtep_details =$statement_rodtep_details->result_array();
            //print_r($result);
        
        foreach($result_rodtep_details as $str_rodtep_details){
                     $iec_rodtep_details=$str_rodtep_details['iec'];
                    $sql_rodtep_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_rodtep_details'";
                    $iecwise_rodtep_details= $this->db->query($sql_rodtep_details);
                    $iecwise_data_rodtep_details =$iecwise_rodtep_details->result_array();
                    $db1_rodtep_details=$this->database_connection($iecwise_data_rodtep_details[0]['lucrative_users_id']);
$sql_insert_rodtep_details = "INSERT INTO `rodtep_details` (`item_id`, `inv_sno`, `item_sno`, `quantity`, `uqc`, `	no_of_units`,`value`) 
VALUES('".$str_rodtep_details['item_id']."','".$str_rodtep_details['inv_sno']."','".$str_rodtep_details['item_sno']."','".$str_rodtep_details['quantity']."','".$str_rodtep_details['uqc']."','".$str_rodtep_details['no_of_units']."','".$str_rodtep_details['value']."')";            
$copy_insert_rodtep_details = $db1_rodtep_details->query($sql_insert_rodtep_details);    
            
        }
	
	
	/******************************************************************Start rodtep_details***************************************************************************************/
	
		/******************************************************************Start jobbing_details***************************************************************************************/
	
            $query_jobbing_details = "SELECT jobbing_details.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM jobbing_details LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id";
            $statement_jobbing_details = $this->db->query($query_jobbing_details);
            $iecwise_jobbing_details=array();
            $result_jobbing_details =$statement_jobbing_details->result_array();
            //print_r($result);
        
        foreach($result_jobbing_details as $str_jobbing_details){
                     $iec_jobbing_details=$str_jobbing_details['iec'];
                    $sql_jobbing_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_jobbing_details'";
                    $iecwise_jobbing_details= $this->db->query($sql_jobbing_details);
                    $iecwise_data_jobbing_details =$iecwise_jobbing_details->result_array();
                    $db1_jobbing_details=$this->database_connection($iecwise_data_jobbing_details[0]['lucrative_users_id']);
$sql_insert_jobbing_details = "INSERT INTO `jobbing_details`(`jobbing_detail_id`, `sbs_id`, `be_no`, `be_date`, `port_code_j`, `descn_of_imported_goods`,  `qty_imp`,  `qty_used`, `created_at`)
 VALUES ('".$str_jobbing_details['jobbing_detail_id']."',
 '".$str_jobbing_details['sbs_id']."',
 '".$str_jobbing_details['be_no']."',
 '".$str_jobbing_details['be_date']."',
 '".$str_jobbing_details['port_code_j']."',
 '".$str_jobbing_details['descn_of_imported_goods']."',
 '".$str_jobbing_details['qty_imp']."',
 '".$str_jobbing_details['qty_used']."',
 '".$str_jobbing_details['created_at']."')";            
$copy_insert_jobbing_details = $db1_insert_jobbing_details->query($sql_insert_jobbing_details);    
            
        }
	
	
	/******************************************************************Start jobbing_details***************************************************************************************/

	/******************************************************************Start ship_bill_summary***************************************************************************************/
	
            $query_ship_bill_summary = "SELECT * FROM ship_bill_summary";
            $statement_ship_bill_summary = $this->db->query($query_ship_bill_summary);
            $iecwise_ship_bill_summary=array();
            $result_ship_bill_summary =$statement_ship_bill_summary->result_array();
            //print_r($result);
        
        foreach($result_ship_bill_summary as $str_ship_bill_summary){
                     $iec_ship_bill_summary=$str_ship_bill_summary['iec'];
                    $sql_ship_bill_summary = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_ship_bill_summary'";
                    $iecwise_ship_bill_summary= $this->db->query($sql_ship_bill_summary);
                    $iecwise_data_ship_bill_summary =$iecwise_ship_bill_summary->result_array();
                    $db1_ship_bill_summary=$this->database_connection($iecwise_data_ship_bill_summary[0]['lucrative_users_id']);
$sql_insert_ship_bill_summary = "INSERT INTO `ship_bill_summary` (`sbs_id`, `sb_file_status_id`, `invoice_title`, `port_code`, `sb_no`, `sb_date`, `iec`, `br`, `iec_br`, `gstin`, `type`, `cb_code`, `inv_nos`, `item_no`, `cont_no`, `address`, `pkg`, `g_wt_unit`, `g_wt_value`, `mode`, `assess`, `exmn`, `jobbing`, `meis`, `dbk`, `rodtp`, `deec_dfia`, `dfrc`, `reexp`, `lut`, `port_of_loading`, `country_of_finaldestination`, `state_of_origin`, `port_of_finaldestination`, `port_of_discharge`, `country_of_discharge`, `exporter_name_and_address`, `consignee_name_and_address`, `declarant_type`, `ad_code`, `gstin_type_`, `rbi_waiver_no_and_dt`, `forex_bank_account_no`, `cb_name`, `dbk_bank_account_no`, `aeo`, `ifsc_code`, `fob_value_sum`, `freight`, `insurance`, `discount`, `com`, `deduction`, `p_c`, `duty`, `cess`, `dbk_claim`, `igst_amt`, `cess_amt`, `igst_value`, `rodtep_amt`, `rosctl_amt`, `mawb_no`, `mawb_dt`, `hawb_no`, `hawb_dt`, `noc`, `cin_no`, `cin_dt`, `cin_site_id`, `seal_type`, `nature_of_cargo`, `no_of_packets`, `no_of_containers`, `loose_packets`, `marks_and_numbers`, `submission_date`, `assessment_date`, `examination_date`, `leo_date`, `submission_time`, `assessment_time`, `examination_time`, `leo_time`, `leo_no`, `leo_dt`, `brc_realisation_date`, `created_at`) VALUES
('".$str_ship_bill_summary['sbs_id']."','".$str_ship_bill_summary['sb_file_status_id']."','".$str_ship_bill_summary['invoice_title']."','".$str_ship_bill_summary['port_code']."','".$str_ship_bill_summary['sb_no']."','".$str_ship_bill_summary['sb_date']."','".$str_ship_bill_summary['iec']."','".$str_ship_bill_summary['br']."','".$str_ship_bill_summary['iec_br']."','".$str_ship_bill_summary['gstin']."','".$str_ship_bill_summary['type']."','".$str_ship_bill_summary['cb_code']."','".$str_ship_bill_summary['inv_nos']."','".$str_ship_bill_summary['item_no']."','".$str_ship_bill_summary['cont_no']."','".$str_ship_bill_summary['address']."','".$str_ship_bill_summary['pkg']."','".$str_ship_bill_summary['g_wt_unit']."','".$str_ship_bill_summary['g_wt_value']."','".$str_ship_bill_summary['mode']."','".$str_ship_bill_summary['assess']."','".$str_ship_bill_summary['exmn']."','".$str_ship_bill_summary['jobbing']."','".$str_ship_bill_summary['meis']."','".$str_ship_bill_summary['dbk']."','".$str_ship_bill_summary['rodtp']."','".$str_ship_bill_summary['deec_dfia']."','".$str_ship_bill_summary['dfrc']."','".$str_ship_bill_summary['reexp']."','".$str_ship_bill_summary['lut']."','".$str_ship_bill_summary['port_of_loading']."','".$str_ship_bill_summary['country_of_finaldestination']."','".$str_ship_bill_summary['state_of_origin']."','".$str_ship_bill_summary['port_of_finaldestination']."','".$str_ship_bill_summary['port_of_discharge']."','".$str_ship_bill_summary['country_of_discharge']."','".$str_ship_bill_summary['exporter_name_and_address']."','".$str_ship_bill_summary['consignee_name_and_address']."','".$str_ship_bill_summary['declarant_type']."','".$str_ship_bill_summary['ad_code']."','".$str_ship_bill_summary['gstin_type_']."','".$str_ship_bill_summary['rbi_waiver_no_and_dt']."','".$str_ship_bill_summary['forex_bank_account_no']."','".$str_ship_bill_summary['cb_name']."','".$str_ship_bill_summary['dbk_bank_account_no']."','".$str_ship_bill_summary['aeo']."','".$str_ship_bill_summary['ifsc_code']."','".$str_ship_bill_summary['fob_value_sum']."','".$str_ship_bill_summary['freight']."','".$str_ship_bill_summary['insurance']."','".$str_ship_bill_summary['discount']."','".$str_ship_bill_summary['com']."','".$str_ship_bill_summary['deduction']."','".$str_ship_bill_summary['p_c']."','".$str_ship_bill_summary['duty']."','".$str_ship_bill_summary['cess']."','".$str_ship_bill_summary['dbk_claim']."','".$str_ship_bill_summary['igst_amt']."','".$str_ship_bill_summary['cess_amt']."','".$str_ship_bill_summary['igst_value']."','".$str_ship_bill_summary['rodtep_amt']."','".$str_ship_bill_summary['rosctl_amt']."','".$str_ship_bill_summary['mawb_no']."','".$str_ship_bill_summary['mawb_dt']."','".$str_ship_bill_summary['hawb_no']."','".$str_ship_bill_summary['hawb_dt']."','".$str_ship_bill_summary['noc']."','".$str_ship_bill_summary['cin_no']."','".$str_ship_bill_summary['cin_dt']."','".$str_ship_bill_summary['cin_site_id']."','".$str_ship_bill_summary['seal_type']."','".$str_ship_bill_summary['nature_of_cargo']."','".$str_ship_bill_summary['no_of_packets']."','".$str_ship_bill_summary['no_of_containers']."','".$str_ship_bill_summary['loose_packets']."','".$str_ship_bill_summary['marks_and_numbers']."','".$str_ship_bill_summary['submission_date']."','".$str_ship_bill_summary['assessment_date']."','".$str_ship_bill_summary['examination_date']."','".$str_ship_bill_summary['leo_date']."','".$str_ship_bill_summary['submission_time']."','".$str_ship_bill_summary['assessment_time']."','".$str_ship_bill_summary['examination_time']."','".$str_ship_bill_summary['leo_time']."','".$str_ship_bill_summary['leo_no']."','".$str_ship_bill_summary['leo_dt']."','".$str_ship_bill_summary['brc_realisation_date']."','".$str_ship_bill_summary['created_at']."')";
$copy_insert_ship_bill_summary = $db1_ship_bill_summary->query($sql_insert_ship_bill_summary);    
            
        }
	
	
	/******************************************************************Start ship_bill_summary***************************************************************************************/
	
	/******************************************************************Start third_party_details***************************************************************************************/
	
  /*          $query_third_party_details = "SELECT third_party_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM third_party_details LEFT JOIN item_details ON item_details.item_id=third_party_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id  LIMIT 5";
            $statement_third_party_details = $this->db->query($query_third_party_details);
            $iecwise_third_party_details=array();
            $result_third_party_details =$statement_third_party_details->result_array();
            //print_r($result);
        
        foreach($result_third_party_details as $str_third_party_details){
                     $iec_third_party_details=$str_third_party_details['iec'];
                    $sql_third_party_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_third_party_details'";
                    $iecwise_third_party_details= $this->db->query($sql_third_party_details);
                    $iecwise_data_third_party_details =$iecwise_third_party_details->result_array();
                    $db1_third_party_details=$this->database_connection($iecwise_data_third_party_details[0]['lucrative_users_id']);
$sql_insert_third_party_details = "INSERT INTO `third_party_details` (`sbs_id`, `sb_file_status_id`, `invoice_title`, `port_code`, `sb_no`, `sb_date`, `iec`, `br`, `iec_br`, `gstin`, `type`, `cb_code`, `inv_nos`, `item_no`, `cont_no`, `address`, `pkg`, `g_wt_unit`, `g_wt_value`, `mode`, `assess`, `exmn`, `jobbing`, `meis`, `dbk`, `rodtp`, `deec_dfia`, `dfrc`, `reexp`, `lut`, `port_of_loading`, `country_of_finaldestination`, `state_of_origin`, `port_of_finaldestination`, `port_of_discharge`, `country_of_discharge`, `exporter_name_and_address`, `consignee_name_and_address`, `declarant_type`, `ad_code`, `gstin_type_`, `rbi_waiver_no_and_dt`, `forex_bank_account_no`, `cb_name`, `dbk_bank_account_no`, `aeo`, `ifsc_code`, `fob_value_sum`, `freight`, `insurance`, `discount`, `com`, `deduction`, `p_c`, `duty`, `cess`, `dbk_claim`, `igst_amt`, `cess_amt`, `igst_value`, `rodtep_amt`, `rosctl_amt`, `mawb_no`, `mawb_dt`, `hawb_no`, `hawb_dt`, `noc`, `cin_no`, `cin_dt`, `cin_site_id`, `seal_type`, `nature_of_cargo`, `no_of_packets`, `no_of_containers`, `loose_packets`, `marks_and_numbers`, `submission_date`, `assessment_date`, `examination_date`, `leo_date`, `submission_time`, `assessment_time`, `examination_time`, `leo_time`, `leo_no`, `leo_dt`, `brc_realisation_date`, `created_at`) VALUES
('".$str_third_party_details['sbs_id']."','".$str_third_party_details['sb_file_status_id']."','".$str_third_party_details['invoice_title']."','".$str_third_party_details['port_code']."','".$str_third_party_details['sb_no']."','".$str_third_party_details['sb_date']."','".$str_third_party_details['iec']."','".$str_third_party_details['br']."','".$str_third_party_details['iec_br']."','".$str_third_party_details['gstin']."','".$str_third_party_details['type']."','".$str_third_party_details['cb_code']."','".$str_third_party_details['inv_nos']."','".$str_third_party_details['item_no']."','".$str_third_party_details['cont_no']."','".$str_third_party_details['address']."','".$str_third_party_details['pkg']."','".$str_third_party_details['g_wt_unit']."','".$str_third_party_details['g_wt_value']."','".$str_third_party_details['mode']."','".$str_third_party_details['assess']."','".$str_third_party_details['exmn']."','".$str_third_party_details['jobbing']."','".$str_third_party_details['meis']."','".$str_third_party_details['dbk']."','".$str_third_party_details['rodtp']."','".$str_third_party_details['deec_dfia']."','".$str_third_party_details['dfrc']."','".$str_third_party_details['reexp']."','".$str_third_party_details['lut']."','".$str_third_party_details['port_of_loading']."','".$str_third_party_details['country_of_finaldestination']."','".$str_third_party_details['state_of_origin']."','".$str_third_party_details['port_of_finaldestination']."','".$str_third_party_details['port_of_discharge']."','".$str_third_party_details['country_of_discharge']."','".$str_third_party_details['exporter_name_and_address']."','".$str_third_party_details['consignee_name_and_address']."','".$str_third_party_details['declarant_type']."','".$str_third_party_details['ad_code']."','".$str_third_party_details['gstin_type_']."','".$str_third_party_details['rbi_waiver_no_and_dt']."','".$str_third_party_details['forex_bank_account_no']."','".$str_third_party_details['cb_name']."','".$str_third_party_details['dbk_bank_account_no']."','".$str_third_party_details['aeo']."','".$str_third_party_details['ifsc_code']."','".$str_third_party_details['fob_value_sum']."','".$str_third_party_details['freight']."','".$str_third_party_details['insurance']."','".$str_third_party_details['discount']."','".$str_third_party_details['com']."','".$str_third_party_details['deduction']."','".$str_ship_bill_summary['p_c']."','".$str_ship_bill_summary['duty']."','".$str_ship_bill_summary['cess']."','".$str_ship_bill_summary['dbk_claim']."','".$str_ship_bill_summary['igst_amt']."','".$str_ship_bill_summary['cess_amt']."','".$str_ship_bill_summary['igst_value']."','".$str_ship_bill_summary['rodtep_amt']."','".$str_ship_bill_summary['rosctl_amt']."','".$str_ship_bill_summary['mawb_no']."','".$str_ship_bill_summary['mawb_dt']."','".$str_ship_bill_summary['hawb_no']."','".$str_ship_bill_summary['hawb_dt']."','".$str_ship_bill_summary['noc']."','".$str_ship_bill_summary['cin_no']."','".$str_ship_bill_summary['cin_dt']."','".$str_ship_bill_summary['cin_site_id']."','".$str_ship_bill_summary['seal_type']."','".$str_ship_bill_summary['nature_of_cargo']."','".$str_ship_bill_summary['no_of_packets']."','".$str_ship_bill_summary['no_of_containers']."','".$str_ship_bill_summary['loose_packets']."','".$str_ship_bill_summary['marks_and_numbers']."','".$str_ship_bill_summary['submission_date']."','".$str_ship_bill_summary['assessment_date']."','".$str_ship_bill_summary['examination_date']."','".$str_ship_bill_summary['leo_date']."','".$str_ship_bill_summary['submission_time']."','".$str_ship_bill_summary['assessment_time']."','".$str_ship_bill_summary['examination_time']."','".$str_ship_bill_summary['leo_time']."','".$str_ship_bill_summary['leo_no']."','".$str_ship_bill_summary['leo_dt']."','".$str_ship_bill_summary['brc_realisation_date']."','".$str_ship_bill_summary['created_at']."')";
$copy_insert_third_party_details = $db1_third_party_details->query($sql_insert_third_party_details);    
            
        }
	*/
	
	/******************************************************************Start third_party_details***************************************************************************************/
	
	/******************************************************************Start duties_and_additional_details***************************************************************************************/
	
            $query_duties_and_additional_details = "SELECT duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id FROM duties_and_additional_details LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id";
            $statement_duties_and_additional_details = $this->db->query($query_duties_and_additional_details);
            $iecwise_duties_and_additional_details=array();
            $result_duties_and_additional_details =$statement_duties_and_additional_details->result_array();
            //print_r($result);
        
        foreach($result_duties_and_additional_details as $str_duties_and_additional_details){
                     $iec_duties_and_additional_details=$str_duties_and_additional_details['iec_no'];
                    $sql_duties_and_additional_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_duties_and_additional_details'";
                    $iecwise_duties_and_additional_details= $this->db->query($sql_duties_and_additional_details);
                    $iecwise_data_duties_and_additional_details =$iecwise_duties_and_additional_details->result_array();
                    $db1_duties_and_additional_details=$this->database_connection($iecwise_data_duties_and_additional_details[0]['lucrative_users_id']);
            
$sql_insert_duties_and_additional_details = "INSERT INTO `duties_and_additional_details` (`boe_id`,`invoice_id`,`s_no`,`cth`,`description`,`unit_price`,`	quantity`,`	uqc`,`amount`,`invsno`,`itemsn`,`cth_item_detail`,`ceth`,`item_description`,`fs`,`pq`,`dc`,`wc`,`aq`,`upi`,`coo`,`c_qty`,`c_uqc`,`s_qty`,`s_uqc`,`sch`,`stdn_pr`,`rsp`,`reimp`,`prov`,`end_use`,`prodn`,`cntrl`,`qualfr`,`contnt`,`stmnt`,`sup_docs`,`assess_value`,`total_duty`,`bcd_notn_no`,`bcd_notn_sno`,`bcd_rate`,`bcd_amount`,`bcd_duty_fg`,`acd_notn_no`,`acd_notn_sno`,`acd_rate`,`acd_amount`,`acd_duty_fg`,`sws_notn_no`,`sws_notn_sno`,`sws_rate`,`sws_amount`,`sws_duty_fg`,`sad_notn_no`,`sad_notn_sno`,`sad_rate`,`sad_amount`,`sad_duty_fg`,`igst_notn_no`,`igst_notn_sno`,`igst_rate`,`igst_amount`,`igst_duty_fg`,`g_cess_notn_no`,`g_cess_notn_sno`,`g_cess_rate`,`g_cess_amount`,`g_cess_duty_fg`,`add_notn_no`,`add_notn_sno`,`add_rate`,`add_amount`,`add_duty_fg`,`cvd_notn_no`,`cvd_notn_sno`,`cvd_rate`,`cvd_amount`,`cvd_duty_fg`,`sg_notn_no`,`sg_notn_sno`,`sg_rate`,`sg_amount`,`sg_duty_fg`,`t_value_notn_no`,`t_value_notn_sno`,`t_value_rate`,`t_value_amount`,`t_value_duty_fg`,`sp_excd_notn_no`,`sp_excd_notn_sno`,`sp_excd_rate`,`sp_excd_amount`,`sp_excd_duty_fg`,`chcess_notn_no`,`chcess_notn_sno`,`chcess_rate`,`chcess_amount`,`chcess_duty_fg`,`tta_notn_no`,`tta_notn_sno`,`tta_rate`,`tta_amount`,`tta_duty_fg`,`cess_notn_no`,`cess_notn_sno`,`cess_rate`,`cess_amount`,`cess_duty_fg`,`caidc_cvd_edc_notn_no`,`caidc_cvd_edc_notn_sno`,`caidc_cvd_edc_rate`,`caidc_cvd_edc_amount`,`caidc_cvd_edc_duty_fg`,`eaidc_cvd_hec_notn_no`,`eaidc_cvd_hec_notn_sno`,`eaidc_cvd_hec_rate`,`eaidc_cvd_hec_amount`,`eaidc_cvd_hec_duty_fg`,`cus_edc_notn_no`,`cus_edc_notn_sno`,`cus_edc_rate`,`cus_edc_amount`,`cus_edc_duty_fg`,`cus_hec_notn_no`,`cus_hec_notn_sno`,`cus_hec_rate`,`cus_hec_amount`,`cus_hec_duty_fg`,`ncd_notn_no`,`ncd_notn_sno`,`ncd_rate`,`ncd_amount`,`ncd_duty_fg`,`aggr_notn_no`,`aggr_notn_sno`,`aggr_rate`,`aggr_amount`,`aggr_duty_fg`,`invsno_add_details`,`itmsno_add_details`,`refno`,`refdt`,`prtcd_svb_d`,`lab`,`pf`,`load_date`,`pf_`,`beno`,`bedate`,`prtcd`,`unitprice`,`currency_code`,`notno`,`slno`,`frt`,`ins`,`duty`,`sb_no`,`sb_dt`,`portcd`,`sinv`,`sitemn`,`type`,`manufact_cd`,`source_cy`,`trans_cy`,`address`,`accessory_item_details`,`lic_slno`,`lic_date`,`code`,`port`,`debit_value`,`qty`,`uqc_lc_d`,`debit_duty`,`certificate_number`,`date`,`type_cert_d`,`prc_level`,`iec`,`branch_slno`,`created_at`) 
VALUES('".$str_duties_and_additional_details['boe_id']."','".$str_duties_and_additional_details['invoice_id']."','".$str_duties_and_additional_details['s_no']."','".$str_duties_and_additional_details['cth']."','".$str_duties_and_additional_details['description']."','".$str_duties_and_additional_details['unit_price']."','".$str_duties_and_additional_details['quantity']."','".$str_duties_and_additional_details['uqc']."','".$str_duties_and_additional_details['amount']."','".$str_duties_and_additional_details['invsno']."','".$str_duties_and_additional_details['itemsn']."','".$str_duties_and_additional_details['cth_item_detail']."','".$str_duties_and_additional_details['ceth']."','".$str_duties_and_additional_details['item_description']."','".$str_duties_and_additional_details['fs']."','".$str_duties_and_additional_details['pq']."','".$str_duties_and_additional_details['dc']."','".$str_duties_and_additional_details['wc']."','".$str_duties_and_additional_details['aq']."','".$str_duties_and_additional_details['upi']."','".$str_duties_and_additional_details['coo']."','".$str_duties_and_additional_details['c_qty']."','".$str_duties_and_additional_details['c_uqc']."','".$str_duties_and_additional_details['s_qty']."','".$str_duties_and_additional_details['s_uqc']."','".$str_duties_and_additional_details['sch']."','".$str_duties_and_additional_details['stdn_pr']."','".$str_duties_and_additional_details['rsp']."','".$str_duties_and_additional_details['reimp']."','".$str_duties_and_additional_details['prov']."','".$str_duties_and_additional_details['end_use']."','".$str_duties_and_additional_details['prodn']."','".$str_duties_and_additional_details['cntrl']."','".$str_duties_and_additional_details['qualfr']."','".$str_duties_and_additional_details['contnt']."','".$str_duties_and_additional_details['stmnt']."','".$str_duties_and_additional_details['sup_docs']."','".$str_duties_and_additional_details['assess_value']."','".$str_duties_and_additional_details['total_duty']."','".$str_duties_and_additional_details['bcd_notn_no']."','".$str_duties_and_additional_details['bcd_notn_sno']."','".$str_duties_and_additional_details['bcd_rate']."','".$str_duties_and_additional_details['bcd_amount']."','".$str_duties_and_additional_details['bcd_duty_fg']."','".$str_duties_and_additional_details['acd_notn_no']."','".$str_duties_and_additional_details['acd_notn_sno']."','".$str_duties_and_additional_details['acd_rate']."','".$str_duties_and_additional_details['acd_amount']."','".$str_duties_and_additional_details['acd_duty_fg']."','".$str_duties_and_additional_details['sws_notn_no']."','".$str_duties_and_additional_details['sws_notn_sno']."','".$str_duties_and_additional_details['sws_rate']."','".$str_duties_and_additional_details['sws_amount']."','".$str_duties_and_additional_details['sws_duty_fg']."','".$str_duties_and_additional_details['sad_notn_no']."','".$str_duties_and_additional_details['sad_notn_sno']."','".$str_duties_and_additional_details['sad_rate']."','".$str_duties_and_additional_details['sad_amount']."','".$str_duties_and_additional_details['sad_duty_fg']."','".$str_duties_and_additional_details['igst_notn_no']."','".$str_duties_and_additional_details['igst_notn_sno']."','".$str_duties_and_additional_details['igst_rate']."','".$str_duties_and_additional_details['igst_amount']."','".$str_duties_and_additional_details['igst_duty_fg']."','".$str_duties_and_additional_details['g_cess_notn_no']."','".$str_duties_and_additional_details['g_cess_notn_sno']."','".$str_duties_and_additional_details['g_cess_rate']."','".$str_duties_and_additional_details['g_cess_amount']."','".$str_duties_and_additional_details['g_cess_duty_fg']."','".$str_duties_and_additional_details['add_notn_no']."','".$str_duties_and_additional_details['add_notn_sno']."','".$str_duties_and_additional_details['add_rate']."','".$str_duties_and_additional_details['add_amount']."','".$str_duties_and_additional_details['add_duty_fg']."','".$str_duties_and_additional_details['cvd_notn_no']."','".$str_duties_and_additional_details['cvd_notn_sno']."','".$str_duties_and_additional_details['cvd_rate']."','".$str_duties_and_additional_details['cvd_amount']."','".$str_duties_and_additional_details['cvd_duty_fg']."','".$str_duties_and_additional_details['sg_notn_no']."','".$str_duties_and_additional_details['sg_notn_sno']."','".$str_duties_and_additional_details['sg_rate']."','".$str_duties_and_additional_details['sg_amount']."','".$str_duties_and_additional_details['sg_duty_fg']."','".$str_duties_and_additional_details['t_value_notn_no']."','".$str_duties_and_additional_details['t_value_notn_sno']."','".$str_duties_and_additional_details['t_value_rate']."','".$str_duties_and_additional_details['t_value_amount']."','".$str_duties_and_additional_details['t_value_duty_fg']."','".$str_duties_and_additional_details['sp_excd_notn_no']."','".$str_duties_and_additional_details['sp_excd_notn_sno']."','".$str_duties_and_additional_details['sp_excd_rate']."','".$str_duties_and_additional_details['sp_excd_amount']."','".$str_duties_and_additional_details['sp_excd_duty_fg']."','".$str_duties_and_additional_details['chcess_notn_no']."','".$str_duties_and_additional_details['chcess_notn_sno']."','".$str_duties_and_additional_details['chcess_rate']."','".$str_duties_and_additional_details['chcess_amount']."','".$str_duties_and_additional_details['chcess_duty_fg']."','".$str_duties_and_additional_details['tta_notn_no']."','".$str_duties_and_additional_details['tta_notn_sno']."','".$str_duties_and_additional_details['tta_rate']."','".$str_duties_and_additional_details['tta_amount']."','".$str_duties_and_additional_details['tta_duty_fg']."','".$str_duties_and_additional_details['cess_notn_no']."','".$str_duties_and_additional_details['cess_notn_sno']."','".$str_duties_and_additional_details['cess_rate']."','".$str_duties_and_additional_details['cess_amount']."','".$str_duties_and_additional_details['cess_duty_fg']."','".$str_duties_and_additional_details['caidc_cvd_edc_notn_no']."','".$str_duties_and_additional_details['caidc_cvd_edc_notn_sno']."','".$str_duties_and_additional_details['caidc_cvd_edc_notn_sno']."','".$str_duties_and_additional_details['caidc_cvd_edc_rate']."','".$str_duties_and_additional_details['caidc_cvd_edc_amount']."','".$str_duties_and_additional_details['caidc_cvd_edc_duty_fg']."','".$str_duties_and_additional_details['eaidc_cvd_hec_notn_no']."','".$str_duties_and_additional_details['eaidc_cvd_hec_notn_sno']."','".$str_duties_and_additional_details['eaidc_cvd_hec_rate']."','".$str_duties_and_additional_details['eaidc_cvd_hec_amount']."','".$str_duties_and_additional_details['eaidc_cvd_hec_duty_fg']."','".$str_duties_and_additional_details['cus_edc_notn_no']."','".$str_duties_and_additional_details['cus_edc_notn_sno']."','".$str_duties_and_additional_details['cus_edc_rate']."','".$str_duties_and_additional_details['cus_edc_amount']."','".$str_duties_and_additional_details['cus_edc_duty_fg']."','".$str_duties_and_additional_details['cus_hec_notn_no']."','".$str_duties_and_additional_details['cus_hec_notn_sno']."','".$str_duties_and_additional_details['cus_hec_rate']."','".$str_duties_and_additional_details['cus_hec_amount']."','".$str_duties_and_additional_details['cus_hec_duty_fg']."','".$str_duties_and_additional_details['ncd_notn_no']."','".$str_duties_and_additional_details['ncd_notn_sno']."','".$str_duties_and_additional_details['ncd_rate']."','".$str_duties_and_additional_details['ncd_amount']."','".$str_duties_and_additional_details['ncd_duty_fg']."','".$str_duties_and_additional_details['aggr_notn_no']."','".$str_duties_and_additional_details['aggr_notn_sno']."','".$str_duties_and_additional_details['aggr_rate']."','".$str_duties_and_additional_details['aggr_amount']."','".$str_duties_and_additional_details['aggr_duty_fg']."','".$str_duties_and_additional_details['invsno_add_details']."','".$str_duties_and_additional_details['itmsno_add_details']."','".$str_duties_and_additional_details['refno']."','".$str_duties_and_additional_details['refdt']."','".$str_duties_and_additional_details['prtcd_svb_d']."','".$str_duties_and_additional_details['lab']."','".$str_duties_and_additional_details['pf']."','".$str_duties_and_additional_details['load_date']."','".$str_duties_and_additional_details['pf_']."','".$str_duties_and_additional_details['beno']."','".$str_duties_and_additional_details['bedate']."','".$str_duties_and_additional_details['prtcd']."','".$str_duties_and_additional_details['unitprice']."','".$str_duties_and_additional_details['currency_code']."','".$str_duties_and_additional_details['notno']."','".$str_duties_and_additional_details['slno']."','".$str_duties_and_additional_details['frt']."','".$str_duties_and_additional_details['ins']."','".$str_duties_and_additional_details['duty']."','".$str_duties_and_additional_details['sb_no']."','".$str_duties_and_additional_details['sb_dt']."','".$str_duties_and_additional_details['portcd']."','".$str_duties_and_additional_details['sinv']."','".$str_duties_and_additional_details['sitemn']."','".$str_duties_and_additional_details['type']."','".$str_duties_and_additional_details['manufact_cd']."','".$str_duties_and_additional_details['source_cy']."','".$str_duties_and_additional_details['trans_cy']."','".$str_duties_and_additional_details['address']."','".$str_duties_and_additional_details['accessory_item_details']."','".$str_duties_and_additional_details['lic_slno']."','".$str_duties_and_additional_details['lic_date']."','".$str_duties_and_additional_details['code']."','".$str_duties_and_additional_details['port']."','".$str_duties_and_additional_details['debit_value']."','".$str_duties_and_additional_details['qty']."','".$str_duties_and_additional_details['uqc_lc_d']."','".$str_duties_and_additional_details['debit_duty']."','".$str_duties_and_additional_details['certificate_number']."','".$str_duties_and_additional_details['date']."','".$str_duties_and_additional_details['type_cert_d']."','".$str_duties_and_additional_details['prc_level']."','".$str_duties_and_additional_details['iec']."','".$str_duties_and_additional_details['branch_slno']."','".$str_duties_and_additional_details['created_at']."')";
$copy_insert_duties_and_additional_details = $db1_insert_duties_and_additional_details->query($sql_insert_duties_and_additional_details);    
            
        }
	
	
/************************************************Start duties_and_additional_detail***************************************************************************************/
			
	/******************************************************************Start equipment_details***************************************************************************************/
	
            $query_equipment_details = "SELECT equipment_details.*,ship_bill_summary.sbs_id,ship_bill_summary.iec FROM equipment_details LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id";
            $statement_equipment_details = $this->db->query($query_equipment_details);
            $iecwise_equipment_details=array();
            $result_equipment_details =$statement_equipment_details->result_array();
            //print_r($result);
        
        foreach($result_equipment_details as $str_equipment_details){
                     $iec_equipment_details=$str_equipment_details['iec'];
                    $sql_equipment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no='$iec_equipment_details'";
                    $iecwise_equipment_details= $this->db->query($sql_equipment_details);
                    $iecwise_data_equipment_details =$iecwise_equipment_details->result_array();
                    $db1_equipment_details=$this->database_connection($iecwise_data_equipment_details[0]['lucrative_users_id']);
$sql_insert_equipment_details = "INSERT INTO `equipment_details` (`sbs_id`, `sb_file_status_id`, `invoice_title`, `port_code`, `sb_no`, `sb_date`, `iec`, `br`, `iec_br`, `gstin`, `type`, `cb_code`, `inv_nos`, `item_no`, `cont_no`, `address`, `pkg`, `g_wt_unit`, `g_wt_value`, `mode`, `assess`, `exmn`, `jobbing`, `meis`, `dbk`, `rodtp`, `deec_dfia`, `dfrc`, `reexp`, `lut`, `port_of_loading`, `country_of_finaldestination`, `state_of_origin`, `port_of_finaldestination`, `port_of_discharge`, `country_of_discharge`, `exporter_name_and_address`, `consignee_name_and_address`, `declarant_type`, `ad_code`, `gstin_type_`, `rbi_waiver_no_and_dt`, `forex_bank_account_no`, `cb_name`, `dbk_bank_account_no`, `aeo`, `ifsc_code`, `fob_value_sum`, `freight`, `insurance`, `discount`, `com`, `deduction`, `p_c`, `duty`, `cess`, `dbk_claim`, `igst_amt`, `cess_amt`, `igst_value`, `rodtep_amt`, `rosctl_amt`, `mawb_no`, `mawb_dt`, `hawb_no`, `hawb_dt`, `noc`, `cin_no`, `cin_dt`, `cin_site_id`, `seal_type`, `nature_of_cargo`, `no_of_packets`, `no_of_containers`, `loose_packets`, `marks_and_numbers`, `submission_date`, `assessment_date`, `examination_date`, `leo_date`, `submission_time`, `assessment_time`, `examination_time`, `leo_time`, `leo_no`, `leo_dt`, `brc_realisation_date`, `created_at`) VALUES
('".$str_equipment_details['sbs_id']."','".$str_equipment_details['sb_file_status_id']."','".$str_equipment_details['invoice_title']."','".$str_equipment_details['port_code']."','".$str_equipment_details['sb_no']."','".$str_equipment_details['sb_date']."','".$str_equipment_details['iec']."','".$str_equipment_details['br']."','".$str_equipment_details['iec_br']."','".$str_equipment_details['gstin']."','".$str_equipment_details['type']."','".$str_equipment_details['cb_code']."','".$str_equipment_details['inv_nos']."','".$str_equipment_details['item_no']."','".$str_equipment_details['cont_no']."','".$str_equipment_details['address']."','".$str_equipment_details['pkg']."','".$str_equipment_details['g_wt_unit']."','".$str_equipment_details['g_wt_value']."','".$str_equipment_details['mode']."','".$str_equipment_details['assess']."','".$str_equipment_details['exmn']."','".$str_equipment_details['jobbing']."','".$str_equipment_details['meis']."','".$str_equipment_details['dbk']."','".$str_equipment_details['rodtp']."','".$str_equipment_details['deec_dfia']."','".$str_equipment_details['dfrc']."','".$str_equipment_details['reexp']."','".$str_equipment_details['lut']."','".$str_equipment_details['port_of_loading']."','".$str_equipment_details['country_of_finaldestination']."','".$str_equipment_details['state_of_origin']."','".$str_equipment_details['port_of_finaldestination']."','".$str_equipment_details['port_of_discharge']."','".$str_equipment_details['country_of_discharge']."','".$str_equipment_details['exporter_name_and_address']."','".$str_equipment_details['consignee_name_and_address']."','".$str_equipment_details['declarant_type']."','".$str_equipment_details['ad_code']."','".$str_equipment_details['gstin_type_']."','".$str_third_party_details['rbi_waiver_no_and_dt']."','".$str_third_party_details['forex_bank_account_no']."','".$str_third_party_details['cb_name']."','".$str_third_party_details['dbk_bank_account_no']."','".$str_third_party_details['aeo']."','".$str_third_party_details['ifsc_code']."','".$str_third_party_details['fob_value_sum']."','".$str_third_party_details['freight']."','".$str_third_party_details['insurance']."','".$str_third_party_details['discount']."','".$str_third_party_details['com']."','".$str_third_party_details['deduction']."','".$str_ship_bill_summary['p_c']."','".$str_ship_bill_summary['duty']."','".$str_ship_bill_summary['cess']."','".$str_ship_bill_summary['dbk_claim']."','".$str_ship_bill_summary['igst_amt']."','".$str_ship_bill_summary['cess_amt']."','".$str_ship_bill_summary['igst_value']."','".$str_ship_bill_summary['rodtep_amt']."','".$str_ship_bill_summary['rosctl_amt']."','".$str_ship_bill_summary['mawb_no']."','".$str_ship_bill_summary['mawb_dt']."','".$str_ship_bill_summary['hawb_no']."','".$str_ship_bill_summary['hawb_dt']."','".$str_ship_bill_summary['noc']."','".$str_ship_bill_summary['cin_no']."','".$str_ship_bill_summary['cin_dt']."','".$str_ship_bill_summary['cin_site_id']."','".$str_ship_bill_summary['seal_type']."','".$str_ship_bill_summary['nature_of_cargo']."','".$str_ship_bill_summary['no_of_packets']."','".$str_ship_bill_summary['no_of_containers']."','".$str_ship_bill_summary['loose_packets']."','".$str_ship_bill_summary['marks_and_numbers']."','".$str_ship_bill_summary['submission_date']."','".$str_ship_bill_summary['assessment_date']."','".$str_ship_bill_summary['examination_date']."','".$str_ship_bill_summary['leo_date']."','".$str_ship_bill_summary['submission_time']."','".$str_ship_bill_summary['assessment_time']."','".$str_ship_bill_summary['examination_time']."','".$str_ship_bill_summary['leo_time']."','".$str_ship_bill_summary['leo_no']."','".$str_ship_bill_summary['leo_dt']."','".$str_ship_bill_summary['brc_realisation_date']."','".$str_ship_bill_summary['created_at']."')";
$copy_insert_equipment_details = $db1_insert_equipment_details->query($sql_insert_equipment_details);    
            
        }
	
	
	/******************************************************************Start equipment_details***************************************************************************************/
				
		$this->load->view('common/header');
		$this->load->view('admin/dashboard');
		$this->load->view('common/footer');		
	}
	
public function database_connection($id){
    $hostname='localhost';
    $username='root';
    $password='%!#^bFjB)z8C';
    
    echo $db_name1='lucrativeesystem_D2D_'.$id;
          $Db1 = new mysqli($hostname,$username,$password,$db_name1); 
 // Check connection
if ($Db1->connect_error) {
  die("Connection failed: " . $Db1->connect_error);
}else{
echo "Connected successfully";
}
    return $Db1;
}	
	
	public function saveimport_export_data(){
	    $post=$this->input->post();
	}
	
	public function iec_signup(){
	    $this->load->view('common/header');
		$this->load->view('admin/signup_iec_form.php');
		$this->load->view('common/footer');	
	}
	
	public function register_iec_user(){
	    $post = $this->input->post();
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	     $pass = array(); //remember to declare $pass as an array
	     $alphaLength = strlen($alphabet) - 1;
	      for ($i = 0; $i < 8; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
           }
    $data =array(
        'fullname'     =>  $post["first_name"]." ".$post["last_name"],
        "email"        =>  $post["iec_email"],
        "iec_no"       =>  $post["iec_no"],
        "mobile"       =>  $post["mobile_no"],
        "password"     =>  implode($pass),
        "role"         =>  "admin",
        'created_at' =>   date('Y-m-d h:i:s')
        );
        //$result = $this->Common_model->insert_iec_user_entry($data);
        $result=1;
        
        if($result) {   
        $db_name = $_SESSION['database_prefix'].$result;
       
        if($this->dbforge->create_database($db_name))
{
    $this->db->query("use ".$db_name."");
    
            $this->db->query("CREATE TABLE `boe_delete_logs` (
  `boe_delete_logs_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `filename` varchar(255),
  `be_no` varchar(255),
  `be_date` datetime,
  `iec_no` varchar(255),
  `br` varchar(255),
  `fullname` varchar(255),
  `email` varchar(255),
  `mobile` varchar(255),
  `deleted_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");
          
          
  $this->db->query("CREATE TABLE `bill_of_entry_summary` (
  `boe_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `boe_file_status_id` integer,
  `invoice_title` varchar(255),
  `port` varchar(255),
  `port_code` varchar(255),
  `be_no` varchar(255),
  `be_date` date,
  `be_type` varchar(255),
  `iec_br` varchar(255),
  `iec_no` varchar(255),
  `br` varchar(255),
  `gstin_type` varchar(255),
  `cb_code` varchar(255),
  `type` varchar(255),
  `nos` integer,
  `pkg` integer,
  `item` integer,
  `g_wt_kgs` integer,
  `cont` integer,
  `be_status` varchar(255),
  `mode` varchar(255),
  `def_be` varchar(255),
  `kacha` varchar(255),
  `sec_48` varchar(255),
  `reimp` varchar(255),
  `adv_be` varchar(255),
  `assess` varchar(255),
  `exam` varchar(255),
  `hss` varchar(255),
  `first_check` varchar(255),
  `prov_final` varchar(255),
  `country_of_origin` varchar(255),
  `country_of_consignment` varchar(255),
  `port_of_loading` varchar(255),
  `port_of_shipment` varchar(255),
  `importer_name_and_address` varchar(255),
  `cb_name` varchar(255),
  `aeo` varchar(255),
  `ucr` varchar(255),
  `bcd` numeric,
  `acd` numeric,
  `sws` numeric,
  `nccd` numeric,
  `add` numeric,
  `cvd` numeric,
  `igst` numeric,
  `g_cess` numeric,
  `sg` numeric,
  `saed` numeric,
  `gsia` numeric,
  `tta` numeric,
  `health` numeric,
  `total_duty` numeric,
  `int` numeric,
  `pnlty` numeric,
  `fine` numeric,
  `tot_ass_val` numeric,
  `tot_amount` numeric,
  `wbe_no` varchar(255),
  `wbe_date` date,
  `wbe_site` varchar(255),
  `wh_code` varchar(255),
  `submission_date` date,
  `assessment_date` date,
  `examination_date` date,
  `ooc_date` date,
  `submission_time` varchar(255),
  `assessment_time` varchar(255),
  `examination_time` varchar(255),
  `ooc_time` varchar(255),
  `submission_exchange_rate` varchar(255),
  `assessment_exchange_rate` varchar(255),
  `ooc_no` varchar(255),
  `ooc_date_` date,
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


 $this->db->query("CREATE TABLE `invoice_and_valuation_details` (
  `invoice_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `boe_id` integer,
  `s_no` integer,
  `invoice_no` varchar(255),
  `purchase_order_no` varchar(255),
  `lc_no` varchar(255),
  `contract_no` varchar(255),
  `buyer_s_name_and_address` varchar(255),
  `seller_s_name_and_address` varchar(255),
  `supplier_name_and_address` varchar(255),
  `third_party_name_and_address` varchar(255),
  `aeo` varchar(255),
  `ad_code` varchar(255),
  `inv_value` numeric,
  `freight` varchar(255),
  `insurance` varchar(255),
  `hss` varchar(255),
  `loading` varchar(255),
  `commn` varchar(255),
  `pay_terms` varchar(255),
  `valuation_method` varchar(255),
  `reltd` varchar(255),
  `svb_ch` varchar(255),
  `svb_no` varchar(255),
  `date` date,
  `loa` integer,
  `cur` varchar(255),
  `term` varchar(255),
  `c_and_b` varchar(255),
  `coc` varchar(255),
  `cop` varchar(255),
  `hnd_chg` varchar(255),
  `g_and_s` varchar(255),
  `doc_ch` varchar(255),
  `coo` varchar(255),
  `r_and_lf` varchar(255),
  `oth_cost` varchar(255),
  `ld_uld` varchar(255),
  `ws` varchar(255),
  `otc` varchar(255),
  `misc_charge` numeric,
  `ass_value` numeric,
  `invoice_date` date,
  `purchase_date` date,
  `lc_date` date,
  `contract_date` date,
  `freight_cur` varchar(255),
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


   $this->db->query("CREATE TABLE `duties_and_additional_details` (
  `duties_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `boe_id` integer,
  `invoice_id` integer NOT NULL,
  `s_no` integer NOT NULL,
  `cth` text,
  `description` text,
  `unit_price` numeric,
  `quantity` integer,
  `uqc` text,
  `amount` numeric,
  `invsno` integer,
  `itemsn` integer,
  `cth_item_detail` text,
  `ceth` text,
  `item_description` text,
  `fs` text,
  `pq` text,
  `dc` text,
  `wc` text,
  `aq` text,
  `upi` numeric,
  `coo` text,
  `c_qty` text,
  `c_uqc` text,
  `s_qty` numeric,
  `s_uqc` text,
  `sch` text,
  `stdn_pr` text,
  `rsp` text,
  `reimp` text,
  `prov` text,
  `end_use` text,
  `prodn` text,
  `cntrl` text,
  `qualfr`text,
  `contnt` text,
  `stmnt` text,
  `sup_docs` text,
  `assess_value` numeric,
  `total_duty` numeric,
  `bcd_notn_no` text,
  `bcd_notn_sno` text,
  `bcd_rate` integer,
  `bcd_amount` numeric,
  `bcd_duty_fg` numeric,
  `acd_notn_no` text,
  `acd_notn_sno` text,
  `acd_rate` integer,
  `acd_amount` numeric,
  `acd_duty_fg` numeric,
  `sws_notn_no` text,
  `sws_notn_sno` text,
  `sws_rate` integer,
  `sws_amount` numeric,
  `sws_duty_fg` numeric,
  `sad_notn_no` text,
  `sad_notn_sno` text,
  `sad_rate` integer,
  `sad_amount` numeric,
  `sad_duty_fg` numeric,
  `igst_notn_no` text,
  `igst_notn_sno` text,
  `igst_rate` integer,
  `igst_amount` numeric,
  `igst_duty_fg` numeric,
  `g_cess_notn_no` text,
  `g_cess_notn_sno` text,
  `g_cess_rate` integer,
  `g_cess_amount` numeric,
  `g_cess_duty_fg` numeric,
  `add_notn_no` text,
  `add_notn_sno` text,
  `add_rate` integer,
  `add_amount` numeric,
  `add_duty_fg` numeric,
  `cvd_notn_no` text,
  `cvd_notn_sno` text,
  `cvd_rate` integer,
  `cvd_amount` numeric,
  `cvd_duty_fg` numeric,
  `sg_notn_no` text,
  `sg_notn_sno` text,
  `sg_rate` integer,
  `sg_amount` numeric,
  `sg_duty_fg` numeric,
  `t_value_notn_no` text,
  `t_value_notn_sno` text,
  `t_value_rate` integer,
  `t_value_amount` numeric,
  `t_value_duty_fg` numeric,
  `sp_excd_notn_no` text,
  `sp_excd_notn_sno` text,
  `sp_excd_rate` integer,
  `sp_excd_amount` numeric,
  `sp_excd_duty_fg` numeric,
  `chcess_notn_no` text,
  `chcess_notn_sno` text,
  `chcess_rate` integer,
  `chcess_amount` numeric,
  `chcess_duty_fg` numeric,
  `tta_notn_no` text,
  `tta_notn_sno` text,
  `tta_rate` integer,
  `tta_amount` numeric,
  `tta_duty_fg` numeric,
  `cess_notn_no` text,
  `cess_notn_sno` text,
  `cess_rate` integer,
  `cess_amount` numeric,
  `cess_duty_fg` numeric,
  `caidc_cvd_edc_notn_no` text,
  `caidc_cvd_edc_notn_sno` text,
  `caidc_cvd_edc_rate` integer,
  `caidc_cvd_edc_amount` numeric,
  `caidc_cvd_edc_duty_fg` numeric,
  `eaidc_cvd_hec_notn_no` text,
  `eaidc_cvd_hec_notn_sno` text,
  `eaidc_cvd_hec_rate` integer,
  `eaidc_cvd_hec_amount` numeric,
  `eaidc_cvd_hec_duty_fg` numeric,
  `cus_edc_notn_no` text,
  `cus_edc_notn_sno` text,
  `cus_edc_rate` integer,
  `cus_edc_amount` numeric,
  `cus_edc_duty_fg` numeric,
  `cus_hec_notn_no` text,
  `cus_hec_notn_sno` text,
  `cus_hec_rate` integer,
  `cus_hec_amount` numeric,
  `cus_hec_duty_fg` numeric,
  `ncd_notn_no` text,
  `ncd_notn_sno` text,
  `ncd_rate` integer,
  `ncd_amount` numeric,
  `ncd_duty_fg` numeric,
  `aggr_notn_no` text,
  `aggr_notn_sno` text,
  `aggr_rate` integer,
  `aggr_amount` numeric,
  `aggr_duty_fg` numeric,
  `invsno_add_details` integer,
  `itmsno_add_details` integer,
  `refno` text,
  `refdt` text,
  `prtcd_svb_d` text,
  `lab` text,
  `pf` text,
  `load_date` date,
  `pf_` text,
  `beno` text,
  `bedate` date,
  `prtcd` text,
  `unitprice` numeric,
  `currency_code` text,
  `notno` integer,
  `slno` integer,
  `frt` text,
  `ins` text,
  `duty` text,
  `sb_no` text,
  `sb_dt` text,
  `portcd` text,
  `sinv` text,
  `sitemn` text,
  `type` text,
  `manufact_cd` text,
  `source_cy` text,
  `trans_cy` text,
  `address` text,
  `accessory_item_details` text,
  `lic_slno` integer,
  `lic_date` date,
  `code` text,
  `port` text,
  `debit_value` text,
  `qty` integer,
  `uqc_lc_d` text,
  `debit_duty` text,
  `certificate_number` text,
  `date` date,
  `type_cert_d` text,
  `prc_level` text,
  `iec` text,
  `branch_slno` integer,
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


 $this->db->query("CREATE TABLE `bill_manifest_details` (
  `manifest_details_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `boe_id` integer NOT NULL,
  `igm_no` varchar(255),
  `igm_date` date,
  `inw_date` date,
  `gigmno` varchar(255),
  `gigmdt` date,
  `mawb_no` varchar(255),
  `mawb_date` date,
  `hawb_no` varchar(255),
  `hawb_date` date,
  `pkg` integer,
  `gw` integer,
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `bill_bond_details` (
  `bond_details_id` integer PRIMARY KEY AUTO_INCREMENT,
  `boe_id` integer NOT NULL,
  `bond_no` varchar(255),
  `port` varchar(255),
  `bond_cd` varchar(255),
  `debt_amt` numeric,
  `bg_amt` numeric,
  `created_at` datetime) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");
  
  
  $this->db->query("CREATE TABLE `bill_payment_details` (
  `payment_details_id` integer PRIMARY KEY AUTO_INCREMENT,
  `boe_id` integer NOT NULL,
  `sr_no` integer,
  `challan_no` varchar(255),
  `paid_on` date,
  `amount` numeric,
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

  $this->db->query("CREATE TABLE `bill_container_details` (
  `container_details_pk` integer PRIMARY KEY AUTO_INCREMENT,
  `boe_id` integer NOT NULL,
  `sno` integer,
  `lcl_fcl` varchar(255),
  `truck` varchar(255),
  `seal` varchar(255),
  `container_number` varchar(255),
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

  $this->db->query("CREATE TABLE `sb_file_status` (
  `sb_file_status_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pdf_filepath` varchar(255),
  `pdf_filename` varchar(255),
  `user_iec_no` varchar(255),
  `lucrative_users_id` integer,
  `excel_filepath` varchar(255),
  `excel_filename` varchar(255),
  `pdf_to_excel_date` timestamp,
  `pdf_to_excel_status` varchar(255),
  `file_iec_no` varchar(255),
  `sb_no` varchar(255),
  `sb_date` date,
  `stage` varchar(255),
  `status` varchar(255),
  `remarks` varchar(255),
  `created_at` datetime,
  `br` varchar(255),
  `is_processed` boolean
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `ship_bill_summary` (
  `sbs_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sb_file_status_id` integer,
  `invoice_title` text,
  `port_code` text,
  `sb_no` integer,
  `sb_date` date,
  `iec` text,
  `br` text,
  `iec_br` text,
  `gstin` text,
  `type` text,
  `cb_code` text,
  `inv_nos` text,
  `item_no` text,
  `cont_no` text,
  `address` text,
  `pkg` text,
  `g_wt_unit` text,
  `g_wt_value` text,
  `mode` text,
  `assess` text,
  `exmn` text,
  `jobbing` text,
  `meis` text,
  `dbk` text,
  `rodtp` text,
  `deec_dfia` text,
  `dfrc` text,
  `reexp` text,
  `lut` text,
  `port_of_loading` text,
  `country_of_finaldestination` text,
  `state_of_origin` text,
  `port_of_finaldestination` text,
  `port_of_discharge` text,
  `country_of_discharge` text,
  `exporter_name_and_address` text,
  `consignee_name_and_address` text,
  `declarant_type` text,
  `ad_code` text,
  `gstin_type_` text,
  `rbi_waiver_no_and_dt` text,
  `forex_bank_account_no` text,
  `cb_name` text,
  `dbk_bank_account_no` text,
  `aeo` text,
  `ifsc_code` text,
  `fob_value_sum` text,
  `freight` text,
  `insurance` text,
  `discount` text,
  `com` text,
  `deduction` text,
  `p_c` text,
  `duty` text,
  `cess` text,
  `dbk_claim` text,
  `igst_amt` text,
  `cess_amt` text,
  `igst_value` text,
  `rodtep_amt` text,
  `rosctl_amt` text,
  `mawb_no` text,
  `mawb_dt` text,
  `hawb_no` text,
  `hawb_dt` text,
  `noc` text,
  `cin_no` text,
  `cin_dt` text,
  `cin_site_id` text,
  `seal_type` text,
  `nature_of_cargo` text,
  `no_of_packets` text,
  `no_of_containers` text,
  `loose_packets` text,
  `marks_and_numbers` text,
  `submission_date` text,
  `assessment_date` text,
  `examination_date` text,
  `leo_date` text,
  `submission_time` text,
  `assessment_time` text,
  `examination_time` text,
  `leo_time` text,
  `leo_no` text,
  `leo_dt` text,
  `brc_realisation_date` text,
  `created_at` datetime)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");




$this->db->query("CREATE TABLE `equipment_details` (
  `equip_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sbs_id` integer,
  `container` text,
  `seal` text,
  `date` text,
  `s_no` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

$this->db->query("CREATE TABLE `challan_details` (
  `challan_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sbs_id` integer,
  `sr_no` text,
  `challan_no` text,
  `paymt_dt` text,
  `amount` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `invoice_summary` (
  `invoice_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sbs_id` integer,
  `s_no_inv` text,
  `inv_no` text,
  `inv_date` date,
  `inv_no_date` text,
  `po_no_date` text,
  `loc_no_date` text,
  `contract_no_date` text,
  `ad_code_inv` text,
  `invterm` text,
  `exporters_name_and_address` text,
  `buyers_name_and_address` text,
  `third_party_name_and_address` text,
  `buyers_aeo_status` text,
  `invoice_value` text,
  `invoice_value_currency` text,
  `fob_value_inv` text,
  `fob_value_currency` text,
  `freight_val` text,
  `freight_currency` text,
  `insurance_val` text,
  `insurance_currency` text,
  `discount_val` text,
  `discount_val_currency` text,
  `commison` text,
  `comission_currency` text,
  `deduct` text,
  `deduct_currency` text,
  `p_c_val` text,
  `p_c_val_currency` text,
  `exchange_rate` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `item_details` (
  `item_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `invoice_id` integer,
  `invsn` text,
  `item_s_no` text,
  `hs_cd` text,
  `description` text,
  `quantity` text,
  `uqc` text,
  `rate` text,
  `value_f_c` text,
  `fob_inr` text,
  `pmv` text,
  `duty_amt` text,
  `cess_rt` text,
  `cesamt` text,
  `dbkclmd` text,
  `igststat` text,
  `igst_value_item` text,
  `igst_amount` text,
  `schcod` text,
  `scheme_description` text,
  `sqc_msr` text,
  `sqc_uqc` text,
  `state_of_origin_i` text,
  `district_of_origin` text,
  `pt_abroad` text,
  `comp_cess` text,
  `end_use` text,
  `fta_benefit_availed` text,
  `reward_benefit` text,
  `third_party_item` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");



$this->db->query("CREATE TABLE `drawback_details` (
  `drawback_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `item_id` integer,
  `inv_sno` integer,
  `item_sno` integer,
  `dbk_sno` character varying(1000),
  `qty_wt` character varying(1000),
  `value` character varying(1000),
  `dbk_amt` character varying(1000),
  `stalev` character varying(1000),
  `cenlev` character varying(1000),
  `rosctl_amt` character varying(1000),
  `created_at` timestamp,
  `rate` character varying(1000)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `aa_dfia_licence_details` (
  `dfia_licence_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `item_id` integer,
  `inv_s_no` text,
  `item_s_no_` text,
  `licence_no` text,
  `descn_of_export_item` text,
  `exp_s_no` text,
  `expqty` text,
  `uqc_aa` text,
  `fob_value` text,
  `sion` text,
  `descn_of_import_item` text,
  `imp_s_no` text,
  `impqt` text,
  `uqc_` text,
  `indig_imp` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");



$this->db->query("CREATE TABLE `jobbing_details` (
  `jobbing_detail_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sbs_id` integer,
  `be_no` text,
  `be_date` text,
  `port_code_j` text,
  `descn_of_imported_goods` text,
  `qty_imp` text,
  `qty_used` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `cb_file_status` (
  `cb_file_status_id` int4 PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `pdf_filepath` varchar(255),
  `pdf_filename` varchar(255),
  `user_iec_no` varchar(255),
  `lucrative_users_id` int4,
  `file_iec_no` varchar(255),
  `cb_no` varchar(255),
  `cb_date` date,
  `stage` varchar(255),
  `status` varchar(255),
  `remarks` varchar(255),
  `created_at` timestamp,
  `br` varchar(255),
  `is_processed` bool
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");



$this->db->query("CREATE TABLE `courier_bill_summary` (
  `courier_bill_of_entry_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `cb_file_status_id` integer,
  `current_status_of_the_cbe` varchar(255),
  `cbexiv_number` varchar(255),
  `courier_registration_number` varchar(255),
  `name_of_the_authorized_courier` varchar(255),
  `address_of_authorized_courier` varchar(255),
  `particulars_customs_house_agent_name` varchar(255),
  `particulars_customs_house_agent_licence_no` varchar(255),
  `particulars_customs_house_agent_address` varchar(255),
  `import_export_code` varchar(255),
  `import_export_branch_code` varchar(255),
  `particulars_of_the_importer_name` varchar(255),
  `particulars_of_the_importer_address` varchar(255),
  `category_of_importer` varchar(255),
  `type_of_importer` varchar(255),
  `in_case_of_other_importer` varchar(255),
  `authorised_dealer_code_of_bank` varchar(255),
  `class_code` varchar(255),
  `cb_no` varchar(255),
  `cb_date` date,
  `category_of_boe` varchar(255),
  `type_of_boe` varchar(255),
  `kyc_document` varchar(255),
  `kyc_id` varchar(255),
  `state_code` varchar(255),
  `high_sea_sale` varchar(255),
  `ie_code_of_hss` varchar(255),
  `ie_branch_code_of_hss` varchar(255),
  `particulars_high_sea_seller_name` varchar(255),
  `particulars_high_sea_seller_address` varchar(255),
  `use_of_the_first_proviso_under_section_461customs_act1962` varchar(255),
  `request_for_first_check` varchar(255),
  `request_for_urgent_clear_ance_against_temporary_documentation` varchar(255),
  `request_for_extension_of_time_as_per_section_48customs_act1962` varchar(255),
  `reason_in_case_extension_of_time_limit_is_requested` varchar(255),
  `country_of_origin` varchar(255),
  `country_of_consignment` varchar(255),
  `name_of_gateway_port` varchar(255),
  `gateway_igm_number` varchar(255),
  `date_of_entry_inwards_of_gateway_port` varchar(255),
  `case_of_crn` varchar(255),
  `number_of_invoices` integer,
  `total_freight` varchar(255),
  `total_insurance` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");



$this->db->query("CREATE TABLE `manifest_details` (
  `courier_bill_of_entry_id` integer,
  `manifest_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `import_general_manifest_igm_number` varchar(255),
  `date_of_entry_inward` date,
  `master_airway_bill_mawb_number` varchar(255),
  `date_of_mawb` date,
  `house_airway_bill_hawb_number` varchar(255),
  `date_of_hawb` date,
  `marks_and_numbers` varchar(255),
  `number_of_packages` varchar(255),
  `type_of_packages` varchar(255),
  `interest_amount` varchar(255),
  `unit_of_measure_for_gross_weight` varchar(255),
  `gross_weight` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `procurment_details` (
  `courier_bill_of_entry_id` integer,
  `procurment_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `procurement_under_3696_cus` varchar(255),
  `procurement_certificate_number` varchar(255),
  `date_of_issuance_of_certificate` varchar(255),
  `location_code_of_the_cent_ral_excise_office_the_certificate` varchar(255),
  `commissione_rate` varchar(255),
  `division` varchar(255),
  `range` varchar(255),
  `import_under_multiple_in_voices` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `invoice_details` (
  `courier_bill_of_entry_id` integer,
  `invoice_detail_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `invoice_number` text,
  `date_of_invoice` date,
  `purchase_order_number` text,
  `date_of_purchase_order` text,
  `contract_number` text,
  `date_of_contract` text,
  `letter_of_credit` text,
  `date_of_letter_of_credit` text,
  `supplier_details_name` text,
  `supplier_details_address` text,
  `if_supplier_is_not_the_seller_name` text,
  `if_supplier_is_not_the_seller_address` text,
  `broker_agent_details_name` text,
  `broker_agent_details_address` text,
  `nature_of_transaction` text,
  `if_others` text,
  `terms_of_payment` text,
  `conditions_or_restrictions_if_any_attached_to_sale` text,
  `method_of_valuation` text,
  `terms_of_invoice` text,
  `invoice_value` text,
  `currency` text,
  `freight_rate` text,
  `freight_amount` text,
  `freight_currency` text,
  `insurance_rate` text,
  `insurance_amount` text,
  `insurance_currency` text,
  `loading_unloading_and_handling_charges_rule_rate`text,
  `loading_unloading_and_handling_charges_rule_amount`text,
  `loading_unloading_and_handling_charges_rule_currency` text,
  `other_charges_related_to_the_carriage_of_goods_rate` text,
  `other_charges_related_to_the_carriage_of_goods_amount` text,
  `other_charges_related_to_the_carriage_of_goods_currency` text,
  `brokerage_and_commission_rate` text,
  `brokerage_and_commission_amount` text,
  `brokerage_and_commission_currency` text,
  `cost_of_containers_rate` text,
  `cost_of_containers_amount` text,
  `cost_of_containers_currency` text,
  `cost_of_packing_rate` text,
  `cost_of_packing_amount` text,
  `cost_of_packing_currency` text,
  `dismantling_transport_handling_in_country_export_rate` text,
  `dismantling_transport_handling_in_country_export_amount` text,
  `dismantling_transport_handling_in_country_export_currency` text,
  `cost_of_goods_and_ser_vices_supplied_by_buyer_rate` text,
  `cost_of_goods_and_ser_vices_supplied_by_buyer_amount` text,
  `cost_of_goods_and_ser_vices_supplied_by_buyer_currency` text,
  `documentation_rate` text,
  `documentation_amount` text,
  `documentation_currency` text,
  `country_of_origin_certificate_rate` text,
  `country_of_origin_certificate_amount` text,
  `country_of_origin_certificate_currency` text,
  `royalty_and_license_fees_rate` text,
  `royalty_and_license_fees_amount` text,
  `royalty_and_license_fees_currency` text,
  `value_of_proceeds_which_accrue_to_seller_rate` text,
  `value_of_proceeds_which_accrue_to_seller_amount` text,
  `value_of_proceeds_which_accrue_to_seller_currency` text,
  `cost_warranty_service_if_any_provided_seller_rate` text,
  `cost_warranty_service_if_any_provided_seller_amount` text,
  `cost_warranty_service_if_any_provided_seller_currency`text,
  `other_payments_satisfy_obligation_rate` text,
  `other_payments_satisfy_obligation_amount` text,
  `other_payments_satisfy_obligation_currency` text,
  `other_charges_and_payments_if_any_rate` text,
  `other_charges_and_payments_if_any_amount` text,
  `other_charges_and_payments_if_any_currency` text,
  `discount_amount` text,
  `discount_currency` text,
  `rate` text,
  `amount` text,
  `any_other_information_which_has_a_bearing_on_value` text,
  `are_the_buyer_and_seller_related` text,
  `if_the_buyer_seller_has_the_relationship_examined_earlier_svb`text,
  `svb_reference_number` text,
  `svb_date` text,
  `indication_for_provisional_final` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `igm_details` (
  `courier_bill_of_entry_id` integer,
  `igm_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `airlines` varchar(255),
  `flight_no` varchar(255),
  `airport_of_arrival` varchar(255),
  `date_of_arrival` date,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

$this->db->query("CREATE TABLE `container_details` (
  `courier_bill_of_entry_id` integer,
  `container_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `container_details_srno` int,
  `container` varchar(255),
  `seal_number` varchar(255),
  `fcl_lcl` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

$this->db->query("CREATE TABLE `bond_details` (
  `courier_bill_of_entry_id` integer,
  `bond_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `bond_details_srno` int,
  `bond_type` varchar(255),
  `bond_number` varchar(255),
  `clearance_of_imported_goods_bond_already_registered_customs` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `items_details` (
  `courier_bill_of_entry_id` integer,
  `items_detail_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `case_for_reimport` varchar(255),
  `import_against_license` varchar(255),
  `serial_number_in_invoice` varchar(255),
  `item_description` varchar(255),
  `general_description` varchar(255),
  `currency_for_unit_price` varchar(255),
  `unit_price` varchar(255),
  `unit_of_measure` varchar(255),
  `quantity` varchar(255),
  `rate_of_exchange` varchar(255),
  `accessories_if_any` varchar(255),
  `name_of_manufacturer` varchar(255),
  `brand` varchar(255),
  `model` varchar(255),
  `grade` varchar(255),
  `specification` varchar(255),
  `end_use_of_item` varchar(255),
  `country_of_origin` varchar(255),
  `bill_of_entry_number` varchar(255),
  `details_in_case_of_previous_imports_date` varchar(255),
  `details_in_case_previous_imports_currency` varchar(255),
  `unit_value` varchar(255),
  `customs_house` varchar(255),
  `ritc` varchar(255),
  `ctsh` varchar(255),
  `cetsh` varchar(255),
  `currency_for_rsp` varchar(255),
  `retail_sales_price_per_unit` varchar(255),
  `exim_scheme_code_if_any` varchar(255),
  `para_noyear_of_exim_policy` varchar(255),
  `items_details_are_the_buyer_and_seller_related` varchar(255),
  `if_the_buyer_and_seller_relation_examined_earlier_by_svb` varchar(255),
  `svb_reference_number` varchar(255),
  `svb_date` varchar(255),
  `indication_for_provisional_final` varchar(255),
  `shipping_bill_number` varchar(255),
  `shipping_bill_date` varchar(255),
  `port_of_export` varchar(255),
  `invoice_number_of_shipping_bill` varchar(255),
  `item_serial_number_in_shipping_bill` varchar(255),
  `freight` varchar(255),
  `insurance` varchar(255),
  `total_repair_cost_including_cost_of_materials` varchar(255),
  `additional_duty_exemption_requested` varchar(255),
  `notification_number` varchar(255),
  `serial_number_in_notification` varchar(255),
  `license_registration_number` varchar(255),
  `license_registration_date` varchar(255),
  `debit_value_rs` varchar(255),
  `unit_of_measure_for_quantity_to_be_debited` varchar(255),
  `debit_quantity` varchar(255),
  `item_serial_number_in_license` varchar(255),
  `assessable_value` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `notification_used_for_items` (
  `items_detail_id` integer,
  `item_notification_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `notification_item_srno` int,
  `notification_number` varchar(255),
  `serial_number_of_notification` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");


$this->db->query("CREATE TABLE `duty_details` (
  `items_detail_id` integer,
  `duty_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `BCD_duty_head` varchar(255),
  `BCD_ad_valorem` varchar(255),
  `BCD_specific_rate` varchar(255),
  `BCD_duty_forgone` varchar(255),
  `BCD_duty_amount` varchar(255),
  `AIDC_duty_head` varchar(255),
  `AIDC_ad_valorem` varchar(255),
  `AIDC_specific_rate` varchar(255),
  `AIDC_duty_forgone` varchar(255),
  `AIDC_duty_amount` varchar(255),
  `SW_Srchrg_duty_head` varchar(255),
  `SW_Srchrg_ad_valorem` varchar(255),
  `SW_Srchrg_specific_rate` varchar(255),
  `SW_Srchrg_duty_forgone` varchar(255),
  `SW_Srchrg_duty_amount` varchar(255),
  `IGST_duty_head` varchar(255),
  `IGST_ad_valorem` varchar(255),
  `IGST_specific_rate` varchar(255),
  `IGST_duty_forgone` varchar(255),
  `IGST_duty_amount` varchar(255),
  `CMPNSTRY_duty_head` varchar(255),
  `CMPNSTRY_ad_valorem` varchar(255),
  `CMPNSTRY_specific_rate` varchar(255),
  `CMPNSTRY_duty_forgone` varchar(255),
  `CMPNSTRY_duty_amount` varchar(255),
  `dummy5_duty_head` varchar(255),
  `dummy5_ad_valorem` varchar(255),
  `dummy5_specific_rate` varchar(255),
  `dummy5_duty_forgone` varchar(255),
  `dummy5_duty_amount` varchar(255),
  `dummy6_duty_head` varchar(255),
  `dummy6_ad_valorem` varchar(255),
  `dummy6_specific_rate` varchar(255),
  `dummy6_duty_forgone` varchar(255),
  `dummy6_duty_amount` varchar(255),
  `dummy7_duty_head` varchar(255),
  `dummy7_ad_valorem` varchar(255),
  `dummy7_specific_rate` varchar(255),
  `dummy7_duty_forgone` varchar(255),
  `dummy7_duty_amount` varchar(255),
  `dummy8_duty_head` varchar(255),
  `dummy8_ad_valorem` varchar(255),
  `dummy8_specific_rate` varchar(255),
  `dummy8_duty_forgone` varchar(255),
  `dummy8_duty_amount` varchar(255),
  `dummy9_duty_head` varchar(255),
  `dummy9_ad_valorem` varchar(255),
  `dummy9_specific_rate` varchar(255),
  `dummy9_duty_forgone` varchar(255),
  `dummy9_duty_amount` varchar(255),
  `dummy10_duty_head` varchar(255),
  `dummy10_ad_valorem` varchar(255),
  `dummy10_specific_rate` varchar(255),
  `dummy10_duty_forgone` varchar(255),
  `dummy10_duty_amount` varchar(255),
  `dummy11_duty_head` varchar(255),
  `dummy11_ad_valorem` varchar(255),
  `dummy11_specific_rate` varchar(255),
  `dummy11_duty_forgone` varchar(255),
  `dummy11_duty_amount` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

$this->db->query("CREATE TABLE `payment_details` (
  `courier_bill_of_entry_id` integer,
  `payment_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `payment_details_srno` integer, 
  `tr6_challan_number` varchar(255),
  `total_amount` varchar(255),
  `challan_date` date,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4");

}
}
        
		if($result)

		{	
		    $this->session->set_flashdata('success','Record update Successfully!');
		}
		else

		{
			$this->session->set_flashdata('error','Record not updated!');
		}

	   redirect('/admin/iec_signup');
        print_r($result);die();
	    print_r(implode($pass)); die();
	}
	
	
	 
	public function get_worksheet_name_by_type(){
        $post= $this->input->post();
        $user_id=$_SESSION["user_id"];
	    $iec_id =$_SESSION["iec_no"];
        $db_name = $_SESSION['database_prefix'].$user_id;
        $this->db->query("use ".$db_name."");
        $type = $_POST['type'];
	   /* $sql = 'SELECT *  FROM tbl_sheets  where type_id ='.$type.'';
        $query = $this->db->query( $sql);
         //print_r($this->db->last_query()); die();
        $importers=$query->result_array();
        $data['list_woksheet']=$importers;
        echo json_encode($data['list_woksheet']);*/
        
        
        if(isset($_POST['type']))
{
 //$id = join("','", $_POST['type']);
 $query = "SELECT *  FROM tbl_sheets  where type_id =".$type;
 $statement = $this->db->query($query);
 //$statement->execute();
 $result =$statement->result_array();
 $output = '';
 foreach($result as $row)
 {
  $output .= '<option value="'.$row["tbl_sheet_name"].'">'.$row["tbl_sheet_name"].'</option>';
 }
 echo $output;
}
        
        
    
}

public function get_worksheet_columns(){
    $data='';
    $user_id=$_SESSION["user_id"];
	$iec_id =$_SESSION["iec_no"];
    $db_name = $_SESSION['database_prefix'].$user_id;
    $this->db->query("use ".$db_name."");
    $post= $this->input->post();
    $data1 = $post['selected1'];   
     
    $len=@strlen(@$data1);
    if($len>0){
    $strs = explode(',', $data1);
      foreach($strs as $str){
           echo $sql ="SHOW COLUMNS FROM ".$str;
        $query = $this->db->query($sql);
        $columns[]=$query->result_array();
        $data['columns'][]=$columns;
      }
        
     }
        echo json_encode($data);
}
	    
	public function iec_reports(){
	    $user_id=$_SESSION["user_id"];
	     $user_id=$_SESSION["user_id"];
	    if($user_id!=1){
	        $db_name = $_SESSION['database_prefix'].$user_id;
	        $this->db->query("use ".$db_name."");
	    }
	    $iec_id =$_SESSION["iec_no"];
	    $this->load->view('common/header');
		$this->load->view('admin/import_reports_setting');
		$this->load->view('common/footer');	
	}
}