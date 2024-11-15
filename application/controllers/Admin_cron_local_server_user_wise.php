<?php defined("BASEPATH") or exit("No direct script access allowed");
//set_time_limit(6000000);
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Admin_cron_local_server_user_wise extends CI_Controller
{
    public function __construct()
    {
        @parent::__construct();
        $this->load->model("admin/Common_model");
        $this->load->dbforge();
        
     // $this->db = $this->load->database('second', TRUE);
    }
    public function __destruct()
    {
       //mysqli_close($Db1->connection);
       $this->db->close();
    }
   
   

    
    public function lucrative_users(){    
    
        /******************************************************************Start lucrative_users***************************************************************************************/

        $query_boe_delete_logs = "SELECT  * FROM lucrative_users ";
         //$query_boe_delete_logs = "SELECT  * FROM lucrative_users";
        $statement_boe_delete_logs = $this->db->query($query_boe_delete_logs);
        $iecwise_data_boe_delete_logs = [];
        $result_boe_delete_logs = $statement_boe_delete_logs->result_array();
        //print_r($result_boe_delete_logs);exit;

        foreach ($result_boe_delete_logs as $str_boe_delete_logs)
        { 
                //print_r($str_boe_delete_logs);
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
                 
                $lucrative_users_id  = $str_boe_delete_logs["lucrative_users_id"];
                $db1 = $this->database_connection_master();
                
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
                 if ($a==1) 
                 {
                    echo   "$lucrative_users_id Duplicate"."</br>";"============";continue;
                 }
                $sql_insert_boe_delete_logs =
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
       $user = $this->register_iec_user($user_id);
       $user_sheetwise = $this->register_iec_user_sheet_wise_database($user_id);
      }//
       
        /******************************************************************End lucrative_users***************************************************************************************/
    $db1->close();
        $this->db->close();
    }
    private function load_secondary_database($users_id)
    {
        $db_secondary_config = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '%!#^bFjB)z8C',
            'database' => "lucrativeesystem_D2D_S{$users_id}",
            'dbdriver' => 'mysqli',
            'pconnect' => FALSE,
            'db_debug' => (ENVIRONMENT !== 'production'),
        ];
        return $this->load->database($db_secondary_config, TRUE);
    }
    
 /******************************************************************************bill_bond_details****************************************************************************************/
   
    public function bill_bond_details()
    {
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_bond_details_query = "SELECT bill_bond_details.*, bill_of_entry_summary.boe_id as boeid ,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no 
        FROM bill_bond_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_bond_details.boe_id and bill_of_entry_summary.iec_no LIKE '%$iec%' ";
 
    
  
                $this->processUser_dynamic_bond_details($user,$bill_bond_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();

}
    
private function processUser_dynamic_bond_details($user,$query)
{
        $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
 $valid_columns = [
            'bond_details_id', 'boe_id', 'bond_no', 'port', 'bond_cd', 'debt_amt', 'bg_amt', 'created_at', 'boeid', 'be_no', 'iec_no'
        ];

                      // Define unwanted fields
  
      // Process and filter the query details to retain only valid columns
        $valid_query_details = array_map(function ($detail) use ($valid_columns) {
            // Convert 'debt_amt' to a numeric value or default to zero
            $detail['debt_amt'] = is_numeric($detail['debt_amt']) ? floatval($detail['debt_amt']) : 0;

            // Convert 'bg_amt' to a numeric value or default to zero
            $detail['bg_amt'] = is_numeric($detail['bg_amt']) ? floatval($detail['bg_amt']) : 0;

            // Filter out unwanted fields from the detail array
            return array_intersect_key($detail, array_flip($valid_columns));
        }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['boeid','be_no', 'iec_no'];
   $table_name='bill_bond_details';
    
 
            $filtered_query_details = $this->filterDuplicateEntries_dynamic($valid_query_details,$users_id,$unwanted_fields);
      
             if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
            $this->insertLicenceDetailsBatch_dynamic($filtered_query_details, $users_id,$table_name);
             }
        }
    }
}

   private function validateQueryDetailColumns_dynamic($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   private function filterDuplicateEntries_dynamic($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}


    private function excludeUnwantedFields_dynamic($detail,$unwanted_fields)
    {
  
    // Filter out unwanted fields from $detail array
    foreach ($unwanted_fields as $field) {
        unset($detail[$field]);
    }

    return $detail;
}
 private function isDuplicateEntry_dynamic($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM bill_bond_details   where boe_id='{$detail['boe_id']}'  and  bond_no ='{$detail['bond_no']}'";
  
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}

    private function insertLicenceDetailsBatch_dynamic($licence_details, $users_id,$table_name)
    {
        $db_secondary = $this->load_secondary_database($users_id); 

        try {
                $db_secondary->insert_batch($table_name, $licence_details);
            } catch (Exception $e) {
                echo 'Database Error: ' . $e->getMessage();
            }
        
        $db_secondary->close();
    }    
/******************************************************************************end bill_bond_details****************************************************************************************/
    
/******************************************************************************aa_dfia_licence_details****************************************************************************************/

    public function aa_dfia_licence_details()
    {
        // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
          echo  $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id Asc LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                $this->processUser($user);
            }
        }

        // Close the main database connection
        $this->db->close();
    }

    private function processUser($user)
    {
        $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
        echo $licenceQuery = "SELECT ald.*, sbs.sbs_id, sbs.sb_no, sbs.iec, id.invoice_id, id.item_id
            FROM aa_dfia_licence_details ald
            LEFT JOIN item_details id ON ald.item_id = id.item_id
            LEFT JOIN invoice_summary isum ON id.invoice_id = isum.invoice_id
            LEFT JOIN ship_bill_summary sbs ON isum.sbs_id = sbs.sbs_id
            WHERE sbs.iec LIKE '%$iec%'";
        $licence_details = $this->db->query($licenceQuery)->result_array();
          if (!empty($licence_details)) {
        $valid_licence_details = array_filter($licence_details, function ($detail) {
            return $this->validateLicenceDetailColumns($detail);
        });
          
        if (!empty($valid_licence_details)) { //print_r($valid_licence_details);  exit;
            $filtered_licence_details = $this->filterDuplicateEntries($valid_licence_details , $users_id);
           //  print_r($filtered_licence_details);echo "=====================";echo $users_id;
             if (!empty($filtered_licence_details)) {
            $this->insertLicenceDetailsBatch($filtered_licence_details, $users_id);
             }
        }
    }
    }

    private function validateLicenceDetailColumns($detail)
    {

        $valid_columns = [
            'dfia_licence_details_id', 'item_id', 'inv_s_no', 'item_s_no_', 'licence_no',
            'descn_of_export_item', 'exp_s_no', 'expqty', 'uqc_aa', 'fob_value', 'sion',
            'descn_of_import_item', 'imp_s_no', 'impqt', 'uqc_', 'indig_imp', 'created_at','sbs_id','sb_no','iec','invoice_id'
        ];
        return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   private function filterDuplicateEntries($licence_details, $users_id)
    {
    $filtered_details = [];

    foreach ($licence_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields($detail);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
            // throw $e;
        }
    }

    return $filtered_details;
}


    private function excludeUnwantedFields($detail)
    {
    // Define unwanted fields
    $unwanted_fields = ['sbs_id', 'sb_no', 'iec', 'invoice_id'];

    // Filter out unwanted fields from $detail array
    foreach ($unwanted_fields as $field) {
        unset($detail[$field]);
    }

    return $detail;
}
    private function isDuplicateEntry($detail, $users_id)
    {
        $db_secondary = $this->load_secondary_database($users_id);
 echo $users_id;
        $duplicate_check_query = "SELECT COUNT(*) AS num_rows 
            FROM aa_dfia_licence_details 
            WHERE item_s_no_ = '{$detail['item_s_no_']}' AND inv_s_no = '{$detail['inv_s_no']}' AND exp_s_no = '{$detail['exp_s_no']}' AND imp_s_no = '{$detail['imp_s_no']}'";
        $result = $db_secondary->query($duplicate_check_query)->row_array();
        return $result['num_rows'] > 0;
    }

    private function insertLicenceDetailsBatch($licence_details, $users_id)
    {
        $db_secondary = $this->load_secondary_database($users_id); 

        try {
                $db_secondary->insert_batch('aa_dfia_licence_details', $licence_details);
            } catch (Exception $e) {
                echo 'Database Error: ' . $e->getMessage();
            }
        
        $db_secondary->close();
    }
/******************************************************************************end aa_dfia_licence_details****************************************************************************************/

    


/******************************************************************************bill_of_entry_summary****************************************************************************************/
public function bill_of_entry_summary()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT * FROM bill_of_entry_summary WHERE iec_no LIKE '%$iec%' ";
 
    
  
                $this->processUser_dynamic_bill_of_entry_summary($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}
