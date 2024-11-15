<?php defined("BASEPATH") or exit("No direct script access allowed");

class Admin_cron extends CI_Controller
{
    public function __construct()
    {
        @parent::__construct();
        $this->load->model("admin/Common_model");
        $this->load->dbforge();
        
         $this->db_doc = $this->load->database('second', TRUE);
    }

    public function check_login()
    {
        if (
            $this->session->userdata("user_id") == "" ||
            $this->session->userdata("user_name") == "" ||
            ($this->session->userdata("user_email") == "" ||
                $this->session->userdata("user_role") == "")
        ) {
            redirect("login");
        }
    }
public function item_details_master()
    {
        /******************************************************************Start item_details***************************************************************************************/
        $idm1 = "SELECT COUNT(*) FROM item_details";
        $statement_idm1 = $this->db->query($idm1);
        $result_idm1 = $statement_idm1->result_array();
        print_r($result_idm1);
        // Set the batch size
        $batchSize = 9000;

        // Loop through the records in batches of 9000
        for (
            $offset = 0;
            $offset < $result_idm1[0]["count"];
            $offset += $batchSize
        ) {
            // Query to retrieve the current batch
            $query_item_details = "SELECT item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id LIMIT $batchSize OFFSET $offset";

            // echo $query_item_details = "SELECT item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id  LIMIT 9000";
            $statement_item_details = $this->db->query($query_item_details);
            $iecwise_item_details = [];
            $result_item_details = $statement_item_details->result_array();
            // print_r($result_item_details);exit;

            foreach ($result_item_details as $str_item_details) {
                $iec_item_details = $str_item_details["iec"];
                $sql_item_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_item_details'";
                $iecwise_item_details = $this->db->query($sql_item_details);
                $iecwise_data_item_details = $iecwise_item_details->result_array();
                $db1_item_details = $this->database_connection(
                    $iecwise_data_item_details[0]["lucrative_users_id"]
                );
                /* $hostname='localhost';
        $username='root';
        $password='%!#^bFjB)z8C';
        $db_name1='lucrativeesystem_D2D_master';
        $db1_item_details = new mysqli($hostname,$username,$password,$db_name1); */
                // Check connection
                if ($db1_item_details->connect_error) {
                    die(
                        "Connection failed: " . $db1_item_details->connect_error
                    );
                } else {
                    echo "Connected successfully";
                }
                if (get_magic_quotes_gpc()) {
                    $item_id = addslashes($str_item_details["item_id"]);
                    $invoice_id = addslashes($str_item_details["invoice_id"]);
                    $invsn = addslashes($str_item_details["invsn"]);
                    $hs_cd = addslashes($str_item_details["hs_cd"]);
                    $description = addslashes(
                        $str_item_details["description"]
                    );
                    $quantity = addslashes($str_item_details["quantity"]);
                    $rate = addslashes($str_item_details["rate"]);
                    $value_f_c = addslashes($str_item_details["value_f_c"]);
                    $fob_inr = addslashes($str_item_details["fob_inr"]);
                    $pmv = addslashes($str_item_details["pmv"]);
                    $duty_amt = addslashes($str_item_details["duty_amt"]);
                    $cess_rt = addslashes($str_item_details["cess_rt"]);
                    $cesamt = addslashes($str_item_details["cesamt"]);
                    $dbkclmd = addslashes($str_item_details["dbkclmd"]);
                    $igststat = addslashes($str_item_details["igststat"]);
                    $igst_value_item = addslashes(
                        $str_item_details["igst_value_item"]
                    );
                    $igst_amount = addslashes(
                        $str_item_details["igst_amount"]
                    );
                    $schcod = addslashes($str_item_details["schcod"]);
                    $scheme_description = addslashes(
                        $str_item_details["scheme_description"]
                    );
                    $sqc_msr = addslashes($str_item_details["sqc_msr"]);
                    $sqc_uqc = addslashes($str_item_details["sqc_uqc"]);
                    $state_of_origin_i = addslashes(
                        $str_item_details["state_of_origin_i"]
                    );
                    $district_of_origin = addslashes(
                        $str_item_details["district_of_origin"]
                    );
                    $pt_abroad = addslashes($str_item_details["pt_abroad"]);
                    $comp_cess = addslashes($str_item_details["comp_cess"]);
                    $end_use = addslashes($str_item_details["end_use"]);
                    $fta_benefit_availed = addslashes(
                        $str_item_details["fta_benefit_availed"]
                    );
                    $reward_benefit = addslashes(
                        $str_item_details["reward_benefit"]
                    );
                    $third_party_item = addslashes(
                        $str_item_details["third_party_item"]
                    );
                    $created_at = addslashes($str_item_details["created_at"]);
                } else {
                    $item_id = $str_item_details["item_id"];
                    $invoice_id = $str_item_details["invoice_id"];
                    $invsn = $str_item_details["invsn"];
                    $hs_cd = $str_item_details["hs_cd"];
                    $description = $str_item_details["description"];
                    $uqc = $str_item_details["uqc"];
                    $rate = $str_item_details["rate"];
                    $value_f_c = $str_item_details["value_f_c"];
                    $fob_inr = $str_item_details["fob_inr"];
                    $pmv = $str_item_details["pmv"];
                    $duty_amt = $str_item_details["duty_amt"];
                    $cess_rt = $str_item_details["cess_rt"];
                    $cesamt = $str_item_details["cesamt"];
                    $dbkclmd = $str_item_details["dbkclmd"];
                    $igststat = $str_item_details["igststat"];
                    $igst_value_item = $str_item_details["igst_value_item"];
                    $igst_amount = $str_item_details["igst_amount"];
                    $schcod = $str_item_details["schcod"];
                    $scheme_description =
                        $str_item_details["scheme_description"];
                    $sqc_msr = $str_item_details["sqc_msr"];
                    $sqc_uqc = $str_item_details["sqc_uqc"];
                    $state_of_origin_i = $str_item_details["state_of_origin_i"];
                    $district_of_origin =
                        $str_item_details["district_of_origin"];
                    $pt_abroad = $str_item_details["pt_abroad"];
                    $comp_cess = $str_item_details["comp_cess"];
                    $end_use = $str_item_details["end_use"];
                    $fta_benefit_availed =
                        $str_item_details["fta_benefit_availed"];
                    $reward_benefit = $str_item_details["reward_benefit"];
                    $third_party_item = $str_item_details["third_party_item"];
                    $created_at = $str_item_details["created_at"];
                }
                echo $sql_insert_item_details =
                    "INSERT INTO `item_details`(`item_id`, `invoice_id`, `invsn`, `item_s_no`, `hs_cd`, `description`, `quantity`, `uqc`, `rate`, `value_f_c`, `fob_inr`, `pmv`, `duty_amt`, `cess_rt`,`cesamt`, `dbkclmd`,
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
 VALUES ('" .
                    $item_id .
                    "',
 '" .
                    $invoice_id .
                    "',
 '" .
                    $invsn .
                    "',
 '" .
                    $item_s_no .
                    "',
 '" .
                    $hs_cd .
                    "',
 '" .
                    $description .
                    "',
 '" .
                    $quantity .
                    "',
 '" .
                    $uqc .
                    "',
 '" .
                    $rate .
                    "',
 '" .
                    $value_f_c .
                    "',
 '" .
                    $fob_inr .
                    "',
 '" .
                    $pmv .
                    "',
 '" .
                    $duty_amt .
                    "',
 '" .
                    $cess_rt .
                    "',
 '" .
                    $cesamt .
                    "',
 '" .
                    $dbkclmd .
                    "',
 '" .
                    $igststat .
                    "',
 '" .
                    $igst_value_item .
                    "',
 '" .
                    $igst_amount .
                    "',
 '" .
                    $schcod .
                    "',
 '" .
                    $scheme_description .
                    "',
 '" .
                    $sqc_msr .
                    "',
 '" .
                    $sqc_uqc .
                    "',
 '" .
                    $state_of_origin_i .
                    "',
 '" .
                    "',
 '" .
                    $pt_abroad .
                    "',
 '" .
                    $comp_cess .
                    "',
 '" .
                    $end_use .
                    "',
 '" .
                    $fta_benefit_availed .
                    "',
 '" .
                    $reward_benefit .
                    "',
 '" .
                    $third_party_item .
                    "',
 '" .
                    $created_at .
                    "')";
                $copy_insert_item_details = $db1_item_details->query(
                    $sql_insert_item_details
                );
            }
        }
        /******************************************************************Start item_details***************************************************************************************/
    }

public function duties_and_additional_details_master()
    {
        /******************************************************************Start duties_and_additional_details***************************************************************************************/
        $q1 = "SELECT COUNT(*) FROM duties_and_additional_details";
        $statement_q1 = $this->db->query($q1);
        $result_q1 = $statement_q1->result_array();

        // Set the batch size
        $batchSize_duties = 9000;

        // Loop through the records in batches of 9000
        for (
            $offset_duties = 0;
            $offset_duties < $result_q1[0]["count"];
            $offset_duties += $batchSize_duties
        ) {
            // Query to retrieve the current batch

            $query_duties_and_additional_details = "SELECT duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id FROM duties_and_additional_details LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id LIMIT $batchSize_duties OFFSET $offset_duties";
            $statement_duties_and_additional_details = $this->db->query(
                $query_duties_and_additional_details
            );
            $iecwise_duties_and_additional_details = [];
            $result_duties_and_additional_details = $statement_duties_and_additional_details->result_array();
            // print_r($result_duties_and_additional_details);

            $hostname = "localhost";
            $username = "root";
            $password = "%!#^bFjB)z8C";
            $db_name1 = "lucrativeesystem_D2D_master";
            $db1_duties_and_additional_details = new mysqli(
                $hostname,
                $username,
                $password,
                $db_name1
            );
            // Check connection
            if ($db1_duties_and_additional_details->connect_error) {
                die(
                    "Connection failed: " .
                        $db1_duties_and_additional_details->connect_error
                );
            } else {
                // echo "Connected successfully";
            }
            //return $Db1;
            foreach (
                $result_duties_and_additional_details
                as $str_duties_and_additional_details
            ) {
                //echo $lastname;exit;
                /* $iec_duties_and_additional_details=$str_duties_and_additional_details['iec_no'];
        $sql_duties_and_additional_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_duties_and_additional_details'";
        $iecwise_duties_and_additional_details= $this->db->query($sql_duties_and_additional_details);
        $iecwise_data_duties_and_additional_details =$iecwise_duties_and_additional_details->result_array();
        $db1_duties_and_additional_details=$this->database_connection($iecwise_data_duties_and_additional_details[0]['lucrative_users_id']);*/
    //            if (get_magic_quotes_gpc()) {
                    $boe_id = addslashes(
                        $str_duties_and_additional_details["boe_id"]
                    );
                    $invoice_id = addslashes(
                        $str_duties_and_additional_details["invoice_id"]
                    );
                    $duties_id = addslashes(
                        $str_duties_and_additional_details["duties_id"]
                    );
                    $s_no = addslashes(
                        $str_duties_and_additional_details["s_no"]
                    );
                    $cth = addslashes(
                        $str_duties_and_additional_details["cth"]
                    );
                    $description = addslashes(
                        $str_duties_and_additional_details["description"]
                    );
                    $unit_price = addslashes(
                        $str_duties_and_additional_details["unit_price"]
                    );
                    $quantity = addslashes(
                        $str_duties_and_additional_details["quantity"]
                    );
                    $uqc = addslashes(
                        $str_duties_and_additional_details["uqc"]
                    );
                    $amount = addslashes(
                        $str_duties_and_additional_details["amount"]
                    );
                    $invsno = addslashes(
                        $str_duties_and_additional_details["invsno"]
                    );
                    $itemsn = addslashes(
                        $str_duties_and_additional_details["itemsn"]
                    );
                    $cth_item_detail = addslashes(
                        $str_duties_and_additional_details["cth_item_detail"]
                    );
                    $ceth = addslashes(
                        $str_duties_and_additional_details["ceth"]
                    );
                    $item_description = addslashes(
                        $str_duties_and_additional_details["item_description"]
                    );
                    $fs = addslashes(
                        $str_duties_and_additional_details["fs"]
                    );
                    $pq = addslashes(
                        $str_duties_and_additional_details["pq"]
                    );
                    $dc = addslashes(
                        $str_duties_and_additional_details["dc"]
                    );
                    $wc = addslashes(
                        $str_duties_and_additional_details["wc"]
                    );
                    $aq = addslashes(
                        $str_duties_and_additional_details["aq"]
                    );
                    $upi = addslashes(
                        $str_duties_and_additional_details["upi"]
                    );
                    $coo = addslashes(
                        $str_duties_and_additional_details["coo"]
                    );
                    $c_qty = addslashes(
                        $str_duties_and_additional_details["c_qty"]
                    );
                    $c_uqc = addslashes(
                        $str_duties_and_additional_details["c_uqc"]
                    );
                    $s_qty = addslashes(
                        $str_duties_and_additional_details["s_qty"]
                    );
                    $s_uqc = addslashes(
                        $str_duties_and_additional_details["s_uqc"]
                    );
                    $sch = addslashes(
                        $str_duties_and_additional_details["sch"]
                    );
                    $stdn_pr = addslashes(
                        $str_duties_and_additional_details["stdn_pr"]
                    );
                    $rsp = addslashes(
                        $str_duties_and_additional_details["rsp"]
                    );
                    $reimp = addslashes(
                        $str_duties_and_additional_details["reimp"]
                    );
                    $prov = addslashes(
                        $str_duties_and_additional_details["prov"]
                    );
                    $end_use = addslashes(
                        $str_duties_and_additional_details["end_use"]
                    );
                    $prodn = addslashes(
                        $str_duties_and_additional_details["prodn"]
                    );
                    $cntrl = addslashes(
                        $str_duties_and_additional_details["cntrl"]
                    );
                    $qualfr = addslashes(
                        $str_duties_and_additional_details["qualfr"]
                    );
                    $contnt = addslashes(
                        $str_duties_and_additional_details["contnt"]
                    );
                    $stmnt = addslashes(
                        $str_duties_and_additional_details["stmnt"]
                    );
                    $sup_docs = addslashes(
                        $str_duties_and_additional_details["sup_docs"]
                    );
                    $assess_value = addslashes(
                        $str_duties_and_additional_details["assess_value"]
                    );
                    $total_duty = addslashes(
                        $str_duties_and_additional_details["total_duty"]
                    );
                    $bcd_notn_no = addslashes(
                        $str_duties_and_additional_details["bcd_notn_no"]
                    );
                    $bcd_notn_sno = addslashes(
                        $str_duties_and_additional_details["bcd_notn_sno"]
                    );
                    $bcd_rate = addslashes(
                        $str_duties_and_additional_details["bcd_rate"]
                    );
                    $bcd_amount = addslashes(
                        $str_duties_and_additional_details["bcd_amount"]
                    );
                    $bcd_duty_fg = addslashes(
                        $str_duties_and_additional_details["bcd_duty_fg"]
                    );
                    $acd_notn_no = addslashes(
                        $str_duties_and_additional_details["acd_notn_no"]
                    );
                    $acd_notn_sno = addslashes(
                        $str_duties_and_additional_details["acd_notn_sno"]
                    );
                    $acd_rate = addslashes(
                        $str_duties_and_additional_details["acd_rate"]
                    );
                    $acd_amount = addslashes(
                        $str_duties_and_additional_details["acd_amount"]
                    );
                    $acd_duty_fg = addslashes(
                        $str_duties_and_additional_details["acd_duty_fg"]
                    );
                    $sws_notn_no = addslashes(
                        $str_duties_and_additional_details["sws_notn_no"]
                    );
                    $sws_notn_sno = addslashes(
                        $str_duties_and_additional_details["sws_notn_sno"]
                    );
                    $sws_rate = addslashes(
                        $str_duties_and_additional_details["sws_rate"]
                    );
                    $sws_amount = addslashes(
                        $str_duties_and_additional_details["sws_amount"]
                    );
                    $sws_duty_fg = addslashes(
                        $str_duties_and_additional_details["sws_duty_fg"]
                    );
                    $sad_notn_no = addslashes(
                        $str_duties_and_additional_details["sad_notn_no"]
                    );
                    $sad_notn_sno = addslashes(
                        $str_duties_and_additional_details["sad_notn_sno"]
                    );
                    $sad_rate = addslashes(
                        $str_duties_and_additional_details["sad_rate"]
                    );
                    $sad_amount = addslashes(
                        $str_duties_and_additional_details["sad_amount"]
                    );
                    $sad_duty_fg = addslashes(
                        $str_duties_and_additional_details["sad_duty_fg"]
                    );
                    $igst_notn_no = addslashes(
                        $str_duties_and_additional_details["igst_notn_no"]
                    );
                    $igst_notn_sno = addslashes(
                        $str_duties_and_additional_details["igst_notn_sno"]
                    );
                    $igst_rate = addslashes(
                        $str_duties_and_additional_details["igst_rate"]
                    );
                    $igst_amount = addslashes(
                        $str_duties_and_additional_details["igst_amount"]
                    );
                    $igst_duty_fg = addslashes(
                        $str_duties_and_additional_details["igst_duty_fg"]
                    );
                    $g_cess_notn_no = addslashes(
                        $str_duties_and_additional_details["g_cess_notn_no"]
                    );
                    $g_cess_notn_sno = addslashes(
                        $str_duties_and_additional_details["g_cess_notn_sno"]
                    );
                    $g_cess_rate = addslashes(
                        $str_duties_and_additional_details["g_cess_rate"]
                    );
                    $g_cess_amount = addslashes(
                        $str_duties_and_additional_details["g_cess_amount"]
                    );
                    $g_cess_duty_fg = addslashes(
                        $str_duties_and_additional_details["g_cess_duty_fg"]
                    );
                    $add_notn_no = addslashes(
                        $str_duties_and_additional_details["add_notn_no"]
                    );
                    $add_notn_sno = addslashes(
                        $str_duties_and_additional_details["add_notn_sno"]
                    );
                    $add_rate = addslashes(
                        $str_duties_and_additional_details["add_rate"]
                    );
                    $add_amount = addslashes(
                        $str_duties_and_additional_details["add_amount"]
                    );
                    $add_duty_fg = addslashes(
                        $str_duties_and_additional_details["add_duty_fg"]
                    );
                    $cvd_notn_no = addslashes(
                        $str_duties_and_additional_details["cvd_notn_no"]
                    );
                    $cvd_notn_sno = addslashes(
                        $str_duties_and_additional_details["cvd_notn_sno"]
                    );
                    $cvd_rate = addslashes(
                        $str_duties_and_additional_details["cvd_rate"]
                    );
                    $cvd_amount = addslashes(
                        $str_duties_and_additional_details["cvd_amount"]
                    );
                    $cvd_duty_fg = addslashes(
                        $str_duties_and_additional_details["cvd_duty_fg"]
                    );
                    $sg_notn_no = addslashes(
                        $str_duties_and_additional_details["sg_notn_no"]
                    );
                    $sg_notn_sno = addslashes(
                        $str_duties_and_additional_details["sg_notn_sno"]
                    );
                    $sg_rate = addslashes(
                        $str_duties_and_additional_details["sg_rate"]
                    );
                    $sg_amount = addslashes(
                        $str_duties_and_additional_details["sg_amount"]
                    );
                    $sg_duty_fg = addslashes(
                        $str_duties_and_additional_details["sg_duty_fg"]
                    );
                    $t_value_notn_no = addslashes(
                        $str_duties_and_additional_details["t_value_notn_no"]
                    );
                    $t_value_notn_sno = addslashes(
                        $str_duties_and_additional_details["t_value_notn_sno"]
                    );
                    $t_value_rate = addslashes(
                        $str_duties_and_additional_details["t_value_rate"]
                    );
                    $t_value_amount = addslashes(
                        $str_duties_and_additional_details["t_value_amount"]
                    );
                    $t_value_duty_fg = addslashes(
                        $str_duties_and_additional_details["t_value_duty_fg"]
                    );
                    $sp_excd_notn_no = addslashes(
                        $str_duties_and_additional_details["sp_excd_notn_no"]
                    );
                    $sp_excd_notn_sno = addslashes(
                        $str_duties_and_additional_details["sp_excd_notn_sno"]
                    );
                    $sp_excd_rate = addslashes(
                        $str_duties_and_additional_details["sp_excd_rate"]
                    );
                    $sp_excd_amount = addslashes(
                        $str_duties_and_additional_details["sp_excd_amount"]
                    );
                    $sp_excd_duty_fg = addslashes(
                        $str_duties_and_additional_details["sp_excd_duty_fg"]
                    );
                    $chcess_notn_no = addslashes(
                        $str_duties_and_additional_details["chcess_notn_no"]
                    );
                    $chcess_notn_sno = addslashes(
                        $str_duties_and_additional_details["chcess_notn_sno"]
                    );
                    $chcess_rate = addslashes(
                        $str_duties_and_additional_details["chcess_rate"]
                    );
                    $chcess_amount = addslashes(
                        $str_duties_and_additional_details["chcess_amount"]
                    );
                    $chcess_duty_fg = addslashes(
                        $str_duties_and_additional_details["chcess_duty_fg"]
                    );
                    $tta_notn_no = addslashes(
                        $str_duties_and_additional_details["tta_notn_no"]
                    );
                    $tta_notn_sno = addslashes(
                        $str_duties_and_additional_details["tta_notn_sno"]
                    );
                    $tta_rate = addslashes(
                        $str_duties_and_additional_details["tta_rate"]
                    );
                    $tta_amount = addslashes(
                        $str_duties_and_additional_details["tta_amount"]
                    );
                    $tta_duty_fg = addslashes(
                        $str_duties_and_additional_details["tta_duty_fg"]
                    );
                    $cess_notn_no = addslashes(
                        $str_duties_and_additional_details["cess_notn_no"]
                    );
                    $cess_notn_sno = addslashes(
                        $str_duties_and_additional_details["cess_notn_sno"]
                    );
                    $cess_rate = addslashes(
                        $str_duties_and_additional_details["cess_rate"]
                    );
                    $cess_amount = addslashes(
                        $str_duties_and_additional_details["cess_amount"]
                    );
                    $cess_duty_fg = addslashes(
                        $str_duties_and_additional_details["cess_duty_fg"]
                    );
                    $caidc_cvd_edc_notn_no = addslashes(
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_notn_no"
                        ]
                    );
                    $caidc_cvd_edc_notn_sno = addslashes(
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_notn_sno"
                        ]
                    );
                    $caidc_cvd_edc_rate = addslashes(
                        $str_duties_and_additional_details["caidc_cvd_edc_rate"]
                    );
                    $caidc_cvd_edc_amount = addslashes(
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_amount"
                        ]
                    );
                    $caidc_cvd_edc_duty_fg = addslashes(
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_duty_fg"
                        ]
                    );
                    $eaidc_cvd_hec_notn_no = addslashes(
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_notn_no"
                        ]
                    );
                    $eaidc_cvd_hec_notn_sno = addslashes(
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_notn_sno"
                        ]
                    );
                    $eaidc_cvd_hec_rate = addslashes(
                        $str_duties_and_additional_details["eaidc_cvd_hec_rate"]
                    );
                    $eaidc_cvd_hec_amount = addslashes(
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_amount"
                        ]
                    );
                    $eaidc_cvd_hec_duty_fg = addslashes(
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_duty_fg"
                        ]
                    );
                    $cus_edc_notn_no = addslashes(
                        $str_duties_and_additional_details["cus_edc_notn_no"]
                    );
                    $cus_edc_notn_sno = addslashes(
                        $str_duties_and_additional_details["cus_edc_notn_sno"]
                    );
                    $cus_edc_rate = addslashes(
                        $str_duties_and_additional_details["cus_edc_rate"]
                    );
                    $cus_edc_amount = addslashes(
                        $str_duties_and_additional_details["cus_edc_amount"]
                    );
                    $cus_edc_duty_fg = addslashes(
                        $str_duties_and_additional_details["cus_edc_duty_fg"]
                    );
                    $cus_hec_notn_no = addslashes(
                        $str_duties_and_additional_details["cus_hec_notn_no"]
                    );
                    $cus_hec_notn_sno = addslashes(
                        $str_duties_and_additional_details["cus_hec_notn_sno"]
                    );
                    $cus_hec_rate = addslashes(
                        $str_duties_and_additional_details["cus_hec_rate"]
                    );
                    $cus_hec_amount = addslashes(
                        $str_duties_and_additional_details["cus_hec_amount"]
                    );
                    $cus_hec_duty_fg = addslashes(
                        $str_duties_and_additional_details["cus_hec_duty_fg"]
                    );
                    $ncd_notn_no = addslashes(
                        $str_duties_and_additional_details["ncd_notn_no"]
                    );
                    $ncd_notn_sno = addslashes(
                        $str_duties_and_additional_details["ncd_notn_sno"]
                    );
                    $ncd_rate = addslashes(
                        $str_duties_and_additional_details["ncd_rate"]
                    );
                    $ncd_amount = addslashes(
                        $str_duties_and_additional_details["ncd_amount"]
                    );
                    $ncd_duty_fg = addslashes(
                        $str_duties_and_additional_details["ncd_duty_fg"]
                    );
                    $aggr_notn_no = addslashes(
                        $str_duties_and_additional_details["aggr_notn_no"]
                    );
                    $aggr_notn_sno = addslashes(
                        $str_duties_and_additional_details["aggr_notn_sno"]
                    );
                    $aggr_rate = addslashes(
                        $str_duties_and_additional_details["aggr_rate"]
                    );
                    $aggr_amount = addslashes(
                        $str_duties_and_additional_details["aggr_amount"]
                    );
                    $aggr_duty_fg = addslashes(
                        $str_duties_and_additional_details["aggr_duty_fg"]
                    );
                    $invsno_add_details = addslashes(
                        $str_duties_and_additional_details["invsno_add_details"]
                    );
                    $itmsno_add_details = addslashes(
                        $str_duties_and_additional_details["itmsno_add_details"]
                    );
                    $refno = addslashes(
                        $str_duties_and_additional_details["refno"]
                    );
                    $refdt = addslashes(
                        $str_duties_and_additional_details["refdt"]
                    );
                    $prtcd_svb_d = addslashes(
                        $str_duties_and_additional_details["prtcd_svb_d"]
                    );
                    $lab = addslashes(
                        $str_duties_and_additional_details["lab"]
                    );
                    $pf = addslashes(
                        $str_duties_and_additional_details["pf"]
                    );
                    $load_date = addslashes(
                        $str_duties_and_additional_details["load_date"]
                    );
                    $pf_ = addslashes(
                        $str_duties_and_additional_details["pf_"]
                    );
                    $beno = addslashes(
                        $str_duties_and_additional_details["beno"]
                    );
                    $bedate = addslashes(
                        $str_duties_and_additional_details["bedate"]
                    );
                    $prtcd = addslashes(
                        $str_duties_and_additional_details["prtcd"]
                    );
                    $unitprice = addslashes(
                        $str_duties_and_additional_details["unitprice"]
                    );
                    $currency_code = addslashes(
                        $str_duties_and_additional_details["currency_code"]
                    );
                    $frt = addslashes(
                        $str_duties_and_additional_details["frt"]
                    );
                    $ins = addslashes(
                        $str_duties_and_additional_details["ins"]
                    );
                    $duty = addslashes(
                        $str_duties_and_additional_details["duty"]
                    );
                    $sb_no = addslashes(
                        $str_duties_and_additional_details["sb_no"]
                    );
                    $sb_dt = addslashes(
                        $str_duties_and_additional_details["sb_dt"]
                    );
                    $portcd = addslashes(
                        $str_duties_and_additional_details["portcd"]
                    );
                    $sinv = addslashes(
                        $str_duties_and_additional_details["sinv"]
                    );
                    $sitemn = addslashes(
                        $str_duties_and_additional_details["sitemn"]
                    );
                    $type = addslashes(
                        $str_duties_and_additional_details["type"]
                    );
                    $manufact_cd = addslashes(
                        $str_duties_and_additional_details["manufact_cd"]
                    );
                    $source_cy = addslashes(
                        $str_duties_and_additional_details["source_cy"]
                    );
                    $trans_cy = addslashes(
                        $str_duties_and_additional_details["trans_cy"]
                    );
                    $address = addslashes(
                        $str_duties_and_additional_details["address"]
                    );
                    $accessory_item_details = addslashes(
                        $str_duties_and_additional_details[
                            "accessory_item_details"
                        ]
                    );
                    $notno = addslashes(
                        $str_duties_and_additional_details["notno"]
                    );
                    $slno = addslashes(
                        $str_duties_and_additional_details["slno"]
                    );
                    $created_at = addslashes(
                        $str_duties_and_additional_details["created_at"]
                    );
              /*  } else {
                    $boe_id = $str_duties_and_additional_details["boe_id"];
                    $invoice_id =
                        $str_duties_and_additional_details["invoice_id"];
                    $duties_id =
                        $str_duties_and_additional_details["duties_id"];
                    $s_no = $str_duties_and_additional_details["s_no"];
                    $cth = $str_duties_and_additional_details["cth"];
                    $description =
                        $str_duties_and_additional_details["description"];
                    echo $unit_price =
                        $str_duties_and_additional_details["unit_price"];
                    echo $quantity =
                        $str_duties_and_additional_details["quantity"];
                    $uqc = $str_duties_and_additional_details["uqc"];
                    $amount = $str_duties_and_additional_details["amount"];
                    $invsno = $str_duties_and_additional_details["invsno"];
                    $itemsn = $str_duties_and_additional_details["itemsn"];

                    $cth_item_detail =
                        $str_duties_and_additional_details["cth_item_detail"];
                    $ceth = $str_duties_and_additional_details["ceth"];
                    $item_description =
                        $str_duties_and_additional_details["item_description"];
                    $fs = $str_duties_and_additional_details["fs"];
                    $pq = $str_duties_and_additional_details["pq"];
                    $dc = $str_duties_and_additional_details["dc"];
                    $wc = $str_duties_and_additional_details["wc"];
                    $aq = $str_duties_and_additional_details["aq"];
                    $upi = $str_duties_and_additional_details["upi"];
                    $coo = $str_duties_and_additional_details["coo"];
                    $c_qty = $str_duties_and_additional_details["c_qty"];
                    $c_uqc = $str_duties_and_additional_details["c_uqc"];
                    $s_qty = $str_duties_and_additional_details["s_qty"];
                    $s_uqc = $str_duties_and_additional_details["s_uqc"];
                    $sch = $str_duties_and_additional_details["sch"];
                    $stdn_pr = $str_duties_and_additional_details["stdn_pr"];
                    $rsp = $str_duties_and_additional_details["rsp"];
                    $reimp = $str_duties_and_additional_details["reimp"];
                    $prov = $str_duties_and_additional_details["prov"];
                    $end_use = $str_duties_and_additional_details["end_use"];
                    $prodn = $str_duties_and_additional_details["prodn"];
                    $cntrl = $str_duties_and_additional_details["cntrl"];
                    $qualfr = $str_duties_and_additional_details["qualfr"];
                    $contnt = $str_duties_and_additional_details["contnt"];
                    $stmnt = $str_duties_and_additional_details["stmnt"];
                    $sup_docs = $str_duties_and_additional_details["sup_docs"];
                    $assess_value =
                        $str_duties_and_additional_details["assess_value"];
                    $total_duty =
                        $str_duties_and_additional_details["total_duty"];
                    $bcd_notn_no =
                        $str_duties_and_additional_details["bcd_notn_no"];
                    $bcd_notn_sno =
                        $str_duties_and_additional_details["bcd_notn_sno"];
                    $bcd_rate = $str_duties_and_additional_details["bcd_rate"];
                    $bcd_amount =
                        $str_duties_and_additional_details["bcd_amount"];
                    $bcd_duty_fg =
                        $str_duties_and_additional_details["bcd_duty_fg"];
                    $acd_notn_no =
                        $str_duties_and_additional_details["acd_notn_no"];
                    $acd_notn_sno =
                        $str_duties_and_additional_details["acd_notn_sno"];
                    $acd_rate = $str_duties_and_additional_details["acd_rate"];
                    $acd_amount =
                        $str_duties_and_additional_details["acd_amount"];
                    $acd_duty_fg =
                        $str_duties_and_additional_details["acd_duty_fg"];
                    $sws_notn_no =
                        $str_duties_and_additional_details["sws_notn_no"];
                    $sws_notn_sno =
                        $str_duties_and_additional_details["sws_notn_sno"];
                    $sws_rate = $str_duties_and_additional_details["sws_rate"];
                    $sws_amount =
                        $str_duties_and_additional_details["sws_amount"];
                    $sws_duty_fg =
                        $str_duties_and_additional_details["sws_duty_fg"];
                    $sad_notn_no =
                        $str_duties_and_additional_details["sad_notn_no"];
                    $sad_notn_sno =
                        $str_duties_and_additional_details["sad_notn_sno"];
                    $sad_rate = $str_duties_and_additional_details["sad_rate"];
                    $sad_amount =
                        $str_duties_and_additional_details["sad_amount"];
                    $sad_duty_fg =
                        $str_duties_and_additional_details["sad_duty_fg"];
                    $igst_notn_no =
                        $str_duties_and_additional_details["igst_notn_no"];
                    $igst_notn_sno =
                        $str_duties_and_additional_details["igst_notn_sno"];
                    $igst_rate =
                        $str_duties_and_additional_details["igst_rate"];
                    $igst_amount =
                        $str_duties_and_additional_details["igst_amount"];
                    $igst_duty_fg =
                        $str_duties_and_additional_details["igst_duty_fg"];
                    $g_cess_notn_no =
                        $str_duties_and_additional_details["g_cess_notn_no"];
                    $g_cess_notn_sno =
                        $str_duties_and_additional_details["g_cess_notn_sno"];
                    $g_cess_rate =
                        $str_duties_and_additional_details["g_cess_rate"];
                    $g_cess_amount =
                        $str_duties_and_additional_details["g_cess_amount"];
                    $g_cess_duty_fg =
                        $str_duties_and_additional_details["g_cess_duty_fg"];
                    $add_notn_no =
                        $str_duties_and_additional_details["add_notn_no"];
                    $add_notn_sno =
                        $str_duties_and_additional_details["add_notn_sno"];
                    $add_rate = $str_duties_and_additional_details["add_rate"];
                    $add_amount =
                        $str_duties_and_additional_details["add_amount"];
                    $add_duty_fg =
                        $str_duties_and_additional_details["add_duty_fg"];
                    $cvd_notn_no =
                        $str_duties_and_additional_details["cvd_notn_no"];
                    $cvd_notn_sno =
                        $str_duties_and_additional_details["cvd_notn_sno"];
                    $cvd_rate = $str_duties_and_additional_details["cvd_rate"];
                    $cvd_amount =
                        $str_duties_and_additional_details["cvd_amount"];
                    $cvd_duty_fg =
                        $str_duties_and_additional_details["cvd_duty_fg"];
                    $sg_notn_no =
                        $str_duties_and_additional_details["sg_notn_no"];
                    $sg_notn_sno =
                        $str_duties_and_additional_details["sg_notn_sno"];
                    $sg_rate = $str_duties_and_additional_details["sg_rate"];
                    $sg_amount =
                        $str_duties_and_additional_details["sg_amount"];
                    $sg_duty_fg =
                        $str_duties_and_additional_details["sg_duty_fg"];
                    $t_value_notn_no =
                        $str_duties_and_additional_details["t_value_notn_no"];
                    $t_value_notn_sno =
                        $str_duties_and_additional_details["t_value_notn_sno"];
                    $t_value_rate =
                        $str_duties_and_additional_details["t_value_rate"];
                    $t_value_amount =
                        $str_duties_and_additional_details["t_value_amount"];
                    $t_value_duty_fg =
                        $str_duties_and_additional_details["t_value_duty_fg"];
                    $sp_excd_notn_no =
                        $str_duties_and_additional_details["sp_excd_notn_no"];
                    $sp_excd_notn_sno =
                        $str_duties_and_additional_details["sp_excd_notn_sno"];
                    $sp_excd_rate =
                        $str_duties_and_additional_details["sp_excd_rate"];
                    $sp_excd_amount =
                        $str_duties_and_additional_details["sp_excd_amount"];
                    $sp_excd_duty_fg =
                        $str_duties_and_additional_details["sp_excd_duty_fg"];
                    $chcess_notn_no =
                        $str_duties_and_additional_details["chcess_notn_no"];
                    $chcess_notn_sno =
                        $str_duties_and_additional_details["chcess_notn_sno"];
                    $chcess_rate =
                        $str_duties_and_additional_details["chcess_rate"];
                    $chcess_amount =
                        $str_duties_and_additional_details["chcess_amount"];
                    $chcess_duty_fg =
                        $str_duties_and_additional_details["chcess_duty_fg"];
                    $tta_notn_no =
                        $str_duties_and_additional_details["tta_notn_no"];
                    $tta_notn_sno =
                        $str_duties_and_additional_details["tta_notn_sno"];
                    $tta_rate = $str_duties_and_additional_details["tta_rate"];
                    $tta_amount =
                        $str_duties_and_additional_details["tta_amount"];
                    $tta_duty_fg =
                        $str_duties_and_additional_details["tta_duty_fg"];
                    $cess_notn_no =
                        $str_duties_and_additional_details["cess_notn_no"];
                    $cess_notn_sno =
                        $str_duties_and_additional_details["cess_notn_sno"];
                    $cess_rate =
                        $str_duties_and_additional_details["cess_rate"];
                    $cess_amount =
                        $str_duties_and_additional_details["cess_amount"];
                    $cess_duty_fg =
                        $str_duties_and_additional_details["cess_duty_fg"];
                    $caidc_cvd_edc_notn_no =
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_notn_no"
                        ];
                    $caidc_cvd_edc_notn_sno =
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_notn_sno"
                        ];
                    $caidc_cvd_edc_rate =
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_rate"
                        ];
                    $caidc_cvd_edc_amount =
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_amount"
                        ];
                    $caidc_cvd_edc_duty_fg =
                        $str_duties_and_additional_details[
                            "caidc_cvd_edc_duty_fg"
                        ];
                    $eaidc_cvd_hec_notn_no =
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_notn_no"
                        ];
                    $eaidc_cvd_hec_notn_sno =
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_notn_sno"
                        ];
                    $eaidc_cvd_hec_rate =
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_rate"
                        ];
                    $eaidc_cvd_hec_amount =
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_amount"
                        ];
                    $eaidc_cvd_hec_duty_fg =
                        $str_duties_and_additional_details[
                            "eaidc_cvd_hec_duty_fg"
                        ];
                    $cus_edc_notn_no =
                        $str_duties_and_additional_details["cus_edc_notn_no"];
                    $cus_edc_notn_sno =
                        $str_duties_and_additional_details["cus_edc_notn_sno"];
                    $cus_edc_rate =
                        $str_duties_and_additional_details["cus_edc_rate"];
                    $cus_edc_amount =
                        $str_duties_and_additional_details["cus_edc_amount"];
                    $cus_edc_duty_fg =
                        $str_duties_and_additional_details["cus_edc_duty_fg"];
                    $cus_hec_notn_no =
                        $str_duties_and_additional_details["cus_hec_notn_no"];
                    $cus_hec_notn_sno =
                        $str_duties_and_additional_details["cus_hec_notn_sno"];
                    $cus_hec_rate =
                        $str_duties_and_additional_details["cus_hec_rate"];
                    $cus_hec_amount =
                        $str_duties_and_additional_details["cus_hec_amount"];
                    $cus_hec_duty_fg =
                        $str_duties_and_additional_details["cus_hec_duty_fg"];
                    $ncd_notn_no =
                        $str_duties_and_additional_details["ncd_notn_no"];
                    $ncd_notn_sno =
                        $str_duties_and_additional_details["ncd_notn_sno"];
                    $ncd_rate = $str_duties_and_additional_details["ncd_rate"];
                    $ncd_amount =
                        $str_duties_and_additional_details["ncd_amount"];
                    $ncd_duty_fg =
                        $str_duties_and_additional_details["ncd_duty_fg"];
                    $aggr_notn_no =
                        $str_duties_and_additional_details["aggr_notn_no"];
                    $aggr_notn_sno =
                        $str_duties_and_additional_details["aggr_notn_sno"];
                    $aggr_rate =
                        $str_duties_and_additional_details["aggr_rate"];
                    $aggr_amount =
                        $str_duties_and_additional_details["aggr_amount"];
                    $aggr_duty_fg =
                        $str_duties_and_additional_details["aggr_duty_fg"];
                    $invsno_add_details =
                        $str_duties_and_additional_details[
                            "invsno_add_details"
                        ];
                    $itmsno_add_details =
                        $str_duties_and_additional_details[
                            "itmsno_add_details"
                        ];
                    $refno = $str_duties_and_additional_details["refno"];
                    $refdt = $str_duties_and_additional_details["refdt"];
                    $prtcd_svb_d =
                        $str_duties_and_additional_details["prtcd_svb_d"];
                    $lab = $str_duties_and_additional_details["lab"];
                    $pf = $str_duties_and_additional_details["pf"];
                    $load_date =
                        $str_duties_and_additional_details["load_date"];
                    $pf_ = $str_duties_and_additional_details["pf_"];
                    $beno = $str_duties_and_additional_details["beno"];
                    $bedate = $str_duties_and_additional_details["bedate"];
                    $prtcd = $str_duties_and_additional_details["prtcd"];
                    $unitprice =
                        $str_duties_and_additional_details["unitprice"];
                    $currency_code =
                        $str_duties_and_additional_details["currency_code"];
                    $frt = $str_duties_and_additional_details["frt"];
                    $ins = $str_duties_and_additional_details["ins"];
                    $duty = $str_duties_and_additional_details["duty"];
                    $sb_no = $str_duties_and_additional_details["sb_no"];
                    $sb_dt = $str_duties_and_additional_details["sb_dt"];
                    $portcd = $str_duties_and_additional_details["portcd"];
                    $sinv = $str_duties_and_additional_details["sinv"];
                    $sitemn = $str_duties_and_additional_details["sitemn"];
                    $type = $str_duties_and_additional_details["type"];
                    $manufact_cd =
                        $str_duties_and_additional_details["manufact_cd"];
                    $source_cy =
                        $str_duties_and_additional_details["source_cy"];
                    $trans_cy = $str_duties_and_additional_details["trans_cy"];
                    $address = $str_duties_and_additional_details["address"];
                    $accessory_item_details =
                        $str_duties_and_additional_details[
                            "accessory_item_details"
                        ];
                    $notno = $str_duties_and_additional_details["notno"];
                    $slno = $str_duties_and_additional_details["slno"];
                    $created_at =
                        $str_duties_and_additional_details["created_at"];
                }*/

                echo $sql_insert_duties_and_additional_details =
                    "INSERT INTO `duties_and_additional_details` (boe_id, invoice_id, duties_id, s_no, cth, description, unit_price, quantity, uqc, amount, invsno, itemsn, cth_item_detail, ceth, item_description, fs, pq, dc, wc, aq, upi, coo, c_qty, c_uqc, s_qty, s_uqc, sch, stdn_pr, rsp, reimp, prov, end_use, prodn, cntrl, qualfr, contnt, stmnt, sup_docs, assess_value, total_duty, bcd_notn_no, bcd_notn_sno, bcd_rate, bcd_amount, bcd_duty_fg, acd_notn_no, acd_notn_sno, acd_rate, acd_amount, acd_duty_fg, sws_notn_no, sws_notn_sno, sws_rate, sws_amount, sws_duty_fg, sad_notn_no, sad_notn_sno, sad_rate, sad_amount, sad_duty_fg, igst_notn_no, igst_notn_sno, igst_rate, igst_amount, igst_duty_fg, g_cess_notn_no, g_cess_notn_sno, g_cess_rate, g_cess_amount, g_cess_duty_fg, add_notn_no, add_notn_sno, add_rate, add_amount, add_duty_fg, cvd_notn_no, cvd_notn_sno, cvd_rate, cvd_amount, cvd_duty_fg, sg_notn_no, sg_notn_sno, sg_rate, sg_amount, sg_duty_fg, t_value_notn_no, t_value_notn_sno, t_value_rate, t_value_amount, t_value_duty_fg, sp_excd_notn_no, sp_excd_notn_sno, sp_excd_rate, sp_excd_amount, sp_excd_duty_fg, chcess_notn_no, chcess_notn_sno, chcess_rate, chcess_amount, chcess_duty_fg, tta_notn_no, tta_notn_sno, tta_rate, tta_amount, tta_duty_fg, cess_notn_no, cess_notn_sno, cess_rate, cess_amount, cess_duty_fg, caidc_cvd_edc_notn_no, caidc_cvd_edc_notn_sno, caidc_cvd_edc_rate, caidc_cvd_edc_amount, caidc_cvd_edc_duty_fg, eaidc_cvd_hec_notn_no, eaidc_cvd_hec_notn_sno, eaidc_cvd_hec_rate, eaidc_cvd_hec_amount, eaidc_cvd_hec_duty_fg, cus_edc_notn_no, cus_edc_notn_sno, cus_edc_rate, cus_edc_amount, cus_edc_duty_fg, cus_hec_notn_no, cus_hec_notn_sno, cus_hec_rate, cus_hec_amount, cus_hec_duty_fg, ncd_notn_no, ncd_notn_sno, ncd_rate, ncd_amount, ncd_duty_fg, aggr_notn_no, aggr_notn_sno, aggr_rate, aggr_amount, aggr_duty_fg, invsno_add_details, itmsno_add_details, refno, refdt, prtcd_svb_d, lab, pf, load_date, pf_, beno, bedate, prtcd, unitprice, currency_code, frt, ins, duty, sb_no, sb_dt, portcd, sinv, sitemn, type, manufact_cd, source_cy, trans_cy, address, accessory_item_details, notno, slno, created_at) 
        VALUES('" .
                    $boe_id .
                    "','" .
                    $invoice_id .
                    "','" .
                    $duties_id .
                    "','" .
                    $s_no .
                    "','" .
                    $cth .
                    "','" .
                    $description .
                    "','" .
                    $unit_price .
                    "','" .
                    $quantity .
                    "','" .
                    $uqc .
                    "','" .
                    $amount .
                    "','" .
                    $invsno .
                    "','" .
                    $itemsn .
                    "','" .
                    $cth_item_detail .
                    "','" .
                    $ceth .
                    "','" .
                    $item_description .
                    "','" .
                    $fs .
                    "','" .
                    $pq .
                    "','" .
                    $dc .
                    "','" .
                    $wc .
                    "','" .
                    $aq .
                    "','" .
                    $upi .
                    "','" .
                    $coo .
                    "','" .
                    $c_qty .
                    "','" .
                    $c_uqc .
                    "','" .
                    $s_qty .
                    "','" .
                    $s_uqc .
                    "','" .
                    $sch .
                    "','" .
                    $stdn_pr .
                    "','" .
                    $rsp .
                    "','" .
                    $reimp .
                    "','" .
                    $prov .
                    "','" .
                    $end_use .
                    "','" .
                    $prodn .
                    "','" .
                    $cntrl .
                    "','" .
                    $qualfr .
                    "','" .
                    $contnt .
                    "','" .
                    $stmnt .
                    "','" .
                    $sup_docs .
                    "','" .
                    $assess_value .
                    "','" .
                    $total_duty .
                    "','" .
                    $bcd_notn_no .
                    "','" .
                    $bcd_notn_sno .
                    "','" .
                    $bcd_rate .
                    "','" .
                    $bcd_amount .
                    "','" .
                    $bcd_duty_fg .
                    "','" .
                    $acd_notn_no .
                    "','" .
                    $acd_notn_sno .
                    "','" .
                    $acd_rate .
                    "','" .
                    $acd_amount .
                    "','" .
                    $acd_duty_fg .
                    "','" .
                    $sws_notn_no .
                    "','" .
                    $sws_notn_sno .
                    "','" .
                    $sws_rate .
                    "','" .
                    $sws_amount .
                    "','" .
                    $sws_duty_fg .
                    "','" .
                    $sad_notn_no .
                    "','" .
                    $sad_notn_sno .
                    "','" .
                    $sad_rate .
                    "','" .
                    $sad_amount .
                    "','" .
                    $sad_duty_fg .
                    "','" .
                    $igst_notn_no .
                    "','" .
                    $igst_notn_sno .
                    "','" .
                    $igst_rate .
                    "','" .
                    $igst_amount .
                    "','" .
                    $igst_duty_fg .
                    "','" .
                    $g_cess_notn_no .
                    "','" .
                    $g_cess_notn_sno .
                    "','" .
                    $g_cess_rate .
                    "','" .
                    $g_cess_amount .
                    "','" .
                    $g_cess_duty_fg .
                    "','" .
                    $add_notn_no .
                    "','" .
                    $add_notn_sno .
                    "','" .
                    $add_rate .
                    "','" .
                    $add_amount .
                    "','" .
                    $add_duty_fg .
                    "','" .
                    $cvd_notn_no .
                    "','" .
                    $cvd_notn_sno .
                    "','" .
                    $cvd_rate .
                    "','" .
                    $cvd_amount .
                    "','" .
                    $cvd_duty_fg .
                    "','" .
                    $sg_notn_no .
                    "','" .
                    $sg_notn_sno .
                    "','" .
                    $sg_rate .
                    "','" .
                    $sg_amount .
                    "','" .
                    $sg_duty_fg .
                    "','" .
                    $t_value_notn_no .
                    "','" .
                    $t_value_notn_sno .
                    "','" .
                    $t_value_rate .
                    "','" .
                    $t_value_amount .
                    "','" .
                    $t_value_duty_fg .
                    "','" .
                    $sp_excd_notn_no .
                    "','" .
                    $sp_excd_notn_sno .
                    "','" .
                    $sp_excd_rate .
                    "','" .
                    $sp_excd_amount .
                    "','" .
                    $sp_excd_duty_fg .
                    "','" .
                    $chcess_notn_no .
                    "','" .
                    $chcess_notn_sno .
                    "','" .
                    $chcess_rate .
                    "','" .
                    $chcess_amount .
                    "','" .
                    $chcess_duty_fg .
                    "','" .
                    $tta_notn_no .
                    "','" .
                    $tta_notn_sno .
                    "','" .
                    $tta_rate .
                    "','" .
                    $tta_amount .
                    "','" .
                    $tta_duty_fg .
                    "','" .
                    $cess_notn_no .
                    "','" .
                    $cess_notn_sno .
                    "','" .
                    $cess_rate .
                    "','" .
                    $cess_amount .
                    "','" .
                    $cess_duty_fg .
                    "','" .
                    $caidc_cvd_edc_notn_no .
                    "','" .
                    $caidc_cvd_edc_notn_sno .
                    "','" .
                    $caidc_cvd_edc_rate .
                    "','" .
                    $caidc_cvd_edc_amount .
                    "','" .
                    $caidc_cvd_edc_duty_fg .
                    "','" .
                    $eaidc_cvd_hec_notn_no .
                    "','" .
                    $eaidc_cvd_hec_notn_sno .
                    "','" .
                    $eaidc_cvd_hec_rate .
                    "','" .
                    $eaidc_cvd_hec_amount .
                    "','" .
                    $eaidc_cvd_hec_duty_fg .
                    "','" .
                    $cus_edc_notn_no .
                    "','" .
                    $cus_edc_notn_sno .
                    "','" .
                    $cus_edc_rate .
                    "','" .
                    $cus_edc_amount .
                    "','" .
                    $cus_edc_duty_fg .
                    "','" .
                    $cus_hec_notn_no .
                    "','" .
                    $cus_hec_notn_sno .
                    "','" .
                    $cus_hec_rate .
                    "','" .
                    $cus_hec_amount .
                    "','" .
                    $cus_hec_duty_fg .
                    "','" .
                    $ncd_notn_no .
                    "','" .
                    $ncd_notn_sno .
                    "','" .
                    $ncd_rate .
                    "','" .
                    $ncd_amount .
                    "','" .
                    $ncd_duty_fg .
                    "','" .
                    $aggr_notn_no .
                    "','" .
                    $aggr_notn_sno .
                    "','" .
                    $aggr_rate .
                    "','" .
                    $aggr_amount .
                    "','" .
                    $aggr_duty_fg .
                    "','" .
                    $invsno_add_details .
                    "','" .
                    $itmsno_add_details .
                    "','" .
                    $refno .
                    "','" .
                    $refdt .
                    "','" .
                    $prtcd_svb_d .
                    "','" .
                    $lab .
                    "','" .
                    $pf .
                    "','" .
                    $load_date .
                    "','" .
                    $pf_ .
                    "','" .
                    $beno .
                    "','" .
                    $bedate .
                    "','" .
                    $prtcd .
                    "','" .
                    $unitprice .
                    "','" .
                    $currency_code .
                    "','" .
                    $frt .
                    "','" .
                    $ins .
                    "','" .
                    $duty .
                    "','" .
                    $sb_no .
                    "','" .
                    $sb_dt .
                    "','" .
                    $portcd .
                    "','" .
                    $sinv .
                    "','" .
                    $sitemn .
                    "','" .
                    $type .
                    "','" .
                    $manufact_cd .
                    "','" .
                    $source_cy .
                    "','" .
                    $trans_cy .
                    "','" .
                    $address .
                    "','" .
                    $accessory_item_details .
                    "','" .
                    $notno .
                    "','" .
                    $slno .
                    "','" .
                    $created_at .
                    "')";

                $copy_insert_duties_and_additional_details = $db1_duties_and_additional_details->query(
                    $sql_insert_duties_and_additional_details
                );
            }
        }
        /******************************************************************Start ship_bill_summary***************************************************************************************/
    }
public function bill_of_entry_summary_try(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/

        //$bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary WHERE Where created_at >= current_date::timestamp and
     // created_at < current_date::timestamp + interval '1 day'";
    //echo $bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary ";
 echo $bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary";
        $statement1_bm1 = $this->db->query($bm1);
        $iecwise_data1_bm1 = [];
        $result1_bm1 = $statement1_bm1->result_array();
   print_r($result1_bm1);
 // Set the batch size
        $batchSize = 9000;

        // Loop through the records in batches of 9000
        for ($offset = 0;$offset < $result1_bm1[0]["count"];$offset += $batchSize) {
         $query1 = "SELECT * FROM bill_of_entry_summary";
        //$query1 = "SELECT * FROM bill_of_entry_summary ";
        $statement1 = $this->db->query($query1);
        $iecwise_data1 = [];
        $result1 = $statement1->result_array();
        
       // print_r($result1);exit;
        foreach ($result1 as $str1) {
           // $iec1 = '0388004673';
            $iec1 =$str1["iec_no"];
            $sql = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec1'";
            $iecwise1 = $this->db->query($sql);
            $iecwise_data1 = $iecwise1->result_array();
            $db1 = $this->database_connection($iecwise_data1[0]["lucrative_users_id"]);
//print_r($str1);
                 $iecwise_data1[0]["lucrative_users_id"];
  $sql_beo = "SELECT * FROM bill_of_entry_summary";
            $iecwise1_beo = $db1->query($sql_beo);
            //$iecwise_data1_beo = $iecwise1_beo->result_array();
            
            $iecwise_data1_beo = array();

while ($row = $iecwise1_beo->fetch_assoc()) {
    $iecwise_data1_beo[] = $row;
}
       // print_r(count($iecwise_data1_beo));  // exit; 
            
            $port = $str1["port"];
          //  if (@get_magic_quotes_gpc()) {
                 $boe_id = addslashes($str1["boe_id"]);
                $boe_file_status_id = addslashes($str1["boe_file_status_id"]);
                $invoice_title = addslashes($str1["invoice_title"]);
                $port = addslashes($str1["port"]);
                $port_code = addslashes($str1["port_code"]);
                $be_no = addslashes($str1["be_no"]);
                $be_date = addslashes($str1["be_date"]);
                $be_type = addslashes($str1["be_type"]);
                $iec_br = addslashes($str1["iec_br"]);
                $iec_no = addslashes($str1["iec_no"]);
                $br = addslashes($str1["br"]);
                $gstin_type = addslashes($str1["gstin_type"]);
                $cb_code = addslashes($str1["cb_code"]);
                $nos = addslashes($str1["nos"]);
                $pkg = addslashes($str1["pkg"]);
                $item = addslashes($str1["item"]);
                $g_wt_kgs = addslashes($str1["g_wt_kgs"]);
                $cont = addslashes($str1["cont"]);
                $be_status = addslashes($str1["be_status"]);
                $mode = addslashes($str1["mode"]);
                $def_be = addslashes($str1["def_be"]);
                $kacha = addslashes($str1["kacha"]);
                $sec_48 = addslashes($str1["sec_48"]);
                $reimp = addslashes($str1["reimp"]);
                $adv_be = addslashes($str1["adv_be"]);
                $assess = addslashes($str1["assess"]);
                $exam = addslashes($str1["exam"]);
                $hss = addslashes($str1["hss"]);
                $first_check = addslashes($str1["first_check"]);
                $prov_final = addslashes($str1["prov_final"]);
                $country_of_origin= addslashes($str1["country_of_origin"]);
                $importer_name_and_address= addslashes($str1["importer_name_and_address"]);
                $country_of_consignment = addslashes($str1["country_of_consignment"]);
                $port_of_loading = addslashes($str1["port_of_loading"]);
                $port_of_shipment = addslashes($str1["port_of_shipment"]);
                $ad_code = addslashes($str1["ad_code"]);
                $cb_name = addslashes($str1["cb_name"]);
                $aeo = addslashes($str1["aeo"]);
                $ucr = addslashes($str1["ucr"]);
                $bcd = addslashes($str1["bcd"]);
                $acd = addslashes($str1["acd"]);
                $sws = addslashes($str1["sws"]);
                $nccd = addslashes($str1["nccd"]);
                $add = addslashes($str1["add"]);
                $cvd = addslashes($str1["cvd"]);
                $igst = addslashes($str1["igst"]);
                $g_cess = addslashes($str1["g_cess"]);
                $sg = addslashes($str1["sg"]);
                $saed = addslashes($str1["saed"]);
                $gsia = addslashes($str1["gsia"]);
                $tta = addslashes($str1["tta"]);
                $health = addslashes($str1["health"]);
                $total_duty = addslashes($str1["total_duty"]);
                $int = addslashes($str1["int"]);
                $pnlty = addslashes($str1["pnlty"]);
                $fine = addslashes($str1["fine"]);
                $tot_ass_val = addslashes($str1["tot_ass_val"]);
                $tot_amount = addslashes($str1["tot_amount"]);
                $wbe_no = addslashes($str1["wbe_no"]);
                $wbe_date = addslashes($str1["wbe_date"]);
                $wh_code = addslashes($str1["wh_code"]);
                $wbe_site= addslashes($str1["wbe_site"]);
                $submission_date = addslashes($str1["submission_date"]);
                $assessment_date = addslashes($str1["assessment_date"]);
                $examination_date = addslashes($str1["examination_date"]);
                $ooc_date = addslashes($str1["ooc_date"]);
                $submission_time = addslashes($str1["submission_time"]);
                $assessment_time = addslashes($str1["assessment_time"]);
                $examination_time = addslashes($str1["examination_time"]);
                $ooc_time = addslashes($str1["ooc_time"]);
                $submission_exchange_rate = addslashes($str1["submission_exchange_rate"]);
                $assessment_exchange_rate = addslashes($str1["assessment_exchange_rate"]);
                $ooc_no = addslashes($str1["ooc_no"]);
                $ooc_date_ = addslashes($str1["ooc_date_"]);
                $created_at = addslashes($str1["created_at"]);
                $examination_exchange_rate = addslashes($str1["examination_exchange_rate"]);
                $ooc_exchange_rate = addslashes($str1["ooc_exchange_rate"]);
     //skip dupliacte entry     
     $a= $this->inArray($iecwise_data1_beo,$be_no); // Output - value exists
     $b= $this->inArray_be_date($iecwise_data1_beo,$be_date);
     if ($a==1 && $b==1) {
          echo "Duplicate";"============";continue;
     }
     else{
 
   
                    if($int ==''){$int=0;}
                    if($bcd ==''){$bcd=0;}
                    if($acd ==''){$acd=0;}    
                    if($sws ==''){$sws=0;}
                    if($nccd ==''){$nccd=0;}
                    if($add ==''){$add=0;}
                    if($cvd ==''){$cvd=0;}
                    if($igst ==''){$igst=0;}
                    if($g_cess ==''){$g_cess=0;}
                    if($sg ==''){$sg=0;}
                    if($saed ==''){$saed=0;}
                    if($gsia ==''){$gsia=0;}
                    if($tta ==''){$tta=0;}
                    if($health ==''){$health=0;}
                    if($total_duty ==''){$total_duty=0;}
                    if($pnlty ==''){$pnlty=0;}
                    if($fine ==''){$fine=0;}
                    if($tot_ass_val ==''){$tot_ass_val=0;}
                    if($tot_amount ==''){$tot_amount=0;}

          //echo $submission_date;
            $wbe_date = date("Y-m-d",strtotime($wbe_date));
              $submission_date = date("Y-m-d",strtotime($submission_date));
               $assessment_date = date("Y-m-d",strtotime($assessment_date));
               $examination_date = date("Y-m-d",strtotime($examination_date));
            $ooc_date = date("Y-m-d",strtotime($ooc_date));
            
           echo $sql_insert1 =
                "INSERT INTO `bill_of_entry_summary`( `boe_id`,`boe_file_status_id`, `invoice_title`, `port`, `port_code`, `be_no`, `be_date`, `be_type`, `iec_br`, `iec_no`, `br`, `gstin_type`, `cb_code`, `nos`, `pkg`, `item`, `g_wt_kgs`, `cont`, `be_status`, `mode`, `def_be`, `kacha`, `sec_48`, `reimp`, `adv_be`, `assess`, `exam`, `hss`, `first_check`, `prov_final`, `country_of_origin`, `country_of_consignment`, `port_of_loading`, `port_of_shipment`, `importer_name_and_address`, `ad_code`, `cb_name`, `aeo`, `ucr`, `bcd`, `acd`, `sws`, `nccd`, `add`, `cvd`, `igst`, `g_cess`, `sg`, `saed`, `gsia`, `tta`, `health`, `total_duty`, `int`, `pnlty`, `fine`, `tot_ass_val`, `tot_amount`, `wbe_no`, `wbe_date`, `wbe_site`, `wh_code`, `submission_date`, `assessment_date`, `examination_date`, `ooc_date`, `submission_time`, `assessment_time`, `examination_time`, `ooc_time`, `submission_exchange_rate`, `assessment_exchange_rate`, `ooc_no`, `ooc_date_`, `created_at`, `examination_exchange_rate`, `ooc_exchange_rate`) 
                    VALUES ('".$boe_id .
                "','" .
                $boe_file_status_id .
                "','" .
                $invoice_title .
                "','" .
                $port .
                "','" .
                $port_code .
                "','" .
                $be_no .
                "','" .
                $be_date .
                "','" .
                $be_type .
                "','" .
                $iec_br .
                "', '" .
                $iec_no .
                "', '" .
                $br .
                "', '" .
                $gstin_type .
                "','" .
                $cb_code .
                "', '" .
                $nos .
                "','" .
                $pkg .
                "','" .
                $item .
                "','" .
                $g_wt_kgs .
                "','" .
                $cont .
                "','" .
                $be_status .
                "','" .
                $mode .
                "','" .
                $def_be .
                "','" .
                $kacha .
                "','" .
                $sec_48 .
                "','" .
                $reimp .
                "','" .
                $adv_be .
                "','" .
                $assess .
                "','" .
                $exam .
                "', '" .
                $hss .
                "','" .
                $first_check .
                "', '" .
                $prov_final .
                "', '" .
                $country_of_origin .
                "','" .
                $country_of_consignment .
                "','" .
                $port_of_loading .
                "','" .
                $port_of_shipment .
                "','" .
                $importer_name_and_address .
                "','" .
                $ad_code .
                "','" .
                $cb_name .
                "','" .
                $aeo .
                "','" .
                $ucr .
                "','" .
                $bcd .
                "','" .
                $acd .
                "','" .
                $sws .
                "','" .
                $nccd .
                "','" .
                $add .
                "','" .
                $cvd .
                "', '" .
                $igst .
                "','" .
                $g_cess .
                "','" .
                $sg .
                "', '" .
                $saed .
                "','" .
                $gsia .
                "', '" .
                $tta .
                "','" .
                $health .
                "','" .
                $total_duty .
                "','" .
                $int .
                "','" .
                $pnlty .
                "','" .
                $fine .
                "', '" .
                $tot_ass_val .
                "', '" .
                $tot_amount .
                "','" .
                $wbe_no .
                "', '" .
                $wbe_date .
                "', '" .
                $wbe_site .
                "','" .
                $wh_code .
                "', '" .
                $submission_date .
                "', '" .
                $assessment_date .
                "', '" .
                $examination_date .
                "', '" .
                $ooc_date .
                "', '" .
                $submission_time .
                "','" .
                $assessment_time .
                "','" .
                $examination_time .
                "','" .
                $ooc_time .
                "','" .
                $submission_exchange_rate .
                "', '" .
                $assessment_exchange_rate .
                "','" .
                $ooc_no .
                "', '" .
                $ooc_date_ .
                "','" .
                $created_at .
                "','" .
                $examination_exchange_rate .
                "', '" .
                $ooc_exchange_rate .
                "')";

            $copy_bill_of_entry_summary = $db1->query($sql_insert1);
           // print_r($db1->last_query());
        }
        }
        }
    
        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }
    
public function lucrative_users(){
        /******************************************************************Start lucrative_users***************************************************************************************/

        $query_boe_delete_logs = "SELECT  * FROM lucrative_users ";
         //$query_boe_delete_logs = "SELECT  * FROM lucrative_users";
        $statement_boe_delete_logs = $this->db->query($query_boe_delete_logs);
        $iecwise_data_boe_delete_logs = [];
        $result_boe_delete_logs = $statement_boe_delete_logs->result_array();
     // print_r($result_boe_delete_logs);exit;

        foreach ($result_boe_delete_logs as $str_boe_delete_logs) { //print_r($str_boe_delete_logs);
          
        
                $lucrative_users_id = $str_boe_delete_logs["lucrative_users_id"];
                $email = $str_boe_delete_logs["email"];
                $fullname = $str_boe_delete_logs["fullname"];
                $password = $str_boe_delete_logs["password"];
                $mobile = $str_boe_delete_logs["mobile"];
                $iec_no = $str_boe_delete_logs["iec_no"];
                $br = $str_boe_delete_logs["br"];
                $role = $str_boe_delete_logs["role"];
                $created_at = $str_boe_delete_logs["created_at"];
                $deleted_at = $str_boe_delete_logs["is_deleted"];
                $db1 = $this->database_connection('master');
                
               // $iecwise_data1[0]["lucrative_users_id"];
               
                /********************checking dupliacte entries d2d***********************/
                         $sql_users = "SELECT * FROM lucrative_users";
                                 $iecwise1_users = $db1->query($sql_users);
                        $iecwise_data1_users = array();
                        
                        while ($rowusers = $iecwise1_users->fetch_assoc()) {
                        $iecwise_data1_users[] = $rowusers;
                        }
                /**************************************************/

                 //skip dupliacte entry     
                 $a= $this->inArray_lucrative_users($iecwise_data1_users,$iec_no); // Output - value exists
                 if ($a==1) {
                      echo "Duplicate";"============";continue;
                 }
                 
                 
                 
            echo $sql_insert_boe_delete_logs =
                "INSERT INTO  `lucrative_users`(`lucrative_users_id`, `email`, `fullname`, `password`, `mobile`, `iec_no`, `br`, `role`, `created_at`,`is_deleted`)  VALUES('" .
                $lucrative_users_id .
                "','" .
                $email .
                "','" .
                $fullname .
                "','" .
                $password .
                "','" .
                $mobile .
                "','" .
                $iec_no .
                "','" .
                $br .
                "','" .
                $role .
                "','" .
                $created_at .
                "','" .
                $deleted_at .
                "')";
 $copy_bill_of_entry_summary_boe_delete_logs = $db1->query($sql_insert_boe_delete_logs);

       $user_id = $db1->insert_id;
              $user=$this->register_iec_user($user_id);
        }//
        /******************************************************************End lucrative_users***************************************************************************************/
    }
public function inArray_lucrative_users($array, $value){
 
   /* Initialize index -1 initially. */
 
    $index = -1;
 
    foreach($array as $val){
// print_r($val);
         /* If value is found, set index to 1. */
 //echo "=>".$val['be_no']. "==". $value;
         if($val['iec_no'] == $value){
 
                $index = 1;
 
           } 
    }
 
    if($index == -1){
 
         echo "value does not exists";
 
     } else {
 
        echo "value exists";
 
     }
     return $index;
}

public function inArray_aa_dfia_licence_details($array, $value){
 
   /* Initialize index -1 initially. */
 
    $index = -1;
 
    foreach($array as $val){
// print_r($val);
         /* If value is found, set index to 1. */
$x= $val['sb_no']."-".$val['inv_s_no']."-".$val['item_s_no_'];
         if($x == $value){
 
                $index = 1;
 
           } 
    }
 
    if($index == -1){
 
         echo "value does not exists";
 
     } else {
 
        echo "value exists";
 
     }
     return $index;
}
public function index(){
        /******************************************************************Start aa_dfia_licence_details***************************************************************************************/
 //$query ="SELECT aa_dfia_licence_details.*, ship_bill_summary.sbs_id, ship_bill_summary.iec,item_details.invoice_id,item_details.item_id FROM aa_dfia_licence_details LEFT JOIN item_details ON aa_dfia_licence_details.item_id = item_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id LEFT JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id  WHERE aa_dfia_licence_details.created_at >= NOW() - INTERVAL '24 HOURS'";
        
        $query ="SELECT aa_dfia_licence_details.*, ship_bill_summary.sbs_id,ship_bill_summary.sb_no, ship_bill_summary.iec,item_details.invoice_id,item_details.item_id FROM aa_dfia_licence_details LEFT JOIN item_details ON aa_dfia_licence_details.item_id = item_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id LEFT JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $result = $statement->result_array();
       // print_r($result);exit;

        foreach ($result as $str) {
            $iec = $str["iec"];
            $sql = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec'";
            $iecwise = $this->db->query($sql);
            $iecwise_data = $iecwise->result_array();
            $db1 = $this->database_connection(
                $iecwise_data[0]["lucrative_users_id"]
            );
           $sb_no= addslashes($str["sb_no"]);
                $item_id = addslashes($str["item_id"]);
                $inv_s_no = addslashes($str["inv_s_no"]);
                $item_s_no_ = addslashes($str["item_s_no_"]);
                $licence_no = addslashes($str["licence_no"]);
                $descn_of_export_item = addslashes(
                    $str["descn_of_export_item"]
                );
                $exp_s_no = addslashes($str["exp_s_no"]);
                $expqty = addslashes($str["expqty"]);
                $uqc_aa = addslashes($str["uqc_aa"]);
                $fob_value = addslashes($str["fob_value"]);
                $sion = addslashes($str["sion"]);
                $descn_of_import_item = addslashes(
                    $str["descn_of_import_item"]
                );
                $imp_s_no = addslashes($str["imp_s_no"]);
                $impqt = addslashes($str["impqt"]);
                $uqc_ = addslashes($str["uqc_"]);
                $indig_imp = addslashes($str["indig_imp"]);
                $created_at = addslashes($str["created_at"]);
                
        /********************checking dupliacte entries d2d***********************/
        $sql_users = "SELECT aa_dfia_licence_details.*, ship_bill_summary.sbs_id,ship_bill_summary.sb_no, ship_bill_summary.iec,item_details.invoice_id,item_details.item_id FROM aa_dfia_licence_details LEFT JOIN item_details ON aa_dfia_licence_details.item_id = item_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id LEFT JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id";
             $iecwise1_users = $db1->query($sql_users);
        $iecwise_data1_users = array();
        
        while ($rowusers = $iecwise1_users->fetch_assoc()) {
            $iecwise_data1_users[] = $rowusers;
        }
        
        $c= $sb_no."-".$inv_s_no."-".$item_s_no_;
        //skip dupliacte entry     
        $a= $this->inArray_aa_dfia_licence_details($iecwise_data1_users,$c); // Output - value exists
        if ($a==1) {
         echo "Duplicate";"============";continue;
        }
        
        /**************************************************/
            echo $sql_insert =
                "INSERT INTO `aa_dfia_licence_details`( `item_id`, `inv_s_no`, `item_s_no_`, `licence_no`, `descn_of_export_item`, `exp_s_no`, `expqty`, `uqc_aa`, `fob_value`, `sion`, `descn_of_import_item`, `imp_s_no`, `impqt`, `uqc_`, `indig_imp`, `created_at`) VALUES ('" .
                $item_id .
                "','" .
                $inv_s_no .
                "','" .
                $item_s_no_ .
                "','" .
                $licence_no .
                "','" .
                $descn_of_export_item .
                "','" .
                $exp_s_no .
                "','" .
                $expqty .
                "','" .
                $uqc_aa .
                "','" .
                $fob_value .
                "','" .
                $sion .
                "','" .
                $descn_of_import_item .
                "','" .
                $imp_s_no .
                "','" .
                $impqt .
                "','" .
                $uqc_ .
                "','" .
                $indig_imp .
                "','" .
                $created_at .
                "')";
        $copy = $db1->query($sql_insert);
            //print_r($db1->last_query());
        }
        /******************************************************************End aa_dfia_licence_details***************************************************************************************/

        
    }


public function inArray($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     //echo "=>".$val['be_no']. "==". $value;
             if($val['be_no'] == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
}
public function inArray_be_date($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     //echo "=>".$val['be_no']. "==". $value;
             if($val['be_date'] == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function bill_of_entry_summary(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/

        //$bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary WHERE Where created_at >= current_date::timestamp and
     // created_at < current_date::timestamp + interval '1 day'";
    echo $bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary";
 //echo $bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary";
        $statement1_bm1 = $this->db->query($bm1);
        $iecwise_data1_bm1 = [];
        $result1_bm1 = $statement1_bm1->result_array();
 // Set the batch size
        $batchSize = 9000;

        // Loop through the records in batches of 9000
        for ($offset = 0;$offset < $result1_bm1[0]["count"];$offset += $batchSize) {
           // echo $offset;
        $query1 = "SELECT * FROM bill_of_entry_summary";
        $statement1 = $this->db->query($query1);
        $iecwise_data1 = [];
        $result1 = $statement1->result_array();
       // print_r($result1);exit;
        foreach ($result1 as $str1) {
           // $iec1 = '0388004673';
            $iec1 =$str1["iec_no"];
            $sql = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec1'";
            $iecwise1 = $this->db->query($sql);
            $iecwise_data1 = $iecwise1->result_array();
            $db1 = $this->database_connection(
                $iecwise_data1[0]["lucrative_users_id"]
            );
//print_r($str1);
echo                 $iecwise_data1[0]["lucrative_users_id"];





            $port = $str1["port"];
          //  if (@get_magic_quotes_gpc()) {
                 $boe_id = addslashes($str1["boe_id"]);
                $boe_file_status_id = addslashes($str1["boe_file_status_id"]);
                $invoice_title = addslashes($str1["invoice_title"]);
                $port = addslashes($str1["port"]);
                $port_code = addslashes($str1["port_code"]);
                $be_no = addslashes($str1["be_no"]);
                $be_date = addslashes($str1["be_date"]);
                $be_type = addslashes($str1["be_type"]);
                $iec_br = addslashes($str1["iec_br"]);
                $iec_no = addslashes($str1["iec_no"]);
                $br = addslashes($str1["br"]);
                $gstin_type = addslashes($str1["gstin_type"]);
                $cb_code = addslashes($str1["cb_code"]);
                $nos = addslashes($str1["nos"]);
                $pkg = addslashes($str1["pkg"]);
                $item = addslashes($str1["item"]);
                $g_wt_kgs = addslashes($str1["g_wt_kgs"]);
                $cont = addslashes($str1["cont"]);
                $be_status = addslashes($str1["be_status"]);
                $mode = addslashes($str1["mode"]);
                $def_be = addslashes($str1["def_be"]);
                $kacha = addslashes($str1["kacha"]);
                $sec_48 = addslashes($str1["sec_48"]);
                $reimp = addslashes($str1["reimp"]);
                $adv_be = addslashes($str1["adv_be"]);
                $assess = addslashes($str1["assess"]);
                $exam = addslashes($str1["exam"]);
                $hss = addslashes($str1["hss"]);
                $first_check = addslashes($str1["first_check"]);
                $prov_final = addslashes($str1["prov_final"]);
                $country_of_origin= addslashes($str1["country_of_origin"]);
                $importer_name_and_address= addslashes($str1["importer_name_and_address"]);
                $country_of_consignment = addslashes($str1["country_of_consignment"]);
                $port_of_loading = addslashes($str1["port_of_loading"]);
                $port_of_shipment = addslashes($str1["port_of_shipment"]);
                $ad_code = addslashes($str1["ad_code"]);
                $cb_name = addslashes($str1["cb_name"]);
                $aeo = addslashes($str1["aeo"]);
                $ucr = addslashes($str1["ucr"]);
                $bcd = addslashes($str1["bcd"]);
                $acd = addslashes($str1["acd"]);
                $sws = addslashes($str1["sws"]);
                $nccd = addslashes($str1["nccd"]);
                $add = addslashes($str1["add"]);
                $cvd = addslashes($str1["cvd"]);
                $igst = addslashes($str1["igst"]);
                $g_cess = addslashes($str1["g_cess"]);
                $sg = addslashes($str1["sg"]);
                $saed = addslashes($str1["saed"]);
                $gsia = addslashes($str1["gsia"]);
                $tta = addslashes($str1["tta"]);
                $health = addslashes($str1["health"]);
                $total_duty = addslashes($str1["total_duty"]);
                $int = addslashes($str1["int"]);
                $pnlty = addslashes($str1["pnlty"]);
                $fine = addslashes($str1["fine"]);
                $tot_ass_val = addslashes($str1["tot_ass_val"]);
                $tot_amount = addslashes($str1["tot_amount"]);
                $wbe_no = addslashes($str1["wbe_no"]);
                $wbe_date = addslashes($str1["wbe_date"]);
                $wh_code = addslashes($str1["wh_code"]);
                $wbe_site= addslashes($str1["wbe_site"]);
                $submission_date = addslashes($str1["submission_date"]);
                $assessment_date = addslashes($str1["assessment_date"]);
                $examination_date = addslashes($str1["examination_date"]);
                $ooc_date = addslashes($str1["ooc_date"]);
                $submission_time = addslashes($str1["submission_time"]);
                $assessment_time = addslashes($str1["assessment_time"]);
                $examination_time = addslashes($str1["examination_time"]);
                $ooc_time = addslashes($str1["ooc_time"]);
                $submission_exchange_rate = addslashes($str1["submission_exchange_rate"]);
                $assessment_exchange_rate = addslashes($str1["assessment_exchange_rate"]);
                $ooc_no = addslashes($str1["ooc_no"]);
                $ooc_date_ = addslashes($str1["ooc_date_"]);
                $created_at = addslashes($str1["created_at"]);
                $examination_exchange_rate = addslashes($str1["examination_exchange_rate"]);
                $ooc_exchange_rate = addslashes($str1["ooc_exchange_rate"]);
                
/******************************checking dupliacte entries d2d**************************************/
        $sql_beo = "SELECT * FROM bill_of_entry_summary";
        $iecwise1_beo = $db1->query($sql_beo);
        $iecwise_data1_beo = array();
        
        while ($row = $iecwise1_beo->fetch_assoc()) {
        $iecwise_data1_beo[] = $row;
        }
        
        //skip dupliacte entry     
              /*   $a= $this->inArray($iecwise_data1_beo,$be_no); // Output - value exists
                 $b= $this->inArray_be_date($iecwise_data1_beo,$be_date);
                 if ($a==1 && $b==1) {
                      echo "Duplicate";"============";continue;
                 }*/
 
/*********************************************************************************************************/
                
                
               
                    if($int ==''){$int=0;}
                    if($bcd ==''){$bcd=0;}
                    if($acd ==''){$acd=0;}    
                    if($sws ==''){$sws=0;}
                    if($nccd ==''){$nccd=0;}
                    if($add ==''){$add=0;}
                    if($cvd ==''){$cvd=0;}
                    if($igst ==''){$igst=0;}
                    if($g_cess ==''){$g_cess=0;}
                    if($sg ==''){$sg=0;}
                    if($saed ==''){$saed=0;}
                    if($gsia ==''){$gsia=0;}
                    if($tta ==''){$tta=0;}
                    if($health ==''){$health=0;}
                    if($total_duty ==''){$total_duty=0;}
                    if($pnlty ==''){$pnlty=0;}
                    if($fine ==''){$fine=0;}
                    if($tot_ass_val ==''){$tot_ass_val=0;}
                    if($tot_amount ==''){$tot_amount=0;}

          //echo $submission_date;
            $wbe_date = date("Y-m-d",strtotime($wbe_date));
              $submission_date = date("Y-m-d",strtotime($submission_date));
               $assessment_date = date("Y-m-d",strtotime($assessment_date));
               $examination_date = date("Y-m-d",strtotime($examination_date));
            $ooc_date = date("Y-m-d",strtotime($ooc_date));
           
           echo $sql_insert1 =
                "INSERT INTO `bill_of_entry_summary`( `boe_id`,`boe_file_status_id`, `invoice_title`, `port`, `port_code`, `be_no`, `be_date`, `be_type`, `iec_br`, `iec_no`, `br`, `gstin_type`, `cb_code`, `nos`, `pkg`, `item`, `g_wt_kgs`, `cont`, `be_status`, `mode`, `def_be`, `kacha`, `sec_48`, `reimp`, `adv_be`, `assess`, `exam`, `hss`, `first_check`, `prov_final`, `country_of_origin`, `country_of_consignment`, `port_of_loading`, `port_of_shipment`, `importer_name_and_address`, `ad_code`, `cb_name`, `aeo`, `ucr`, `bcd`, `acd`, `sws`, `nccd`, `add`, `cvd`, `igst`, `g_cess`, `sg`, `saed`, `gsia`, `tta`, `health`, `total_duty`, `int`, `pnlty`, `fine`, `tot_ass_val`, `tot_amount`, `wbe_no`, `wbe_date`, `wbe_site`, `wh_code`, `submission_date`, `assessment_date`, `examination_date`, `ooc_date`, `submission_time`, `assessment_time`, `examination_time`, `ooc_time`, `submission_exchange_rate`, `assessment_exchange_rate`, `ooc_no`, `ooc_date_`, `created_at`, `examination_exchange_rate`, `ooc_exchange_rate`) 
                    VALUES ('".$boe_id .
                "','" .
                $boe_file_status_id .
                "','" .
                $invoice_title .
                "','" .
                $port .
                "','" .
                $port_code .
                "','" .
                $be_no .
                "','" .
                $be_date .
                "','" .
                $be_type .
                "','" .
                $iec_br .
                "', '" .
                $iec_no .
                "', '" .
                $br .
                "', '" .
                $gstin_type .
                "','" .
                $cb_code .
                "', '" .
                $nos .
                "','" .
                $pkg .
                "','" .
                $item .
                "','" .
                $g_wt_kgs .
                "','" .
                $cont .
                "','" .
                $be_status .
                "','" .
                $mode .
                "','" .
                $def_be .
                "','" .
                $kacha .
                "','" .
                $sec_48 .
                "','" .
                $reimp .
                "','" .
                $adv_be .
                "','" .
                $assess .
                "','" .
                $exam .
                "', '" .
                $hss .
                "','" .
                $first_check .
                "', '" .
                $prov_final .
                "', '" .
                $country_of_origin .
                "','" .
                $country_of_consignment .
                "','" .
                $port_of_loading .
                "','" .
                $port_of_shipment .
                "','" .
                $importer_name_and_address .
                "','" .
                $ad_code .
                "','" .
                $cb_name .
                "','" .
                $aeo .
                "','" .
                $ucr .
                "','" .
                $bcd .
                "','" .
                $acd .
                "','" .
                $sws .
                "','" .
                $nccd .
                "','" .
                $add .
                "','" .
                $cvd .
                "', '" .
                $igst .
                "','" .
                $g_cess .
                "','" .
                $sg .
                "', '" .
                $saed .
                "','" .
                $gsia .
                "', '" .
                $tta .
                "','" .
                $health .
                "','" .
                $total_duty .
                "','" .
                $int .
                "','" .
                $pnlty .
                "','" .
                $fine .
                "', '" .
                $tot_ass_val .
                "', '" .
                $tot_amount .
                "','" .
                $wbe_no .
                "', '" .
                $wbe_date .
                "', '" .
                $wbe_site .
                "','" .
                $wh_code .
                "', '" .
                $submission_date .
                "', '" .
                $assessment_date .
                "', '" .
                $examination_date .
                "', '" .
                $ooc_date .
                "', '" .
                $submission_time .
                "','" .
                $assessment_time .
                "','" .
                $examination_time .
                "','" .
                $ooc_time .
                "','" .
                $submission_exchange_rate .
                "', '" .
                $assessment_exchange_rate .
                "','" .
                $ooc_no .
                "', '" .
                $ooc_date_ .
                "','" .
                $created_at .
                "','" .
                $examination_exchange_rate .
                "', '" .
                $ooc_exchange_rate .
                "')";

        $copy_bill_of_entry_summary = $db1->query($sql_insert1);
           // print_r($db1->last_query());
        }
        }
        
        /******************************************************************End bill_of_entry_summary***************************************************************************************/
        
    }
    
public function inArray_boe_delete_logs($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['be_no']."-".$val['be_date']."-".$val['iec_no'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function boe_delete_logs(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/
        $query_boe_delete_logs = "SELECT  * FROM boe_delete_logs WHERE ";

        //$query_boe_delete_logs = "SELECT  * FROM boe_delete_logs WHERE deleted_at >= CURRENT_DATE AND deleted_at < CURRENT_DATE + INTERVAL '1 day'";
        $statement_boe_delete_logs = $this->db->query($query_boe_delete_logs);
        $iecwise_data_boe_delete_logs = [];
        $result_boe_delete_logs = $statement_boe_delete_logs->result_array();
       // echo count($result_boe_delete_logs);
     //print_r($result_boe_delete_logs);exit;
    if(!empty($result_boe_delete_logs)){
        foreach ($result_boe_delete_logs as $str_boe_delete_logs) { print_r($str_boe_delete_logs);
            $iec_boe_delete_logs = $str_boe_delete_logs["iec_no"];
            $sql_boe_delete_logs = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_boe_delete_logs'";
            $iecwise_boe_delete_logs = $this->db->query($sql_boe_delete_logs);
            $iecwise_data_boe_delete_logs = $iecwise_boe_delete_logs->result_array();
            $db1_boe_delete_logs = $this->database_connection(
                $iecwise_data_boe_delete_logs[0]["lucrative_users_id"]
            );
       
                 $boe_delete_logs_id = $str_boe_delete_logs["boe_delete_logs_id"];
                $filename = $str_boe_delete_logs["filename"];
                $be_no = $str_boe_delete_logs["be_no"];
                $be_date = $str_boe_delete_logs["be_date"];
                $iec_no = $str_boe_delete_logs["iec_no"];
                $br = $str_boe_delete_logs["br"];
                $fullname = $str_boe_delete_logs["fullname"];
                $email = $str_boe_delete_logs["email"];
                $mobile = $str_boe_delete_logs["mobile"];
                $deleted_at = $str_boe_delete_logs["deleted_at"];
         
             /********************checking dupliacte entries d2d***********************/
                         $sql_users = "SELECT  * FROM boe_delete_logs";
                                 $iecwise1_users = $db1_boe_delete_logs->query($sql_users);
                        $iecwise_data1_users = array();
                        
                        while ($rowusers = $iecwise1_users->fetch_assoc()) {
                        $iecwise_data1_users[] = $rowusers;
                        }
                        
                       $c= $be_no."-".$be_date."-".$iec_no;
                        //skip dupliacte entry     
                 $a= $this->inArray_boe_delete_logs($iecwise_data1_users,$c); // Output - value exists
                 if ($a==1) {
                      echo "Duplicate";"============";continue;
                 }
                 
                /**************************************************/
            echo $sql_insert_boe_delete_logs =
                "INSERT INTO `boe_delete_logs` (`boe_delete_logs_id`,`filename`, `be_no`, `be_date`, `iec_no`, `br`, `fullname`, `email`, `mobile`, `deleted_at`) VALUES('" .
                $boe_delete_logs_id .
                "','" .
                $filename .
                "','" .
                $be_no .
                "','" .
                $be_date .
                "','" .
                $iec_no .
                "','" .
                $br .
                "','" .
                $fullname .
                "','" .
                $email .
                "','" .
                $mobile .
                "','" .
                $deleted_at .
                "')";

            $copy_bill_of_entry_summary_boe_delete_logs = $db1_boe_delete_logs->query(
                $sql_insert_boe_delete_logs
            );
            //  print_r($db1->last_query());
        }
        
}
        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }

  
public function inArray_bill_payment_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['be_no']."-".$val['challan_no'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function bill_payment_details(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/
//$query_bill_payment_details = "SELECT  bill_payment_details.* , bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_payment_details  LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_payment_details.boe_id WHERE bill_payment_details.created_at >= CURRENT_DATE AND bill_payment_details.created_at >= NOW() - INTERVAL '24 HOURS'";
       
        $query_bill_payment_details =
            "SELECT  bill_payment_details.* , bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_payment_details  LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_payment_details.boe_id ";
        $statement_bill_payment_details = $this->db->query(
            $query_bill_payment_details
        );
        $iecwise_data_bill_payment_details = [];
        $result_bill_payment_details = $statement_bill_payment_details->result_array();
      //  print_r($result_bill_payment_details);exit;

        foreach ($result_bill_payment_details as $str_bill_payment_details) {
            $iec_bill_payment_details = $str_bill_payment_details["iec_no"];
            echo $sql_bill_payment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_bill_payment_details'";
            $iecwise_bill_payment_details = $this->db->query(
                $sql_bill_payment_details
            );
            $iecwise_data_bill_payment_details = $iecwise_bill_payment_details->result_array();
            $db1_bill_payment_details = $this->database_connection(
                $iecwise_data_bill_payment_details[0]["lucrative_users_id"]
            );
                $payment_details_id = addslashes($str_bill_payment_details["payment_details_id"]);
                $sr_no = addslashes($str_bill_payment_details["sr_no"]);
                 $be_no = addslashes($str_bill_payment_details["be_no"]);
                 $boe_id = addslashes($str_bill_payment_details["boe_id"]);
                $challan_no = addslashes($str_bill_payment_details["challan_no"]);
                $paid_on = addslashes($str_bill_payment_details["paid_on"]);
                $amount = addslashes($str_bill_payment_details["amount"]);
                $created_at = addslashes($str_bill_payment_details["created_at"]);
         
            
                        $paid_on = date("Y-m-d",strtotime($paid_on));
         /********************checking dupliacte entries d2d***********************/
                         $sql_users = "SELECT  bill_payment_details.* , bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_payment_details  LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_payment_details.boe_id";
                        $iecwise1_users = $db1_bill_payment_details->query($sql_users);
                        $iecwise_data1_users = array();
                        
                        while ($rowusers = $iecwise1_users->fetch_assoc()) {
                        $iecwise_data1_users[] = $rowusers;
                        }
                        
                        $c= $be_no."-".$challan_no;
                        //skip dupliacte entry     
                        $a= $this->inArray_bill_payment_details($iecwise_data1_users,$c); // Output - value exists
                        if ($a==1) {
                        echo "Duplicate";"============";continue;
                        }
                        else{
                 
     /***********************************************************************/
            echo $sql_insert_bill_payment_details =
                "INSERT INTO `bill_payment_details` (`payment_details_id`,`boe_id`, `sr_no`, `challan_no`, `paid_on`, `amount`, `created_at`) VALUES('" .
                $payment_details_id .
                  "','" .
                $boe_id .
                "','" .
                $sr_no .
                "','" .
                $challan_no .
                "','" .
                $paid_on .
                "','" .
                $amount .
                "','" .
                $created_at .
                "')";

           $copy_bill_of_entry_summary_bill_payment_details = $db1_bill_payment_details->query(
                $sql_insert_bill_payment_details
            );
            //  print_r($db1->last_query());
        }
     }

        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }
    
public function inArray_bill_container_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['be_no']."-".$val['container_number'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function bill_container_details(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/

        $query_bill_container_details =
            "SELECT bill_container_details.*, bill_of_entry_summary.be_no,bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM bill_container_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_container_details.boe_id";
        $statement_bill_container_details = $this->db->query(
            $query_bill_container_details
        );
        $iecwise_data_bill_container_details = [];
        $result_bill_container_details = $statement_bill_container_details->result_array();
    // print_r($result_bill_container_details);exit;

        foreach (
            $result_bill_container_details
            as $str_bill_container_details
        ) {print_r($str_bill_container_details);
            $iec_bill_container_details = $str_bill_container_details["iec_no"];
            $sql_bill_container_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_bill_container_details'";
            $iecwise_bill_container_details = $this->db->query(
                $sql_bill_container_details
            );
            $iecwise_data_bill_container_details = $iecwise_bill_container_details->result_array();
            $db1_bill_container_details = $this->database_connection(
                $iecwise_data_bill_container_details[0]["lucrative_users_id"]
            );

           // if (get_magic_quotes_gpc()) {
                $container_details_id = addslashes(
                    $str_bill_container_details["container_details_id"]
                );
                $be_no = addslashes($str_bill_container_details['be_no']);
                $boe_id = addslashes($str_bill_container_details['boe_id']);
                $sno = addslashes($str_bill_container_details["sno"]);
                $lcl_fcl = addslashes($str_bill_container_details["lcl_fcl"]);
                $truck = addslashes($str_bill_container_details["truck"]);
                $seal = addslashes($str_bill_container_details["seal"]);
                $container_number = addslashes(
                    $str_bill_container_details["container_number"]
                );
                $created_at = addslashes(
                    $str_bill_container_details["created_at"]
                );
          /********************checking dupliacte entries d2d***********************/
                         $sql_users = "SELECT bill_container_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_container_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_container_details.boe_id";
                        $iecwise1_users = $db1_bill_container_details->query($sql_users);
                        $iecwise_data1_users = array();
                        
                        while ($rowusers = $iecwise1_users->fetch_assoc()) {
                        $iecwise_data1_users[] = $rowusers;
                        }
                        
                        $c= $be_no."-".$container_number;
                        //skip dupliacte entry     
                        $a= $this->inArray_bill_container_details($iecwise_data1_users,$c); // Output - value exists
                        if ($a==1) {
                        echo "Duplicate";"============";continue;
                        }
                        else{
                 
     /***********************************************************************/
if(empty($sno)){$sno=0;}
//if(empty($sno)){$sno=0;}
           echo $sql_insert_bill_container_details =
                "INSERT INTO `bill_container_details` (`container_details_pk`, `boe_id`,`sno`, `lcl_fcl`, `truck`, `seal`, `container_number`,`created_at`) VALUES('" .
                $container_details_id .
                "','" .
                $boe_id .
                "','" .
                $sno .
                "','" .
                $lcl_fcl .
                "','" .
                $truck .
                "','" .
                $seal .
                "','" .
                $container_number .
                "','" .
                $created_at .
                "')";
            $copy_bill_container_details = $db1_bill_container_details->query(
                $sql_insert_bill_container_details
            );
            //  print_r($db1->last_query());
        }
}
        /******************************************************************End bill_of_entry_summary***************************************************************************************/
}

public function bill_bond_details(){
    /******************************************************************Start bill_of_entry_summary***************************************************************************************/
$query_bill_bond_details =
        "SELECT bill_bond_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_bond_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_bond_details.boe_id  ";
    $statement_bill_bond_details = $this->db->query(
        $query_bill_bond_details
    );
    $iecwise_data_bill_bond_details = [];
    $result_bill_bond_details = $statement_bill_bond_details->result_array();
    echo count($result_bill_bond_details);
//print_r($result_bill_bond_details);exit;

    foreach ($result_bill_bond_details as $str_bill_bond_details) {
        $iec_bill_bond_details = $str_bill_bond_details["iec_no"];
        //$iec_bill_bond_details ='1308004672';
        echo $sql_bill_bond_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_bill_bond_details'";
        $iecwise_bill_bond_details = $this->db->query(
            $sql_bill_bond_details
        );
        $iecwise_data_bill_bond_details = $iecwise_bill_bond_details->result_array();
        $db1_bill_bond_details = $this->database_connection(
            $iecwise_data_bill_bond_details[0]["lucrative_users_id"]
        );
       // if (get_magic_quotes_gpc()) {
        $boe_id = addslashes($str_bill_bond_details["boe_id"]);
        $be_no = addslashes($str_bill_bond_details["be_no"]);
        $bond_details_id = addslashes($str_bill_bond_details["bond_details_id"]);
        $bond_no = addslashes($str_bill_bond_details["bond_no"]);
        $port = addslashes($str_bill_bond_details["port"]);
        $bond_cd = addslashes($str_bill_bond_details["bond_cd"]);
        $debt_amt = addslashes($str_bill_bond_details["debt_amt"]);
        $bg_amt = addslashes($str_bill_bond_details["bg_amt"]);
        $created_at = addslashes($str_bill_bond_details["created_at"]);
      
       /********************checking dupliacte entries d2d***********************/
                     $sql_users = "SELECT bill_bond_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_bond_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_bond_details.boe_id";
                             $iecwise1_users = $db1_bill_bond_details->query($sql_users);
                    $iecwise_data1_users = array();
                    
                    while ($rowusers = $iecwise1_users->fetch_assoc()) {
                    $iecwise_data1_users[] = $rowusers;
                    }
                    
                   $c= $be_no."-".$bond_no;
                    //skip dupliacte entry     
             $a= $this->inArray_bill_bond_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
 /***********************************************************************/
     echo   $sql_insert_bill_bond_details =
            "INSERT INTO `bill_bond_details` (`boe_id`,`bond_details_id`, `bond_no`, `port`, `bond_cd`, `debt_amt`, `bg_amt`,`created_at`) VALUES('" .
            $boe_id .
            "','" .
            $bond_details_id .
            "','" .
            $bond_no .
            "','" .
            $port .
            "','" .
            $bond_cd .
            "','" .
            $debt_amt .
            "','" .
            $bg_amt .
            "','" .
            $created_at .
            "')";
       $copy_bill_bond_details = $db1_bill_bond_details->query(
            $sql_insert_bill_bond_details
        );
        //  print_r($db1->last_query());
    }
}

    /******************************************************************End bill_of_entry_summary***************************************************************************************/
} 
public function inArray_bill_bond_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['be_no']."-".$val['bond_no'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
    
    
public function bill_licence_details(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/
       $query_bill_licence_details ="SELECT CONCAT(n1.be_no,'-',bill_licence_details.invsno,'-',bill_licence_details.itemsn) as reference_code,n1.boe_id, n1.iec_no,n1.be_no,n1.be_date, bill_licence_details.* FROM (select * from bill_of_entry_summary order by boe_id ) n1 JOIN invoice_and_valuation_details ON n1.boe_id = invoice_and_valuation_details.boe_id JOIN duties_and_additional_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id JOIN bill_licence_details ON duties_and_additional_details.duties_id = bill_licence_details.duties_id and bill_licence_details.invsno != ''";
     
        //echo $query_bill_licence_details ="SELECT CONCAT(n1.be_no,'-',bill_licence_details.invsno,'-',bill_licence_details.itemsn) as reference_code,n1.boe_id, n1.iec_no,n1.be_no,n1.be_date, bill_licence_details.* FROM (select * from bill_of_entry_summary order by boe_id ) n1 JOIN invoice_and_valuation_details ON n1.boe_id = invoice_and_valuation_details.boe_id JOIN duties_and_additional_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id JOIN bill_licence_details ON duties_and_additional_details.duties_id = bill_licence_details.duties_id and bill_licence_details.invsno != ''";
        $statement_bill_licence_details = $this->db->query(
            $query_bill_licence_details
        );
        $iecwise_data_bill_licence_details = [];
        $result_bill_licence_details = $statement_bill_licence_details->result_array();
       //print_r($result_bill_licence_details);exit;

        foreach ($result_bill_licence_details as $str_bill_licence_details) {
            $iec_bill_licence_details = $str_bill_licence_details["iec_no"];
            $sql_bill_licence_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_bill_licence_details'";
            $iecwise_bill_licence_details = $this->db->query(
                $sql_bill_licence_details
            );
            $iecwise_data_bill_licence_details = $iecwise_bill_licence_details->result_array();
            $db1_bill_licence_details = $this->database_connection(
                $iecwise_data_bill_licence_details[0]["lucrative_users_id"]
            );
//print_r($result_bill_licence_details);exit;
            //if (get_magic_quotes_gpc()) {
                $duties_id = addslashes( $str_bill_licence_details["duties_id"]);
                 $reference_code = addslashes($str_bill_licence_details["reference_code"]);
                $be_no = addslashes($str_bill_licence_details["be_no"]);
                $be_date = addslashes($str_bill_licence_details["be_date"]);
                $invsno = addslashes($str_bill_licence_details["invsno"]);
                $itemsn = addslashes($str_bill_licence_details["itemsn"]);
                $lic_slno = addslashes($str_bill_licence_details["lic_slno"]);
                $lic_no = addslashes($str_bill_licence_details["lic_no"]);
                $lic_date = addslashes($str_bill_licence_details["lic_date"]);
                $code = addslashes($str_bill_licence_details["code"]);
                $port = addslashes($str_bill_licence_details["port"]);
                $debit_value = addslashes($str_bill_licence_details["debit_value"]);
                $qty = addslashes($str_bill_licence_details["qty"]);
                $uqc_lc_d = addslashes($str_bill_licence_details["uqc_lc_d"]);
                $debit_duty = addslashes($str_bill_licence_details["debit_duty"]);
                $created_at = addslashes($str_bill_licence_details["created_at"]);
           
 $lic_date =   date("Y-m-d",strtotime($lic_date));
 if(empty($qty)){
   $qty=0;  
 }
 if(empty($uqc_lc_d)){
   $uqc_lc_d=0;  
 }
 /********************checking dupliacte entries d2d***********************/
        $sql_users ="SELECT CONCAT(n1.be_no,'-',bill_licence_details.invsno,'-',bill_licence_details.itemsn) as reference_code,n1.boe_id, n1.iec_no,n1.be_no,n1.be_date, bill_licence_details.* FROM (select * from bill_of_entry_summary order by boe_id ) n1 JOIN invoice_and_valuation_details ON n1.boe_id = invoice_and_valuation_details.boe_id JOIN duties_and_additional_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id JOIN bill_licence_details ON duties_and_additional_details.duties_id = bill_licence_details.duties_id and bill_licence_details.invsno != ''";

                        $iecwise1_users = $db1_bill_licence_details->query($sql_users);
                        $iecwise_data1_users = array();
                        
                        while ($rowusers = $iecwise1_users->fetch_assoc()) {
                        $iecwise_data1_users[] = $rowusers;
                        }
                       echo $c= $reference_code."/".$be_date."/".$lic_slno."/".$lic_no."/".$debit_value."/".$qty;
                     
                        //skip dupliacte entry     
                        $a= $this->inArray_bill_licence_details($iecwise_data1_users,$c); // Output - value exists
                        if ($a==1) {
                        echo "Duplicate";"============";continue;
                        }
                        else{
                
     /***********************************************************************/
            echo $sql_insert_bill_licence_details =
                "INSERT INTO `bill_licence_details` (`duties_id`, `invsno`, `itemsn`, `lic_slno`, `lic_no`, `lic_date`,`code`,`port`,`debit_value`,`qty`,`uqc_lc_d`,`debit_duty`,`created_at`) VALUES('" .
                $duties_id .
                "','" .
                $invsno .
                "','" .
                $itemsn .
                "','" .
                $lic_slno .
                "','" .
                $lic_no .
                "','" .
                $lic_date .
                "','" .
                $code .
                "','" .
                $port .
                "','" .
                $debit_value .
                "','" .
                $qty .
                "','" .
                $uqc_lc_d .
                "','" .
                $debit_duty .
                "','" .
                $created_at .
                "')";
            $copy_bill_licence_details = $db1_bill_licence_details->query(
                $sql_insert_bill_licence_details
            );
        }
       }
//exit;
        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }
public function inArray_bill_licence_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
    $c= $val['reference_code'].'/'.$val['be_date'].'/'.$val['lic_slno'].'/'.$val['lic_no'].'/'.$val['debit_value'].'/'.$val['qty'];
   //  $c= $reference_code."/".$be_date."/".$lic_slno."/".$lic_no."/".$debit_value."/".$qty;
    
     //$c= $val['be_no']."-".$val['invsno']."-".$val['itemsn'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }

public function boe_file_status(){
    /******************************************************************Start bill_of_entry_summary***************************************************************************************/
    $query_boe_file_status = "SELECT * FROM public.boe_file_status ";
   
          //$query_boe_file_status = "SELECT * FROM public.boe_file_status";

    $statement_boe_file_status = $this->db->query($query_boe_file_status);
    $iecwise_data_boe_file_status = [];
    $result_boe_file_status = $statement_boe_file_status->result_array();
  // print_r($result_boe_file_status);//exit;

    foreach ($result_boe_file_status as $str_boe_file_status) {
        $iec_boe_file_status = $str_boe_file_status["user_iec_no"];
        echo $sql_boe_file_status = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_boe_file_status%'";
        $iecwise_boe_file_status = $this->db->query($sql_boe_file_status);
        $iecwise_data_boe_file_status = $iecwise_boe_file_status->result_array();
        $db1_boe_file_status = $this->database_connection(
            $iecwise_data_boe_file_status[0]["lucrative_users_id"]
        );
      //  if (get_magic_quotes_gpc()) {
        $boe_file_status_id= addslashes($str_boe_file_status["boe_file_status_id"]);
        $pdf_filepath = addslashes($str_boe_file_status["pdf_filepath"]);
        $pdf_filename = addslashes($str_boe_file_status["pdf_filename"]);
        $user_iec_no = addslashes($str_boe_file_status["user_iec_no"]);
        $lucrative_users_id = addslashes($str_boe_file_status["lucrative_users_id"]);
        $file_iec_no = addslashes($str_boe_file_status["file_iec_no"]);
        $br = addslashes($str_boe_file_status["br"]);
        $be_no = addslashes($str_boe_file_status["be_no"]);
        $stage = addslashes($str_boe_file_status["stage"]);
        $status = addslashes($str_boe_file_status["status"]);
        $remarks = addslashes($str_boe_file_status["remarks"]);
        $created_at = addslashes($str_boe_file_status["created_at"]);

       echo $sql_insert_boe_file_status =
            "INSERT INTO `boe_file_status` (`boe_file_status_id`,`pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`,`br`,`be_no`,`stage`,`status`,`remarks`,`created_at`) VALUES('" .
            $boe_file_status_id.
            "','" .
            $pdf_filename .
            "','" .
            $pdf_filename .
            "','" .
            $user_iec_no .
            "','" .
            $lucrative_users_id .
            "','" .
            $file_iec_no .
            "','" .
            $br .
            "','" .
            $be_no .
            "','" .
            $stage .
            "','" .
            $status .
            "','" .
            $remarks .
            "','" .
            $created_at .
            "')"; //  print_r($db1->last_query());

        $copy_insert_boe_file_status = $db1_boe_file_status->query(
            $sql_insert_boe_file_status
        );
    }

    /******************************************************************End bill_of_entry_summary***************************************************************************************/
}

public function inArray_bill_manifest_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
              //$c= $be_no."-".$igm_no;
     $c= $val['be_no']."-".$val['igm_no'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function bill_manifest_details(){
    /******************************************************************Start bill_of_entry_summary***************************************************************************************/
       $query_bill_manifest_details ="SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_manifest_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id";
      
// $query_bill_manifest_details =
       // "SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_manifest_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id  ";
  
    //$query_bill_manifest_details =
       // "SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM bill_manifest_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id WHERE bill_manifest_details.created_at >= CURRENT_DATE AND bill_manifest_details.created_at >= NOW() - INTERVAL '24 HOURS'";
    $statement_bill_manifest_details = $this->db->query(
        $query_bill_manifest_details
    );
    $iecwise_data_bill_manifest_details = [];
    $result_bill_manifest_details = $statement_bill_manifest_details->result_array();
 
    foreach ($result_bill_manifest_details as $str_bill_manifest_details) {
        $iec_bill_manifest_details = $str_bill_manifest_details["iec_no"];
      // $iec_bill_manifest_details ='0388004673';
         $sql_bill_manifest_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_bill_manifest_details' and role='admin'";
        $iecwise_bill_manifest_details = $this->db->query(
            $sql_bill_manifest_details
        );
        $iecwise_data_bill_manifest_details = $iecwise_bill_manifest_details->result_array();
        $db1_bill_manifest_details = $this->database_connection(
            $iecwise_data_bill_manifest_details[0]["lucrative_users_id"]
        );
        //exit;
         //print_r($result_bill_manifest_details);exit;
     //   if (get_magic_quotes_gpc()) {
            $manifest_details_id  = addslashes($str_bill_manifest_details["manifest_details_id"]);

            $boe_id = addslashes($str_bill_manifest_details["boe_id"]);
            $be_no = addslashes($str_bill_manifest_details["be_no"]);
            $igm_no = addslashes($str_bill_manifest_details["igm_no"]);
            $igm_date = addslashes($str_bill_manifest_details["igm_date"]);
            $inw_date = addslashes($str_bill_manifest_details["inw_date"]);
            $gigmno = addslashes($str_bill_manifest_details["gigmno"]);
            $gigmdt = addslashes($str_bill_manifest_details["gigmdt"]);
            $mawb_no = addslashes($str_bill_manifest_details["mawb_no"]);
            $mawb_date = addslashes($str_bill_manifest_details["mawb_date"]);
            $hawb_no = addslashes($str_bill_manifest_details["hawb_no"]);
            $hawb_date = addslashes($str_bill_manifest_details["hawb_date"]);
            $pkg = addslashes($str_bill_manifest_details["pkg"]);
            $gw = addslashes($str_bill_manifest_details["gw"]);
            $created_at = addslashes($str_bill_manifest_details["created_at"]);
           /********************checking dupliacte entries d2d***********************/
     $sql_users ="SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no FROM bill_manifest_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id ";

                    $iecwise1_users = $db1_bill_manifest_details->query($sql_users);
                    $iecwise_data1_users = array();
                    
                    while ($rowusers = $iecwise1_users->fetch_assoc()) {
                    $iecwise_data1_users[] = $rowusers;
                    }
                    $c= $be_no."-".$igm_no;
                 
                    //skip dupliacte entry     
                    $a= $this->inArray_bill_manifest_details($iecwise_data1_users,$c); // Output - value exists
                    if ($a==1) {
                    echo "Duplicate";"============";continue;
                    }
                    else{
             
 /***********************************************************************/
        /*$igm_date =   date("Y-m-d",strtotime($igm_date));
        $inw_date =   date("Y-m-d",strtotime($inw_date));
        $gigmdt =   date("Y-m-d",strtotime($gigmdt));
        $mawb_date =   date("Y-m-d",strtotime($mawb_date));
          $hawb_date =   date("Y-m-d",strtotime($hawb_date));*/
echo        $sql_insert_bill_manifest_details =
            "INSERT INTO `bill_manifest_details` (`manifest_details_id`,`boe_id`, `igm_no`, `igm_date`, `inw_date`, `gigmno`, `gigmdt`,`mawb_no`,`mawb_date`,`hawb_no`,`hawb_date`,`pkg`,`gw`,`created_at`) VALUES('" .
            $manifest_details_id.
            "','" .
            $boe_id .
            "','" .
            $igm_no .
            "','" .
            $igm_date .
            "','" .
            $inw_date .
            "','" .
            $gigmno .
            "','" .
            $gigmdt .
            "','" .
            $mawb_no .
            "','" .
            $mawb_date .
            "','" .
            $hawb_no .
            "','" .
            $hawb_date .
            "','" .
            $pkg .
            "','" .
            $gw .
            "','" .
            $created_at .
            "')";
      $copy_insert_bill_manifest_details = $db1_bill_manifest_details->query(
            $sql_insert_bill_manifest_details
        );
    }
}

    /******************************************************************End bill_of_entry_summary***************************************************************************************/
}

public function inArray_challan_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
              //$c= $be_no."-".$igm_no;
     $c= $val['sb_no']."-".$val['challan_no'];
     
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function challan_details(){
    /******************************************************************Start bill_of_entry_summary***************************************************************************************/

    $query_challan_details =
        "SELECT challan_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM challan_details  JOIN ship_bill_summary ON ship_bill_summary.sbs_id=challan_details.sbs_id ";
    $statement_challan_details = $this->db->query($query_challan_details);
    $iecwise_challan_details = [];
    $result_challan_details = $statement_challan_details->result_array();
   // print_r($result_challan_details);exit;

    foreach ($result_challan_details as $str_challan_details) {print_r($str_challan_details);
        $iec_challan_details = $str_challan_details["iec"];
        $sql_challan_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_challan_details'";
        $iecwise_challan_details = $this->db->query($sql_challan_details);
        $iecwise_data_challan_details = $iecwise_challan_details->result_array();
        $db1_challan_details = $this->database_connection(
            $iecwise_data_challan_details[0]["lucrative_users_id"]
        );

        //if (get_magic_quotes_gpc()) {
          $challan_id = addslashes($str_challan_details["challan_id"]);
            $sbs_id = addslashes($str_challan_details["sbs_id"]);
            $sb_no = addslashes($str_challan_details["sb_no"]);
            $sr_no = addslashes($str_challan_details["sr_no"]);
            $challan_no = addslashes($str_challan_details["challan_no"]);
            $paymt_dt = addslashes($str_challan_details["paymt_dt"]);
            $amount = addslashes($str_challan_details["amount"]);
            $created_at = addslashes(
                $str_challan_details["created_at"]
            );
            
           /********************checking dupliacte entries d2d***********************/
     $sql_users ="SELECT challan_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM challan_details  JOIN ship_bill_summary ON ship_bill_summary.sbs_id=challan_details.sbs_id";

                    $iecwise1_users = $db1_challan_details->query($sql_users);
                    $iecwise_data1_users = array();
                    
                    while ($rowusers = $iecwise1_users->fetch_assoc()) {
                    $iecwise_data1_users[] = $rowusers;
                    }
                    $c= $sb_no."-".$challan_no;
                 
                    //skip dupliacte entry     
                    $a= $this->inArray_bill_manifest_details($iecwise_data1_users,$c); // Output - value exists
                    if ($a==1) {
                    echo "Duplicate";"============";continue;
                    }
                    else{
             
 /***********************************************************************/    
            
            
       
$paymt_dt =   date("Y-m-d",strtotime($paymt_dt));
        echo $sql_insert_challan_details =
            "INSERT INTO `challan_details` (`challan_id`,`sbs_id`, `sr_no`, `challan_no`, `paymt_dt`, `amount`, `created_at`) VALUES('" .
            $challan_id .
            "','" .
            $sbs_id .
            "','" .
            $sr_no .
            "','" .
            $challan_no .
            "','" .
            $paymt_dt .
            "','" .
            $amount .
            "','" .
            $created_at .
            "')";

        $copy_insert_challan_details = $db1_challan_details->query(
            $sql_insert_challan_details
        );
    }
  }

    /******************************************************************End bill_of_entry_summary***************************************************************************************/
}

public function inArray_drawback_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
              //$c= $be_no."-".$igm_no;
     $c= $val['reference_code'];
     
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function drawback_details(){
    /******************************************************************Start bill_of_entry_summary***************************************************************************************/
$query_drawback_details ="SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br,iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* FROM (select * from ship_bill_summary order by sbs_id desc) n1 JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id JOIN drawback_details ON drawback_details.item_id = item_details.item_id ";
//$query_drawback_details ="SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br,iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* FROM (select * from ship_bill_summary order by sbs_id desc) n1 JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id JOIN drawback_details ON drawback_details.item_id = item_details.item_id";
    
    
    $statement_drawback_details = $this->db->query($query_drawback_details);
    $iecwise_drawback_details = [];
    $result_drawback_details = $statement_drawback_details->result_array();
    //print_r($result_drawback_details);exit;

    foreach ($result_drawback_details as $str_drawback_details) {
        $iec_drawback_details = $str_drawback_details["iec"];
        $sql_drawback_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_drawback_details'";
        $iecwise_drawback_details = $this->db->query($sql_drawback_details);
        $iecwis_drawback_details = $iecwise_drawback_details->result_array();
        $db1_drawback_details = $this->database_connection(
            $iecwis_drawback_details[0]["lucrative_users_id"]
        );
    //    if (get_magic_quotes_gpc()) {
            $reference_code = addslashes($str_drawback_details["reference_code"]);
             $drawback_id = addslashes($str_drawback_details["drawback_id"]);
            $item_id = addslashes($str_drawback_details["item_id"]);
            $inv_sno = addslashes($str_drawback_details["inv_sno"]);
            $item_sno = addslashes($str_drawback_details["item_sno"]);
            $dbk_sno = addslashes($str_drawback_details["dbk_sno"]);
            $qty_wt = addslashes($str_drawback_details["qty_wt"]);
            $value = addslashes($str_drawback_details["value"]);
            $dbk_amt = addslashes($str_drawback_details["dbk_amt"]);
            $stalev = addslashes($str_drawback_details["stalev"]);
            $cenlev = addslashes($str_drawback_details["cenlev"]);
            $rosctl_amt = addslashes($str_drawback_details["rosctl_amt"]);
            $rate = addslashes($str_drawback_details["rate"]);
            $rebate = addslashes($str_drawback_details["rebate"]);
            $amount = addslashes($str_drawback_details["amount"]);
            $dbk_rosl = addslashes($str_drawback_details["dbk_rosl"]);
            $created_at = addslashes($str_drawback_details["created_at"]);
/********************checking dupliacte entries d2d***********************/
//$sql_users ="SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br,iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* FROM (select * from ship_bill_summary order by sbs_id desc) n1 JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id JOIN drawback_details ON drawback_details.item_id = item_details.item_id";
$sql_users ="SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br,iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* FROM (select * from ship_bill_summary order by sbs_id desc) n1 JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id JOIN drawback_details ON drawback_details.item_id = item_details.item_id";
      
                $iecwise1_users = $db1_drawback_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                $c= $reference_code;
             
                //skip dupliacte entry     
                $a= $this->inArray_drawback_details($iecwise_data1_users,$c); // Output - value exists
                if ($a==1) {
                echo "Duplicate";"============";continue;
                }
                else{
         
/***********************************************************************/    
  
        echo $sql_insert_drawback_details =
            "INSERT INTO `drawback_details` (`drawback_id`,`item_id`, `inv_sno`, `item_sno`, `dbk_sno`, `qty_wt`, `value`,`dbk_amt`,`stalev`,`cenlev`,`rosctl_amt`,`created_at`,`rate`,`rebate`,`amount`,`dbk_rosl`) VALUES('" .
            $drawback_id .
            "','" .
            $item_id .
            "','" .
            $inv_sno .
            "','" .
            $item_sno .
            "','" .
            $dbk_sno .
            "','" .
            $qty_wt .
            "','" .
            $value .
            "','" .
            $dbk_amt .
            "','" .
            $stalev .
            "','" .
            $cenlev .
            "','" .
            $rosctl_amt .
            "','" .
            $created_at .
            "','" .
            $rate .
            "','" .
            $rebate .
            "','" .
            $amount .
            "','" .
            $dbk_rosl .
            "')";
        $copy_insert_drawback_details = $db1_drawback_details->query(
            $sql_insert_drawback_details
        );
    }
 }

    /******************************************************************End bill_of_entry_summary***************************************************************************************/
}


public function cb_file_status(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/

        $query_cb_file_status = "SELECT * FROM cb_file_status ";
        $statement_cb_file_status = $this->db->query($query_cb_file_status);
        $iecwise_cb_file_status = [];
        $result_cb_file_status = $statement_cb_file_status->result_array();
       // print_r($result_cb_file_status);exit;

        foreach ($result_cb_file_status as $str_cb_file_status) {
            $iec_cb_file_status = $str_cb_file_status["user_iec_no"];
            $sql_cb_file_status = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_cb_file_status'";
            $iecwise_cb_file_status = $this->db->query($sql_cb_file_status);
            $iecwise_data_cb_file_status = $iecwise_cb_file_status->result_array();
            $db1_cb_file_status = $this->database_connection(
                $iecwise_data_cb_file_status[0]["lucrative_users_id"]
            );
           // if (get_magic_quotes_gpc()) {
                $pdf_filepath = addslashes(
                    $str_cb_file_status["pdf_filepath"]
                );
                $pdf_filename = addslashes(
                    $str_cb_file_status["pdf_filename"]
                );
                $user_iec_no = addslashes($str_cb_file_status["user_iec_no"]);
                $lucrative_users_id = addslashes(
                    $str_cb_file_status["lucrative_users_id"]
                );
                $file_iec_no = addslashes($str_cb_file_status["file_iec_no"]);
                $cb_no = addslashes($str_cb_file_status["cb_no"]);
                $cb_date = addslashes($str_cb_file_status["cb_date"]);
                $stage = addslashes($str_cb_file_status["stage"]);
                $status = addslashes($str_cb_file_status["status"]);
                $remarks = addslashes($str_cb_file_status["remarks"]);
                $br = addslashes($str_cb_file_status["br"]);
                $is_processed = addslashes(
                    $str_cb_file_status["is_processed"]
                );
                $created_at = addslashes($str_cb_file_status["created_at"]);
           /* } else {
                $pdf_filepath = $str_cb_file_status["pdf_filepath"];
                $pdf_filename = $str_cb_file_status["pdf_filename"];
                $user_iec_no = $str_cb_file_status["user_iec_no"];
                $lucrative_users_id = $str_cb_file_status["lucrative_users_id"];
                $file_iec_no = $str_cb_file_status["file_iec_no"];
                $cb_no = $str_cb_file_status["cb_no"];
                $cb_date = $str_cb_file_status["cb_date"];
                $stage = $str_cb_file_status["stage"];
                $status = $str_cb_file_status["status"];
                $remarks = $str_cb_file_status["remarks"];
                $br = $str_cb_file_status["br"];
                $is_processed = $str_cb_file_status["is_processed"];
                $created_at = $str_cb_file_status["created_at"];
            }*/
        echo    $sql_insert_cb_file_status =
                "INSERT INTO `cb_file_status` (`pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`, `cb_no`, `cb_date`, `stage`, `status`, `remarks`, `created_at`, `br`, `is_processed`) 
VALUES('" .
                $pdf_filepath .
                "','" .
                $pdf_filename .
                "','" .
                $user_iec_no .
                "','" .
                $lucrative_users_id .
                "','" .
                $file_iec_no .
                "','" .
                $cb_no .
                "','" .
                $cb_date .
                "','" .
                $stage .
                "','" .
                $status .
                "','" .
                $remarks .
                "','" .
                $created_at .
                "','" .
                $br .
                "','" .
                $is_processed .
                "')";
            $copy_insert_cb_file_status = $db1_cb_file_status->query(
                $sql_insert_cb_file_status
            );
        }

        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }

public function courier_bill_bond_details(){
        /******************************************************************Start bill_of_entry_summary***************************************************************************************/

        $query_courier_bill_bond_details =
            "SELECT courier_bill_bond_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_bond_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_bond_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id ";
        $statement_courier_bill_bond_details = $this->db->query(
            $query_courier_bill_bond_details
        );
        $iecwise_courier_bill_bond_details = [];
        $result_courier_bill_bond_details = $statement_courier_bill_bond_details->result_array();
       // print_r($result_courier_bill_bond_details);exit;

        foreach (
            $result_courier_bill_bond_details
            as $str_courier_bill_bond_details
        ) {
            $iec_courier_bill_bond_details =
                $str_courier_bill_bond_details["user_iec_no"];
            $sql_courier_bill_bond_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_bond_details'";
            $iecwise_courier_bill_bond_details = $this->db->query(
                $sql_courier_bill_bond_details
            );
            $iecwise_data_courier_bill_bond_details = $iecwise_courier_bill_bond_details->result_array();
            $db1_courier_bill_bond_details = $this->database_connection(
                $iecwise_data_courier_bill_bond_details[0]["lucrative_users_id"]
            );
           // if (get_magic_quotes_gpc()) {
                $bond_details_id = addslashes(
                    $str_courier_bill_bond_details["bond_details_id"]
                );
                $bond_details_srno = addslashes(
                    $str_courier_bill_bond_details["bond_details_srno"]
                );
                $bond_type = addslashes(
                    $str_courier_bill_bond_details["bond_type"]
                );
                $bond_number = addslashes(
                    $str_courier_bill_bond_details["bond_number"]
                );
                $clearance_of_imported_goods_bond_already_registered_customs = addslashes(
                    $str_courier_bill_bond_details[
                        "clearance_of_imported_goods_bond_already_registered_customs"
                    ]
                );
                $created_at = addslashes(
                    $str_courier_bill_bond_details["created_at"]
                );
           /* } else {
                $bond_details_id =
                    $str_courier_bill_bond_details["bond_details_id"];
                $bond_details_srno =
                    $str_courier_bill_bond_details["bond_details_srno"];
                $bond_type = $str_courier_bill_bond_details["bond_type"];
                $bond_number = $str_courier_bill_bond_details["bond_number"];
                $clearance_of_imported_goods_bond_already_registered_customs =
                    $str_courier_bill_bond_details[
                        "clearance_of_imported_goods_bond_already_registered_customs"
                    ];
                $created_at = $str_courier_bill_bond_details["created_at"];
            }*/
           echo $sql_insert_courier_bill_bond_details =
                "INSERT INTO `courier_bill_bond_details` (`bond_details_id`, `bond_details_srno`, `bond_type`, `bond_number`, `clearance_of_imported_goods_bond_already_registered_customs`, `created_at`) 
VALUES('" .
                $bond_details_id .
                "','" .
                $bond_details_srno .
                "','" .
                $bond_type .
                "','" .
                $bond_number .
                "','" .
                $clearance_of_imported_goods_bond_already_registered_customs .
                "','" .
                $created_at .
                "')";
            $copy_insert_courier_bill_bond_details = $db1_courier_bill_bond_details->query(
                $sql_insert_courier_bill_bond_details
            );
        }

        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }

public function courier_bill_container_details(){
        /******************************************************************Start courier_bill_container_details***************************************************************************************/

        $query_courier_bill_container_details =
            "SELECT courier_bill_container_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_container_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_container_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id";
        $statement_courier_bill_container_details = $this->db->query(
            $query_courier_bill_container_details
        );
        $iecwise_courier_bill_container_details = [];
        $result_courier_bill_container_details = $statement_courier_bill_container_details->result_array();
     //   print_r($result_courier_bill_container_details);exit;

        foreach (
            $result_courier_bill_container_details
            as $str_courier_bill_container_details
        ) {//print_r($str_courier_bill_container_details);
            $iec_courier_bill_container_details =
                $str_courier_bill_container_details["user_iec_no"];
            $sql_courier_bill_container_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_container_details'";
            $iecwise_courier_bill_container_details = $this->db->query(
                $sql_courier_bill_container_details
            );
            $iecwise_data_courier_bill_container_details = $iecwise_courier_bill_container_details->result_array();
            $db1_insert_courier_bill_container_details = $this->database_connection(
                $iecwise_data_courier_bill_container_details[0][
                    "lucrative_users_id"
                ]
            );
            //if (get_magic_quotes_gpc()) {
                $courier_bill_of_entry_id= addslashes(
                    $str_courier_bill_container_details["courier_bill_of_entry_id"]
                );
                $container_details_id = addslashes(
                    $str_courier_bill_container_details["container_details_id"]
                );
                $container_details_srno = addslashes(
                    $str_courier_bill_container_details["container_details_srno"]
                );
                $container = addslashes(
                    $str_courier_bill_container_details["container"]
                );
                $seal_number = addslashes(
                    $str_courier_bill_container_details[
                        "seal_number"
                    ]
                );
                
                 $fcl_lcl = addslashes(
                    $str_courier_bill_container_details["fcl_lcl"]
                );
                $created_at = addslashes(
                    $str_courier_bill_container_details["created_at"]
                );
        /*    } else {
                $bond_details_id =
                    $str_courier_bill_container_details["bond_details_id"];
                $bond_details_srno =
                    $str_courier_bill_container_details["bond_details_srno"];
                $bond_type = $str_courier_bill_container_details["bond_type"];
                $bond_number =
                    $str_courier_bill_container_details["bond_number"];
                $clearance_of_imported_goods_bond_already_registered_customs =
                    $str_courier_bill_container_details[
                        "clearance_of_imported_goods_bond_already_registered_customs"
                    ];
                $created_at = $str_courier_bill_container_details["created_at"];
            }*/
           echo $sql_insert_courier_bill_container_details =
                "INSERT INTO `courier_bill_container_details` (`courier_bill_of_entry_id`, `container_details_id`, `container_details_srno`, `container`, `seal_number`, `fcl_lcl`, `created_at`) 
VALUES('" .$courier_bill_of_entry_id .
                "','" .
                $container_details_id .
                "','" .
                $container_details_srno .
                "','" .
                $container .
                "','" .
                $seal_number.
                "','" .
                $fcl_lcl .
                "','" .
                $created_at .
                "')";
            $copy_insert_courier_bill_container_details = $db1_insert_courier_bill_container_details->query(
                $sql_insert_courier_bill_container_details
            );
        }

        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }

public function courier_bill_duty_details(){
        /******************************************************************Start courier_bill_duty_details***************************************************************************************/

        $query_courier_bill_duty_details =
            "SELECT courier_bill_duty_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no,courier_bill_items_details.items_detail_id,courier_bill_items_details.courier_bill_of_entry_id FROM courier_bill_duty_details LEFT JOIN courier_bill_items_details ON courier_bill_items_details.items_detail_id=courier_bill_duty_details.items_detail_id LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id  ";
        $statement_courier_bill_duty_details = $this->db->query(
            $query_courier_bill_duty_details
        );
        $iecwise_courier_bill_duty_details = [];
        $result_courier_bill_duty_details = $statement_courier_bill_duty_details->result_array();
     //   print_r($result_courier_bill_duty_details);exit;

        foreach (
            $result_courier_bill_duty_details
            as $str_courier_bill_duty_details
        ) {
            $iec_courier_bill_duty_details =
                $str_courier_bill_duty_details["user_iec_no"];
            $sql_courier_bill_duty_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_duty_details'";
            $iecwise_courier_bill_duty_details = $this->db->query(
                $sql_courier_bill_duty_details
            );
            $iecwise_data_courier_bill_duty_details = $iecwise_courier_bill_duty_details->result_array();
            $db1_courier_bill_duty_details = $this->database_connection(
                $iecwise_data_courier_bill_duty_details[0]["lucrative_users_id"]
            );

           // if (get_magic_quotes_gpc()) {
                $duty_details_id = addslashes(
                    $str_courier_bill_duty_details["duty_details_id"]
                );
                $bcd_duty_head = addslashes(
                    $str_courier_bill_duty_details["bcd_duty_head"]
                );
                $bcd_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["bcd_ad_valorem"]
                );
                $bcd_specific_rate = addslashes(
                    $str_courier_bill_duty_details["bcd_specific_rate"]
                );
                $bcd_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["bcd_duty_forgone"]
                );
                $sw_srchrg_duty_head = addslashes(
                    $str_courier_bill_duty_details["sw_srchrg_duty_head"]
                );
                $sw_srchrg_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["sw_srchrg_ad_valorem"]
                );
                $sw_srchrg_specific_rate = addslashes(
                    $str_courier_bill_duty_details["sw_srchrg_specific_rate"]
                );
                $sw_srchrg_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["sw_srchrg_duty_forgone"]
                );
                $sw_srchrg_duty_amount = addslashes(
                    $str_courier_bill_duty_details["sw_srchrg_duty_amount"]
                );
                $igst_duty_head = addslashes(
                    $str_courier_bill_duty_details["igst_duty_head"]
                );
                $igst_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["igst_ad_valorem"]
                );
                $igst_specific_rate = addslashes(
                    $str_courier_bill_duty_details["igst_specific_rate"]
                );

                $igst_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["igst_duty_forgone"]
                );
                $igst_duty_amount = addslashes(
                    $str_courier_bill_duty_details["igst_duty_amount"]
                );
                $cmpnstry_duty_head = addslashes(
                    $str_courier_bill_duty_details["cmpnstry_duty_head"]
                );
                $cmpnstry_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["cmpnstry_ad_valorem"]
                );
                $cmpnstry_specific_rate = addslashes(
                    $str_courier_bill_duty_details["cmpnstry_specific_rate"]
                );
                $cmpnstry_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["cmpnstry_duty_forgone"]
                );
                $cmpnstry_duty_amount = addslashes(
                    $str_courier_bill_duty_details["cmpnstry_duty_amount"]
                );
                $dummy5_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy5_duty_head"]
                );
                $dummy5_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy5_ad_valorem"]
                );

                $dummy5_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy5_specific_rate"]
                );
                $dummy5_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy5_duty_forgone"]
                );
                $dummy5_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy5_duty_amount"]
                );
                $dummy6_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy6_duty_head"]
                );
                $dummy6_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy6_ad_valorem"]
                );
                $dummy6_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy6_specific_rate"]
                );
                $dummy6_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy6_duty_forgone"]
                );
                $dummy6_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy6_duty_amount"]
                );
                $dummy7_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy7_duty_head"]
                );

                $dummy7_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy7_ad_valorem"]
                );
                $dummy7_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy7_specific_rate"]
                );
                $dummy7_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy7_duty_forgone"]
                );
                $dummy7_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy7_duty_amount"]
                );
                $dummy8_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy8_duty_head"]
                );
                $dummy8_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy8_ad_valorem"]
                );
                $dummy8_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy8_specific_rate"]
                );
                $dummy8_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy8_duty_forgone"]
                );
                $dummy8_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy8_duty_amount"]
                );

                $dummy9_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy9_duty_head"]
                );
                $dummy9_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy9_ad_valorem"]
                );
                $dummy9_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy9_specific_rate"]
                );
                $dummy9_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy9_duty_forgone"]
                );
                $dummy9_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy9_duty_amount"]
                );
                $dummy10_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy10_duty_head"]
                );
                $dummy10_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy10_ad_valorem"]
                );
                $dummy10_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy10_specific_rate"]
                );
                $dummy10_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy10_duty_forgone"]
                );

                $dummy10_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy10_duty_amount"]
                );
                $dummy11_duty_head = addslashes(
                    $str_courier_bill_duty_details["dummy11_duty_head"]
                );
                $dummy11_ad_valorem = addslashes(
                    $str_courier_bill_duty_details["dummy11_ad_valorem"]
                );
                $dummy11_specific_rate = addslashes(
                    $str_courier_bill_duty_details["dummy11_specific_rate"]
                );
                $dummy11_duty_forgone = addslashes(
                    $str_courier_bill_duty_details["dummy11_duty_forgone"]
                );
                $dummy11_duty_amount = addslashes(
                    $str_courier_bill_duty_details["dummy11_duty_amount"]
                );
                $created_at = addslashes(
                    $str_courier_bill_duty_details["created_at"]
                );
           /* } else {
                $duty_details_id =
                    $str_courier_bill_duty_details["duty_details_id"];
                $bcd_duty_head =
                    $str_courier_bill_duty_details["bcd_duty_head"];
                $bcd_ad_valorem =
                    $str_courier_bill_duty_details["bcd_ad_valorem"];
                $bcd_specific_rate =
                    $str_courier_bill_duty_details["bcd_specific_rate"];
                $bcd_duty_forgone =
                    $str_courier_bill_duty_details["bcd_duty_forgone"];
                $sw_srchrg_duty_head =
                    $str_courier_bill_duty_details["sw_srchrg_duty_head"];
                $sw_srchrg_ad_valorem =
                    $str_courier_bill_duty_details["sw_srchrg_ad_valorem"];
                $sw_srchrg_specific_rate =
                    $str_courier_bill_duty_details["sw_srchrg_specific_rate"];
                $sw_srchrg_duty_forgone =
                    $str_courier_bill_duty_details["sw_srchrg_duty_forgone"];
                $sw_srchrg_duty_amount =
                    $str_courier_bill_duty_details["sw_srchrg_duty_amount"];
                $igst_duty_head =
                    $str_courier_bill_duty_details["igst_duty_head"];
                $igst_ad_valorem =
                    $str_courier_bill_duty_details["igst_ad_valorem"];
                $igst_specific_rate =
                    $str_courier_bill_duty_details["igst_specific_rate"];

                $igst_duty_forgone =
                    $str_courier_bill_duty_details["igst_duty_forgone"];
                $igst_duty_amount =
                    $str_courier_bill_duty_details["igst_duty_amount"];
                $cmpnstry_duty_head =
                    $str_courier_bill_duty_details["cmpnstry_duty_head"];
                $cmpnstry_ad_valorem =
                    $str_courier_bill_duty_details["cmpnstry_ad_valorem"];
                $cmpnstry_specific_rate =
                    $str_courier_bill_duty_details["cmpnstry_specific_rate"];
                $cmpnstry_duty_forgone =
                    $str_courier_bill_duty_details["cmpnstry_duty_forgone"];
                $cmpnstry_duty_amount =
                    $str_courier_bill_duty_details["cmpnstry_duty_amount"];
                $dummy5_duty_head =
                    $str_courier_bill_duty_details["dummy5_duty_head"];
                $dummy5_ad_valorem =
                    $str_courier_bill_duty_details["dummy5_ad_valorem"];

                $dummy5_specific_rate =
                    $str_courier_bill_duty_details["dummy5_specific_rate"];
                $dummy5_duty_forgone =
                    $str_courier_bill_duty_details["dummy5_duty_forgone"];
                $dummy5_duty_amount =
                    $str_courier_bill_duty_details["dummy5_duty_amount"];
                $dummy6_duty_head =
                    $str_courier_bill_duty_details["dummy6_duty_head"];
                $dummy6_ad_valorem =
                    $str_courier_bill_duty_details["dummy6_ad_valorem"];
                $dummy6_specific_rate =
                    $str_courier_bill_duty_details["dummy6_specific_rate"];
                $dummy6_duty_forgone =
                    $str_courier_bill_duty_details["dummy6_duty_forgone"];
                $dummy6_duty_amount =
                    $str_courier_bill_duty_details["dummy6_duty_amount"];
                $dummy7_duty_head =
                    $str_courier_bill_duty_details["dummy7_duty_head"];

                $dummy7_ad_valorem =
                    $str_courier_bill_duty_details["dummy7_ad_valorem"];
                $dummy7_specific_rate =
                    $str_courier_bill_duty_details["dummy7_specific_rate"];
                $dummy7_duty_forgone =
                    $str_courier_bill_duty_details["dummy7_duty_forgone"];
                $dummy7_duty_amount =
                    $str_courier_bill_duty_details["dummy7_duty_amount"];
                $dummy8_duty_head =
                    $str_courier_bill_duty_details["dummy8_duty_head"];
                $dummy8_ad_valorem =
                    $str_courier_bill_duty_details["dummy8_ad_valorem"];
                $dummy8_specific_rate =
                    $str_courier_bill_duty_details["dummy8_specific_rate"];
                $dummy8_duty_forgone =
                    $str_courier_bill_duty_details["dummy8_duty_forgone"];
                $dummy8_duty_amount =
                    $str_courier_bill_duty_details["dummy8_duty_amount"];

                $dummy9_duty_head =
                    $str_courier_bill_duty_details["dummy9_duty_head"];
                $dummy9_ad_valorem =
                    $str_courier_bill_duty_details["dummy9_ad_valorem"];
                $dummy9_specific_rate =
                    $str_courier_bill_duty_details["dummy9_specific_rate"];
                $dummy9_duty_forgone =
                    $str_courier_bill_duty_details["dummy9_duty_forgone"];
                $dummy9_duty_amount =
                    $str_courier_bill_duty_details["dummy9_duty_amount"];
                $dummy10_duty_head =
                    $str_courier_bill_duty_details["dummy10_duty_head"];
                $dummy10_ad_valorem =
                    $str_courier_bill_duty_details["dummy10_ad_valorem"];
                $dummy10_specific_rate =
                    $str_courier_bill_duty_details["dummy10_specific_rate"];
                $dummy10_duty_forgone =
                    $str_courier_bill_duty_details["dummy10_duty_forgone"];

                $dummy10_duty_amount =
                    $str_courier_bill_duty_details["dummy10_duty_amount"];
                $dummy11_duty_head =
                    $str_courier_bill_duty_details["dummy11_duty_head"];
                $dummy11_ad_valorem =
                    $str_courier_bill_duty_details["dummy11_ad_valorem"];
                $dummy11_specific_rate =
                    $str_courier_bill_duty_details["dummy11_specific_rate"];
                $dummy11_duty_forgone =
                    $str_courier_bill_duty_details["dummy11_duty_forgone"];
                $dummy11_duty_amount =
                    $str_courier_bill_duty_details["dummy11_duty_amount"];
                $created_at = $str_courier_bill_duty_details["created_at"];
            }*/
            
            //if(){}
            echo $sql_insert_courier_bill_duty_details =
                "INSERT INTO `courier_bill_duty_details`( `duty_details_id`, `bcd_duty_head`, `bcd_ad_valorem`, `bcd_specific_rate`, `bcd_duty_forgone`, `bcd_duty_amount`, `aidc_duty_head`, `aidc_ad_valorem`, `aidc_specific_rate`, `aidc_duty_forgone`, `aidc_duty_amount`, `sw_srchrg_duty_head`, `sw_srchrg_ad_valorem`, `sw_srchrg_specific_rate`, `sw_srchrg_duty_forgone`, `sw_srchrg_duty_amount`, `igst_duty_head`, `igst_ad_valorem`, `igst_specific_rate`, `igst_duty_forgone`, `igst_duty_amount`, `cmpnstry_duty_head`, `cmpnstry_ad_valorem`, `cmpnstry_specific_rate`, `cmpnstry_duty_forgone`, `cmpnstry_duty_amount`, `dummy5_duty_head`, `dummy5_ad_valorem`, `dummy5_specific_rate`, `dummy5_duty_forgone`, `dummy5_duty_amount`, `dummy6_duty_head`, `dummy6_ad_valorem`, `dummy6_specific_rate`, `dummy6_duty_forgone`, `dummy6_duty_amount`, `dummy7_duty_head`, `dummy7_ad_valorem`, `dummy7_specific_rate`, `dummy7_duty_forgone`, `dummy7_duty_amount`, `dummy8_duty_head`, `dummy8_ad_valorem`, `dummy8_specific_rate`, `dummy8_duty_forgone`, `dummy8_duty_amount`, `dummy9_duty_head`, `dummy9_ad_valorem`, `dummy9_specific_rate`, `dummy9_duty_forgone`, `dummy9_duty_amount`, `dummy10_duty_head`, `dummy10_ad_valorem`, `dummy10_specific_rate`, `dummy10_duty_forgone`, `dummy10_duty_amount`, `dummy11_duty_head`, `dummy11_ad_valorem`, `dummy11_specific_rate`, `dummy11_duty_forgone`, `dummy11_duty_amount`, `created_at`)
 VALUES ('" .
                $duty_details_id .
                "',
 '" .
                $bcd_duty_head .
                "',
 '" .
                $bcd_ad_valorem .
                "',
 '" .
                $bcd_specific_rate .
                "',
 '" .
                $bcd_duty_forgone .
                "',
 '" .
                $bcd_duty_amount .
                "',
 '" .
                $sw_srchrg_duty_head .
                "',
 '" .
                $aidc_ad_valorem .
                "',
 '" .
                $bcd_specific_rate .
                "',
 '" .
                $aidc_duty_forgone .
                "',
'" .
                $aidc_duty_amount .
                "',
'" .
                $sw_srchrg_duty_head .
                "',
'" .
                $sw_srchrg_ad_valorem .
                "',
'" .
                $sw_srchrg_specific_rate .
                "',
'" .
                $sw_srchrg_duty_forgone .
                "',
'" .
                $sw_srchrg_duty_amount .
                "',
'" .
                $igst_duty_head .
                "',
'" .
                $igst_ad_valorem .
                "',
'" .
                $igst_specific_rate .
                "',
'" .
                $igst_duty_forgone .
                "',
'" .
                $igst_duty_amount .
                "',
'" .
                $cmpnstry_duty_head .
                "',
'" .
                $cmpnstry_ad_valorem .
                "',
'" .
                $cmpnstry_specific_rate .
                "',
'" .
                $cmpnstry_duty_forgone .
                "',
'" .
                $cmpnstry_duty_amount .
                "',
'" .
                $dummy5_duty_head .
                "',
'" .
                $dummy5_ad_valorem .
                "',
'" .
                $dummy5_specific_rate .
                "',
'" .
                $dummy5_duty_forgone .
                "',
'" .
                $dummy5_duty_amount .
                "',
'" .
                $dummy6_duty_head .
                "',
'" .
                $dummy6_ad_valorem .
                "',
'" .
                $dummy6_specific_rate .
                "',
'" .
                $dummy6_duty_forgone .
                "',
'" .
                $dummy6_duty_amount .
                "',
'" .
                $dummy7_duty_head .
                "',
'" .
                $dummy7_ad_valorem .
                "',
'" .
                $dummy7_specific_rate .
                "',
'" .
                $dummy7_duty_forgone .
                "',
'" .
                $dummy7_duty_amount .
                "',
'" .
                $dummy8_duty_head .
                "',
'" .
                $dummy8_ad_valorem .
                "',
'" .
                $dummy8_specific_rate .
                "',
'" .
                $dummy8_duty_forgone .
                "',
'" .
                $dummy8_duty_amount .
                "',
'" .
                $dummy9_duty_head .
                "',
'" .
                $dummy9_ad_valorem .
                "',
'" .
                $dummy9_specific_rate .
                "',
'" .
                $dummy9_duty_forgone .
                "',
'" .
                $dummy9_duty_amount .
                "',
'" .
                $dummy10_duty_head .
                "',
'" .
                $dummy10_ad_valorem .
                "',
'" .
                $dummy10_specific_rate .
                "',
'" .
                $dummy10_duty_forgone .
                "',
'" .
                $dummy10_duty_amount .
                "',
'" .
                $dummy11_duty_head .
                "',
'" .
                $dummy11_ad_valorem .
                "',
'" .
                $dummy11_specific_rate .
                "',
'" .
                $dummy11_duty_forgone .
                "',
'" .
                $dummy11_duty_amount .
                "',
'" .
                $created_at .
                "')";//exit;
            $copy_insert_courier_bill_duty_details = $db1_courier_bill_duty_details->query(
                $sql_insert_courier_bill_duty_details
            );
        }

        /******************************************************************End bill_of_entry_summary***************************************************************************************/
    }

public function courier_bill_igm_details(){
        /******************************************************************Start courier_bill_container_details***************************************************************************************/

        echo $query_courier_bill_igm_details =
            "SELECT courier_bill_igm_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_igm_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_igm_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id";
        $statement_courier_bill_igm_details = $this->db->query(
            $query_courier_bill_igm_details
        );
        $iecwise_courier_bill_igm_details = [];
        $result_courier_bill_igm_details = $statement_courier_bill_igm_details->result_array();
        //print_r($result_courier_bill_igm_details);exit;

        foreach (
            $result_courier_bill_igm_details
            as $str_courier_bill_igm_details
        ) {
            $iec_courier_bill_igm_details =
                $str_courier_bill_igm_details["user_iec_no"];
            $sql_courier_bill_igm_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_igm_details'";
            $iecwise_courier_bill_igm_details = $this->db->query(
                $sql_courier_bill_igm_details
            );
            $iecwise_data_courier_bill_igm_details = $iecwise_courier_bill_igm_details->result_array();
            $db1_courier_bill_igm_details = $this->database_connection(
                $iecwise_data_courier_bill_igm_details[0]["lucrative_users_id"]
            );
        //    if (get_magic_quotes_gpc()) {
                $igm_details_id = addslashes(
                    $str_courier_bill_igm_details["igm_details_id"]
                );
                $airlines = addslashes(
                    $str_courier_bill_igm_details["airlines"]
                );
                $flight_no = addslashes(
                    $str_courier_bill_igm_details["flight_no"]
                );
                $airport_of_arrival = addslashes(
                    $str_courier_bill_igm_details["airport_of_arrival"]
                );
                $date_of_arrival = addslashes(
                    $str_courier_bill_igm_details["date_of_arrival"]
                );
                $created_at = addslashes(
                    $str_courier_bill_igm_details["created_at"]
                );
        /*    } else {
                $igm_details_id =
                    $str_courier_bill_container_details["igm_details_id"];
                $airlines = $str_courier_bill_container_details["airlines"];
                $flight_no = $str_courier_bill_container_details["flight_no"];
                $airport_of_arrival =
                    $str_courier_bill_container_details["airport_of_arrival"];
                $date_of_arrival =
                    $str_courier_bill_container_details["date_of_arrival"];
                $created_at = $str_courier_bill_container_details["created_at"];
            }*/
         echo   $sql_insert_courier_bill_igm_details =
                "INSERT INTO `courier_bill_igm_details` (`igm_details_id`, `airlines`, `flight_no`, `airport_of_arrival`, `date_of_arrival`, `created_at`) 
VALUES('" .
                $igm_details_id .
                "','" .
                $airlines .
                "','" .
                $flight_no .
                "','" .
                $airport_of_arrival .
                "','" .
                $date_of_arrival .
                "','" .
                $created_at .
                "')";
            $copy_insert_courier_bill_igm_details = $db1_courier_bill_igm_details->query(
                $sql_insert_courier_bill_igm_details
            );
        }

        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }

public function courier_bill_invoice_details(){
        /******************************************************************Start courier_bill_container_details***************************************************************************************/

        $query_courier_bill_invoice_details =
            "SELECT courier_bill_invoice_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no FROM courier_bill_invoice_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_invoice_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id";
        $statement_courier_bill_invoice_details = $this->db->query(
            $query_courier_bill_invoice_details
        );

        $iecwise_courier_bill_invoice_details = [];
        $result_courier_bill_invoice_details = $statement_courier_bill_invoice_details->result_array();
      //  print_r($result_courier_bill_invoice_details);exit;

        foreach (
            $result_courier_bill_invoice_details
            as $str_courier_bill_invoice_details
        ) {
            $iec_courier_bill_invoice_details =
                $str_courier_bill_invoice_details["user_iec_no"];
            $sql_courier_bill_invoice_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_invoice_details'";
            $iecwise_courier_bill_invoice_details = $this->db->query(
                $sql_courier_bill_invoice_details
            );
            $iecwise_data_courier_bill_invoice_details = $iecwise_courier_bill_invoice_details->result_array();
            $db1_courier_bill_invoice_details = $this->database_connection(
                $iecwise_data_courier_bill_invoice_details[0][
                    "lucrative_users_id"
                ]
            );
            //if (get_magic_quotes_gpc()) {
                $invoice_detail_id = addslashes(
                    $str_courier_bill_invoice_details["invoice_detail_id"]
                );
                $invoice_number = addslashes(
                    $str_courier_bill_invoice_details["invoice_number"]
                );
                $date_of_invoice = addslashes(
                    $str_courier_bill_invoice_details["date_of_invoice"]
                );
                $purchase_order_number = addslashes(
                    $str_courier_bill_invoice_details["purchase_order_number"]
                );
                $date_of_purchase_order = addslashes(
                    $str_courier_bill_invoice_details["date_of_purchase_order"]
                );
                $contract_number = addslashes(
                    $str_courier_bill_invoice_details["contract_number"]
                );
                $date_of_contract = addslashes(
                    $str_courier_bill_invoice_details["date_of_contract"]
                );
                $letter_of_credit = addslashes(
                    $str_courier_bill_invoice_details["letter_of_credit"]
                );
                $date_of_letter_of_credit = addslashes(
                    $str_courier_bill_invoice_details[
                        "date_of_letter_of_credit"
                    ]
                );
                $supplier_details_name = addslashes(
                    $str_courier_bill_invoice_details["supplier_details_name"]
                );
                $supplier_details_address = addslashes(
                    $str_courier_bill_invoice_details[
                        "supplier_details_address"
                    ]
                );
                $if_supplier_is_not_the_seller_name = addslashes(
                    $str_courier_bill_invoice_details[
                        "if_supplier_is_not_the_seller_name"
                    ]
                );
                $if_supplier_is_not_the_seller_address = addslashes(
                    $str_courier_bill_invoice_details[
                        "if_supplier_is_not_the_seller_address"
                    ]
                );
                $broker_agent_details_name = addslashes(
                    $str_courier_bill_invoice_details[
                        "broker_agent_details_name"
                    ]
                );
                $broker_agent_details_address = addslashes(
                    $str_courier_bill_invoice_details[
                        "broker_agent_details_address"
                    ]
                );
                $nature_of_transaction = addslashes(
                    $str_courier_bill_invoice_details["nature_of_transaction"]
                );
                $if_others = addslashes(
                    $str_courier_bill_invoice_details["if_others"]
                );
                $terms_of_payment = addslashes(
                    $str_courier_bill_invoice_details["terms_of_payment"]
                );
                $conditions_or_restrictions_if_any_attached_to_sale = addslashes(
                    $str_courier_bill_invoice_details[
                        "conditions_or_restrictions_if_any_attached_to_sale"
                    ]
                );
                $method_of_valuation = addslashes(
                    $str_courier_bill_invoice_details["method_of_valuation"]
                );
                $terms_of_invoice = addslashes(
                    $str_courier_bill_invoice_details["terms_of_invoice"]
                );
                $invoice_value = addslashes(
                    $str_courier_bill_invoice_details["invoice_value"]
                );
                $currency = addslashes(
                    $str_courier_bill_invoice_details["currency"]
                );
                $freight_rate = addslashes(
                    $str_courier_bill_invoice_details["freight_rate"]
                );
                $freight_amount = addslashes(
                    $str_courier_bill_invoice_details["freight_amount"]
                );
                $freight_currency = addslashes(
                    $str_courier_bill_invoice_details["freight_currency"]
                );
                $insurance_rate = addslashes(
                    $str_courier_bill_invoice_details["insurance_rate"]
                );
                $insurance_amount = addslashes(
                    $str_courier_bill_invoice_details["insurance_amount"]
                );
                $insurance_currency = addslashes(
                    $str_courier_bill_invoice_details["insurance_currency"]
                );
                $loading_unloading_and_handling_charges_rule_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "loading_unloading_and_handling_charges_rule_rate"
                    ]
                );
                $loading_unloading_and_handling_charges_rule_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "loading_unloading_and_handling_charges_rule_amount"
                    ]
                );
                $loading_unloading_and_handling_charges_rule_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "loading_unloading_and_handling_charges_rule_currency"
                    ]
                );
                $other_charges_related_to_the_carriage_of_goods_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_charges_related_to_the_carriage_of_goods_rate"
                    ]
                );
                $other_charges_related_to_the_carriage_of_goods_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_charges_related_to_the_carriage_of_goods_amount"
                    ]
                );
                $other_charges_related_to_the_carriage_of_goods_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_charges_related_to_the_carriage_of_goods_currency"
                    ]
                );
                $brokerage_and_commission_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "brokerage_and_commission_rate"
                    ]
                );
                $brokerage_and_commission_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "brokerage_and_commission_amount"
                    ]
                );
                $brokerage_and_commission_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "brokerage_and_commission_currency"
                    ]
                );
                $cost_of_containers_rate = addslashes(
                    $str_courier_bill_invoice_details["cost_of_containers_rate"]
                );
                $cost_of_containers_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_of_containers_amount"
                    ]
                );
                $cost_of_containers_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_of_containers_currency"
                    ]
                );
                $cost_of_packing_rate = addslashes(
                    $str_courier_bill_invoice_details["cost_of_packing_rate"]
                );
                $cost_of_packing_amount = addslashes(
                    $str_courier_bill_invoice_details["cost_of_packing_amount"]
                );
                $cost_of_packing_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_of_packing_currency"
                    ]
                );
                $dismantling_transport_handling_in_country_export_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "dismantling_transport_handling_in_country_export_rate"
                    ]
                );
                $dismantling_transport_handling_in_country_export_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "dismantling_transport_handling_in_country_export_amount"
                    ]
                );
                $dismantling_transport_handling_in_country_export_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "dismantling_transport_handling_in_country_export_currency"
                    ]
                );
                $cost_of_goods_and_ser_vices_supplied_by_buyer_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_of_goods_and_ser_vices_supplied_by_buyer_rate"
                    ]
                );
                $cost_of_goods_and_ser_vices_supplied_by_buyer_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_of_goods_and_ser_vices_supplied_by_buyer_amount"
                    ]
                );
                $cost_of_goods_and_ser_vices_supplied_by_buyer_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_of_goods_and_ser_vices_supplied_by_buyer_currency"
                    ]
                );
                $documentation_rate = addslashes(
                    $str_courier_bill_invoice_details["documentation_rate"]
                );
                $documentation_amount = addslashes(
                    $str_courier_bill_invoice_details["documentation_amount"]
                );
                $documentation_currency = addslashes(
                    $str_courier_bill_invoice_details["documentation_currency"]
                );
                $country_of_origin_certificate_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "country_of_origin_certificate_rate"
                    ]
                );
                $country_of_origin_certificate_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "country_of_origin_certificate_amount"
                    ]
                );
                $country_of_origin_certificate_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "country_of_origin_certificate_currency"
                    ]
                );
                $royalty_and_license_fees_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "royalty_and_license_fees_rate"
                    ]
                );
                $royalty_and_license_fees_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "royalty_and_license_fees_amount"
                    ]
                );
                $royalty_and_license_fees_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "royalty_and_license_fees_currency"
                    ]
                );
                $value_of_proceeds_which_accrue_to_seller_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "value_of_proceeds_which_accrue_to_seller_rate"
                    ]
                );
                $value_of_proceeds_which_accrue_to_seller_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "value_of_proceeds_which_accrue_to_seller_amount"
                    ]
                );
                $value_of_proceeds_which_accrue_to_seller_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "value_of_proceeds_which_accrue_to_seller_currency"
                    ]
                );
                $cost_warranty_service_if_any_provided_seller_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_warranty_service_if_any_provided_seller_rate"
                    ]
                );
                $cost_warranty_service_if_any_provided_seller_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_warranty_service_if_any_provided_seller_amount"
                    ]
                );
                $cost_warranty_service_if_any_provided_seller_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "cost_warranty_service_if_any_provided_seller_currency"
                    ]
                );
                $other_payments_satisfy_obligation_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_payments_satisfy_obligation_rate"
                    ]
                );
                $other_payments_satisfy_obligation_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_payments_satisfy_obligation_amount"
                    ]
                );
                $other_payments_satisfy_obligation_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_payments_satisfy_obligation_currency"
                    ]
                );
                $other_charges_and_payments_if_any_rate = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_charges_and_payments_if_any_rate"
                    ]
                );
                $other_charges_and_payments_if_any_amount = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_charges_and_payments_if_any_amount"
                    ]
                );
                $other_charges_and_payments_if_any_currency = addslashes(
                    $str_courier_bill_invoice_details[
                        "other_charges_and_payments_if_any_currency"
                    ]
                );
                $discount_amount = addslashes(
                    $str_courier_bill_invoice_details["discount_amount"]
                );
                $discount_currency = addslashes(
                    $str_courier_bill_invoice_details["discount_currency"]
                );
                $rate = addslashes($str_courier_bill_invoice_details["rate"]);
                $amount = addslashes(
                    $str_courier_bill_invoice_details["amount"]
                );
                $any_other_information_which_has_a_bearing_on_value = addslashes(
                    $str_courier_bill_invoice_details[
                        "any_other_information_which_has_a_bearing_on_value"
                    ]
                );
                $are_the_buyer_and_seller_related = addslashes(
                    $str_courier_bill_invoice_details[
                        "are_the_buyer_and_seller_related"
                    ]
                );
                $if_the_buyer_seller_has_the_relationship_examined_earlier_svb = addslashes(
                    $str_courier_bill_invoice_details[
                        "if_the_buyer_seller_has_the_relationship_examined_earlier_svb"
                    ]
                );
                $svb_reference_number = addslashes(
                    $str_courier_bill_invoice_details["svb_reference_number"]
                );
                $svb_date = addslashes(
                    $str_courier_bill_invoice_details["svb_date"]
                );
                $indication_for_provisional_final = addslashes(
                    $str_courier_bill_invoice_details[
                        "indication_for_provisional_final"
                    ]
                );
                $created_at = addslashes(
                    $str_courier_bill_invoice_details["created_at"]
                );
      
       echo     $sql_insert_courier_bill_invoice_details =
                "INSERT INTO `courier_bill_invoice_details` 
(`invoice_detail_id`, `invoice_number`, `date_of_invoice`, `purchase_order_number`, `date_of_purchase_order`, `contract_number`, `date_of_contract`, `letter_of_credit`, `date_of_letter_of_credit`, `supplier_details_name`, `supplier_details_address`, `if_supplier_is_not_the_seller_name`, `if_supplier_is_not_the_seller_address`, `broker_agent_details_name`, `broker_agent_details_address`, `nature_of_transaction`, `if_others`, `terms_of_payment`, `conditions_or_restrictions_if_any_attached_to_sale`, `method_of_valuation`, `terms_of_invoice`, `invoice_value`, `currency`, `freight_rate`, `freight_amount`, `freight_currency`, `insurance_rate`, `insurance_amount`, `insurance_currency`, `loading_unloading_and_handling_charges_rule_rate`, `loading_unloading_and_handling_charges_rule_amount`, `loading_unloading_and_handling_charges_rule_currency`, `other_charges_related_to_the_carriage_of_goods_rate`, `other_charges_related_to_the_carriage_of_goods_amount`, `other_charges_related_to_the_carriage_of_goods_currency`, `brokerage_and_commission_rate`, `brokerage_and_commission_amount`, `brokerage_and_commission_currency`, `cost_of_containers_rate`, `cost_of_containers_amount`, `cost_of_containers_currency`, `cost_of_packing_rate`, `cost_of_packing_amount`, `cost_of_packing_currency`, `dismantling_transport_handling_in_country_export_rate`, `dismantling_transport_handling_in_country_export_amount`, `dismantling_transport_handling_in_country_export_currency`, `cost_of_goods_and_ser_vices_supplied_by_buyer_rate`, `cost_of_goods_and_ser_vices_supplied_by_buyer_amount`, `cost_of_goods_and_ser_vices_supplied_by_buyer_currency`, `documentation_rate`, `documentation_amount`, `documentation_currency`, `country_of_origin_certificate_rate`, `country_of_origin_certificate_amount`, `country_of_origin_certificate_currency`, `royalty_and_license_fees_rate`, `royalty_and_license_fees_amount`, `royalty_and_license_fees_currency`, `value_of_proceeds_which_accrue_to_seller_rate`, `value_of_proceeds_which_accrue_to_seller_amount`, `value_of_proceeds_which_accrue_to_seller_currency`, `cost_warranty_service_if_any_provided_seller_rate`, `cost_warranty_service_if_any_provided_seller_amount`, `cost_warranty_service_if_any_provided_seller_currency`, `other_payments_satisfy_obligation_rate`, `other_payments_satisfy_obligation_amount`, `other_payments_satisfy_obligation_currency`, `other_charges_and_payments_if_any_rate`, `other_charges_and_payments_if_any_amount`, `other_charges_and_payments_if_any_currency`, `discount_amount`, `discount_currency`, `rate`, `amount`, `any_other_information_which_has_a_bearing_on_value`, `are_the_buyer_and_seller_related`, `if_the_buyer_seller_has_the_relationship_examined_earlier_svb`, `svb_reference_number`, `svb_date`, `indication_for_provisional_final`, `created_at`) 
VALUES('" .
                $invoice_detail_id .
                "',
'" .
                $invoice_number .
                "',
'" .
                $date_of_invoice .
                "',
'" .
                $purchase_order_number .
                "',
'" .
                $date_of_purchase_order .
                "',
'" .
                $contract_number .
                "',
'" .
                $date_of_contract .
                "',
'" .
                $letter_of_credit .
                "';,
'" .
                $date_of_letter_of_credit .
                "',
'" .
                $supplier_details_name .
                ",
'" .
                $supplier_details_address .
                "',
'" .
                $if_supplier_is_not_the_seller_name .
                "',
'" .
                $if_supplier_is_not_the_seller_address .
                "',
'" .
                $royalty_and_license_fees_amount .
                "',
'" .
                $broker_agent_details_address .
                "',
'" .
                $nature_of_transaction .
                "',
'" .
                $if_others .
                "',
'" .
                $terms_of_payment .
                "',
'" .
                $conditions_or_restrictions_if_any_attached_to_sale .
                "',
'" .
                $method_of_valuation .
                "',
'" .
                $terms_of_invoice .
                "', 
'" .
                $invoice_value .
                "',
'" .
                $currency .
                "',
'" .
                $freight_rate .
                "',
'" .
                $freight_amount .
                ",
'" .
                $freight_currency .
                "', 
'" .
                $insurance_rate .
                "',
'" .
                $insurance_amount .
                "',
'" .
                $insurance_currency .
                "',
'" .
                $loading_unloading_and_handling_charges_rule_rate .
                "',
'" .
                $loading_unloading_and_handling_charges_rule_amount .
                "',
'" .
                $loading_unloading_and_handling_charges_rule_currency .
                "',
'" .
                $other_charges_related_to_the_carriage_of_goods_rate .
                "',
'" .
                $other_charges_related_to_the_carriage_of_goods_amount .
                "',
'" .
                $other_charges_related_to_the_carriage_of_goods_currency .
                "',
'" .
                $brokerage_and_commission_rate .
                "',
'" .
                $brokerage_and_commission_amount .
                "',
'" .
                $brokerage_and_commission_currency .
                "',
'" .
                $cost_of_containers_rate .
                "',
'" .
                $cost_of_containers_amount .
                "',
'" .
                $cost_of_containers_currency .
                "',
'" .
                $cost_of_packing_rate .
                "',
'" .
                $cost_of_packing_amount .
                "',
'" .
                $cost_of_packing_currency .
                "',
'" .
                $dismantling_transport_handling_in_country_export_rate .
                "',
'" .
                $dismantling_transport_handling_in_country_export_amount .
                "', 
'" .
                $dismantling_transport_handling_in_country_export_currency .
                "',
'" .
                $cost_of_goods_and_ser_vices_supplied_by_buyer_rate .
                "', 
'" .
                $cost_of_goods_and_ser_vices_supplied_by_buyer_amount .
                "',
'" .
                $cost_of_goods_and_ser_vices_supplied_by_buyer_currency .
                "',
'" .
                $documentation_rate .
                "',
'" .
                $documentation_amount .
                "',
'" .
                $documentation_currency .
                "',
'" .
                $country_of_origin_certificate_rate .
                "',
'" .
                $country_of_origin_certificate_amount .
                "', 
'" .
                $country_of_origin_certificate_currency .
                "',
'" .
                $royalty_and_license_fees_rate .
                "',
'" .
                $royalty_and_license_fees_amount .
                "',
'" .
                $royalty_and_license_fees_currency .
                "',
'" .
                $value_of_proceeds_which_accrue_to_seller_rate .
                "',
'" .
                $value_of_proceeds_which_accrue_to_seller_amount .
                "',
'" .
                $value_of_proceeds_which_accrue_to_seller_currency .
                "',
'" .
                $cost_warranty_service_if_any_provided_seller_rate .
                "',
'" .
                $cost_warranty_service_if_any_provided_seller_amount .
                "',
'" .
                $cost_warranty_service_if_any_provided_seller_currency .
                "',
'" .
                $other_payments_satisfy_obligation_rate .
                "',
'" .
                $other_payments_satisfy_obligation_amount .
                "',
'" .
                $other_payments_satisfy_obligation_currency .
                "',
'" .
                $other_charges_and_payments_if_any_rate .
                "',
'" .
                $other_charges_and_payments_if_any_amount .
                "',
'" .
                $other_charges_and_payments_if_any_currency .
                "',
'" .
                $discount_amount .
                ",
'" .
                $discount_currency .
                "', 
'" .
                $rate .
                "',
'" .
                $amount .
                "',
'" .
                $any_other_information_which_has_a_bearing_on_value .
                "',
'" .
                $are_the_buyer_and_seller_related .
                "',
'" .
                $if_the_buyer_seller_has_the_relationship_examined_earlier_svb .
                "', 
'" .
                $svb_reference_number .
                "',
'" .
                $svb_date .
                "',
'" .
                $indication_for_provisional_final .
                "',
'" .
                $created_at .
                "')";//exit;
            $copy_insert_courier_bill_invoice_details = $db1_courier_bill_invoice_details->query(
                $sql_insert_courier_bill_invoice_details
            );
        }

        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }
public function courier_bill_items_details(){
        /******************************************************************Start courier_bill_items_details***************************************************************************************/

        $query_courier_bill_items_details =
            "SELECT courier_bill_items_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no FROM courier_bill_items_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id  ";
        $statement_courier_bill_items_details = $this->db->query(
            $query_courier_bill_items_details
        );
        $iecwise_courier_bill_items_details = [];
        $result_courier_bill_items_details = $statement_courier_bill_items_details->result_array();
       // print_r($result_courier_bill_items_details);exit;

        foreach (
            $result_courier_bill_items_details
            as $str_courier_bill_items_details
        ) {
            $iec_courier_bill_items_details =
                $str_courier_bill_items_details["user_iec_no"];
            $sql_courier_bill_items_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_items_details'";
            $iecwise_courier_bill_items_details = $this->db->query(
                $sql_courier_bill_items_details
            );
            $iecwise_data_courier_bill_items_details = $iecwise_courier_bill_items_details->result_array();
            $db1_courier_bill_items_details = $this->database_connection(
                $iecwise_data_courier_bill_items_details[0][
                    "lucrative_users_id"
                ]
            );

            //if (get_magic_quotes_gpc()) {
                $items_detail_id = addslashes(
                    $str_courier_bill_items_details["items_detail_id"]
                );
                $case_for_reimport = addslashes(
                    $str_courier_bill_items_details["case_for_reimport"]
                );
                $import_against_license = addslashes(
                    $str_courier_bill_items_details["import_against_license"]
                );
                $serial_number_in_invoice = addslashes(
                    $str_courier_bill_items_details["serial_number_in_invoice"]
                );
                $item_description = addslashes(
                    $str_courier_bill_items_details["item_description"]
                );
                $general_description = addslashes(
                    $str_courier_bill_items_details["general_description"]
                );
                $currency_for_unit_price = addslashes(
                    $str_courier_bill_items_details["currency_for_unit_price"]
                );
                $unit_price = addslashes(
                    $str_courier_bill_items_details["unit_price"]
                );
                $unit_of_measure = addslashes(
                    $str_courier_bill_items_details["unit_of_measure"]
                );
                $quantity = addslashes(
                    $str_courier_bill_items_details["quantity"]
                );
                $rate_of_exchange = addslashes(
                    $str_courier_bill_items_details["rate_of_exchange"]
                );
                $name_of_manufacturer = addslashes(
                    $str_courier_bill_items_details["name_of_manufacturer"]
                );
                $brand = addslashes($str_courier_bill_items_details["brand"]);
                $grade = addslashes($str_courier_bill_items_details["grade"]);
                $specification = addslashes(
                    $str_courier_bill_items_details["specification"]
                );
                $end_use_of_item = addslashes(
                    $str_courier_bill_items_details["end_use_of_item"]
                );
                $items_details_country_of_origin = addslashes(
                    $str_courier_bill_items_details[
                        "items_details_country_of_origin"
                    ]
                );
                $bill_of_entry_number = addslashes(
                    $str_courier_bill_items_details["bill_of_entry_number"]
                );
                $details_in_case_of_previous_imports_date = addslashes(
                    $str_courier_bill_items_details[
                        "details_in_case_of_previous_imports_date"
                    ]
                );
                $details_in_case_previous_imports_currency = addslashes(
                    $str_courier_bill_items_details[
                        "details_in_case_previous_imports_currency"
                    ]
                );
                $unit_value = addslashes(
                    $str_courier_bill_items_details["unit_value"]
                );
                $customs_house = addslashes(
                    $str_courier_bill_items_details["customs_house"]
                );
                $ritc = addslashes($str_courier_bill_items_details["ritc"]);
                $ctsh = addslashes($str_courier_bill_items_details["ctsh"]);
                $cetsh = addslashes($str_courier_bill_items_details["cetsh"]);
                $currency_for_rsp = addslashes(
                    $str_courier_bill_items_details["currency_for_rsp"]
                );
                $retail_sales_price_per_unit = addslashes(
                    $str_courier_bill_items_details[
                        "retail_sales_price_per_unit"
                    ]
                );
                $exim_scheme_code_if_any = addslashes(
                    $str_courier_bill_items_details["exim_scheme_code_if_any"]
                );
                $para_noyear_of_exim_policy = addslashes(
                    $str_courier_bill_items_details[
                        "para_noyear_of_exim_policy"
                    ]
                );
                $items_details_are_the_buyer_and_seller_related = addslashes(
                    $str_courier_bill_items_details[
                        "items_details_are_the_buyer_and_seller_related"
                    ]
                );
                $if_the_buyer_and_seller_relation_examined_earlier_by_svb = addslashes(
                    $str_courier_bill_items_details[
                        "if_the_buyer_and_seller_relation_examined_earlier_by_svb"
                    ]
                );
                $items_details_svb_reference_number = addslashes(
                    $str_courier_bill_items_details[
                        "items_details_svb_reference_number"
                    ]
                );
                $items_details_svb_date = addslashes(
                    $str_courier_bill_items_details["items_details_svb_date"]
                );
                $items_details_indication_for_provisional_final = addslashes(
                    $str_courier_bill_items_details[
                        "items_details_indication_for_provisional_final"
                    ]
                );
                $shipping_bill_number = addslashes(
                    $str_courier_bill_items_details["shipping_bill_number"]
                );
                $shipping_bill_date = addslashes(
                    $str_courier_bill_items_details["shipping_bill_date"]
                );
                $port_of_export = addslashes(
                    $str_courier_bill_items_details["port_of_export"]
                );
                $invoice_number_of_shipping_bill = addslashes(
                    $str_courier_bill_items_details[
                        "invoice_number_of_shipping_bill"
                    ]
                );
                $item_serial_number_in_shipping_bill = addslashes(
                    $str_courier_bill_items_details[
                        "item_serial_number_in_shipping_bill"
                    ]
                );
                $freight = addslashes(
                    $str_courier_bill_items_details["freight"]
                );
                $insurance = addslashes(
                    $str_courier_bill_items_details["insurance"]
                );
                $total_repair_cost_including_cost_of_materials = addslashes(
                    $str_courier_bill_items_details[
                        "total_repair_cost_including_cost_of_materials"
                    ]
                );
                $additional_duty_exemption_requested = addslashes(
                    $str_courier_bill_items_details[
                        "additional_duty_exemption_requested"
                    ]
                );
                $items_details_notification_number = addslashes(
                    $str_courier_bill_items_details[
                        "items_details_notification_number"
                    ]
                );
                $serial_number_in_notification = addslashes(
                    $str_courier_bill_items_details[
                        "serial_number_in_notification"
                    ]
                );
                $license_registration_number = addslashes(
                    $str_courier_bill_items_details[
                        "license_registration_number"
                    ]
                );
                $license_registration_date = addslashes(
                    $str_courier_bill_items_details["license_registration_date"]
                );
                $debit_value_rs = addslashes(
                    $str_courier_bill_items_details["debit_value_rs"]
                );
                $unit_of_measure_for_quantity_to_be_debited = addslashes(
                    $str_courier_bill_items_details[
                        "unit_of_measure_for_quantity_to_be_debited"
                    ]
                );
                $debit_quantity = addslashes(
                    $str_courier_bill_items_details["debit_quantity"]
                );
                $item_serial_number_in_license = addslashes(
                    $str_courier_bill_items_details[
                        "item_serial_number_in_license"
                    ]
                );
                $assessable_value = addslashes(
                    $str_courier_bill_items_details["assessable_value"]
                );
                $created_at = addslashes(
                    $str_courier_bill_items_details["created_at"]
                );
           /* } else {
                $items_detail_id =
                    $str_courier_bill_items_details["items_detail_id"];
                $case_for_reimport =
                    $str_courier_bill_items_details["case_for_reimport"];
                $import_against_license =
                    $str_courier_bill_items_details["import_against_license"];
                $serial_number_in_invoice =
                    $str_courier_bill_items_details["serial_number_in_invoice"];
                $item_description =
                    $str_courier_bill_items_details["item_description"];
                $general_description =
                    $str_courier_bill_items_details["general_description"];
                $currency_for_unit_price =
                    $str_courier_bill_items_details["currency_for_unit_price"];
                $unit_price = $str_courier_bill_items_details["unit_price"];
                $unit_of_measure =
                    $str_courier_bill_items_details["unit_of_measure"];
                $quantity = $str_courier_bill_items_details["quantity"];
                $rate_of_exchange =
                    $str_courier_bill_items_details["rate_of_exchange"];
                $accessories_if_any =
                    $str_courier_bill_items_details["accessories_if_any"];
                $name_of_manufacturer =
                    $str_courier_bill_items_details["name_of_manufacturer"];
                $brand = $str_courier_bill_items_details["brand"];
                $grade = $str_courier_bill_items_details["grade"];
                $specification =
                    $str_courier_bill_items_details["specification"];
                $end_use_of_item =
                    $str_courier_bill_items_details["end_use_of_item"];
                $items_details_country_of_origin =
                    $str_courier_bill_items_details[
                        "items_details_country_of_origin"
                    ];
                $bill_of_entry_number =
                    $str_courier_bill_items_details["bill_of_entry_number"];
                $details_in_case_of_previous_imports_date =
                    $str_courier_bill_items_details[
                        "details_in_case_of_previous_imports_date"
                    ];
                $details_in_case_previous_imports_currency =
                    $str_courier_bill_items_details[
                        "details_in_case_previous_imports_currency"
                    ];
                $unit_value = $str_courier_bill_items_details["unit_value"];
                $customs_house =
                    $str_courier_bill_items_details["customs_house"];
                $ritc = $str_courier_bill_items_details["ritc"];
                $ctsh = $str_courier_bill_items_details["ctsh"];
                $cetsh = $str_courier_bill_items_details["cetsh"];
                $currency_for_rsp =
                    $str_courier_bill_items_details["currency_for_rsp"];
                $retail_sales_price_per_unit =
                    $str_courier_bill_items_details[
                        "retail_sales_price_per_unit"
                    ];
                $exim_scheme_code_if_any =
                    $str_courier_bill_items_details["exim_scheme_code_if_any"];
                $para_noyear_of_exim_policy =
                    $str_courier_bill_items_details[
                        "para_noyear_of_exim_policy"
                    ];
                $items_details_are_the_buyer_and_seller_related =
                    $str_courier_bill_items_details[
                        "items_details_are_the_buyer_and_seller_related"
                    ];
                $if_the_buyer_and_seller_relation_examined_earlier_by_svb =
                    $str_courier_bill_items_details[
                        "if_the_buyer_and_seller_relation_examined_earlier_by_svb"
                    ];
                $items_details_svb_reference_number =
                    $str_courier_bill_items_details[
                        "items_details_svb_reference_number"
                    ];
                $items_details_svb_date =
                    $str_courier_bill_items_details["items_details_svb_date"];
                $items_details_indication_for_provisional_final =
                    $str_courier_bill_items_details[
                        "items_details_indication_for_provisional_final"
                    ];
                $shipping_bill_number =
                    $str_courier_bill_items_details["shipping_bill_number"];
                $shipping_bill_date =
                    $str_courier_bill_items_details["shipping_bill_date"];
                $port_of_export =
                    $str_courier_bill_items_details["port_of_export"];
                $invoice_number_of_shipping_bill =
                    $str_courier_bill_items_details[
                        "invoice_number_of_shipping_bill"
                    ];
                $item_serial_number_in_shipping_bill =
                    $str_courier_bill_items_details[
                        "item_serial_number_in_shipping_bill"
                    ];
                $freight = $str_courier_bill_items_details["freight"];
                $insurance = $str_courier_bill_items_details["insurance"];
                $total_repair_cost_including_cost_of_materials =
                    $str_courier_bill_items_details[
                        "total_repair_cost_including_cost_of_materials"
                    ];
                $additional_duty_exemption_requested =
                    $str_courier_bill_items_details[
                        "additional_duty_exemption_requested"
                    ];
                $items_details_notification_number =
                    $str_courier_bill_items_details[
                        "items_details_notification_number"
                    ];
                $serial_number_in_notification =
                    $str_courier_bill_items_details[
                        "serial_number_in_notification"
                    ];
                $license_registration_number =
                    $str_courier_bill_items_details[
                        "license_registration_number"
                    ];
                $license_registration_date =
                    $str_courier_bill_items_details[
                        "license_registration_date"
                    ];
                $debit_value_rs =
                    $str_courier_bill_items_details["debit_value_rs"];
                $debit_quantity =
                    $str_courier_bill_items_details["debit_quantity"];
                $item_serial_number_in_license =
                    $str_courier_bill_items_details[
                        "item_serial_number_in_license"
                    ];
                $assessable_value =
                    $str_courier_bill_items_details["assessable_value"];
                $created_at = $str_courier_bill_items_details["created_at"];
            }*/
           echo $sql_insert_courier_bill_items_details =
                "INSERT INTO `courier_bill_items_details`
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
VALUES('" .
                $items_detail_id .
                "',
'" .
                $case_for_reimport .
                "',
'" .
                $import_against_license .
                "',
'" .
                $serial_number_in_invoice .
                "',
'" .
                $item_description .
                "',
'" .
                $general_description .
                "',
'" .
                $currency_for_unit_price .
                "',
'" .
                $unit_price .
                "',
'" .
                $unit_of_measure .
                "',
'" .
                $quantity .
                "',
'" .
                $rate_of_exchange .
                "',
'" .
                $accessories_if_any .
                "',
'" .
                $name_of_manufacturer .
                "',
'" .
                $brand .
                "',
'" .
                $model .
                "',
'" .
                $grade .
                "',
'" .
                $specification .
                "',
'" .
                $end_use_of_item .
                "',
'" .
                $items_details_country_of_origin .
                "',
'" .
                $bill_of_entry_number .
                "',
'" .
                $details_in_case_of_previous_imports_date .
                "',
'" .
                $details_in_case_previous_imports_currency .
                "',
'" .
                $unit_value .
                "',
'" .
                $customs_house .
                "',
'" .
                $ritc .
                "',
'" .
                $ctsh .
                "',
'" .
                $cetsh .
                "',
'" .
                $currency_for_rsp .
                "',
'" .
                $retail_sales_price_per_unit .
                "',
'" .
                $exim_scheme_code_if_any .
                "',
'" .
                $para_noyear_of_exim_policy .
                "',
'" .
                $items_details_are_the_buyer_and_seller_related .
                "',
'" .
                $if_the_buyer_and_seller_relation_examined_earlier_by_svb .
                "',
'" .
                $items_details_svb_reference_number .
                "',
'" .
                $items_details_svb_date .
                "',
'" .
                $items_details_indication_for_provisional_final .
                "',
'" .
                $shipping_bill_number .
                "',
'" .
                $shipping_bill_date .
                "',
'" .
                $port_of_export .
                "',
'" .
                $invoice_number_of_shipping_bill .
                "',
'" .
                $item_serial_number_in_shipping_bill .
                "',
'" .
                $freight .
                "',
'" .
                $insurance .
                "',
'" .
                $total_repair_cost_including_cost_of_materials .
                "',
'" .
                $additional_duty_exemption_requested .
                "',
'" .
                $items_details_notification_number .
                "',
'" .
                $serial_number_in_notification .
                "',
'" .
                $license_registration_number .
                "',
'" .
                $license_registration_date .
                "',
'" .
                $debit_value_rs .
                "',
'" .
                $unit_of_measure_for_quantity_to_be_debited .
                "',
'" .
                $debit_quantity .
                "',
'" .
                $item_serial_number_in_license .
                "',
'" .
                $assessable_value .
                "',
'" .
                $str_courier_bill_items_details["created_at"] .
                "')";
            $copy_insert_courier_bill_items_details = $db1_courier_bill_items_details->query(
                $sql_insert_courier_bill_items_details
            );
        }
    }

public function courier_bill_manifest_details(){
        /******************************************************************Start courier_bill_container_details***************************************************************************************/
        $query_courier_bill_manifest_details =
            "SELECT courier_bill_manifest_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no FROM courier_bill_manifest_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_manifest_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id ";
        $statement_courier_bill_manifest_details = $this->db->query(
            $query_courier_bill_manifest_details
        );
        $iecwise_courier_bill_manifest_details = [];
        $result_courier_bill_manifest_details = $statement_courier_bill_manifest_details->result_array();

        foreach (
            $result_courier_bill_manifest_details
            as $str_courier_bill_manifest_details
        ) {
            $iec_courier_bill_manifest_details =
                $str_courier_bill_manifest_details["user_iec_no"];
            $sql_courier_bill_manifest_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_manifest_details'";
            $iecwise_courier_bill_manifest_details = $this->db->query(
                $sql_courier_bill_manifest_details
            );
            $iecwise_data_courier_bill_manifest_details = $iecwise_courier_bill_manifest_details->result_array();
            $db1_courier_bill_manifest_details = $this->database_connection(
                $iecwise_data_courier_bill_manifest_details[0][
                    "lucrative_users_id"
                ]
            );
         //   if (get_magic_quotes_gpc()) {
                $manifest_details_id = addslashes(
                    $str_courier_bill_manifest_details["manifest_details_id"]
                );
                $import_general_manifest_igm_number = addslashes(
                    $str_courier_bill_manifest_details[
                        "import_general_manifest_igm_number"
                    ]
                );
                $date_of_entry_inward = addslashes(
                    $str_courier_bill_manifest_details["date_of_entry_inward"]
                );
                $master_airway_bill_mawb_number = addslashes(
                    $str_courier_bill_manifest_details[
                        "master_airway_bill_mawb_number"
                    ]
                );
                $date_of_mawb = addslashes(
                    $str_courier_bill_manifest_details["date_of_mawb"]
                );
                $house_airway_bill_hawb_number = addslashes(
                    $str_courier_bill_manifest_details[
                        "house_airway_bill_hawb_number"
                    ]
                );
                $date_of_hawb = addslashes(
                    $str_courier_bill_manifest_details["date_of_hawb"]
                );
                $marks_and_numbers = addslashes(
                    $str_courier_bill_manifest_details["marks_and_numbers"]
                );
                $number_of_packages = addslashes(
                    $str_courier_bill_manifest_details["number_of_packages"]
                );
                $type_of_packages = addslashes(
                    $str_courier_bill_manifest_details["type_of_packages"]
                );
                $interest_amount = addslashes(
                    $str_courier_bill_manifest_details["interest_amount"]
                );
                $unit_of_measure_for_gross_weight = addslashes(
                    $str_courier_bill_manifest_details[
                        "unit_of_measure_for_gross_weight"
                    ]
                );
                $gross_weight = addslashes(
                    $str_courier_bill_manifest_details["gross_weight"]
                );
                $created_at = addslashes(
                    $str_courier_bill_manifest_details["created_at"]
                );
           /* } else {
                $manifest_details_id =
                    $str_courier_bill_manifest_details["manifest_details_id"];
                $import_general_manifest_igm_number =
                    $str_courier_bill_manifest_details[
                        "import_general_manifest_igm_number"
                    ];
                $date_of_entry_inward =
                    $str_courier_bill_manifest_details["date_of_entry_inward"];
                $master_airway_bill_mawb_number =
                    $str_courier_bill_manifest_details[
                        "master_airway_bill_mawb_number"
                    ];
                $date_of_mawb =
                    $str_courier_bill_manifest_details["date_of_mawb"];
                $house_airway_bill_hawb_number =
                    $str_courier_bill_manifest_details[
                        "house_airway_bill_hawb_number"
                    ];
                $date_of_hawb =
                    $str_courier_bill_manifest_details["date_of_hawb"];
                $marks_and_numbers =
                    $str_courier_bill_manifest_details["marks_and_numbers"];
                $number_of_packages =
                    $str_courier_bill_manifest_details["number_of_packages"];
                $type_of_packages =
                    $str_courier_bill_manifest_details["type_of_packages"];
                $interest_amount =
                    $str_courier_bill_manifest_details["interest_amount"];
                $unit_of_measure_for_gross_weight =
                    $str_courier_bill_manifest_details[
                        "unit_of_measure_for_gross_weight"
                    ];
                $gross_weight =
                    $str_courier_bill_manifest_details["gross_weight"];
                $created_at = $str_courier_bill_manifest_details["created_at"];
            }*/
           echo $sql_insert_courier_bill_manifest_details =
                "INSERT INTO `courier_bill_manifest_details` ( `manifest_details_id`, `import_general_manifest_igm_number`, `date_of_entry_inward`, `master_airway_bill_mawb_number`, `date_of_mawb`, `house_airway_bill_hawb_number`, `date_of_hawb`, `marks_and_numbers`, `number_of_packages`, `type_of_packages`, `interest_amount`, `unit_of_measure_for_gross_weight`, `gross_weight`, `created_at`)
VALUES('" .
                $manifest_details_id .
                "','" .
                $import_general_manifest_igm_number .
                "','" .
                $date_of_entry_inward .
                "','" .
                $master_airway_bill_mawb_number .
                "','" .
                $date_of_mawb .
                "','" .
                $house_airway_bill_hawb_number .
                "','" .
                $date_of_hawb .
                "','" .
                $marks_and_numbers .
                "','" .
                $number_of_packages .
                "','" .
                $type_of_packages .
                "','" .
                $interest_amount .
                "','" .
                $unit_of_measure_for_gross_weight .
                "','" .
                $gross_weight .
                "','" .
                $created_at .
                "')";
            $copy_insert_courier_bill_manifest_details = $db1_courier_bill_manifest_details->query(
                $sql_insert_courier_bill_manifest_details
            );
        }

        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }

public function courier_bill_notification_used_for_items(){
        /******************************************************************Start courier_bill_container_details***************************************************************************************/
        $query_courier_bill_notification_used_for_items =
            "SELECT courier_bill_notification_used_for_items.*,courier_bill_items_details.items_detail_id,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_notification_used_for_items LEFT JOIN courier_bill_items_details ON courier_bill_notification_used_for_items.items_detail_id=courier_bill_items_details.items_detail_id LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id ";
        $statement_courier_bill_notification_used_for_items = $this->db->query(
            $query_courier_bill_notification_used_for_items
        );
        $iecwise_courier_bill_notification_used_for_items = [];
        $result_courier_bill_notification_used_for_items = $statement_courier_bill_notification_used_for_items->result_array();
        //print_r($result_courier_bill_notification_used_for_items);exit;

        foreach (
            $result_courier_bill_notification_used_for_items
            as $str_courier_bill_notification_used_for_items
        ) {
            $iec_courier_bill_notification_used_for_items =
                $str_courier_bill_notification_used_for_items["user_iec_no"];
            $sql_courier_bill_notification_used_for_items = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_notification_used_for_items'";
            $iecwise_courier_bill_notification_used_for_items = $this->db->query(
                $sql_courier_bill_notification_used_for_items
            );
            $iecwise_data_courier_bill_notification_used_for_items = $iecwise_courier_bill_notification_used_for_items->result_array();
            $db1_courier_bill_notification_used_for_items = $this->database_connection(
                $iecwise_data_courier_bill_notification_used_for_items[0][
                    "lucrative_users_id"
                ]
            );

           // if (get_magic_quotes_gpc()) {
                $items_detail_id = addslashes(
                    $str_courier_bill_notification_used_for_items[
                        "items_detail_id"
                    ]
                );
                $notification_item_srno = addslashes(
                    $str_courier_bill_notification_used_for_items[
                        "notification_item_srno"
                    ]
                );
                $notification_number = addslashes(
                    $str_courier_bill_notification_used_for_items[
                        "notification_number"
                    ]
                );
                $serial_number_of_notification = addslashes(
                    $str_courier_bill_notification_used_for_items[
                        "serial_number_of_notification"
                    ]
                );
                $created_at = addslashes(
                    $str_courier_bill_notification_used_for_items["created_at"]
                );
        /*    } else {
                $items_detail_id =
                    $str_courier_bill_notification_used_for_items[
                        "items_detail_id"
                    ];
                $notification_item_srno =
                    $str_courier_bill_notification_used_for_items[
                        "notification_item_srno"
                    ];
                $notification_number =
                    $str_courier_bill_notification_used_for_items[
                        "notification_number"
                    ];
                $serial_number_of_notification =
                    $str_courier_bill_notification_used_for_items[
                        "serial_number_of_notification"
                    ];
                $created_at =
                    $str_courier_bill_notification_used_for_items["created_at"];
            }*/

          echo  $sql_insert_courier_bill_notification_used_for_items =
                "INSERT INTO `courier_bill_notification_used_for_items` ( `items_detail_id`, `notification_item_srno`, `notification_number`, `serial_number_of_notification`, `created_at`)
VALUES('" .
                $items_detail_id .
                "','" .
                $notification_item_srno .
                "','" .
                $notification_number .
                "','" .
                $serial_number_of_notification .
                "','" .
                $created_at .
                "')";
            $copy_insert_courier_bill_notification_used_for_items = $db1_courier_bill_notification_used_for_items->query(
                $sql_insert_courier_bill_notification_used_for_items
            );
        }

        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }

public function courier_bill_payment_details(){
        /******************************************************************Start courier_bill_payment_details***************************************************************************************/
        $query_courier_bill_payment_details =
            "SELECT courier_bill_payment_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_payment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_payment_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id ";
        $statement_courier_bill_payment_details = $this->db->query(
            $query_courier_bill_payment_details
        );
        $iecwise_courier_bill_payment_details = [];
        $result_courier_bill_payment_details = $statement_courier_bill_payment_details->result_array();
       // print_r($result_courier_bill_payment_details);exit;

        foreach (
            $result_courier_bill_payment_details
            as $str_courier_bill_payment_details
        ) {
            $iec_courier_bill_payment_details =
                $str_courier_bill_payment_details["user_iec_no"];
            $sql_courier_bill_payment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_payment_details'";
            $iecwise_courier_bill_payment_details = $this->db->query(
                $sql_courier_bill_payment_details
            );
            $iecwise_data_courier_bill_payment_details = $iecwise_courier_bill_payment_details->result_array();
            $db1_courier_bill_payment_details = $this->database_connection(
                $iecwise_data_courier_bill_payment_details[0][
                    "lucrative_users_id"
                ]
            );

         //   if (get_magic_quotes_gpc()) {
                
                $courier_bill_of_entry_id = addslashes(
                    $str_courier_bill_payment_details[
                        "courier_bill_of_entry_id"
                    ]
                );
                $payment_details_id = addslashes(
                    $str_courier_bill_payment_details["payment_details_id"]
                );
                $payment_details_srno = addslashes(
                    $str_courier_bill_payment_details["payment_details_srno"]
                );
                $tr6_challan_number = addslashes(
                    $str_courier_bill_payment_details["tr6_challan_number"]
                );
                $total_amount = addslashes(
                    $str_courier_bill_payment_details["total_amount"]
                );
                $challan_date = addslashes(
                    $str_courier_bill_payment_details["challan_date"]
                );
                $created_at = addslashes(
                    $str_courier_bill_payment_details["created_at"]
                );
    /*        } else {
                $courier_bill_of_entry_id =
                    $str_courier_bill_payment_details[
                        "courier_bill_of_entry_id"
                    ];
                $payment_details_id =
                    $str_courier_bill_payment_details["payment_details_id"];
                $payment_details_srno =
                    $str_courier_bill_payment_details["payment_details_srno"];
                $tr6_challan_number =
                    $str_courier_bill_payment_details["tr6_challan_number"];
                $total_amount =
                    $str_courier_bill_payment_details["total_amount"];
                $challan_date =
                    $str_courier_bill_payment_details["challan_date"];
                $created_at = $str_courier_bill_payment_details["created_at"];
            }*/

        echo    $sql_insert_courier_bill_payment_details =
                "INSERT INTO `courier_bill_payment_details` (`courier_bill_of_entry_id`, `payment_details_id`, `payment_details_srno`, `tr6_challan_number`, `total_amount`, `challan_date`, `created_at`)
VALUES('" .
                $courier_bill_of_entry_id .
                "','" .
                $payment_details_id .
                "','" .
                $payment_details_srno .
                "','" .
                $tr6_challan_number .
                "','" .
                $total_amount .
                "','" .
                $challan_date .
                "','" .
                $created_at .
                "')";
            $copy_insert_courier_bill_payment_details = $db1_courier_bill_payment_details->query(
                $sql_insert_courier_bill_payment_details
            );
        }

        /******************************************************************End courier_bill_payment_details***************************************************************************************/
    }

public function courier_bill_procurment_details(){
        /******************************************************************Start courier_bill_procurment_details***************************************************************************************/
        $query_courier_bill_procurment_details =
            "SELECT courier_bill_procurment_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_procurment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_procurment_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id ";
        $statement_courier_bill_procurment_details = $this->db->query(
            $query_courier_bill_procurment_details
        );
        $iecwise_courier_bill_procurment_details = [];
        $result_courier_bill_procurment_details = $statement_courier_bill_procurment_details->result_array();
       // print_r($result_courier_bill_procurment_details);exit;

        foreach (
            $result_courier_bill_procurment_details
            as $str_courier_bill_procurment_details
        ) {
            $iec_courier_bill_procurment_details =
                $str_courier_bill_procurment_details["user_iec_no"];
            $sql_courier_bill_procurment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_procurment_details'";
            $iecwise_courier_bill_procurment_details = $this->db->query(
                $sql_courier_bill_procurment_details
            );
            $iecwise_data_courier_bill_procurment_details = $iecwise_courier_bill_procurment_details->result_array();
            $db1_courier_bill_procurment_details = $this->database_connection(
                $iecwise_data_courier_bill_procurment_details[0][
                    "lucrative_users_id"
                ]
            );

       //     if (get_magic_quotes_gpc()) {
                $courier_bill_of_entry_id = addslashes(
                    $str_courier_bill_procurment_details[
                        "courier_bill_of_entry_id"
                    ]
                );
                $procurment_details_id = addslashes(
                    $str_courier_bill_procurment_details[
                        "procurment_details_id"
                    ]
                );
                $procurement_under_3696_cus = addslashes(
                    $str_courier_bill_procurment_details[
                        "procurement_under_3696_cus"
                    ]
                );
                $procurement_certificate_number = addslashes(
                    $str_courier_bill_procurment_details[
                        "procurement_certificate_number"
                    ]
                );
                $date_of_issuance_of_certificate = addslashes(
                    $str_courier_bill_procurment_details[
                        "date_of_issuance_of_certificate"
                    ]
                );
                $location_code_of_the_cent_ral_excise_office_issuing_the_certifi = addslashes(
                    $str_courier_bill_procurment_details[
                        "location_code_of_the_cent_ral_excise_office_issuing_the_certifi"
                    ]
                );
                $commissione_rate = addslashes(
                    $str_courier_bill_procurment_details["commissione_rate"]
                );
                $division = addslashes(
                    $str_courier_bill_procurment_details["division"]
                );
                $range = addslashes(
                    $str_courier_bill_procurment_details["range"]
                );
                $import_under_multiple_in_voices = addslashes(
                    $str_courier_bill_procurment_details[
                        "import_under_multiple_in_voices"
                    ]
                );
                $created_at = addslashes(
                    $str_courier_bill_procurment_details["created_at"]
                );
     /*       } else {
                $courier_bill_of_entry_id =
                    $str_courier_bill_procurment_details[
                        "courier_bill_of_entry_id"
                    ];
                $procurment_details_id =
                    $str_courier_bill_procurment_details[
                        "procurment_details_id"
                    ];
                $procurement_under_3696_cus =
                    $str_courier_bill_procurment_details[
                        "procurement_under_3696_cus"
                    ];
                $procurement_certificate_number =
                    $str_courier_bill_procurment_details[
                        "procurement_certificate_number"
                    ];
                $date_of_issuance_of_certificate =
                    $str_courier_bill_procurment_details[
                        "date_of_issuance_of_certificate"
                    ];
                $location_code_of_the_cent_ral_excise_office_issuing_the_certifi =
                    $str_courier_bill_procurment_details[
                        "location_code_of_the_cent_ral_excise_office_issuing_the_certifi"
                    ];
                $commissione_rate =
                    $str_courier_bill_procurment_details["commissione_rate"];
                $division = addslashes(
                    $str_courier_bill_procurment_details["division"]
                );
                $range = addslashes(
                    $str_courier_bill_procurment_details["range"]
                );
                $import_under_multiple_in_voices = addslashes(
                    $str_courier_bill_procurment_details[
                        "import_under_multiple_in_voices"
                    ]
                );
                $created_at = addslashes(
                    $str_courier_bill_procurment_details["created_at"]
                );
            }*/

         echo   $sql_insert_courier_bill_procurment_details =
                "INSERT INTO `courier_bill_procurment_details`(`courier_bill_of_entry_id`,`procurment_details_id`, `procurement_under_3696_cus`, `procurement_certificate_number`,`date_of_issuance_of_certificate`, `location_code_of_the_cent_ral_excise_office_issuing_the_certifi`, `commissione_rate`,`division`, `range`, `import_under_multiple_in_voices`,`created_at`)
 VALUES (''" .
                $courier_bill_of_entry_id .
                "',
 '" .
                $procurment_details_id .
                "',
 '" .
                $procurement_under_3696_cus .
                "',
 '" .
                $procurement_certificate_number .
                "',
 '" .
                $date_of_issuance_of_certificate .
                "',
 '" .
                $location_code_of_the_cent_ral_excise_office_issuing_the_certifi .
                "',
 '" .
                $commissione_rate .
                "',
 '" .
                $division .
                "',
 '" .
                $range .
                "',
 '" .
                $import_under_multiple_in_voices .
                "',
 '" .
                $created_at .
                "')";
            $copy_insert_courier_bill_procurment_details = $db1_courier_bill_procurment_details->query(
                $sql_insert_courier_bill_procurment_details
            );
        }
        /******************************************************************End courier_bill_procurment_details***************************************************************************************/
    }

public function courier_bill_summary(){
        $query_courier_bill_summary =
            "SELECT courier_bill_summary.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_summary LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id ";

        $statement_courier_bill_summary = $this->db->query(
            $query_courier_bill_summary
        );
        $iecwise_courier_bill_summary = [];
        $result_courier_bill_summary = $statement_courier_bill_summary->result_array();
       // print_r($result_courier_bill_summary);exit;

        foreach ($result_courier_bill_summary as $str_courier_bill_summary) {
            $iec_courier_bill_summary =
                $str_courier_bill_summary["user_iec_no"];
            $sql_courier_bill_summary = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_courier_bill_summary'";
            $iecwise_courier_bill_summary = $this->db->query(
                $sql_courier_bill_summary
            );
            $iecwise_data_courier_bill_summary = $iecwise_courier_bill_summary->result_array();
            $db1_courier_bill_summary = $this->database_connection(
                $iecwise_data_courier_bill_summary[0]["lucrative_users_id"]
            );

            //if (get_magic_quotes_gpc()) {
                $courier_bill_of_entry_id = addslashes(
                    $str_courier_bill_summary["courier_bill_of_entry_id"]
                );
                $cb_file_status_id = addslashes(
                    $str_courier_bill_summary["cb_file_status_id"]
                );
                $current_status_of_the_cbe = addslashes(
                    $str_courier_bill_summary["current_status_of_the_cbe"]
                );
                $cbexiv_number = addslashes(
                    $str_courier_bill_summary["cbexiv_number"]
                );
                $courier_registration_number = addslashes(
                    $str_courier_bill_summary["courier_registration_number"]
                );
                $name_of_the_authorized_courier = addslashes(
                    $str_courier_bill_summary["name_of_the_authorized_courier"]
                );
                $address_of_authorized_courier = addslashes(
                    $str_courier_bill_summary["address_of_authorized_courier"]
                );
                $particulars_customs_house_agent_name = addslashes(
                    $str_courier_bill_summary[
                        "particulars_customs_house_agent_name"
                    ]
                );
                $particulars_customs_house_agent_licence_no = addslashes(
                    $str_courier_bill_summary[
                        "particulars_customs_house_agent_licence_no"
                    ]
                );
                $particulars_customs_house_agent_address = addslashes(
                    $str_courier_bill_summary[
                        "particulars_customs_house_agent_address"
                    ]
                );
                $import_export_code = addslashes(
                    $str_courier_bill_summary["import_export_code"]
                );
                $import_export_branch_code = addslashes(
                    $str_courier_bill_summary["import_export_branch_code"]
                );
                $particulars_of_the_importer_name = addslashes(
                    $str_courier_bill_summary[
                        "particulars_of_the_importer_name"
                    ]
                );
                $particulars_of_the_importer_address = addslashes(
                    $str_courier_bill_summary[
                        "particulars_of_the_importer_address"
                    ]
                );
                $category_of_importer = addslashes(
                    $str_courier_bill_summary["category_of_importer"]
                );
                $type_of_importer = addslashes(
                    $str_courier_bill_summary["type_of_importer"]
                );
                $in_case_of_other_importer = addslashes(
                    $str_courier_bill_summary["in_case_of_other_importer"]
                );
                $authorised_dealer_code_of_bank = addslashes(
                    $str_courier_bill_summary["authorised_dealer_code_of_bank"]
                );
                $class_code = addslashes(
                    $str_courier_bill_summary["class_code"]
                );
                $cb_no = addslashes($str_courier_bill_summary["cb_no"]);
                $cb_date = addslashes($str_courier_bill_summary["cb_date"]);
                $category_of_boe = addslashes(
                    $str_courier_bill_summary["category_of_boe"]
                );
                $type_of_boe = addslashes(
                    $str_courier_bill_summary["type_of_boe"]
                );
                $kyc_document = addslashes(
                    $str_courier_bill_summary["kyc_document"]
                );
                $kyc_id = addslashes($str_courier_bill_summary["kyc_id"]);
                $state_code = addslashes(
                    $str_courier_bill_summary["state_code"]
                );
                $high_sea_sale = addslashes(
                    $str_courier_bill_summary["high_sea_sale"]
                );
                $ie_code_of_hss = addslashes(
                    $str_courier_bill_summary["ie_code_of_hss"]
                );
                $ie_branch_code_of_hss = addslashes(
                    $str_courier_bill_summary["ie_branch_code_of_hss"]
                );
                $particulars_high_sea_seller_name = addslashes(
                    $str_courier_bill_summary[
                        "particulars_high_sea_seller_name"
                    ]
                );
                $particulars_high_sea_seller_address = addslashes(
                    $str_courier_bill_summary[
                        "particulars_high_sea_seller_address"
                    ]
                );
                $use_of_the_first_proviso_under_section_461customs_act1962 = addslashes(
                    $str_courier_bill_summary[
                        "use_of_the_first_proviso_under_section_461customs_act1962"
                    ]
                );
                $request_for_first_check = addslashes(
                    $str_courier_bill_summary["request_for_first_check"]
                );
                $request_for_urgent_clear_ance_against_temporary_documentation = addslashes(
                    $str_courier_bill_summary[
                        "request_for_urgent_clear_ance_against_temporary_documentation"
                    ]
                );
                $request_for_extension_of_time_limit_as_per_section_48customs_ac = addslashes(
                    $str_courier_bill_summary[
                        "request_for_extension_of_time_limit_as_per_section_48customs_ac"
                    ]
                );
                $reason_in_case_extension_of_time_limit_is_requested = addslashes(
                    $str_courier_bill_summary[
                        "reason_in_case_extension_of_time_limit_is_requested"
                    ]
                );
                $country_of_origin = addslashes(
                    $str_courier_bill_summary["country_of_origin"]
                );
                $country_of_consignment = addslashes(
                    $str_courier_bill_summary["country_of_consignment"]
                );
                $name_of_gateway_port = addslashes(
                    $str_courier_bill_summary["name_of_gateway_port"]
                );
                $gateway_igm_number = addslashes(
                    $str_courier_bill_summary["gateway_igm_number"]
                );
                $date_of_entry_inwards_of_gateway_port = addslashes(
                    $str_courier_bill_summary[
                        "date_of_entry_inwards_of_gateway_port"
                    ]
                );
                $case_of_crn = addslashes(
                    $str_courier_bill_summary["case_of_crn"]
                );
                $number_of_invoices = addslashes(
                    $str_courier_bill_summary["number_of_invoices"]
                );
                $total_freight = addslashes(
                    $str_courier_bill_summary["total_freight"]
                );
                $total_insurance = addslashes(
                    $str_courier_bill_summary["total_insurance"]
                );
                $created_at = addslashes(
                    $str_courier_bill_summary["created_at"]
                );
  /*          } else {
                $courier_bill_of_entry_id =
                    $str_courier_bill_summary["courier_bill_of_entry_id"];
                $cb_file_status_id =
                    $str_courier_bill_summary["cb_file_status_id"];
                $current_status_of_the_cbe =
                    $str_courier_bill_summary["current_status_of_the_cbe"];
                $cbexiv_number = $str_courier_bill_summary["cbexiv_number"];
                $courier_registration_number =
                    $str_courier_bill_summary["courier_registration_number"];
                $name_of_the_authorized_courier =
                    $str_courier_bill_summary["name_of_the_authorized_courier"];
                $address_of_authorized_courier =
                    $str_courier_bill_summary["address_of_authorized_courier"];
                $particulars_customs_house_agent_name =
                    $str_courier_bill_summary[
                        "particulars_customs_house_agent_name"
                    ];
                $particulars_customs_house_agent_licence_no =
                    $str_courier_bill_summary[
                        "particulars_customs_house_agent_licence_no"
                    ];
                $particulars_customs_house_agent_address =
                    $str_courier_bill_summary[
                        "particulars_customs_house_agent_address"
                    ];
                $import_export_code =
                    $str_courier_bill_summary["import_export_code"];
                $import_export_branch_code =
                    $str_courier_bill_summary["import_export_branch_code"];
                $particulars_of_the_importer_name =
                    $str_courier_bill_summary[
                        "particulars_of_the_importer_name"
                    ];
                $particulars_of_the_importer_address =
                    $str_courier_bill_summary[
                        "particulars_of_the_importer_address"
                    ];
                $category_of_importer =
                    $str_courier_bill_summary["category_of_importer"];
                $type_of_importer =
                    $str_courier_bill_summary["type_of_importer"];
                $in_case_of_other_importer =
                    $str_courier_bill_summary["in_case_of_other_importer"];
                $authorised_dealer_code_of_bank =
                    $str_courier_bill_summary["authorised_dealer_code_of_bank"];
                $class_code = $str_courier_bill_summary["class_code"];
                $cb_no = $str_courier_bill_summary["cb_no"];
                $cb_date = $str_courier_bill_summary["cb_date"];
                $category_of_boe = $str_courier_bill_summary["category_of_boe"];
                $type_of_boe = $str_courier_bill_summary["type_of_boe"];
                $kyc_document = $str_courier_bill_summary["kyc_document"];
                $kyc_id = $str_courier_bill_summary["kyc_id"];
                $high_sea_sale = $str_courier_bill_summary["high_sea_sale"];
                $ie_code_of_hss = $str_courier_bill_summary["ie_code_of_hss"];
                $ie_branch_code_of_hss =
                    $str_courier_bill_summary["ie_branch_code_of_hss"];
                $particulars_high_sea_seller_name =
                    $str_courier_bill_summary[
                        "particulars_high_sea_seller_name"
                    ];
                $particulars_high_sea_seller_address =
                    $str_courier_bill_summary[
                        "particulars_high_sea_seller_address"
                    ];
                $use_of_the_first_proviso_under_section_461customs_act1962 =
                    $str_courier_bill_summary[
                        "use_of_the_first_proviso_under_section_461customs_act1962"
                    ];
                $request_for_first_check =
                    $str_courier_bill_summary["request_for_first_check"];
                $request_for_urgent_clear_ance_against_temporary_documentation =
                    $str_courier_bill_summary[
                        "request_for_urgent_clear_ance_against_temporary_documentation"
                    ];
                $request_for_extension_of_time_limit_as_per_section_48customs_ac =
                    $str_courier_bill_summary[
                        "request_for_extension_of_time_limit_as_per_section_48customs_ac"
                    ];
                $reason_in_case_extension_of_time_limit_is_requested =
                    $str_courier_bill_summary[
                        "reason_in_case_extension_of_time_limit_is_requested"
                    ];
                $country_of_origin =
                    $str_courier_bill_summary["country_of_origin"];
                $country_of_consignment =
                    $str_courier_bill_summary["country_of_consignment"];
                $name_of_gateway_port =
                    $str_courier_bill_summary["name_of_gateway_port"];
                $gateway_igm_number =
                    $str_courier_bill_summary["gateway_igm_number"];
                $date_of_entry_inwards_of_gateway_port =
                    $str_courier_bill_summary[
                        "date_of_entry_inwards_of_gateway_port"
                    ];
                $case_of_crn = $str_courier_bill_summary["case_of_crn"];
                $number_of_invoices =
                    $str_courier_bill_summary["number_of_invoices"];
                $total_freight = $str_courier_bill_summary["total_freight"];
                $total_insurance = $str_courier_bill_summary["total_insurance"];
                $created_at = $str_courier_bill_summary["created_at"];
            }*/

           echo $sql_insert_courier_bill_summary =
                "INSERT INTO `courier_bill_summary`(`courier_bill_of_entry_id`, `cb_file_status_id`, `current_status_of_the_cbe`, `cbexiv_number`, `courier_registration_number`, `name_of_the_authorized_courier`, `address_of_authorized_courier`, `particulars_customs_house_agent_name`, `particulars_customs_house_agent_licence_no`, `particulars_customs_house_agent_address`, 
`import_export_code`, `import_export_branch_code`, `particulars_of_the_importer_name`, `particulars_of_the_importer_address`, 
`category_of_importer`, `type_of_importer`, `in_case_of_other_importer`, `authorised_dealer_code_of_bank`, `class_code`, `cb_no`, `cb_date`,
 `category_of_boe`, `type_of_boe`, `kyc_document`, `kyc_id`, `state_code`, `high_sea_sale`, `ie_code_of_hss`, `ie_branch_code_of_hss`, 
 `particulars_high_sea_seller_name`, `particulars_high_sea_seller_address`, `use_of_the_first_proviso_under_section_461customs_act1962`,
 `request_for_first_check`, `request_for_urgent_clear_ance_against_temporary_documentation`, `request_for_extension_of_time_limit_as_per_section_48customs_ac`, 
 `reason_in_case_extension_of_time_limit_is_requested`, `country_of_origin`, `country_of_consignment`, `name_of_gateway_port`, `gateway_igm_number`, 
 `date_of_entry_inwards_of_gateway_port`, `case_of_crn`, `number_of_invoices`, `total_freight`, `total_insurance`, `created_at`)
 VALUES ('" .
                $courier_bill_of_entry_id .
                "',
 '" .
                $cb_file_status_id .
                "',
 '" .
                $current_status_of_the_cbe .
                "',
 '" .
                $cbexiv_number .
                "',
 '" .
                $courier_registration_number .
                "',
 '" .
                $name_of_the_authorized_courier .
                "',
 '" .
                $address_of_authorized_courier .
                "',
 '" .
                $particulars_customs_house_agent_name .
                "',
 '" .
                $particulars_customs_house_agent_licence_no .
                "',
 '" .
                $particulars_customs_house_agent_address .
                "',
 '" .
                $import_export_code .
                "',
 '" .
                $import_export_branch_code .
                "',
 '" .
                $particulars_of_the_importer_name .
                "',
 '" .
                $particulars_of_the_importer_address .
                "',
 '" .
                $category_of_importer .
                "',
 '" .
                $type_of_importer .
                "',
'" .
                $in_case_of_other_importer .
                "',
'" .
                $authorised_dealer_code_of_bank .
                "',
'" .
                $class_code .
                "',
'" .
                $cb_no .
                "',
'" .
                $cb_date .
                "',
'" .
               $category_of_boe .
                "',
'" .
                $type_of_boe .
                "',
'" .
                $kyc_document .
                "',
'" .
                $kyc_id .
                "',
'" .
                $state_code .
                "',
'" .
                $high_sea_sale .
                "',
'" .
                $ie_code_of_hss .
                "',
 '" .
                $ie_branch_code_of_hss.
                "',
'" .
                $particulars_high_sea_seller_name .
                "',
'" .
                $particulars_high_sea_seller_address .
                "',
'" .
                $use_of_the_first_proviso_under_section_461customs_act1962 .
                "',
'" .
                $request_for_first_check.
                "',                
'" .
                $request_for_urgent_clear_ance_against_temporary_documentation .
                "',
'" .
                $request_for_extension_of_time_limit_as_per_section_48customs_ac .
                "',
'" .
                $reason_in_case_extension_of_time_limit_is_requested .
                "',
'" .
                $country_of_origin .
                "',
'" .
                $country_of_consignment .
                "',
'".
                $name_of_gateway_port .
                "',
'" .
                $gateway_igm_number .
                "',
'" .
                $date_of_entry_inwards_of_gateway_port .
                "',
'" .
                $case_of_crn .
                "',
'".
                $number_of_invoices .
                "',
'" .
                $total_freight .
                "',
'".
                $total_insurance .
                "',
'" .
                $created_at .
                "')";
            $copy_insert_courier_bill_summary = $db1_courier_bill_summary->query(
                $sql_insert_courier_bill_summary
            );
        }
    }

public function inArray_item_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
              //$c= $be_no."-".$igm_no;
     $c= $val['reference_code'];
     
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  
public function item_details(){
        /******************************************************************Start item_details***************************************************************************************/
    //     echo $query_item_details ="SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv,'-',item_details.item_s_no) as reference_code,item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE item_details.created_at >= NOW() - INTERVAL '24 HOURS'";
         echo $query_item_details ="SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv,'-',item_details.item_s_no) as reference_code,item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
        $statement_item_details = $this->db->query($query_item_details);
        $iecwise_item_details = [];
        $result_item_details = $statement_item_details->result_array();
//print_r($result_item_details);exit;
$batchSize = 9000;

        // Loop through the records in batches of 9000
        for (
            $offset = 0;
            $offset < count($result_item_details);
            $offset += $batchSize
        ) {
       foreach ($result_item_details as $str_item_details) {
            $iec_item_details = $str_item_details["iec"];
            $sql_item_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_item_details'";
            $iecwise_item_details = $this->db->query($sql_item_details);
            $iecwise_data_item_details = $iecwise_item_details->result_array();
            $db1_item_details = $this->database_connection(
                $iecwise_data_item_details[0]["lucrative_users_id"]
            );

       //     if (get_magic_quotes_gpc()) {
                $reference_code = addslashes($str_item_details["reference_code"]);
                $item_id = addslashes($str_item_details["item_id"]);
                $invoice_id = addslashes(
                    $str_item_details["invoice_id"]
                );
                $invsn = addslashes(
                    $str_item_details["invsn"]
                );
                $item_s_no = addslashes(
                    $str_item_details["item_s_no"]
                );
                $uqc = addslashes(
                    $str_item_details["uqc"]
                );
                $hs_cd = addslashes(
                    $str_item_details["hs_cd"]
                );
                $description = addslashes(
                    $str_item_details["description"]
                );
                $quantity = addslashes(
                    $str_item_details["quantity"]
                );
                $rate = addslashes(
                    $str_item_details["rate"]
                );
                $value_f_c = addslashes(
                    $str_item_details["value_f_c"]
                );
                $fob_inr = addslashes(
                    $str_item_details["fob_inr"]
                );
                $pmv = addslashes($str_item_details["pmv"]);
                $duty_amt = addslashes(
                    $str_item_details["duty_amt"]
                );
                $cess_rt = addslashes(
                    $str_item_details["cess_rt"]
                );
                $cesamt = addslashes(
                    $str_item_details["cesamt"]
                );
                $dbkclmd = addslashes(
                    $str_item_details["dbkclmd"]
                );
                $igststat = addslashes(
                    $str_item_details["igststat"]
                );
                $igst_value_item = addslashes(
                    $str_item_details["igst_value_item"]
                );
                $igst_amount = addslashes(
                    $str_item_details["igst_amount"]
                );
                $schcod = addslashes(
                    $str_item_details["schcod"]
                );
                $scheme_description = addslashes(
                    $str_item_details["scheme_description"]
                );
                $sqc_msr = addslashes(
                    $str_item_details["sqc_msr"]
                );
                $sqc_uqc = addslashes(
                    $str_item_details["sqc_uqc"]
                );
                $state_of_origin_i = addslashes(
                    $str_item_details["state_of_origin_i"]
                );
                $district_of_origin = addslashes(
                    $str_item_details["district_of_origin"]
                );
                $pt_abroad = addslashes(
                    $str_item_details["pt_abroad"]
                );
                $comp_cess = addslashes(
                    $str_item_details["comp_cess"]
                );
                $end_use = addslashes(
                    $str_item_details["end_use"]
                );
                $fta_benefit_availed = addslashes(
                    $str_item_details["fta_benefit_availed"]
                );
                $reward_benefit = addslashes(
                    $str_item_details["reward_benefit"]
                );
                $third_party_item = addslashes(
                    $str_item_details["third_party_item"]
                );
                $created_at = addslashes(
                    $str_item_details["created_at"]
                );
               $query_item_details =

         /********************checking dupliacte entries d2d***********************/
//$sql_users ="SELECT CONCAT(ship_bill_summary.sb_no,"-",invoice_summary.s_no_inv,"-",item_details.item_s_no) as reference_code, item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id ";
 $sql_users ="SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv,'-',item_details.item_s_no) as reference_code, item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id ";
 
                $iecwise1_users = $db1_item_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                $c= $reference_code;
             
                //skip dupliacte entry     
                $a= $this->inArray_item_details($iecwise_data1_users,$c); // Output - value exists
                if ($a==1) {
                echo "Duplicate";"============";continue;
                }
                else{
         
/***********************************************************************/    
  
            echo $sql_insert_item_details =
                "INSERT INTO `item_details`(`item_id`, `invoice_id`, `invsn`, `item_s_no`, `hs_cd`, `description`, `quantity`, `uqc`, `rate`, `value_f_c`, `fob_inr`, `pmv`, `duty_amt`, `cess_rt`,`cesamt`, `dbkclmd`,
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
 VALUES ('" .
                $item_id .
                "',
 '" .
                $invoice_id .
                "',
 '" .
                $invsn .
                "',
 '" .
                $item_s_no .
                "',
 '" .
                $hs_cd .
                "',
 '" .
                $description .
                "',
 '" .
                $quantity .
                "',
 '" .
                $uqc .
                "',
 '" .
                $rate .
                "',
 '" .
                $value_f_c .
                "',
 '" .
                $fob_inr .
                "',
 '" .
                $pmv .
                "',
 '" .
                $duty_amt .
                "',
 '" .
                $cess_rt .
                "',
 '" .
                $cesamt .
                "',
 '" .
                $dbkclmd .
                "',
 '" .
                $igststat .
                "',
 '" .
                $igst_value_item .
                "',
 '" .
                $igst_amount .
                "',
 '" .
                $schcod .
                "',
 '" .
                $scheme_description .
                "',
 '" .
                $sqc_msr .
                "',
 '" .
                $sqc_uqc .
                "',
 '" .
                $state_of_origin_i .
                "',
 '" .
                $district_of_origin .
                "',
 '" .
                $pt_abroad .
                "',
 '" .
                $comp_cess .
                "',
 '" .
                $end_use .
                "',
 '" .
                $fta_benefit_availed .
                "',
 '" .
                $reward_benefit .
                "',
 '" .
                $third_party_item .
                "',
 '" .
                $created_at .
                "')";
            $copy_insert_item_details = $db1_item_details->query(
                $sql_insert_item_details
            );
        }
       }
    }
        /******************************************************************Start item_details***************************************************************************************/
}
    
    
public function inArray_invoice_and_valuation_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
              //$c= $be_no."-".$igm_no;
     
     $c= $val['s_no']."-".$val['invoice_no']."-".$val['invoice_date'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  
public function invoice_and_valuation_details(){
       echo $query_invoice_and_valuation_details =
            "SELECT invoice_and_valuation_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM invoice_and_valuation_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = invoice_and_valuation_details.boe_id ";
        $statement_invoice_and_valuation_details = $this->db->query($query_invoice_and_valuation_details);
        $iecwise_invoice_and_valuation_details = [];
        $result_invoice_and_valuation_details = $statement_invoice_and_valuation_details->result_array();
     // echo count($result_invoice_and_valuation_details);
     //print_r($result_invoice_and_valuation_details);exit;
$batchSize = 9000;

        // Loop through the records in batches of 9000
        for ($offset = 0;$offset < count($result_invoice_and_valuation_details);$offset += $batchSize) {
        foreach ($result_invoice_and_valuation_details as $str_invoice_and_valuation_details) 
        {
            $iec_invoice_and_valuation_details =$str_invoice_and_valuation_details["iec_no"];
            $sql_invoice_and_valuation_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_invoice_and_valuation_details'";
            $iecwise_invoice_and_valuation_details = $this->db->query(
                $sql_invoice_and_valuation_details
            );
            $iecwise_data_invoice_and_valuation_details = $iecwise_invoice_and_valuation_details->result_array();
            $db1_invoice_and_valuation_details = $this->database_connection(
                $iecwise_data_invoice_and_valuation_details[0][
                    "lucrative_users_id"
                ]
            );
            //if (get_magic_quotes_gpc()) {
                $invoice_id = addslashes(
                    $str_invoice_and_valuation_details["invoice_id"]
                );
                $boe_id = addslashes(
                    $str_invoice_and_valuation_details["boe_id"]
                );
                $s_no = addslashes(
                    $str_invoice_and_valuation_details["s_no"]
                );
                $invoice_no = addslashes(
                    $str_invoice_and_valuation_details["invoice_no"]
                );
                $purchase_order_no = addslashes(
                    $str_invoice_and_valuation_details["purchase_order_no"]
                );
                $lc_no = addslashes(
                    $str_invoice_and_valuation_details["lc_no"]
                );
                $contract_no = addslashes(
                    $str_invoice_and_valuation_details["contract_no"]
                );
                $buyer_s_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "buyer_s_name_and_address"
                    ]
                );
                $seller_s_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "seller_s_name_and_address"
                    ]
                );
                $supplier_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "supplier_name_and_address"
                    ]
                );
                $third_party_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "third_party_name_and_address"
                    ]
                );
                $aeo = addslashes($str_invoice_and_valuation_details["aeo"]);
                $ad_code = addslashes(
                    $str_invoice_and_valuation_details["ad_code"]
                );
                $inv_value = addslashes(
                    $str_invoice_and_valuation_details["inv_value"]
                );
                $freight = addslashes(
                    $str_invoice_and_valuation_details["freight"]
                );
                $insurance = addslashes(
                    $str_invoice_and_valuation_details["insurance"]
                );
                $hss = addslashes($str_invoice_and_valuation_details["hss"]);
                $loading = addslashes(
                    $str_invoice_and_valuation_details["loading"]
                );
                $commn = addslashes(
                    $str_invoice_and_valuation_details["commn"]
                );
                $pay_terms = addslashes(
                    $str_invoice_and_valuation_details["pay_terms"]
                );
                $valuation_method = addslashes(
                    $str_invoice_and_valuation_details["valuation_method"]
                );
                $reltd = addslashes(
                    $str_invoice_and_valuation_details["reltd"]
                );
                $svb_ch = addslashes(
                    $str_invoice_and_valuation_details["svb_ch"]
                );
                $svb_no = addslashes(
                    $str_invoice_and_valuation_details["svb_no"]
                );
                $date = addslashes(
                    $str_invoice_and_valuation_details["date"]
                );
                $loa = addslashes($str_invoice_and_valuation_details["loa"]);
                $cur = addslashes($str_invoice_and_valuation_details["cur"]);
                $term = addslashes(
                    $str_invoice_and_valuation_details["term"]
                );
                $c_and_b = addslashes(
                    $str_invoice_and_valuation_details["c_and_b"]
                );
                $coc = addslashes($str_invoice_and_valuation_details["coc"]);
                $cop = addslashes($str_invoice_and_valuation_details["cop"]);
                $hnd_chg = addslashes(
                    $str_invoice_and_valuation_details["hnd_chg"]
                );
                $g_and_s = addslashes(
                    $str_invoice_and_valuation_details["g_and_s"]
                );
                $doc_ch = addslashes(
                    $str_invoice_and_valuation_details["doc_ch"]
                );
                $coo = addslashes($str_invoice_and_valuation_details["coo"]);
                $r_and_lf = addslashes(
                    $str_invoice_and_valuation_details["r_and_lf"]
                );
                $oth_cost = addslashes(
                    $str_invoice_and_valuation_details["oth_cost"]
                );
                $ld_uld = addslashes(
                    $str_invoice_and_valuation_details["ld_uld"]
                );
                $ws = addslashes($str_invoice_and_valuation_details["ws"]);
                $otc = addslashes($str_invoice_and_valuation_details["otc"]);
                $misc_charge = addslashes(
                    $str_invoice_and_valuation_details["misc_charge"]
                );
                $ass_value = addslashes(
                    $str_invoice_and_valuation_details["ass_value"]
                );
                $invoice_date = addslashes(
                    $str_invoice_and_valuation_details["invoice_date"]
                );
                $purchase_date = addslashes(
                    $str_invoice_and_valuation_details["purchase_order_date"]
                );
                $lc_date = addslashes(
                    $str_invoice_and_valuation_details["lc_date"]
                );
                $contract_date = addslashes(
                    $str_invoice_and_valuation_details["contract_date"]
                );
                $freight_cur = addslashes(
                    $str_invoice_and_valuation_details["freight_cur"]
                );
                $created_at = addslashes(
                    $str_invoice_and_valuation_details["created_at"]
                );
                
                 $date = date("Y-m-d",strtotime($date));
              $invoice_date = date("Y-m-d",strtotime($invoice_date));
               $purchase_date = date("Y-m-d",strtotime($purchase_date));
               $lc_date = date("Y-m-d",strtotime($lc_date));
            $contract_date = date("Y-m-d",strtotime($contract_date));
                
                if(empty($loa)){$loa=0;}
                 if(empty($misc_charge)){$misc_charge=0;}
                  if(empty($ass_value)){$ass_value=0;}
                  
                  
/********************checking dupliacte entries d2d***********************/
       $sql_users ="SELECT invoice_and_valuation_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM invoice_and_valuation_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = invoice_and_valuation_details.boe_id ";

                $iecwise1_users = $db1_invoice_and_valuation_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
                   $c= $s_no."-".$invoice_no."-".$invoice_date;
                    //skip dupliacte entry     
             $a= $this->inArray_invoice_and_valuation_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
 /***********************************************************************/

            echo $sql_insert_invoice_and_valuation_details =
                "INSERT INTO `invoice_and_valuation_details` (`invoice_id`, `boe_id`, `s_no`, `invoice_no`, `purchase_order_no`, `lc_no`, `contract_no`, `buyer_s_name_and_address`, `seller_s_name_and_address`, `supplier_name_and_address`, `third_party_name_and_address`, `aeo`, `ad_code`, `inv_value`, `freight`, `insurance`, `hss`, `loading`, `commn`, `pay_terms`, `valuation_method`, `reltd`, `svb_ch`, `svb_no`, `date`, `loa`, `cur`, `term`, `c_and_b`, `coc`, `cop`, `hnd_chg`, `g_and_s`, `doc_ch`, `coo`, `r_and_lf`, `oth_cost`, `ld_uld`, `ws`, `otc`, `misc_charge`, `ass_value`, `invoice_date`, `purchase_order_date`, `lc_date`, `contract_date`, `freight_cur`, `created_at`) 
VALUES('" .
                $invoice_id .
                "','" .
                $boe_id .
                "','" .
                $s_no .
                "','" .
                $invoice_no .
                "','" .
                $purchase_order_no .
                "','" .
                $lc_no .
                "','" .
                $contract_no .
                "','" .
                $buyer_s_name_and_address .
                "','" .
                $seller_s_name_and_address .
                "','" .
                $supplier_name_and_address .
                "','" .
                $third_party_name_and_address .
                "','" .
                $aeo .
                "','" .
                $ad_code .
                "','" .
                $inv_value .
                "','" .
                $freight .
                "','" .
                $insurance .
                "','" .
                $hss .
                "','" .
                $loading .
                "','" .
                $commn .
                "','" .
                $pay_terms .
                "','" .
                $valuation_method .
                "','" .
                $reltd .
                "','" .
                $svb_ch .
                "','" .
                $svb_no .
                "','" .
                $date .
                "','" .
                $loa .
                "','" .
                $cur .
                "','" .
                $term .
                "','" .
                $c_and_b .
                "','" .
                $coc .
                "','" .
                $cop .
                "','" .
                $hnd_chg .
                "','" .
                $g_and_s .
                "','" .
                $doc_ch .
                "','" .
                $coo .
                "','" .
                $r_and_lf .
                "','" .
                $oth_cost .
                "','" .
                $ld_uld .
                "','" .
                $ws .
                "','" .
                $otc .
                "','" .
                $misc_charge .
                "','" .
                $ass_value .
                "','" .
                $invoice_date .
                "','" .
                $purchase_date .
                "','" .
                $lc_date .
                "','" .
                $contract_date .
                "','" .
                $freight_cur .
                "','" .
                $created_at .
                "')";

            $copy_insert_invoice_and_valuation_details = $db1_invoice_and_valuation_details->query($sql_insert_invoice_and_valuation_details);
        }
      }
    }
        /******************************************************************Start invoice_and_valuation_details***************************************************************************************/
}
    
public function invoice_and_valuation_details_ORIGENAL(){
       echo $query_invoice_and_valuation_details =
            "SELECT invoice_and_valuation_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM invoice_and_valuation_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = invoice_and_valuation_details.boe_id ";
        $statement_invoice_and_valuation_details = $this->db->query($query_invoice_and_valuation_details);
        $iecwise_invoice_and_valuation_details = [];
        $result_invoice_and_valuation_details = $statement_invoice_and_valuation_details->result_array();
       //echo count($result_invoice_and_valuation_details);
     // print_r($result_invoice_and_valuation_details);exit;
$batchSize = 9000;

        // Loop through the records in batches of 9000
        for ($offset = 0;$offset < count($result_invoice_and_valuation_details);$offset += $batchSize) {
        foreach ($result_invoice_and_valuation_details as $str_invoice_and_valuation_details) 
        {
            $iec_invoice_and_valuation_details =
                $str_invoice_and_valuation_details["iec_no"];
            $sql_invoice_and_valuation_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_invoice_and_valuation_details'";
            $iecwise_invoice_and_valuation_details = $this->db->query(
                $sql_invoice_and_valuation_details
            );
            $iecwise_data_invoice_and_valuation_details = $iecwise_invoice_and_valuation_details->result_array();
            $db1_invoice_and_valuation_details = $this->database_connection(
                $iecwise_data_invoice_and_valuation_details[0][
                    "lucrative_users_id"
                ]
            );
            //if (get_magic_quotes_gpc()) {
                $invoice_id = addslashes(
                    $str_invoice_and_valuation_details["invoice_id"]
                );
                $boe_id = addslashes(
                    $str_invoice_and_valuation_details["boe_id"]
                );
                $s_no = addslashes(
                    $str_invoice_and_valuation_details["s_no"]
                );
                $invoice_no = addslashes(
                    $str_invoice_and_valuation_details["invoice_no"]
                );
                $purchase_order_no = addslashes(
                    $str_invoice_and_valuation_details["purchase_order_no"]
                );
                $lc_no = addslashes(
                    $str_invoice_and_valuation_details["lc_no"]
                );
                $contract_no = addslashes(
                    $str_invoice_and_valuation_details["contract_no"]
                );
                $buyer_s_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "buyer_s_name_and_address"
                    ]
                );
                $seller_s_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "seller_s_name_and_address"
                    ]
                );
                $supplier_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "supplier_name_and_address"
                    ]
                );
                $third_party_name_and_address = addslashes(
                    $str_invoice_and_valuation_details[
                        "third_party_name_and_address"
                    ]
                );
                $aeo = addslashes($str_invoice_and_valuation_details["aeo"]);
                $ad_code = addslashes(
                    $str_invoice_and_valuation_details["ad_code"]
                );
                $inv_value = addslashes(
                    $str_invoice_and_valuation_details["inv_value"]
                );
                $freight = addslashes(
                    $str_invoice_and_valuation_details["freight"]
                );
                $insurance = addslashes(
                    $str_invoice_and_valuation_details["insurance"]
                );
                $hss = addslashes($str_invoice_and_valuation_details["hss"]);
                $loading = addslashes(
                    $str_invoice_and_valuation_details["loading"]
                );
                $commn = addslashes(
                    $str_invoice_and_valuation_details["commn"]
                );
                $pay_terms = addslashes(
                    $str_invoice_and_valuation_details["pay_terms"]
                );
                $valuation_method = addslashes(
                    $str_invoice_and_valuation_details["valuation_method"]
                );
                $reltd = addslashes(
                    $str_invoice_and_valuation_details["reltd"]
                );
                $svb_ch = addslashes(
                    $str_invoice_and_valuation_details["svb_ch"]
                );
                $svb_no = addslashes(
                    $str_invoice_and_valuation_details["svb_no"]
                );
                $date = addslashes(
                    $str_invoice_and_valuation_details["date"]
                );
                $loa = addslashes($str_invoice_and_valuation_details["loa"]);
                $cur = addslashes($str_invoice_and_valuation_details["cur"]);
                $term = addslashes(
                    $str_invoice_and_valuation_details["term"]
                );
                $c_and_b = addslashes(
                    $str_invoice_and_valuation_details["c_and_b"]
                );
                $coc = addslashes($str_invoice_and_valuation_details["coc"]);
                $cop = addslashes($str_invoice_and_valuation_details["cop"]);
                $hnd_chg = addslashes(
                    $str_invoice_and_valuation_details["hnd_chg"]
                );
                $g_and_s = addslashes(
                    $str_invoice_and_valuation_details["g_and_s"]
                );
                $doc_ch = addslashes(
                    $str_invoice_and_valuation_details["doc_ch"]
                );
                $coo = addslashes($str_invoice_and_valuation_details["coo"]);
                $r_and_lf = addslashes(
                    $str_invoice_and_valuation_details["r_and_lf"]
                );
                $oth_cost = addslashes(
                    $str_invoice_and_valuation_details["oth_cost"]
                );
                $ld_uld = addslashes(
                    $str_invoice_and_valuation_details["ld_uld"]
                );
                $ws = addslashes($str_invoice_and_valuation_details["ws"]);
                $otc = addslashes($str_invoice_and_valuation_details["otc"]);
                $misc_charge = addslashes(
                    $str_invoice_and_valuation_details["misc_charge"]
                );
                $ass_value = addslashes(
                    $str_invoice_and_valuation_details["ass_value"]
                );
                $invoice_date = addslashes(
                    $str_invoice_and_valuation_details["invoice_date"]
                );
                $purchase_date = addslashes(
                    $str_invoice_and_valuation_details["purchase_order_date"]
                );
                $lc_date = addslashes(
                    $str_invoice_and_valuation_details["lc_date"]
                );
                $contract_date = addslashes(
                    $str_invoice_and_valuation_details["contract_date"]
                );
                $freight_cur = addslashes(
                    $str_invoice_and_valuation_details["freight_cur"]
                );
                $created_at = addslashes(
                    $str_invoice_and_valuation_details["created_at"]
                );
                
                 $date = date("Y-m-d",strtotime($date));
              $invoice_date = date("Y-m-d",strtotime($invoice_date));
               $purchase_date = date("Y-m-d",strtotime($purchase_date));
               $lc_date = date("Y-m-d",strtotime($lc_date));
            $contract_date = date("Y-m-d",strtotime($contract_date));
                
                if(empty($loa)){$loa=0;}
        /*    } else {
                $invoice_id = $str_invoice_and_valuation_details["invoice_id"];
                $boe_id = $str_invoice_and_valuation_details["boe_id"];
                $s_no = $str_invoice_and_valuation_details["s_no"];
                $invoice_no = $str_invoice_and_valuation_details["invoice_no"];
                $purchase_order_no =
                    $str_invoice_and_valuation_details["purchase_order_no"];
                $lc_no = $str_invoice_and_valuation_details["lc_no"];
                $contract_no =
                    $str_invoice_and_valuation_details["contract_no"];
                $buyer_s_name_and_address =
                    $str_invoice_and_valuation_details[
                        "buyer_s_name_and_address"
                    ];
                $seller_s_name_and_address =
                    $str_invoice_and_valuation_details[
                        "seller_s_name_and_address"
                    ];
                $supplier_name_and_address =
                    $str_invoice_and_valuation_details[
                        "supplier_name_and_address"
                    ];
                $third_party_name_and_address =
                    $str_invoice_and_valuation_details[
                        "third_party_name_and_address"
                    ];
                $aeo = $str_invoice_and_valuation_details["aeo"];
                $ad_code = $str_invoice_and_valuation_details["ad_code"];
                $inv_value = $str_invoice_and_valuation_details["inv_value"];
                $freight = $str_invoice_and_valuation_details["freight"];
                $insurance = $str_invoice_and_valuation_details["insurance"];
                $hss = $str_invoice_and_valuation_details["hss"];
                $loading = $str_invoice_and_valuation_details["loading"];
                $commn = $str_invoice_and_valuation_details["commn"];
                $pay_terms = $str_invoice_and_valuation_details["pay_terms"];
                $valuation_method =
                    $str_invoice_and_valuation_details["valuation_method"];
                $reltd = $str_invoice_and_valuation_details["reltd"];
                $svb_ch = $str_invoice_and_valuation_details["svb_ch"];
                $svb_no = $str_invoice_and_valuation_details["svb_no"];
                $date = $str_invoice_and_valuation_details["date"];
                $loa = $str_invoice_and_valuation_details["loa"];
                $cur = $str_invoice_and_valuation_details["cur"];
                $term = $str_invoice_and_valuation_details["term"];
                $c_and_b = $str_invoice_and_valuation_details["c_and_b"];
                $coc = $str_invoice_and_valuation_details["coc"];
                $cop = $str_invoice_and_valuation_details["cop"];
                $hnd_chg = $str_invoice_and_valuation_details["hnd_chg"];
                $g_and_s = $str_invoice_and_valuation_details["g_and_s"];
                $doc_ch = $str_invoice_and_valuation_details["doc_ch"];
                $coo = $str_invoice_and_valuation_details["coo"];
                $r_and_lf = $str_invoice_and_valuation_details["r_and_lf"];
                $oth_cost = $str_invoice_and_valuation_details["oth_cost"];
                $ld_uld = $str_invoice_and_valuation_details["ld_uld"];
                $ws = $str_invoice_and_valuation_details["ws"];
                $otc = $str_invoice_and_valuation_details["otc"];
                $misc_charge =
                    $str_invoice_and_valuation_details["misc_charge"];
                $ass_value = $str_invoice_and_valuation_details["ass_value"];
                $invoice_date =
                    $str_invoice_and_valuation_details["invoice_date"];
                $purchase_date =
                    $str_invoice_and_valuation_details["purchase_date"];
                $lc_date = $str_invoice_and_valuation_details["lc_date"];
                $contract_date =
                    $str_invoice_and_valuation_details["contract_date"];
                $freight_cur =
                    $str_invoice_and_valuation_details["freight_cur"];
                $created_at = $str_invoice_and_valuation_details["created_at"];
            }*/

            echo $sql_insert_invoice_and_valuation_details =
                "INSERT INTO `invoice_and_valuation_details` (`invoice_id`, `boe_id`, `s_no`, `invoice_no`, `purchase_order_no`, `lc_no`, `contract_no`, `buyer_s_name_and_address`, `seller_s_name_and_address`, `supplier_name_and_address`, `third_party_name_and_address`, `aeo`, `ad_code`, `inv_value`, `freight`, `insurance`, `hss`, `loading`, `commn`, `pay_terms`, `valuation_method`, `reltd`, `svb_ch`, `svb_no`, `date`, `loa`, `cur`, `term`, `c_and_b`, `coc`, `cop`, `hnd_chg`, `g_and_s`, `doc_ch`, `coo`, `r_and_lf`, `oth_cost`, `ld_uld`, `ws`, `otc`, `misc_charge`, `ass_value`, `invoice_date`, `purchase_order_date`, `lc_date`, `contract_date`, `freight_cur`, `created_at`) 
VALUES('" .
                $invoice_id .
                "','" .
                $boe_id .
                "','" .
                $s_no .
                "','" .
                $invoice_no .
                "','" .
                $purchase_order_no .
                "','" .
                $lc_no .
                "','" .
                $contract_no .
                "','" .
                $buyer_s_name_and_address .
                "','" .
                $seller_s_name_and_address .
                "','" .
                $supplier_name_and_address .
                "','" .
                $third_party_name_and_address .
                "','" .
                $aeo .
                "','" .
                $ad_code .
                "','" .
                $inv_value .
                "','" .
                $freight .
                "','" .
                $insurance .
                "','" .
                $hss .
                "','" .
                $loading .
                "','" .
                $commn .
                "','" .
                $pay_terms .
                "','" .
                $valuation_method .
                "','" .
                $reltd .
                "','" .
                $svb_ch .
                "','" .
                $svb_no .
                "','" .
                $date .
                "','" .
                $loa .
                "','" .
                $cur .
                "','" .
                $term .
                "','" .
                $c_and_b .
                "','" .
                $coc .
                "','" .
                $cop .
                "','" .
                $hnd_chg .
                "','" .
                $g_and_s .
                "','" .
                $doc_ch .
                "','" .
                $coo .
                "','" .
                $r_and_lf .
                "','" .
                $oth_cost .
                "','" .
                $ld_uld .
                "','" .
                $ws .
                "','" .
                $otc .
                "','" .
                $misc_charge .
                "','" .
                $ass_value .
                "','" .
                $invoice_date .
                "','" .
                $purchase_date .
                "','" .
                $lc_date .
                "','" .
                $contract_date .
                "','" .
                $freight_cur .
                "','" .
                $created_at .
                "')";

            $copy_insert_invoice_and_valuation_details = $db1_invoice_and_valuation_details->query(
                $sql_insert_invoice_and_valuation_details
            );
        }
        }
        /******************************************************************Start invoice_and_valuation_details***************************************************************************************/
    }
    
public function inArray_rodtep_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
              //$c= $be_no."-".$igm_no;
     $c= $val['reference_code']."-".$val['sb_date'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  
   
public function rodtep_details(){
        /******************************************************************Start rodtep_details***************************************************************************************/
//echo $query_rodtep_details ="SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code,rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id,ship_bill_summary.sb_date FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE item_details.created_at >= NOW() - INTERVAL '24 HOURS'";
      
echo $query_rodtep_details =  "SELECT CONCAT(ship_bill_summary.sb_no,'-',rodtep_details.inv_sno, '-', rodtep_details.item_sno) as reference_code,rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id,ship_bill_summary.sb_date FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
        $statement_rodtep_details = $this->db->query($query_rodtep_details);
        $iecwise_rodtep_details = [];
        $result_rodtep_details = $statement_rodtep_details->result_array();
       //echo count($result_rodtep_details);exit;
        //print_r($result_rodtep_details);exit;
$batchSize = 9000;

        // Loop through the records in batches of 9000
        for (
            $offset = 0;
            $offset < count($result_rodtep_details);
            $offset += $batchSize
        ) {
        foreach ($result_rodtep_details as $str_rodtep_details) {
            $iec_rodtep_details = $str_rodtep_details["iec"];
            $sql_rodtep_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_rodtep_details'";
            $iecwise_rodtep_details = $this->db->query($sql_rodtep_details);
            $iecwise_data_rodtep_details = $iecwise_rodtep_details->result_array();
            $db1_rodtep_details = $this->database_connection(
                $iecwise_data_rodtep_details[0]["lucrative_users_id"]
            );
          //  if (get_magic_quotes_gpc()) {
           $reference_code = addslashes($str_rodtep_details["reference_code"]);
                $item_id = addslashes($str_rodtep_details["item_id"]);
                $sb_date = addslashes($str_rodtep_details["sb_date"]);
                $inv_sno = addslashes($str_rodtep_details["inv_sno"]);
                $item_sno = addslashes($str_rodtep_details["item_sno"]);
              ///  $invoice_no = addslashes($str_rodtep_details["invoice_no"]);
                $quantity = addslashes($str_rodtep_details["quantity"]);
                $uqc = addslashes($str_rodtep_details["uqc"]);
                $no_of_units = addslashes($str_rodtep_details["no_of_units"]);
                $value = addslashes(
                    $str_rodtep_details["value"]
                );
         /********************checking dupliacte entries d2d***********************/
 //$sql_users ="SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code,rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_date FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
 $sql_users ="SELECT CONCAT(ship_bill_summary.sb_no,'-',rodtep_details.inv_sno, '-', rodtep_details.item_sno) as reference_code,rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_date FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
        
                $iecwise1_users = $db1_rodtep_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
                $c= $reference_code."-".$sb_date;
                    //skip dupliacte entry     
             $a= $this->inArray_rodtep_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
 /***********************************************************************/
            echo $sql_insert_rodtep_details =
                "INSERT INTO `rodtep_details` (`item_id`, `inv_sno`, `item_sno`, `quantity`, `uqc`, `no_of_units`,`value`) 
VALUES('" .
                $item_id .
                "','" .
                $inv_sno .
                "','" .
                $item_sno .
                "','" .
                $quantity .
                "','" .
                $uqc .
                "','" .
                $no_of_units .
                "','" .
                $value .
                "')";
            $copy_insert_rodtep_details = $db1_rodtep_details->query(
                $sql_insert_rodtep_details
            );
        }
      }
       
        /******************************************************************Start rodtep_details***************************************************************************************/
    }
            //exit;
}
        
public function inArray_jobbing_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
             
     $c= $val['sb_no']."-".$val['be_no']."-".$val['qty_imp'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  

public function jobbing_details(){
        /******************************************************************Start jobbing_details***************************************************************************************/

      echo  $query_jobbing_details =
            "SELECT jobbing_details.*,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_no FROM jobbing_details JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id ";
        $statement_jobbing_details = $this->db->query($query_jobbing_details);
        $iecwise_jobbing_details = [];
        $result_jobbing_details = $statement_jobbing_details->result_array();
        //print_r($result_jobbing_details);exit;

        foreach ($result_jobbing_details as $str_jobbing_details) {
            $iec_jobbing_details = $str_jobbing_details["iec"];
            $sql_jobbing_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_jobbing_details'";
            $iecwise_jobbing_details = $this->db->query($sql_jobbing_details);
            $iecwise_data_jobbing_details = $iecwise_jobbing_details->result_array();
            $db1_insert_jobbing_details = $this->database_connection(
                $iecwise_data_jobbing_details[0]["lucrative_users_id"]
            );
          //  if (get_magic_quotes_gpc()) {
                $jobbing_detail_id = addslashes(
                    $str_jobbing_details["jobbing_detail_id"]
                );
                $sbs_id = addslashes($str_jobbing_details["sbs_id"]);
                $sb_no = addslashes($str_jobbing_details["sb_no"]);
                $be_no = addslashes($str_jobbing_details["be_no"]);
                $be_date = addslashes($str_jobbing_details["be_date"]);
                $port_code_j = addslashes(
                    $str_jobbing_details["port_code_j"]
                );
                $descn_of_imported_goods = addslashes(
                    $str_jobbing_details["descn_of_imported_goods"]
                );
                $qty_imp = addslashes($str_jobbing_details["qty_imp"]);
                $qty_used = addslashes($str_jobbing_details["qty_used"]);
                $created_at = addslashes($str_jobbing_details["created_at"]);

 /********************checking dupliacte entries d2d***********************/
$sql_users =
            "SELECT jobbing_details.*,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_no FROM jobbing_details JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id ";
                
                $iecwise1_users = $db1_insert_jobbing_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
                   $c= $sb_no."-".$be_no."-".$qty_imp;
                    //skip dupliacte entry     
             $a= $this->inArray_jobbing_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
 /***********************************************************************/
         echo   $sql_insert_jobbing_details =
                "INSERT INTO `jobbing_details`(`jobbing_detail_id`, `sbs_id`, `be_no`, `be_date`, `port_code_j`, `descn_of_imported_goods`,  `qty_imp`,  `qty_used`, `created_at`)
 VALUES ('" .
                $jobbing_detail_id .
                "',
 '" .
                $sbs_id .
                "',
 '" .
                $be_no .
                "',
 '" .
                $be_date .
                "',
 '" .
                $port_code_j .
                "',
 '" .
                $descn_of_imported_goods .
                "',
 '" .
                $qty_imp .
                "',
 '" .
                $qty_used .
                "',
 '" .
                $created_at .
                "')";
            $copy_insert_jobbing_details = $db1_insert_jobbing_details->query(
                $sql_insert_jobbing_details
            );
        }
      }
        /******************************************************************Start jobbing_details***************************************************************************************/
}
public function inArray_ship_bill_summary($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
             
     $c= $val['sb_no']."-".$val['sb_date'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  
   
public function ship_bill_summary(){
		/******************************************************************Start ship_bill_summary***************************************************************************************/
		$query_ship_bill_summary = "SELECT * FROM ship_bill_summary";

	//	$query_ship_bill_summary = "SELECT * FROM ship_bill_summary";
		$statement_ship_bill_summary = $this->db->query(
			$query_ship_bill_summary
		);
		$iecwise_ship_bill_summary = [];
		$result_ship_bill_summary = $statement_ship_bill_summary->result_array();

		foreach ($result_ship_bill_summary as $str_ship_bill_summary) {
			$iec_ship_bill_summary = $str_ship_bill_summary["iec"];
			$sql_ship_bill_summary = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_ship_bill_summary'";
			$iecwise_ship_bill_summary = $this->db->query(
				$sql_ship_bill_summary
			);
			$iecwise_data_ship_bill_summary = $iecwise_ship_bill_summary->result_array();
			$db1_ship_bill_summary = $this->database_connection(
				$iecwise_data_ship_bill_summary[0]["lucrative_users_id"]
			);
		//if (get_magic_quotes_gpc()) {
     			
		echo		$sbs_id = addslashes($str_ship_bill_summary["sbs_id"]);
				$sb_file_status_id = addslashes($str_ship_bill_summary["sb_file_status_id"]);
				$invoice_title = addslashes($str_ship_bill_summary["invoice_title"]);
				$port_code = addslashes($str_ship_bill_summary["port_code"]);
				$sb_no = addslashes($str_ship_bill_summary["sb_no"]);
				$sb_date = addslashes($str_ship_bill_summary["sb_date"]);
				$iec = addslashes($str_ship_bill_summary["iec"]);
				$br = addslashes($str_ship_bill_summary["br"]);
				$iec_br = addslashes($str_ship_bill_summary["iec_br"]);
				$gstin = addslashes($str_ship_bill_summary["gstin"]);
				$type = addslashes($str_ship_bill_summary["type"]);
				$cb_code = addslashes($str_ship_bill_summary["cb_code"]);
				$inv_nos = addslashes($str_ship_bill_summary["inv_nos"]);
				$item_no = addslashes($str_ship_bill_summary["item_no"]);
				$cont_no = addslashes($str_ship_bill_summary["cont_no"]);
				$address = addslashes($str_ship_bill_summary["address"]);
				$pkg = addslashes($str_ship_bill_summary["pkg"]);
				$g_wt_unit = addslashes($str_ship_bill_summary["g_wt_unit"]);
				$g_wt_value = addslashes($str_ship_bill_summary["g_wt_value"]);
				$mode = addslashes($str_ship_bill_summary["mode"]);
				$assess = addslashes($str_ship_bill_summary["assess"]);
				$exmn = addslashes($str_ship_bill_summary["exmn"]);
				$jobbing = addslashes($str_ship_bill_summary["jobbing"]);
				$meis = addslashes($str_ship_bill_summary["meis"]);
				$dbk = addslashes($str_ship_bill_summary["dbk"]);
				$rodtp = addslashes($str_ship_bill_summary["rodtp"]);
				$deec_dfia = addslashes($str_ship_bill_summary["deec_dfia"]);
				$dfrc = addslashes($str_ship_bill_summary["dfrc"]);
				$reexp = addslashes($str_ship_bill_summary["reexp"]);
				$lut = addslashes($str_ship_bill_summary["lut"]);
				$port_of_loading = addslashes($str_ship_bill_summary["port_of_loading"]);
				$country_of_finaldestination = addslashes($str_ship_bill_summary["country_of_finaldestination"]);
				$state_of_origin = addslashes($str_ship_bill_summary["state_of_origin"]);
				$port_of_finaldestination = addslashes($str_ship_bill_summary["port_of_finaldestination"]);
				$port_of_discharge = addslashes($str_ship_bill_summary["port_of_discharge"]);
				$country_of_discharge = addslashes($str_ship_bill_summary["country_of_discharge"]);
				$exporter_name_and_address = addslashes($str_ship_bill_summary["exporter_name_and_address"]);
				$consignee_name_and_address = addslashes($str_ship_bill_summary["consignee_name_and_address"]);
				$declarant_type = addslashes($str_ship_bill_summary["declarant_type"]);
				$ad_code = addslashes($str_ship_bill_summary["ad_code"]);
				$gstin_type_ = addslashes($str_ship_bill_summary["gstin_type_"]);
				$rbi_waiver_no_and_dt = addslashes($str_ship_bill_summary["rbi_waiver_no_and_dt"]);
				$forex_bank_account_no = addslashes($str_ship_bill_summary["forex_bank_account_no"]);
				$cb_name = addslashes($str_ship_bill_summary["cb_name"]);
				$dbk_bank_account_no = addslashes($str_ship_bill_summary["dbk_bank_account_no"]);
				$aeo = addslashes($str_ship_bill_summary["aeo"]);
				$ifsc_code = addslashes($str_ship_bill_summary["ifsc_code"]);
				$fob_value_sum = addslashes($str_ship_bill_summary["fob_value_sum"]);
				$freight = addslashes($str_ship_bill_summary["freight"]);
				$insurance = addslashes($str_ship_bill_summary["insurance"]);
				$discount = addslashes($str_ship_bill_summary["discount"]);
				$com = addslashes($str_ship_bill_summary["com"]);
				$deduction = addslashes($str_ship_bill_summary["deduction"]);
				$p_c = addslashes($str_ship_bill_summary["p_c"]);
				$duty = addslashes($str_ship_bill_summary["duty"]);
				$cess = addslashes($str_ship_bill_summary["cess"]);
				$dbk_claim = addslashes($str_ship_bill_summary["dbk_claim"]);
				$igst_amt = addslashes($str_ship_bill_summary["igst_amt"]);
				$cess_amt = addslashes($str_ship_bill_summary["cess_amt"]);
				$igst_value = addslashes($str_ship_bill_summary["igst_value"]);
				$rodtep_amt = addslashes($str_ship_bill_summary["rodtep_amt"]);
				$rosctl_amt = addslashes($str_ship_bill_summary["rosctl_amt"]);
				$mawb_no = addslashes($str_ship_bill_summary["mawb_no"]);
			   // $shawb_no = addslashes($str_ship_bill_summary["shawb_no"]);
				$mawb_dt = addslashes($str_ship_bill_summary["mawb_dt"]);
				$hawb_no = addslashes($str_ship_bill_summary["hawb_no"]);
				$hawb_dt = addslashes($str_ship_bill_summary["hawb_dt"]);
				$noc = addslashes($str_ship_bill_summary["noc"]);
				$cin_no = addslashes($str_ship_bill_summary["cin_no"]);
				$cin_dt = addslashes($str_ship_bill_summary["cin_dt"]);
				$cin_site_id = addslashes($str_ship_bill_summary["cin_site_id"]);
				$seal_type = addslashes($str_ship_bill_summary["seal_type"]);
				$nature_of_cargo = addslashes($str_ship_bill_summary["nature_of_cargo"]);
				$no_of_packets = addslashes($str_ship_bill_summary["no_of_packets"]);
				$no_of_containers = addslashes($str_ship_bill_summary["no_of_containers"]);
				$loose_packets = addslashes($str_ship_bill_summary["loose_packets"]);
				$marks_and_numbers = addslashes($str_ship_bill_summary["marks_and_numbers"]);
				$submission_date = addslashes($str_ship_bill_summary["submission_date"]);
				$assessment_date = addslashes($str_ship_bill_summary["assessment_date"]);
				$examination_date = addslashes($str_ship_bill_summary["examination_date"]);
				$leo_date = addslashes($str_ship_bill_summary["leo_date"]);
				$submission_time = addslashes($str_ship_bill_summary["submission_time"]);
				$assessment_time = addslashes($str_ship_bill_summary["assessment_time"]);
				$examination_time = addslashes($str_ship_bill_summary["examination_time"]);
				$leo_time = addslashes($str_ship_bill_summary["leo_time"]);
				$leo_no = addslashes($str_ship_bill_summary["leo_no"]);
				$leo_dt = addslashes($str_ship_bill_summary["leo_dt"]);
				$brc_realisation_date = addslashes($str_ship_bill_summary["brc_realisation_date"]);
				$created_at = addslashes($str_ship_bill_summary["created_at"]);
				
				
				    
		 /********************checking dupliacte entries d2d***********************/
	$sql_users = "SELECT * FROM ship_bill_summary";
       	//$sql_users = "SELECT * FROM ship_bill_summary";
      
                $iecwise1_users = $db1_ship_bill_summary->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
                   $c= $sb_no."-".$sb_date;
                    //skip dupliacte entry     
             $a= $this->inArray_ship_bill_summary($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
 /***********************************************************************/
		echo	$sql_insert_ship_bill_summary = "INSERT INTO `ship_bill_summary` (`sbs_id`, `sb_file_status_id`, `invoice_title`, `port_code`, `sb_no`, `sb_date`, `iec`, `br`, `iec_br`, `gstin`, `type`, `cb_code`, `inv_nos`, `item_no`, `cont_no`, `address`, `pkg`, `g_wt_unit`, `g_wt_value`, `mode`, `assess`, `exmn`, `jobbing`, `meis`, `dbk`, `rodtp`, `deec_dfia`, `dfrc`, `reexp`, `lut`, `port_of_loading`, `country_of_finaldestination`, `state_of_origin`, `port_of_finaldestination`, `port_of_discharge`, `country_of_discharge`, `exporter_name_and_address`, `consignee_name_and_address`, `declarant_type`, `ad_code`, `gstin_type_`, `rbi_waiver_no_and_dt`, `forex_bank_account_no`, `cb_name`, `dbk_bank_account_no`, `aeo`, `ifsc_code`, `fob_value_sum`, `freight`, `insurance`, `discount`, `com`, `deduction`, `p_c`, `duty`, `cess`, `dbk_claim`, `igst_amt`, `cess_amt`, `igst_value`, `rodtep_amt`, `rosctl_amt`, `mawb_no`, `mawb_dt`, `hawb_no`, `hawb_dt`, `noc`, `cin_no`, `cin_dt`, `cin_site_id`, `seal_type`, `nature_of_cargo`, `no_of_packets`, `no_of_containers`, `loose_packets`, `marks_and_numbers`, `submission_date`, `assessment_date`, `examination_date`, `leo_date`, `submission_time`, `assessment_time`, `examination_time`, `leo_time`, `leo_no`, `leo_dt`, `brc_realisation_date`, `created_at`) VALUES
('"
				. $sbs_id
				. "','"
				. $sb_file_status_id
				. "','"
				. $invoice_title
				. "','"
				. $port_code
				. "','"
				. $sb_no
				. "','"
				. $sb_date
				. "','"
				. $iec
				. "','"
				. $br
				. "','"
				. $iec_br
				. "','"
				. $gstin
				. "','"
				. $type
				. "','"
				. $cb_code
				. "','"
				. $inv_nos
				. "','"
				. $item_no
				. "','"
				. $cont_no
				. "','"
				. $address
				. "','"
				. $pkg
				. "','"
				. $g_wt_unit
				. "','"
				. $g_wt_value
				. "','"
				. $mode
				. "','"
				. $assess
				. "','"
				. $exmn
				. "','"
				. $jobbing
				. "','"
				. $meis
				. "','"
				. $dbk
				. "','"
				. $rodtp
				. "','"
				. $deec_dfia
				. "','"
				. $dfrc
				. "','"
				. $reexp
				. "','"
				. $lut
				. "','"
				. $port_of_loading
				. "','"
				. $country_of_finaldestination
				. "','"
				. $state_of_origin
				. "','"
				. $port_of_finaldestination
				. "','"
				. $port_of_discharge
				. "','"
				. $country_of_discharge
				. "','"
				. $exporter_name_and_address
				. "','"
				. $consignee_name_and_address
				. "','"
				. $declarant_type
				. "','"
				. $ad_code
				. "','"
				. $gstin_type_
				. "','"
				. $rbi_waiver_no_and_dt
				. "','"
				. $forex_bank_account_no
				. "','"
				. $cb_name
				. "','"
				. $dbk_bank_account_no
				. "','"
				. $aeo
				. "','"
				. $ifsc_code
				. "','"
				. $fob_value_sum
				. "','"
				. $freight
				. "','"
				. $insurance
				. "','"
				. $discount
				. "','"
				. $com
				. "','"
				. $deduction
				. "','"
				. $p_c
				. "','"
				. $duty
				. "','"
				. $cess
				. "','"
				. $dbk_claim
				. "','"
				. $igst_amt
				. "','"
				. $cess_amt
				. "','"
				. $igst_value
				. "','"
				. $rodtep_amt
				. "','"
				. $rosctl_amt
				. "','"
				. $mawb_no
				. "','"
				. $mawb_dt
				. "','"
				. $hawb_no
				. "','"
				. $hawb_dt
				. "','"
				. $noc
				. "','"
				. $cin_no
				. "','"
				. $cin_dt
				. "','"
				. $cin_site_id
				. "','"
				. $seal_type
				. "','"
				. $nature_of_cargo
				. "','"
				. $no_of_packets
				. "','"
				. $no_of_containers
				. "','"
				. $loose_packets
				. "','"
				. $marks_and_numbers
				. "','"
				. $submission_date
				. "','"
				. $assessment_date
				. "','"
				. $examination_date
				. "','"
				. $leo_date
				. "','"
				. $submission_time
				. "','"
				. $assessment_time
				. "','"
				. $examination_time
				. "','"
				. $leo_time
				. "','"
				. $leo_no
				. "','"
				. $leo_dt
				. "','"
				. $brc_realisation_date
				. "','"
				. $created_at
				. "')";
			$copy_insert_ship_bill_summary = $db1_ship_bill_summary->query(
				$sql_insert_ship_bill_summary
			);
		}
	}
/******************************************************************Start ship_bill_summary***************************************************************************************/
}
	
public function inArray_duties_and_additional_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['reference_code']."-".$val['be_no']."-".$val['be_date'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  
    
    
public function duties_and_additional_details(){
        /******************************************************************Start duties_and_additional_details***************************************************************************************/

        $query_duties_and_additional_details = "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details 
        LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
        LEFT JOIN invoice_and_valuation_details
                    ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id";

        $statement_duties_and_additional_details = $this->db->query(
            $query_duties_and_additional_details
        );
        $iecwise_duties_and_additional_details = [];
        $result_duties_and_additional_details = $statement_duties_and_additional_details->result_array();
        //echo count($result_duties_and_additional_details);exit;
        $batchSize = 9000;

        // Loop through the records in batches of 9000
        for (
            $offset = 0;
            $offset < count($result_duties_and_additional_details);
            $offset += $batchSize
        ) {
        foreach (
            $result_duties_and_additional_details
            as $str_duties_and_additional_details
        ) {
            $iec_no = $str_duties_and_additional_details['iec_no'];
           echo $sql_duties_and_additional_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_no%'";
            $iecwise_duties_and_additional_details = $this->db->query(
                $sql_duties_and_additional_details
            );
            $iecwise_data_duties_and_additional_details = $iecwise_duties_and_additional_details->result_array();
            $db1_duties_and_additional_details = $this->database_connection(
                $iecwise_data_duties_and_additional_details[0][
                    "lucrative_users_id"
                ]
            );
          //  if (get_magic_quotes_gpc()) {
                $reference_code = addslashes(
                    $str_duties_and_additional_details["reference_code"]
                );
                 $be_no = addslashes(
                    $str_duties_and_additional_details["be_no"]
                );
                 $be_date = addslashes(
                    $str_duties_and_additional_details["be_date"]
                );
                $boe_id = addslashes(
                    $str_duties_and_additional_details["boe_id"]
                );
                $invoice_id = addslashes(
                    $str_duties_and_additional_details["invoice_id"]
                );
                $duties_id = addslashes(
                    $str_duties_and_additional_details["duties_id"]
                );
                $s_no = addslashes(
                    $str_duties_and_additional_details["s_no"]
                );
                $cth = addslashes($str_duties_and_additional_details["cth"]);
                $description = addslashes(
                    $str_duties_and_additional_details["description"]
                );
                $unit_price = addslashes(
                    $str_duties_and_additional_details["unit_price"]
                );
                $quantity = addslashes(
                    $str_duties_and_additional_details["quantity"]
                );
                $uqc = addslashes($str_duties_and_additional_details["uqc"]);
                $amount = addslashes(
                    $str_duties_and_additional_details["amount"]
                );
                $invsno = addslashes(
                    $str_duties_and_additional_details["invsno"]
                );
                $itemsn = addslashes(
                    $str_duties_and_additional_details["itemsn"]
                );
                $cth_item_detail = addslashes(
                    $str_duties_and_additional_details["cth_item_detail"]
                );
                $ceth = addslashes(
                    $str_duties_and_additional_details["ceth"]
                );
                $item_description = addslashes(
                    $str_duties_and_additional_details["item_description"]
                );
                $fs = addslashes($str_duties_and_additional_details["fs"]);
                $pq = addslashes($str_duties_and_additional_details["pq"]);
                $dc = addslashes($str_duties_and_additional_details["dc"]);
                $wc = addslashes($str_duties_and_additional_details["wc"]);
                $aq = addslashes($str_duties_and_additional_details["aq"]);
                $upi = addslashes($str_duties_and_additional_details["upi"]);
                $coo = addslashes($str_duties_and_additional_details["coo"]);
                $c_qty = addslashes(
                    $str_duties_and_additional_details["c_qty"]
                );
                $c_uqc = addslashes(
                    $str_duties_and_additional_details["c_uqc"]
                );
                $s_qty = addslashes(
                    $str_duties_and_additional_details["s_qty"]
                );
                $s_uqc = addslashes(
                    $str_duties_and_additional_details["s_uqc"]
                );
                $sch = addslashes($str_duties_and_additional_details["sch"]);
                $stdn_pr = addslashes(
                    $str_duties_and_additional_details["stdn_pr"]
                );
                $rsp = addslashes($str_duties_and_additional_details["rsp"]);
                $reimp = addslashes(
                    $str_duties_and_additional_details["reimp"]
                );
                $prov = addslashes(
                    $str_duties_and_additional_details["prov"]
                );
                $end_use = addslashes(
                    $str_duties_and_additional_details["end_use"]
                );
                $prodn = addslashes(
                    $str_duties_and_additional_details["prodn"]
                );
                $cntrl = addslashes(
                    $str_duties_and_additional_details["cntrl"]
                );
                $qualfr = addslashes(
                    $str_duties_and_additional_details["qualfr"]
                );
                $contnt = addslashes(
                    $str_duties_and_additional_details["contnt"]
                );
                $stmnt = addslashes(
                    $str_duties_and_additional_details["stmnt"]
                );
                $sup_docs = addslashes(
                    $str_duties_and_additional_details["sup_docs"]
                );
                $assess_value = addslashes(
                    $str_duties_and_additional_details["assess_value"]
                );
                $total_duty = addslashes(
                    $str_duties_and_additional_details["total_duty"]
                );
                $bcd_notn_no = addslashes(
                    $str_duties_and_additional_details["bcd_notn_no"]
                );
                $bcd_notn_sno = addslashes(
                    $str_duties_and_additional_details["bcd_notn_sno"]
                );
                $bcd_rate = addslashes(
                    $str_duties_and_additional_details["bcd_rate"]
                );
                $bcd_amount = addslashes(
                    $str_duties_and_additional_details["bcd_amount"]
                );
                $bcd_duty_fg = addslashes(
                    $str_duties_and_additional_details["bcd_duty_fg"]
                );
                $acd_notn_no = addslashes(
                    $str_duties_and_additional_details["acd_notn_no"]
                );
                $acd_notn_sno = addslashes(
                    $str_duties_and_additional_details["acd_notn_sno"]
                );
                $acd_rate = addslashes(
                    $str_duties_and_additional_details["acd_rate"]
                );
                $acd_amount = addslashes(
                    $str_duties_and_additional_details["acd_amount"]
                );
                $acd_duty_fg = addslashes(
                    $str_duties_and_additional_details["acd_duty_fg"]
                );
                $sws_notn_no = addslashes(
                    $str_duties_and_additional_details["sws_notn_no"]
                );
                $sws_notn_sno = addslashes(
                    $str_duties_and_additional_details["sws_notn_sno"]
                );
                $sws_rate = addslashes(
                    $str_duties_and_additional_details["sws_rate"]
                );
                $sws_amount = addslashes(
                    $str_duties_and_additional_details["sws_amount"]
                );
                $sws_duty_fg = addslashes(
                    $str_duties_and_additional_details["sws_duty_fg"]
                );
                $sad_notn_no = addslashes(
                    $str_duties_and_additional_details["sad_notn_no"]
                );
                $sad_notn_sno = addslashes(
                    $str_duties_and_additional_details["sad_notn_sno"]
                );
                $sad_rate = addslashes(
                    $str_duties_and_additional_details["sad_rate"]
                );
                $sad_amount = addslashes(
                    $str_duties_and_additional_details["sad_amount"]
                );
                $sad_duty_fg = addslashes(
                    $str_duties_and_additional_details["sad_duty_fg"]
                );
                $igst_notn_no = addslashes(
                    $str_duties_and_additional_details["igst_notn_no"]
                );
                $igst_notn_sno = addslashes(
                    $str_duties_and_additional_details["igst_notn_sno"]
                );
                $igst_rate = addslashes(
                    $str_duties_and_additional_details["igst_rate"]
                );
                $igst_amount = addslashes(
                    $str_duties_and_additional_details["igst_amount"]
                );
                $igst_duty_fg = addslashes(
                    $str_duties_and_additional_details["igst_duty_fg"]
                );
                $g_cess_notn_no = addslashes(
                    $str_duties_and_additional_details["g_cess_notn_no"]
                );
                $g_cess_notn_sno = addslashes(
                    $str_duties_and_additional_details["g_cess_notn_sno"]
                );
                $g_cess_rate = addslashes(
                    $str_duties_and_additional_details["g_cess_rate"]
                );
                $g_cess_amount = addslashes(
                    $str_duties_and_additional_details["g_cess_amount"]
                );
                $g_cess_duty_fg = addslashes(
                    $str_duties_and_additional_details["g_cess_duty_fg"]
                );
                $add_notn_no = addslashes(
                    $str_duties_and_additional_details["add_notn_no"]
                );
                $add_notn_sno = addslashes(
                    $str_duties_and_additional_details["add_notn_sno"]
                );
                $add_rate = addslashes(
                    $str_duties_and_additional_details["add_rate"]
                );
                $add_amount = addslashes(
                    $str_duties_and_additional_details["add_amount"]
                );
                $add_duty_fg = addslashes(
                    $str_duties_and_additional_details["add_duty_fg"]
                );
                $cvd_notn_no = addslashes(
                    $str_duties_and_additional_details["cvd_notn_no"]
                );
                $cvd_notn_sno = addslashes(
                    $str_duties_and_additional_details["cvd_notn_sno"]
                );
                $cvd_rate = addslashes(
                    $str_duties_and_additional_details["cvd_rate"]
                );
                $cvd_amount = addslashes(
                    $str_duties_and_additional_details["cvd_amount"]
                );
                $cvd_duty_fg = addslashes(
                    $str_duties_and_additional_details["cvd_duty_fg"]
                );
                $sg_notn_no = addslashes(
                    $str_duties_and_additional_details["sg_notn_no"]
                );
                $sg_notn_sno = addslashes(
                    $str_duties_and_additional_details["sg_notn_sno"]
                );
                $sg_rate = addslashes(
                    $str_duties_and_additional_details["sg_rate"]
                );
                $sg_amount = addslashes(
                    $str_duties_and_additional_details["sg_amount"]
                );
                $sg_duty_fg = addslashes(
                    $str_duties_and_additional_details["sg_duty_fg"]
                );
                $t_value_notn_no = addslashes(
                    $str_duties_and_additional_details["t_value_notn_no"]
                );
                $t_value_notn_sno = addslashes(
                    $str_duties_and_additional_details["t_value_notn_sno"]
                );
                $t_value_rate = addslashes(
                    $str_duties_and_additional_details["t_value_rate"]
                );
                $t_value_amount = addslashes(
                    $str_duties_and_additional_details["t_value_amount"]
                );
                $t_value_duty_fg = addslashes(
                    $str_duties_and_additional_details["t_value_duty_fg"]
                );
                $sp_excd_notn_no = addslashes(
                    $str_duties_and_additional_details["sp_excd_notn_no"]
                );
                $sp_excd_notn_sno = addslashes(
                    $str_duties_and_additional_details["sp_excd_notn_sno"]
                );
                $sp_excd_rate = addslashes(
                    $str_duties_and_additional_details["sp_excd_rate"]
                );
                $sp_excd_amount = addslashes(
                    $str_duties_and_additional_details["sp_excd_amount"]
                );
                $sp_excd_duty_fg = addslashes(
                    $str_duties_and_additional_details["sp_excd_duty_fg"]
                );
                $chcess_notn_no = addslashes(
                    $str_duties_and_additional_details["chcess_notn_no"]
                );
                $chcess_notn_sno = addslashes(
                    $str_duties_and_additional_details["chcess_notn_sno"]
                );
                $chcess_rate = addslashes(
                    $str_duties_and_additional_details["chcess_rate"]
                );
                $chcess_amount = addslashes(
                    $str_duties_and_additional_details["chcess_amount"]
                );
                $chcess_duty_fg = addslashes(
                    $str_duties_and_additional_details["chcess_duty_fg"]
                );
                $tta_notn_no = addslashes(
                    $str_duties_and_additional_details["tta_notn_no"]
                );
                $tta_notn_sno = addslashes(
                    $str_duties_and_additional_details["tta_notn_sno"]
                );
                $tta_rate = addslashes(
                    $str_duties_and_additional_details["tta_rate"]
                );
                $tta_amount = addslashes(
                    $str_duties_and_additional_details["tta_amount"]
                );
                $tta_duty_fg = addslashes(
                    $str_duties_and_additional_details["tta_duty_fg"]
                );
                $cess_notn_no = addslashes(
                    $str_duties_and_additional_details["cess_notn_no"]
                );
                $cess_notn_sno = addslashes(
                    $str_duties_and_additional_details["cess_notn_sno"]
                );
                $cess_rate = addslashes(
                    $str_duties_and_additional_details["cess_rate"]
                );
                $cess_amount = addslashes(
                    $str_duties_and_additional_details["cess_amount"]
                );
                $cess_duty_fg = addslashes(
                    $str_duties_and_additional_details["cess_duty_fg"]
                );
                $caidc_cvd_edc_notn_no = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_notn_no"]
                );
                $caidc_cvd_edc_notn_sno = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_notn_sno"]
                );
                $caidc_cvd_edc_rate = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_rate"]
                );
                $caidc_cvd_edc_amount = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_amount"]
                );
                $caidc_cvd_edc_duty_fg = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_duty_fg"]
                );
                $eaidc_cvd_hec_notn_no = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_notn_no"]
                );
                $eaidc_cvd_hec_notn_sno = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_notn_sno"]
                );
                $eaidc_cvd_hec_rate = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_rate"]
                );
                $eaidc_cvd_hec_amount = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_amount"]
                );
                $eaidc_cvd_hec_duty_fg = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_duty_fg"]
                );
                $cus_edc_notn_no = addslashes(
                    $str_duties_and_additional_details["cus_edc_notn_no"]
                );
                $cus_edc_notn_sno = addslashes(
                    $str_duties_and_additional_details["cus_edc_notn_sno"]
                );
                $cus_edc_rate = addslashes(
                    $str_duties_and_additional_details["cus_edc_rate"]
                );
                $cus_edc_amount = addslashes(
                    $str_duties_and_additional_details["cus_edc_amount"]
                );
                $cus_edc_duty_fg = addslashes(
                    $str_duties_and_additional_details["cus_edc_duty_fg"]
                );
                $cus_hec_notn_no = addslashes(
                    $str_duties_and_additional_details["cus_hec_notn_no"]
                );
                $cus_hec_notn_sno = addslashes(
                    $str_duties_and_additional_details["cus_hec_notn_sno"]
                );
                $cus_hec_rate = addslashes(
                    $str_duties_and_additional_details["cus_hec_rate"]
                );
                $cus_hec_amount = addslashes(
                    $str_duties_and_additional_details["cus_hec_amount"]
                );
                $cus_hec_duty_fg = addslashes(
                    $str_duties_and_additional_details["cus_hec_duty_fg"]
                );
                $ncd_notn_no = addslashes(
                    $str_duties_and_additional_details["ncd_notn_no"]
                );
                $ncd_notn_sno = addslashes(
                    $str_duties_and_additional_details["ncd_notn_sno"]
                );
                $ncd_rate = addslashes(
                    $str_duties_and_additional_details["ncd_rate"]
                );
                $ncd_amount = addslashes(
                    $str_duties_and_additional_details["ncd_amount"]
                );
                $ncd_duty_fg = addslashes(
                    $str_duties_and_additional_details["ncd_duty_fg"]
                );
                $aggr_notn_no = addslashes(
                    $str_duties_and_additional_details["aggr_notn_no"]
                );
                $aggr_notn_sno = addslashes(
                    $str_duties_and_additional_details["aggr_notn_sno"]
                );
                $aggr_rate = addslashes(
                    $str_duties_and_additional_details["aggr_rate"]
                );
                $aggr_amount = addslashes(
                    $str_duties_and_additional_details["aggr_amount"]
                );
                $aggr_duty_fg = addslashes(
                    $str_duties_and_additional_details["aggr_duty_fg"]
                );
                $invsno_add_details = addslashes(
                    $str_duties_and_additional_details["invsno_add_details"]
                );
                $itmsno_add_details = addslashes(
                    $str_duties_and_additional_details["itmsno_add_details"]
                );
                $refno = addslashes(
                    $str_duties_and_additional_details["refno"]
                );
                $refdt = addslashes(
                    $str_duties_and_additional_details["refdt"]
                );
                $prtcd_svb_d = addslashes(
                    $str_duties_and_additional_details["prtcd_svb_d"]
                );
                $lab = addslashes($str_duties_and_additional_details["lab"]);
                $pf = addslashes($str_duties_and_additional_details["pf"]);
                $load_date = addslashes(
                    $str_duties_and_additional_details["load_date"]
                );
                $pf_ = addslashes($str_duties_and_additional_details["pf_"]);
                $beno = addslashes(
                    $str_duties_and_additional_details["beno"]
                );
                $bedate = addslashes(
                    $str_duties_and_additional_details["bedate"]
                );
                $prtcd = addslashes(
                    $str_duties_and_additional_details["prtcd"]
                );
                $unitprice = addslashes(
                    $str_duties_and_additional_details["unitprice"]
                );
                $currency_code = addslashes(
                    $str_duties_and_additional_details["currency_code"]
                );
                $frt = addslashes($str_duties_and_additional_details["frt"]);
                $ins = addslashes($str_duties_and_additional_details["ins"]);
                $duty = addslashes(
                    $str_duties_and_additional_details["duty"]
                );
                $sb_no = addslashes(
                    $str_duties_and_additional_details["sb_no"]
                );
                $sb_dt = addslashes(
                    $str_duties_and_additional_details["sb_dt"]
                );
                $portcd = addslashes(
                    $str_duties_and_additional_details["portcd"]
                );
                $sinv = addslashes(
                    $str_duties_and_additional_details["sinv"]
                );
                $sitemn = addslashes(
                    $str_duties_and_additional_details["sitemn"]
                );
                $type = addslashes(
                    $str_duties_and_additional_details["type"]
                );
                $manufact_cd = addslashes(
                    $str_duties_and_additional_details["manufact_cd"]
                );
                $source_cy = addslashes(
                    $str_duties_and_additional_details["source_cy"]
                );
                $trans_cy = addslashes(
                    $str_duties_and_additional_details["trans_cy"]
                );
                $address = addslashes(
                    $str_duties_and_additional_details["address"]
                );
                $accessory_item_details = addslashes(
                    $str_duties_and_additional_details["accessory_item_details"]
                );
                $slno = addslashes(
                    $str_duties_and_additional_details["slno"]
                );
                $notno = addslashes(
                    $str_duties_and_additional_details["notno"]
                );
                $created_at = addslashes(
                    $str_duties_and_additional_details["created_at"]
                );
                
                
       		 /********************checking dupliacte entries d2d***********************/
          echo  $sql_users = "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details 
        LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
        LEFT JOIN invoice_and_valuation_details
                    ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id";
         
                $iecwise1_users = $db1_duties_and_additional_details->query($sql_users);
                $rowusers = $iecwise1_users->fetch_assoc();
              //  print_r($rowusers);exit;
                $iecwise_data1_users = array();
                
//if($iecwise1_users !== FALSE && $iecwise1_users->num_rows() > 0){
    //$data = $query->result_array();

                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
// }                  
 $c= $reference_code."-".$be_no."-".$be_date;
                    //skip dupliacte entry     
             $a= $this->inArray_duties_and_additional_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             
 /***********************************************************************/
           echo $sql_insert_duties_and_additional_details =
                "INSERT INTO `duties_and_additional_details` (boe_id, invoice_id, duties_id, s_no, cth, description, unit_price, quantity, uqc, amount, invsno, itemsn, cth_item_detail, ceth, item_description, fs, pq, dc, wc, aq, upi, coo, c_qty, c_uqc, s_qty, s_uqc, sch, stdn_pr, rsp, reimp, prov, end_use, prodn, cntrl, qualfr, contnt, stmnt, sup_docs, assess_value, total_duty, bcd_notn_no, bcd_notn_sno, bcd_rate, bcd_amount, bcd_duty_fg, acd_notn_no, acd_notn_sno, acd_rate, acd_amount, acd_duty_fg, sws_notn_no, sws_notn_sno, sws_rate, sws_amount, sws_duty_fg, sad_notn_no, sad_notn_sno, sad_rate, sad_amount, sad_duty_fg, igst_notn_no, igst_notn_sno, igst_rate, igst_amount, igst_duty_fg, g_cess_notn_no, g_cess_notn_sno, g_cess_rate, g_cess_amount, g_cess_duty_fg, add_notn_no, add_notn_sno, add_rate, add_amount, add_duty_fg, cvd_notn_no, cvd_notn_sno, cvd_rate, cvd_amount, cvd_duty_fg, sg_notn_no, sg_notn_sno, sg_rate, sg_amount, sg_duty_fg, t_value_notn_no, t_value_notn_sno, t_value_rate, t_value_amount, t_value_duty_fg, sp_excd_notn_no, sp_excd_notn_sno, sp_excd_rate, sp_excd_amount, sp_excd_duty_fg, chcess_notn_no, chcess_notn_sno, chcess_rate, chcess_amount, chcess_duty_fg, tta_notn_no, tta_notn_sno, tta_rate, tta_amount, tta_duty_fg, cess_notn_no, cess_notn_sno, cess_rate, cess_amount, cess_duty_fg, caidc_cvd_edc_notn_no, caidc_cvd_edc_notn_sno, caidc_cvd_edc_rate, caidc_cvd_edc_amount, caidc_cvd_edc_duty_fg, eaidc_cvd_hec_notn_no, eaidc_cvd_hec_notn_sno, eaidc_cvd_hec_rate, eaidc_cvd_hec_amount, eaidc_cvd_hec_duty_fg, cus_edc_notn_no, cus_edc_notn_sno, cus_edc_rate, cus_edc_amount, cus_edc_duty_fg, cus_hec_notn_no, cus_hec_notn_sno, cus_hec_rate, cus_hec_amount, cus_hec_duty_fg, ncd_notn_no, ncd_notn_sno, ncd_rate, ncd_amount, ncd_duty_fg, aggr_notn_no, aggr_notn_sno, aggr_rate, aggr_amount, aggr_duty_fg, invsno_add_details, itmsno_add_details, refno, refdt, prtcd_svb_d, lab, pf, load_date, pf_, beno, bedate, prtcd, unitprice, currency_code, frt, ins, duty, sb_no, sb_dt, portcd, sinv, sitemn, type, manufact_cd, source_cy, trans_cy, address, accessory_item_details, notno, slno, created_at) 
VALUES('" .$boe_id .
"','" .
$invoice_id .
"','" .
$duties_id .
"','" .
$s_no .
"','" .
$cth .
"','" .
$description .
"','" .
$unit_price .
"','" .
$quantity .
"','" .
$uqc .
"','" .
$amount .
"','" .
$invsno .
"','" .
$itemsn .
"','" .
$cth_item_detail .
"','" .
$ceth .
"','" .
$item_description .
"','" .
$fs .
"','" .
$pq .
"','" .
$dc .
"','" .
$wc .
"','" .
$aq .
"','" .
$upi .
"','" .
$coo .
"','" .
$c_qty .
"','" .
$c_uqc .
"','" .
$s_qty .
"','" .
$s_uqc .
"','" .
$sch .
"','" .
$stdn_pr .
"','" .
$rsp .
"','" .
$reimp .
"','" .
$prov .
"','" .
$end_use .
"','" .
$prodn .
"','" .
$cntrl .
"','" .
$qualfr .
"','" .
$contnt .
"','" .
$stmnt .
"','" .
$sup_docs .
"','" .
$assess_value .
"','" .
$total_duty .
"','" .
$bcd_notn_no .
"','" .
$bcd_notn_sno .
"','" .
$bcd_rate .
"','" .
$bcd_amount .
"','" .
$bcd_duty_fg .
"','" .
$acd_notn_no .
"','" .
$acd_notn_sno .
"','" .
$acd_rate .
"','" .
$acd_amount .
"','" .
$acd_duty_fg .
"','" .
$sws_notn_no .
"','" .
$sws_notn_sno .
"','" .
$sws_rate .
"','" .
$sws_amount .
"','" .
$sws_duty_fg .
"','" .
$sad_notn_no .
"','" .
$sad_notn_sno .
"','" .
$sad_rate .
"','" .
$sad_amount .
"','" .
$sad_duty_fg .
"','" .
$igst_notn_no .
"','" .
$igst_notn_sno .
"','" .
$igst_rate .
"','" .
$igst_amount .
"','" .
$igst_duty_fg .
"','" .
$g_cess_notn_no .
"','" .
$g_cess_notn_sno .
"','" .
$g_cess_rate .
"','" .
$g_cess_amount .
"','" .
$g_cess_duty_fg .
"','" .
$add_notn_no .
"','" .
$add_notn_sno .
"','" .
$add_rate .
"','" .
$add_amount .
"','" .
$add_duty_fg .
"','" .
$cvd_notn_no .
"','" .
$cvd_notn_sno .
"','" .
$cvd_rate .
"','" .
$cvd_amount .
"','" .
$cvd_duty_fg .
"','" .
$sg_notn_no .
"','" .
$sg_notn_sno .
"','" .
$sg_rate .
"','" .
$sg_amount .
"','" .
$sg_duty_fg .
"','" .
$t_value_notn_no .
"','" .
$t_value_notn_sno .
"','" .
$t_value_rate .
"','" .
$t_value_amount .
"','" .
$t_value_duty_fg .
"','" .
$sp_excd_notn_no .
"','" .
$sp_excd_notn_sno .
"','" .
$sp_excd_rate .
"','" .
$sp_excd_amount .
"','" .
$sp_excd_duty_fg .
"','" .
$chcess_notn_no .
"','" .
$chcess_notn_sno .
"','" .
$chcess_rate .
"','" .
$chcess_amount .
"','" .
$chcess_duty_fg .
"','" .
$tta_notn_no .
"','" .
$tta_notn_sno .
"','" .
$tta_rate .
"','" .
$tta_amount .
"','" .
$tta_duty_fg .
"','" .
$cess_notn_no .
"','" .
$cess_notn_sno .
"','" .
$cess_rate .
"','" .
$cess_amount .
"','" .
$cess_duty_fg .
"','" .
$caidc_cvd_edc_notn_no .
"','" .
$caidc_cvd_edc_notn_sno .
"','" .
$caidc_cvd_edc_rate .
"','" .
$caidc_cvd_edc_amount .
"','" .
$caidc_cvd_edc_duty_fg .
"','" .
$eaidc_cvd_hec_notn_no .
"','" .
$eaidc_cvd_hec_notn_sno .
"','" .
$eaidc_cvd_hec_rate .
"','" .
$eaidc_cvd_hec_amount .
"','" .
$eaidc_cvd_hec_duty_fg .
"','" .
$cus_edc_notn_no .
"','" .
$cus_edc_notn_sno .
"','" .
$cus_edc_rate .
"','" .
$cus_edc_amount .
"','" .
$cus_edc_duty_fg .
"','" .
$cus_hec_notn_no .
"','" .
$cus_hec_notn_sno .
"','" .
$cus_hec_rate .
"','" .
$cus_hec_amount .
"','" .
$cus_hec_duty_fg .
"','" .
$ncd_notn_no .
"','" .
$ncd_notn_sno .
"','" .
$ncd_rate .
"','" .
$ncd_amount .
"','" .
$ncd_duty_fg .
"','" .
$aggr_notn_no .
"','" .
$aggr_notn_sno .
"','" .
$aggr_rate .
"','" .
$aggr_amount .
"','" .
$aggr_duty_fg .
"','" .
$invsno_add_details .
"','" .
$itmsno_add_details .
"','" .
$refno .
"','" .
$refdt .
"','" .
$prtcd_svb_d .
"','" .
$lab .
"','" .
$pf .
"','" .
$load_date .
"','" .
$pf_ .
"','" .
$beno .
"','" .
$bedate .
"','" .
$prtcd .
"','" .
$unitprice .
"','" .
$currency_code .
"','" .
$frt .
"','" .
$ins .
"','" .
$duty .
"','" .
$sb_no .
"','" .
$sb_dt .
"','" .
$portcd .
"','" .
$sinv .
"','" .
$sitemn .
"','" .
$type .
"','" .
$manufact_cd .
"','" .
$source_cy .
"','" .
$trans_cy .
"','" .
$address .
"','" .
$accessory_item_details .
"','" .
$notno .
"','" .
$slno .
"','" .
$created_at."')";

            $copy_insert_duties_and_additional_details = $db1_duties_and_additional_details->query(
                $sql_insert_duties_and_additional_details
            );
        }
        
        }
        /******************************************************************Start ship_bill_summary***************************************************************************************/
    }
    
    
    
    
/*public function duties_and_additional_details(){

        $query_duties_and_additional_details = "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details 
        LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
        LEFT JOIN invoice_and_valuation_details
                    ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id";
 
        $statement_duties_and_additional_details = $this->db->query(
            $query_duties_and_additional_details
        );
        $iecwise_duties_and_additional_details = [];
        $result_duties_and_additional_details = $statement_duties_and_additional_details->result_array();
        //echo count($result_duties_and_additional_details);exit;
        $batchSize = 9000;

        // Loop through the records in batches of 9000
        for (
            $offset = 0;
            $offset < count($result_duties_and_additional_details);
            $offset += $batchSize
        ) {
        foreach (
            $result_duties_and_additional_details
            as $str_duties_and_additional_details
        ) {
            $iec_no = $str_duties_and_additional_details['iec_no'];
           echo $sql_duties_and_additional_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_no%'";
            $iecwise_duties_and_additional_details = $this->db->query(
                $sql_duties_and_additional_details
            );
            $iecwise_data_duties_and_additional_details = $iecwise_duties_and_additional_details->result_array();
            $db1_duties_and_additional_details = $this->database_connection(
                $iecwise_data_duties_and_additional_details[0][
                    "lucrative_users_id"
                ]
            );
          //  if (get_magic_quotes_gpc()) {
                $reference_code = addslashes(
                    $str_duties_and_additional_details["reference_code"]
                );
                 $be_no = addslashes(
                    $str_duties_and_additional_details["be_no"]
                );
                 $be_date = addslashes(
                    $str_duties_and_additional_details["be_date"]
                );
                $boe_id = addslashes(
                    $str_duties_and_additional_details["boe_id"]
                );
                $invoice_id = addslashes(
                    $str_duties_and_additional_details["invoice_id"]
                );
                $duties_id = addslashes(
                    $str_duties_and_additional_details["duties_id"]
                );
                $s_no = addslashes(
                    $str_duties_and_additional_details["s_no"]
                );
                $cth = addslashes($str_duties_and_additional_details["cth"]);
                $description = addslashes(
                    $str_duties_and_additional_details["description"]
                );
                $unit_price = addslashes(
                    $str_duties_and_additional_details["unit_price"]
                );
                $quantity = addslashes(
                    $str_duties_and_additional_details["quantity"]
                );
                $uqc = addslashes($str_duties_and_additional_details["uqc"]);
                $amount = addslashes(
                    $str_duties_and_additional_details["amount"]
                );
                $invsno = addslashes(
                    $str_duties_and_additional_details["invsno"]
                );
                $itemsn = addslashes(
                    $str_duties_and_additional_details["itemsn"]
                );
                $cth_item_detail = addslashes(
                    $str_duties_and_additional_details["cth_item_detail"]
                );
                $ceth = addslashes(
                    $str_duties_and_additional_details["ceth"]
                );
                $item_description = addslashes(
                    $str_duties_and_additional_details["item_description"]
                );
                $fs = addslashes($str_duties_and_additional_details["fs"]);
                $pq = addslashes($str_duties_and_additional_details["pq"]);
                $dc = addslashes($str_duties_and_additional_details["dc"]);
                $wc = addslashes($str_duties_and_additional_details["wc"]);
                $aq = addslashes($str_duties_and_additional_details["aq"]);
                $upi = addslashes($str_duties_and_additional_details["upi"]);
                $coo = addslashes($str_duties_and_additional_details["coo"]);
                $c_qty = addslashes(
                    $str_duties_and_additional_details["c_qty"]
                );
                $c_uqc = addslashes(
                    $str_duties_and_additional_details["c_uqc"]
                );
                $s_qty = addslashes(
                    $str_duties_and_additional_details["s_qty"]
                );
                $s_uqc = addslashes(
                    $str_duties_and_additional_details["s_uqc"]
                );
                $sch = addslashes($str_duties_and_additional_details["sch"]);
                $stdn_pr = addslashes(
                    $str_duties_and_additional_details["stdn_pr"]
                );
                $rsp = addslashes($str_duties_and_additional_details["rsp"]);
                $reimp = addslashes(
                    $str_duties_and_additional_details["reimp"]
                );
                $prov = addslashes(
                    $str_duties_and_additional_details["prov"]
                );
                $end_use = addslashes(
                    $str_duties_and_additional_details["end_use"]
                );
                $prodn = addslashes(
                    $str_duties_and_additional_details["prodn"]
                );
                $cntrl = addslashes(
                    $str_duties_and_additional_details["cntrl"]
                );
                $qualfr = addslashes(
                    $str_duties_and_additional_details["qualfr"]
                );
                $contnt = addslashes(
                    $str_duties_and_additional_details["contnt"]
                );
                $stmnt = addslashes(
                    $str_duties_and_additional_details["stmnt"]
                );
                $sup_docs = addslashes(
                    $str_duties_and_additional_details["sup_docs"]
                );
                $assess_value = addslashes(
                    $str_duties_and_additional_details["assess_value"]
                );
                $total_duty = addslashes(
                    $str_duties_and_additional_details["total_duty"]
                );
                $bcd_notn_no = addslashes(
                    $str_duties_and_additional_details["bcd_notn_no"]
                );
                $bcd_notn_sno = addslashes(
                    $str_duties_and_additional_details["bcd_notn_sno"]
                );
                $bcd_rate = addslashes(
                    $str_duties_and_additional_details["bcd_rate"]
                );
                $bcd_amount = addslashes(
                    $str_duties_and_additional_details["bcd_amount"]
                );
                $bcd_duty_fg = addslashes(
                    $str_duties_and_additional_details["bcd_duty_fg"]
                );
                $acd_notn_no = addslashes(
                    $str_duties_and_additional_details["acd_notn_no"]
                );
                $acd_notn_sno = addslashes(
                    $str_duties_and_additional_details["acd_notn_sno"]
                );
                $acd_rate = addslashes(
                    $str_duties_and_additional_details["acd_rate"]
                );
                $acd_amount = addslashes(
                    $str_duties_and_additional_details["acd_amount"]
                );
                $acd_duty_fg = addslashes(
                    $str_duties_and_additional_details["acd_duty_fg"]
                );
                $sws_notn_no = addslashes(
                    $str_duties_and_additional_details["sws_notn_no"]
                );
                $sws_notn_sno = addslashes(
                    $str_duties_and_additional_details["sws_notn_sno"]
                );
                $sws_rate = addslashes(
                    $str_duties_and_additional_details["sws_rate"]
                );
                $sws_amount = addslashes(
                    $str_duties_and_additional_details["sws_amount"]
                );
                $sws_duty_fg = addslashes(
                    $str_duties_and_additional_details["sws_duty_fg"]
                );
                $sad_notn_no = addslashes(
                    $str_duties_and_additional_details["sad_notn_no"]
                );
                $sad_notn_sno = addslashes(
                    $str_duties_and_additional_details["sad_notn_sno"]
                );
                $sad_rate = addslashes(
                    $str_duties_and_additional_details["sad_rate"]
                );
                $sad_amount = addslashes(
                    $str_duties_and_additional_details["sad_amount"]
                );
                $sad_duty_fg = addslashes(
                    $str_duties_and_additional_details["sad_duty_fg"]
                );
                $igst_notn_no = addslashes(
                    $str_duties_and_additional_details["igst_notn_no"]
                );
                $igst_notn_sno = addslashes(
                    $str_duties_and_additional_details["igst_notn_sno"]
                );
                $igst_rate = addslashes(
                    $str_duties_and_additional_details["igst_rate"]
                );
                $igst_amount = addslashes(
                    $str_duties_and_additional_details["igst_amount"]
                );
                $igst_duty_fg = addslashes(
                    $str_duties_and_additional_details["igst_duty_fg"]
                );
                $g_cess_notn_no = addslashes(
                    $str_duties_and_additional_details["g_cess_notn_no"]
                );
                $g_cess_notn_sno = addslashes(
                    $str_duties_and_additional_details["g_cess_notn_sno"]
                );
                $g_cess_rate = addslashes(
                    $str_duties_and_additional_details["g_cess_rate"]
                );
                $g_cess_amount = addslashes(
                    $str_duties_and_additional_details["g_cess_amount"]
                );
                $g_cess_duty_fg = addslashes(
                    $str_duties_and_additional_details["g_cess_duty_fg"]
                );
                $add_notn_no = addslashes(
                    $str_duties_and_additional_details["add_notn_no"]
                );
                $add_notn_sno = addslashes(
                    $str_duties_and_additional_details["add_notn_sno"]
                );
                $add_rate = addslashes(
                    $str_duties_and_additional_details["add_rate"]
                );
                $add_amount = addslashes(
                    $str_duties_and_additional_details["add_amount"]
                );
                $add_duty_fg = addslashes(
                    $str_duties_and_additional_details["add_duty_fg"]
                );
                $cvd_notn_no = addslashes(
                    $str_duties_and_additional_details["cvd_notn_no"]
                );
                $cvd_notn_sno = addslashes(
                    $str_duties_and_additional_details["cvd_notn_sno"]
                );
                $cvd_rate = addslashes(
                    $str_duties_and_additional_details["cvd_rate"]
                );
                $cvd_amount = addslashes(
                    $str_duties_and_additional_details["cvd_amount"]
                );
                $cvd_duty_fg = addslashes(
                    $str_duties_and_additional_details["cvd_duty_fg"]
                );
                $sg_notn_no = addslashes(
                    $str_duties_and_additional_details["sg_notn_no"]
                );
                $sg_notn_sno = addslashes(
                    $str_duties_and_additional_details["sg_notn_sno"]
                );
                $sg_rate = addslashes(
                    $str_duties_and_additional_details["sg_rate"]
                );
                $sg_amount = addslashes(
                    $str_duties_and_additional_details["sg_amount"]
                );
                $sg_duty_fg = addslashes(
                    $str_duties_and_additional_details["sg_duty_fg"]
                );
                $t_value_notn_no = addslashes(
                    $str_duties_and_additional_details["t_value_notn_no"]
                );
                $t_value_notn_sno = addslashes(
                    $str_duties_and_additional_details["t_value_notn_sno"]
                );
                $t_value_rate = addslashes(
                    $str_duties_and_additional_details["t_value_rate"]
                );
                $t_value_amount = addslashes(
                    $str_duties_and_additional_details["t_value_amount"]
                );
                $t_value_duty_fg = addslashes(
                    $str_duties_and_additional_details["t_value_duty_fg"]
                );
                $sp_excd_notn_no = addslashes(
                    $str_duties_and_additional_details["sp_excd_notn_no"]
                );
                $sp_excd_notn_sno = addslashes(
                    $str_duties_and_additional_details["sp_excd_notn_sno"]
                );
                $sp_excd_rate = addslashes(
                    $str_duties_and_additional_details["sp_excd_rate"]
                );
                $sp_excd_amount = addslashes(
                    $str_duties_and_additional_details["sp_excd_amount"]
                );
                $sp_excd_duty_fg = addslashes(
                    $str_duties_and_additional_details["sp_excd_duty_fg"]
                );
                $chcess_notn_no = addslashes(
                    $str_duties_and_additional_details["chcess_notn_no"]
                );
                $chcess_notn_sno = addslashes(
                    $str_duties_and_additional_details["chcess_notn_sno"]
                );
                $chcess_rate = addslashes(
                    $str_duties_and_additional_details["chcess_rate"]
                );
                $chcess_amount = addslashes(
                    $str_duties_and_additional_details["chcess_amount"]
                );
                $chcess_duty_fg = addslashes(
                    $str_duties_and_additional_details["chcess_duty_fg"]
                );
                $tta_notn_no = addslashes(
                    $str_duties_and_additional_details["tta_notn_no"]
                );
                $tta_notn_sno = addslashes(
                    $str_duties_and_additional_details["tta_notn_sno"]
                );
                $tta_rate = addslashes(
                    $str_duties_and_additional_details["tta_rate"]
                );
                $tta_amount = addslashes(
                    $str_duties_and_additional_details["tta_amount"]
                );
                $tta_duty_fg = addslashes(
                    $str_duties_and_additional_details["tta_duty_fg"]
                );
                $cess_notn_no = addslashes(
                    $str_duties_and_additional_details["cess_notn_no"]
                );
                $cess_notn_sno = addslashes(
                    $str_duties_and_additional_details["cess_notn_sno"]
                );
                $cess_rate = addslashes(
                    $str_duties_and_additional_details["cess_rate"]
                );
                $cess_amount = addslashes(
                    $str_duties_and_additional_details["cess_amount"]
                );
                $cess_duty_fg = addslashes(
                    $str_duties_and_additional_details["cess_duty_fg"]
                );
                $caidc_cvd_edc_notn_no = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_notn_no"]
                );
                $caidc_cvd_edc_notn_sno = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_notn_sno"]
                );
                $caidc_cvd_edc_rate = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_rate"]
                );
                $caidc_cvd_edc_amount = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_amount"]
                );
                $caidc_cvd_edc_duty_fg = addslashes(
                    $str_duties_and_additional_details["caidc_cvd_edc_duty_fg"]
                );
                $eaidc_cvd_hec_notn_no = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_notn_no"]
                );
                $eaidc_cvd_hec_notn_sno = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_notn_sno"]
                );
                $eaidc_cvd_hec_rate = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_rate"]
                );
                $eaidc_cvd_hec_amount = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_amount"]
                );
                $eaidc_cvd_hec_duty_fg = addslashes(
                    $str_duties_and_additional_details["eaidc_cvd_hec_duty_fg"]
                );
                $cus_edc_notn_no = addslashes(
                    $str_duties_and_additional_details["cus_edc_notn_no"]
                );
                $cus_edc_notn_sno = addslashes(
                    $str_duties_and_additional_details["cus_edc_notn_sno"]
                );
                $cus_edc_rate = addslashes(
                    $str_duties_and_additional_details["cus_edc_rate"]
                );
                $cus_edc_amount = addslashes(
                    $str_duties_and_additional_details["cus_edc_amount"]
                );
                $cus_edc_duty_fg = addslashes(
                    $str_duties_and_additional_details["cus_edc_duty_fg"]
                );
                $cus_hec_notn_no = addslashes(
                    $str_duties_and_additional_details["cus_hec_notn_no"]
                );
                $cus_hec_notn_sno = addslashes(
                    $str_duties_and_additional_details["cus_hec_notn_sno"]
                );
                $cus_hec_rate = addslashes(
                    $str_duties_and_additional_details["cus_hec_rate"]
                );
                $cus_hec_amount = addslashes(
                    $str_duties_and_additional_details["cus_hec_amount"]
                );
                $cus_hec_duty_fg = addslashes(
                    $str_duties_and_additional_details["cus_hec_duty_fg"]
                );
                $ncd_notn_no = addslashes(
                    $str_duties_and_additional_details["ncd_notn_no"]
                );
                $ncd_notn_sno = addslashes(
                    $str_duties_and_additional_details["ncd_notn_sno"]
                );
                $ncd_rate = addslashes(
                    $str_duties_and_additional_details["ncd_rate"]
                );
                $ncd_amount = addslashes(
                    $str_duties_and_additional_details["ncd_amount"]
                );
                $ncd_duty_fg = addslashes(
                    $str_duties_and_additional_details["ncd_duty_fg"]
                );
                $aggr_notn_no = addslashes(
                    $str_duties_and_additional_details["aggr_notn_no"]
                );
                $aggr_notn_sno = addslashes(
                    $str_duties_and_additional_details["aggr_notn_sno"]
                );
                $aggr_rate = addslashes(
                    $str_duties_and_additional_details["aggr_rate"]
                );
                $aggr_amount = addslashes(
                    $str_duties_and_additional_details["aggr_amount"]
                );
                $aggr_duty_fg = addslashes(
                    $str_duties_and_additional_details["aggr_duty_fg"]
                );
                $invsno_add_details = addslashes(
                    $str_duties_and_additional_details["invsno_add_details"]
                );
                $itmsno_add_details = addslashes(
                    $str_duties_and_additional_details["itmsno_add_details"]
                );
                $refno = addslashes(
                    $str_duties_and_additional_details["refno"]
                );
                $refdt = addslashes(
                    $str_duties_and_additional_details["refdt"]
                );
                $prtcd_svb_d = addslashes(
                    $str_duties_and_additional_details["prtcd_svb_d"]
                );
                $lab = addslashes($str_duties_and_additional_details["lab"]);
                $pf = addslashes($str_duties_and_additional_details["pf"]);
                $load_date = addslashes(
                    $str_duties_and_additional_details["load_date"]
                );
                $pf_ = addslashes($str_duties_and_additional_details["pf_"]);
                $beno = addslashes(
                    $str_duties_and_additional_details["beno"]
                );
                $bedate = addslashes(
                    $str_duties_and_additional_details["bedate"]
                );
                $prtcd = addslashes(
                    $str_duties_and_additional_details["prtcd"]
                );
                $unitprice = addslashes(
                    $str_duties_and_additional_details["unitprice"]
                );
                $currency_code = addslashes(
                    $str_duties_and_additional_details["currency_code"]
                );
                $frt = addslashes($str_duties_and_additional_details["frt"]);
                $ins = addslashes($str_duties_and_additional_details["ins"]);
                $duty = addslashes(
                    $str_duties_and_additional_details["duty"]
                );
                $sb_no = addslashes(
                    $str_duties_and_additional_details["sb_no"]
                );
                $sb_dt = addslashes(
                    $str_duties_and_additional_details["sb_dt"]
                );
                $portcd = addslashes(
                    $str_duties_and_additional_details["portcd"]
                );
                $sinv = addslashes(
                    $str_duties_and_additional_details["sinv"]
                );
                $sitemn = addslashes(
                    $str_duties_and_additional_details["sitemn"]
                );
                $type = addslashes(
                    $str_duties_and_additional_details["type"]
                );
                $manufact_cd = addslashes(
                    $str_duties_and_additional_details["manufact_cd"]
                );
                $source_cy = addslashes(
                    $str_duties_and_additional_details["source_cy"]
                );
                $trans_cy = addslashes(
                    $str_duties_and_additional_details["trans_cy"]
                );
                $address = addslashes(
                    $str_duties_and_additional_details["address"]
                );
                $accessory_item_details = addslashes(
                    $str_duties_and_additional_details["accessory_item_details"]
                );
                $slno = addslashes(
                    $str_duties_and_additional_details["slno"]
                );
                $notno = addslashes(
                    $str_duties_and_additional_details["notno"]
                );
                $created_at = addslashes(
                    $str_duties_and_additional_details["created_at"]
                );
                
                
          echo  $sql_users = "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details 
        LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
        LEFT JOIN invoice_and_valuation_details
                    ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id";
         
                $iecwise1_users = $db1_duties_and_additional_details->query($sql_users);
                $rowusers = $iecwise1_users->fetch_assoc();
               // print_r($rowusers);exit;
                $iecwise_data1_users = array();
                
//if($iecwise1_users !== FALSE && $iecwise1_users->num_rows() > 0){
    //$data = $query->result_array();

                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
// }                  
 $c= $reference_code."-".$be_no."-".$be_date;
                    //skip dupliacte entry     
             $a= $this->inArray_duties_and_additional_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
           echo $sql_insert_duties_and_additional_details =
                "INSERT INTO `duties_and_additional_details` (boe_id, invoice_id, duties_id, s_no, cth, description, unit_price, quantity, uqc, amount, invsno, itemsn, cth_item_detail, ceth, item_description, fs, pq, dc, wc, aq, upi, coo, c_qty, c_uqc, s_qty, s_uqc, sch, stdn_pr, rsp, reimp, prov, end_use, prodn, cntrl, qualfr, contnt, stmnt, sup_docs, assess_value, total_duty, bcd_notn_no, bcd_notn_sno, bcd_rate, bcd_amount, bcd_duty_fg, acd_notn_no, acd_notn_sno, acd_rate, acd_amount, acd_duty_fg, sws_notn_no, sws_notn_sno, sws_rate, sws_amount, sws_duty_fg, sad_notn_no, sad_notn_sno, sad_rate, sad_amount, sad_duty_fg, igst_notn_no, igst_notn_sno, igst_rate, igst_amount, igst_duty_fg, g_cess_notn_no, g_cess_notn_sno, g_cess_rate, g_cess_amount, g_cess_duty_fg, add_notn_no, add_notn_sno, add_rate, add_amount, add_duty_fg, cvd_notn_no, cvd_notn_sno, cvd_rate, cvd_amount, cvd_duty_fg, sg_notn_no, sg_notn_sno, sg_rate, sg_amount, sg_duty_fg, t_value_notn_no, t_value_notn_sno, t_value_rate, t_value_amount, t_value_duty_fg, sp_excd_notn_no, sp_excd_notn_sno, sp_excd_rate, sp_excd_amount, sp_excd_duty_fg, chcess_notn_no, chcess_notn_sno, chcess_rate, chcess_amount, chcess_duty_fg, tta_notn_no, tta_notn_sno, tta_rate, tta_amount, tta_duty_fg, cess_notn_no, cess_notn_sno, cess_rate, cess_amount, cess_duty_fg, caidc_cvd_edc_notn_no, caidc_cvd_edc_notn_sno, caidc_cvd_edc_rate, caidc_cvd_edc_amount, caidc_cvd_edc_duty_fg, eaidc_cvd_hec_notn_no, eaidc_cvd_hec_notn_sno, eaidc_cvd_hec_rate, eaidc_cvd_hec_amount, eaidc_cvd_hec_duty_fg, cus_edc_notn_no, cus_edc_notn_sno, cus_edc_rate, cus_edc_amount, cus_edc_duty_fg, cus_hec_notn_no, cus_hec_notn_sno, cus_hec_rate, cus_hec_amount, cus_hec_duty_fg, ncd_notn_no, ncd_notn_sno, ncd_rate, ncd_amount, ncd_duty_fg, aggr_notn_no, aggr_notn_sno, aggr_rate, aggr_amount, aggr_duty_fg, invsno_add_details, itmsno_add_details, refno, refdt, prtcd_svb_d, lab, pf, load_date, pf_, beno, bedate, prtcd, unitprice, currency_code, frt, ins, duty, sb_no, sb_dt, portcd, sinv, sitemn, type, manufact_cd, source_cy, trans_cy, address, accessory_item_details, notno, slno, created_at) 
VALUES('" .$boe_id .
"','" .
$invoice_id .
"','" .
$duties_id .
"','" .
$s_no .
"','" .
$cth .
"','" .
$description .
"','" .
$unit_price .
"','" .
$quantity .
"','" .
$uqc .
"','" .
$amount .
"','" .
$invsno .
"','" .
$itemsn .
"','" .
$cth_item_detail .
"','" .
$ceth .
"','" .
$item_description .
"','" .
$fs .
"','" .
$pq .
"','" .
$dc .
"','" .
$wc .
"','" .
$aq .
"','" .
$upi .
"','" .
$coo .
"','" .
$c_qty .
"','" .
$c_uqc .
"','" .
$s_qty .
"','" .
$s_uqc .
"','" .
$sch .
"','" .
$stdn_pr .
"','" .
$rsp .
"','" .
$reimp .
"','" .
$prov .
"','" .
$end_use .
"','" .
$prodn .
"','" .
$cntrl .
"','" .
$qualfr .
"','" .
$contnt .
"','" .
$stmnt .
"','" .
$sup_docs .
"','" .
$assess_value .
"','" .
$total_duty .
"','" .
$bcd_notn_no .
"','" .
$bcd_notn_sno .
"','" .
$bcd_rate .
"','" .
$bcd_amount .
"','" .
$bcd_duty_fg .
"','" .
$acd_notn_no .
"','" .
$acd_notn_sno .
"','" .
$acd_rate .
"','" .
$acd_amount .
"','" .
$acd_duty_fg .
"','" .
$sws_notn_no .
"','" .
$sws_notn_sno .
"','" .
$sws_rate .
"','" .
$sws_amount .
"','" .
$sws_duty_fg .
"','" .
$sad_notn_no .
"','" .
$sad_notn_sno .
"','" .
$sad_rate .
"','" .
$sad_amount .
"','" .
$sad_duty_fg .
"','" .
$igst_notn_no .
"','" .
$igst_notn_sno .
"','" .
$igst_rate .
"','" .
$igst_amount .
"','" .
$igst_duty_fg .
"','" .
$g_cess_notn_no .
"','" .
$g_cess_notn_sno .
"','" .
$g_cess_rate .
"','" .
$g_cess_amount .
"','" .
$g_cess_duty_fg .
"','" .
$add_notn_no .
"','" .
$add_notn_sno .
"','" .
$add_rate .
"','" .
$add_amount .
"','" .
$add_duty_fg .
"','" .
$cvd_notn_no .
"','" .
$cvd_notn_sno .
"','" .
$cvd_rate .
"','" .
$cvd_amount .
"','" .
$cvd_duty_fg .
"','" .
$sg_notn_no .
"','" .
$sg_notn_sno .
"','" .
$sg_rate .
"','" .
$sg_amount .
"','" .
$sg_duty_fg .
"','" .
$t_value_notn_no .
"','" .
$t_value_notn_sno .
"','" .
$t_value_rate .
"','" .
$t_value_amount .
"','" .
$t_value_duty_fg .
"','" .
$sp_excd_notn_no .
"','" .
$sp_excd_notn_sno .
"','" .
$sp_excd_rate .
"','" .
$sp_excd_amount .
"','" .
$sp_excd_duty_fg .
"','" .
$chcess_notn_no .
"','" .
$chcess_notn_sno .
"','" .
$chcess_rate .
"','" .
$chcess_amount .
"','" .
$chcess_duty_fg .
"','" .
$tta_notn_no .
"','" .
$tta_notn_sno .
"','" .
$tta_rate .
"','" .
$tta_amount .
"','" .
$tta_duty_fg .
"','" .
$cess_notn_no .
"','" .
$cess_notn_sno .
"','" .
$cess_rate .
"','" .
$cess_amount .
"','" .
$cess_duty_fg .
"','" .
$caidc_cvd_edc_notn_no .
"','" .
$caidc_cvd_edc_notn_sno .
"','" .
$caidc_cvd_edc_rate .
"','" .
$caidc_cvd_edc_amount .
"','" .
$caidc_cvd_edc_duty_fg .
"','" .
$eaidc_cvd_hec_notn_no .
"','" .
$eaidc_cvd_hec_notn_sno .
"','" .
$eaidc_cvd_hec_rate .
"','" .
$eaidc_cvd_hec_amount .
"','" .
$eaidc_cvd_hec_duty_fg .
"','" .
$cus_edc_notn_no .
"','" .
$cus_edc_notn_sno .
"','" .
$cus_edc_rate .
"','" .
$cus_edc_amount .
"','" .
$cus_edc_duty_fg .
"','" .
$cus_hec_notn_no .
"','" .
$cus_hec_notn_sno .
"','" .
$cus_hec_rate .
"','" .
$cus_hec_amount .
"','" .
$cus_hec_duty_fg .
"','" .
$ncd_notn_no .
"','" .
$ncd_notn_sno .
"','" .
$ncd_rate .
"','" .
$ncd_amount .
"','" .
$ncd_duty_fg .
"','" .
$aggr_notn_no .
"','" .
$aggr_notn_sno .
"','" .
$aggr_rate .
"','" .
$aggr_amount .
"','" .
$aggr_duty_fg .
"','" .
$invsno_add_details .
"','" .
$itmsno_add_details .
"','" .
$refno .
"','" .
$refdt .
"','" .
$prtcd_svb_d .
"','" .
$lab .
"','" .
$pf .
"','" .
$load_date .
"','" .
$pf_ .
"','" .
$beno .
"','" .
$bedate .
"','" .
$prtcd .
"','" .
$unitprice .
"','" .
$currency_code .
"','" .
$frt .
"','" .
$ins .
"','" .
$duty .
"','" .
$sb_no .
"','" .
$sb_dt .
"','" .
$portcd .
"','" .
$sinv .
"','" .
$sitemn .
"','" .
$type .
"','" .
$manufact_cd .
"','" .
$source_cy .
"','" .
$trans_cy .
"','" .
$address .
"','" .
$accessory_item_details .
"','" .
$notno .
"','" .
$slno .
"','" .
$created_at."')";

            $copy_insert_duties_and_additional_details = $db1_duties_and_additional_details->query(
                $sql_insert_duties_and_additional_details
            );
        }
    }
        
        }
         }*/
    
public function inArray_equipment_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
            
     $c= $val['sb_no']."-".$val['container'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }  
public function equipment_details(){
        /******************************************************************Start equipment_details***************************************************************************************/
 echo $query_equipment_details ="SELECT equipment_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM equipment_details LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id ";
      
 // $query_equipment_details ="SELECT equipment_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM equipment_details LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id ";
        $statement_equipment_details = $this->db->query(
            $query_equipment_details
        );
        $iecwise_equipment_details = [];
        $result_equipment_details = $statement_equipment_details->result_array();
        //print_r($result_equipment_details);exit;

        foreach ($result_equipment_details as $str_equipment_details) {
            $iec_equipment_details = $str_equipment_details["iec"];
            $sql_equipment_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_equipment_details'";
            $iecwise_equipment_details = $this->db->query(
                $sql_equipment_details
            );
            $iecwise_data_equipment_details = $iecwise_equipment_details->result_array();
            $db1_equipment_details = $this->database_connection(
                $iecwise_data_equipment_details[0]["lucrative_users_id"]
            );
           // if (get_magic_quotes_gpc()) {
                $equip_id = addslashes($str_equipment_details["equip_id"]);
                $sbs_id = addslashes($str_equipment_details["sbs_id"]);
                $sb_no = addslashes($str_equipment_details["sb_no"]);
                $container = addslashes($str_equipment_details["container"]);
                $seal = addslashes($str_equipment_details["seal"]);
                $date = addslashes($str_equipment_details["date"]);
                $s_no = addslashes($str_equipment_details["s_no"]);
                $created_at = addslashes( $str_equipment_details["created_at"]);
 /*************************************checking dupliacte entries d2d************************************************/
 echo $sql_users =
            "SELECT equipment_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM equipment_details LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id ";
                 
                $iecwise1_users = $db1_equipment_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
            $c= $sb_no."-".$container;
                    //skip dupliacte entry     
             $a= $this->inArray_equipment_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
              $date = date("Y-m-d",strtotime($date));
  /************************************************************************************************************/
echo $sql_insert_equipment_details ="INSERT INTO `equipment_details` (`equip_id`,`sbs_id`, `container`, `seal`, `date`, `s_no`, `created_at`)
VALUES('".$equip_id. "','".$sbs_id."','".$container."','".$seal."','".$date."','".$s_no."','".$created_at."')";

            $copy_insert_equipment_details = $db1_equipment_details->query($sql_insert_equipment_details);
        }
    }

        /******************************************************************Start equipment_details***************************************************************************************/
    }

/******************************************************************Start courier_bill_summary***************************************************************************************/
public function inArray_item_manufacturer_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['reference_code']."-".$val['be_no']."-".$val['be_date'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function item_manufacturer_details(){
        /******************************************************************Start item_manufacturer_details***************************************************************************************/

       echo $query_item_manufacturer_details =
            "SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code,item_manufacturer_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_manufacturer_details LEFT JOIN item_details ON item_details.item_id=item_manufacturer_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id";
        $statement_item_manufacturer_details = $this->db->query(
            $query_item_manufacturer_details
        );
        $iecwise_item_manufacturer_details = [];
        $result_item_manufacturer_details = $statement_item_manufacturer_details->result_array();
        //print_r($result_item_manufacturer_details);

        foreach (
            $result_item_manufacturer_details
            as $str_item_manufacturer_details
        ) {
            $iec_item_manufacturer_details =
                $str_item_manufacturer_details["iec"];
          echo  $sql_item_manufacturer_details = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_item_manufacturer_details'";
            $iecwise_item_manufacturer_details = $this->db->query(
                $sql_item_manufacturer_details
            );
            $iecwise_data_item_manufacturer_details = $iecwise_item_manufacturer_details->result_array();
            $db1_insert_item_manufacturer_details = $this->database_connection(
                $iecwise_data_item_manufacturer_details[0]["lucrative_users_id"]
            );
        //    if (get_magic_quotes_gpc()) {
         $reference_code = addslashes($str_item_manufacturer_details["reference_code"]);
                $item_manufact_id = addslashes(
                    $str_item_manufacturer_details["item_manufact_id"]
                );
                $item_id = addslashes(
                    $str_item_manufacturer_details["item_id"]
                );
                $inv_sno = addslashes(
                    $str_item_manufacturer_details["inv_sno"]
                );
                $item_sno = addslashes(
                    $str_item_manufacturer_details["item_sno"]
                );
                $manufact_cd = addslashes(
                    $str_item_manufacturer_details["manufact_cd"]
                );
                $source_state = addslashes(
                    $str_item_manufacturer_details["source_state"]
                );
                $trans_cy = addslashes(
                    $str_item_manufacturer_details["trans_cy"]
                );
                $address = addslashes(
                    $str_item_manufacturer_details["address"]
                );
 /********************checking dupliacte entries d2d***********************/
            $sql_users = "SELECT CONCAT(n1.be_no,"-",invoice_and_valuation_details.s_no, "-", duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id ";
         
                $iecwise1_users = $db1_insert_item_manufacturer_details->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
                   $c= $reference_code."-".$be_no."-".$be_date;
                    //skip dupliacte entry     
             $a= $this->inArray_item_manufacturer_details($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
             
 /***********************************************************************/
       echo     $sql_insert_item_manufacturer_details =
                "INSERT INTO `item_manufacturer_details` (`item_manufact_id`, `item_id`, `inv_sno`, `item_sno`, `manufact_cd`, `source_state`, `trans_cy`, `address`)
VALUES('" .
                $item_manufact_id .
                "','" .
                $item_id .
                "','" .
                $inv_sno .
                "','" .
                $item_sno .
                "','" .
                $manufact_cd .
                "','" .
                $source_state .
                "','" .
                $trans_cy .
                "','" .
                $address .
                "')";
            $copy_insert_item_manufacturer_details = $db1_insert_item_manufacturer_details->query(
                $sql_insert_item_manufacturer_details
            );
        }
    }
}

    /******************************************************************Start item_manufacturer_details***************************************************************************************/


public function inArray_invoice_summery($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['inv_no']."-".$val['inv_date'];
                       

             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
    
/*public function invoice_summery(){ 
       
 $query_invoice_summery ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id where iec='0888009364' ";
      
// echo $query_invoice_summery ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id";
        $statement_invoice_summery = $this->db->query($query_invoice_summery);
        $iecwise_invoice_summery = [];
        $result_invoice_summery = $statement_invoice_summery->result_array();
        foreach ($result_invoice_summery as $str_invoice_summery) {
            $iec_invoice_summery = $str_invoice_summery["iec"];
            $sql_invoice_summery = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_invoice_summery'";
            $iecwise_invoice_summery = $this->db->query($sql_invoice_summery);
            $iecwise_data_invoice_summery = $iecwise_invoice_summery->result_array();
           
            $db1_insert_invoice_summery = $this->database_connection(
                $iecwise_data_invoice_summery[0]["lucrative_users_id"]
            );
            
          
            
           // if (get_magic_quotes_gpc()) {
                $invoice_id = addslashes($str_invoice_summery["invoice_id"]);
                $sbs_id = addslashes($str_invoice_summery["sbs_id"]);
                $s_no_inv = addslashes($str_invoice_summery["s_no_inv"]);
                $inv_no = addslashes($str_invoice_summery["inv_no"]);
                $inv_date = addslashes($str_invoice_summery["inv_date"]);
                $inv_no_date = addslashes(
                    $str_invoice_summery["inv_no_date"]
                );
                $po_no_date = addslashes($str_invoice_summery["po_no_date"]);
                $loc_no_date = addslashes(
                    $str_invoice_summery["loc_no_date"]
                );
                $contract_no_date = addslashes(
                    $str_invoice_summery["contract_no_date"]
                );
                $ad_code_inv = addslashes(
                    $str_invoice_summery["ad_code_inv"]
                );
                $invterm = addslashes($str_invoice_summery["invterm"]);
                $exporters_name_and_address = addslashes(
                    $str_invoice_summery["exporters_name_and_address"]
                );
                $buyers_name_and_address = addslashes(
                    $str_invoice_summery["buyers_name_and_address"]
                );
                $third_party_name_and_address = addslashes(
                    $str_invoice_summery["third_party_name_and_address"]
                );
                $buyers_aeo_status = addslashes(
                    $str_invoice_summery["buyers_aeo_status"]
                );
                $invoice_value = addslashes(
                    $str_invoice_summery["invoice_value"]
                );
                $invoice_value_currency = addslashes(
                    $str_invoice_summery["invoice_value_currency"]
                );
                $fob_value_inv = addslashes(
                    $str_invoice_summery["fob_value_inv"]
                );
                $fob_value_currency = addslashes(
                    $str_invoice_summery["fob_value_currency"]
                );
                $freight_val = addslashes(
                    $str_invoice_summery["freight_val"]
                );
                $freight_currency = addslashes(
                    $str_invoice_summery["freight_currency"]
                );
                $insurance_val = addslashes(
                    $str_invoice_summery["insurance_val"]
                );
                $insurance_currency = addslashes(
                    $str_invoice_summery["insurance_currency"]
                );
                $discount_val = addslashes(
                    $str_invoice_summery["discount_val"]
                );
                $discount_val_currency = addslashes(
                    $str_invoice_summery["discount_val_currency"]
                );
                $commison = addslashes($str_invoice_summery["commison"]);
                $comission_currency = addslashes(
                    $str_invoice_summery["comission_currency"]
                );
                $deduct = addslashes($str_invoice_summery["deduct"]);
                $deduct_currency = addslashes(
                    $str_invoice_summery["deduct_currency"]
                );
                $p_c_val = addslashes($str_invoice_summery["p_c_val"]);
                $p_c_val_currency = addslashes(
                    $str_invoice_summery["p_c_val_currency"]
                );
                $exchange_rate = addslashes(
                    $str_invoice_summery["exchange_rate"]
                );
                $created_at = addslashes($str_invoice_summery["created_at"]);
                
                
                  $inv_date = date("Y-m-d",strtotime($inv_date));
            //  $inv_no_date = date("Y-m-d",strtotime($inv_no_date));
             //  $po_no_date = date("Y-m-d",strtotime($po_no_date));
             //  $loc_no_date = date("Y-m-d",strtotime($loc_no_date));
           // $contract_no_date = date("Y-m-d",strtotime($contract_no_date));
            //    $exchange_rate = date("Y-m-d",strtotime($exchange_rate));
               
     $sql_users ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id";
   //  echo $sql_users ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id ";
           
                $iecwise1_users = $db1_insert_invoice_summery->query($sql_users);
                $iecwise_data1_users = array();
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                 
                   $c= $inv_no."-".$inv_date;
                    //skip dupliacte entry     
             $a= $this->inArray_invoice_summery($iecwise_data1_users,$c); // Output - value exists
             print_r($a);   
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             

            echo $sql_insert_invoice_summery =
                "INSERT INTO `invoice_summary` (`invoice_id`, `sbs_id`, `s_no_inv`, `inv_no`, `inv_date`, `inv_no_date`, `po_no_date`, `loc_no_date`, `contract_no_date`, `ad_code_inv`, `invterm`, `exporters_name_and_address`, `buyers_name_and_address`, `third_party_name_and_address`, `buyers_aeo_status`, `invoice_value`, `invoice_value_currency`, `fob_value_inv`, `fob_value_currency`, `freight_val`, `freight_currency`, `insurance_val`, `insurance_currency`, `discount_val`, `discount_val_currency`, `commison`, `comission_currency`, `deduct`, `deduct_currency`, `p_c_val`, `p_c_val_currency`, `exchange_rate`, `created_at`)
VALUES('" .
                $invoice_id .
                "','" .
                $sbs_id .
                "','" .
                $s_no_inv .
                "','" .
                $inv_no .
                "','" .
                $inv_date .
                "','" .
                $inv_no_date .
                "','" .
                $po_no_date .
                "','" .
                $loc_no_date .
                "','" .
                $contract_no_date .
                "','" .
                $ad_code_inv .
                "','" .
                $invterm .
                "','" .
                $exporters_name_and_address .
                "','" .
                $buyers_name_and_address .
                "','" .
                $third_party_name_and_address .
                "','" .
                $buyers_aeo_status .
                "','" .
                $invoice_value .
                "','" .
                $invoice_value_currency .
                "','" .
                $fob_value_inv .
                "','" .
                $fob_value_currency .
                "','" .
                $freight_val .
                "','" .
                $freight_currency .
                "','" .
                $insurance_val .
                "','" .
                $insurance_currency .
                "','" .
                $discount_val .
                "','" .
                $discount_val_currency .
                "','" .
                $commison .
                "','" .
                $comission_currency .
                "','" .
                $deduct .
                "','" .
                $deduct_currency .
                "','" .
                $p_c_val .
                "','" .
                $p_c_val_currency .
                "','" .
                $exchange_rate .
                "','" .
                $created_at .
                "')";
            $copy_insert_invoice_summery = $db1_insert_invoice_summery->query(
                $sql_insert_invoice_summery
            );
        }

       
    }*/
    

  public function invoice_summery(){
        /******************************************************************Start invoice_summery***************************************************************************************/
 $query_invoice_summery ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id ";
      
// echo $query_invoice_summery ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id";
        $statement_invoice_summery = $this->db->query($query_invoice_summery);
        $iecwise_invoice_summery = [];
        $result_invoice_summery = $statement_invoice_summery->result_array();
       
        foreach ($result_invoice_summery as $str_invoice_summery) {
            $iec_invoice_summery = $str_invoice_summery["iec"];
            $sql_invoice_summery = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_invoice_summery'";
            $iecwise_invoice_summery = $this->db->query($sql_invoice_summery);
            $iecwise_data_invoice_summery = $iecwise_invoice_summery->result_array();
           
            $db1_insert_invoice_summery = $this->database_connection(
                $iecwise_data_invoice_summery[0]["lucrative_users_id"]
            );
            
          
            
           // if (get_magic_quotes_gpc()) {
                $invoice_id = addslashes($str_invoice_summery["invoice_id"]);
                $sbs_id = addslashes($str_invoice_summery["sbs_id"]);
                $s_no_inv = addslashes($str_invoice_summery["s_no_inv"]);
                $inv_no = addslashes($str_invoice_summery["inv_no"]);
                $inv_date = addslashes($str_invoice_summery["inv_date"]);
                $inv_no_date = addslashes(
                    $str_invoice_summery["inv_no_date"]
                );
                $po_no_date = addslashes($str_invoice_summery["po_no_date"]);
                $loc_no_date = addslashes(
                    $str_invoice_summery["loc_no_date"]
                );
                $contract_no_date = addslashes(
                    $str_invoice_summery["contract_no_date"]
                );
                $ad_code_inv = addslashes(
                    $str_invoice_summery["ad_code_inv"]
                );
                $invterm = addslashes($str_invoice_summery["invterm"]);
                $exporters_name_and_address = addslashes(
                    $str_invoice_summery["exporters_name_and_address"]
                );
                $buyers_name_and_address = addslashes(
                    $str_invoice_summery["buyers_name_and_address"]
                );
                $third_party_name_and_address = addslashes(
                    $str_invoice_summery["third_party_name_and_address"]
                );
                $buyers_aeo_status = addslashes(
                    $str_invoice_summery["buyers_aeo_status"]
                );
                $invoice_value = addslashes(
                    $str_invoice_summery["invoice_value"]
                );
                $invoice_value_currency = addslashes(
                    $str_invoice_summery["invoice_value_currency"]
                );
                $fob_value_inv = addslashes(
                    $str_invoice_summery["fob_value_inv"]
                );
                $fob_value_currency = addslashes(
                    $str_invoice_summery["fob_value_currency"]
                );
                $freight_val = addslashes(
                    $str_invoice_summery["freight_val"]
                );
                $freight_currency = addslashes(
                    $str_invoice_summery["freight_currency"]
                );
                $insurance_val = addslashes(
                    $str_invoice_summery["insurance_val"]
                );
                $insurance_currency = addslashes(
                    $str_invoice_summery["insurance_currency"]
                );
                $discount_val = addslashes(
                    $str_invoice_summery["discount_val"]
                );
                $discount_val_currency = addslashes(
                    $str_invoice_summery["discount_val_currency"]
                );
                $commison = addslashes($str_invoice_summery["commison"]);
                $comission_currency = addslashes(
                    $str_invoice_summery["comission_currency"]
                );
                $deduct = addslashes($str_invoice_summery["deduct"]);
                $deduct_currency = addslashes(
                    $str_invoice_summery["deduct_currency"]
                );
                $p_c_val = addslashes($str_invoice_summery["p_c_val"]);
                $p_c_val_currency = addslashes(
                    $str_invoice_summery["p_c_val_currency"]
                );
                $exchange_rate = addslashes(
                    $str_invoice_summery["exchange_rate"]
                );
                $created_at = addslashes($str_invoice_summery["created_at"]);
                
                
              //    $inv_date = date("Y-m-d",strtotime($inv_date));
            //  $inv_no_date = date("Y-m-d",strtotime($inv_no_date));
             //  $po_no_date = date("Y-m-d",strtotime($po_no_date));
             //  $loc_no_date = date("Y-m-d",strtotime($loc_no_date));
           // $contract_no_date = date("Y-m-d",strtotime($contract_no_date));
            //    $exchange_rate = date("Y-m-d",strtotime($exchange_rate));
                /********************checking dupliacte entries d2d***********************/
    echo $sql_users ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id";
   //  echo $sql_users ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id ";
           
                $iecwise1_users = $db1_insert_invoice_summery->query($sql_users);
                $iecwise_data1_users = array();
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                 
                   $c= $inv_no."-".$inv_date;
                    //skip dupliacte entry     
             $a= $this->inArray_invoice_summery($iecwise_data1_users,$c); // Output - value exists
            
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             else{
            
 /***********************************************************************/
            echo $sql_insert_invoice_summery =
                "INSERT INTO `invoice_summary` (`invoice_id`, `sbs_id`, `s_no_inv`, `inv_no`, `inv_date`, `inv_no_date`, `po_no_date`, `loc_no_date`, `contract_no_date`, `ad_code_inv`, `invterm`, `exporters_name_and_address`, `buyers_name_and_address`, `third_party_name_and_address`, `buyers_aeo_status`, `invoice_value`, `invoice_value_currency`, `fob_value_inv`, `fob_value_currency`, `freight_val`, `freight_currency`, `insurance_val`, `insurance_currency`, `discount_val`, `discount_val_currency`, `commison`, `comission_currency`, `deduct`, `deduct_currency`, `p_c_val`, `p_c_val_currency`, `exchange_rate`, `created_at`)
                
VALUES('" .
                $invoice_id .
                "','" .
                $sbs_id .
                "','" .
                $s_no_inv .
                "','" .
                $inv_no .
                "','" .
                $inv_date .
                "','" .
                $inv_no_date .
                "','" .
                $po_no_date .
                "','" .
                $loc_no_date .
                "','" .
                $contract_no_date .
                "','" .
                $ad_code_inv .
                "','" .
                $invterm .
                "','" .
                $exporters_name_and_address .
                "','" .
                $buyers_name_and_address .
                "','" .
                $third_party_name_and_address .
                "','" .
                $buyers_aeo_status .
                "','" .
                $invoice_value .
                "','" .
                $invoice_value_currency .
                "','" .
                $fob_value_inv .
                "','" .
                $fob_value_currency .
                "','" .
                $freight_val .
                "','" .
                $freight_currency .
                "','" .
                $insurance_val .
                "','" .
                $insurance_currency .
                "','" .
                $discount_val .
                "','" .
                $discount_val_currency .
                "','" .
                $commison .
                "','" .
                $comission_currency .
                "','" .
                $deduct .
                "','" .
                $deduct_currency .
                "','" .
                $p_c_val .
                "','" .
                $p_c_val_currency .
                "','" .
                $exchange_rate .
                "','" .
                $created_at .
                "')";
               
            $copy_insert_invoice_summery = $db1_insert_invoice_summery->query(
                $sql_insert_invoice_summery
            );
        }
    }

        /******************************************************************Start invoice_summery***************************************************************************************/
}  

public function inArray_sb_file_status($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['reference_code']."-".$val['be_no']."-".$val['be_date'];
             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
             echo "value does not exists";
     
         } else {
     
            echo "value exists";
     
         }
         return $index;
    }
public function sb_file_status(){
        /******************************************************************Start sb_file_status***************************************************************************************/

        echo $query_sb_file_status = "SELECT * FROM sb_file_status ";
        $statement_sb_file_status = $this->db->query($query_sb_file_status);
        $iecwise_sb_file_status = [];
        $result_sb_file_status = $statement_sb_file_status->result_array();
       // print_r($result_sb_file_status);exit;

        foreach ($result_sb_file_status as $str_sb_file_status) {
            $iec_sb_file_status = $str_sb_file_status["user_iec_no"];
            $sql_sb_file_status = "SELECT lucrative_users_id  FROM lucrative_users where iec_no LIKE '%$iec_sb_file_status'";
            $iecwise_sb_file_status = $this->db->query($sql_sb_file_status);
            $iecwise_data_sb_file_status = $iecwise_sb_file_status->result_array();
            $db1_sb_file_status = $this->database_connection(
                $iecwise_data_sb_file_status[0]["lucrative_users_id"]
            );
           // if (get_magic_quotes_gpc()) {
                $sb_file_status_id = addslashes(
                    $str_sb_file_status["sb_file_status_id"]
                );
                $pdf_filepath = addslashes(
                    $str_sb_file_status["pdf_filepath"]
                );
                $pdf_filename = addslashes(
                    $str_sb_file_status["pdf_filename"]
                );
                $user_iec_no = addslashes($str_sb_file_status["user_iec_no"]);
                $lucrative_users_id = addslashes(
                    $str_sb_file_status["lucrative_users_id"]
                );
                $file_iec_no = addslashes($str_sb_file_status["file_iec_no"]);
                $sb_no = addslashes($str_sb_file_status["sb_no"]);
                $sb_date = addslashes($str_sb_file_status["sb_date"]);
                $stage = addslashes($str_sb_file_status["stage"]);
                $status = addslashes($str_sb_file_status["status"]);
                $remarks = addslashes($str_sb_file_status["remarks"]);
                $created_at = addslashes($str_sb_file_status["created_at"]);
                $br = addslashes($str_sb_file_status["br"]);
                $is_processed = addslashes(
                    $str_sb_file_status["is_processed"]
                );
	 /********************checking dupliacte entries d2d***********************/
            $sql_users = "SELECT CONCAT(n1.be_no,"-",invoice_and_valuation_details.s_no, "-", duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id ";
         
                $iecwise1_users = $db1_sb_file_status->query($sql_users);
                $iecwise_data1_users = array();
                
                while ($rowusers = $iecwise1_users->fetch_assoc()) {
                $iecwise_data1_users[] = $rowusers;
                }
                    
                   $c= $reference_code."-".$be_no."-".$be_date;
                    //skip dupliacte entry     
             $a= $this->inArray_ship_bill_summary($iecwise_data1_users,$c); // Output - value exists
             if ($a==1) {
                  echo "Duplicate";"============";continue;
             }
             
 /***********************************************************************/
            echo $sql_insert_sb_file_status =
                "INSERT INTO `sb_file_status` (`sb_file_status_id`, `pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`, `sb_no`, `sb_date`, `stage`, `status`, `remarks`, `created_at`, `br`, `is_processed`) 
VALUES('" .
                $sb_file_status_id .
                "','" .
                $pdf_filepath .
                "','" .
                $pdf_filename .
                "','" .
                $user_iec_no .
                "','" .
                $lucrative_users_id .
                "','" .
                $file_iec_no .
                "','" .
                $sb_no .
                "','" .
                $sb_date .
                "','" .
                $stage .
                "','" .
                $status .
                "','" .
                $remarks .
                "','" .
                $created_at .
                "','" .
                $br .
                "','" .
                $is_processed .
                "')";
            $copy_insert_sb_file_status = $db1_sb_file_status->query(
                $sql_insert_sb_file_status
            );
        }
        /******************************************************************Start sb_file_status***************************************************************************************/
    }
   
public function database_connection($id){
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";

        echo $db_name1 = "lucrativeesystem_D2D_".$id;
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);
        // Check connection
        if ($Db1->connect_error) {
            die("Connection failed: " . $Db1->connect_error);
        } else {
            echo "Connected successfully";
        }
        return $Db1;
    }
public function saveimport_export_data(){
        $post = $this->input->post();
    }

public function iec_signup(){
        $this->load->view("common/header");
        $this->load->view("admin/signup_iec_form.php");
        $this->load->view("common/footer");
    }

public function register_iec_user($user_id){
        $post = $this->input->post();
       /* $alphabet =
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $data = [
            "fullname" => $post["first_name"] . " " . $post["last_name"],
            "email" => $post["iec_email"],
            "iec_no" => $post["iec_no"],
            "mobile" => $post["mobile_no"],
            "password" => implode($pass),
            "role" => "admin",
            "created_at" => date("Y-m-d h:i:s"),
        ];*/
        //$result = $this->Common_model->insert_iec_user_entry($data);
       
       
        
        
        $result = $user_id;

        if ($result) {
            $db_name = 'lucrativeesystem_D2D_'.$result;
            $servername = "localhost";
            $username = "root";
            $password = "%!#^bFjB)z8C";
            
            // Create connection
            $conn = new mysqli($servername, $username, $password);
            // Check connection
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }
            
            // Create database
           echo $sql = "CREATE DATABASE $db_name";
            
            if ($conn->query($sql) === TRUE) {
              echo "Database created successfully";
           
                
                $db2=$this->database_connection($result);
                $db2->query("use ".$db_name. "");
            /*****************************************Create Table aa_dfia_licence_details***************************************************************/
               $db2->query("CREATE TABLE `aa_dfia_licence_details` (
              `dfia_licence_details_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `item_id` int(20) DEFAULT NULL,
              `inv_s_no` int(20) DEFAULT NULL,
              `item_s_no_` int(20) DEFAULT NULL,
              `licence_no` text,
              `descn_of_export_item` text,
              `exp_s_no` int(20) DEFAULT NULL,
              `expqty` text,
              `uqc_aa` varchar(256) DEFAULT NULL,
              `fob_value` text,
              `sion` text,
              `descn_of_import_item` text,
              `imp_s_no` varchar(256) DEFAULT NULL,
              `impqt` text,
              `uqc_` varchar(256) DEFAULT NULL,
              `indig_imp` varchar(256) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `a` varchar(256) DEFAULT NULL,
              `b` varchar(256) DEFAULT NULL,
              `c` varchar(256) DEFAULT NULL,
              `d` varchar(256) DEFAULT NULL,
              `e` varchar(256) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
             
    /*****************************************Create Table bill_bond_details***************************************************************/
               $db2->query("CREATE TABLE `bill_bond_details` (
                          `bond_details_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `boe_id` int(20) NOT NULL,
                          `bond_no` varchar(256) DEFAULT NULL,
                          `port` varchar(256) DEFAULT NULL,
                          `bond_cd` varchar(256) DEFAULT NULL,
                          `debt_amt` decimal(12,0) DEFAULT NULL,
                          `bg_amt` decimal(12,0) DEFAULT NULL,
                          `created_at` datetime DEFAULT NULL,
                          `a` varchar(256) DEFAULT NULL,
                          `b` varchar(256) DEFAULT NULL,
                          `c` varchar(256) DEFAULT NULL,
                          `d` varchar(256) DEFAULT NULL,
                          `e` varchar(256) DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
           /*****************************************Create Table bill_container_details***************************************************************/
               $db2->query("CREATE TABLE `bill_container_details` (
                          `container_details_pk` int(20) NOT NULL,
                          `boe_id` int(20) NOT NULL,
                          `sno` int(20) DEFAULT NULL,
                          `lcl_fcl` varchar(256) DEFAULT NULL,
                          `truck` varchar(256) DEFAULT NULL,
                          `seal` varchar(256) DEFAULT NULL,
                          `container_number` varchar(256) DEFAULT NULL,
                          `created_at` datetime DEFAULT NULL,
                          `a` varchar(256) DEFAULT NULL,
                          `b` varchar(256) DEFAULT NULL,
                          `c` varchar(256) DEFAULT NULL,
                          `d` varchar(256) DEFAULT NULL,
                          `e` varchar(256) DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
             
                /*****************************************Create Table bill_licence_details***************************************************************/
               $db2->query("CREATE TABLE `bill_licence_details` (
                                  `licence_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                  `duties_id` int(20) DEFAULT NULL,
                                  `invsno` int(20) DEFAULT NULL,
                                  `itemsn` int(20) DEFAULT NULL,
                                  `lic_slno` int(20) DEFAULT NULL,
                                  `lic_no` varchar(256) DEFAULT NULL,
                                  `lic_date` varchar(20) DEFAULT NULL,
                                  `code` varchar(256) DEFAULT NULL,
                                  `port` varchar(256) DEFAULT NULL,
                                  `debit_value` decimal(11,2) DEFAULT NULL,
                                  `qty` varchar(20) DEFAULT NULL,
                                  `uqc_lc_d` varchar(20) DEFAULT NULL,
                                  `debit_duty` varchar(256) DEFAULT NULL,
                                  `created_at` varchar(256) DEFAULT NULL,
                                  `a` varchar(256) DEFAULT NULL,
                                  `b` varchar(256) DEFAULT NULL,
                                  `c` varchar(256) DEFAULT NULL,
                                  `d` varchar(256) DEFAULT NULL,
                                  `e` varchar(256) DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        /********************************************************************************************************/  
        
       /*****************************************Create Table bill_manifest_details***************************************************************/
               $db2->query("CREATE TABLE `bill_manifest_details` (
                              `manifest_details_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `boe_id` int(20) NOT NULL,
                              `igm_no` varchar(256) DEFAULT NULL,
                              `igm_date` date DEFAULT NULL,
                              `inw_date` date DEFAULT NULL,
                              `gigmno` varchar(256) DEFAULT NULL,
                              `gigmdt` date DEFAULT NULL,
                              `mawb_no` varchar(256) DEFAULT NULL,
                              `mawb_date` date DEFAULT NULL,
                              `hawb_no` varchar(256) DEFAULT NULL,
                              `hawb_date` date DEFAULT NULL,
                              `pkg` int(20) DEFAULT NULL,
                              `gw` int(20) DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
               /*****************************************Create Table bill_of_entry_summary***************************************************************/
               $db2->query("CREATE TABLE `bill_of_entry_summary` (
                                  `boe_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                  `boe_file_status_id` int(20) DEFAULT NULL,
                                  `invoice_title` varchar(256) DEFAULT NULL,
                                  `port` varchar(256) DEFAULT NULL,
                                  `port_code` varchar(256) DEFAULT NULL,
                                  `be_no` varchar(256) DEFAULT NULL,
                                  `be_date` date DEFAULT NULL,
                                  `be_type` varchar(256) DEFAULT NULL,
                                  `iec_br` varchar(256) DEFAULT NULL,
                                  `iec_no` varchar(256) DEFAULT NULL,
                                  `br` varchar(256) DEFAULT NULL,
                                  `gstin_type` varchar(256) DEFAULT NULL,
                                  `cb_code` varchar(256) DEFAULT NULL,
                                  `ad_code` varchar(256) DEFAULT NULL,
                                  `nos` int(20) DEFAULT NULL,
                                  `pkg` int(20) DEFAULT NULL,
                                  `item` int(20) DEFAULT NULL,
                                  `g_wt_kgs` int(20) DEFAULT NULL,
                                  `cont` int(20) DEFAULT NULL,
                                  `be_status` varchar(256) DEFAULT NULL,
                                  `mode` varchar(256) DEFAULT NULL,
                                  `def_be` varchar(256) DEFAULT NULL,
                                  `kacha` varchar(256) DEFAULT NULL,
                                  `sec_48` varchar(256) DEFAULT NULL,
                                  `reimp` varchar(256) DEFAULT NULL,
                                  `adv_be` varchar(256) DEFAULT NULL,
                                  `assess` varchar(256) DEFAULT NULL,
                                  `exam` varchar(256) DEFAULT NULL,
                                  `hss` varchar(256) DEFAULT NULL,
                                  `first_check` varchar(256) DEFAULT NULL,
                                  `prov_final` varchar(256) DEFAULT NULL,
                                  `country_of_origin` varchar(256) DEFAULT NULL,
                                  `country_of_consignment` varchar(256) DEFAULT NULL,
                                  `port_of_loading` varchar(256) DEFAULT NULL,
                                  `port_of_shipment` varchar(256) DEFAULT NULL,
                                  `importer_name_and_address` varchar(256) DEFAULT NULL,
                                  `cb_name` varchar(256) DEFAULT NULL,
                                  `aeo` varchar(256) DEFAULT NULL,
                                  `ucr` varchar(256) DEFAULT NULL,
                                  `bcd` decimal(12,0) DEFAULT NULL,
                                  `acd` decimal(12,0) DEFAULT NULL,
                                  `sws` decimal(12,0) DEFAULT NULL,
                                  `nccd` decimal(12,0) DEFAULT NULL,
                                  `add` decimal(12,0) DEFAULT NULL,
                                  `cvd` decimal(12,0) DEFAULT NULL,
                                  `igst` decimal(12,0) DEFAULT NULL,
                                  `g_cess` decimal(12,0) DEFAULT NULL,
                                  `sg` decimal(12,0) DEFAULT NULL,
                                  `saed` decimal(12,0) DEFAULT NULL,
                                  `gsia` decimal(12,0) DEFAULT NULL,
                                  `tta` decimal(12,0) DEFAULT NULL,
                                  `health` decimal(12,0) DEFAULT NULL,
                                  `total_duty` decimal(12,0) DEFAULT NULL,
                                  `int` decimal(12,0) DEFAULT NULL,
                                  `pnlty` decimal(12,0) DEFAULT NULL,
                                  `fine` decimal(12,0) DEFAULT NULL,
                                  `tot_ass_val` decimal(12,0) DEFAULT NULL,
                                  `tot_amount` decimal(12,0) DEFAULT NULL,
                                  `wbe_no` varchar(256) DEFAULT NULL,
                                  `wbe_date` date DEFAULT NULL,
                                  `wbe_site` varchar(256) DEFAULT NULL,
                                  `wh_code` varchar(256) DEFAULT NULL,
                                  `submission_date` date DEFAULT NULL,
                                  `assessment_date` date DEFAULT NULL,
                                  `examination_date` date DEFAULT NULL,
                                  `ooc_date` date DEFAULT NULL,
                                  `submission_time` varchar(256) DEFAULT NULL,
                                  `assessment_time` varchar(256) DEFAULT NULL,
                                  `examination_time` varchar(256) DEFAULT NULL,
                                  `ooc_time` varchar(256) DEFAULT NULL,
                                  `submission_exchange_rate` varchar(256) DEFAULT NULL,
                                  `assessment_exchange_rate` varchar(256) DEFAULT NULL,
                                  `ooc_no` varchar(256) DEFAULT NULL,
                                  `ooc_date_` date DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `examination_exchange_rate` varchar(256) NOT NULL,
                                  `ooc_exchange_rate` varchar(256) NOT NULL,
                                  `a` varchar(256) DEFAULT NULL,
                                  `b` varchar(256) DEFAULT NULL,
                                  `c` varchar(256) DEFAULT NULL,
                                  `d` varchar(256) DEFAULT NULL,
                                  `e` varchar(256) DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/
        
        
               /*****************************************Create Table bill_payment_details***************************************************************/
               $db2->query("CREATE TABLE `bill_payment_details` (
                              `payment_details_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `boe_id` int(20) NOT NULL,
                              `sr_no` int(20) DEFAULT NULL,
                              `challan_no` varchar(256) DEFAULT NULL,
                              `paid_on` date DEFAULT NULL,
                              `amount` decimal(12,0) DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
            /*****************************************Create Table boe_delete_logs***************************************************************/
               $db2->query("CREATE TABLE `boe_delete_logs` (
                           `boe_delete_logs_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `filename` varchar(256) DEFAULT NULL,
                          `be_no` varchar(256) DEFAULT NULL,
                          `be_date` datetime DEFAULT NULL,
                          `iec_no` varchar(256) DEFAULT NULL,
                          `br` varchar(256) DEFAULT NULL,
                          `fullname` varchar(256) DEFAULT NULL,
                          `email` varchar(256) DEFAULT NULL,
                          `mobile` varchar(256) DEFAULT NULL,
                          `deleted_at` datetime DEFAULT NULL,
                          `a` varchar(256) DEFAULT NULL,
                          `b` varchar(256) DEFAULT NULL,
                          `c` varchar(256) DEFAULT NULL,
                          `d` varchar(256) DEFAULT NULL,
                          `e` varchar(256) DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  

            /*****************************************Create Table boe_file_status***************************************************************/
        
            $db2->query("CREATE TABLE `boe_file_status` (
                               `boe_file_status_id` int(20) NOT NULL,
                              `pdf_filepath` varchar(256) DEFAULT NULL,
                              `pdf_filename` varchar(256) DEFAULT NULL,
                              `user_iec_no` varchar(256) DEFAULT NULL,
                              `lucrative_users_id` int(20) DEFAULT NULL,
                              `excel_filepath` varchar(256) DEFAULT NULL,
                              `excel_filename` varchar(256) DEFAULT NULL,
                              `pdf_to_excel_date` datetime DEFAULT NULL,
                              `pdf_to_excel_status` varchar(256) DEFAULT NULL,
                              `file_iec_no` varchar(256) DEFAULT NULL,
                              `br` varchar(256) DEFAULT NULL,
                              `be_no` varchar(256) DEFAULT NULL,
                              `stage` varchar(256) DEFAULT NULL,
                              `status` varchar(256) DEFAULT NULL,
                              `remarks` varchar(256) DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `is_deleted` tinyint(20) DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL,
                              `is_processed` varchar(1) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
                    /*****************************************Create Table cb_file_status***************************************************************/
        
                        $db2->query("CREATE TABLE `cb_file_status` (
                             `cb_file_status_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `pdf_filepath` varchar(256) DEFAULT NULL,
                              `pdf_filename` varchar(256) DEFAULT NULL,
                              `user_iec_no` varchar(256) DEFAULT NULL,
                              `lucrative_users_id` int(20) DEFAULT NULL,
                              `file_iec_no` varchar(256) DEFAULT NULL,
                              `cb_no` varchar(256) DEFAULT NULL,
                              `cb_date` date DEFAULT NULL,
                              `stage` varchar(256) DEFAULT NULL,
                              `status` varchar(256) DEFAULT NULL,
                              `remarks` varchar(256) DEFAULT NULL,
                              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `br` varchar(256) DEFAULT NULL,
                              `is_processed` varchar(256) DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
            /*****************************************Create Table challan_details***************************************************************/
        
                        $db2->query("CREATE TABLE `challan_details` (
                                          `challan_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                          `sbs_id` int(20) DEFAULT NULL,
                                          `sr_no` text,
                                          `challan_no` text,
                                          `paymt_dt` text,
                                          `amount` text,
                                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                          `a` varchar(256) DEFAULT NULL,
                                          `b` varchar(256) DEFAULT NULL,
                                          `c` varchar(256) DEFAULT NULL,
                                          `d` varchar(256) DEFAULT NULL,
                                          `e` varchar(256) DEFAULT NULL
                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
             /*****************************************Create Table courier_bill_container_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_container_details` (
                                       `courier_bill_of_entry_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `container_details_id` int(20) DEFAULT NULL,
                                      `container_details_srno` int(20) DEFAULT NULL,
                                      `container` varchar(256) DEFAULT NULL,
                                      `seal_number` varchar(256) DEFAULT NULL,
                                      `fcl_lcl` varchar(256) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
           /*****************************************Create Table courier_bill_bond_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_bond_details` (
                                      `courier_bill_of_entry_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `bond_details_id` int(20) DEFAULT NULL,
                                      `bond_details_srno` int(20) DEFAULT NULL,
                                      `bond_type` varchar(256) DEFAULT NULL,
                                      `bond_number` varchar(256) DEFAULT NULL,
                                      `clearance_of_imported_goods_bond_already_registered_customs` varchar(256) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
         /*****************************************Create Table courier_bill_duty_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_duty_details` (
                                      `items_detail_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `duty_details_id` int(20) DEFAULT NULL,
                                      `bcd_duty_head` varchar(256) DEFAULT NULL,
                                      `bcd_ad_valorem` decimal(12,1) DEFAULT NULL,
                                      `bcd_specific_rate` int(20) DEFAULT NULL,
                                      `bcd_duty_forgone` int(20) DEFAULT NULL,
                                      `bcd_duty_amount` int(20) DEFAULT NULL,
                                      `aidc_duty_head` varchar(256) DEFAULT NULL,
                                      `aidc_ad_valorem` int(20) DEFAULT NULL,
                                      `aidc_specific_rate` int(20) DEFAULT NULL,
                                      `aidc_duty_forgone` int(20) DEFAULT NULL,
                                      `aidc_duty_amount` int(20) DEFAULT NULL,
                                      `sw_srchrg_duty_head` varchar(256) DEFAULT NULL,
                                      `sw_srchrg_ad_valorem` int(20) DEFAULT NULL,
                                      `sw_srchrg_specific_rate` int(20) DEFAULT NULL,
                                      `sw_srchrg_duty_forgone` int(20) DEFAULT NULL,
                                      `sw_srchrg_duty_amount` int(20) DEFAULT NULL,
                                      `igst_duty_head` varchar(256) DEFAULT NULL,
                                      `igst_ad_valorem` int(20) DEFAULT NULL,
                                      `igst_specific_rate` int(20) DEFAULT NULL,
                                      `igst_duty_forgone` int(20) DEFAULT NULL,
                                      `igst_duty_amount` int(20) DEFAULT NULL,
                                      `cmpnstry_duty_head` varchar(256) DEFAULT NULL,
                                      `cmpnstry_ad_valorem` int(20) DEFAULT NULL,
                                      `cmpnstry_specific_rate` int(20) DEFAULT NULL,
                                      `cmpnstry_duty_forgone` int(20) DEFAULT NULL,
                                      `cmpnstry_duty_amount` int(20) DEFAULT NULL,
                                      `dummy5_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy5_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy5_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy5_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy5_duty_amount` varchar(256) DEFAULT NULL,
                                      `dummy6_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy6_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy6_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy6_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy6_duty_amount` varchar(256) DEFAULT NULL,
                                      `dummy7_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy7_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy7_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy7_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy7_duty_amount` varchar(256) DEFAULT NULL,
                                      `dummy8_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy8_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy8_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy8_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy8_duty_amount` varchar(256) DEFAULT NULL,
                                      `dummy9_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy9_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy9_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy9_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy9_duty_amount` varchar(256) DEFAULT NULL,
                                      `dummy10_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy10_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy10_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy10_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy10_duty_amount` varchar(256) DEFAULT NULL,
                                      `dummy11_duty_head` varchar(256) DEFAULT NULL,
                                      `dummy11_ad_valorem` varchar(256) DEFAULT NULL,
                                      `dummy11_specific_rate` varchar(256) DEFAULT NULL,
                                      `dummy11_duty_forgone` varchar(256) DEFAULT NULL,
                                      `dummy11_duty_amount` varchar(256) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/ 
        
        
          /*****************************************Create Table courier_bill_igm_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_igm_details` (
                                      `courier_bill_of_entry_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `igm_details_id` int(20) DEFAULT NULL,
                                      `airlines` varchar(256) DEFAULT NULL,
                                      `flight_no` varchar(256) DEFAULT NULL,
                                      `airport_of_arrival` varchar(256) DEFAULT NULL,
                                      `date_of_arrival` varchar(256) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
          /*****************************************Create Table courier_bill_invoice_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_invoice_details` (
                                      `courier_bill_of_entry_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `invoice_detail_id` int(20) DEFAULT NULL,
                                    `invoice_number` int(20) DEFAULT NULL,
                                    `date_of_invoice` varchar(256) DEFAULT NULL,
                                    `purchase_order_number` varchar(256) DEFAULT NULL,
                                    `date_of_purchase_order` varchar(256) DEFAULT NULL,
                                    `contract_number` varchar(256) DEFAULT NULL,
                                    `date_of_contract` varchar(256) DEFAULT NULL,
                                    `letter_of_credit` varchar(256) DEFAULT NULL,
                                    `date_of_letter_of_credit` varchar(256) DEFAULT NULL,
                                    `supplier_details_name` varchar(256) DEFAULT NULL,
                                    `supplier_details_address` varchar(65) DEFAULT NULL,
                                    `if_supplier_is_not_the_seller_name` varchar(256) DEFAULT NULL,
                                    `if_supplier_is_not_the_seller_address` varchar(256) DEFAULT NULL,
                                    `broker_agent_details_name` varchar(256) DEFAULT NULL,
                                    `broker_agent_details_address` varchar(256) DEFAULT NULL,
                                    `nature_of_transaction` varchar(256) DEFAULT NULL,
                                    `if_others` varchar(256) DEFAULT NULL,
                                    `terms_of_payment` varchar(256) DEFAULT NULL,
                                    `conditions_or_restrictions_if_any_attached_to_sale` varchar(256) DEFAULT NULL,
                                    `method_of_valuation` varchar(256) DEFAULT NULL,
                                    `terms_of_invoice` varchar(256) DEFAULT NULL,
                                    `invoice_value` int(20) DEFAULT NULL,
                                    `currency` varchar(256) DEFAULT NULL,
                                    `freight_rate` decimal(12,2) DEFAULT NULL,
                                    `freight_amount` decimal(12,1) DEFAULT NULL,
                                    `freight_currency` varchar(256) DEFAULT NULL,
                                    `insurance_rate` decimal(12,2) DEFAULT NULL,
                                    `insurance_amount` decimal(12,2) DEFAULT NULL,
                                    `insurance_currency` varchar(256) DEFAULT NULL,
                                    `loading_unloading_and_handling_charges_rule_rate` varchar(256) DEFAULT NULL,
                                    `loading_unloading_and_handling_charges_rule_amount` varchar(256) DEFAULT NULL,
                                    `loading_unloading_and_handling_charges_rule_currency` varchar(256) DEFAULT NULL,
                                    `other_charges_related_to_the_carriage_of_goods_rate` varchar(256) DEFAULT NULL,
                                    `other_charges_related_to_the_carriage_of_goods_amount` varchar(256) DEFAULT NULL,
                                    `other_charges_related_to_the_carriage_of_goods_currency` varchar(256) DEFAULT NULL,
                                    `brokerage_and_commission_rate` varchar(256) DEFAULT NULL,
                                    `brokerage_and_commission_amount` varchar(256) DEFAULT NULL,
                                    `brokerage_and_commission_currency` varchar(256) DEFAULT NULL,
                                    `cost_of_containers_rate` varchar(256) DEFAULT NULL,
                                    `cost_of_containers_amount` varchar(256) DEFAULT NULL,
                                    `cost_of_containers_currency` varchar(256) DEFAULT NULL,
                                    `cost_of_packing_rate` varchar(256) DEFAULT NULL,
                                    `cost_of_packing_amount` varchar(256) DEFAULT NULL,
                                    `cost_of_packing_currency` varchar(256) DEFAULT NULL,
                                    `dismantling_transport_handling_in_country_export_rate` varchar(256) DEFAULT NULL,
                                    `dismantling_transport_handling_in_country_export_amount` varchar(256) DEFAULT NULL,
                                    `dismantling_transport_handling_in_country_export_currency` varchar(256) DEFAULT NULL,
                                    `cost_of_goods_and_ser_vices_supplied_by_buyer_rate` varchar(256) DEFAULT NULL,
                                    `cost_of_goods_and_ser_vices_supplied_by_buyer_amount` varchar(256) DEFAULT NULL,
                                    `cost_of_goods_and_ser_vices_supplied_by_buyer_currency` varchar(256) DEFAULT NULL,
                                    `documentation_rate` varchar(256) DEFAULT NULL,
                                    `documentation_amount` varchar(256) DEFAULT NULL,
                                    `documentation_currency` varchar(256) DEFAULT NULL,
                                    `country_of_origin_certificate_rate` varchar(256) DEFAULT NULL,
                                    `country_of_origin_certificate_amount` varchar(256) DEFAULT NULL,
                                    `country_of_origin_certificate_currency` varchar(256) DEFAULT NULL,
                                    `royalty_and_license_fees_rate` varchar(256) DEFAULT NULL,
                                    `royalty_and_license_fees_amount` varchar(256) DEFAULT NULL,
                                    `royalty_and_license_fees_currency` varchar(256) DEFAULT NULL,
                                    `value_of_proceeds_which_accrue_to_seller_rate` varchar(256) DEFAULT NULL,
                                    `value_of_proceeds_which_accrue_to_seller_amount` varchar(256) DEFAULT NULL,
                                    `value_of_proceeds_which_accrue_to_seller_currency` varchar(256) DEFAULT NULL,
                                    `cost_warranty_service_if_any_provided_seller_rate` varchar(256) DEFAULT NULL,
                                    `cost_warranty_service_if_any_provided_seller_amount` varchar(256) DEFAULT NULL,
                                    `cost_warranty_service_if_any_provided_seller_currency` varchar(256) DEFAULT NULL,
                                    `other_payments_satisfy_obligation_rate` varchar(256) DEFAULT NULL,
                                    `other_payments_satisfy_obligation_amount` varchar(256) DEFAULT NULL,
                                    `other_payments_satisfy_obligation_currency` varchar(256) DEFAULT NULL,
                                    `other_charges_and_payments_if_any_rate` int(20) DEFAULT NULL,
                                    `other_charges_and_payments_if_any_amount` int(20) DEFAULT NULL,
                                    `other_charges_and_payments_if_any_currency` varchar(256) DEFAULT NULL,
                                    `discount_amount` varchar(256) DEFAULT NULL,
                                    `discount_currency` varchar(256) DEFAULT NULL,
                                    `rate` varchar(256) DEFAULT NULL,
                                    `amount` varchar(256) DEFAULT NULL,
                                    `any_other_information_which_has_a_bearing_on_value` varchar(256) DEFAULT NULL,
                                    `are_the_buyer_and_seller_related` varchar(256) DEFAULT NULL,
                                    `if_the_buyer_seller_has_the_relationship_examined_earlier_svb` varchar(256) DEFAULT NULL,
                                    `svb_reference_number` varchar(256) DEFAULT NULL,
                                    `svb_date` varchar(256) DEFAULT NULL,
                                    `indication_for_provisional_final` varchar(256) DEFAULT NULL,
                                    `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
               /*****************************************Create Table courier_bill_items_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_items_details` (
                                     `courier_bill_of_entry_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `items_detail_id` int(20) DEFAULT NULL,
                                      `case_for_reimport` varchar(256) DEFAULT NULL,
                                      `import_against_license` varchar(256) DEFAULT NULL,
                                      `serial_number_in_invoice` varchar(256) DEFAULT NULL,
                                      `item_description` varchar(256) DEFAULT NULL,
                                      `general_description` varchar(256) DEFAULT NULL,
                                      `currency_for_unit_price` varchar(256) DEFAULT NULL,
                                      `unit_price` int(20) DEFAULT NULL,
                                      `unit_of_measure` varchar(256) DEFAULT NULL,
                                      `quantity` int(20) DEFAULT NULL,
                                      `rate_of_exchange` decimal(12,2) DEFAULT NULL,
                                      `accessories_if_any` varchar(256) DEFAULT NULL,
                                      `name_of_manufacturer` varchar(256) DEFAULT NULL,
                                      `brand` varchar(256) DEFAULT NULL,
                                      `model` varchar(256) DEFAULT NULL,
                                      `grade` varchar(256) DEFAULT NULL,
                                      `specification` varchar(256) DEFAULT NULL,
                                      `end_use_of_item` varchar(256) DEFAULT NULL,
                                      `items_details_country_of_origin` varchar(256) DEFAULT NULL,
                                      `bill_of_entry_number` varchar(256) DEFAULT NULL,
                                      `details_in_case_of_previous_imports_date` varchar(256) DEFAULT NULL,
                                      `details_in_case_previous_imports_currency` varchar(256) DEFAULT NULL,
                                      `unit_value` varchar(256) DEFAULT NULL,
                                      `customs_house` varchar(256) DEFAULT NULL,
                                      `ritc` int(20) DEFAULT NULL,
                                      `ctsh` int(20) DEFAULT NULL,
                                      `cetsh` int(20) DEFAULT NULL,
                                      `currency_for_rsp` varchar(256) DEFAULT NULL,
                                      `retail_sales_price_per_unit` varchar(256) DEFAULT NULL,
                                      `exim_scheme_code_if_any` varchar(256) DEFAULT NULL,
                                      `para_noyear_of_exim_policy` varchar(256) DEFAULT NULL,
                                      `items_details_are_the_buyer_and_seller_related` varchar(256) DEFAULT NULL,
                                      `if_the_buyer_and_seller_relation_examined_earlier_by_svb` varchar(256) DEFAULT NULL,
                                      `items_details_svb_reference_number` varchar(256) DEFAULT NULL,
                                      `items_details_svb_date` varchar(256) DEFAULT NULL,
                                      `items_details_indication_for_provisional_final` varchar(256) DEFAULT NULL,
                                      `shipping_bill_number` varchar(256) DEFAULT NULL,
                                      `shipping_bill_date` varchar(256) DEFAULT NULL,
                                      `port_of_export` varchar(256) DEFAULT NULL,
                                      `invoice_number_of_shipping_bill` varchar(256) DEFAULT NULL,
                                      `item_serial_number_in_shipping_bill` varchar(256) DEFAULT NULL,
                                      `freight` varchar(256) DEFAULT NULL,
                                      `insurance` varchar(256) DEFAULT NULL,
                                      `total_repair_cost_including_cost_of_materials` varchar(256) DEFAULT NULL,
                                      `additional_duty_exemption_requested` varchar(256) DEFAULT NULL,
                                      `items_details_notification_number` varchar(256) DEFAULT NULL,
                                      `serial_number_in_notification` varchar(256) DEFAULT NULL,
                                      `license_registration_number` varchar(256) DEFAULT NULL,
                                      `license_registration_date` varchar(256) DEFAULT NULL,
                                      `debit_value_rs` varchar(256) DEFAULT NULL,
                                      `unit_of_measure_for_quantity_to_be_debited` varchar(256) DEFAULT NULL,
                                      `debit_quantity` varchar(256) DEFAULT NULL,
                                      `item_serial_number_in_license` varchar(256) DEFAULT NULL,
                                      `assessable_value` decimal(12,2) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
        
        /*****************************************Create Table courier_bill_manifest_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_manifest_details` (
                                      `courier_bill_of_entry_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                      `manifest_details_id` int(20) DEFAULT NULL,
                                      `import_general_manifest_igm_number` varchar(256) DEFAULT NULL,
                                      `date_of_entry_inward` varchar(256) DEFAULT NULL,
                                      `master_airway_bill_mawb_number` bigint(20) DEFAULT NULL,
                                      `date_of_mawb` varchar(256) DEFAULT NULL,
                                      `house_airway_bill_hawb_number` bigint(20) DEFAULT NULL,
                                      `date_of_hawb` varchar(256) DEFAULT NULL,
                                      `marks_and_numbers` int(20) DEFAULT NULL,
                                      `number_of_packages` int(20) DEFAULT NULL,
                                      `type_of_packages` varchar(256) DEFAULT NULL,
                                      `interest_amount` int(20) DEFAULT NULL,
                                      `unit_of_measure_for_gross_weight` varchar(256) DEFAULT NULL,
                                      `gross_weight` decimal(12,1) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                      `a` varchar(256) DEFAULT NULL,
                                      `b` varchar(256) DEFAULT NULL,
                                      `c` varchar(256) DEFAULT NULL,
                                      `d` varchar(256) DEFAULT NULL,
                                      `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
        
        /*****************************************Create Table courier_bill_notification_used_for_items***************************************************************/
               $db2->query("CREATE TABLE `courier_bill_notification_used_for_items` (
                          `items_detail_id` int(20) DEFAULT NULL,
                          `item_notification_id` int(20) NOT NULL,
                          `notification_item_srno` int(20) DEFAULT NULL,
                          `notification_number` varchar(256) DEFAULT NULL,
                          `serial_number_of_notification` varchar(256) DEFAULT NULL,
                          `created_at` varchar(256) DEFAULT NULL,
                          `a` varchar(256) DEFAULT NULL,
                          `b` varchar(256) DEFAULT NULL,
                          `c` varchar(256) DEFAULT NULL,
                          `d` varchar(256) DEFAULT NULL,
                          `e` varchar(256) DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
          
        /*****************************************Create Table courier_bill_payment_details***************************************************************/
               $db2->query("CREATE TABLE `courier_bill_payment_details` (
                              `courier_bill_of_entry_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `payment_details_id` int(20) DEFAULT NULL,
                              `payment_details_srno` int(20) DEFAULT NULL,
                              `tr6_challan_number` bigint(19) DEFAULT NULL,
                              `total_amount` int(20) DEFAULT NULL,
                              `challan_date` varchar(256) DEFAULT NULL,
                              `created_at` varchar(256) DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
            /*****************************************Create Table courier_bill_procurment_details***************************************************************/
               $db2->query("CREATE TABLE `courier_bill_procurment_details` (
                                  `courier_bill_of_entry_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                  `procurment_details_id` int(20) DEFAULT NULL,
                                  `procurement_under_3696_cus` varchar(256) DEFAULT NULL,
                                  `procurement_certificate_number` varchar(256) DEFAULT NULL,
                                  `date_of_issuance_of_certificate` varchar(256) DEFAULT NULL,
                                  `location_code_of_the_cent_ral_excise_office_issuing_the_certifi` varchar(256) DEFAULT NULL,
                                  `commissione_rate` varchar(256) DEFAULT NULL,
                                  `division` varchar(256) DEFAULT NULL,
                                  `range` varchar(256) DEFAULT NULL,
                                  `import_under_multiple_in_voices` varchar(256) DEFAULT NULL,
                                  `created_at` varchar(256) DEFAULT NULL,
                                  `a` varchar(256) DEFAULT NULL,
                                  `b` varchar(256) DEFAULT NULL,
                                  `c` varchar(256) DEFAULT NULL,
                                  `d` varchar(256) DEFAULT NULL,
                                  `e` varchar(256) DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/  
   
       
               /*****************************************Create Table courier_bill_summary***************************************************************/
                 $db2->query("CREATE TABLE `courier_bill_summary` (
                      `courier_bill_of_entry_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                      `cb_file_status_id` int(20) DEFAULT NULL,
                      `current_status_of_the_cbe` varchar(29) DEFAULT NULL,
                      `cbexiv_number` varchar(256) DEFAULT NULL,
                      `courier_registration_number` varchar(256) DEFAULT NULL,
                      `name_of_the_authorized_courier` varchar(256) DEFAULT NULL,
                      `address_of_authorized_courier` varchar(256) DEFAULT NULL,
                      `particulars_customs_house_agent_name` varchar(256) DEFAULT NULL,
                      `particulars_customs_house_agent_licence_no` varchar(256) DEFAULT NULL,
                      `particulars_customs_house_agent_address` varchar(256) DEFAULT NULL,
                      `import_export_code` varchar(256) DEFAULT NULL,
                      `import_export_branch_code` int(20) DEFAULT NULL,
                      `particulars_of_the_importer_name` varchar(256) DEFAULT NULL,
                      `particulars_of_the_importer_address` varchar(256) DEFAULT NULL,
                      `category_of_importer` varchar(256) DEFAULT NULL,
                      `type_of_importer` varchar(256) DEFAULT NULL,
                      `in_case_of_other_importer` varchar(256) DEFAULT NULL,
                      `authorised_dealer_code_of_bank` int(20) DEFAULT NULL,
                      `class_code` varchar(256) DEFAULT NULL,
                      `cb_no` varchar(256) DEFAULT NULL,
                      `cb_date` varchar(256) DEFAULT NULL,
                      `category_of_boe` varchar(256) DEFAULT NULL,
                      `type_of_boe` varchar(256) DEFAULT NULL,
                      `kyc_document` varchar(256) DEFAULT NULL,
                      `kyc_id` varchar(256) DEFAULT NULL,
                      `state_code` int(20) DEFAULT NULL,
                      `high_sea_sale` varchar(256) DEFAULT NULL,
                      `ie_code_of_hss` varchar(256) DEFAULT NULL,
                      `ie_branch_code_of_hss` varchar(256) DEFAULT NULL,
                      `particulars_high_sea_seller_name` varchar(256) DEFAULT NULL,
                      `particulars_high_sea_seller_address` varchar(256) DEFAULT NULL,
                      `use_of_the_first_proviso_under_section_461customs_act1962` varchar(256) DEFAULT NULL,
                      `request_for_first_check` varchar(256) DEFAULT NULL,
                      `request_for_urgent_clear_ance_against_temporary_documentation` varchar(256) DEFAULT NULL,
                      `request_for_extension_of_time_limit_as_per_section_48customs_ac` varchar(256) DEFAULT NULL,
                      `reason_in_case_extension_of_time_limit_is_requested` varchar(256) DEFAULT NULL,
                      `country_of_origin` varchar(256) DEFAULT NULL,
                      `country_of_consignment` varchar(256) DEFAULT NULL,
                      `name_of_gateway_port` varchar(256) DEFAULT NULL,
                      `gateway_igm_number` varchar(256) DEFAULT NULL,
                      `date_of_entry_inwards_of_gateway_port` varchar(256) DEFAULT NULL,
                      `case_of_crn` varchar(256) DEFAULT NULL,
                      `number_of_invoices` int(20) DEFAULT NULL,
                      `total_freight` decimal(12,1) DEFAULT NULL,
                      `total_insurance` decimal(12,2) DEFAULT NULL,
                      `created_at` varchar(256) DEFAULT NULL,
                      `a` varchar(256) DEFAULT NULL,
                      `b` varchar(256) DEFAULT NULL,
                      `c` varchar(256) DEFAULT NULL,
                      `d` varchar(256) DEFAULT NULL,
                      `e` varchar(256) DEFAULT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
        
                 /*****************************************Create Table drawback_details***************************************************************/
               $db2->query("CREATE TABLE `drawback_details` (
                         `drawback_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `item_id` int(20) DEFAULT NULL,
                        `inv_sno` int(20) DEFAULT NULL,
                        `item_sno` int(20) DEFAULT NULL,
                        `dbk_sno` text,
                        `qty_wt` text,
                        `value` text,
                        `dbk_amt` text,
                        `stalev` text,
                        `cenlev` text,
                        `rosctl_amt` text,
                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `rate` text,
                        `rebate` varchar(256) NOT NULL,
                        `amount` varchar(256) NOT NULL,
                        `dbk_rosl` varchar(256) NOT NULL,
                        `a` varchar(256) DEFAULT NULL,
                        `b` varchar(256) DEFAULT NULL,
                        `c` varchar(256) DEFAULT NULL,
                        `d` varchar(256) DEFAULT NULL,
                        `e` varchar(256) DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
        
            /*****************************************Create Table duties_and_additional_details***************************************************************/
               $db2->query("CREATE TABLE `duties_and_additional_details` (
                                     `boe_id` int(20) DEFAULT NULL,
                                      `invoice_id` int(20) DEFAULT NULL,
                                      `duties_id` int(20) DEFAULT NULL,
                                      `s_no` int(20) DEFAULT NULL,
                                      `cth` varchar(256) DEFAULT NULL,
                                      `description` varchar(120) DEFAULT NULL,
                                      `unit_price` varchar(256) DEFAULT NULL,
                                      `quantity` decimal(12,6) DEFAULT NULL,
                                      `uqc` varchar(256) DEFAULT NULL,
                                      `amount` decimal(10,2) DEFAULT NULL,
                                      `invsno` int(20) DEFAULT NULL,
                                      `itemsn` int(20) DEFAULT NULL,
                                      `cth_item_detail` varchar(256) DEFAULT NULL,
                                      `ceth` varchar(256) DEFAULT NULL,
                                      `item_description` varchar(120) DEFAULT NULL,
                                      `fs` varchar(256) DEFAULT NULL,
                                      `pq` varchar(256) DEFAULT NULL,
                                      `dc` varchar(256) DEFAULT NULL,
                                      `wc` varchar(256) DEFAULT NULL,
                                      `aq` varchar(256) DEFAULT NULL,
                                      `upi` decimal(14,6) DEFAULT NULL,
                                      `coo` varchar(256) DEFAULT NULL,
                                      `c_qty` decimal(11,5) DEFAULT NULL,
                                      `c_uqc` varchar(256) DEFAULT NULL,
                                      `s_qty` decimal(13,6) DEFAULT NULL,
                                      `s_uqc` varchar(256) DEFAULT NULL,
                                      `sch` varchar(256) DEFAULT NULL,
                                      `stdn_pr` varchar(256) DEFAULT NULL,
                                      `rsp` varchar(256) DEFAULT NULL,
                                      `reimp` varchar(256) DEFAULT NULL,
                                      `prov` varchar(256) DEFAULT NULL,
                                      `end_use` varchar(256) DEFAULT NULL,
                                      `prodn` varchar(256) DEFAULT NULL,
                                      `cntrl` varchar(256) DEFAULT NULL,
                                      `qualfr` varchar(256) DEFAULT NULL,
                                      `contnt` varchar(256) DEFAULT NULL,
                                      `stmnt` varchar(256) DEFAULT NULL,
                                      `sup_docs` varchar(256) DEFAULT NULL,
                                      `assess_value` decimal(11,2) DEFAULT NULL,
                                      `total_duty` varchar(256) DEFAULT NULL,
                                      `bcd_notn_no` varchar(256) DEFAULT NULL,
                                      `bcd_notn_sno` varchar(256) DEFAULT NULL,
                                      `bcd_rate` decimal(12,2) DEFAULT NULL,
                                      `bcd_amount` decimal(12,1) DEFAULT NULL,
                                      `bcd_duty_fg` varchar(256) DEFAULT NULL,
                                      `acd_notn_no` varchar(256) DEFAULT NULL,
                                      `acd_notn_sno` varchar(256) DEFAULT NULL,
                                      `acd_rate` varchar(256) DEFAULT NULL,
                                      `acd_amount` varchar(256) DEFAULT NULL,
                                      `acd_duty_fg` varchar(256) DEFAULT NULL,
                                      `sws_notn_no` varchar(256) DEFAULT NULL,
                                      `sws_notn_sno` varchar(256) DEFAULT NULL,
                                      `sws_rate` decimal(12,6) DEFAULT NULL,
                                      `sws_amount` decimal(12,1) DEFAULT NULL,
                                      `sws_duty_fg` varchar(256) DEFAULT NULL,
                                      `sad_notn_no` varchar(256) DEFAULT NULL,
                                      `sad_notn_sno` varchar(256) DEFAULT NULL,
                                      `sad_rate` varchar(256) DEFAULT NULL,
                                      `sad_amount` varchar(256) DEFAULT NULL,
                                      `sad_duty_fg` varchar(256) DEFAULT NULL,
                                      `igst_notn_no` varchar(256) DEFAULT NULL,
                                      `igst_notn_sno` varchar(256) DEFAULT NULL,
                                      `igst_rate` int(20) DEFAULT NULL,
                                      `igst_amount` decimal(12,2) DEFAULT NULL,
                                      `igst_duty_fg` decimal(12,1) DEFAULT NULL,
                                      `g_cess_notn_no` varchar(256) DEFAULT NULL,
                                      `g_cess_notn_sno` varchar(256) DEFAULT NULL,
                                      `g_cess_rate` int(20) DEFAULT NULL,
                                      `g_cess_amount` int(20) DEFAULT NULL,
                                      `g_cess_duty_fg` int(20) DEFAULT NULL,
                                      `add_notn_no` varchar(256) DEFAULT NULL,
                                      `add_notn_sno` varchar(256) DEFAULT NULL,
                                      `add_rate` varchar(256) DEFAULT NULL,
                                      `add_amount` varchar(256) DEFAULT NULL,
                                      `add_duty_fg` varchar(256) DEFAULT NULL,
                                      `cvd_notn_no` varchar(256) DEFAULT NULL,
                                      `cvd_notn_sno` varchar(256) DEFAULT NULL,
                                      `cvd_rate` int(20) DEFAULT NULL,
                                      `cvd_amount` varchar(256) DEFAULT NULL,
                                      `cvd_duty_fg` varchar(256) DEFAULT NULL,
                                      `sg_notn_no` varchar(256) DEFAULT NULL,
                                      `sg_notn_sno` varchar(256) DEFAULT NULL,
                                      `sg_rate` varchar(256) DEFAULT NULL,
                                      `sg_amount` varchar(256) DEFAULT NULL,
                                      `sg_duty_fg` varchar(256) DEFAULT NULL,
                                      `t_value_notn_no` varchar(256) DEFAULT NULL,
                                      `t_value_notn_sno` varchar(256) DEFAULT NULL,
                                      `t_value_rate` varchar(256) DEFAULT NULL,
                                      `t_value_amount` varchar(256) DEFAULT NULL,
                                      `t_value_duty_fg` varchar(256) DEFAULT NULL,
                                      `sp_excd_notn_no` varchar(256) DEFAULT NULL,
                                      `sp_excd_notn_sno` varchar(256) DEFAULT NULL,
                                      `sp_excd_rate` varchar(256) DEFAULT NULL,
                                      `sp_excd_amount` varchar(256) DEFAULT NULL,
                                      `sp_excd_duty_fg` varchar(256) DEFAULT NULL,
                                      `chcess_notn_no` varchar(256) DEFAULT NULL,
                                      `chcess_notn_sno` varchar(256) DEFAULT NULL,
                                      `chcess_rate` varchar(256) DEFAULT NULL,
                                      `chcess_amount` varchar(256) DEFAULT NULL,
                                      `chcess_duty_fg` varchar(256) DEFAULT NULL,
                                      `tta_notn_no` varchar(256) DEFAULT NULL,
                                      `tta_notn_sno` varchar(256) DEFAULT NULL,
                                      `tta_rate` varchar(256) DEFAULT NULL,
                                      `tta_amount` varchar(256) DEFAULT NULL,
                                      `tta_duty_fg` varchar(256) DEFAULT NULL,
                                      `cess_notn_no` varchar(256) DEFAULT NULL,
                                      `cess_notn_sno` varchar(256) DEFAULT NULL,
                                      `cess_rate` varchar(256) DEFAULT NULL,
                                      `cess_amount` varchar(256) DEFAULT NULL,
                                      `cess_duty_fg` varchar(256) DEFAULT NULL,
                                      `caidc_cvd_edc_notn_no` varchar(256) DEFAULT NULL,
                                      `caidc_cvd_edc_notn_sno` int(20) DEFAULT NULL,
                                      `caidc_cvd_edc_rate` int(20) DEFAULT NULL,
                                      `caidc_cvd_edc_amount` decimal(10,2) DEFAULT NULL,
                                      `caidc_cvd_edc_duty_fg` decimal(11,2) DEFAULT NULL,
                                      `eaidc_cvd_hec_notn_no` varchar(256) DEFAULT NULL,
                                      `eaidc_cvd_hec_notn_sno` varchar(256) DEFAULT NULL,
                                      `eaidc_cvd_hec_rate` varchar(256) DEFAULT NULL,
                                      `eaidc_cvd_hec_amount` varchar(256) DEFAULT NULL,
                                      `eaidc_cvd_hec_duty_fg` varchar(256) DEFAULT NULL,
                                      `cus_edc_notn_no` varchar(256) DEFAULT NULL,
                                      `cus_edc_notn_sno` varchar(256) DEFAULT NULL,
                                      `cus_edc_rate` int(20) DEFAULT NULL,
                                      `cus_edc_amount` varchar(256) DEFAULT NULL,
                                      `cus_edc_duty_fg` varchar(256) DEFAULT NULL,
                                      `cus_hec_notn_no` varchar(256) DEFAULT NULL,
                                      `cus_hec_notn_sno` varchar(256) DEFAULT NULL,
                                      `cus_hec_rate` int(20) DEFAULT NULL,
                                      `cus_hec_amount` varchar(256) DEFAULT NULL,
                                      `cus_hec_duty_fg` varchar(256) DEFAULT NULL,
                                      `ncd_notn_no` varchar(256) DEFAULT NULL,
                                      `ncd_notn_sno` varchar(256) DEFAULT NULL,
                                      `ncd_rate` varchar(256) DEFAULT NULL,
                                      `ncd_amount` varchar(256) DEFAULT NULL,
                                      `ncd_duty_fg` varchar(256) DEFAULT NULL,
                                      `aggr_notn_no` varchar(256) DEFAULT NULL,
                                      `aggr_notn_sno` varchar(256) DEFAULT NULL,
                                      `aggr_rate` varchar(256) DEFAULT NULL,
                                      `aggr_amount` varchar(256) DEFAULT NULL,
                                      `aggr_duty_fg` varchar(256) DEFAULT NULL,
                                      `invsno_add_details` varchar(256) DEFAULT NULL,
                                      `itmsno_add_details` varchar(256) DEFAULT NULL,
                                      `refno` varchar(256) DEFAULT NULL,
                                      `refdt` varchar(256) DEFAULT NULL,
                                      `prtcd_svb_d` varchar(256) DEFAULT NULL,
                                      `lab` varchar(256) DEFAULT NULL,
                                      `pf` varchar(256) DEFAULT NULL,
                                      `load_date` varchar(256) DEFAULT NULL,
                                      `pf_` varchar(256) DEFAULT NULL,
                                      `beno` varchar(256) DEFAULT NULL,
                                      `bedate` varchar(256) DEFAULT NULL,
                                      `prtcd` varchar(256) DEFAULT NULL,
                                      `unitprice` varchar(256) DEFAULT NULL,
                                      `currency_code` varchar(256) DEFAULT NULL,
                                      `frt` varchar(256) DEFAULT NULL,
                                      `ins` varchar(256) DEFAULT NULL,
                                      `duty` varchar(256) DEFAULT NULL,
                                      `sb_no` varchar(256) DEFAULT NULL,
                                      `sb_dt` varchar(256) DEFAULT NULL,
                                      `portcd` varchar(256) DEFAULT NULL,
                                      `sinv` varchar(256) DEFAULT NULL,
                                      `sitemn` varchar(256) DEFAULT NULL,
                                      `type` varchar(256) DEFAULT NULL,
                                      `manufact_cd` varchar(256) DEFAULT NULL,
                                      `source_cy` varchar(256) DEFAULT NULL,
                                      `trans_cy` varchar(256) DEFAULT NULL,
                                      `address` varchar(256) DEFAULT NULL,
                                      `accessory_item_details` varchar(208) DEFAULT NULL,
                                      `notno` varchar(256) DEFAULT NULL,
                                      `slno` varchar(256) DEFAULT NULL,
                                      `created_at` varchar(256) DEFAULT NULL,
                                    `a` varchar(256) DEFAULT NULL,
                                    `b` varchar(256) DEFAULT NULL,
                                    `c` varchar(256) DEFAULT NULL,
                                    `d` varchar(256) DEFAULT NULL,
                                    `e` varchar(256) DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
  
            /*****************************************Create Table equipment_details***************************************************************/
               $db2->query("CREATE TABLE `equipment_details` (
                          `equip_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `sbs_id` int(20) DEFAULT NULL,
                          `container` text,
                          `seal` text,
                          `date` text,
                          `s_no` text,
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `a` varchar(256) NOT NULL,
                          `b` varchar(256) NOT NULL,
                          `c` varchar(256) NOT NULL,
                          `d` varchar(256) NOT NULL,
                          `e` varchar(256) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
        
        
        /*****************************************Create Table invoice_and_valuation_details***************************************************************/
               $db2->query("CREATE TABLE `invoice_and_valuation_details` (
                                  `invoice_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                  `boe_id` int(20) DEFAULT NULL,
                                  `s_no` int(20) DEFAULT NULL,
                                  `invoice_no` varchar(256) DEFAULT NULL,
                                  `purchase_order_no` varchar(256) DEFAULT NULL,
                                  `lc_no` varchar(256) DEFAULT NULL,
                                  `contract_no` varchar(256) DEFAULT NULL,
                                  `buyer_s_name_and_address` text ,
                                  `seller_s_name_and_address` text ,
                                  `supplier_name_and_address` text ,
                                  `third_party_name_and_address` text ,
                                  `aeo` varchar(256) DEFAULT NULL,
                                  `ad_code` varchar(256) DEFAULT NULL,
                                  `inv_value` decimal(12,0) DEFAULT NULL,
                                  `freight` varchar(256) DEFAULT NULL,
                                  `insurance` varchar(256) DEFAULT NULL,
                                  `hss` varchar(256) DEFAULT NULL,
                                  `loading` varchar(256) DEFAULT NULL,
                                  `commn` varchar(256) DEFAULT NULL,
                                  `pay_terms` varchar(256) DEFAULT NULL,
                                  `valuation_method` varchar(256) DEFAULT NULL,
                                  `reltd` varchar(256) DEFAULT NULL,
                                  `svb_ch` varchar(256) DEFAULT NULL,
                                  `svb_no` varchar(256) DEFAULT NULL,
                                  `date` date DEFAULT NULL,
                                  `loa` int(20) DEFAULT NULL,
                                  `cur` varchar(256) DEFAULT NULL,
                                  `term` varchar(256) DEFAULT NULL,
                                  `c_and_b` varchar(256) DEFAULT NULL,
                                  `coc` varchar(256) DEFAULT NULL,
                                  `cop` varchar(256) DEFAULT NULL,
                                  `hnd_chg` varchar(256) DEFAULT NULL,
                                  `g_and_s` varchar(256) DEFAULT NULL,
                                  `doc_ch` varchar(256) DEFAULT NULL,
                                  `coo` varchar(256) DEFAULT NULL,
                                  `r_and_lf` varchar(256) DEFAULT NULL,
                                  `oth_cost` varchar(256) DEFAULT NULL,
                                  `ld_uld` varchar(256) DEFAULT NULL,
                                  `ws` varchar(256) DEFAULT NULL,
                                  `otc` varchar(256) DEFAULT NULL,
                                  `misc_charge` decimal(12,0) DEFAULT NULL,
                                  `ass_value` decimal(12,0) DEFAULT NULL,
                                  `invoice_date` date DEFAULT NULL,
                                  `purchase_order_date` date DEFAULT NULL,
                                  `lc_date` date DEFAULT NULL,
                                  `contract_date` date DEFAULT NULL,
                                  `freight_cur` varchar(256) DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `a` varchar(256) DEFAULT NULL,
                                  `b` varchar(256) DEFAULT NULL,
                                  `c` varchar(256) DEFAULT NULL,
                                  `d` varchar(256) DEFAULT NULL,
                                  `e` varchar(256) DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

    /*************************************************************************************************************************************/
            
    /*****************************************Create Table equipment_details***************************************************************/
               $db2->query("CREATE TABLE `invoice_summary` (
                              `invoice_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `sbs_id` int(20) DEFAULT NULL,
                              `s_no_inv` text,
                              `inv_no` text,
                              `inv_date` date DEFAULT NULL,
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
                              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /******************************************************************************************************************************/
        
            
             /*****************************************Create Table equipment_details***************************************************************/
               $db2->query("CREATE TABLE `item_details` (
                              `item_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `invoice_id` int(20) DEFAULT NULL,
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
                              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
       
             /*****************************************Create Table item_manufacturer_details***************************************************************/
               $db2->query("CREATE TABLE `item_manufacturer_details` (
                                `item_manufact_id` int(20) DEFAULT NULL,
                              `item_id` varchar(256) DEFAULT NULL,
                              `inv_sno` varchar(256) DEFAULT NULL,
                              `item_sno` varchar(256) DEFAULT NULL,
                              `manufact_cd` varchar(256) DEFAULT NULL,
                              `source_state` varchar(256) DEFAULT NULL,
                              `trans_cy` varchar(256) DEFAULT NULL,
                              `address` varchar(256) DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
          /*****************************************Create Table item_manufacturer_details***************************************************************/
               $db2->query("CREATE TABLE `jobbing_details` (
                              `jobbing_detail_id` int(20) NOT NULL,
                              `sbs_id` int(20) DEFAULT NULL,
                              `be_no` text,
                              `be_date` text,
                              `port_code_j` text,
                              `descn_of_imported_goods` text,
                              `qty_imp` text,
                              `qty_used` text,
                              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            
               /*****************************************Create Table report_setting_export***************************************************************/
               $db2->query("CREATE TABLE `report_setting_export` (
                              `id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `export_importer_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `type` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `BOE_Summary` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `BOE_Summary_index` varchar(256) DEFAULT NULL,
                              `Bill_Of_Entry` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `Bill_Of_Entry_index` varchar(256) DEFAULT NULL,
                              `Bond_Details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `Bond_Details_index` varchar(256) DEFAULT NULL,
                              `Container_Details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `Container_Details_index` varchar(256) DEFAULT NULL,
                              `Manifest_Details` text,
                              `Manifest_Details_index` varchar(256) DEFAULT NULL,
                              `Payment_Details` text,
                              `Payment_Details_index` varchar(256) DEFAULT NULL,
                              `License_Details` text,
                              `License_Details_index` varchar(256) DEFAULT NULL,
                              `SHB_Summary` text,
                              `Shipping_Bill_Summary` text,
                              `Equipment_Details` text,
                              `Challan_Details` text,
                              `Jobbing_Details` text,
                              `DFIA_Licence_Details` text,
                              `Drawback_Details` text,
                              `Third_Party_Details` text,
                              `Item_Manufacturer` text,
                              `Rodtep_Details` text,
                              `frequency` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `time` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `format` enum('excel','csv','both') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `report_name` text NOT NULL,
                              `from_date` text NOT NULL,
                              `to_date` text,
                              `email_id` text NOT NULL,
                              `report_path` text NOT NULL,
                              `CREATED` date NOT NULL,
                              `IS_DELETED` int(20) NOT NULL
                            ) ENGINE=MyISAM DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
                /*****************************************Create Table rodtep_details***************************************************************/
               $db2->query("CREATE TABLE `rodtep_details` (
                              `rodtep_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `item_id` int(20) DEFAULT NULL,
                              `inv_sno` int(20) DEFAULT NULL,
                              `item_sno` int(20) DEFAULT NULL,
                              `quantity` varchar(256) DEFAULT NULL,
                              `uqc` varchar(256) DEFAULT NULL,
                              `no_of_units` int(20) DEFAULT NULL,
                              `value` int(20) DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/

           /*****************************************Create Table report_setting_export***************************************************************/
               $db2->query("CREATE TABLE `sb_file_status` (
                              `sb_file_status_id` int(20) DEFAULT NULL,
                              `pdf_filepath` varchar(256) DEFAULT NULL,
                              `pdf_filename` varchar(256) DEFAULT NULL,
                              `user_iec_no` varchar(256) DEFAULT NULL,
                              `lucrative_users_id` int(20) DEFAULT NULL,
                              `file_iec_no` varchar(256) DEFAULT NULL,
                              `sb_no` varchar(256) DEFAULT NULL,
                              `sb_date` varchar(256) DEFAULT NULL,
                              `stage` varchar(256) DEFAULT NULL,
                              `status` varchar(256) DEFAULT NULL,
                              `remarks` varchar(256) DEFAULT NULL,
                              `created_at` varchar(256) DEFAULT NULL,
                              `br` varchar(256) DEFAULT NULL,
                              `is_processed` varchar(256) DEFAULT NULL,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/

            /*****************************************Create Table ship_bill_summary******************************************/
               $db2->query("CREATE TABLE `ship_bill_summary` (
                             `sbs_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `sb_file_status_id` int(20) DEFAULT NULL,
                              `invoice_title` text,
                              `port_code` text,
                              `sb_no` int(20) DEFAULT NULL,
                              `sb_date` date DEFAULT NULL,
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
                              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `a` varchar(256) DEFAULT NULL,
                              `b` varchar(256) DEFAULT NULL,
                              `c` varchar(256) DEFAULT NULL,
                              `d` varchar(256) DEFAULT NULL,
                              `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
             /*****************************************Create Table ship_bill_summary***************************************************************/
               $db2->query("CREATE TABLE `tbl_graph_reports_settings` (
                              `graph_setting_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                `user_id` int(20) NOT NULL,
                                `graph_name` text NOT NULL,
                                `type` int(20) NOT NULL,
                                `sheet_id` varchar(256) NOT NULL,
                                `fieldx` text NOT NULL,
                                `fieldy` text NOT NULL,
                                `graph_type` varchar(256) NOT NULL,
                                `graph_looks` varchar(256) NOT NULL,
                                `from_date` date DEFAULT NULL,
                                `to_date` date DEFAULT NULL,
                                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `a` varchar(256) DEFAULT NULL,
                                `b` varchar(256) DEFAULT NULL,
                                `c` varchar(256) DEFAULT NULL,
                                `d` varchar(256) DEFAULT NULL,
                                `e` varchar(256) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            /*****************************************Create Table ship_bill_summary***************************************************/
               $db2->query("CREATE TABLE `third_party_details` (
                                  `third_party_id` int(1) DEFAULT NULL,
                                  `item_id` varchar(10) DEFAULT NULL,
                                  `inv_sno` varchar(10) DEFAULT NULL,
                                  `item_sno` varchar(10) DEFAULT NULL,
                                  `iec_tpd` varchar(10) DEFAULT NULL,
                                  `exporter_name` varchar(10) DEFAULT NULL,
                                  `address` varchar(10) DEFAULT NULL,
                                  `gstn_id_type` varchar(3) DEFAULT NULL,
                                  `a` varchar(256) DEFAULT NULL,
                                  `b` varchar(256) DEFAULT NULL,
                                  `c` varchar(256) DEFAULT NULL,
                                  `d` varchar(256) DEFAULT NULL,
                                  `e` varchar(256) DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            /*****************************************Create Table ship_bill_summary***************************************************/
               $db2->query("CREATE TABLE `tbl_graph_set_user` (
                        `graphs_settings_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `graphs_id` varchar(256) NOT NULL,
                        `user_id` int(20) NOT NULL,
                        `created_at` date NOT NULL,
                        `a` varchar(256) DEFAULT NULL,
                        `b` varchar(256) DEFAULT NULL,
                        `c` varchar(256) DEFAULT NULL,
                        `d` varchar(256) DEFAULT NULL,
                        `e` varchar(256) DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            
              /*****************************************Create Table tbl_import_export_sheets_name***************************************************/
               $db2->query("CREATE TABLE `tbl_import_export_sheets_name` (
  `ie_sheet_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` int(20) NOT NULL,
  `sheet_name` text NOT NULL,
`a` varchar(256) DEFAULT NULL,
`b` varchar(256) DEFAULT NULL,
`c` varchar(256) DEFAULT NULL,
`d` varchar(256) DEFAULT NULL,
`e` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
              /*****************************************Create Table tbl_ie_sheets_fields***************************************************/
               $db2->query("CREATE TABLE `tbl_ie_sheets_fields` (
  `field_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ie_sheet_id` int(20) NOT NULL,
  `field_name` text NOT NULL,
`a` varchar(256) DEFAULT NULL,
`b` varchar(256) DEFAULT NULL,
`c` varchar(256) DEFAULT NULL,
`d` varchar(256) DEFAULT NULL,
`e` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            }
        }
/*
        if ($result) {
            $this->session->set_flashdata(
                "success",
                "Record update Successfully!"
            );
        } else {
            $this->session->set_flashdata("error", "Record not updated!");
        }*/

       // redirect("/admin/iec_signup");
       
    }

public function register_iec_user_old(){
        $post = $this->input->post();
        $alphabet =
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $data = [
            "fullname" => $post["first_name"] . " " . $post["last_name"],
            "email" => $post["iec_email"],
            "iec_no" => $post["iec_no"],
            "mobile" => $post["mobile_no"],
            "password" => implode($pass),
            "role" => "admin",
            "created_at" => date("Y-m-d h:i:s"),
        ];
        //$result = $this->Common_model->insert_iec_user_entry($data);
        $result = 1;

        if ($result) {
            $db_name = $_SESSION["database_prefix"] . $result;

            if ($this->dbforge->create_database($db_name)) {
                $this->db->query("use " . $db_name . "");

                $this->db->query("CREATE TABLE `boe_delete_logs` (
  `boe_delete_logs_id` integer NOT NULL ,
  `filename` varchar(255),
  `be_no` varchar(255),
  `be_date` datetime,
  `iec_no` varchar(255),
  `br` varchar(255),
  `fullname` varchar(255),
  `email` varchar(255),
  `mobile` varchar(255),
  `deleted_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `bill_of_entry_summary` (
  `boe_id` integer NOT NULL ,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `invoice_and_valuation_details` (
  `invoice_id` integer NOT NULL ,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `duties_and_additional_details` (
  `duties_id` integer NOT NULL ,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `bill_manifest_details` (
  `manifest_details_id` integer NOT NULL ,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `bill_bond_details` (
  `bond_details_id` integer PRIMARY KEY AUTO_INCREMENT,
  `boe_id` integer NOT NULL,
  `bond_no` varchar(255),
  `port` varchar(255),
  `bond_cd` varchar(255),
  `debt_amt` numeric,
  `bg_amt` numeric,
  `created_at` datetime) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `bill_payment_details` (
  `payment_details_id` integer PRIMARY KEY AUTO_INCREMENT,
  `boe_id` integer NOT NULL,
  `sr_no` integer,
  `challan_no` varchar(255),
  `paid_on` date,
  `amount` numeric,
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `bill_container_details` (
  `container_details_pk` integer PRIMARY KEY AUTO_INCREMENT,
  `boe_id` integer NOT NULL,
  `sno` integer,
  `lcl_fcl` varchar(255),
  `truck` varchar(255),
  `seal` varchar(255),
  `container_number` varchar(255),
  `created_at` datetime
) ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `sb_file_status` (
  `sb_file_status_id` integer NOT NULL ,
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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
  `created_at` datetime)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `equipment_details` (
  `equip_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sbs_id` integer,
  `container` text,
  `seal` text,
  `date` text,
  `s_no` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `challan_details` (
  `challan_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sbs_id` integer,
  `sr_no` text,
  `challan_no` text,
  `paymt_dt` text,
  `amount` text,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `igm_details` (
  `courier_bill_of_entry_id` integer,
  `igm_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `airlines` varchar(255),
  `flight_no` varchar(255),
  `airport_of_arrival` varchar(255),
  `date_of_arrival` date,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `container_details` (
  `courier_bill_of_entry_id` integer,
  `container_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `container_details_srno` int,
  `container` varchar(255),
  `seal_number` varchar(255),
  `fcl_lcl` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `bond_details` (
  `courier_bill_of_entry_id` integer,
  `bond_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `bond_details_srno` int,
  `bond_type` varchar(255),
  `bond_number` varchar(255),
  `clearance_of_imported_goods_bond_already_registered_customs` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `notification_used_for_items` (
  `items_detail_id` integer,
  `item_notification_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `notification_item_srno` int,
  `notification_number` varchar(255),
  `serial_number_of_notification` varchar(255),
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

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
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");

                $this->db->query("CREATE TABLE `payment_details` (
  `courier_bill_of_entry_id` integer,
  `payment_details_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `payment_details_srno` integer, 
  `tr6_challan_number` varchar(255),
  `total_amount` varchar(255),
  `challan_date` date,
  `created_at` timestamp
)ENGINE=MyISAM DEFAULT CHARSET=latin1mb4");






            }
        }

        if ($result) {
            $this->session->set_flashdata(
                "success",
                "Record update Successfully!"
            );
        } else {
            $this->session->set_flashdata("error", "Record not updated!");
        }

        redirect("/admin/iec_signup");
        
        //print_r(implode($pass));
    }

public function get_worksheet_name_by_type(){
        $post = $this->input->post();
        $user_id = $_SESSION["user_id"];
        $iec_id = $_SESSION["iec_no"];
        $db_name = $_SESSION["database_prefix"] . $user_id;
        $this->db->query("use " . $db_name . "");
        $type = $_POST["type"];
        /* $sql = 'SELECT *  FROM tbl_sheets  where type_id ='.$type.'';
        $query = $this->db->query( $sql);
         //print_r($this->db->last_query()); die();
        $importers=$query->result_array();
        $data['list_woksheet']=$importers;
        echo json_encode($data['list_woksheet']);*/

        if (isset($_POST["type"])) {
            //$id = join("','", $_POST['type']);
            $query = "SELECT *  FROM tbl_sheets  where type_id =" . $type;
            $statement = $this->db->query($query);
            //$statement->execute();
            $result = $statement->result_array();
            $output = "";
            foreach ($result as $row) {
                $output .=
                    '<option value="' .
                    $row["tbl_sheet_name"] .
                    '">' .
                    $row["tbl_sheet_name"] .
                    "</option>";
            }
            echo $output;
        }
    }

public function get_worksheet_columns(){
        $data = "";
        $user_id = $_SESSION["user_id"];
        $iec_id = $_SESSION["iec_no"];
        $db_name = $_SESSION["database_prefix"] . $user_id;
        $this->db->query("use " . $db_name . "");
        $post = $this->input->post();
        $data1 = $post["selected1"];

        $len = @strlen(@$data1);
        if ($len > 0) {
            $strs = explode(",", $data1);
            foreach ($strs as $str) {
                echo $sql = "SHOW COLUMNS FROM " . $str;
                $query = $this->db->query($sql);
                $columns[] = $query->result_array();
                $data["columns"][] = $columns;
            }
        }
        echo json_encode($data);
    }

public function iec_reports()
    {
        $user_id = $_SESSION["user_id"];
        $user_id = $_SESSION["user_id"];
        if ($user_id != 1) {
            $db_name = $_SESSION["database_prefix"] . $user_id;
            $this->db->query("use " . $db_name . "");
        }
        $iec_id = $_SESSION["iec_no"];
        $this->load->view("common/header");
        $this->load->view("admin/import_reports_setting");
        $this->load->view("common/footer");
    }
}