private function processUser_dynamic_bill_of_entry_summary($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                    $valid_columns = [
                        'boe_id', 'boe_file_status_id', 'invoice_title', 'port', 'port_code', 'be_date', 'be_type',
                        'iec_br', 'iec_no', 'br', 'gstin_type', 'cb_code', 'nos', 'pkg', 'item', 'g_wt_kgs', 'cont',
                        'be_status', 'mode', 'def_be', 'kacha', 'sec_48', 'reimp', 'adv_be', 'assess', 'exam', 'hss',
                        'first_check', 'prov_final', 'country_of_origin', 'importer_name_and_address', 'country_of_consignment',
                        'port_of_loading', 'port_of_shipment', 'ad_code', 'cb_name', 'aeo', 'ucr', 'bcd', 'acd', 'sws', 'nccd',
                        'add', 'cvd', 'igst', 'g_cess', 'sg', 'saed', 'gsia', 'tta', 'health', 'total_duty', 'int', 'pnlty', 'fine',
                        'tot_ass_val', 'tot_amount', 'wbe_no', 'wbe_date', 'wh_code', 'wbe_site', 'submission_date', 'assessment_date',
                        'examination_date', 'ooc_date', 'submission_time', 'assessment_time', 'examination_time', 'ooc_time',
                        'submission_exchange_rate', 'assessment_exchange_rate', 'ooc_no', 'ooc_date_', 'created_at',
                        'examination_exchange_rate', 'ooc_exchange_rate','be_no'
                    ];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [
                                'int', 'bcd', 'acd', 'sws', 'nccd', 'add', 'cvd', 'igst', 'g_cess',
                                'sg', 'saed', 'gsia', 'tta', 'health', 'total_duty', 'pnlty', 'fine',
                                'tot_ass_val', 'tot_amount'
                            ];
                            
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }

                          // Define your date fields
                        $date_fields = [
                            'wbe_date', 
                            'submission_date', 
                            'assessment_date', 
                            'examination_date', 
                            'ooc_date'
                        ];
                        
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }


            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='bill_of_entry_summary';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_of_entry_summary($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_bill_of_entry_summary($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_bill_of_entry_summary($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_bill_of_entry_summary($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_bill_of_entry_summary($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
 private function isDuplicateEntry_dynamic_bill_of_entry_summary($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM bill_of_entry_summary   where be_no='{$detail['be_no']}'  and boe_id='{$detail['boe_id']}'";
  
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_bill_of_entry_summary($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_bill_of_entry_summary($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}

private function insertLicenceDetailsBatch_dynamic_bill_of_entry_summary($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    

/******************************************************************************end bill_of_entry_summary****************************************************************************************/



    
public function boe_delete_logs()
{
    // Load PostgearSQL database
    $this->load->database('second');

    // Fetch all admin users from the PostgearSQL database
    $sql_PostgearSQL_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'";
    $admin_users_PostgearSQL = $this->db->query($sql_PostgearSQL_users)->result_array();

    // Process each admin user
    foreach ($admin_users_PostgearSQL as $user) {
        $lucrative_users_id = $user["lucrative_users_id"];
        $iec = $user["iec_no"];

        // Load the secondary database connection based on lucrative_users_id
        $db_secondary = $this->database_connection($lucrative_users_id);

        // Retrieve delete logs from the primary database
        $query_boe_delete_logs = "SELECT * FROM boe_delete_logs where iec_no LIKE '%$iec'";
        $statement_boe_delete_logs = $this->db->query($query_boe_delete_logs);
        $result_boe_delete_logs = $statement_boe_delete_logs->result_array();

        // Process each delete log entry
        if (!empty($result_boe_delete_logs)) {
            foreach ($result_boe_delete_logs as $str_boe_delete_logs) {
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

                // Check for duplicate entries in the secondary database
                $c = $be_no . "-" . $be_date . "-" . $iec_no;
                $sql_duplicate_check = "SELECT * FROM boe_delete_logs WHERE be_no = '$be_no' AND be_date = '$be_date' AND iec_no = '$iec_no'";
                $duplicate_check = $db_secondary->query($sql_duplicate_check);

                // Skip duplicate entries
                if ($duplicate_check->num_rows > 0) {
                    continue; // Skip this entry
                }

                // Insert delete log entry into the secondary database
                $sql_insert_boe_delete_logs =
                    "INSERT INTO `boe_delete_logs` (`boe_delete_logs_id`, `filename`, `be_no`, `be_date`, `iec_no`, `br`, `fullname`, `email`, `mobile`, `deleted_at`) VALUES (" .
                    "'" . $boe_delete_logs_id . "', " .
                    "'" . $filename . "', " .
                    "'" . $be_no . "', " .
                    "'" . $be_date . "', " .
                    "'" . $iec_no . "', " .
                    "'" . $br . "', " .
                    "'" . $fullname . "', " .
                    "'" . $email . "', " .
                    "'" . $mobile . "', " .
                    "'" . $deleted_at . "')";

                $copy_bill_of_entry_summary_boe_delete_logs = $db_secondary->query($sql_insert_boe_delete_logs);
            }
        }

        // Close the secondary database connection
        $db_secondary->close();
    }

    // Close the PostgearSQL database connection
    $this->db->close();
}

/******************************************************************************bill_payment_details****************************************************************************************/
  
public function bill_payment_details()
{
    
     
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_payment_details_query = "SELECT bill_payment_details.*, bill_of_entry_summary.boe_id as boeid, bill_of_entry_summary.be_no, bill_of_entry_summary.iec_no 
            FROM bill_payment_details  
            LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_payment_details.boe_id 
            WHERE bill_of_entry_summary.iec_no  LIKE '%$iec%' ";
 
    
  
                $this->processUser_dynamic_bill_payment_details($user,$bill_payment_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}
private function processUser_dynamic_bill_payment_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
        
         if (!empty($query_details)) {
        $query_details = array_filter($query_details, function ($detail) {
            return $this->validateQueryDetailColumns_dynamic_bill_payment_details($detail);
        });
        
         }
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                    
                    $valid_columns = ['payment_details_id','boe_id','sr_no','challan_no','paid_on','amount','created_at'];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                            
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }

                          // Define your date fields
                        $date_fields = ['paid_on'];
                        
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }


            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['boeid','be_no', 'iec_no'];
                     $table_name='bill_payment_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_payment_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_bill_payment_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_bill_payment_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_bill_payment_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_bill_payment_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_bill_payment_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM bill_payment_details   where boe_id='{$detail['boe_id']}'  and challan_no='{$detail['challan_no']}'  and payment_details_id='{$detail['payment_details_id']}'";
  
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_bill_payment_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_bill_payment_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_bill_payment_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  

/******************************************************************************end bill_payment_details****************************************************************************************/




/******************************************************************************bill_container_details****************************************************************************************/



public function bill_container_details()
{
    
    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT bill_container_details.*, bill_of_entry_summary.boe_id as boeid, bill_of_entry_summary.be_no, bill_of_entry_summary.iec_no 
            FROM bill_container_details  
            LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_container_details.boe_id 
            WHERE bill_of_entry_summary.iec_no  LIKE '%$iec%'";
 
    
  
                $this->processUser_dynamic_bill_of_entry_summary($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();

}
private function processUser_dynamic_bill_container_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape

                    $valid_columns = ['container_details_id','boe_id','sno','lcl_fcl','truck','seal','container_number','created_at'];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = ['sno'];
                            
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }

                          // Define your date fields
                        $date_fields = [];
                        
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }


            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['boeid','be_no', 'iec_no'];
                     $table_name='bill_container_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_container_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_bill_container_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_bill_container_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_bill_container_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_bill_container_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_bill_container_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM bill_container_details   where boe_id='{$detail['boe_id']}'  and container_number='{$detail['container_number']}'  and container_details_pk='{$detail['container_details_id']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_bill_container_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_bill_container_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_bill_container_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  
    

/******************************************************************************end bill_container_details****************************************************************************************/



/******************************************************************************bill_licence_details****************************************************************************************/

public function bill_licence_details()
{
      
    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin' and lucrative_users_id ='152' ")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' and lucrative_users_id ='152' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_licence_details_query = "SELECT CONCAT(n1.be_no,'-',bill_licence_details.invsno,'-',bill_licence_details.itemsn) as reference_code, n1.boe_id as boeid, n1.iec_no, n1.be_no, n1.be_date, bill_licence_details.*
    FROM bill_of_entry_summary n1
    JOIN invoice_and_valuation_details ON n1.boe_id = invoice_and_valuation_details.boe_id 
    JOIN duties_and_additional_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id 
    JOIN bill_licence_details ON duties_and_additional_details.duties_id = bill_licence_details.duties_id 
    WHERE n1.iec_no  LIKE '%$iec%'";
 
    
  
                $this->processUser_dynamic_bill_licence_details($user,$bill_licence_details_query);
            }
        }

        // Close the main database connection
        $this->db->close(); 
   

}


private function processUser_dynamic_bill_licence_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape

                    $valid_columns = ['licence_id','duties_id','invsno','itemsn','lic_slno','lic_no','lic_date','code','port','debit_value','qty','uqc_lc_d','debit_duty','created_at'];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = ['qty','uqc_lc_d'];
                         
 
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }

                          // Define your date fields
                        $date_fields = ['be_date','lic_date'];
                        
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }


            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
      //  print_r($valid_query_details);
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['boeid','be_no', 'iec_no'];
                     $table_name='bill_licence_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_licence_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) 
                         {   
                             echo "=====================";echo 
                             $users_id;
                             $this->insertLicenceDetailsBatch_dynamic_bill_licence_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_bill_licence_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_bill_licence_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_bill_licence_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_bill_licence_details($detail, $users_id)
{
    try { 
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM bill_licence_details   where licence_id ='{$detail['licence_id']}'  and lic_slno='{$detail['lic_slno']}'  and lic_no='{$detail['lic_no']}'  and debit_value='{$detail['debit_value']}'  and qty='{$detail['qty']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_bill_licence_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_bill_licence_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_bill_licence_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  


/******************************************************************************end bill_licence_details****************************************************************************************/







public function boe_file_status()
{
    // Load PostgearSQL database
    $this->load->database('second');
    
    // Fetch all admin users from the PostgearSQL database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_PostgearSQL = $this->db->query($sql_admin_users)->result_array();
    
    // Process each admin user
    foreach ($admin_users_PostgearSQL as $user) {
        $iec_boe_file_status = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];
        
        // Establish connection to the corresponding user's database
        $db_secondary = $this->database_connection($lucrative_users_id);
        
        // Fetch boe_file_status records based on user_iec_no
        $query_boe_file_status = "SELECT * FROM public.boe_file_status WHERE user_iec_no LIKE '%$iec_boe_file_status%'";
        $statement_boe_file_status = $this->db->query($query_boe_file_status);
        $result_boe_file_status = $statement_boe_file_status->result_array();
        
        // Process each boe_file_status record
        foreach ($result_boe_file_status as $str_boe_file_status) {
            $boe_file_status_id = addslashes($str_boe_file_status["boe_file_status_id"]);
            $pdf_filepath = addslashes($str_boe_file_status["pdf_filepath"]);
            $pdf_filename = addslashes($str_boe_file_status["pdf_filename"]);
            $user_iec_no = addslashes($str_boe_file_status["user_iec_no"]);
            $file_iec_no = addslashes($str_boe_file_status["file_iec_no"]);
            $br = addslashes($str_boe_file_status["br"]);
            $be_no = addslashes($str_boe_file_status["be_no"]);
            $stage = addslashes($str_boe_file_status["stage"]);
            $status = addslashes($str_boe_file_status["status"]);
            $remarks = addslashes($str_boe_file_status["remarks"]);
            $created_at = addslashes($str_boe_file_status["created_at"]);
            
            // Insert boe_file_status record into the secondary database
            $sql_insert_boe_file_status =
                "INSERT INTO `boe_file_status` (`boe_file_status_id`, `pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`, `br`, `be_no`, `stage`, `status`, `remarks`, `created_at`) 
                VALUES ('$boe_file_status_id', '$pdf_filepath', '$pdf_filename', '$user_iec_no', '$lucrative_users_id', '$file_iec_no', '$br', '$be_no', '$stage', '$status', '$remarks', '$created_at')";
            
            $db_secondary->query($sql_insert_boe_file_status);
        }
        
        // Close the connection to the secondary database
        $db_secondary->close();
    }
    
    // Close the PostgearSQL database connection
    $this->db->close();
}
/******************************************************************************bill_manifest_details****************************************************************************************/

public function bill_manifest_details()
{
    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_manifest_details_query = "SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id as boeid, bill_of_entry_summary.be_no, bill_of_entry_summary.iec_no 
                                        FROM bill_manifest_details 
                                        LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id 
                                        WHERE bill_of_entry_summary.iec_no LIKE '%$iec%'";
 
    
  
                $this->processUser_dynamic_bill_manifest_details($user,$bill_manifest_details_query);
            }
        }

        // Close the main database connection
        $this->db->close(); 
    

}
private function processUser_dynamic_bill_manifest_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        $valid_columns = [
                            "manifest_details_id" ,"boe_id", "be_no", "igm_no", "igm_date", "inw_date",
                            "gigmno", "gigmdt", "mawb_no", "mawb_date", "hawb_no",
                            "hawb_date", "pkg", "gw", "created_at"
                        ];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = ['gw'];
                         
 
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }

                          // Define your date fields
                        $date_fields = ['igm_date','inw_date','gigmdt','mawb_date','hawb_date'];
        
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }


            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['boeid','be_no', 'iec_no'];
                     $table_name='bill_manifest_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_manifest_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_bill_manifest_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_bill_manifest_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_bill_manifest_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_bill_manifest_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_bill_manifest_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
   echo           $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM bill_manifest_details   where manifest_details_id  ='{$detail['manifest_details_id']}'  and igm_no='{$detail['igm_no']}' ";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_bill_manifest_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_bill_manifest_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_bill_manifest_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  
/******************************************************************************end bill_manifest_details****************************************************************************************/



/******************************************************************************challan_details****************************************************************************************/

public function challan_details(){    
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_manifest_details_query = "SELECT challan_details.*,ship_bill_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sb_no,ship_bill_summary.iec FROM challan_details  
    JOIN ship_bill_summary ON ship_bill_summary.sbs_id=challan_details.sbs_id  Where ship_bill_summary.iec LIKE '%$iec%'";
 
    
  
                $this->processUser_dynamic_challan_details($user,$bill_manifest_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();  

    /******************************************************************End bill_of_entry_summary***************************************************************************************/
}
private function processUser_dynamic_challan_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        $valid_columns = [
                            "challan_id" ,"sbs_id", "sb_no", "sr_no", "challan_no", "paymt_dt",
                            "amount", "created_at"
                        ];
                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
 
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }

                          // Define your date fields
                        $date_fields = ['paymt_dt'];
        
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }


            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['iec','sb_no'];
                     $table_name='challan_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_challan_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_challan_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_challan_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_challan_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_challan_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_challan_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM challan_details   where sbs_id  ='{$detail['sbs_id']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_challan_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_challan_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_challan_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  
/******************************************************************************End challan_details****************************************************************************************/


/******************************************************************************drawback_details****************************************************************************************/

public function drawback_details()
{
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
         
           $admin_users = $this->db->query($adminUsersQuery)->result_array();
           //echo '<pre>'; print_r( $admin_users);
            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
               echo $bill_manifest_details_query = "SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br, iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* 
                                    FROM ship_bill_summary n1 
                                    JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id 
                                    JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id 
                                    JOIN drawback_details ON drawback_details.item_id = item_details.item_id 
                                    WHERE n1.iec LIKE '%$iec%'";
 
                    $this->processUser_dynamic_drawback_details($user,$bill_manifest_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();   
    

}
private function processUser_dynamic_drawback_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                        $valid_columns = [
                            'drawback_id', 'item_id', 'inv_sno', 'item_sno', 'dbk_sno', 'qty_wt',
                            'value', 'dbk_amt', 'stalev', 'cenlev', 'rosctl_amt', 'created_at',
                            'rate', 'rebate', 'amount', 'dbk_rosl'
                        ];
                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = [];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
       //print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['iec','reference_code', 'sb_no', 'sb_date', 'iec_br', 'inv_sno', 'item_sno', 'hs_cd', 'description', 'dbk_sno', 'qty_wt', 'value', 'dbk_amt', 'stalev', 'cenlev'];
                     $table_name='drawback_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_drawback_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_drawback_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}

private function filterDuplicateEntries_dynamic_drawback_details($valid_query_details, $users_id, $unwanted_fields)
{
        // Filter details to exclude duplicates and unwanted fields
        $filtered_details = array_filter($valid_query_details, function($detail) use ($users_id) {
            return !$this->isDuplicateEntry_dynamic_drawback_details($detail, $users_id);
        });

        // Exclude unwanted fields from each filtered detail
        $filtered_details = array_map(function($detail) use ($unwanted_fields) {
            return $this->excludeUnwantedFields_dynamic_drawback_details($detail, $unwanted_fields);
        }, $filtered_details);

        return $filtered_details;
    }

private function isDuplicateEntry_dynamic_drawback_details($detail, $users_id)
{
        try {
            // Load secondary database connection
            $db_secondary = $this->load_secondary_database($users_id);

            // Prepare and execute the duplicate check query
            $duplicate_query = "SELECT COUNT(*) AS num_rows 
                                FROM drawback_details
                                WHERE item_id = ? AND qty_wt = ? AND inv_sno = ? AND item_sno = ? AND dbk_sno = ?";
            
            // Bind parameters and execute query
            $result = $db_secondary->query($duplicate_query,[
                $detail['item_id'],
                $detail['qty_wt'],
                $detail['inv_sno'],
                $detail['item_sno'],
                $detail['dbk_sno']
            ])->row_array();
            print_r($result);
            // Close secondary database connection
            $db_secondary->close();

            // Check if there are duplicate rows based on the query result
            return $result['num_rows'] > 0;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic_drawback_details: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }

private function excludeUnwantedFields_dynamic_drawback_details($detail, $unwanted_fields)
{
        // Create a copy of the detail array to avoid modifying the original array
        $filtered_detail = $detail;

        // Filter out unwanted fields from the copied detail array
        foreach ($unwanted_fields as $field) {
            unset($filtered_detail[$field]);
        }

        return $filtered_detail;
    }

private function validateQueryDetailColumns_dynamic_drawback_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}

private function insertLicenceDetailsBatch_dynamic_drawback_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
           // Generate the insert batch query (without executing)
            $insert_query = $db_secondary->insert_batch($table_name, $licence_details);
            
            // Echo the generated insert batch query
            echo $insert_query;
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    //print_r($db_secondary->last_query());
    $db_secondary->close();
} 
/******************************************************************************End drawback_details****************************************************************************************/


public function cb_file_status()
{
    // Load PostgearSQL database
    $this->load->database('second');

    // Fetch all admin users from the PostgearSQL database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_PostgearSQL = $this->db->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_PostgearSQL as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's database
        $db_secondary = $this->database_connection($lucrative_users_id);

        // Fetch cb_file_status records based on user_iec_no
        $query_cb_file_status = "SELECT * FROM cb_file_status WHERE user_iec_no = ?";
        $statement_cb_file_status = $db_secondary->query($query_cb_file_status, array($iec_no));
        $result_cb_file_status = $statement_cb_file_status->result_array();

        // Process each cb_file_status record
        foreach ($result_cb_file_status as $str_cb_file_status) {
            // Check if the record already exists in the secondary database
            $check_duplicate_query = "SELECT * FROM `cb_file_status` WHERE `pdf_filepath` = ? AND `pdf_filename` = ?";
            $duplicate_result = $db_secondary->query($check_duplicate_query, array($str_cb_file_status["pdf_filepath"], $str_cb_file_status["pdf_filename"]));

            if ($duplicate_result->num_rows() > 0) {
                continue; // Skip this entry if duplicate exists
            }

            // Insert cb_file_status record into the secondary database using parameterized query
            $sql_insert_cb_file_status =
                "INSERT INTO `cb_file_status` (`pdf_filepath`, `pdf_filename`, `user_iec_no`, `lucrative_users_id`, `file_iec_no`, `cb_no`, `cb_date`, `stage`, `status`, `remarks`, `created_at`, `br`, `is_processed`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE `user_iec_no` = VALUES(`user_iec_no`), `lucrative_users_id` = VALUES(`lucrative_users_id`), `file_iec_no` = VALUES(`file_iec_no`), `cb_no` = VALUES(`cb_no`), `cb_date` = VALUES(`cb_date`), `stage` = VALUES(`stage`), `status` = VALUES(`status`), `remarks` = VALUES(`remarks`), `created_at` = VALUES(`created_at`), `br` = VALUES(`br`), `is_processed` = VALUES(`is_processed`)";

            // Execute the insertion query with parameters
            $db_secondary->query($sql_insert_cb_file_status, array(
                $str_cb_file_status["pdf_filepath"],
                $str_cb_file_status["pdf_filename"],
                $str_cb_file_status["user_iec_no"],
                $str_cb_file_status["lucrative_users_id"],
                $str_cb_file_status["file_iec_no"],
                $str_cb_file_status["cb_no"],
                $str_cb_file_status["cb_date"],
                $str_cb_file_status["stage"],
                $str_cb_file_status["status"],
                $str_cb_file_status["remarks"],
                $str_cb_file_status["created_at"],
                $str_cb_file_status["br"],
                $str_cb_file_status["is_processed"]
            ));
        }

        // Close the connection to the secondary database
        $db_secondary->close();
    }

    // Close the PostgearSQL database connection
    $this->db->close();
    // Close the main database connection
    $this->db->close();
}


public function courier_bill_bond_details()
{
    // Load PostgearSQL database
    $this->load->database('second');

    // Fetch all admin users from the PostgearSQL database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_PostgearSQL = $this->db->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_PostgearSQL as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's database
        $db_secondary = $this->database_connection($lucrative_users_id);

        // Fetch courier_bill_bond_details records based on user_iec_no
        $query_courier_bill_bond_details =
            "SELECT cbd.*, cbs.user_iec_no, cbs.cb_file_status_id
            FROM courier_bill_bond_details cbd
            LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbd.courier_bill_of_entry_id
            WHERE cbs.user_iec_no = '%$iec_no'";

        $statement_courier_bill_bond_details = $this->db->query($query_courier_bill_bond_details);
        $result_courier_bill_bond_details = $statement_courier_bill_bond_details->result_array();

        // Process each courier_bill_bond_details record
        foreach ($result_courier_bill_bond_details as $str_courier_bill_bond_details) {
            $bond_details_id = addslashes($str_courier_bill_bond_details["bond_details_id"]);
            $bond_details_srno = addslashes($str_courier_bill_bond_details["bond_details_srno"]);
            $bond_type = addslashes($str_courier_bill_bond_details["bond_type"]);
            $bond_number = addslashes($str_courier_bill_bond_details["bond_number"]);
            $clearance_of_imported_goods_bond_already_registered_customs = addslashes($str_courier_bill_bond_details["clearance_of_imported_goods_bond_already_registered_customs"]);
            $created_at = addslashes($str_courier_bill_bond_details["created_at"]);

            // Insert courier_bill_bond_details record into the secondary database
            $sql_insert_courier_bill_bond_details =
                "INSERT INTO `courier_bill_bond_details` (`bond_details_id`, `bond_details_srno`, `bond_type`, `bond_number`, `clearance_of_imported_goods_bond_already_registered_customs`, `created_at`) 
                VALUES ('$bond_details_id', '$bond_details_srno', '$bond_type', '$bond_number', '$clearance_of_imported_goods_bond_already_registered_customs', '$created_at')";

            // Execute the insertion query on the secondary database
            $db_secondary->query($sql_insert_courier_bill_bond_details);
        }

        // Close the connection to the secondary database
        $db_secondary->close();
    }

    // Close the PostgearSQL database connection
    $this->db->close();
    
}

public function courier_bill_container_details()
{
    // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);

        // Retrieve courier bill container details using a LEFT JOIN query
        $query_courier_bill_container_details =
            "SELECT cbc.*, cbs.courier_bill_of_entry_id, cbs.container_details_id, cbs.container_details_srno, cbs.container, cbs.seal_number, cbs.fcl_lcl, cbs.created_at 
            FROM courier_bill_container_details cbc 
            LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbc.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id 
            WHERE cfs.user_iec_no LIKE '%$iec_no'";

        $statement_courier_bill_container_details = $db_primary->query($query_courier_bill_container_details);
        $result_courier_bill_container_details = $statement_courier_bill_container_details->result_array();

        // Process each courier bill container detail for insertion into the secondary database
        foreach ($result_courier_bill_container_details as $str_courier_bill_container_details) {
            $courier_bill_of_entry_id = addslashes($str_courier_bill_container_details['courier_bill_of_entry_id']);
            $container_details_id = addslashes($str_courier_bill_container_details['container_details_id']);
            $container_details_srno = addslashes($str_courier_bill_container_details['container_details_srno']);
            $container = addslashes($str_courier_bill_container_details['container']);
            $seal_number = addslashes($str_courier_bill_container_details['seal_number']);
            $fcl_lcl = addslashes($str_courier_bill_container_details['fcl_lcl']);
            $created_at = addslashes($str_courier_bill_container_details['created_at']);

            // Check for duplicate entries in the corresponding user's database
            $duplicate_check = $db_secondary->get_where('courier_bill_container_details', array(
                'courier_bill_of_entry_id' => $courier_bill_of_entry_id,
                'container_details_id' => $container_details_id
            ));

            // Skip duplicate entries
            if ($duplicate_check->num_rows() > 0) {
                continue; // Skip this entry
            }

            // Prepare data for insertion into the secondary database
            $data = array(
                'courier_bill_of_entry_id' => $courier_bill_of_entry_id,
                'container_details_id' => $container_details_id,
                'container_details_srno' => $container_details_srno,
                'container' => $container,
                'seal_number' => $seal_number,
                'fcl_lcl' => $fcl_lcl,
                'created_at' => $created_at
            );

            // Insert courier bill container details into the corresponding user's database
            $db_secondary->insert('courier_bill_container_details', $data);
        }

        // Close the connection to the secondary database
        $db_secondary->close();
    }

    // Close the primary database connection
    $db_primary->close();
}

public function courier_bill_duty_details()
{
    // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);

        // Retrieve duty details based on user_iec_no using a JOIN query
        $query_courier_bill_duty_details =
            "SELECT cbd.*, cbs.courier_bill_of_entry_id, cbs.container_details_id, cbs.items_detail_id 
            FROM courier_bill_duty_details cbd 
            LEFT JOIN courier_bill_items_details cbs ON cbs.items_detail_id = cbd.items_detail_id 
            LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbs.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id 
            WHERE cfs.user_iec_no LIKE '%$iec_no'";

        $statement_courier_bill_duty_details = $db_primary->query($query_courier_bill_duty_details);
        $result_courier_bill_duty_details = $statement_courier_bill_duty_details->result_array();

        // Process each duty detail for insertion into the secondary database
        foreach ($result_courier_bill_duty_details as $str_courier_bill_duty_details) {
            // Prepare duty details for insertion into the secondary database
            $data = array(
                'duty_details_id' => addslashes($str_courier_bill_duty_details['duty_details_id']),
                'bcd_duty_head' => addslashes($str_courier_bill_duty_details['bcd_duty_head']),
                'bcd_ad_valorem' => addslashes($str_courier_bill_duty_details['bcd_ad_valorem']),
                'bcd_specific_rate' => addslashes($str_courier_bill_duty_details['bcd_specific_rate']),
                'bcd_duty_forgone' => addslashes($str_courier_bill_duty_details['bcd_duty_forgone']),
                'sw_srchrg_duty_head' => addslashes($str_courier_bill_duty_details['sw_srchrg_duty_head']),
                'sw_srchrg_ad_valorem' => addslashes($str_courier_bill_duty_details['sw_srchrg_ad_valorem']),
                'sw_srchrg_specific_rate' => addslashes($str_courier_bill_duty_details['sw_srchrg_specific_rate']),
                'sw_srchrg_duty_forgone' => addslashes($str_courier_bill_duty_details['sw_srchrg_duty_forgone']),
                'sw_srchrg_duty_amount' => addslashes($str_courier_bill_duty_details['sw_srchrg_duty_amount']),
                'igst_duty_head' => addslashes($str_courier_bill_duty_details['igst_duty_head']),
                'igst_ad_valorem' => addslashes($str_courier_bill_duty_details['igst_ad_valorem']),
                'igst_specific_rate' => addslashes($str_courier_bill_duty_details['igst_specific_rate']),
                'igst_duty_forgone' => addslashes($str_courier_bill_duty_details['igst_duty_forgone']),
                'igst_duty_amount' => addslashes($str_courier_bill_duty_details['igst_duty_amount']),
                'cmpnstry_duty_head' => addslashes($str_courier_bill_duty_details['cmpnstry_duty_head']),
                'cmpnstry_ad_valorem' => addslashes($str_courier_bill_duty_details['cmpnstry_ad_valorem']),
                'cmpnstry_specific_rate' => addslashes($str_courier_bill_duty_details['cmpnstry_specific_rate']),
                'cmpnstry_duty_forgone' => addslashes($str_courier_bill_duty_details['cmpnstry_duty_forgone']),
                'cmpnstry_duty_amount' => addslashes($str_courier_bill_duty_details['cmpnstry_duty_amount']),
                'created_at' => addslashes($str_courier_bill_duty_details['created_at'])
            );

            // Insert duty details into the corresponding user's database
            $db_secondary->insert('courier_bill_duty_details', $data);
        }

        // Close the connection to the secondary database
        $db_secondary->close();
    }

    // Close the primary database connection
    $db_primary->close();
}

public function courier_bill_igm_details(){    
        /******************************************************************Start courier_bill_container_details***************************************************************************************/
    // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);
         $query_courier_bill_igm_details =
            "SELECT courier_bill_igm_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_igm_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_igm_details.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id  Where cb_file_status.user_iec_no Like '%$iec'";
        $statement_courier_bill_igm_details = $db_secondary->query(
            $query_courier_bill_igm_details
        );
        $iecwise_courier_bill_igm_details = [];
        $result_courier_bill_igm_details = $statement_courier_bill_igm_details->result_array();
        //print_r($result_courier_bill_igm_details);exit;

        foreach ( $result_courier_bill_igm_details as $str_courier_bill_igm_details) {
            $iec_courier_bill_igm_details =
           
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
                        $date_of_arrival = date("Y-m-d",strtotime($date_of_arrival));

            $sql_insert_courier_bill_igm_details =
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
$db1_courier_bill_igm_details->close();
}
        $this->db->close();
        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }

public function courier_bill_invoice_details()
{
    // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);

        $query_courier_bill_invoice_details =
            "SELECT cbi.*, cfs.cb_file_status_id, cfs.user_iec_no 
            FROM courier_bill_invoice_details cbi 
            left JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbi.courier_bill_of_entry_id 
            left JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id 
            WHERE cfs.user_iec_no LIKE '%$iec_no'";

        $statement_courier_bill_invoice_details = $db_primary->query($query_courier_bill_invoice_details);
        $result_courier_bill_invoice_details = $statement_courier_bill_invoice_details->result_array();

        foreach ($result_courier_bill_invoice_details as $str_courier_bill_invoice_details) {
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
      
            $sql_insert_courier_bill_invoice_details =
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
            $copy_insert_courier_bill_invoice_details = $db_secondary->query(
                $sql_insert_courier_bill_invoice_details
            );
            // Insert into the secondary database
        }

        // Close the connection to the secondary database
        $db_secondary->close();
    }

    // Close the primary database connection
    $db_primary->close();
}
public function courier_bill_items_details(){    
        /******************************************************************Start courier_bill_items_details***************************************************************************************/
 // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);

        // Fetch all courier bill items details along with their corresponding user IEC numbers
        $query_courier_bill_items_details =
            "SELECT courier_bill_items_details.*, cb_file_status.user_iec_no 
            FROM courier_bill_items_details 
            LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_items_details.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id = courier_bill_summary.cb_file_status_id 
            WHERE cb_file_status.user_iec_no LIKE '%$iec_no'";

        $statement_courier_bill_items_details = $db_primary->query($query_courier_bill_items_details);
        $result_courier_bill_items_details = $statement_courier_bill_items_details->result_array();

        

        foreach (
            $result_courier_bill_items_details
            as $str_courier_bill_items_details
        ) {
         

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
          $accessories_if_any= addslashes(
                    $str_courier_bill_items_details["accessories_if_any"]
                );
                $model= addslashes(
                    $str_courier_bill_items_details["model"]
                );
            $sql_insert_courier_bill_items_details =
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
VALUES('" .$items_detail_id .
                "',
'" .$case_for_reimport .
                "',
'" .$import_against_license .
                "',
'" .$serial_number_in_invoice .
                "',
'" .$item_description .
                "',
'" .$general_description .
                "',
'" .$currency_for_unit_price .
                "',
'" .$unit_price .
                "',
'" .$unit_of_measure .
                "',
'" .$quantity .
                "',
'" .$rate_of_exchange .
                "',
'" .$accessories_if_any.
                "',
'" .$name_of_manufacturer.
                "',
'" .$brand .
                "',
'" .$model .
                "',
'" .$grade .
                "',
'" .$specification .
                "',
'" .$end_use_of_item .
                "',
'" .$items_details_country_of_origin .
                "',
'" .$bill_of_entry_number .
                "',
'" .$details_in_case_of_previous_imports_date .
                "',
'" .$details_in_case_previous_imports_currency .
                "',
'" .$unit_value .
                "',
'" .$customs_house .
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
            $copy_insert_courier_bill_items_details = $db_secondary->query(
                $sql_insert_courier_bill_items_details
            );
        }
    
 $db_primary->close();
    }
        $db_secondary->close();   
    
    
}

public function courier_bill_manifest_details(){    
        /******************************************************************Start courier_bill_container_details***************************************************************************************/
       
       // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);
       
       
       
        $query_courier_bill_manifest_details =
            "SELECT courier_bill_manifest_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no FROM courier_bill_manifest_details 
            LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_manifest_details.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id where cb_file_status.user_iec_no Like '%$iec_no'";
        $statement_courier_bill_manifest_details = $db_primary->query(
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
            $sql_courier_bill_manifest_details = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_courier_bill_manifest_details'";
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
         
            $sql_insert_courier_bill_manifest_details =
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
            $copy_insert_courier_bill_manifest_details = $db_secondary->query(
                $sql_insert_courier_bill_manifest_details
            );
        }
 $db_primary->close();
    }
        $db_secondary->close(); 
        /******************************************************************End courier_bill_container_details***************************************************************************************/
    }

public function courier_bill_notification_used_for_items(){    
        /******************************************************************Start courier_bill_container_details***************************************************************************************/
              
       // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);
       
       
       
        $query_courier_bill_notification_used_for_items =
            "SELECT courier_bill_notification_used_for_items.*,courier_bill_items_details.items_detail_id,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_notification_used_for_items LEFT JOIN courier_bill_items_details ON courier_bill_notification_used_for_items.items_detail_id=courier_bill_items_details.items_detail_id 
            LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id Where cb_file_status.user_iec_no Like '%$iec_no'";
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
            $sql_courier_bill_notification_used_for_items = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_courier_bill_notification_used_for_items'";
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
    

            $sql_insert_courier_bill_notification_used_for_items =
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
            $copy_insert_courier_bill_notification_used_for_items = $db_secondary->query(
                $sql_insert_courier_bill_notification_used_for_items
            );
        }
$db_secondary->close();
}
        $db_primary->close(); 
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
            $sql_courier_bill_payment_details = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_courier_bill_payment_details'";
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

            $sql_insert_courier_bill_payment_details =
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
$db1_courier_bill_payment_details->close();
        $this->db->close(); 
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
            $sql_courier_bill_procurment_details = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_courier_bill_procurment_details'";
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

            $sql_insert_courier_bill_procurment_details =
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
    $db1_courier_bill_procurment_details->close();
        $this->db->close(); 
    
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


            $sql_insert_courier_bill_summary =
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
        
      $db1_courier_bill_summary->close();
        $this->db->close();    
        
    }



/******************************************************************************item_details****************************************************************************************/

public function item_details(){    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $item_details_query = "SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv,'-',item_details.item_s_no) as reference_code,ship_bill_summary.sb_date,item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id as sbsid,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details 
        JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id 
        JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE ship_bill_summary.iec LIKE '%$iec%'";
 
    
  
                $this->processUser_dynamic_item_details($user,$item_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();   
   

}

private function processUser_dynamic_item_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["reference_code","sb_date","item_id", "invoice_id", "invsn", "item_s_no", "hs_cd", "description", "quantity", "uqc", "rate", "value_f_c", "fob_inr",
                   "pmv", "duty_amt", "cess_rt", "cesamt", "dbkclmd", "igststat", "igst_value_item", "igst_amount", "schcod", "scheme_description", "sqc_msr",
                   "sqc_uqc", "state_of_origin_i", "district_of_origin", "pt_abroad", "comp_cess", "end_use", "fta_benefit_availed", "reward_benefit", 
                   "third_party_item", "created_at"];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = [];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
         //print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['iec','reference_code', 'sb_no', 'sb_date', 'iec_br','invoiceid','sbsid'];
                     $table_name='item_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_item_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_item_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_item_details($valid_query_details, $users_id,$unwanted_fields)
{
     // Filter details to exclude duplicates and unwanted fields
        $filtered_details = array_filter($valid_query_details, function($detail) use ($users_id) {
            return !$this->isDuplicateEntry_dynamic_item_details($detail, $users_id);
        });

        // Exclude unwanted fields from each filtered detail
        $filtered_details = array_map(function($detail) use ($unwanted_fields) {
            return $this->excludeUnwantedFields_dynamic_item_details($detail, $unwanted_fields);
        }, $filtered_details);

        return $filtered_details;
   
}
private function isDuplicateEntry_dynamic_item_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM item_details   where item_id  ='{$detail['item_id']}' and invoice_id  ='{$detail['invoice_id']}' and invsn  ='{$detail['invsn']}' and item_s_no  ='{$detail['item_s_no']}' ";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_item_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_item_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_item_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    print_r($db_secondary->last_query());
    $db_secondary->close();
}





    
/******************************************************************************End item_details****************************************************************************************/
    
    
    
/******************************************************************************invoice_and_valuation_details****************************************************************************************/
    
public function invoice_and_valuation_details()
{    
    // Load secondary database
    $this->load->database('second');

    // Fetch admin users with paginated queries to handle large datasets
    $perPage = 50; // Adjust based on memory and performance testing
    $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
    $pages = ceil($totalUsers / $perPage);

    for ($page = 0; $page < $pages; $page++) {
        $offset = $page * $perPage;
        $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id ASC LIMIT $perPage OFFSET $offset";
        $admin_users = $this->db->query($adminUsersQuery)->result_array();

        foreach ($admin_users as $user) {
            $iec = $user['iec_no'];
            $invoice_and_valuation_details_query = "SELECT invoice_and_valuation_details.*, bill_of_entry_summary.iec_no 
                                                    FROM invoice_and_valuation_details 
                                                    LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = invoice_and_valuation_details.boe_id  
                                                    WHERE bill_of_entry_summary.iec_no LIKE '%$iec%'";

            $this->processUser_dynamic_invoice_and_valuation_details($user, $invoice_and_valuation_details_query);
        }
    }

    // Close the main database connection
    $this->db->close();  
}

private function processUser_dynamic_invoice_and_valuation_details($user, $query)
{
    $iec = $user["iec_no"];
    $users_id = $user["lucrative_users_id"];

    // Fetch details using the specified query
    $query_details = $this->db->query($query)->result_array();

    if (!empty($query_details)) {
        // Define the valid columns to be inserted into invoice_and_valuation_details
        $valid_columns = [
            "invoice_id", "boe_id", "s_no", "invoice_no", "purchase_order_no", "lc_no", "contract_no", 
            "buyer_s_name_and_address", "seller_s_name_and_address", "supplier_name_and_address", 
            "third_party_name_and_address", "aeo", "ad_code", "inv_value", "freight", "insurance", "hss", 
            "loading", "commn", "pay_terms", "valuation_method", "reltd", "svb_ch", "svb_no", "date", "loa", 
            "cur", "term", "c_and_b", "coc", "cop", "hnd_chg", "g_and_s", "doc_ch", "coo", "r_and_lf", 
            "oth_cost", "ld_uld", "ws", "otc", "misc_charge", "ass_value", "invoice_date", "purchase_order_date", 
            "lc_date", "contract_date", "freight_cur", "created_at"
        ];
        
           $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
        
        
                $numeric_fields = ['loa','misc_charge','ass_value'];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = ['date','invoice_date','purchase_date','lc_date','contract_date'];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
             // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
         //print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 

      
         $unwanted_fields = ['iec_no'];
                     $table_name='invoice_and_valuation_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_invoice_and_valuation_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_invoice_and_valuation_details($filtered_query_details, $users_id,$table_name);
                         }
        
        
        
    }
}
}
private function validateQueryDetailColumns_dynamic_invoice_and_valuation_details($detail, $valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}

private function filterDuplicateEntries_dynamic_invoice_and_valuation_details($valid_query_details, $users_id, $unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            if (!$this->isDuplicateEntry_dynamic_invoice_and_valuation_details($detail, $users_id)) {
                $filtered_detail = $this->excludeUnwantedFields_dynamic_invoice_and_valuation_details($detail, $unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            throw $e;
        }
    }

    return $filtered_details;
}

private function isDuplicateEntry_dynamic_invoice_and_valuation_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);

        $duplicate_query = "SELECT COUNT(*) AS num_rows 
                            FROM invoice_and_valuation_details  
                            WHERE s_no = ? AND invoice_no = ? AND invoice_date = ?";

        // Bind parameters and execute the query using CodeIgniter's query builder
        $result = $db_secondary->query($duplicate_query, array(
            $detail['s_no'],
            $detail['invoice_no'],
            $detail['invoice_date']
        ))->row_array();

        $db_secondary->close();

        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        log_message('error', 'Database Error in isDuplicateEntry_dynamic_invoice_and_valuation_details: ' . $e->getMessage());
        return false;
    }
}


private function excludeUnwantedFields_dynamic_invoice_and_valuation_details($detail, $unwanted_fields)
{
    foreach ($unwanted_fields as $field) {
        unset($detail[$field]);
    }

    return $detail;
}

private function insertLicenceDetailsBatch_dynamic_invoice_and_valuation_details($licence_details, $users_id, $table_name)
{
    $db_secondary = $this->load_secondary_database($users_id);

    try {
        $db_secondary->insert_batch($table_name, $licence_details);
    } catch (Exception $e) {
        log_message('error', 'Database Error: ' . $e->getMessage());
    }

    $db_secondary->close();
}
   
/******************************************************************************End invoice_and_valuation_details****************************************************************************************/
    
    
    
    
    
    
    
public function invoice_and_valuation_details_ORIGENAL(){    
        $query_invoice_and_valuation_details =
            "SELECT invoice_and_valuation_details.*, bill_of_entry_summary.boe_id,bill_of_entry_summary.iec_no FROM invoice_and_valuation_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = invoice_and_valuation_details.boe_id ";
        $statement_invoice_and_valuation_details = $this->db->query($query_invoice_and_valuation_details);
        $iecwise_invoice_and_valuation_details = [];
        $result_invoice_and_valuation_details = $statement_invoice_and_valuation_details->result_array();
       // count($result_invoice_and_valuation_details);
     // print_r($result_invoice_and_valuation_details);exit;
$batchSize = 9000;

        // Loop through the records in batches of 9000
        for ($offset = 0;$offset < count($result_invoice_and_valuation_details);$offset += $batchSize) {
        foreach ($result_invoice_and_valuation_details as $str_invoice_and_valuation_details) 
        {
            $iec_invoice_and_valuation_details =
                $str_invoice_and_valuation_details["iec_no"];
            $sql_invoice_and_valuation_details = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_invoice_and_valuation_details'";
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

             $sql_insert_invoice_and_valuation_details =
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
    $db1_invoice_and_valuation_details->close();
        $this->db->close(); 
    
    
}
    
/****************************************************************************** rodtep_details****************************************************************************************/

public function rodtep_details(){    
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $invoice_and_valuation_details_query = "SELECT CONCAT(ship_bill_summary.sb_no,'-',rodtep_details.inv_sno, '-', rodtep_details.item_sno) as reference_code,rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id,ship_bill_summary.sb_date FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE ship_bill_summary.iec LIKE '%$iec%'";

                $this->processUser_dynamic_rodtep_details($user,$invoice_and_valuation_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();  
   
}
private function processUser_dynamic_rodtep_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["item_id", "inv_sno", "item_sno", "quantity", "uqc", "no_of_units","value"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = ['value'];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = [];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
         //print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['invoice_id','sbs_id','iec','sb_date'];
                     $table_name='rodtep_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_rodtep_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                             $this->insertLicenceDetailsBatch_dynamic_rodtep_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_rodtep_details($valid_query_details, $users_id,$unwanted_fields)
{
     // Filter details to exclude duplicates and unwanted fields
        $filtered_details = array_filter($valid_query_details, function($detail) use ($users_id) {
            return !$this->isDuplicateEntry_dynamic_rodtep_details($detail, $users_id);
        });

        // Exclude unwanted fields from each filtered detail
        $filtered_details = array_map(function($detail) use ($unwanted_fields) {
            return $this->excludeUnwantedFields_dynamic_rodtep_details($detail, $unwanted_fields);
        }, $filtered_details);

        return $filtered_details;
}
private function isDuplicateEntry_dynamic_rodtep_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
               $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM rodtep_details  where item_id  ='{$detail['item_id']}' and inv_sno  ='{$detail['inv_sno']}' and item_sno  ='{$detail['item_sno']}'";
          // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_rodtep_details($detail,$valid_columns)
{
 return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_rodtep_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_rodtep_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}          
/******************************************************************************End rodtep_details****************************************************************************************/

/*****************************************************************************jobbing_details****************************************************************************************/

public function jobbing_details(){    
    
        // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $jobbing_details_query = "SELECT jobbing_details.*,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_no FROM jobbing_details JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id Where ship_bill_summary.iec LIKE '%$iec%'";

                $this->processUser_dynamic_jobbing_details($user,$jobbing_details_query);
            }
        }

        // Close the main database connection
        $this->db->close(); 
    
}
private function processUser_dynamic_jobbing_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["jobbing_detail_id", "sb_no","sbs_id", "be_no", "be_date", "port_code_j", "descn_of_imported_goods",  "qty_imp",  "qty_used", "created_at"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = ['be_date'];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
         //print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = ['iec','sb_no'];
                     $table_name='jobbing_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_jobbing_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_jobbing_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_jobbing_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_jobbing_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_jobbing_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_jobbing_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
               $duplicate_query = "SELECT COUNT(*) AS num_rows FROM jobbing_details
            JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id  where sb_no  ='{$detail['sb_no']}' and be_no  ='{$detail['be_no']}' and qty_imp  ='{$detail['qty_imp']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_jobbing_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_jobbing_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_jobbing_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  
/******************************************************************************End jobbing_details****************************************************************************************/

/******************************************************************************ship_bill_summary****************************************************************************************/


public function ship_bill_summary()
{
    
         // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              echo  $jobbing_details_query = " SELECT * FROM ship_bill_summary Where iec LIKE '%$iec%'";

                $this->processUser_dynamic_ship_bill_summary($user,$jobbing_details_query);
            }
        }

        // Close the main database connection
        $this->db->close(); 
    
}
private function processUser_dynamic_ship_bill_summary($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["sbs_id", "sb_file_status_id", "invoice_title", "port_code", "sb_no", "sb_date", "iec", "br", "iec_br", "gstin", "type", "cb_code", "inv_nos", "item_no", "cont_no", "address", "pkg", "g_wt_unit", "g_wt_value", "mode", "assess", "exmn", "jobbing", "meis", "dbk", "rodtp", "deec_dfia", "dfrc", "reexp", "lut", "port_of_loading", "country_of_finaldestination", "state_of_origin", "port_of_finaldestination", "port_of_discharge", "country_of_discharge", "exporter_name_and_address", "consignee_name_and_address", "declarant_type", "ad_code", "gstin_type_", "rbi_waiver_no_and_dt", "forex_bank_account_no", "cb_name", "dbk_bank_account_no", "aeo", "ifsc_code", "fob_value_sum", "freight", "insurance", "discount", "com", "deduction", "p_c", "duty", "cess", "dbk_claim", "igst_amt", "cess_amt", "igst_value", "rodtep_amt", "rosctl_amt", "mawb_no", "mawb_dt", "hawb_no", "hawb_dt", "noc", "cin_no", "cin_dt", "cin_site_id", "seal_type", "nature_of_cargo", "no_of_packets", "no_of_containers", "loose_packets", "marks_and_numbers", "submission_date", "assessment_date", "examination_date", "leo_date", "submission_time", "assessment_time", "examination_time", "leo_time", "leo_no", "leo_dt", "brc_realisation_date", "created_at"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = ['cin_dt','hawb_dt','mawb_dt','rbi_waiver_no_and_dt','sb_date','submission_date','assessment_date','examination_date','leo_date','leo_dt'];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
     //   print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='ship_bill_summary';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_ship_bill_summary($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_ship_bill_summary($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_ship_bill_summary($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_ship_bill_summary($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_ship_bill_summary($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_ship_bill_summary($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
               $duplicate_query = "SELECT COUNT(*) AS num_rows 
                     FROM ship_bill_summary  where sb_no  ='{$detail['sb_no']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_ship_bill_summary($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_ship_bill_summary($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_ship_bill_summary($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}  	
/******************************************************************************End ship_bill_summary****************************************************************************************/

    
public function duties_and_additional_details5555(){    
        /******************************************************************Start duties_and_additional_details***************************************************************************************/
 // Load the primary database
    
$db_primary = $this->load->database('second', TRUE);
    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'  ORDER BY lucrative_users_id ASC";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();
     $batchSize1 = 20; // Number of records to process in each batch
                $totalRecords = count($admin_users_primary);
        for ($offset1 = 0; $offset1 < $totalRecords; $offset1 += $batchSize1) 
        {
            $batch = array_slice($admin_users_primary, $offset1, $batchSize1);
          //  print_r($batch);exit;
    // Process each admin user
    foreach ($batch as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);    

        $query_duties_and_additional_details = "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details 
        LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
        LEFT JOIN invoice_and_valuation_details
        ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id Where bill_of_entry_summary.iec_no Like '%$iec_no'";

        $statement_duties_and_additional_details = $db_primary->query(
            $query_duties_and_additional_details
        );
        $iecwise_duties_and_additional_details = [];
        $result_duties_and_additional_details = $statement_duties_and_additional_details->result_array();
  
   //  echo  count($result_duties_and_additional_details);exit;
   
    $batchSize2 = 9000; // Number of records to process in each batch
                $totalRecords2 = count($result_duties_and_additional_details);
        for ($offset2 = 0; $offset2 < $totalRecords2; $offset2 += $batchSize2) 
        {
            $batch2 = array_slice($result_duties_and_additional_details, $offset2, $batchSize2);
    // Process each admin user
   print_r($batch2);exit;
        foreach ($batch2 as $str_duties_and_additional_details) {
                     
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
             $sql_users = "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date FROM duties_and_additional_details 
        LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
        LEFT JOIN invoice_and_valuation_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id
       where  CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no)='".$reference_code."' 
       AND bill_of_entry_summary.be_no='".$be_no."' AND bill_of_entry_summary.be_date='".$be_date."'";
       
                $iecwise1_users = $db_secondary->query($sql_users);
                $iecwise_data1_users = array();
                
  if ($iecwise1_users->num_rows > 0) {  
      continue;
      
  }
            $c= $reference_code."-".$be_no."-".$be_date;
    
         
 /***********************************************************************/
            $sql_insert_duties_and_additional_details =
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

$copy_insert_duties_and_additional_details = $db_secondary->query(
                $sql_insert_duties_and_additional_details
            );

           
            
           }
        }
        }
        
        }
        /******************************************************************Start ship_bill_summary***************************************************************************************/
  $db_secondary->close();
        $db_primary->close(); 
        }
    
    
public function equipment_details(){    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              echo  $jobbing_details_query = "SELECT equipment_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM equipment_details 
  LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id  where ship_bill_summary.iec Like '%$iec%'";

                $this->processUser_dynamic_equipment_details($user,$jobbing_details_query);
            }
        }

        // Close the main database connection
        $this->db->close(); 
  
  
  
  
    
   
        /******************************************************************Start equipment_details***************************************************************************************/
    }
private function processUser_dynamic_equipment_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["equip_id","sbs_id","container","seal","date","s_no","created_at"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = ['date'];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
     //   print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='equipment_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_equipment_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_equipment_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_equipment_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_equipment_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_equipment_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_equipment_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);

$duplicate_query = "SELECT COUNT(*) AS num_rows  FROM equipment_details 
                LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id where equipment_details.sbs_id='{$detail['sbs_id']}'  AND equipment_details.container ='{$detail['container']}' ";
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_equipment_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_equipment_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_equipment_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}




/******************************************************************Start item_manufacturer_details***************************************************************************************/
public function item_manufacturer_details() {
    
    
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              $query_item_manufacturer_details = "SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code,
              item_manufacturer_details.*, item_details.invoice_id, invoice_summary.invoice_id, invoice_summary.sbs_id, ship_bill_summary.iec, 
              ship_bill_summary.sbs_id FROM item_manufacturer_details LEFT JOIN item_details ON item_details.item_id=item_manufacturer_details.item_id 
              LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON 
              ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE ship_bill_summary.iec LIKE '%$iec'";


                $this->processUser_dynamic_item_manufacturer_details($user,$query_item_manufacturer_details);
            }
        }

        // Close the main database connection
        $this->db->close(); 
  

}
private function processUser_dynamic_item_manufacturer_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["item_manufact_id", "item_id", "inv_sno", "item_sno", "manufact_cd", "source_state", "trans_cy", "address"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
                        $date_fields = ['date'];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
     //   print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='item_manufacturer_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_item_manufacturer_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_item_manufacturer_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_item_manufacturer_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_item_manufacturer_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_item_manufacturer_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_item_manufacturer_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);


	$duplicate_query = "SELECT COUNT(*) AS num_rows   FROM duties_and_additional_details LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id 
            where item_manufact_id='{$detail['item_manufact_id']}'  AND  item_id='{$detail['item_id']}'  AND inv_sno='{$detail['inv_sno']}'  AND item_sno='{$detail['item_sno']}' ";
       
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_item_manufacturer_details($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_item_manufacturer_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_item_manufacturer_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}












/******************************************************************End item_manufacturer_details***************************************************************************************/
 
/****************************************************************** invoice_summery***************************************************************************************/

public function invoice_summery(){    
      
       // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
  
        $query_invoice_summery ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary 
        ON invoice_summary.sbs_id=ship_bill_summary.sbs_id  Where ship_bill_summary.iec Like '%$iec'";


                $this->processUser_dynamic_invoice_summery($user,$query_invoice_summery);
            }
        }

        // Close the main database connection
        $this->db->close(); 
      

}  
private function processUser_dynamic_invoice_summery($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                        // Define your array of valid column names
                   $valid_columns = ["invoice_id", "sbs_id", "s_no_inv", "inv_no", "inv_date", "inv_no_date", "po_no_date", "loc_no_date", "contract_no_date", "ad_code_inv", "invterm", "exporters_name_and_address", "buyers_name_and_address", "third_party_name_and_address", "buyers_aeo_status", "invoice_value", "invoice_value_currency", "fob_value_inv", "fob_value_currency", "freight_val", "freight_currency", "insurance_val", "insurance_currency", "discount_val", "discount_val_currency", "commison", "comission_currency", "deduct", "deduct_currency", "p_c_val", "p_c_val_currency", "exchange_rate", "created_at"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = [];
                         
                        if (!empty($numeric_fields)) {
                            foreach ($numeric_fields as $field) {
                                if (!empty($query_details[$field])) {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
                                        // Handle non-numeric or invalid values
                                        $query_details[$field] = 0; // Set to a default value or handle accordingly
                                    }
                                } else {
                                    // Handle empty values
                                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                                }
                            }
                        }
                          // Define your date fields
        
                        $date_fields = ['inv_date','inv_no_date','po_no_date','loc_no_date','contract_no_date','exchange_rate'];
                       if (!empty($date_fields)) {
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) {
                            if (!empty($query_details[$field])) {
                                // Ensure the date value is not empty and then format it
                                $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                            } else {
                                // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
                                $query_details[$field] = '1970-01-01';
                            }
                        }

                       }
            // Filter out unwanted fields from the detail array
            return array_intersect_key($query_details, array_flip($valid_columns));
        }, $query_details);
     //   print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='invoice_summary';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_invoice_summery($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_invoice_summery($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_invoice_summery($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
           $a= $this->isDuplicateEntry_dynamic_invoice_summery($detail, $users_id);
           echo $a;
            if ($a==='0') { echo "entered";
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_invoice_summery($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_invoice_summery($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
$duplicate_query = "SELECT COUNT(*) AS num_rows   FROM invoice_summary 
    JOIN ship_bill_summary ON invoice_summary.sbs_id=ship_bill_summary.sbs_id  where  invoice_summary.invoice_id='{$detail['invoice_id']}' AND invoice_summary.inv_no='{$detail['inv_no']}' AND invoice_summary.inv_date='{$detail['inv_date']}'";


        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        // Close secondary database connection
        $db_secondary->close();  //echo $result['num_rows'];exit;

        // Check if there are duplicate rows based on the query result
        return $result['num_rows'] > 0;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_invoice_summery($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}
private function excludeUnwantedFields_dynamic_invoice_summery($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
private function insertLicenceDetailsBatch_dynamic_invoice_summery($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}

/******************************************************************End invoice_summery***************************************************************************************/


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
     
              "value does not exists";
     
         } else {
     
             "value exists";
     
         }
         return $index;
    }
public function sb_file_status(){    
        /******************************************************************Start sb_file_status***************************************************************************************/
            // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);
         $query_sb_file_status = "SELECT * FROM sb_file_status Where user_iec_no Like '%$iec_no'";
        $statement_sb_file_status = $db_primary->query($query_sb_file_status);
        $iecwise_sb_file_status = [];
        $result_sb_file_status = $statement_sb_file_status->result_array();
       // print_r($result_sb_file_status);exit;

        foreach ($result_sb_file_status as $str_sb_file_status) {
        
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
                   "Duplicate";"============";continue;
             }
             
 /***********************************************************************/
             $sql_insert_sb_file_status =
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
            $copy_insert_sb_file_status = $db_secondary->query(
                $sql_insert_sb_file_status
            );
        }
        /******************************************************************Start sb_file_status***************************************************************************************/
   $db_secondary->close();
    }
    $db_primary->close();  
        
   
    }
   public function inArray_third_party_details($array, $value){
     
       /* Initialize index -1 initially. */
     
        $index = -1;
     
        foreach($array as $val){
    // print_r($val);
             /* If value is found, set index to 1. */
     $c= $val['reference_code'];
                       

             if($c == $value){
     
                    $index = 1;
     
               } 
        }
     
        if($index == -1){
     
              "value does not exists";
     
         } else {
     
             "value exists";
     
         }
         return $index;
    }
    
    
    
 public function third_party_details(){    
        /******************************************************************Start sb_file_status***************************************************************************************/
            // Load the primary database
    $db_primary = $this->load->database('second', TRUE);

    // Fetch all admin users from the primary database
    $sql_admin_users = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  and lucrative_users_id='199'";
    $admin_users_primary = $db_primary->query($sql_admin_users)->result_array();

    // Process each admin user
    foreach ($admin_users_primary as $user) {
        $iec_no = $user["iec_no"];
        $lucrative_users_id = $user["lucrative_users_id"];

        // Establish connection to the corresponding user's secondary database
        $db_secondary = $this->database_connection($lucrative_users_id);
         $query_sb_file_status = "SELECT  CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, 
       n1.sb_no, n1.iec,n1.sb_date, n1.iec_br, n1.inv_sno, item_details.item_sno, n1.iec_tpd, n1.exporter_name, 
       third_party_details.address, third_party_details.gstn_id_type FROM third_party_details 
      JOIN third_party_details ON third_party_details.item_id = item_details.item_id
      JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id
      JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id  Where n1.iec Like '%$iec_no'
      ORDER BY n1.sbs_id DESC";
        $statement_sb_file_status = $db_primary->query($query_sb_file_status);
        $iecwise_sb_file_status = [];
        $result_sb_file_status = $statement_sb_file_status->result_array();
       // print_r($result_sb_file_status);exit;

        foreach ($result_sb_file_status as $str_sb_file_status) {
     
            
            //third_party_id, item_id, inv_sno, item_sno, iec_tpd, exporter_name, address, gstn_id_type
           // if (get_magic_quotes_gpc()) {
                $third_party_id = addslashes(
                    $str_sb_file_status["third_party_id"]
                );
                $item_id = addslashes(
                    $str_sb_file_status["item_id"]
                );
                $inv_sno = addslashes(
                    $str_sb_file_status["inv_sno"]
                );
                $item_sno = addslashes($str_sb_file_status["item_sno"]);
                
                $iec_tpd = addslashes($str_sb_file_status["iec_tpd"]);
                $exporter_name = addslashes($str_sb_file_status["exporter_name"]);
                $address = addslashes($str_sb_file_status["address"]);
                $gstn_id_type = addslashes($str_sb_file_status["gstn_id_type"]);
               
	 /********************checking dupliacte entries d2d***********************/
            $sql_users = "SELECT  CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, 
       n1.sb_no, n1.iec,n1.sb_date, n1.iec_br, n1.inv_sno, item_details.item_sno, n1.iec_tpd, n1.exporter_name, 
       third_party_details.address, third_party_details.gstn_id_type FROM third_party_details 
      JOIN third_party_details ON third_party_details.item_id = item_details.item_id
      JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id
 JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id Where CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no)='".$reference_code."'
ORDER BY n1.sbs_id DESC ";
         
                $iecwise1_users = $db_secondary->query($sql_users);
           
             if ($iecwise1_users->num_rows > 0) {
                   "Duplicate";"============";continue;
             }
             
 /***********************************************************************/
             $sql_insert_sb_file_status =
                "INSERT INTO `third_party_details` (`third_party_id`, `item_id`, `inv_sno`, `item_sno`, `iec_tpd`, `exporter_name`, `address`, `gstn_id_type`) 
VALUES('" .$third_party_id .
                "','" .
                $item_id .
                "','" .
                $inv_sno .
                "','" .
                $item_sno .
                "','" .
                $iec_tpd.
                "','" .
                $exporter_name .
                "','" .
                $address .
                "','" .
                $gstn_id_type .
                "',')";
            $copy_insert_sb_file_status = $db1_sb_file_status->query(
                $sql_insert_sb_file_status
            );
        }
        /******************************************************************Start sb_file_status***************************************************************************************/
   $db_secondary->close();
    }
       $db_primary->close();  
        
   
    }  
   
   
public function database_connection($id){
        $hostname = "localhost";
        $username = "root1";
        $password = "[b~BiWQ!l9BH";
       echo $db_name1 = "lucrativeesystem_D2D_S".$id;
       
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);
        // Check connection
        if ($Db1->connect_error)
        {
            die("Connection failed: " . $Db1->connect_error);
        } 
        else 
        {
             "Connected successfully";
        }
        return $Db1;
    }
    
    public function database_connection_master(){    
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";

         $db_name1 = "lucrativeesystem_D2D_master";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);
        // Check connection
        if ($Db1->connect_error) {
            die("Connection failed: " . $Db1->connect_error);
        } else {
             "Connected successfully";
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
            $sql = "CREATE DATABASE $db_name";
            
            if ($conn->query($sql) === TRUE) {
               "Database created successfully";
           
                
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

   public function register_iec_user_sheet_wise_database($user_id)
   {
        $post = $this->input->post();
        $result = $user_id;

        if ($result) {
            $db_name = 'sheet_wise_database_'.$result;
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
            $sql = "CREATE DATABASE $db_name";
            
            if ($conn->query($sql) === TRUE) {
               "Database created successfully";
           
                
                $db2=$this->database_connection($result);
                $db2->query("use ".$db_name. "");
            /*****************************************Create Table bill_container_details***************************************************************/
               $db2->query("CREATE TABLE `bill_container_details` (
                              `be_no` text NOT NULL,
                              `be_date` text NOT NULL,
                              `iec_br` text NOT NULL,
                              `sno` text NOT NULL,
                              `lcl_fcl` text NOT NULL,
                              `truck` text NOT NULL,
                              `seal` text NOT NULL,
                              `container_number` text NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
             
    /*****************************************Create Table bill_licence_details***************************************************************/
               $db2->query("CREATE TABLE `bill_licence_details`(
                                  `reference_code` text NOT NULL,
                                  `be_no` text NOT NULL,
                                  `be_date` text NOT NULL,
                                  `iec_br` text NOT NULL,
                                  `invsno` text NOT NULL,
                                  `itemsn` text NOT NULL,
                                  `lic_slno` text NOT NULL,
                                  `lic_no` text NOT NULL,
                                  `lic_date` text NOT NULL,
                                  `code` text NOT NULL,
                                  `port` text NOT NULL,
                                  `debit_value` text NOT NULL,
                                  `qty` text NOT NULL,
                                  `uqc_lc_d` text NOT NULL,
                                  `debit_duty` text NOT NULL,
                                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
           /*****************************************Create Table bill_of_entry***************************************************************/
                           $db2->query("CREATE TABLE `bill_of_entry`(
                                `reference_code` text NOT NULL,
                                `invoice_title` text NOT NULL,
                                `port` text NOT NULL,
                                `port_code` text NOT NULL,
                                `be_no` text NOT NULL,
                                `be_date` text NOT NULL,
                                `be_type` text NOT NULL,
                                `iec_br` text NOT NULL,
                                `iec_no` text NOT NULL,
                                `br` text NOT NULL,
                                `gstin_type` text NOT NULL,
                                `cb_code` text NOT NULL,
                                `nos` text NOT NULL,
                                `pkg1` text NOT NULL,
                                `item` text NOT NULL,
                                `g_wt_kgs` text NOT NULL,
                                `cont` text NOT NULL,
                                `be_status` text NOT NULL,
                                `mode` text NOT NULL,
                                `def_be` text NOT NULL,
                                `kacha` text NOT NULL,
                                `sec_48` text NOT NULL,
                                `reimp1` text NOT NULL,
                                `adv_be` text NOT NULL,
                                `assess` text NOT NULL,
                                `exam` text NOT NULL,
                                `hss` text NOT NULL,
                                `first_check` text NOT NULL,
                                `prov_final2` text NOT NULL,
                                `country_of_origin` text NOT NULL,
                                `country_of_consignment` text NOT NULL,
                                `port_of_loading` text NOT NULL,
                                `port_of_shipment` text NOT NULL,
                                `importer_name_and_address` text NOT NULL,
                                `ad_code1` text NOT NULL,
                                `cb_name` text NOT NULL,
                                `aeo1` text NOT NULL,
                                `ucr` text NOT NULL,
                                `bcd` text NOT NULL,
                                `acd` text NOT NULL,
                                `sws` text NOT NULL,
                                `nccd` text NOT NULL,
                                `add` text NOT NULL,
                                `cvd` text NOT NULL,
                                `igst` text NOT NULL,
                                `g_cess` text NOT NULL,
                                `sg` text NOT NULL,
                                `saed` text NOT NULL,
                                `gsia` text NOT NULL,
                                `tta` text NOT NULL,
                                `health` text NOT NULL,
                                `total_duty1` text NOT NULL,
                                `int` text NOT NULL,
                                `pnlty` text NOT NULL,
                                `fine` text NOT NULL,
                                `tot_ass_val` text NOT NULL,
                                `tot_amount` text NOT NULL,
                                `wbe_no` text NOT NULL,
                                `wbe_date` text NOT NULL,
                                `wbe_site` text NOT NULL,
                                `wh_code` text NOT NULL,
                                `submission_date` text NOT NULL,
                                `assessment_date` text NOT NULL,
                                `examination_date` text NOT NULL,
                                `ooc_date` text NOT NULL,
                                `submission_time` text NOT NULL,
                                `assessment_time` text NOT NULL,
                                `examination_time` text NOT NULL,
                                `ooc_time` text NOT NULL,
                                `submission_exchange_rate` text NOT NULL,
                                `assessment_exchange_rate` text NOT NULL,
                                `ooc_no` text NOT NULL,
                                `ooc_date_` text NOT NULL,
                                `examination_exchange_rate` text NOT NULL,
                                `ooc_exchange_rate` text NOT NULL,
                                `s_no` text NOT NULL,
                                `invoice_no` text NOT NULL,
                                `invoice_date` text NOT NULL,
                                `purchase_order_no` text NOT NULL,
                                `purchase_order_date` text NOT NULL,
                                `lc_no` text NOT NULL,
                                `lc_date` text NOT NULL,
                                `contract_no` text NOT NULL,
                                `contract_date` text NOT NULL,
                                `buyer_s_name_and_address` text NOT NULL,
                                `seller_s_name_and_address` text NOT NULL,
                                `supplier_name_and_address` text NOT NULL,
                                `third_party_name_and_address` text NOT NULL,
                                `aeo2` text NOT NULL,
                                `ad_code2` text NOT NULL,
                                `inv_value` text NOT NULL,
                                `freight` text NOT NULL,
                                `freight_cur` text NOT NULL,
                                `insurance` text NOT NULL,
                                `hss1` text NOT NULL,
                                `loading` text NOT NULL,
                                `commn` text NOT NULL,
                                `pay_terms` text NOT NULL,
                                `valuation_method` text NOT NULL,
                                `reltd` text NOT NULL,
                                `svb_ch` text NOT NULL,
                                `svb_no` text NOT NULL,
                                `date` text NOT NULL,
                                `loa` text NOT NULL,
                                `cur` text NOT NULL,
                                `term` text NOT NULL,
                                `c_and_b` text NOT NULL,
                                `coc` text NOT NULL,
                                `cop` text NOT NULL,
                                `hnd_chg` text NOT NULL,
                                `g_and_s` text NOT NULL,
                                `doc_ch` text NOT NULL,
                                `coo1` text NOT NULL,
                                `r_and_lf` text NOT NULL,
                                `oth_cost` text NOT NULL,
                                `ld_uld` text NOT NULL,
                                `ws` text NOT NULL,
                                `otc` text NOT NULL,
                                `misc_charge` text NOT NULL,
                                `ass_value` text NOT NULL,
                                `s_no2` text NOT NULL,
                                `cth` text NOT NULL,
                                `description` text NOT NULL,
                                `unit_price` text NOT NULL,
                                `quantity` text NOT NULL,
                                `uqc` text NOT NULL,
                                `amount` text NOT NULL,
                                `invsno` text NOT NULL,
                                `itemsn` text NOT NULL,
                                `cth_item_detail` text NOT NULL,
                                `ceth` text NOT NULL,
                                `item_description` text NOT NULL,
                                `fs` text NOT NULL,
                                `pq` text NOT NULL,
                                `dc` text NOT NULL,
                                `wc` text NOT NULL,
                                `aq` text NOT NULL,
                                `upi` text NOT NULL,
                                `coo2` text NOT NULL,
                                `c_qty` text NOT NULL,
                                `c_uqc` text NOT NULL,
                                `s_qty` text NOT NULL,
                                `s_uqc` text NOT NULL,
                                `sch` text NOT NULL,
                                `stdn_pr` text NOT NULL,
                                `rsp` text NOT NULL,
                                `reimp2` text NOT NULL,
                                `prov` text NOT NULL,
                                `end_use` text NOT NULL,
                                `prodn` text NOT NULL,
                                `cntrl` text NOT NULL,
                                `qualfr` text NOT NULL,
                                `contnt` text NOT NULL,
                                `stmnt` text NOT NULL,
                                `sup_docs` text NOT NULL,
                                `assess_value` text NOT NULL,
                                `total_duty2` text NOT NULL,
                                `bcd_notn_no` text NOT NULL,
                                `bcd_notn_sno` text NOT NULL,
                                `bcd_rate` text NOT NULL,
                                `bcd_amount` text NOT NULL,
                                `bcd_duty_fg` text NOT NULL,
                                `acd_notn_no` text NOT NULL,
                                `acd_notn_sno` text NOT NULL,
                                `acd_rate` text NOT NULL,
                                `acd_amount` text NOT NULL,
                                `acd_duty_fg` text NOT NULL,
                                `sws_notn_no` text NOT NULL,
                                `sws_notn_sno` text NOT NULL,
                                `sws_rate` text NOT NULL,
                                `sws_amount` text NOT NULL,
                                `sws_duty_fg` text NOT NULL,
                                `sad_notn_no` text NOT NULL,
                                `sad_notn_sno` text NOT NULL,
                                `sad_rate` text NOT NULL,
                                `sad_amount` text NOT NULL,
                                `sad_duty_fg` text NOT NULL,
                                `igst_notn_no` text NOT NULL,
                                `igst_notn_sno` text NOT NULL,
                                `igst_rate` text NOT NULL,
                                `igst_amount` text NOT NULL,
                                `igst_duty_fg` text NOT NULL,
                                `g_cess_notn_no` text NOT NULL,
                                `g_cess_notn_sno` text NOT NULL,
                                `g_cess_rate` text NOT NULL,
                                `g_cess_amount` text NOT NULL,
                                `g_cess_duty_fg` text NOT NULL,
                                `add_notn_no` text NOT NULL,
                                `add_notn_sno` text NOT NULL,
                                `add_rate` text NOT NULL,
                                `add_amount` text NOT NULL,
                                `add_duty_fg` text NOT NULL,
                                `cvd_notn_no` text NOT NULL,
                                `cvd_notn_sno` text NOT NULL,
                                `cvd_rate` text NOT NULL,
                                `cvd_amount` text NOT NULL,
                                `cvd_duty_fg` text NOT NULL,
                                `sg_notn_no` text NOT NULL,
                                `sg_notn_sno` text NOT NULL,
                                `sg_rate` text NOT NULL,
                                `sg_amount` text NOT NULL,
                                `sg_duty_fg` text NOT NULL,
                                `t_value_notn_no` text NOT NULL,
                                `t_value_notn_sno` text NOT NULL,
                                `t_value_rate` text NOT NULL,
                                `t_value_amount` text NOT NULL,
                                `t_value_duty_fg` text NOT NULL,
                                `sp_excd_notn_no` text NOT NULL,
                                `sp_excd_notn_sno` text NOT NULL,
                                `sp_excd_rate` text NOT NULL,
                                `sp_excd_amount` text NOT NULL,
                                `sp_excd_duty_fg` text NOT NULL,
                                `chcess_notn_no` text NOT NULL,
                                `chcess_notn_sno` text NOT NULL,
                                `chcess_rate` text NOT NULL,
                                `chcess_amount` text NOT NULL,
                                `chcess_duty_fg` text NOT NULL,
                                `tta_notn_no` text NOT NULL,
                                `tta_notn_sno` text NOT NULL,
                                `tta_rate` text NOT NULL,
                                `tta_amount` text NOT NULL,
                                `tta_duty_fg` text NOT NULL,
                                `cess_notn_no` text NOT NULL,
                                `cess_notn_sno` text NOT NULL,
                                `cess_rate` text NOT NULL,
                                `cess_amount` text NOT NULL,
                                `cess_duty_fg` text NOT NULL,
                                `caidc_cvd_edc_notn_no` text NOT NULL,
                                `caidc_cvd_edc_notn_sno` text NOT NULL,
                                `caidc_cvd_edc_rate` text NOT NULL,
                                `caidc_cvd_edc_amount` text NOT NULL,
                                `caidc_cvd_edc_duty_fg` text NOT NULL,
                                `eaidc_cvd_hec_notn_no` text NOT NULL,
                                `eaidc_cvd_hec_notn_sno` text NOT NULL,
                                `eaidc_cvd_hec_rate` text NOT NULL,
                                `eaidc_cvd_hec_amount` text NOT NULL,
                                `eaidc_cvd_hec_duty_fg` text NOT NULL,
                                `cus_edc_notn_no` text NOT NULL,
                                `cus_edc_notn_sno` text NOT NULL,
                                `cus_edc_rate` text NOT NULL,
                                `cus_edc_amount` text NOT NULL,
                                `cus_edc_duty_fg` text NOT NULL,
                                `cus_hec_notn_no` text NOT NULL,
                                `cus_hec_notn_sno` text NOT NULL,
                                `cus_hec_rate` text NOT NULL,
                                `cus_hec_amount` text NOT NULL,
                                `cus_hec_duty_fg` text NOT NULL,
                                `ncd_notn_no` text NOT NULL,
                                `ncd_notn_sno` text NOT NULL,
                                `ncd_rate` text NOT NULL,
                                `ncd_amount` text NOT NULL,
                                `ncd_duty_fg` text NOT NULL,
                                `aggr_notn_no` text NOT NULL,
                                `aggr_notn_sno` text NOT NULL,
                                `aggr_rate` text NOT NULL,
                                `aggr_amount` text NOT NULL,
                                `aggr_duty_fg` text NOT NULL,
                                `invsno_add_details` text NOT NULL,
                                `itmsno_add_details` text NOT NULL,
                                `refno` text NOT NULL,
                                `refdt` text NOT NULL,
                                `prtcd_svb_d` text NOT NULL,
                                `lab` text NOT NULL,
                                `pf` text NOT NULL,
                                `load_date` text NOT NULL,
                                `pf_` text NOT NULL,
                                `beno` text NOT NULL,
                                `bedate` text NOT NULL,
                                `prtcd` text NOT NULL,
                                `unitprice` text NOT NULL,
                                `currency_code` text NOT NULL,
                                `frt` text NOT NULL,
                                `ins` text NOT NULL,
                                `duty` text NOT NULL,
                                `sb_no` text NOT NULL,
                                `sb_dt` text NOT NULL,
                                `portcd` text NOT NULL,
                                `sinv` text NOT NULL,
                                `sitemn` text NOT NULL,
                                `type` text NOT NULL,
                                `manufact_cd` text NOT NULL,
                                `source_cy` text NOT NULL,
                                `trans_cy` text NOT NULL,
                                `address` text NOT NULL,
                                `accessory_item_details` text NOT NULL,
                                `notno` text NOT NULL,
                                `slno` text NOT NULL,
                                `inw_date` text NOT NULL,
                                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                              )ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
           
             
                /*****************************************Create Table bill_payment_details***************************************************************/
               $db2->query("CREATE TABLE `bill_payment_details` (
                          `be_no` text NOT NULL,
                          `be_date` text NOT NULL,
                          `iec_br` text NOT NULL,
                          `sr_no` text NOT NULL,
                          `challan_no` text NOT NULL,
                          `paid_on` text NOT NULL,
                          `amount` text NOT NULL,
                          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        /********************************************************************************************************/  
        
       /*****************************************Create Table bill_summary***************************************************************/
               $db2->query("CREATE TABLE `bill_summary` (
                              `invoice_title` text NOT NULL,
                              `port` text NOT NULL,
                              `port_code` text NOT NULL,
                              `be_no` text NOT NULL,
                              `be_date` text NOT NULL,
                              `be_type` text NOT NULL,
                              `iec_br` text NOT NULL,
                              `iec_no` text NOT NULL,
                              `br` text NOT NULL,
                              `gstin_type` text NOT NULL,
                              `cb_code` text NOT NULL,
                              `nos` text NOT NULL,
                              `pkg` text NOT NULL,
                              `item` text NOT NULL,
                              `g_wt_kgs` text NOT NULL,
                              `cont` text NOT NULL,
                              `be_status` text NOT NULL,
                              `mode` text NOT NULL,
                              `def_be` text NOT NULL,
                              `kacha` text NOT NULL,
                              `sec_48` text NOT NULL,
                              `reimp` text NOT NULL,
                              `adv_be` text NOT NULL,
                              `assess` text NOT NULL,
                              `exam` text NOT NULL,
                              `hss` text NOT NULL,
                              `first_check` text NOT NULL,
                              `prov_final` text NOT NULL,
                              `country_of_origin` text NOT NULL,
                              `country_of_consignment` text NOT NULL,
                              `port_of_loading` text NOT NULL,
                              `port_of_shipment` text NOT NULL,
                              `importer_name_and_address` text NOT NULL,
                              `ad_code` text NOT NULL,
                              `cb_name` text NOT NULL,
                              `aeo` text NOT NULL,
                              `ucr` text NOT NULL,
                              `bcd` text NOT NULL,
                              `acd` text NOT NULL,
                              `sws` text NOT NULL,
                              `nccd` text NOT NULL,
                              `add` text NOT NULL,
                              `cvd` text NOT NULL,
                              `igst` text NOT NULL,
                              `g_cess` text NOT NULL,
                              `sg` text NOT NULL,
                              `saed` text NOT NULL,
                              `gsia` text NOT NULL,
                              `tta` text NOT NULL,
                              `health` text NOT NULL,
                              `total_duty` text NOT NULL,
                              `int` text NOT NULL,
                              `pnlty` text NOT NULL,
                              `fine` text NOT NULL,
                              `tot_ass_val` text NOT NULL,
                              `tot_amount` text NOT NULL,
                              `wbe_no` text NOT NULL,
                              `wbe_date` text NOT NULL,
                              `wbe_site` text NOT NULL,
                              `wh_code` text NOT NULL,
                              `submission_date` text NOT NULL,
                              `assessment_date` text NOT NULL,
                              `examination_date` text NOT NULL,
                              `ooc_date` text NOT NULL,
                              `submission_time` text NOT NULL,
                              `assessment_time` text NOT NULL,
                              `examination_time` text NOT NULL,
                              `ooc_time` text NOT NULL,
                              `submission_exchange_rate` text NOT NULL,
                              `assessment_exchange_rate` text NOT NULL,
                              `ooc_no` text NOT NULL,
                              `ooc_date_` text NOT NULL,
                              `examination_exchange_rate` text NOT NULL,
                              `ooc_exchange_rate` text NOT NULL,
                              `inw_date` text NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
                            
                            
                        $db2->query("CREATE TABLE `bill_of_entry` (
                          
                                `reference_code` text NOT NULL,
                                `invoice_title` text NOT NULL,
                                `port` text NOT NULL,
                                `port_code` text NOT NULL,
                                `be_no` text NOT NULL,
                                `be_date` text NOT NULL,
                                `be_type` text NOT NULL,
                                `iec_br` text NOT NULL,
                                `iec_no` text NOT NULL,
                                `br` text NOT NULL,
                                `gstin_type` text NOT NULL,
                                `cb_code` text NOT NULL,
                                `nos` text NOT NULL,
                                `pkg1` text NOT NULL,
                                `item` text NOT NULL,
                                `g_wt_kgs` text NOT NULL,
                                `cont` text NOT NULL,
                                `be_status` text NOT NULL,
                                `mode` text NOT NULL,
                                `def_be` text NOT NULL,
                                `kacha` text NOT NULL,
                                `sec_48` text NOT NULL,
                                `reimp1` text NOT NULL,
                                `adv_be` text NOT NULL,
                                `assess` text NOT NULL,
                                `exam` text NOT NULL,
                                `hss` text NOT NULL,
                                `first_check` text NOT NULL,
                                `prov_final2` text NOT NULL,
                                `country_of_origin` text NOT NULL,
                                `country_of_consignment` text NOT NULL,
                                `port_of_loading` text NOT NULL,
                                `port_of_shipment` text NOT NULL,
                                `importer_name_and_address` text NOT NULL,
                                `ad_code1` text NOT NULL,
                                `cb_name` text NOT NULL,
                                `aeo1` text NOT NULL,
                                `ucr` text NOT NULL,
                                `bcd` text NOT NULL,
                                `acd` text NOT NULL,
                                `sws` text NOT NULL,
                                `nccd` text NOT NULL,
                                `add` text NOT NULL,
                                `cvd` text NOT NULL,
                                `igst` text NOT NULL,
                                `g_cess` text NOT NULL,
                                `sg` text NOT NULL,
                                `saed` text NOT NULL,
                                `gsia` text NOT NULL,
                                `tta` text NOT NULL,
                                `health` text NOT NULL,
                                `total_duty1` text NOT NULL,
                                `int` text NOT NULL,
                                `pnlty` text NOT NULL,
                                `fine` text NOT NULL,
                                `tot_ass_val` text NOT NULL,
                                `tot_amount` text NOT NULL,
                                `wbe_no` text NOT NULL,
                                `wbe_date` text NOT NULL,
                                `wbe_site` text NOT NULL,
                                `wh_code` text NOT NULL,
                                `submission_date` text NOT NULL,
                                `assessment_date` text NOT NULL,
                                `examination_date` text NOT NULL,
                                `ooc_date` text NOT NULL,
                                `submission_time` text NOT NULL,
                                `assessment_time` text NOT NULL,
                                `examination_time` text NOT NULL,
                                `ooc_time` text NOT NULL,
                                `submission_exchange_rate` text NOT NULL,
                                `assessment_exchange_rate` text NOT NULL,
                                `ooc_no` text NOT NULL,
                                `ooc_date_` text NOT NULL,
                                `examination_exchange_rate` text NOT NULL,
                                `ooc_exchange_rate` text NOT NULL,
                                `s_no` text NOT NULL,
                                `invoice_no` text NOT NULL,
                                `invoice_date` text NOT NULL,
                                `purchase_order_no` text NOT NULL,
                                `purchase_order_date` text NOT NULL,
                                `lc_no` text NOT NULL,
                                `lc_date` text NOT NULL,
                                `contract_no` text NOT NULL,
                                `contract_date` text NOT NULL,
                                `buyer_s_name_and_address` text NOT NULL,
                                `seller_s_name_and_address` text NOT NULL,
                                `supplier_name_and_address` text NOT NULL,
                                `third_party_name_and_address` text NOT NULL,
                                `aeo2` text NOT NULL,
                                `ad_code2` text NOT NULL,
                                `inv_value` text NOT NULL,
                                `freight` text NOT NULL,
                                `freight_cur` text NOT NULL,
                                `insurance` text NOT NULL,
                                `hss1` text NOT NULL,
                                `loading` text NOT NULL,
                                `commn` text NOT NULL,
                                `pay_terms` text NOT NULL,
                                `valuation_method` text NOT NULL,
                                `reltd` text NOT NULL,
                                `svb_ch` text NOT NULL,
                                `svb_no` text NOT NULL,
                                `date` text NOT NULL,
                                `loa` text NOT NULL,
                                `cur` text NOT NULL,
                                `term` text NOT NULL,
                                `c_and_b` text NOT NULL,
                                `coc` text NOT NULL,
                                `cop` text NOT NULL,
                                `hnd_chg` text NOT NULL,
                                `g_and_s` text NOT NULL,
                                `doc_ch` text NOT NULL,
                                `coo1` text NOT NULL,
                                `r_and_lf` text NOT NULL,
                                `oth_cost` text NOT NULL,
                                `ld_uld` text NOT NULL,
                                `ws` text NOT NULL,
                                `otc` text NOT NULL,
                                `misc_charge` text NOT NULL,
                                `ass_value` text NOT NULL,
                                `s_no2` text NOT NULL,
                                `cth` text NOT NULL,
                                `description` text NOT NULL,
                                `unit_price` text NOT NULL,
                                `quantity` text NOT NULL,
                                `uqc` text NOT NULL,
                                `amount` text NOT NULL,
                                `invsno` text NOT NULL,
                                `itemsn` text NOT NULL,
                                `cth_item_detail` text NOT NULL,
                                `ceth` text NOT NULL,
                                `item_description` text NOT NULL,
                                `fs` text NOT NULL,
                                `pq` text NOT NULL,
                                `dc` text NOT NULL,
                                `wc` text NOT NULL,
                                `aq` text NOT NULL,
                                `upi` text NOT NULL,
                                `coo2` text NOT NULL,
                                `c_qty` text NOT NULL,
                                `c_uqc` text NOT NULL,
                                `s_qty` text NOT NULL,
                                `s_uqc` text NOT NULL,
                                `sch` text NOT NULL,
                                `stdn_pr` text NOT NULL,
                                `rsp` text NOT NULL,
                                `reimp2` text NOT NULL,
                                `prov` text NOT NULL,
                                `end_use` text NOT NULL,
                                `prodn` text NOT NULL,
                                `cntrl` text NOT NULL,
                                `qualfr` text NOT NULL,
                                `contnt` text NOT NULL,
                                `stmnt` text NOT NULL,
                                `sup_docs` text NOT NULL,
                                `assess_value` text NOT NULL,
                                `total_duty2` text NOT NULL,
                                `bcd_notn_no` text NOT NULL,
                                `bcd_notn_sno` text NOT NULL,
                                `bcd_rate` text NOT NULL,
                                `bcd_amount` text NOT NULL,
                                `bcd_duty_fg` text NOT NULL,
                                `acd_notn_no` text NOT NULL,
                                `acd_notn_sno` text NOT NULL,
                                `acd_rate` text NOT NULL,
                                `acd_amount` text NOT NULL,
                                `acd_duty_fg` text NOT NULL,
                                `sws_notn_no` text NOT NULL,
                                `sws_notn_sno` text NOT NULL,
                                `sws_rate` text NOT NULL,
                                `sws_amount` text NOT NULL,
                                `sws_duty_fg` text NOT NULL,
                                `sad_notn_no` text NOT NULL,
                                `sad_notn_sno` text NOT NULL,
                                `sad_rate` text NOT NULL,
                                `sad_amount` text NOT NULL,
                                `sad_duty_fg` text NOT NULL,
                                `igst_notn_no` text NOT NULL,
                                `igst_notn_sno` text NOT NULL,
                                `igst_rate` text NOT NULL,
                                `igst_amount` text NOT NULL,
                                `igst_duty_fg` text NOT NULL,
                                `g_cess_notn_no` text NOT NULL,
                                `g_cess_notn_sno` text NOT NULL,
                                `g_cess_rate` text NOT NULL,
                                `g_cess_amount` text NOT NULL,
                                `g_cess_duty_fg` text NOT NULL,
                                `add_notn_no` text NOT NULL,
                                `add_notn_sno` text NOT NULL,
                                `add_rate` text NOT NULL,
                                `add_amount` text NOT NULL,
                                `add_duty_fg` text NOT NULL,
                                `cvd_notn_no` text NOT NULL,
                                `cvd_notn_sno` text NOT NULL,
                                `cvd_rate` text NOT NULL,
                                `cvd_amount` text NOT NULL,
                                `cvd_duty_fg` text NOT NULL,
                                `sg_notn_no` text NOT NULL,
                                `sg_notn_sno` text NOT NULL,
                                `sg_rate` text NOT NULL,
                                `sg_amount` text NOT NULL,
                                `sg_duty_fg` text NOT NULL,
                                `t_value_notn_no` text NOT NULL,
                                `t_value_notn_sno` text NOT NULL,
                                `t_value_rate` text NOT NULL,
                                `t_value_amount` text NOT NULL,
                                `t_value_duty_fg` text NOT NULL,
                                `sp_excd_notn_no` text NOT NULL,
                                `sp_excd_notn_sno` text NOT NULL,
                                `sp_excd_rate` text NOT NULL,
                                `sp_excd_amount` text NOT NULL,
                                `sp_excd_duty_fg` text NOT NULL,
                                `chcess_notn_no` text NOT NULL,
                                `chcess_notn_sno` text NOT NULL,
                                `chcess_rate` text NOT NULL,
                                `chcess_amount` text NOT NULL,
                                `chcess_duty_fg` text NOT NULL,
                                `tta_notn_no` text NOT NULL,
                                `tta_notn_sno` text NOT NULL,
                                `tta_rate` text NOT NULL,
                                `tta_amount` text NOT NULL,
                                `tta_duty_fg` text NOT NULL,
                                `cess_notn_no` text NOT NULL,
                                `cess_notn_sno` text NOT NULL,
                                `cess_rate` text NOT NULL,
                                `cess_amount` text NOT NULL,
                                `cess_duty_fg` text NOT NULL,
                                `caidc_cvd_edc_notn_no` text NOT NULL,
                                `caidc_cvd_edc_notn_sno` text NOT NULL,
                                `caidc_cvd_edc_rate` text NOT NULL,
                                `caidc_cvd_edc_amount` text NOT NULL,
                                `caidc_cvd_edc_duty_fg` text NOT NULL,
                                `eaidc_cvd_hec_notn_no` text NOT NULL,
                                `eaidc_cvd_hec_notn_sno` text NOT NULL,
                                `eaidc_cvd_hec_rate` text NOT NULL,
                                `eaidc_cvd_hec_amount` text NOT NULL,
                                `eaidc_cvd_hec_duty_fg` text NOT NULL,
                                `cus_edc_notn_no` text NOT NULL,
                                `cus_edc_notn_sno` text NOT NULL,
                                `cus_edc_rate` text NOT NULL,
                                `cus_edc_amount` text NOT NULL,
                                `cus_edc_duty_fg` text NOT NULL,
                                `cus_hec_notn_no` text NOT NULL,
                                `cus_hec_notn_sno` text NOT NULL,
                                `cus_hec_rate` text NOT NULL,
                                `cus_hec_amount` text NOT NULL,
                                `cus_hec_duty_fg` text NOT NULL,
                                `ncd_notn_no` text NOT NULL,
                                `ncd_notn_sno` text NOT NULL,
                                `ncd_rate` text NOT NULL,
                                `ncd_amount` text NOT NULL,
                                `ncd_duty_fg` text NOT NULL,
                                `aggr_notn_no` text NOT NULL,
                                `aggr_notn_sno` text NOT NULL,
                                `aggr_rate` text NOT NULL,
                                `aggr_amount` text NOT NULL,
                                `aggr_duty_fg` text NOT NULL,
                                `invsno_add_details` text NOT NULL,
                                `itmsno_add_details` text NOT NULL,
                                `refno` text NOT NULL,
                                `refdt` text NOT NULL,
                                `prtcd_svb_d` text NOT NULL,
                                `lab` text NOT NULL,
                                `pf` text NOT NULL,
                                `load_date` text NOT NULL,
                                `pf_` text NOT NULL,
                                `beno` text NOT NULL,
                                `bedate` text NOT NULL,
                                `prtcd` text NOT NULL,
                                `unitprice` text NOT NULL,
                                `currency_code` text NOT NULL,
                                `frt` text NOT NULL,
                                `ins` text NOT NULL,
                                `duty` text NOT NULL,
                                `sb_no` text NOT NULL,
                                `sb_dt` text NOT NULL,
                                `portcd` text NOT NULL,
                                `sinv` text NOT NULL,
                                `sitemn` text NOT NULL,
                                `type` text NOT NULL,
                                `manufact_cd` text NOT NULL,
                                `source_cy` text NOT NULL,
                                `trans_cy` text NOT NULL,
                                `address` text NOT NULL,
                                `accessory_item_details` text NOT NULL,
                                `notno` text NOT NULL,
                                `slno` text NOT NULL,
                                `inw_date` text NOT NULL,
                                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
             /********************************************************************************************************/  
        
               /*****************************************Create Table Bond_Details***************************************************************/
               $db2->query("CREATE TABLE `Bond_Details` (
                          `be_no` text NOT NULL,
                          `be_date` text NOT NULL,
                          `iec_br` text NOT NULL,
                          `bond_no` text NOT NULL,
                          `port` text NOT NULL,
                          `bond_cd` text NOT NULL,
                          `debt_amt` text NOT NULL,
                          `bg_amt` text NOT NULL,
                          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/
        
        
               /*****************************************Create Table challan_details***************************************************************/
               $db2->query("CREATE TABLE `challan_details` (
                              `sb_no` text NOT NULL,
                              `sb_date` text NOT NULL,
                              `iec_br` text NOT NULL,
                              `sr_no` text NOT NULL,
                              `challan_no` text NOT NULL,
                              `paymt_dt` text NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
            /*****************************************Create Table dfia_licence_details***************************************************************/
               $db2->query("CREATE TABLE `dfia_licence_details` (
                              `reference_code` text NOT NULL,
                              `sb_no` text NOT NULL,
                              `sb_date` text NOT NULL,
                              `iec_br` text NOT NULL,
                              `inv_s_no` text NOT NULL,
                              `item_s_no_` text NOT NULL,
                              `hs_cd` text NOT NULL,
                              `description` text NOT NULL,
                              `licence_no` text NOT NULL,
                              `descn_of_export_item` text NOT NULL,
                              `exp_s_no` text NOT NULL,
                              `expqty` text NOT NULL,
                              `uqc_aa` text NOT NULL,
                              `fob_value` text NOT NULL,
                              `sion` text NOT NULL,
                              `descn_of_import_item` text NOT NULL,
                              `imp_s_no` text NOT NULL,
                              `impqt` text NOT NULL,
                              `uqc_` text NOT NULL,
                              `indig_imp` text NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  

            /*****************************************Create Table drawback_details***************************************************************/
        
            $db2->query("CREATE TABLE `drawback_details` (
                              `reference_code` text NOT NULL,
                              `sb_no` text NOT NULL,
                              `sb_date` text NOT NULL,
                              `iec_br` text NOT NULL,
                              `inv_sno` text NOT NULL,
                              `item_sno` text NOT NULL,
                              `hs_cd` text NOT NULL,
                              `description` text NOT NULL,
                              `dbk_sno` text NOT NULL,
                              `qty_wt` text NOT NULL,
                              `value` text NOT NULL,
                              `dbk_amt` text NOT NULL,
                              `stalev` text NOT NULL,
                              `cenlev` text NOT NULL,
                              `rosctl_amt` text NOT NULL,
                              `rate` text NOT NULL,
                              `rebate` text NOT NULL,
                              `amount` text NOT NULL,
                              `dbk_rosl` text NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
                    /*****************************************Create Table equipment_details***************************************************************/
        
                        $db2->query("CREATE TABLE `equipment_details` (
                                      `sb_no` text NOT NULL,
                                      `sb_date` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `container` text NOT NULL,
                                      `seal` text NOT NULL,
                                      `date` text NOT NULL,
                                      `s_no` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
            /*****************************************Create Table item_manufacture***************************************************************/
        
                        $db2->query("CREATE TABLE `item_manufacture` (
                                  `reference_code` text NOT NULL,
                                  `sb_no` text NOT NULL,
                                  `sb_date` text NOT NULL,
                                  `iec_br` text NOT NULL,
                                  `inv_sno` text NOT NULL,
                                  `item_sno` text NOT NULL,
                                  `manufact_cd` text NOT NULL,
                                  `source_state` text NOT NULL,
                                  `trans_cy` text NOT NULL,
                                  `address` text NOT NULL,
                                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
             /*****************************************Create Table jobbing_details***************************************************************/
        
                        $db2->query("CREATE TABLE `jobbing_details` (
                                      `sb_no` text NOT NULL,
                                      `sb_date` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `be_no` text NOT NULL,
                                      `be_date` text NOT NULL,
                                      `port_code_j` text NOT NULL,
                                      `descn_of_imported_goods` text NOT NULL,
                                      `qty_imp` text NOT NULL,
                                      `qty_used` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
           /*****************************************Create Table Manifest_Details***************************************************************/
        
                        $db2->query("CREATE TABLE `Manifest_Details` (
                                      `be_no` text NOT NULL,
                                      `be_date` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `ooc_date_` text NOT NULL,
                                      `igm_no` text NOT NULL,
                                      `inw_date` text NOT NULL,
                                      `gigmno` text NOT NULL,
                                      `gigmdt` text NOT NULL,
                                      `mawb_no` text NOT NULL,
                                      `mawb_date` text NOT NULL,
                                      `hawb_no` text NOT NULL,
                                      `hawb_date` text NOT NULL,
                                      `pkg5` text NOT NULL,
                                      `gw` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
         /*****************************************Create Table Rodtep_Details***************************************************************/
        
                        $db2->query("CREATE TABLE `Rodtep_Details` (
                                      `reference_code` text NOT NULL,
                                      `sb_no` text NOT NULL,
                                      `sb_date` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `inv_sno` text NOT NULL,
                                      `item_sno` text NOT NULL,
                                      `quantity` text NOT NULL,
                                      `uqc` text NOT NULL,
                                      `no_of_units` text NOT NULL,
                                      `value` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/ 
        
        
          /*****************************************Create Table shb_summary***************************************************************/
        
                        $db2->query("CREATE TABLE `shb_summary` (
                                      `invoice_title` text NOT NULL,
                                      `port_code` text NOT NULL,
                                      `sb_no` text NOT NULL,
                                      `sb_date` text NOT NULL,
                                      `iec` text NOT NULL,
                                      `br` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `gstin` text NOT NULL,
                                      `type` text NOT NULL,
                                      `cb_code` text NOT NULL,
                                      `inv_nos` text NOT NULL,
                                      `item_no` text NOT NULL,
                                      `cont_no` text NOT NULL,
                                      `address` text NOT NULL,
                                      `pkg8` text NOT NULL,
                                      `g_wt_unit` text NOT NULL,
                                      `g_wt_value` text NOT NULL,
                                      `mode` text NOT NULL,
                                      `assess` text NOT NULL,
                                      `exmn` text NOT NULL,
                                      `jobbing` text NOT NULL,
                                      `meis` text NOT NULL,
                                      `dbk` text NOT NULL,
                                      `rodtp` text NOT NULL,
                                      `deec_dfia` text NOT NULL,
                                      `dfrc` text NOT NULL,
                                      `reexp` text NOT NULL,
                                      `lut` text NOT NULL,
                                      `port_of_loading` text NOT NULL,
                                      `country_of_finaldestination` text NOT NULL,
                                      `state_of_origin` text NOT NULL,
                                      `port_of_finaldestination` text NOT NULL,
                                      `port_of_discharge` text NOT NULL,
                                      `country_of_discharge` text NOT NULL,
                                      `exporter_name_and_address` text NOT NULL,
                                      `consignee_name_and_address` text NOT NULL,
                                      `declarant_type` text NOT NULL,
                                      `ad_code` text NOT NULL,
                                      `gstin_type_` text NOT NULL,
                                      `rbi_waiver_no_and_dt` text NOT NULL,
                                      `forex_bank_account_no` text NOT NULL,
                                      `cb_name` text NOT NULL,
                                      `dbk_bank_account_no` text NOT NULL,
                                      `aeo` text NOT NULL,
                                      `ifsc_code` text NOT NULL,
                                      `freight` text NOT NULL,
                                      `insurance` text NOT NULL,
                                      `discount` text NOT NULL,
                                      `com` text NOT NULL,
                                      `deduction` text NOT NULL,
                                      `p_c` text NOT NULL,
                                      `duty` text NOT NULL,
                                      `cess` text NOT NULL,
                                      `dbk_claim` text NOT NULL,
                                      `igst_amt` text NOT NULL,
                                      `cess_amt` text NOT NULL,
                                      `igst_value` text NOT NULL,
                                      `rodtep_amt` text NOT NULL,
                                      `rosctl_amt` text NOT NULL,
                                      `mawb_no` text NOT NULL,
                                      `mawb_dt` text NOT NULL,
                                      `hawb_no` text NOT NULL,
                                      `hawb_dt` text NOT NULL,
                                      `noc` text NOT NULL,
                                      `cin_no` text NOT NULL,
                                      `cin_dt` text NOT NULL,
                                      `cin_site_id` text NOT NULL,
                                      `seal_type` text NOT NULL,
                                      `nature_of_cargo` text NOT NULL,
                                      `no_of_packets` text NOT NULL,
                                      `no_of_containers` text NOT NULL,
                                      `loose_packets` text NOT NULL,
                                      `marks_and_numbers` text NOT NULL,
                                      `submission_date` text NOT NULL,
                                      `assessment_date` text NOT NULL,
                                      `examination_date` text NOT NULL,
                                      `leo_date` text NOT NULL,
                                      `submission_time` text NOT NULL,
                                      `assessment_time` text NOT NULL,
                                      `examination_time` text NOT NULL,
                                      `leo_time` text NOT NULL,
                                      `leo_no` text NOT NULL,
                                      `leo_dt` text NOT NULL,
                                      `brc_realisation_date` text NOT NULL,
                                      `fob_value_sum` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
          /*****************************************Create Table shipping_bill_summary***************************************************************/
        
                        $db2->query("CREATE TABLE `shipping_bill_summary` (
                                      `reference_code` text NOT NULL,
                                      `invoice_title` text NOT NULL,
                                      `port_code` text NOT NULL,
                                      `sb_no` text NOT NULL,
                                      `sb_date` text NOT NULL,
                                      `iec` text NOT NULL,
                                      `br` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `gstin` text NOT NULL,
                                      `type` text NOT NULL,
                                      `cb_code` text NOT NULL,
                                      `inv_nos` text NOT NULL,
                                      `item_no` text NOT NULL,
                                      `cont_no` text NOT NULL,
                                      `address` text NOT NULL,
                                      `pkg9` text NOT NULL,
                                      `g_wt_unit` text NOT NULL,
                                      `g_wt_value` text NOT NULL,
                                      `mode` text NOT NULL,
                                      `assess` text NOT NULL,
                                      `exmn` text NOT NULL,
                                      `jobbing` text NOT NULL,
                                      `meis` text NOT NULL,
                                      `dbk` text NOT NULL,
                                      `rodtp` text NOT NULL,
                                      `deec_dfia` text NOT NULL,
                                      `dfrc` text NOT NULL,
                                      `reexp` text NOT NULL,
                                      `lut` text NOT NULL,
                                      `port_of_loading` text NOT NULL,
                                      `country_of_finaldestination` text NOT NULL,
                                      `state_of_origin` text NOT NULL,
                                      `port_of_finaldestination` text NOT NULL,
                                      `port_of_discharge` text NOT NULL,
                                      `country_of_discharge` text NOT NULL,
                                      `exporter_name_and_address` text NOT NULL,
                                      `consignee_name_and_address` text NOT NULL,
                                      `declarant_type` text NOT NULL,
                                      `ad_code` text NOT NULL,
                                      `gstin_type_` text NOT NULL,
                                      `rbi_waiver_no_and_dt` text NOT NULL,
                                      `forex_bank_account_no` text NOT NULL,
                                      `cb_name` text NOT NULL,
                                      `dbk_bank_account_no` text NOT NULL,
                                      `aeo` text NOT NULL,
                                      `ifsc_code` text NOT NULL,
                                      `fob_value_sum` text NOT NULL,
                                      `freight` text NOT NULL,
                                      `insurance` text NOT NULL,
                                      `discount` text NOT NULL,
                                      `com` text NOT NULL,
                                      `deduction` text NOT NULL,
                                      `p_c` text NOT NULL,
                                      `duty` text NOT NULL,
                                      `cess` text NOT NULL,
                                      `dbk_claim` text NOT NULL,
                                      `igst_amt` text NOT NULL,
                                      `cess_amt` text NOT NULL,
                                      `igst_value` text NOT NULL,
                                      `rodtep_amt` text NOT NULL,
                                      `rosctl_amt` text NOT NULL,
                                      `mawb_no` text NOT NULL,
                                      `mawb_dt` text NOT NULL,
                                      `hawb_no` text NOT NULL,
                                      `hawb_dt` text NOT NULL,
                                      `noc` text NOT NULL,
                                      `cin_no` text NOT NULL,
                                      `cin_dt` text NOT NULL,
                                      `cin_site_id` text NOT NULL,
                                      `seal_type` text NOT NULL,
                                      `nature_of_cargo` text NOT NULL,
                                      `no_of_packets` text NOT NULL,
                                      `no_of_containers` text NOT NULL,
                                      `loose_packets` text NOT NULL,
                                      `marks_and_numbers` text NOT NULL,
                                      `submission_date` text NOT NULL,
                                      `assessment_date` text NOT NULL,
                                      `examination_date` text NOT NULL,
                                      `leo_date` text NOT NULL,
                                      `submission_time` text NOT NULL,
                                      `assessment_time` text NOT NULL,
                                      `examination_time` text NOT NULL,
                                      `leo_time` text NOT NULL,
                                      `leo_no` text NOT NULL,
                                      `leo_dt` text NOT NULL,
                                      `brc_realisation_date` text NOT NULL,
                                      `s_no_inv` text NOT NULL,
                                      `inv_no` text NOT NULL,
                                      `inv_date` text NOT NULL,
                                      `inv_no_date` text NOT NULL,
                                      `po_no_date` text NOT NULL,
                                      `loc_no_date` text NOT NULL,
                                      `contract_no_date` text NOT NULL,
                                      `ad_code_inv` text NOT NULL,
                                      `invterm` text NOT NULL,
                                      `exporters_name_and_address` text NOT NULL,
                                      `buyers_name_and_address` text NOT NULL,
                                      `third_party_name_and_address` text NOT NULL,
                                      `buyers_aeo_status` text NOT NULL,
                                      `invoice_value` text NOT NULL,
                                      `invoice_value_currency` text NOT NULL,
                                      `fob_value_inv` text NOT NULL,
                                      `fob_value_currency` text NOT NULL,
                                      `freight_val` text NOT NULL,
                                      `freight_currency` text NOT NULL,
                                      `insurance_val` text NOT NULL,
                                      `insurance_currency` text NOT NULL,
                                      `discount_val` text NOT NULL,
                                      `discount_val_currency` text NOT NULL,
                                      `commison` text NOT NULL,
                                      `comission_currency` text NOT NULL,
                                      `deduct` text NOT NULL,
                                      `deduct_currency` text NOT NULL,
                                      `p_c_val` text NOT NULL,
                                      `p_c_val_currency` text NOT NULL,
                                      `exchange_rate` text NOT NULL,
                                      `invsn` text NOT NULL,
                                      `item_s_no` text NOT NULL,
                                      `hs_cd` text NOT NULL,
                                      `description` text NOT NULL,
                                      `quantity` text NOT NULL,
                                      `uqc` text NOT NULL,
                                      `rate` text NOT NULL,
                                      `value_f_c` text NOT NULL,
                                      `fob_inr` text NOT NULL,
                                      `pmv` text NOT NULL,
                                      `duty_amt` text NOT NULL,
                                      `cess_rt` text NOT NULL,
                                      `cesamt` text NOT NULL,
                                      `dbkclmd` text NOT NULL,
                                      `igststat` text NOT NULL,
                                      `igst_value_item` text NOT NULL,
                                      `igst_amount` text NOT NULL,
                                      `schcod` text NOT NULL,
                                      `scheme_description` text NOT NULL,
                                      `sqc_msr` text NOT NULL,
                                      `sqc_uqc` text NOT NULL,
                                      `state_of_origin_i` text NOT NULL,
                                      `district_of_origin` text NOT NULL,
                                      `pt_abroad` text NOT NULL,
                                      `comp_cess` text NOT NULL,
                                      `end_use` text NOT NULL,
                                      `fta_benefit_availed` text NOT NULL,
                                      `reward_benefit` text NOT NULL,
                                      `third_party_item` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
               /*****************************************Create Table third_party_details***************************************************************/
        
                        $db2->query("CREATE TABLE `third_party_details` (
                                      `reference_code` text NOT NULL,
                                      `sb_no` text NOT NULL,
                                      `sb_date` text NOT NULL,
                                      `iec_br` text NOT NULL,
                                      `inv_sno` text NOT NULL,
                                      `item_sno` text NOT NULL,
                                      `iec_tpd` text NOT NULL,
                                      `exporter_name` text NOT NULL,
                                      `address` text NOT NULL,
                                      `gstn_id_type` text NOT NULL,
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
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
         json_encode($data['list_woksheet']);*/

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
             $output;
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
                 $sql = "SHOW COLUMNS FROM " .$str;
                $query = $this->db->query($sql);
                $columns[] = $query->result_array();
                $data["columns"][] = $columns;
            }
        }
         json_encode($data);
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
    
    
       
   
public function item_details_master()
    {
        /******************************************************************Start item_details***************************************************************************************/
    echo    $idm1 = "SELECT COUNT(*) FROM item_details";exit;
        $statement_idm1 = $this->db->query($idm1);
        $result_idm1 = $statement_idm1->result_array();
        //print_r($result_idm1);
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

            //  $query_item_details = "SELECT item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id  LIMIT 9000";
            $statement_item_details = $this->db->query($query_item_details);
            $iecwise_item_details = [];
            $result_item_details = $statement_item_details->result_array();
            // print_r($result_item_details);exit;

            foreach ($result_item_details as $str_item_details) {
                $iec_item_details = $str_item_details["iec"];
                $sql_item_details = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_item_details'";
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
                     "Connected successfully";
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
                 $sql_insert_item_details =
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
       echo $q1 = "SELECT COUNT(*) FROM duties_and_additional_details";
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
                //  "Connected successfully";
            }
            //return $Db1;
            foreach (
                $result_duties_and_additional_details
                as $str_duties_and_additional_details
            ) {
                // $lastname;exit;
                /* $iec_duties_and_additional_details=$str_duties_and_additional_details['iec_no'];
        $sql_duties_and_additional_details = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec_duties_and_additional_details'";
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
                     $unit_price =
                        $str_duties_and_additional_details["unit_price"];
                     $quantity =
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

                 $sql_insert_duties_and_additional_details =
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
    // $bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary ";
  $bm1 = "SELECT COUNT(*) FROM bill_of_entry_summary";
        $statement1_bm1 = $this->db->query($bm1);
        $iecwise_data1_bm1 = [];
        $result1_bm1 = $statement1_bm1->result_array();
   //print_r($result1_bm1);
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
            $sql = "SELECT lucrative_users_id  FROM lucrative_users where role='admin' AND iec_no LIKE '%$iec1'";
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
           "Duplicate";"============";continue;
     }
 
   
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

          // $submission_date;
            $wbe_date = date("Y-m-d",strtotime($wbe_date));
              $submission_date = date("Y-m-d",strtotime($submission_date));
               $assessment_date = date("Y-m-d",strtotime($assessment_date));
               $examination_date = date("Y-m-d",strtotime($examination_date));
            $ooc_date = date("Y-m-d",strtotime($ooc_date));
            
            $sql_insert1 =
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
    
  public function aa_dfia_licence_details_old(){    
        // Load PostgearSQL database 
        $db_mysql = $this->load->database('second', TRUE);
         $sql = "SELECT lucrative_users_id  FROM lucrative_users where role='admin'";
            $iecwise = $this->db->query($sql);
            $iecwise_data1 = $iecwise->result_array();
        /******************************************************************Start aa_dfia_licence_details***************************************************************************************/
       foreach ($iecwise_data1 as $user) {
           
           $iec = $user["iec"];
           $user_id=$user["lucrative_users_id"];
      // Set maximum execution time to 60 seconds for this script
        $query ="SELECT aa_dfia_licence_details.*, ship_bill_summary.sbs_id,ship_bill_summary.sb_no, ship_bill_summary.iec,
        item_details.invoice_id,item_details.item_id FROM aa_dfia_licence_details 
        LEFT JOIN item_details ON aa_dfia_licence_details.item_id = item_details.item_id 
        LEFT JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id 
        LEFT JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id  where iec Like '%$iec'";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $iecwise_data = $statement->result_array();
     //echo count($result);exit;
      
            
             foreach ($iecwise_data as $str) {
            $db1 = $this->database_connection($users_id);
                $sb_no= addslashes($str["sb_no"]);
                $item_id = addslashes($str["item_id"]);
                $inv_s_no = addslashes($str["inv_s_no"]);
                $item_s_no_ = addslashes($str["item_s_no_"]);
                $licence_no = addslashes($str["licence_no"]);
                $descn_of_export_item = addslashes($str["descn_of_export_item"]);
                $exp_s_no = addslashes($str["exp_s_no"]);
                $expqty = addslashes($str["expqty"]);
                $uqc_aa = addslashes($str["uqc_aa"]);
                $fob_value = addslashes($str["fob_value"]);
                $sion = addslashes($str["sion"]);
                $descn_of_import_item = addslashes($str["descn_of_import_item"]);
                $imp_s_no = addslashes($str["imp_s_no"]);
                $impqt = addslashes($str["impqt"]);
                $uqc_ = addslashes($str["uqc_"]);
                $indig_imp = addslashes($str["indig_imp"]);
                $created_at = addslashes($str["created_at"]);
                
        /********************checking dupliacte entries d2d***********************/
        $sql_users = "SELECT aa_dfia_licence_details.*, ship_bill_summary.sbs_id,ship_bill_summary.sb_no, ship_bill_summary.iec,item_details.invoice_id,item_details.item_id FROM aa_dfia_licence_details 
        LEFT JOIN item_details ON aa_dfia_licence_details.item_id = item_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id 
        LEFT JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id 
        WHERE aa_dfia_licence_details.item_s_no_ = '$item_s_no_' AND aa_dfia_licence_details.inv_s_no = '$inv_s_no' AND ship_bill_summary.sb_no = '$sb_no'";
             $dup_aa_dfia_licence_details = $db1->query($sql_users);

      /*  while ($rowusers = $iecwise1_users->fetch_assoc()) {
            $iecwise_data1_users[] = $rowusers;
        }*/
      //   $iecwise1_users->num_rows;exit;
        //$c= $sb_no."-".$inv_s_no."-".$item_s_no_;
        //skip dupliacte entry     
        /*$a= $this->inArray_aa_dfia_licence_details($iecwise_data1_users,$c); // Output - value exists
        if ($a==1) {
          "Duplicate";"============";continue;
        }*/
        // $dup_aa_dfia_licence_details_select = $dup_aa_dfia_licence_details->result_array();
        
        if($dup_aa_dfia_licence_details->num_rows== 0 ) { 
        /**************************************************/
             $sql_insert =
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
        }
        /******************************************************************End aa_dfia_licence_details***************************************************************************************/
       
       }
        $db1->close();
        $this->db->close();
    }
  
    public function duties_and_additional_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
          $perPage = 50; // Adjust based on memory and performance testing
          $totalUsers = $this->db->query("SELECT COUNT(*)
          as total FROM lucrative_users WHERE role = 'admin'and lucrative_users_id='212'")->row()->total;
          $pages = ceil($totalUsers / $perPage);

           for ($page = 0; $page < $pages; $page++)
           {
            $offset = $page * $perPage;
             $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' and lucrative_users_id='212' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) 
            {
                $iec = $user['iec_no'];
             echo   $duties_and_additional_details_query = 
                "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date
                FROM duties_and_additional_details 
                LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
                LEFT JOIN invoice_and_valuation_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id Where bill_of_entry_summary.iec_no Like '%$iec'";
                $this->processUser_dynamic_duties_and_additional_details($user,$duties_and_additional_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}
private function processUser_dynamic_duties_and_additional_details($user,$query)
{
    $iec = $user["iec_no"];
    $users_id = $user["lucrative_users_id"];
    // Fetch licence details efficiently
    $query_details = $this->db->query($query)->result_array();
    //print_r(count($query_details)); die();
    if (!empty($query_details))
    {
        // Define the valid columns to be inserted into aa_dfia_licence_details
        // List of fields to process and escape
        $valid_columns = [
        'boe_id', 'invoice_id', 'duties_id', 's_no', 'cth', 'description', 'unit_price',
        'quantity', 'uqc', 'amount', 'invsno', 'itemsn', 'cth_item_detail', 'ceth', 'item_description', 'fs', 'pq',
        'dc', 'wc', 'aq', 'upi', 'coo', 'c_qty', 'c_uqc', 's_qty', 's_uqc', 'sch',
        'stdn_pr', 'rsp', 'reimp', 'prov', 'end_use',
        'prodn', 'cntrl', 'qualfr', 'contnt', 'stmnt', 'sup_docs', 'assess_value', 'total_duty', 'bcd_notn_no', 'bcd_notn_sno',
        'bcd_rate', 'bcd_amount', 'bcd_duty_fg', 'acd_notn_no', 'acd_notn_sno', 'acd_rate', 'acd_amount', 'acd_duty_fg', 'sws_notn_no', 'sws_notn_sno', 'sws_rate', 'sws_amount', 'sws_duty_fg',
        'sad_notn_no', 'sad_notn_sno', 'sad_rate', 'sad_amount', 'sad_duty_fg', 'igst_notn_no', 'igst_notn_sno', 'igst_rate',
        'igst_amount', 'igst_duty_fg', 'g_cess_notn_no', 'g_cess_notn_sno', 'g_cess_rate', 'g_cess_amount',
        'g_cess_duty_fg', 'add_notn_no', 'add_notn_sno', 'add_rate', 'add_amount',
        'add_duty_fg', 'cvd_notn_no','cvd_notn_sno','cvd_rate','cvd_amount','cvd_duty_fg','sg_notn_no','sg_notn_sno','sg_rate','sg_amount','sg_duty_fg','t_value_notn_no','t_value_notn_sno','t_value_rate','t_value_amount','t_value_duty_fg','sp_excd_notn_no','sp_excd_notn_sno','sp_excd_rate','sp_excd_amount','sp_excd_duty_fg','chcess_notn_no','chcess_notn_sno','chcess_rate','chcess_amount','chcess_duty_fg','tta_notn_no','tta_notn_sno','tta_rate','tta_amount','tta_duty_fg','cess_notn_no','cess_notn_sno','cess_rate','cess_amount','cess_duty_fg','caidc_cvd_edc_notn_no','caidc_cvd_edc_notn_sno','caidc_cvd_edc_rate','caidc_cvd_edc_amount','caidc_cvd_edc_duty_fg','eaidc_cvd_hec_notn_no','eaidc_cvd_hec_notn_sno','eaidc_cvd_hec_rate','eaidc_cvd_hec_amount','eaidc_cvd_hec_duty_fg','cus_edc_notn_no','cus_edc_notn_sno','cus_edc_rate','cus_edc_amount','cus_edc_duty_fg','cus_hec_notn_no','cus_hec_notn_sno','cus_hec_rate','cus_hec_amount','cus_hec_duty_fg','ncd_notn_no','ncd_notn_sno','ncd_rate','ncd_amount','ncd_duty_fg','aggr_notn_no','aggr_notn_sno','aggr_rate','aggr_amount','aggr_duty_fg','invsno_add_details','itmsno_add_details','refno','refdt','prtcd_svb_d','lab','pf','load_date','pf_','beno','bedate','prtcd','unitprice','currency_code','frt','ins','duty','sb_no','sb_dt','portcd','sinv','sitemn','type','manufact_cd','source_cy','trans_cy','address','accessory_item_details','notno','slno','created_at'
        ];

        // Define unwanted fields
        // Process and filter the query details to retain only valid columns
        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
        // Convert 'debt_amt' to a numeric value or default to zero
        // Validate and format numeric fields
        $numeric_fields = [
                                'boe_id', 'invoice_id', 'duties_id', 's_no', 'invsno', 'itemsn', 'upi', 'c_qty', 's_qty',
                                'assess_value', 'bcd_rate', 'bcd_amount', 'sws_rate', 'sws_amount', 'igst_rate', 'igst_amount', 'igst_duty_fg',
                                'g_cess_rate', 'g_cess_amount','g_cess_duty_fg','cvd_rate','caidc_cvd_edc_notn_sno','caidc_cvd_edc_rate','caidc_cvd_edc_amount','caidc_cvd_edc_duty_fg','cus_edc_rate','cus_hec_rate'
                            ];
                            
        foreach ($numeric_fields as $field) 
        {
            if (!empty($query_details[$field]))
            {
                // Check if the value is numeric
                if (is_numeric($query_details[$field]))
                {
                    // Convert the value to a float or decimal
                    $query_details[$field] = (float)$query_details[$field];
                } 
                else 
                {
                    // Handle non-numeric or invalid values
                    $query_details[$field] = 0; // Set to a default value or handle accordingly
                }
            } 
            else 
            {
                // Handle empty values
                $query_details[$field] = 0; // Set to a default value or handle accordingly
            }
        }
        
        // Define your date fields
        $date_fields = [
                            'load_date', 
                            'bedate', 
                            'created_at'
                        ];
                        
                        // Loop through each date field and format it
    foreach ($date_fields as $field)
    {
        if (!empty($query_details[$field]))
        {
            // Ensure the date value is not empty and then format it
            $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
        } 
        else
        {
            // If the date value is empty, set it to a default date or leave it as is (in this case, it's set to NULL)
            $query_details[$field] = '1970-01-01';
        }
    
    }


    // Filter out unwanted fields from the detail array
    return array_intersect_key($query_details, array_flip($valid_columns));
  }, $query_details);
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details))
        { 
               $unwanted_fields = [];
                     $table_name='duties_and_additional_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_duties_and_additional_details($valid_query_details,$users_id,$unwanted_fields);
                         if (!empty($filtered_query_details))
                         {   
                              echo "=====================";
                              echo $users_id;
                              $this->insertLicenceDetailsBatch_dynamic_duties_and_additional_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_duties_and_additional_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if (!$this->isDuplicateEntry_dynamic_duties_and_additional_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_duties_and_additional_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            // Optionally, throw the exception to propagate the error
             throw $e;
        }
    }

    return $filtered_details;
}
 private function isDuplicateEntry_dynamic_duties_and_additional_details($detail, $users_id)
{
    try 
    {
         
           $db_secondary = $this->load_secondary_database($users_id);
           $duplicate_query= 
           "SELECT COUNT(*) AS num_rows
           FROM duties_and_additional_details 
           LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
           LEFT JOIN invoice_and_valuation_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id where duties_and_additional_details.duties_id='{$detail['duties_id']}'";
           // Execute the duplicate query and fetch the result
           $result = $db_secondary->query($duplicate_query)->row_array();
           // Close secondary database connection
           $db_secondary->close();
           // Check if there are duplicate rows based on the query result
           return $result['num_rows'] > 0;
    } 
    catch (Exception $e)
    {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
  private function validateQueryDetailColumns_dynamic_duties_and_additional_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   


private function excludeUnwantedFields_dynamic_duties_and_additional_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}  

private function insertLicenceDetailsBatch_dynamic_duties_and_additional_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
} 



}
