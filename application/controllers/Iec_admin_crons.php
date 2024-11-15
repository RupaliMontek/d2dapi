<?php defined("BASEPATH") or exit("No direct script access allowed");
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Iec_admin_crons extends CI_Controller
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
       $this->db->close();
    }
    public function index()
    {
    }
 
    
    
public function lucrative_users()
{    
    
        /******************************************************************Start lucrative_users***************************************************************************************/

        $query_boe_delete_logs = "SELECT  * FROM lucrative_users";
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
       
       $user_id = $lucrative_users_id;
       $user = $this->register_iec_user($user_id);
       //$user_sheetwise = $this->register_iec_user_sheet_wise_database($user_id);
      }//
       
        /******************************************************************End lucrative_users***************************************************************************************/
    $db1->close();
        $this->db->close();
    }    
    
/**********************************************************************bill_of_entry_summary*************************************************************************************/    
public function bill_of_entry_summary()
{
    // Load secondary database
    $this->load->database('second');

    // Fetch admin users with paginated queries to handle large datasets
    $perPage = 50; // Adjust based on memory and performance testing

    // Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

    $totalUsers = $this->db->query("SELECT COUNT(*) AS total FROM lucrative_users WHERE role = 'admin'")->row()->total;
    $pages = ceil($totalUsers / $perPage); 

    for ($page = 0; $page < $pages; $page++) {
        $offset = $page * $perPage;
       echo $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
        $admin_users = $this->db->query($adminUsersQuery)->result_array();

        foreach ($admin_users as $user) {
            $iec = $user['iec_no'];
 
            // Construct the bill_of_entry_summary query with a date condition
     $bill_of_entry_summary_query = "SELECT * FROM bill_of_entry_summary WHERE iec_no LIKE '%$iec%' AND created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
        //  $bill_of_entry_summary_query = "SELECT * FROM bill_of_entry_summary WHERE iec_no LIKE '%$iec%'  AND created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";

            // Process the user's bill_of_entry_summary data
            $this->processUser_dynamic_bill_of_entry_summary($user, $bill_of_entry_summary_query);
        }
    }

    // Close the secondary database connection
    $this->db->close();
}


private function processUser_dynamic_bill_of_entry_summary($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
       // print_r($query_details);exit;
          if (!empty($query_details)) {
                    // Define the valid columns to be inserted into aa_dfia_licence_details
                    // List of fields to process and escape
                    $valid_columns = ['boe_id', 'boe_file_status_id', 'invoice_title', 'port', 'port_code', 'be_no', 'be_date','be_type', 'iec_br', 'iec_no', 'br', 'gstin_type', 'cb_code', 'ad_code', 'nos', 'pkg', 'item','g_wt_kgs','cont','be_status','mode','def_be','kacha','sec_48','reimp','adv_be','assess','exam','hss','first_check','prov_final','country_of_origin','country_of_consignment','port_of_loading','port_of_shipment','importer_name_and_address','cb_name','aeo','ucr','bcd','acd','sws','nccd','add','cvd','igst','g_cess','sg','saed','gsia','tta','health','total_duty','int','pnlty','fine','tot_ass_val','tot_amount','wbe_no','wbe_date','wbe_site','wh_code','submission_date','assessment_date','examination_date','ooc_date','submission_time','assessment_time','examination_time','ooc_time','submission_exchange_rate','assessment_exchange_rate','ooc_no','ooc_date_','created_at','examination_exchange_rate','ooc_exchange_rate'];
                    // Define unwanted fields
                    // Process and filter the query details to retain only valid columns
                    $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                    // Convert 'debt_amt' to a numeric value or default to zero
                    // Validate and format numeric fields
                    $numeric_fields =[
                                'boe_file_status_id', 'nos', 'pkg', 'item', 'g_wt_kgs', 'cont', 'cvd', 'igst', 'g_cess',
                                'sg', 'saed', 'gsia', 'tta', 'health', 'total_duty', 'pnlty', 'fine',
                                'tot_ass_val', 'tot_amount','add','nccd','int','acd'
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
                        $date_fields = ['be_date','wbe_date','submission_date','assessment_date','examination_date','ooc_date_','created_at'];
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
                        $table_name='bill_of_entry_summary';
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_of_entry_summary($valid_query_details,$users_id,$unwanted_fields);
                        if (!empty($filtered_query_details))
                        {   echo "=====================";echo $users_id;
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
            echo $a=$this->isDuplicateEntry_dynamic_bill_of_entry_summary($detail, $users_id);
            if ($this->isDuplicateEntry_dynamic_bill_of_entry_summary($detail, $users_id)) {echo $users_id;
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
            $duplicate_query = "SELECT COUNT(*) AS num_rows FROM bill_of_entry_summary where boe_id='{$detail['boe_id']}' and boe_file_status_id='{$detail['boe_file_status_id']}'";
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
            
              if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM bill_of_entry_summary 
                             WHERE boe_id = '{$detail['boe_id']}' 
                             AND boe_file_status_id = '{$detail['boe_file_status_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    } 
    catch (Exception $e)
    {
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
    foreach ($unwanted_fields as $field)
    {
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

/**********************************************************************END bill_of_entry_summary*************************************************************************************/    


/**********************************************************************boe_file_status*************************************************************************************/    

public function boe_file_status()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
          // Calculate the date 3 days ago
          $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
    
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        for ($page = 0; $page < $pages; $page++)
        {
            $offset = $page * $perPage;
            echo $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user)
            {
                $iec = $user['iec_no'];
               $boe_file_status_query = "SELECT * FROM boe_file_status WHERE user_iec_no LIKE '%$iec%' AND created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
                // $boe_file_status_query = "SELECT * FROM boe_file_status WHERE user_iec_no LIKE '%$iec%'";
                $this->processUser_dynamic_boe_file_status($user,$boe_file_status_query);
            }
        }
        // Close the main database connection
        $this->db->close();
}

private function processUser_dynamic_boe_file_status($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                    // Define the valid columns to be inserted into aa_dfia_licence_details
                    // List of fields to process and escape
                    $valid_columns = ['boe_file_status_id', 'pdf_filepath', 'pdf_filename', 'user_iec_no', 'lucrative_users_id', 'excel_filepath', 'excel_filename','pdf_to_excel_date', 'pdf_to_excel_status', 'file_iec_no', 'br', 'be_no', 'stage', 'status', 'remarks', 'created_at', 'is_deleted',];
                    // Define unwanted fields
                    // Process and filter the query details to retain only valid columns
                    $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                    // Convert 'debt_amt' to a numeric value or default to zero
                    // Validate and format numeric fields
                    $numeric_fields =[
                                'lucrative_users_id', 'bcd', 'acd', 'sws', 'nccd', 'add', 'cvd', 'igst', 'g_cess',
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
                            'pdf_to_excel_date', 
                            'created_at'];
                        
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
                     $table_name='boe_file_status';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_boe_file_status($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_boe_file_status($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}

private function filterDuplicateEntries_dynamic_boe_file_status($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_boe_file_status($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_boe_file_status($detail,$unwanted_fields);
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

private function isDuplicateEntry_dynamic_boe_file_status($detail, $users_id)
{
    try {
            $db_secondary = $this->load_secondary_database($users_id);
            $duplicate_query = "SELECT COUNT(*) AS num_rows FROM boe_file_status where boe_file_status_id='{$detail['boe_file_status_id']}'";
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
            if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
               // Check for existing duplicate entry and remove it
                    $remove_duplicate_query = "DELETE FROM boe_file_status WHERE boe_file_status_id = '{$detail['boe_file_status_id']}'";
                    $db_secondary->query($remove_duplicate_query);
            }
            
            
            
            
         
            
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
             return true;
    } 
    catch (Exception $e)
    {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}

private function validateQueryDetailColumns_dynamic_boe_file_status($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }
    
private function excludeUnwantedFields_dynamic_boe_file_status($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}


private function insertLicenceDetailsBatch_dynamic_boe_file_status($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
} 

/**********************************************************************END bill_of_entry_summary*************************************************************************************/    


/**********************************************************************boe_delete_logs*************************************************************************************/    

public function boe_delete_logs()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        
         // Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-10 days'));
    
         $totalUsers = $this->db->query("SELECT COUNT(*)
 as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user)
            {
                 $iec = $user['iec_no'];
                 $boe_delete_logs_query="SELECT *  FROM boe_delete_logs WHERE iec_no LIKE '%$iec%' AND deleted_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
                 $this->processUser_dynamic_boe_delete_logs($user,$boe_delete_logs_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_boe_delete_logs($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                      $valid_columns = ['boe_delete_logs_id', 'filename', 'be_no', 'be_date', 'iec_no', 'br', 'fullname','email', 'mobile', 'deleted_at'];
                      // Define unwanted fields
                      // Process and filter the query details to retain only valid columns
                      $valid_query_details = array_map(function ($query_details) use ($valid_columns)
                      {
                            // Convert 'debt_amt' to a numeric value or default to zero
                            // Validate and format numeric fields
                            $numeric_fields = [
                                'int', 'bcd', 'acd', 'sws', 'nccd', 'add', 'cvd', 'igst', 'g_cess',
                                'sg', 'saed', 'gsia', 'tta', 'health', 'total_duty', 'pnlty', 'fine',
                                'tot_ass_val', 'tot_amount'
                            ];
                            
                            foreach ($numeric_fields as $field)
                            {
                                if (!empty($query_details[$field])) 
                                {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
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
                        $date_fields = ['deleted_at','be_date'];
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
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='boe_delete_logs';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_boe_delete_logs($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_boe_delete_logs($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_boe_delete_logs($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_boe_delete_logs($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_boe_delete_logs($detail,$unwanted_fields);
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
 private function isDuplicateEntry_dynamic_boe_delete_logs($detail, $users_id)
{
    try {
            $db_secondary = $this->load_secondary_database($users_id);
            $duplicate_query = "SELECT COUNT(*) AS num_rows 
            FROM boe_delete_logs WHERE be_no = '{$detail['be_no']}' AND be_date = '{$detail['be_date']}'
                             AND boe_delete_logs_id = '{$detail['boe_delete_logs_id']}' AND iec_no = '{$detail['iec_no']}'";
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
            if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
            $delete_query = "DELETE FROM boe_delete_logs  WHERE be_no = '{$detail['be_no']}' AND be_date = '{$detail['be_date']}'
                             AND boe_delete_logs_id = '{$detail['boe_delete_logs_id']}' AND iec_no = '{$detail['iec_no']}'";

            $db_secondary->query($delete_query);
            }
            
            
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    } 
    catch (Exception $e) 
    {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
  private function validateQueryDetailColumns_dynamic_boe_delete_logs($detail,$valid_columns)
{
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_boe_delete_logs($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}


private function insertLicenceDetailsBatch_dynamic_boe_delete_logs($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
} 

/**********************************************************************End boe_delete_logs*************************************************************************************/    


/**********************************************************************End third_party_details*************************************************************************************/    


public function third_party_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*)
         as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                $iec = $user['iec_no'];
                $third_party_details_query = "SELECT CONCAT(ship_bill_summary.sb_no, '-', invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, 
ship_bill_summary.sb_no, ship_bill_summary.iec, ship_bill_summary.sb_date, 
ship_bill_summary.iec_br, n1.inv_sno, n1.item_sno, n1.iec_tpd, 
n1.exporter_name, n1.address, n1.gstn_id_type FROM third_party_details n1 
JOIN item_details ON n1.item_id = item_details.item_id 
JOIN invoice_summary ON invoice_summary.invoice_id = item_details.invoice_id 
JOIN ship_bill_summary ON invoice_summary.sbs_id = ship_bill_summary.sbs_id 
WHERE ship_bill_summary.iec LIKE '%$iec%' AND ship_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
                $this->processUser_dynamic_third_party_details($user,$third_party_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_third_party_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                    $valid_columns = [
                        'third_party_id', 'item_id', 'inv_sno', 'item_sno', 'iec_tpd', 'exporter_name', 'address',
                        'gstn_id_type'];

                      // Define unwanted fields
  
                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = ['third_party_id'];
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
                        
                        if(!empty($date_fields))
                        {
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
          // print_r($valid_query_details);exit;
        if (!empty($valid_query_details)) { 
               $unwanted_fields = [];
                     $table_name='third_party_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_third_party_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_third_party_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_third_party_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_third_party_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_third_party_details($detail,$unwanted_fields);
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
 private function isDuplicateEntry_dynamic_third_party_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
              $duplicate_query = 
               "SELECT COUNT(*) AS num_rows 
                FROM third_party_details 
                JOIN third_party_details ON third_party_details.item_id = item_details.item_id
                JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id
                JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id  where third_party_id='{$detail['third_party_id']}'
                ORDER BY n1.sbs_id DESC";
  
       if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM third_party_details 
                             WHERE third_party_id = '{$detail['third_party_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
  private function validateQueryDetailColumns_dynamic_third_party_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   


private function excludeUnwantedFields_dynamic_third_party_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}

private function insertLicenceDetailsBatch_dynamic_third_party_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
} 


/**********************************************************************End third_party_details*************************************************************************************/    


/**********************************************************************duties_and_additional_details*************************************************************************************/    

public function duties_and_additional_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
          $perPage = 50; // Adjust based on memory and performance testing
          $totalUsers = $this->db->query("SELECT COUNT(*)
          as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
          $pages = ceil($totalUsers / $perPage); 
          $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
 

           for ($page = 0; $page < $pages; $page++)
           {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id ASC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) 
            {
                $iec = $user['iec_no'];
                $duties_and_additional_details_query = 
                "SELECT CONCAT(bill_of_entry_summary.be_no,'-',invoice_and_valuation_details.s_no, '-', duties_and_additional_details.s_no) as reference_code,duties_and_additional_details.*,bill_of_entry_summary.iec_no ,bill_of_entry_summary.boe_id ,bill_of_entry_summary.be_no ,bill_of_entry_summary.be_date
                FROM duties_and_additional_details 
                LEFT JOIN bill_of_entry_summary ON duties_and_additional_details.boe_id = bill_of_entry_summary.boe_id
                LEFT JOIN invoice_and_valuation_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id 
                Where bill_of_entry_summary.iec_no Like '%$iec' AND duties_and_additional_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
                $this->processUser_dynamic_duties_and_additional_details($user,$duties_and_additional_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_duties_and_additional_details($user,$query,$batch_limit = 100)
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
        $numeric_fields = 
                        [
                            'examination_exchange_rate','boe_id', 'invoice_id', 'duties_id', 's_no', 'invsno', 'itemsn', 'upi', 'c_qty', 's_qty',
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
        
        if (!empty($valid_query_details))
        { 
          $chunks = array_chunk($valid_query_details, $batch_limit);
          // print_r($valid_query_details);exit;
        foreach ($chunks as $chunk)
        {
            $unwanted_fields = [];
            // Filter duplicate entries
            $filtered_query_details = $this->filterDuplicateEntries_dynamic_duties_and_additional_details($chunk, $users_id, $unwanted_fields);
            if (!empty($filtered_query_details)) 
            {
                echo "=====================";
                echo $users_id;
                echo "</br>";
                
                $table_name = 'duties_and_additional_details';
                $this->insertLicenceDetailsBatch_dynamic_duties_and_additional_details($filtered_query_details, $users_id, $table_name);
            }
        }
        }
      /*  if (!empty($valid_query_details))
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
        }*/
    }
}
private function filterDuplicateEntries_dynamic_duties_and_additional_details($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_duties_and_additional_details($detail, $users_id)) {
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
           LEFT JOIN invoice_and_valuation_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id 
           where duties_and_additional_details.duties_id='{$detail['duties_id']}'";
           // Execute the duplicate query and fetch the result
           $result = $db_secondary->query($duplicate_query)->row_array();
             if ($result['num_rows'] > 0) {  //echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
            $delete_query = "DELETE FROM duties_and_additional_details 
                             WHERE duties_id = '{$detail['duties_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
              // Check if there are duplicate rows based on the query result
            return true;
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

/**********************************************************************End duties_and_additional_details*************************************************************************************/    



/**********************************************************************cb_file_status*************************************************************************************/    

public function cb_file_status()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        // Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT * FROM cb_file_status where user_iec_no Like '%$iec' AND created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
                $this->processUser_dynamic_cb_file_status($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_cb_file_status($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['cb_file_status_id','pdf_filepath', 'pdf_filename', 'user_iec_no', 'lucrative_users_id', 'file_iec_no', 'cb_no','cb_date','stage','status','remarks','created_at','br','is_processed'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['lucrative_users_id',];
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
                        $date_fields = ['created_at','cb_date'];
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
                     $table_name='cb_file_status';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_cb_file_status($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_cb_file_status($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_cb_file_status($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_cb_file_status($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_cb_file_status($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_cb_file_status($detail, $users_id)
    {
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM cb_file_status where pdf_filepath ='{$detail['pdf_filepath']}'  and pdf_filename ='{$detail['pdf_filename']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
          if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM cb_file_status 
                             WHERE pdf_filepath = '{$detail['pdf_filepath']}' 
                             AND pdf_filename = '{$detail['pdf_filename']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_cb_file_status($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_cb_file_status($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_cb_file_status($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}   

/**********************************************************************End cb_file_status*************************************************************************************/    


/**********************************************************************courier_bill_summary*************************************************************************************/    

public function courier_bill_summary()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
 // Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user)
            {
                 $iec = $user['iec_no'];
                 $bill_of_entry_summary_query = "SELECT courier_bill_summary.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id 
                 FROM courier_bill_summary LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id   
                 Where cb_file_status.user_iec_no Like '%$iec'  AND courier_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
                 $this->processUser_dynamic_courier_bill_summary($user,$bill_of_entry_summary_query);
            }
        }
        // Close the main database connection
        $this->db->close();

}

private function processUser_dynamic_courier_bill_summary($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();  //var_dump($query_details);
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','cb_file_status_id', 'current_status_of_the_cbe', 'cbexiv_number', 'courier_registration_number', 'name_of_the_authorized_courier', 'address_of_authorized_courier','particulars_customs_house_agent_name','particulars_customs_house_agent_licence_no','particulars_customs_house_agent_address','import_export_code','import_export_branch_code','particulars_of_the_importer_name','particulars_of_the_importer_address','type_of_importer','in_case_of_other_importer','authorised_dealer_code_of_bank','class_code','cb_no','cb_date','category_of_boe','type_of_boe','kyc_document','kyc_id','state_code','high_sea_sale','ie_code_of_hss','ie_branch_code_of_hss','particulars_high_sea_seller_name','particulars_high_sea_seller_address','use_of_the_first_proviso_under_section_461customs_act1962','request_for_first_check','request_for_urgent_clear_ance_against_temporary_documentation','request_for_extension_of_time_limit_as_per_section_48customs_ac','reason_in_case_extension_of_time_limit_is_requested','country_of_origin','country_of_consignment','name_of_gateway_port','gateway_igm_number','date_of_entry_inwards_of_gateway_port','case_of_crn','number_of_invoices','total_freight','total_insurance','created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['cb_file_status_id','import_export_branch_code','authorised_dealer_code_of_bank','state_code','number_of_invoices','total_freight','total_insurance'];
                            foreach ($numeric_fields as $field)
                            {
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
                        $date_fields = ['created_at','date_of_arrival','date_of_entry_inwards_of_gateway_port'];
                        // Loop through each date field and format it
                        foreach ($date_fields as $field) 
                        {
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
        if (!empty($valid_query_details))
        { 
               $unwanted_fields = [];
                     $table_name='courier_bill_summary';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_summary($valid_query_details,$users_id,$unwanted_fields);
                  
                        if (!empty($filtered_query_details))
                        {   
                            echo "=====================";
                            echo $users_id;
                            $this->insertLicenceDetailsBatch_dynamic_courier_bill_summary($filtered_query_details, $users_id,$table_name);
                        }
        }
    }
}

private function filterDuplicateEntries_dynamic_courier_bill_summary($valid_query_details, $users_id,$unwanted_fields)
{
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_summary($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_summary($detail,$unwanted_fields);
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
     
private function isDuplicateEntry_dynamic_courier_bill_summary($detail, $users_id)
{
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM courier_bill_summary LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id   where cb_file_status.cb_file_status_id ='{$detail['cb_file_status_id']}'  and courier_bill_summary.courier_bill_of_entry_id  ='{$detail['courier_bill_of_entry_id']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
             if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_summary 
                             WHERE courier_bill_of_entry_id = '{$detail['courier_bill_of_entry_id']}'  AND cb_file_status_id ='{$detail['cb_file_status_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_summary($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_courier_bill_summary($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_summary($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
} 
/**********************************************************************courier_bill_summary*************************************************************************************/    


/**********************************************************************courier_bill_procurment_details*************************************************************************************/    

public function courier_bill_procurment_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
 // Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT courier_bill_procurment_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_procurment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_procurment_details.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id   
                Where cb_file_status.user_iec_no Like '%$iec'  and courier_bill_procurment_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
                $this->processUser_dynamic_courier_bill_procurment_details($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_courier_bill_procurment_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','procurment_details_id','payment_details_srno','procurement_under_3696_cus', 'procurement_certificate_number', 'date_of_issuance_of_certificate', 'location_code_of_the_cent_ral_excise_office_issuing_the_certifi', 'commissione_rate','division','range','import_under_multiple_in_voices','created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['igm_details_id '];
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
                        $date_fields = ['created_at'];
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
                     $table_name='courier_bill_procurment_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_procurment_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_courier_bill_procurment_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_procurment_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_procurment_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_procurment_details($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_courier_bill_procurment_details($detail, $users_id)
    {
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM courier_bill_procurment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_procurment_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id where procurment_details_id ='{$detail['procurment_details_id']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
             if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_procurment_details 
                             WHERE procurment_details_id = '{$detail['procurment_details_id']}' 
                             AND courier_bill_of_entry_id = '{$detail['courier_bill_of_entry_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
           
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_procurment_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   


private function excludeUnwantedFields_dynamic_courier_bill_procurment_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
  

private function insertLicenceDetailsBatch_dynamic_courier_bill_procurment_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}   


/**********************************************************************courier_bill_payment_details*************************************************************************************/    

public function courier_bill_payment_details()
{
        // Load secondary database
        $this->load->database('second');
        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
 // Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        for ($page = 0; $page < $pages; $page++)
        {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user)
            {
                $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT courier_bill_payment_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id 
                FROM courier_bill_payment_details LEFT JOIN courier_bill_summary ON 
                courier_bill_summary.courier_bill_of_entry_id=courier_bill_payment_details.courier_bill_of_entry_id LEFT JOIN 
                cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id 
                where cb_file_status.user_iec_no Like '%$iec' and courier_bill_payment_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
                $this->processUser_dynamic_courier_bill_payment_details($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
}

private function processUser_dynamic_courier_bill_payment_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','payment_details_id', 'payment_details_srno', 'tr6_challan_number', 'total_amount', 'challan_date', 'created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['courier_bill_of_entry_id','payment_details_srno','tr6_challan_number','total_amount'];
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
                        $date_fields = ['created_at','challan_date'];
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
        if (!empty($valid_query_details))
        { 
               $unwanted_fields = [];
                $table_name='courier_bill_payment_details';
                $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_payment_details($valid_query_details,$users_id,$unwanted_fields);
                if (!empty($filtered_query_details)) 
                {   
                    echo "=====================";echo $users_id;
                    $this->insertLicenceDetailsBatch_dynamic_courier_bill_payment_details($filtered_query_details, $users_id,$table_name);
                }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_payment_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_payment_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_payment_details($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_courier_bill_payment_details($detail, $users_id)
    {
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows from courier_bill_payment_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_payment_details.courier_bill_of_entry_id LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id  where payment_details_srno ='{$detail['payment_details_srno']}'  and courier_bill_payment_details.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
             if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_payment_details 
                             WHERE courier_bill_of_entry_id = '{$detail['courier_bill_of_entry_id']}' ";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_payment_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   


private function excludeUnwantedFields_dynamic_courier_bill_payment_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_payment_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    

/**********************************************************************end courier_bill_payment_details*************************************************************************************/    

/**********************************************************************courier_bill_notification_used_for_items*************************************************************************************/    

public function courier_bill_notification_used_for_items()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
// Calculate the date 3 days ago
    $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                 $bill_of_entry_summary_query = "SELECT courier_bill_notification_used_for_items.*,courier_bill_items_details.items_detail_id,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_notification_used_for_items LEFT JOIN courier_bill_items_details ON courier_bill_notification_used_for_items.items_detail_id=courier_bill_items_details.items_detail_id 
                 LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id 
                 LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id Where cb_file_status.user_iec_no Like '%$iec'   and courier_bill_notification_used_for_items.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
                 $this->processUser_dynamic_courier_bill_notification_used_for_items($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_courier_bill_notification_used_for_items($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['items_detail_id','igm_details_id','item_notification_id', 'notification_item_srno', 'notification_number', 'serial_number_of_notification','created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['items_detail_id','notification_item_srno'];
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
                        $date_fields = ['created_at'];
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
                     $table_name='courier_bill_notification_used_for_items';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_notification_used_for_items($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_courier_bill_notification_used_for_items($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}

private function filterDuplicateEntries_dynamic_courier_bill_notification_used_for_items($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_notification_used_for_items($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_notification_used_for_items($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_courier_bill_notification_used_for_items($detail, $users_id)
    {
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM courier_bill_notification_used_for_items LEFT JOIN courier_bill_items_details ON courier_bill_notification_used_for_items.items_detail_id=courier_bill_items_details.items_detail_id 
                 LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_items_details.courier_bill_of_entry_id 
                 LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id =cb_file_status.cb_file_status_id   where item_notification_id ='{$detail['item_notification_id']}'  and courier_bill_notification_used_for_items.items_detail_id ='{$detail['items_detail_id']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
            if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_notification_used_for_items 
                             WHERE item_notification_id = '{$detail['item_notification_id']}' 
                             AND items_detail_id = '{$detail['items_detail_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_notification_used_for_items($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   


private function excludeUnwantedFields_dynamic_courier_bill_notification_used_for_items($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_notification_used_for_items($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}   

/**********************************************************************end courier_bill_notification_used_for_items*************************************************************************************/    

/**********************************************************************courier_bill_manifest_details*************************************************************************************/    

public function courier_bill_manifest_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) 
            { 
                 $iec = $user['iec_no'];
                 $bill_of_entry_summary_query = "SELECT courier_bill_manifest_details.*,cb_file_status.cb_file_status_id, cb_file_status.user_iec_no FROM courier_bill_manifest_details 
                 LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_manifest_details.courier_bill_of_entry_id 
                 LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id where cb_file_status.user_iec_no Like '%$iec' and courier_bill_manifest_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  ";
                 $this->processUser_dynamic_courier_bill_manifest_details($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_courier_bill_manifest_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','manifest_details_id', 'import_general_manifest_igm_number', 'date_of_entry_inward', 'master_airway_bill_mawb_number', 'date_of_mawb', 'house_airway_bill_hawb_number','date_of_hawb','marks_and_numbers','number_of_packages','type_of_packages','interest_amount','unit_of_measure_for_gross_weight','gross_weight','created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['courier_bill_of_entry_id','master_airway_bill_mawb_number','house_airway_bill_hawb_number','marks_and_numbers','number_of_packages','interest_amount','gross_weight','created_at'];
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
                        $date_fields = ['created_at'];
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
        if (!empty($valid_query_details))
        { 
                $unwanted_fields = [];
                $table_name='courier_bill_manifest_details';
                $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_manifest_details($valid_query_details,$users_id,$unwanted_fields);
                  
                if (!empty($filtered_query_details))
                {   
                    echo "=====================";echo $users_id;
                    $this->insertLicenceDetailsBatch_dynamic_courier_bill_manifest_details($filtered_query_details, $users_id,$table_name);
                }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_manifest_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_manifest_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_manifest_details($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_courier_bill_manifest_details($detail, $users_id)
    {
        try 
        {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows from courier_bill_manifest_details 
                LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id=courier_bill_manifest_details.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status ON cb_file_status.cb_file_status_id=courier_bill_summary.cb_file_status_id where  courier_bill_summary.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'  and courier_bill_manifest_details.manifest_details_id ='{$detail['manifest_details_id']}'";
                // Execute the duplicate query and fetch the result
                $result = $db_secondary->query($duplicate_query)->row_array();
                // Close secondary database connection
                if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_manifest_details 
                             WHERE courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'  and manifest_details_id ='{$detail['manifest_details_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } 
        catch (Exception $e)
        {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_manifest_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_courier_bill_manifest_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_manifest_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    

/**********************************************************************end courier_bill_manifest_details*************************************************************************************/    


/**********************************************************************courier_bill_items_details*************************************************************************************/    


public function courier_bill_items_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT courier_bill_items_details.courier_bill_of_entry_id,courier_bill_items_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_items_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_items_details.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id  Where cb_file_status.user_iec_no Like '%$iec' and courier_bill_items_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
 
    
  
                $this->processUser_dynamic_courier_bill_items_details($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close(); 
    

}

private function processUser_dynamic_courier_bill_items_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','items_detail_id', 'case_for_reimport', 'import_against_license', 'serial_number_in_invoice', 'item_description', 'general_description','currency_for_unit_price','unit_price','unit_of_measure','quantity','rate_of_exchange','accessories_if_any','name_of_manufacturer','brand','model','grade','specification','end_use_of_item','items_details_country_of_origin','bill_of_entry_number','details_in_case_of_previous_imports_date','details_in_case_previous_imports_currency','unit_value','customs_house','ritc','ctsh','cetsh','currency_for_rsp','retail_sales_price_per_unit','exim_scheme_code_if_any','para_noyear_of_exim_policy','items_details_are_the_buyer_and_seller_related','if_the_buyer_and_seller_relation_examined_earlier_by_svb','items_details_svb_reference_number','items_details_svb_date','items_details_indication_for_provisional_final','shipping_bill_number','shipping_bill_date','port_of_export','invoice_number_of_shipping_bill','item_serial_number_in_shipping_bill','freight','insurance','total_repair_cost_including_cost_of_materials','additional_duty_exemption_requested','items_details_notification_number','serial_number_in_notification','license_registration_number','license_registration_date','debit_value_rs','unit_of_measure_for_quantity_to_be_debited','debit_quantity','item_serial_number_in_license','assessable_value','created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['igm_details_id','ritc','unit_price','quantity','rate_of_exchange','ctsh','cetsh','assessable_value'];
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
                        $date_fields = ['items_details_svb_date','shipping_bill_date','license_registration_date','created_at'];
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
                     $table_name='courier_bill_items_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_items_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_courier_bill_items_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_items_details($valid_query_details, $users_id,$unwanted_fields)
{
$filtered_details = [];

foreach ($valid_query_details as $detail) {
    try {
        // Use more efficient duplicate checking mechanisms if possible
        if ($this->isDuplicateEntry_dynamic_courier_bill_items_details($detail, $users_id)) {
            // Exclude unwanted fields before adding to filtered_details
            $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_items_details($detail,$unwanted_fields);
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
 private function isDuplicateEntry_dynamic_courier_bill_items_details($detail, $users_id)
{
    try {
          
            $db_secondary = $this->load_secondary_database($users_id);
            $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM courier_bill_items_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_items_details.courier_bill_of_entry_id 
            LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id   where items_detail_id ='{$detail['items_detail_id']}'  and courier_bill_items_details.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";
  
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
        // Delete existing duplicate entries based on the criteria
   echo     $delete_query = "DELETE FROM courier_bill_items_details 
                         WHERE items_detail_id ='{$detail['items_detail_id']}'  and courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";

        $db_secondary->query($delete_query);
        }
        // Close secondary database connection
        $db_secondary->close();
        // Check if there are duplicate rows based on the query result
        return true;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
private function validateQueryDetailColumns_dynamic_courier_bill_items_details($detail,$valid_columns)
{
   return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}

private function excludeUnwantedFields_dynamic_courier_bill_items_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_items_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    

/**********************************************************************courier_bill_items_details*************************************************************************************/    



/**********************************************************************courier_bill_invoice_details*************************************************************************************/    

public function courier_bill_invoice_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user)
            {
                 $iec = $user['iec_no'];
                 $bill_of_entry_summary_query = "SELECT courier_bill_invoice_details.courier_bill_of_entry_id,courier_bill_invoice_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_invoice_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_invoice_details.courier_bill_of_entry_id 
                 LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id  Where cb_file_status.user_iec_no Like '%$iec'  and courier_bill_invoice_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
                 $this->processUser_dynamic_courier_bill_invoice_details($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_courier_bill_invoice_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','igm_details_id','invoice_detail_id', 'invoice_number', 'date_of_invoice', 'purchase_order_number', 'date_of_purchase_order', 'contract_number','date_of_contract','letter_of_credit','date_of_letter_of_credit','supplier_details_name','supplier_details_address','if_supplier_is_not_the_seller_name','if_supplier_is_not_the_seller_address','broker_agent_details_name','broker_agent_details_address','nature_of_transaction','if_others','terms_of_payment','conditions_or_restrictions_if_any_attached_to_sale','method_of_valuation','terms_of_invoice','invoice_value','currency','freight_rate','freight_amount','freight_currency','insurance_rate','insurance_amount','insurance_currency','loading_unloading_and_handling_charges_rule_rate','loading_unloading_and_handling_charges_rule_amount','loading_unloading_and_handling_charges_rule_currency','other_charges_related_to_the_carriage_of_goods_rate','other_charges_related_to_the_carriage_of_goods_amount','other_charges_related_to_the_carriage_of_goods_currency','brokerage_and_commission_rate','brokerage_and_commission_amount','brokerage_and_commission_currency','cost_of_containers_rate','cost_of_containers_amount','cost_of_containers_currency','cost_of_packing_rate','cost_of_packing_amount','cost_of_packing_currency','dismantling_transport_handling_in_country_export_rate','dismantling_transport_handling_in_country_export_amount','dismantling_transport_handling_in_country_export_currency','cost_of_goods_and_ser_vices_supplied_by_buyer_rate','cost_of_goods_and_ser_vices_supplied_by_buyer_amount','cost_of_goods_and_ser_vices_supplied_by_buyer_currency','documentation_rate','documentation_amount','documentation_currency','country_of_origin_certificate_rate','country_of_origin_certificate_amount','country_of_origin_certificate_currency','royalty_and_license_fees_rate','royalty_and_license_fees_amount','royalty_and_license_fees_currency','value_of_proceeds_which_accrue_to_seller_rate','value_of_proceeds_which_accrue_to_seller_amount','value_of_proceeds_which_accrue_to_seller_currency','cost_warranty_service_if_any_provided_seller_rate','cost_warranty_service_if_any_provided_seller_amount','cost_warranty_service_if_any_provided_seller_currency','other_payments_satisfy_obligation_rate','other_payments_satisfy_obligation_amount','other_payments_satisfy_obligation_currency','other_charges_and_payments_if_any_rate','other_charges_and_payments_if_any_amount','other_charges_and_payments_if_any_currency','discount_amount','discount_currency','rate','amount','any_other_information_which_has_a_bearing_on_value','are_the_buyer_and_seller_related','if_the_buyer_seller_has_the_relationship_examined_earlier_svb','svb_reference_number','svb_date','indication_for_provisional_final','created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['courier_bill_of_entry_id','invoice_number','invoice_value','freight_rate','freight_amount','insurance_rate','insurance_amount','other_charges_and_payments_if_any_rate','other_charges_and_payments_if_any_amount'];
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
                        $date_fields = ['created_at','svb_date','date_of_letter_of_credit','date_of_contract','date_of_purchase_order'];
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
                     $table_name='courier_bill_invoice_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_invoice_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details))
                         {   
                             echo "=====================";echo 
                             $users_id;
                             $this->insertLicenceDetailsBatch_dynamic_courier_bill_invoice_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_invoice_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_invoice_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_invoice_details($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_courier_bill_invoice_details($detail, $users_id)
    {
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM courier_bill_invoice_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_invoice_details.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id   where invoice_detail_id ='{$detail['invoice_detail_id']}'  and courier_bill_invoice_details.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";
                // Execute the duplicate query and fetch the result
                $result = $db_secondary->query($duplicate_query)->row_array();
              
              if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_invoice_details 
                             WHERE invoice_detail_id ='{$detail['invoice_detail_id']}'  and courier_bill_invoice_details.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_invoice_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_courier_bill_invoice_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_invoice_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    
/**********************************************************************end courier_bill_invoice_details*************************************************************************************/    



/**********************************************************************courier_bill_igm_details*************************************************************************************/    

public function courier_bill_igm_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT courier_bill_igm_details.courier_bill_of_entry_id,courier_bill_igm_details.*,cb_file_status.user_iec_no ,cb_file_status.cb_file_status_id FROM courier_bill_igm_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_igm_details.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id  Where cb_file_status.user_iec_no Like '%$iec'  and courier_bill_igm_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
 
    
  
                $this->processUser_dynamic_courier_bill_igm_details($user,$bill_of_entry_summary_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_courier_bill_igm_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                       $valid_columns = ['courier_bill_of_entry_id','igm_details_id', 'airlines', 'flight_no', 'airport_of_arrival', 'date_of_arrival', 'created_at'];
                       // Define unwanted field s
                       // Process and filter the query details to retain only valid columns
                       $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                       // Convert 'debt_amt' to a numeric value or default to zero
                       // Validate and format numeric fields
                            $numeric_fields = ['igm_details_id '];
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
                        $date_fields = ['created_at','date_of_arrival'];
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
                     $table_name='courier_bill_igm_details';
    
 
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_igm_details($valid_query_details,$users_id,$unwanted_fields);
                  
                         if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_courier_bill_igm_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_igm_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_igm_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_igm_details($detail,$unwanted_fields);
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
     private function isDuplicateEntry_dynamic_courier_bill_igm_details($detail, $users_id)
    {
        try {
              
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM courier_bill_igm_details LEFT JOIN courier_bill_summary ON courier_bill_summary.courier_bill_of_entry_id = courier_bill_igm_details.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status ON courier_bill_summary.cb_file_status_id = cb_file_status.cb_file_status_id   where igm_details_id ='{$detail['igm_details_id']}'  and courier_bill_igm_details.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
            if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_igm_details 
                             WHERE igm_details_id ='{$detail['igm_details_id']}'  and courier_bill_igm_details.courier_bill_of_entry_id ='{$detail['courier_bill_of_entry_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
    }
  private function validateQueryDetailColumns_dynamic_courier_bill_igm_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

private function excludeUnwantedFields_dynamic_courier_bill_igm_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}
 

private function insertLicenceDetailsBatch_dynamic_courier_bill_igm_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    
/**********************************************************************end courier_bill_igm_details*************************************************************************************/    
    
/********************************************************************** courier_bill_duty_details*************************************************************************************/    
    
public function courier_bill_duty_details()
{
    
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*)
 as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) 
            {
               $iec = $user['iec_no'];
               $courier_bill_duty_details_query = "SELECT cbd.*, cbs.courier_bill_of_entry_id, cbid.items_detail_id 
               FROM courier_bill_duty_details cbd
               join courier_bill_items_details as cbid on cbid.items_detail_id = cbd.items_detail_id
               join courier_bill_summary as cbs on cbs.courier_bill_of_entry_id = cbid.courier_bill_of_entry_id
               join cb_file_status as cfs on cfs.cb_file_status_id = cbs.cb_file_status_id Where cfs.user_iec_no Like '%$iec'  and cbd.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
               $this->processUser_dynamic_courier_bill_duty_details($user,$courier_bill_duty_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    

}

private function processUser_dynamic_courier_bill_duty_details($user,$query)
{
          $iec = $user["iec_no"];
         $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
   
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) {
                   // Define the valid columns to be inserted into aa_dfia_licence_details
                     // List of fields to process and escape
                    $valid_columns = [
                        'items_detail_id','cobe_id','duty_details_id', 'bcd_duty_head ', 'bcd_ad_valorem', 'bcd_specific_rate', 'bcd_duty_forgone', 'bcd_duty_amount', 'aidc_duty_head',
                        'igst_ad_valorem', 'igst_specific_rate', 'igst_duty_forgone', 'igst_duty_amount ', 'cmpnstry_duty_head', 'cmpnstry_ad_valorem', 'cmpnstry_specific_rate', 'cmpnstry_duty_forgone', 'cmpnstry_duty_amount', 'dummy5_duty_head',
                        'dummy5_ad_valorem', 'dummy5_specific_rate', 'dummy5_duty_forgone', 'dummy5_duty_amount', 'dummy6_duty_head', 'dummy6_ad_valorem', 'dummy6_specific_rate', 'dummy6_duty_forgone', 'dummy6_duty_amount', 'dummy7_duty_head',
                        'dummy7_ad_valorem', 'dummy7_specific_rate', 'dummy7_duty_forgone', 'dummy7_duty_amount', 'dummy8_duty_head',
                        'dummy8_ad_valorem', 'dummy8_specific_rate', 'dummy8_duty_forgone', 'dummy8_duty_amount', 'dummy9_duty_head', 'dummy9_ad_valorem ', 'dummy9_specific_rate', 'dummy9_duty_forgone', 'dummy9_duty_amount', 'dummy10_duty_head',
                        'dummy10_ad_valorem', 'dummy10_specific_rate', 'dummy10_duty_forgone', 'dummy11_duty_head', 'dummy11_ad_valorem', 'dummy11_specific_rate', 'dummy11_duty_forgone', 'dummy11_duty_amount', 'created_at'];
                        // Define unwanted fields
                        // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                        // Convert 'debt_amt' to a numeric value or default to zero
                        // Validate and format numeric fields
                        $numeric_fields = [
                               'bcd_specific_rate', 'bcd_duty_forgone', 'bcd_duty_amount', 'aidc_ad_valorem', 'aidc_specific_rate', 'aidc_duty_forgone', 'aidc_duty_amount', 'sw_srchrg_ad_valorem',
                                'sw_srchrg_specific_rate', 'sw_srchrg_duty_forgone', 'sw_srchrg_duty_amount', 'igst_ad_valorem', 'igst_specific_rate', 'igst_duty_forgone', 'igst_duty_amount', 'cmpnstry_ad_valorem',
                                'cmpnstry_specific_rate', 'cmpnstry_duty_forgone','cmpnstry_duty_amount'
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
                        
                        if(!empty($date_fields))
                        {
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
         
        if (!empty($valid_query_details)) 
        { 
                        $unwanted_fields = [];
                        $table_name='courier_bill_duty_details';
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_duty_details($valid_query_details,$users_id,$unwanted_fields);
                         if (!empty($filtered_query_details)) 
                         {   
                            echo "=====================";
                            echo $users_id;
                            $this->insertLicenceDetailsBatch_dynamic_courier_bill_duty_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}
private function filterDuplicateEntries_dynamic_courier_bill_duty_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
         
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_duty_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_duty_details($detail,$unwanted_fields);
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

private function isDuplicateEntry_dynamic_courier_bill_duty_details($detail, $users_id)
{
    try 
    {
        $db_secondary = $this->load_secondary_database($users_id);
        $duplicate_query = "SELECT COUNT(*) AS num_rows FROM courier_bill_duty_details WHERE duty_details_id ='{$detail['duty_details_id']}' AND items_detail_id ='{$detail['items_detail_id']}'";
        // Execute the duplicate query and fetch the result  
        $result = $db_secondary->query($duplicate_query)->row_array();
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_duty_details 
                             WHERE duty_details_id ='{$detail['duty_details_id']}' AND items_detail_id ='{$detail['items_detail_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    } 
    catch (Exception $e)
    {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}
  private function validateQueryDetailColumns_dynamic_courier_bill_duty_details($detail,$valid_columns)
    {
       return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
    }

   


private function excludeUnwantedFields_dynamic_courier_bill_duty_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
    unset($detail[$field]);
}

return $detail;
}    


private function insertLicenceDetailsBatch_dynamic_courier_bill_duty_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
} 
/**********************************************************************end courier_bill_duty_details*************************************************************************************/    


/*********************************************************************courier_bill_container_details*************************************************************************************/    

public function courier_bill_container_details()
{
         // Load secondary database
         $this->load->database('second');
        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user)
            {
                $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "
                SELECT cbc.*, cbc.courier_bill_of_entry_id
                FROM courier_bill_container_details cbc 
                LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbc.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id 
                WHERE cfs.user_iec_no LIKE '%$iec'  and cbc.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";  
                $this->processUser_dynamic_courier_bill_container_details($user,$bill_of_entry_summary_query);
            }
        }

        $this->db->close();
}   


public function processUser_dynamic_courier_bill_container_details($user,$query)
{
        $iec = $user["iec_no"];
        $users_id = $user["lucrative_users_id"];
        // Fetch licence details efficiently
        $query_details = $this->db->query($query)->result_array();
          if (!empty($query_details)) 
          {
                // Define the valid columns to be inserted into aa_dfia_licence_details
                // List of fields to process and escape
                $valid_columns = ['courier_bill_of_entry_id', 'container_details_id', 'container_details_srno', 'container', 'seal_number','fcl_lcl','created_at'];
                // Define unwanted fields
                // Process and filter the query details to retain only valid columns
                $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                // Convert 'debt_amt' to a numeric value or default to zero
                // Validate and format numeric fields
                $numeric_fields = ['courier_bill_of_entry_id', 'container_details_id','container_details_srno'];
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
                        $date_fields = ['created_at'];
                        
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
        if (!empty($valid_query_details))
        { 
                        $unwanted_fields = [];
                        $table_name='courier_bill_container_details';
                        $filtered_query_details = $this->filterDuplicateEntries_dynamic_courier_bill_container_details($valid_query_details,$users_id,$unwanted_fields);
                         if (!empty($filtered_query_details)) 
                         {   
                            echo "=====================";echo $users_id;
                            $this->insertLicenceDetailsBatch_dynamic_courier_bill_container_details($filtered_query_details, $users_id,$table_name);
                         }
        }
    }
}


public function filterDuplicateEntries_dynamic_courier_bill_container_details($valid_query_details, $users_id,$unwanted_fields)
{
     $filtered_details = [];
    foreach ($valid_query_details as $detail) 
    {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_container_details($detail, $users_id))
            {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_container_details($detail,$unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } 
        catch (Exception $e)
        {
            // Handle database error
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
             throw $e;
        }
    }
    return $filtered_details;
}

public function isDuplicateEntry_dynamic_courier_bill_container_details($detail, $users_id)
{
    try {
                $db_secondary = $this->load_secondary_database($users_id);
                $duplicate_query = 
                "SELECT COUNT(*) AS num_rows  FROM courier_bill_container_details cbc 
                LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbc.courier_bill_of_entry_id 
                LEFT JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id 
                WHERE   cbc.courier_bill_of_entry_id='{$detail['courier_bill_of_entry_id']}'  and container_details_srno='{$detail['container_details_srno']}' and  cbs.courier_bill_of_entry_id='{$detail['courier_bill_of_entry_id']}'";
      
            // Execute the duplicate query and fetch the result
            $result = $db_secondary->query($duplicate_query)->row_array();
            
            if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_container_details 
                             WHERE courier_bill_of_entry_id='{$detail['courier_bill_of_entry_id']}'  and container_details_srno='{$detail['container_details_srno']}' and  cbs.courier_bill_of_entry_id='{$detail['courier_bill_of_entry_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } catch (Exception $e) {
            // Handle database error
            log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
            return false; // Return false indicating error occurred
        }
}

private function excludeUnwantedFields_dynamic_courier_bill_container_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field)
{
    unset($detail[$field]);
}

return $detail;
}


private function insertLicenceDetailsBatch_dynamic_courier_bill_container_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    
/*********************************************************************End courier_bill_container_details*************************************************************************************/    

/*********************************************************************courier_bill_bond_details*************************************************************************************/    

public function courier_bill_bond_details()
{
        // Load secondary database
        $this->load->database('second');
        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        for ($page = 0; $page < $pages; $page++)
        {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) 
            {
                $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT cbd.*, cbs.cb_file_status_id
                FROM courier_bill_bond_details cbd
                LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbd.courier_bill_of_entry_id
                LEFT JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id 
                WHERE cfs.user_iec_no LIKE '%$iec'  and cbd.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') "; 
                $this->processUser_courier_bill_bond_details($user,$bill_of_entry_summary_query);
            }
        }
        $this->db->close();
}


private function processUser_courier_bill_bond_details($user,$query)
{
           $iec = $user["iec_no"];
           $users_id = $user["lucrative_users_id"];
           $query_details = $this->db->query($query)->result_array();
           if (!empty($query_details)) 
           {
                        // Define the valid columns to be inserted into aa_dfia_licence_details
                        // List of fields to process and escape
                   $valid_columns = ['courier_bill_of_entry_id', 'bond_details_id', 'bond_details_srno', 'bond_type', 'bond_number', 'clearance_of_imported_goods_bond_already_registered_customs',];

                    // Process and filter the query details to retain only valid columns
                    $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                    // Convert 'debt_amt' to a numeric value or default to zero
                    // Validate and format numeric fields
                    $numeric_fields =
                    [
                        'bond_details_id', 'bond_details_srno',
                    ];
                            
                            foreach ($numeric_fields as $field) 
                            {
                                if (!empty($query_details[$field])) 
                                {
                                    // Check if the value is numeric
                                    if (is_numeric($query_details[$field])) {
                                        // Convert the value to a float or decimal
                                        $query_details[$field] = (float)$query_details[$field];
                                    } else {
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
                        $date_fields = ['created_at ', ];
                        
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
        
        if (!empty($valid_query_details))
        { 
                    $unwanted_fields = [];
                    $table_name='courier_bill_bond_details';
                    $filtered_query_details = $this->filterDuplicateEntries_courier_bill_bond_details($valid_query_details,$users_id,$unwanted_fields);
                    if (!empty($filtered_query_details)) 
                    {   
                        echo "=====================";
                        echo $users_id;
                        $this->insertLicenceDetailsBatch_dynamic_courier_bill_bond_details($filtered_query_details, $users_id,$table_name);
                    }
        }
    }
}


private function filterDuplicateEntries_courier_bill_bond_details($valid_query_details, $users_id,$unwanted_fields)
    {
    $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            // Use more efficient duplicate checking mechanisms if possible
            if ($this->isDuplicateEntry_dynamic_courier_bill_bond_details($detail, $users_id)) {
                // Exclude unwanted fields before adding to filtered_details
                $filtered_detail = $this->excludeUnwantedFields_dynamic_courier_bill_bond_details($detail,$unwanted_fields);
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


 private function isDuplicateEntry_dynamic_courier_bill_bond_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
	    $duplicate_query = "SELECT  COUNT(*) AS num_rows FROM courier_bill_bond_details cbd
        LEFT JOIN courier_bill_summary cbs ON cbs.courier_bill_of_entry_id = cbd.courier_bill_of_entry_id
        LEFT JOIN cb_file_status cfs ON cfs.cb_file_status_id = cbs.cb_file_status_id  where bond_details_id  ='{$detail['bond_details_id']}' and bond_details_srno ='{$detail['bond_details_srno']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM courier_bill_bond_details 
                             WHERE bond_details_id  ='{$detail['bond_details_id']}' and bond_details_srno ='{$detail['bond_details_srno']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    } catch (Exception $e) {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
        return false; // Return false indicating error occurred
    }
}


private function insertLicenceDetailsBatch_dynamic_courier_bill_bond_details($licence_details, $users_id,$table_name)
{
    $db_secondary = $this->load_secondary_database($users_id); 

    try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    
    $db_secondary->close();
}    

private function excludeUnwantedFields_dynamic_courier_bill_bond_details($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field)
{
    unset($detail[$field]);
}

return $detail;
}
/*********************************************************************end courier_bill_bond_details*************************************************************************************/    


/*********************************************************************bill_bond_details*************************************************************************************/    

 public function bill_bond_details()
    {
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_bond_details_query = "SELECT bill_bond_details.*, bill_of_entry_summary.boe_id as boeid ,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no 
        FROM bill_bond_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_bond_details.boe_id and bill_of_entry_summary.iec_no LIKE '%$iec%'  
        and bill_bond_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ";
 
    
  
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
            if ($this->isDuplicateEntry_dynamic($detail, $users_id)) {
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
        
       if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM bill_bond_details   where boe_id='{$detail['boe_id']}'  and  bond_no ='{$detail['bond_no']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************bill_bond_details*************************************************************************************/    




/*********************************************************************aa_dfia_licence_details*************************************************************************************/    

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
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
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
 $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));
        // Fetch licence details efficiently
         $licenceQuery = "SELECT ald.*, sbs.sbs_id, sbs.sb_no, sbs.iec, id.invoice_id, id.item_id
            FROM aa_dfia_licence_details ald
            LEFT JOIN item_details id ON ald.item_id = id.item_id
            LEFT JOIN invoice_summary isum ON id.invoice_id = isum.invoice_id
            LEFT JOIN ship_bill_summary sbs ON isum.sbs_id = sbs.sbs_id
            WHERE sbs.iec LIKE '%$iec%'  and sbs.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
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
            if ($this->isDuplicateEntry($detail, $users_id)) {
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
       if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM  aa_dfia_licence_details 
            WHERE item_s_no_ = '{$detail['item_s_no_']}' AND inv_s_no = '{$detail['inv_s_no']}' AND exp_s_no = '{$detail['exp_s_no']}' AND imp_s_no = '{$detail['imp_s_no']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************aa_dfia_licence_details*************************************************************************************/    
    
/*********************************************************************bill_payment_details*************************************************************************************/    
    
    public function bill_payment_details()
{
    
     
      // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_payment_details_query = "SELECT bill_payment_details.*, bill_of_entry_summary.boe_id as boeid, bill_of_entry_summary.be_no, bill_of_entry_summary.iec_no 
            FROM bill_payment_details  
            LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_payment_details.boe_id 
            WHERE bill_of_entry_summary.iec_no  LIKE '%$iec%'  and bill_of_entry_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
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
        
         /*if (!empty($query_details)) {
        $query_details = array_filter($query_details, function ($detail) {
            return $this->validateQueryDetailColumns_dynamic_bill_payment_details($detail);
        });
        
         }*/
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
            if ($this->isDuplicateEntry_dynamic_bill_payment_details($detail, $users_id)) {
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
        
       if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM bill_payment_details   where boe_id='{$detail['boe_id']}'  and challan_no='{$detail['challan_no']}'  and payment_details_id='{$detail['payment_details_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************end bill_payment_details*************************************************************************************/    





/******************************************************************************bill_container_details****************************************************************************************/



public function bill_container_details()
{
    
    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_of_entry_summary_query = "SELECT bill_container_details.*, bill_of_entry_summary.boe_id as boeid, bill_of_entry_summary.be_no, bill_of_entry_summary.iec_no 
            FROM bill_container_details  
            LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_container_details.boe_id 
            WHERE bill_of_entry_summary.iec_no  LIKE '%$iec%'  and bill_of_entry_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
                $this->processUser_dynamic_bill_container_details($user,$bill_of_entry_summary_query);
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
            if ($this->isDuplicateEntry_dynamic_bill_container_details($detail, $users_id)) {
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
                     FROM bill_container_details   where boe_id='{$detail['boe_id']}'  and container_number='{$detail['container_number']}'  and container_details_id='{$detail['container_details_id']}'";

        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
         if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM bill_container_details   where boe_id='{$detail['boe_id']}'  and container_number='{$detail['container_number']}'  and container_details_id='{$detail['container_details_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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


    public function bill_licence_details() {
        // Load secondary database
        $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                $iec = $user['iec_no'];
                 $bill_licence_details_query = "SELECT CONCAT(n1.be_no,'-',bill_licence_details.invsno,'-',bill_licence_details.itemsn) as reference_code, n1.boe_id as boeid, n1.iec_no, n1.be_no, n1.be_date, bill_licence_details.*
                    FROM bill_of_entry_summary n1
                    JOIN invoice_and_valuation_details ON n1.boe_id = invoice_and_valuation_details.boe_id
                    JOIN duties_and_additional_details ON invoice_and_valuation_details.invoice_id = duties_and_additional_details.invoice_id
                    JOIN bill_licence_details ON duties_and_additional_details.duties_id = bill_licence_details.duties_id
                    WHERE n1.iec_no LIKE '%$iec%'  and n1.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";

                $this->processUser_dynamic_bill_licence_details($user, $bill_licence_details_query);
            }
        }

        // Close the main database connection
        $this->db->close();
    }

    private function processUser_dynamic_bill_licence_details($user, $query) {
        $iec = $user["iec_no"];
        $users_id = $user["lucrative_users_id"];

        // Fetch licence details efficiently
        $query_details = $this->db->query($query)->result_array();
        //print_r($query_details);
        if (!empty($query_details)) {
            // Define the valid columns to be inserted into aa_dfia_licence_details
            $valid_columns = ['licence_id', 'duties_id', 'invsno', 'itemsn', 'lic_slno', 'lic_no', 'lic_date', 'code', 'port', 'debit_value', 'qty', 'uqc_lc_d', 'debit_duty', 'created_at'];

            // Process and filter the query details to retain only valid columns
            $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                // Validate and format numeric fields
                $numeric_fields = ['qty', 'uqc_lc_d'];
                foreach ($numeric_fields as $field) {
                    if (!empty($query_details[$field])) {
                        if (is_numeric($query_details[$field])) {
                            $query_details[$field] = (float)$query_details[$field];
                        } else {
                            $query_details[$field] = 0;
                        }
                    } else {
                        $query_details[$field] = 0;
                    }
                }

                // Validate and format date fields
                $date_fields = ['be_date', 'lic_date'];
                foreach ($date_fields as $field) {
                    if (!empty($query_details[$field])) {
                        $query_details[$field] = date("Y-m-d", strtotime($query_details[$field]));
                    } else {
                        $query_details[$field] = '1970-01-01';
                    }
                }

                return array_intersect_key($query_details, array_flip($valid_columns));
            }, $query_details);

            if (!empty($valid_query_details)) {
                $unwanted_fields = ['boeid', 'be_no', 'iec_no'];
                $table_name = 'bill_licence_details';

                $filtered_query_details = $this->filterDuplicateEntries_dynamic_bill_licence_details($valid_query_details, $users_id, $unwanted_fields);

                if (!empty($filtered_query_details)) {
                    $this->insertLicenceDetailsBatch_dynamic_bill_licence_details($filtered_query_details, $users_id, $table_name);
                }
            }
        }
    }

    private function filterDuplicateEntries_dynamic_bill_licence_details($valid_query_details, $users_id, $unwanted_fields) {
        $filtered_details = [];

        foreach ($valid_query_details as $detail) {
            try {
                // Check if the current detail is a duplicate
               echo $isDuplicate = $this->isDuplicateEntry_dynamic_bill_licence_details($detail, $users_id);

                if ($this->isDuplicateEntry_dynamic_bill_licence_details($detail, $users_id)) { echo $users_id;
                    // Detail is not a duplicate, proceed to exclude unwanted fields
                    $filtered_detail = $this->excludeUnwantedFields_dynamic_bill_licence_details($detail, $unwanted_fields);
                    $filtered_details[] = $filtered_detail;
                }
            } catch (Exception $e) {
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    log_message('info', 'Duplicate entry error encountered. Skipping this record.');
                } else {
                    log_message('error', 'Database Error: ' . $e->getMessage());
                }
            }
        }

        return $filtered_details;
    }

    private function isDuplicateEntry_dynamic_bill_licence_details($detail, $users_id) {
        try {
            $db_secondary = $this->load_secondary_database($users_id);

            /* $duplicate_query = "SELECT COUNT(*) AS num_rows 
                                FROM bill_licence_details  
                                WHERE licence_id = ? AND lic_slno = ? AND lic_no = ? AND debit_value = ? AND qty = ?";*/
                                $duplicate_query = "SELECT COUNT(*) AS num_rows 
                                FROM bill_licence_details  
                                WHERE licence_id = ?";

            $result = $db_secondary->query($duplicate_query, [
                $detail['licence_id']/*,
                $detail['lic_slno'],
                $detail['lic_no'],
                $detail['debit_value'],
                $detail['qty']*/
            ])->row_array();

            if ($result['num_rows'] > 0) {
                /*$delete_query = "DELETE FROM bill_licence_details 
                                 WHERE licence_id = ? AND lic_slno = ? AND lic_no = ? AND debit_value = ? AND qty = ?";*/
                                   $delete_query = "DELETE FROM bill_licence_details 
                                 WHERE licence_id = ?";

                $db_secondary->query($delete_query, [
                    $detail['licence_id']/*,
                    $detail['lic_slno'],
                    $detail['lic_no'],
                    $detail['debit_value'],
                    $detail['qty']*/
                ]);

                $db_secondary->close();
                return true;
            }

            $db_secondary->close();
             return true;
        } catch (Exception $e) {
            log_message('error', 'Database Error in isDuplicateEntry_dynamic_bill_licence_details: ' . $e->getMessage());
            return false;
        }
    }

    private function excludeUnwantedFields_dynamic_bill_licence_details($detail, $unwanted_fields) {
        foreach ($unwanted_fields as $field) {
            unset($detail[$field]);
        }
        return $detail;
    }

    private function insertLicenceDetailsBatch_dynamic_bill_licence_details($licence_details, $users_id, $table_name) {
        $db_secondary = $this->load_secondary_database($users_id);

        try {
            $db_secondary->insert_batch($table_name, $licence_details);
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
        }

        $db_secondary->close();
    }

  


/******************************************************************************end bill_licence_details****************************************************************************************/




/******************************************************************************bill_manifest_details****************************************************************************************/

public function bill_manifest_details()
{
    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_manifest_details_query = "SELECT bill_manifest_details.*, bill_of_entry_summary.boe_id as boeid, bill_of_entry_summary.be_no, bill_of_entry_summary.iec_no 
                                        FROM bill_manifest_details 
                                        LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_manifest_details.boe_id 
                                        WHERE bill_of_entry_summary.iec_no LIKE '%$iec%'  and bill_of_entry_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
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
            if ($this->isDuplicateEntry_dynamic_bill_manifest_details($detail, $users_id)) {
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
        
        
              if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM bill_manifest_details   where manifest_details_id  ='{$detail['manifest_details_id']}'  and igm_no='{$detail['igm_no']}' ";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $bill_manifest_details_query = "SELECT challan_details.*,ship_bill_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sb_no,ship_bill_summary.iec FROM challan_details  
    JOIN ship_bill_summary ON ship_bill_summary.sbs_id=challan_details.sbs_id  Where ship_bill_summary.iec LIKE '%$iec%'  and challan_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
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
            if ($this->isDuplicateEntry_dynamic_challan_details($detail, $users_id)) {
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
        
        
              if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM challan_details   where sbs_id  ='{$detail['sbs_id']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/******************************************************************************end challan_details****************************************************************************************/

/******************************************************************************drawback_details****************************************************************************************/

public function drawback_details()
{
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'     order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
         
           $admin_users = $this->db->query($adminUsersQuery)->result_array();
           //echo '<pre>'; print_r( $admin_users);
            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              echo  $bill_manifest_details_query = "SELECT CONCAT(n1.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code, sb_no, sb_date, iec_br, iec, inv_sno, item_sno, item_details.hs_cd, item_details.description, dbk_sno, qty_wt, value, dbk_amt, stalev, cenlev, drawback_details.* 
                                    FROM ship_bill_summary n1 
                                    JOIN invoice_summary ON invoice_summary.sbs_id = n1.sbs_id 
                                    JOIN item_details ON invoice_summary.invoice_id = item_details.invoice_id 
                                    JOIN drawback_details ON drawback_details.item_id = item_details.item_id 
                                    WHERE n1.iec LIKE '%$iec%' and drawback_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
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
            
   
        
            
               $unwanted_fields = ['iec'];
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
        $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            if ($this->isDuplicateEntry_dynamic_drawback_details($detail, $users_id)) {
                $filtered_detail = $this->excludeUnwantedFields_dynamic_drawback_details($detail, $unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            throw $e;
        }
    }

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
          //  print_r($result);
               if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM drawback_details
                                WHERE item_id = '{$detail['item_id']}' AND qty_wt = '{$detail['qty_wt']}' AND inv_sno = '{$detail['inv_sno']}' AND item_sno = '{$detail['item_sno']}' AND dbk_sno = '{$detail['dbk_sno']}' ";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
        } 
    catch (Exception $e)
    {
        // Handle database error
        log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
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
/******************************************************************************drawback_details****************************************************************************************/



/******************************************************************************item_details****************************************************************************************/

public function item_details(){    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'    ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $item_details_query = "SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv,'-',item_details.item_s_no) as reference_code,ship_bill_summary.sb_date,item_details.*,invoice_summary.invoice_id as invoiceid,invoice_summary.sbs_id as sbsid,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM item_details 
        JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id 
        JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE ship_bill_summary.iec LIKE '%$iec%'  and item_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
 
    
  
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
     $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            if ($this->isDuplicateEntry_dynamic_item_details($detail, $users_id)) {
                $filtered_detail = $this->excludeUnwantedFields_dynamic_item_details($detail, $unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            throw $e;
        }
    }

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
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM item_details   where item_id  ='{$detail['item_id']}' and invoice_id  ='{$detail['invoice_id']}' and invsn  ='{$detail['invsn']}' and item_s_no  ='{$detail['item_s_no']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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

/****************************************************************************invoice_and_valuation_details************************************************************************************/
public function invoice_and_valuation_details()
{    
    // Load secondary database
    $this->load->database('second');

    // Fetch admin users with paginated queries to handle large datasets
    $perPage = 50; // Adjust based on memory and performance testing
    $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
    $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

    for ($page = 0; $page < $pages; $page++) {
        $offset = $page * $perPage;
        $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
        $admin_users = $this->db->query($adminUsersQuery)->result_array();

        foreach ($admin_users as $user) {
            $iec = $user['iec_no'];
            $invoice_and_valuation_details_query = "SELECT invoice_and_valuation_details.*, bill_of_entry_summary.iec_no 
                                                    FROM invoice_and_valuation_details 
                                                    LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = invoice_and_valuation_details.boe_id  
                                                    WHERE bill_of_entry_summary.iec_no LIKE '%$iec%'  and bill_of_entry_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";

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
          /*  if ($this->isDuplicateEntry_dynamic_invoice_and_valuation_details($detail, $users_id)) {
                $filtered_detail = $this->excludeUnwantedFields_dynamic_invoice_and_valuation_details($detail, $unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }*/
            
             $filtered_query_details =$this->isDuplicateEntry_dynamic_invoice_and_valuation_details($detail, $users_id);
            if (!empty($filtered_query_details)) 
            {
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

        // Use parameter binding to avoid SQL injection and syntax errors
        $duplicate_query = "SELECT COUNT(*) AS num_rows 
                            FROM invoice_and_valuation_details  
                            WHERE invoice_id = ? AND s_no = ? AND invoice_no = ? AND invoice_date = ?";

        $result = $db_secondary->query($duplicate_query, [
            $detail['invoice_id'], 
            $detail['s_no'], 
            $detail['invoice_no'], 
            $detail['invoice_date']
        ])->row_array();

        if ($result['num_rows'] > 0) {
            // Delete existing duplicate entries based on the criteria
            $delete_query = "DELETE FROM invoice_and_valuation_details 
                             WHERE invoice_id = ? AND s_no = ? AND invoice_no = ? AND invoice_date = ?";

            $db_secondary->query($delete_query, [
                $detail['invoice_id'], 
                $detail['s_no'], 
                $detail['invoice_no'], 
                $detail['invoice_date']
            ]);
        }

        // Close secondary database connection
        $db_secondary->close();

        // Check if there are duplicate rows based on the query result
        return true;
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
/*********************************************************************end invoice_and_valuation_details************************************************************************************/

/*********************************************************************end rodtep_details************************************************************************************/

public function rodtep_details(){    
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
                $invoice_and_valuation_details_query = "SELECT CONCAT(ship_bill_summary.sb_no,'-',rodtep_details.inv_sno, '-', rodtep_details.item_sno) as reference_code,rodtep_details.*,item_details.invoice_id,invoice_summary.invoice_id,invoice_summary.sbs_id,ship_bill_summary.iec,ship_bill_summary.sbs_id,ship_bill_summary.sb_date FROM rodtep_details LEFT JOIN item_details ON item_details.item_id=rodtep_details.item_id LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE ship_bill_summary.iec LIKE '%$iec%' 
                and ship_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";

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
                   $valid_columns = ["rodtep_id","item_id", "inv_sno", "item_sno", "quantity", "uqc", "no_of_units","value"];

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
       $filtered_details = [];

    foreach ($valid_query_details as $detail) {
        try {
            if ($this->isDuplicateEntry_dynamic_rodtep_details($detail, $users_id)) {
                $filtered_detail = $this->excludeUnwantedFields_dynamic_rodtep_details($detail, $unwanted_fields);
                $filtered_details[] = $filtered_detail;
            }
        } catch (Exception $e) {
            log_message('error', 'Error while filtering duplicate entries: ' . $e->getMessage());
            throw $e;
        }
    }

    return $filtered_details;
}
private function isDuplicateEntry_dynamic_rodtep_details($detail, $users_id)
{
    try {
        $db_secondary = $this->load_secondary_database($users_id);
               $duplicate_query = "SELECT COUNT(*) AS num_rows FROM rodtep_details  where rodtep_id= '{$detail['rodtep_id']}' and item_id = '{$detail['item_id']}' AND inv_sno = '{$detail['inv_sno']}'
                             AND item_sno = '{$detail['item_sno']}'";
          // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM rodtep_details 
                             WHERE 	rodtep_id= '{$detail['rodtep_id']}' and item_id = '{$detail['item_id']}' AND inv_sno = '{$detail['inv_sno']}'
                             AND item_sno = '{$detail['item_sno']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************end rodtep_details************************************************************************************/

/*********************************************************************jobbing_details************************************************************************************/

public function jobbing_details(){    
    
        // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
               // $jobbing_details_query = "SELECT jobbing_details.*,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_no FROM jobbing_details JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id Where ship_bill_summary.iec LIKE '%$iec%' ";

$jobbing_details_query = "SELECT jobbing_details.*,ship_bill_summary.iec,ship_bill_summary.sbs_id ,ship_bill_summary.sb_no FROM 
jobbing_details JOIN ship_bill_summary ON ship_bill_summary.sbs_id=jobbing_details.sbs_id Where ship_bill_summary.iec LIKE '%$iec%' 
and jobbing_details.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
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
            if ($this->isDuplicateEntry_dynamic_jobbing_details($detail, $users_id)) {
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
        
       if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM jobbing_details 
                             WHERE where jobbing_detail_id  ='{$detail['jobbing_detail_id']}' and be_no  ='{$detail['be_no']}' and qty_imp  ='{$detail['qty_imp']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************End jobbing_details************************************************************************************/

/*********************************************************************ship_bill_summary************************************************************************************/

public function ship_bill_summary()
{
    
         // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              echo  $jobbing_details_query = " SELECT * FROM ship_bill_summary Where iec LIKE '%$iec%' and ship_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";
             // echo  $jobbing_details_query = " SELECT * FROM ship_bill_summary Where iec LIKE '%$iec%' ";

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

                        // Define your date fields
$date_fields = ['cin_dt', 'hawb_dt', 'mawb_dt', 'rbi_waiver_no_and_dt', 'sb_date', 'submission_date', 'assessment_date', 'examination_date', 'leo_date', 'leo_dt'];

if (!empty($date_fields)) {
    // Loop through each date field and format it
    foreach ($date_fields as $field) {
        if (isset($query_details[$field]) && !empty($query_details[$field])) {
            // Attempt to parse the date string
            $timestamp = strtotime($query_details[$field]);

            if ($timestamp !== false) {
                // Valid date, format it as 'Y-m-d'
                 $formatted_date = date("Y-m-d", $timestamp);
                if($formatted_date=='-0001-11-30'){
                         $query_details[$field] = '2023-11-30';
                   }else{
                // Invalid date format, handle accordingly (set to default or NULL)
                        $query_details[$field] = $formatted_date; // Default date
                   }
                // Update the query_details with the formatted date
                
            } else {
                
                if( $formatted_date=='-0001-11-30'){
                    $query_details[$field] = '2023-11-30';
                   }else{
                // Invalid date format, handle accordingly (set to default or NULL)
                $query_details[$field] = '1970-01-01'; // Default date
                   }
            }
        } else {
            // Empty or missing date value, handle accordingly (set to default or NULL)
            
            $query_details[$field] = '1970-01-01'; // Default date
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
            if ($this->isDuplicateEntry_dynamic_ship_bill_summary($detail, $users_id)) {
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
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM  ship_bill_summary  where sb_no  ='{$detail['sb_no']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    
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
/*********************************************************************End ship_bill_summary************************************************************************************/
  
/********************************************************************* equipment_details************************************************************************************/
  
 public function equipment_details(){    
     // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'  ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              echo  $jobbing_details_query = "SELECT equipment_details.*,ship_bill_summary.sbs_id,ship_bill_summary.sb_no,ship_bill_summary.iec FROM equipment_details 
  LEFT JOIN ship_bill_summary ON ship_bill_summary.sbs_id=equipment_details.sbs_id  where ship_bill_summary.iec Like '%$iec%' 
  AND ship_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  ";

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
                   $valid_columns = ["equip_id","sbs_id","container","seal","date","s_no","created_at","a","b","c","d","e"];

                      // Define unwanted fields

                      // Process and filter the query details to retain only valid columns
                        $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
                            // Convert 'debt_amt' to a numeric value or default to zero
              // Validate and format numeric fields
                            $numeric_fields = ["a","b","c","d","e"];
                         
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
            if ($this->isDuplicateEntry_dynamic_equipment_details($detail, $users_id)) {
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
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM equipment_details 
                             WHERE equipment_details.sbs_id='{$detail['sbs_id']}'  AND equipment_details.container ='{$detail['container']}' ";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************End equipment_details************************************************************************************/

/*********************************************************************item_manufacturer_details************************************************************************************/

public function item_manufacturer_details() {
    
    
    // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
              $query_item_manufacturer_details = "SELECT CONCAT(ship_bill_summary.sb_no,'-',invoice_summary.s_no_inv, '-', item_details.item_s_no) as reference_code,
              item_manufacturer_details.*, item_details.invoice_id, invoice_summary.invoice_id, invoice_summary.sbs_id, ship_bill_summary.iec, 
              ship_bill_summary.sbs_id FROM item_manufacturer_details LEFT JOIN item_details ON item_details.item_id=item_manufacturer_details.item_id 
              LEFT JOIN invoice_summary ON invoice_summary.invoice_id=item_details.invoice_id LEFT JOIN ship_bill_summary ON 
              ship_bill_summary.sbs_id=invoice_summary.sbs_id WHERE ship_bill_summary.iec LIKE '%$iec'  AND ship_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";


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
            if ($this->isDuplicateEntry_dynamic_item_manufacturer_details($detail, $users_id)) {
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
        
        if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM item_manufacturer_details 
                             WHERE item_manufact_id='{$detail['item_manufact_id']}'  AND  item_id='{$detail['item_id']}'  AND inv_sno='{$detail['inv_sno']}'  AND item_sno='{$detail['item_sno']}' ";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
    
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
/*********************************************************************End item_manufacturer_details************************************************************************************/


/*********************************************************************invoice_summery************************************************************************************/

public function invoice_summery(){    
      
       // Load secondary database
         $this->load->database('second');

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
         $totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage); 
        $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();

            foreach ($admin_users as $user) {
                 $iec = $user['iec_no'];
  
        $query_invoice_summery ="SELECT invoice_summary.*,ship_bill_summary.iec,ship_bill_summary.sbs_id FROM invoice_summary JOIN ship_bill_summary 
        ON invoice_summary.sbs_id=ship_bill_summary.sbs_id  Where ship_bill_summary.iec Like '%$iec' AND ship_bill_summary.created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";


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
        
                        $date_fields = ['inv_date'];
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
        if (!empty($valid_query_details)) 
        { 
            $unwanted_fields = [];
            $table_name='invoice_summary';
            $filtered_query_details = $this->filterDuplicateEntries_dynamic_invoice_summery($valid_query_details,$users_id,$unwanted_fields);
            
            if (!empty($filtered_query_details)) 
             {  
                 echo "=====================";
                echo $users_id;
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
           if ($this->isDuplicateEntry_dynamic_invoice_summery($detail, $users_id)) { 
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
        where  invoice_summary.invoice_id='{$detail['invoice_id']}'  ";
        // Execute the duplicate query and fetch the result
        $result = $db_secondary->query($duplicate_query)->row_array();
       if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM invoice_summary 
                             WHERE invoice_id='{$detail['invoice_id']}' ";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
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
/*********************************************************************end invoice_summery************************************************************************************/
 
 
/*********************************************************************sb_file_status************************************************************************************/
    
public function sb_file_status()
{

// Load secondary database
$this->load->database('second');

// Fetch admin  users with paginated queries to handle large datasets
$perPage = 50; // Adjust based on memory and performance testing
$totalUsers = $this->db->query("SELECT COUNT(*) as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
$pages = ceil($totalUsers / $perPage); $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

for ($page = 0; $page < $pages; $page++) {
$offset = $page * $perPage;
$adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin'   ORDER BY lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
$admin_users = $this->db->query($adminUsersQuery)->result_array();

foreach ($admin_users as $user) {
     $iec = $user['iec_no'];
    $bill_of_entry_summary_query = "SELECT * FROM sb_file_status Where user_iec_no Like '%$iec'  AND created_at >= TO_TIMESTAMP('$threeDaysAgo 00:00:00', 'YYYY-MM-DD HH24:MI:SS')";



    $this->processUser_dynamic_sb_file_status($user,$bill_of_entry_summary_query);
}
}

// Close the main database connection
$this->db->close();


}

private function processUser_dynamic_sb_file_status($user,$query)
{
$iec = $user["iec_no"];
$users_id = $user["lucrative_users_id"];

// Fetch licence details efficiently

$query_details = $this->db->query($query)->result_array();
if (!empty($query_details)) {
       // Define the valid columns to be inserted into aa_dfia_licence_details
         // List of fields to process and escape

         
           $valid_columns = ['sb_file_status_id','pdf_filepath', 'pdf_filename', 'user_iec_no', 'lucrative_users_id', 'file_iec_no', 'sb_no','sb_date','stage','status','remarks','created_at','br','is_processed'];
           // Define unwanted field s
           // Process and filter the query details to retain only valid columns
           $valid_query_details = array_map(function ($query_details) use ($valid_columns) {
           // Convert 'debt_amt' to a numeric value or default to zero
           // Validate and format numeric fields
                $numeric_fields = ['lucrative_users_id',];
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
            $date_fields = ['created_at','sb_date'];
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
         $table_name='sb_file_status';


            $filtered_query_details = $this->filterDuplicateEntries_dynamic_sb_file_status($valid_query_details,$users_id,$unwanted_fields);
      
             if (!empty($filtered_query_details)) {   echo "=====================";echo $users_id;
            $this->insertLicenceDetailsBatch_dynamic_sb_file_status($filtered_query_details, $users_id,$table_name);
             }
}
}
}
private function filterDuplicateEntries_dynamic_sb_file_status($valid_query_details, $users_id,$unwanted_fields)
{
$filtered_details = [];

foreach ($valid_query_details as $detail) {
try {
// Use more efficient duplicate checking mechanisms if possible
if (!$this->isDuplicateEntry_dynamic_sb_file_status($detail, $users_id)) {
    // Exclude unwanted fields before adding to filtered_details
    $filtered_detail = $this->excludeUnwantedFields_dynamic_sb_file_status($detail,$unwanted_fields);
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
private function isDuplicateEntry_dynamic_sb_file_status($detail, $users_id)
{
try {
  
    $db_secondary = $this->load_secondary_database($users_id);
    $duplicate_query = "SELECT COUNT(*) AS num_rows  FROM sb_file_status where pdf_filepath ='{$detail['pdf_filepath']}'  and pdf_filename ='{$detail['pdf_filename']}'";

        $result = $db_secondary->query($duplicate_query)->row_array();


 if ($result['num_rows'] > 0) {  echo "=====================";echo $users_id;
            // Delete existing duplicate entries based on the criteria
       echo     $delete_query = "DELETE FROM sb_file_status where pdf_filepath ='{$detail['pdf_filepath']}'  and pdf_filename ='{$detail['pdf_filename']}'";

            $db_secondary->query($delete_query);
            }
            // Close secondary database connection
            $db_secondary->close();
            // Check if there are duplicate rows based on the query result
            return true;
} catch (Exception $e) {
// Handle database error
log_message('error', 'Database Error in isDuplicateEntry_dynamic: ' . $e->getMessage());
return false; // Return false indicating error occurred
}
}
private function validateQueryDetailColumns_dynamic_sb_file_status($detail,$valid_columns)
{
return array_intersect_key($detail, array_flip($valid_columns)) == $detail;
}




private function excludeUnwantedFields_dynamic_sb_file_status($detail,$unwanted_fields)
{

// Filter out unwanted fields from $detail array
foreach ($unwanted_fields as $field) {
unset($detail[$field]);
}

return $detail;
}


private function insertLicenceDetailsBatch_dynamic_sb_file_status($licence_details, $users_id,$table_name)
{
$db_secondary = $this->load_secondary_database($users_id); 

try {
$db_secondary->insert_batch($table_name, $licence_details);
} catch (Exception $e) {
echo 'Database Error: ' . $e->getMessage();
}

$db_secondary->close();
} 

/*********************************************************************end sb_file_status************************************************************************************/
 
 
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
            $db_name = 'lucrativeesystem_D2D_S'.$result;
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
              `dfia_licence_details_id` int(20) NOT NULL  PRIMARY KEY,
              `item_id` int(20) DEFAULT NULL,
              `inv_s_no` int(20) DEFAULT NULL,
              `item_s_no_` int(20) DEFAULT NULL,
              `licence_no` text,
              `descn_of_export_item` text,
              `exp_s_no` int(20) DEFAULT NULL,
              `expqty` text,
              `uqc_aa` varchar(350) DEFAULT NULL,
              `fob_value` text,
              `sion` text,
              `descn_of_import_item` text,
              `imp_s_no` varchar(350) DEFAULT NULL,
              `impqt` text,
              `uqc_` varchar(350) DEFAULT NULL,
              `indig_imp` varchar(350) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `a` text DEFAULT NULL,
              `b` text DEFAULT NULL,
              `c` text DEFAULT NULL,
              `d` text DEFAULT NULL,
              `e` text DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
             
    /*****************************************Create Table bill_bond_details***************************************************************/
               $db2->query("CREATE TABLE `bill_bond_details` (
                          `bond_details_id` int(20)  NOT NULL  PRIMARY KEY,
                          `boe_id` int(20) NOT NULL,
                          `bond_no` text DEFAULT NULL,
                          `port` text DEFAULT NULL,
                          `bond_cd` text DEFAULT NULL,
                          `debt_amt` float(25,2) DEFAULT NULL,
                          `bg_amt` float(25,2) DEFAULT NULL,
                          `created_at` datetime DEFAULT NULL,
                          `a` text DEFAULT NULL,
                          `b` text DEFAULT NULL,
                          `c` text DEFAULT NULL,
                          `d` text DEFAULT NULL,
                          `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
           /*****************************************Create Table bill_container_details***************************************************************/
               $db2->query("CREATE TABLE `bill_container_details` (
                          `container_details_id` int(20) NOT NULL  PRIMARY KEY,
                          `boe_id` int(20) NOT NULL,
                          `sno` int(20) DEFAULT NULL,
                          `lcl_fcl` varchar(350) DEFAULT NULL,
                          `truck` varchar(350) DEFAULT NULL,
                          `seal` text DEFAULT NULL,
                          `container_number` varchar(350) DEFAULT NULL,
                          `created_at` datetime DEFAULT NULL,
                          `a` text DEFAULT NULL,
                          `b` text DEFAULT NULL,
                          `c` text DEFAULT NULL,
                          `d` text DEFAULT NULL,
                          `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
             
                /*****************************************Create Table bill_licence_details***************************************************************/
               $db2->query("CREATE TABLE `bill_licence_details` (
                                  `licence_id` int(20) NOT NULL  PRIMARY KEY,
                                  `duties_id` int(20) DEFAULT NULL,
                                  `invsno` int(20) DEFAULT NULL,
                                  `itemsn` int(20) DEFAULT NULL,
                                  `lic_slno` int(20) DEFAULT NULL,
                                  `lic_no` varchar(350) DEFAULT NULL,
                                  `lic_date` date DEFAULT NULL,
                                  `code` varchar(350) DEFAULT NULL,
                                  `port` varchar(700) DEFAULT NULL,
                                  `debit_value` float(25,2) DEFAULT NULL,
                                  `qty` varchar(20) DEFAULT NULL,
                                  `uqc_lc_d` varchar(20) DEFAULT NULL,
                                  `debit_duty` varchar(500) DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `a` text DEFAULT NULL,
                                  `b` text DEFAULT NULL,
                                  `c` text DEFAULT NULL,
                                  `d` text DEFAULT NULL,
                                  `e` text DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

        /********************************************************************************************************/  
        
       /*****************************************Create Table bill_manifest_details***************************************************************/
               $db2->query("CREATE TABLE `bill_manifest_details` (
                              `manifest_details_id` int(20) NOT NULL  PRIMARY KEY,
                              `boe_id` int(20) NOT NULL,
                              `igm_no` text DEFAULT NULL,
                              `igm_date` date DEFAULT NULL,
                              `inw_date` date DEFAULT NULL,
                              `gigmno` text DEFAULT NULL,
                              `gigmdt` date DEFAULT NULL,
                              `mawb_no` text DEFAULT NULL,
                              `mawb_date` date DEFAULT NULL,
                              `hawb_no` text DEFAULT NULL,
                              `hawb_date` date DEFAULT NULL,
                              `pkg` int(20) DEFAULT NULL,
                              `gw` int(20) DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
               /*****************************************Create Table bill_of_entry_summary***************************************************************/
               $db2->query("CREATE TABLE `bill_of_entry_summary` (
                                  `boe_id` int(20)  NOT NULL  PRIMARY KEY,
                                  `boe_file_status_id` int(20) DEFAULT NULL,
                                  `invoice_title` text DEFAULT NULL,
                                  `port` text DEFAULT NULL,
                                  `port_code` text DEFAULT NULL,
                                  `be_no` text DEFAULT NULL,
                                  `be_date` date DEFAULT NULL,
                                  `be_type` text DEFAULT NULL,
                                  `iec_br` text DEFAULT NULL,
                                  `iec_no` text DEFAULT NULL,
                                  `br` text DEFAULT NULL,
                                  `gstin_type` text DEFAULT NULL,
                                  `cb_code` text DEFAULT NULL,
                                  `ad_code` text DEFAULT NULL,
                                  `nos` int(20) DEFAULT NULL,
                                  `pkg` int(20) DEFAULT NULL,
                                  `item` int(20) DEFAULT NULL,
                                  `g_wt_kgs` int(20) DEFAULT NULL,
                                  `cont` int(20) DEFAULT NULL,
                                  `be_status` text DEFAULT NULL,
                                  `mode` text DEFAULT NULL,
                                  `def_be` text DEFAULT NULL,
                                  `kacha` text DEFAULT NULL,
                                  `sec_48` text DEFAULT NULL,
                                  `reimp` text DEFAULT NULL,
                                  `adv_be` text DEFAULT NULL,
                                  `assess` text DEFAULT NULL,
                                  `exam` text DEFAULT NULL,
                                  `hss` text DEFAULT NULL,
                                  `first_check` text DEFAULT NULL,
                                  `prov_final` text DEFAULT NULL,
                                  `country_of_origin` text DEFAULT NULL,
                                  `country_of_consignment` text DEFAULT NULL,
                                  `port_of_loading` text DEFAULT NULL,
                                  `port_of_shipment` text DEFAULT NULL,
                                  `importer_name_and_address` text DEFAULT NULL,
                                  `cb_name` text DEFAULT NULL,
                                  `aeo` text DEFAULT NULL,
                                  `ucr` text DEFAULT NULL,
                                  `bcd` float(25,2) DEFAULT NULL,
                                  `acd` float(25,2) DEFAULT NULL,
                                  `sws` float(25,2) DEFAULT NULL,
                                  `nccd` float(25,2) DEFAULT NULL,
                                  `add` float(25,2) DEFAULT NULL,
                                  `cvd` float(25,2) DEFAULT NULL,
                                  `igst` float(25,2) DEFAULT NULL,
                                  `g_cess` float(25,2) DEFAULT NULL,
                                  `sg` float(25,2) DEFAULT NULL,
                                  `saed` float(25,2) DEFAULT NULL,
                                  `gsia` float(25,2) DEFAULT NULL,
                                  `tta` float(25,2) DEFAULT NULL,
                                  `health` float(25,2) DEFAULT NULL,
                                  `total_duty` float(25,2) DEFAULT NULL,
                                  `int` float(25,2) DEFAULT NULL,
                                  `pnlty` float(25,2) DEFAULT NULL,
                                  `fine` float(25,2) DEFAULT NULL,
                                  `tot_ass_val` float(25,2) DEFAULT NULL,
                                  `tot_amount` float(25,2) DEFAULT NULL,
                                  `wbe_no` text DEFAULT NULL,
                                  `wbe_date` date DEFAULT NULL,
                                  `wbe_site` text DEFAULT NULL,
                                  `wh_code` text DEFAULT NULL,
                                  `submission_date` date DEFAULT NULL,
                                  `assessment_date` date DEFAULT NULL,
                                  `examination_date` date DEFAULT NULL,
                                  `ooc_date` date DEFAULT NULL,
                                  `submission_time` text DEFAULT NULL,
                                  `assessment_time` text DEFAULT NULL,
                                  `examination_time` text DEFAULT NULL,
                                  `ooc_time` text DEFAULT NULL,
                                  `submission_exchange_rate` text DEFAULT NULL,
                                  `assessment_exchange_rate` text DEFAULT NULL,
                                  `ooc_no` text DEFAULT NULL,
                                  `ooc_date_` date DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `examination_exchange_rate` text NOT NULL,
                                  `ooc_exchange_rate` text NOT NULL,
                                  `a` text DEFAULT NULL,
                                  `b` text DEFAULT NULL,
                                  `c` text DEFAULT NULL,
                                  `d` text DEFAULT NULL,
                                  `e` text DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/
        
        
               /*****************************************Create Table bill_payment_details***************************************************************/
               $db2->query("CREATE TABLE `bill_payment_details` (
                              `payment_details_id` int(20)  NOT NULL  PRIMARY KEY,
                              `boe_id` int(20) NOT NULL,
                              `sr_no` int(20) DEFAULT NULL,
                              `challan_no` text DEFAULT NULL,
                              `paid_on` date DEFAULT NULL,
                              `amount` float(25,2) DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
            /*****************************************Create Table boe_delete_logs***************************************************************/
               $db2->query("CREATE TABLE `boe_delete_logs` (
                           `boe_delete_logs_id` int(20) NOT NULL  PRIMARY KEY,
                          `filename` text DEFAULT NULL,
                          `be_no` text DEFAULT NULL,
                          `be_date` datetime DEFAULT NULL,
                          `iec_no` text DEFAULT NULL,
                          `br` text DEFAULT NULL,
                          `fullname` text DEFAULT NULL,
                          `email` text DEFAULT NULL,
                          `mobile` text DEFAULT NULL,
                          `deleted_at` datetime DEFAULT NULL,
                          `a` text DEFAULT NULL,
                          `b` text DEFAULT NULL,
                          `c` text DEFAULT NULL,
                          `d` text DEFAULT NULL,
                          `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  

            /*****************************************Create Table boe_file_status***************************************************************/
        
            $db2->query("CREATE TABLE `boe_file_status` (
                              `boe_file_status_id` int(20) NOT NULL  PRIMARY KEY,
                              `pdf_filepath` text DEFAULT NULL,
                              `pdf_filename` text DEFAULT NULL,
                              `user_iec_no` text DEFAULT NULL,
                              `lucrative_users_id` int(20) DEFAULT NULL,
                              `excel_filepath` text DEFAULT NULL,
                              `excel_filename` text DEFAULT NULL,
                              `pdf_to_excel_date` datetime DEFAULT NULL,
                              `pdf_to_excel_status` text DEFAULT NULL,
                              `file_iec_no` text DEFAULT NULL,
                              `br` text DEFAULT NULL,
                              `be_no` text DEFAULT NULL,
                              `stage` text DEFAULT NULL,
                              `status` text DEFAULT NULL,
                              `remarks` text DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `is_deleted` tinyint(20) DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL,
                              `is_processed` varchar(1) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
                    /*****************************************Create Table cb_file_status***************************************************************/
        
                        $db2->query("CREATE TABLE `cb_file_status` (
                             `cb_file_status_id` int(20)  NOT NULL  PRIMARY KEY,
                              `pdf_filepath` text DEFAULT NULL,
                              `pdf_filename` text DEFAULT NULL,
                              `user_iec_no` text DEFAULT NULL,
                              `lucrative_users_id` int(20) DEFAULT NULL,
                              `file_iec_no` text DEFAULT NULL,
                              `cb_no` text DEFAULT NULL,
                              `cb_date` date DEFAULT NULL,
                              `stage` text DEFAULT NULL,
                              `status` text DEFAULT NULL,
                              `remarks` text DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `br` text DEFAULT NULL,
                              `is_processed` text DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
       
            /*****************************************Create Table challan_details***************************************************************/
        
                        $db2->query("CREATE TABLE `challan_details` (
                                          `challan_id` int(20)  NOT NULL PRIMARY KEY,
                                          `sbs_id` int(20) DEFAULT NULL,
                                          `sr_no` text,
                                          `challan_no` text,
                                          `paymt_dt`  date DEFAULT NULL,
                                          `amount` text,
                                          `created_at` datetime DEFAULT NULL,
                                          `a` text DEFAULT NULL,
                                          `b` text DEFAULT NULL,
                                          `c` text DEFAULT NULL,
                                          `d` text DEFAULT NULL,
                                          `e` text DEFAULT NULL
                                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
             /*****************************************Create Table courier_bill_container_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_container_details` (
                                      `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                                      `container_details_id` int(20) NOT NULL PRIMARY KEY,
                                      `container_details_srno` int(20) DEFAULT NULL,
                                      `container` text DEFAULT NULL,
                                      `seal_number` text DEFAULT NULL,
                                      `fcl_lcl` text DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
           /*****************************************Create Table courier_bill_bond_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_bond_details` (
                                      `courier_bill_of_entry_id` int(20) DEFAULT NULL ,
                                      `bond_details_id` int(20) NOT NULL  PRIMARY KEY ,
                                      `bond_details_srno` int(20) DEFAULT NULL,
                                      `bond_type` text DEFAULT NULL,
                                      `bond_number` text DEFAULT NULL,
                                      `clearance_of_imported_goods_bond_already_registered_customs` text DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
         /*****************************************Create Table courier_bill_duty_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_duty_details` (
                                      `items_detail_id` int(20) DEFAULT NULL ,
                                      `duty_details_id` int(20) NOT NULL  PRIMARY KEY,
                                      `bcd_duty_head` text DEFAULT NULL,
                                      `bcd_ad_valorem` float(25,2) DEFAULT NULL,
                                      `bcd_specific_rate` int(20) DEFAULT NULL,
                                      `bcd_duty_forgone` int(20) DEFAULT NULL,
                                      `bcd_duty_amount` int(20) DEFAULT NULL,
                                      `aidc_duty_head` text DEFAULT NULL,
                                      `aidc_ad_valorem` int(20) DEFAULT NULL,
                                      `aidc_specific_rate` int(20) DEFAULT NULL,
                                      `aidc_duty_forgone` int(20) DEFAULT NULL,
                                      `aidc_duty_amount` int(20) DEFAULT NULL,
                                      `sw_srchrg_duty_head` text DEFAULT NULL,
                                      `sw_srchrg_ad_valorem` int(20) DEFAULT NULL,
                                      `sw_srchrg_specific_rate` int(20) DEFAULT NULL,
                                      `sw_srchrg_duty_forgone` int(20) DEFAULT NULL,
                                      `sw_srchrg_duty_amount` int(20) DEFAULT NULL,
                                      `igst_duty_head` text DEFAULT NULL,
                                      `igst_ad_valorem` int(20) DEFAULT NULL,
                                      `igst_specific_rate` int(20) DEFAULT NULL,
                                      `igst_duty_forgone` int(20) DEFAULT NULL,
                                      `igst_duty_amount` int(20) DEFAULT NULL,
                                      `cmpnstry_duty_head` text DEFAULT NULL,
                                      `cmpnstry_ad_valorem` int(20) DEFAULT NULL,
                                      `cmpnstry_specific_rate` int(20) DEFAULT NULL,
                                      `cmpnstry_duty_forgone` int(20) DEFAULT NULL,
                                      `cmpnstry_duty_amount` int(20) DEFAULT NULL,
                                      `dummy5_duty_head` text DEFAULT NULL,
                                      `dummy5_ad_valorem` text DEFAULT NULL,
                                      `dummy5_specific_rate` text DEFAULT NULL,
                                      `dummy5_duty_forgone` text DEFAULT NULL,
                                      `dummy5_duty_amount` text DEFAULT NULL,
                                      `dummy6_duty_head` text DEFAULT NULL,
                                      `dummy6_ad_valorem` text DEFAULT NULL,
                                      `dummy6_specific_rate` text DEFAULT NULL,
                                      `dummy6_duty_forgone` text DEFAULT NULL,
                                      `dummy6_duty_amount` text DEFAULT NULL,
                                      `dummy7_duty_head` text DEFAULT NULL,
                                      `dummy7_ad_valorem` text DEFAULT NULL,
                                      `dummy7_specific_rate` text DEFAULT NULL,
                                      `dummy7_duty_forgone` text DEFAULT NULL,
                                      `dummy7_duty_amount` text DEFAULT NULL,
                                      `dummy8_duty_head` text DEFAULT NULL,
                                      `dummy8_ad_valorem` text DEFAULT NULL,
                                      `dummy8_specific_rate` text DEFAULT NULL,
                                      `dummy8_duty_forgone` text DEFAULT NULL,
                                      `dummy8_duty_amount` text DEFAULT NULL,
                                      `dummy9_duty_head` text DEFAULT NULL,
                                      `dummy9_ad_valorem` text DEFAULT NULL,
                                      `dummy9_specific_rate` text DEFAULT NULL,
                                      `dummy9_duty_forgone` text DEFAULT NULL,
                                      `dummy9_duty_amount` text DEFAULT NULL,
                                      `dummy10_duty_head` text DEFAULT NULL,
                                      `dummy10_ad_valorem` text DEFAULT NULL,
                                      `dummy10_specific_rate` text DEFAULT NULL,
                                      `dummy10_duty_forgone` text DEFAULT NULL,
                                      `dummy10_duty_amount` text DEFAULT NULL,
                                      `dummy11_duty_head` text DEFAULT NULL,
                                      `dummy11_ad_valorem` text DEFAULT NULL,
                                      `dummy11_specific_rate` text DEFAULT NULL,
                                      `dummy11_duty_forgone` text DEFAULT NULL,
                                      `dummy11_duty_amount` text DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/ 
        
        
          /*****************************************Create Table courier_bill_igm_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_igm_details` (
                                      `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                                      `igm_details_id` int(20) NOT NULL  PRIMARY KEY,
                                      `airlines` text DEFAULT NULL,
                                      `flight_no` text DEFAULT NULL,
                                      `airport_of_arrival` text DEFAULT NULL,
                                      `date_of_arrival` date DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
          /*****************************************Create Table courier_bill_invoice_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_invoice_details` (
                                     `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                                    `invoice_detail_id` int(20) NOT NULL  PRIMARY KEY,
                                    `invoice_number` int(20) DEFAULT NULL,
                                    `date_of_invoice` date DEFAULT NULL,
                                    `purchase_order_number` text DEFAULT NULL,
                                    `date_of_purchase_order` date DEFAULT NULL,
                                    `contract_number` text DEFAULT NULL,
                                    `date_of_contract` date DEFAULT NULL,
                                    `letter_of_credit` text DEFAULT NULL,
                                    `date_of_letter_of_credit` date DEFAULT NULL,
                                    `supplier_details_name` text DEFAULT NULL,
                                    `supplier_details_address` text DEFAULT NULL,
                                    `if_supplier_is_not_the_seller_name` text DEFAULT NULL,
                                    `if_supplier_is_not_the_seller_address` text DEFAULT NULL,
                                    `broker_agent_details_name` text DEFAULT NULL,
                                    `broker_agent_details_address` text DEFAULT NULL,
                                    `nature_of_transaction` text DEFAULT NULL,
                                    `if_others` text DEFAULT NULL,
                                    `terms_of_payment` text DEFAULT NULL,
                                    `conditions_or_restrictions_if_any_attached_to_sale` text DEFAULT NULL,
                                    `method_of_valuation` text DEFAULT NULL,
                                    `terms_of_invoice` text DEFAULT NULL,
                                    `invoice_value` int(20) DEFAULT NULL,
                                    `currency` text DEFAULT NULL,
                                    `freight_rate` float(25,2) DEFAULT NULL,
                                    `freight_amount` float(25,2) DEFAULT NULL,
                                    `freight_currency` text DEFAULT NULL,
                                    `insurance_rate` float(25,2) DEFAULT NULL,
                                    `insurance_amount` float(25,2) DEFAULT NULL,
                                    `insurance_currency` text DEFAULT NULL,
                                    `loading_unloading_and_handling_charges_rule_rate` text DEFAULT NULL,
                                    `loading_unloading_and_handling_charges_rule_amount` text DEFAULT NULL,
                                    `loading_unloading_and_handling_charges_rule_currency` text DEFAULT NULL,
                                    `other_charges_related_to_the_carriage_of_goods_rate` text DEFAULT NULL,
                                    `other_charges_related_to_the_carriage_of_goods_amount` text DEFAULT NULL,
                                    `other_charges_related_to_the_carriage_of_goods_currency` text DEFAULT NULL,
                                    `brokerage_and_commission_rate` text DEFAULT NULL,
                                    `brokerage_and_commission_amount` text DEFAULT NULL,
                                    `brokerage_and_commission_currency` text DEFAULT NULL,
                                    `cost_of_containers_rate` text DEFAULT NULL,
                                    `cost_of_containers_amount` text DEFAULT NULL,
                                    `cost_of_containers_currency` text DEFAULT NULL,
                                    `cost_of_packing_rate` text DEFAULT NULL,
                                    `cost_of_packing_amount` text DEFAULT NULL,
                                    `cost_of_packing_currency` text DEFAULT NULL,
                                    `dismantling_transport_handling_in_country_export_rate` text DEFAULT NULL,
                                    `dismantling_transport_handling_in_country_export_amount` text DEFAULT NULL,
                                    `dismantling_transport_handling_in_country_export_currency` text DEFAULT NULL,
                                    `cost_of_goods_and_ser_vices_supplied_by_buyer_rate` text DEFAULT NULL,
                                    `cost_of_goods_and_ser_vices_supplied_by_buyer_amount` text DEFAULT NULL,
                                    `cost_of_goods_and_ser_vices_supplied_by_buyer_currency` text DEFAULT NULL,
                                    `documentation_rate` text DEFAULT NULL,
                                    `documentation_amount` text DEFAULT NULL,
                                    `documentation_currency` text DEFAULT NULL,
                                    `country_of_origin_certificate_rate` text DEFAULT NULL,
                                    `country_of_origin_certificate_amount` text DEFAULT NULL,
                                    `country_of_origin_certificate_currency` text DEFAULT NULL,
                                    `royalty_and_license_fees_rate` text DEFAULT NULL,
                                    `royalty_and_license_fees_amount` text DEFAULT NULL,
                                    `royalty_and_license_fees_currency` text DEFAULT NULL,
                                    `value_of_proceeds_which_accrue_to_seller_rate` text DEFAULT NULL,
                                    `value_of_proceeds_which_accrue_to_seller_amount` text DEFAULT NULL,
                                    `value_of_proceeds_which_accrue_to_seller_currency` text DEFAULT NULL,
                                    `cost_warranty_service_if_any_provided_seller_rate` text DEFAULT NULL,
                                    `cost_warranty_service_if_any_provided_seller_amount` text DEFAULT NULL,
                                    `cost_warranty_service_if_any_provided_seller_currency` text DEFAULT NULL,
                                    `other_payments_satisfy_obligation_rate` text DEFAULT NULL,
                                    `other_payments_satisfy_obligation_amount` text DEFAULT NULL,
                                    `other_payments_satisfy_obligation_currency` text DEFAULT NULL,
                                    `other_charges_and_payments_if_any_rate` int(20) DEFAULT NULL,
                                    `other_charges_and_payments_if_any_amount` int(20) DEFAULT NULL,
                                    `other_charges_and_payments_if_any_currency` text DEFAULT NULL,
                                    `discount_amount` text DEFAULT NULL,
                                    `discount_currency` text DEFAULT NULL,
                                    `rate` text DEFAULT NULL,
                                    `amount` text DEFAULT NULL,
                                    `any_other_information_which_has_a_bearing_on_value` text DEFAULT NULL,
                                    `are_the_buyer_and_seller_related` text DEFAULT NULL,
                                    `if_the_buyer_seller_has_the_relationship_examined_earlier_svb` text DEFAULT NULL,
                                    `svb_reference_number` text DEFAULT NULL,
                                    `svb_date` date DEFAULT NULL,
                                    `indication_for_provisional_final` text DEFAULT NULL,
                                    `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
               /*****************************************Create Table courier_bill_items_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_items_details` (
                                     `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                                      `items_detail_id` int(20) NOT NULL PRIMARY KEY,
                                      `case_for_reimport` text DEFAULT NULL,
                                      `import_against_license` text DEFAULT NULL,
                                      `serial_number_in_invoice` text DEFAULT NULL,
                                      `item_description` text DEFAULT NULL,
                                      `general_description` text DEFAULT NULL,
                                      `currency_for_unit_price` text DEFAULT NULL,
                                      `unit_price` int(20) DEFAULT NULL,
                                      `unit_of_measure` text DEFAULT NULL,
                                      `quantity` int(20) DEFAULT NULL,
                                      `rate_of_exchange` float(25,2) DEFAULT NULL,
                                      `accessories_if_any` text DEFAULT NULL,
                                      `name_of_manufacturer` text DEFAULT NULL,
                                      `brand` text DEFAULT NULL,
                                      `model` text DEFAULT NULL,
                                      `grade` text DEFAULT NULL,
                                      `specification` text DEFAULT NULL,
                                      `end_use_of_item` text DEFAULT NULL,
                                      `items_details_country_of_origin` text DEFAULT NULL,
                                      `bill_of_entry_number` text DEFAULT NULL,
                                      `details_in_case_of_previous_imports_date` text DEFAULT NULL,
                                      `details_in_case_previous_imports_currency` text DEFAULT NULL,
                                      `unit_value` text DEFAULT NULL,
                                      `customs_house` text DEFAULT NULL,
                                      `ritc` int(20) DEFAULT NULL,
                                      `ctsh` int(20) DEFAULT NULL,
                                      `cetsh` int(20) DEFAULT NULL,
                                      `currency_for_rsp` text DEFAULT NULL,
                                      `retail_sales_price_per_unit` text DEFAULT NULL,
                                      `exim_scheme_code_if_any` text DEFAULT NULL,
                                      `para_noyear_of_exim_policy` text DEFAULT NULL,
                                      `items_details_are_the_buyer_and_seller_related` text DEFAULT NULL,
                                      `if_the_buyer_and_seller_relation_examined_earlier_by_svb` text DEFAULT NULL,
                                      `items_details_svb_reference_number` text DEFAULT NULL,
                                      `items_details_svb_date` date DEFAULT NULL,
                                      `items_details_indication_for_provisional_final` text DEFAULT NULL,
                                      `shipping_bill_number` text DEFAULT NULL,
                                      `shipping_bill_date` date DEFAULT NULL,
                                      `port_of_export` text DEFAULT NULL,
                                      `invoice_number_of_shipping_bill` text DEFAULT NULL,
                                      `item_serial_number_in_shipping_bill` text DEFAULT NULL,
                                      `freight` text DEFAULT NULL,
                                      `insurance` text DEFAULT NULL,
                                      `total_repair_cost_including_cost_of_materials` text DEFAULT NULL,
                                      `additional_duty_exemption_requested` text DEFAULT NULL,
                                      `items_details_notification_number` text DEFAULT NULL,
                                      `serial_number_in_notification` text DEFAULT NULL,
                                      `license_registration_number` text DEFAULT NULL,
                                      `license_registration_date` text DEFAULT NULL,
                                      `debit_value_rs` text DEFAULT NULL,
                                      `unit_of_measure_for_quantity_to_be_debited` text DEFAULT NULL,
                                      `debit_quantity` text DEFAULT NULL,
                                      `item_serial_number_in_license` text DEFAULT NULL,
                                      `assessable_value` float(25,2) DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
        
        /*****************************************Create Table courier_bill_manifest_details***************************************************************/
        
                        $db2->query("CREATE TABLE `courier_bill_manifest_details` (
                                      `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                                      `manifest_details_id` int(20) NOT NULL PRIMARY KEY ,
                                      `import_general_manifest_igm_number` text DEFAULT NULL,
                                      `date_of_entry_inward` date DEFAULT NULL,
                                      `master_airway_bill_mawb_number` bigint(20) DEFAULT NULL,
                                      `date_of_mawb` date DEFAULT NULL,
                                      `house_airway_bill_hawb_number` bigint(20) DEFAULT NULL,
                                      `date_of_hawb` date DEFAULT NULL,
                                      `marks_and_numbers` int(20) DEFAULT NULL,
                                      `number_of_packages` int(20) DEFAULT NULL,
                                      `type_of_packages` text DEFAULT NULL,
                                      `interest_amount` int(20) DEFAULT NULL,
                                      `unit_of_measure_for_gross_weight` text DEFAULT NULL,
                                      `gross_weight` float(25,2) DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                      `a` text DEFAULT NULL,
                                      `b` text DEFAULT NULL,
                                      `c` text DEFAULT NULL,
                                      `d` text DEFAULT NULL,
                                      `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
        
        /*****************************************Create Table courier_bill_notification_used_for_items***************************************************************/
               $db2->query("CREATE TABLE `courier_bill_notification_used_for_items` (
                          `items_detail_id` int(20) DEFAULT NULL,
                          `item_notification_id` int(20) NOT NULL PRIMARY KEY,
                          `notification_item_srno` int(20) DEFAULT NULL,
                          `notification_number` text DEFAULT NULL,
                          `serial_number_of_notification` text DEFAULT NULL,
                          `created_at` datetime DEFAULT NULL,
                          `a` text DEFAULT NULL,
                          `b` text DEFAULT NULL,
                          `c` text DEFAULT NULL,
                          `d` text DEFAULT NULL,
                          `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
          
        /*****************************************Create Table courier_bill_payment_details***************************************************************/
               $db2->query("CREATE TABLE `courier_bill_payment_details` (
                              `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                              `payment_details_id` int(20) NOT NULL  PRIMARY KEY,
                              `payment_details_srno` int(20) DEFAULT NULL,
                              `tr6_challan_number` bigint(19) DEFAULT NULL,
                              `total_amount` int(20) DEFAULT NULL,
                              `challan_date` date DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

             /********************************************************************************************************/  
        
            /*****************************************Create Table courier_bill_procurment_details***************************************************************/
               $db2->query("CREATE TABLE `courier_bill_procurment_details` (
                                  `courier_bill_of_entry_id` int(20) DEFAULT NULL,
                                  `procurment_details_id` int(20) NOT NULL  PRIMARY KEY,
                                  `procurement_under_3696_cus` text DEFAULT NULL,
                                  `procurement_certificate_number` text DEFAULT NULL,
                                  `date_of_issuance_of_certificate` date DEFAULT NULL,
                                  `location_code_of_the_cent_ral_excise_office_issuing_the_certifi` text DEFAULT NULL,
                                  `commissione_rate` text DEFAULT NULL,
                                  `division` text DEFAULT NULL,
                                  `range` text DEFAULT NULL,
                                  `import_under_multiple_in_voices` text DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `a` text DEFAULT NULL,
                                  `b` text DEFAULT NULL,
                                  `c` text DEFAULT NULL,
                                  `d` text DEFAULT NULL,
                                  `e` text DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/  
   
       
               /*****************************************Create Table courier_bill_summary***************************************************************/
                 $db2->query("CREATE TABLE `courier_bill_summary` (
                      `courier_bill_of_entry_id` int(20) NOT NULL PRIMARY KEY,
                      `cb_file_status_id` int(20) DEFAULT NULL,
                      `current_status_of_the_cbe` varchar(29) DEFAULT NULL,
                      `cbexiv_number` text DEFAULT NULL,
                      `courier_registration_number` text DEFAULT NULL,
                      `name_of_the_authorized_courier` text DEFAULT NULL,
                      `address_of_authorized_courier` text DEFAULT NULL,
                      `particulars_customs_house_agent_name` text DEFAULT NULL,
                      `particulars_customs_house_agent_licence_no` text DEFAULT NULL,
                      `particulars_customs_house_agent_address` text DEFAULT NULL,
                      `import_export_code` text DEFAULT NULL,
                      `import_export_branch_code` int(20) DEFAULT NULL,
                      `particulars_of_the_importer_name` text DEFAULT NULL,
                      `particulars_of_the_importer_address` text DEFAULT NULL,
                      `category_of_importer` text DEFAULT NULL,
                      `type_of_importer` text DEFAULT NULL,
                      `in_case_of_other_importer` text DEFAULT NULL,
                      `authorised_dealer_code_of_bank` int(20) DEFAULT NULL,
                      `class_code` text DEFAULT NULL,
                      `cb_no` text DEFAULT NULL,
                      `cb_date` date DEFAULT NULL,
                      `category_of_boe` text DEFAULT NULL,
                      `type_of_boe` text DEFAULT NULL,
                      `kyc_document` text DEFAULT NULL,
                      `kyc_id` text DEFAULT NULL,
                      `state_code` int(20) DEFAULT NULL,
                      `high_sea_sale` text DEFAULT NULL,
                      `ie_code_of_hss` text DEFAULT NULL,
                      `ie_branch_code_of_hss` text DEFAULT NULL,
                      `particulars_high_sea_seller_name` text DEFAULT NULL,
                      `particulars_high_sea_seller_address` text DEFAULT NULL,
                      `use_of_the_first_proviso_under_section_461customs_act1962` text DEFAULT NULL,
                      `request_for_first_check` text DEFAULT NULL,
                      `request_for_urgent_clear_ance_against_temporary_documentation` text DEFAULT NULL,
                      `request_for_extension_of_time_limit_as_per_section_48customs_ac` text DEFAULT NULL,
                      `reason_in_case_extension_of_time_limit_is_requested` text DEFAULT NULL,
                      `country_of_origin` text DEFAULT NULL,
                      `country_of_consignment` text DEFAULT NULL,
                      `name_of_gateway_port` text DEFAULT NULL,
                      `gateway_igm_number` text DEFAULT NULL,
                      `date_of_entry_inwards_of_gateway_port` date DEFAULT NULL,
                      `case_of_crn` text DEFAULT NULL,
                      `number_of_invoices` int(20) DEFAULT NULL,
                      `total_freight` float(25,2) DEFAULT NULL,
                      `total_insurance` float(25,2) DEFAULT NULL,
                      `created_at` datetime DEFAULT NULL,
                      `a` text DEFAULT NULL,
                      `b` text DEFAULT NULL,
                      `c` text DEFAULT NULL,
                      `d` text DEFAULT NULL,
                      `e` text DEFAULT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
        
                 /*****************************************Create Table drawback_details***************************************************************/
               $db2->query("CREATE TABLE `drawback_details` (
                        `drawback_id` int(20)  NOT NULL PRIMARY KEY,
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
                        `created_at` datetime DEFAULT NULL,
                        `rate` text,
                        `rebate` text NOT NULL,
                        `amount` text NOT NULL,
                        `dbk_rosl` text NOT NULL,
                        `a` text DEFAULT NULL,
                        `b` text DEFAULT NULL,
                        `c` text DEFAULT NULL,
                        `d` text DEFAULT NULL,
                        `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
        
            /*****************************************Create Table duties_and_additional_details***************************************************************/
               $db2->query("CREATE TABLE `duties_and_additional_details` (
                                     `boe_id` int(20) DEFAULT NULL,
                                      `invoice_id` int(20) DEFAULT NULL,
                                      `duties_id` int(20) NOT NULL PRIMARY KEY,
                                      `s_no` int(20) DEFAULT NULL,
                                      `cth` text DEFAULT NULL,
                                      `description` text DEFAULT NULL,
                                      `unit_price` text DEFAULT NULL,
                                      `quantity` float(25,2) DEFAULT NULL,
                                      `uqc` text DEFAULT NULL,
                                      `amount` float(25,2) DEFAULT NULL,
                                      `invsno` int(20) DEFAULT NULL,
                                      `itemsn` int(20) DEFAULT NULL,
                                      `cth_item_detail` text DEFAULT NULL,
                                      `ceth` text DEFAULT NULL,
                                      `item_description` varchar(120) DEFAULT NULL,
                                      `fs` text DEFAULT NULL,
                                      `pq` text DEFAULT NULL,
                                      `dc` text DEFAULT NULL,
                                      `wc` text DEFAULT NULL,
                                      `aq` text DEFAULT NULL,
                                      `upi` float(25,2) DEFAULT NULL,
                                      `coo` text DEFAULT NULL,
                                      `c_qty` float(25,2) DEFAULT NULL,
                                      `c_uqc` text DEFAULT NULL,
                                      `s_qty` float(25,2) DEFAULT NULL,
                                      `s_uqc` text DEFAULT NULL,
                                      `sch` text DEFAULT NULL,
                                      `stdn_pr` text DEFAULT NULL,
                                      `rsp` text DEFAULT NULL,
                                      `reimp` text DEFAULT NULL,
                                      `prov` text DEFAULT NULL,
                                      `end_use` text DEFAULT NULL,
                                      `prodn` text DEFAULT NULL,
                                      `cntrl` text DEFAULT NULL,
                                      `qualfr` text DEFAULT NULL,
                                      `contnt` text DEFAULT NULL,
                                      `stmnt` text DEFAULT NULL,
                                      `sup_docs` text DEFAULT NULL,
                                      `assess_value` float(25,2) DEFAULT NULL,
                                      `total_duty` text DEFAULT NULL,
                                      `bcd_notn_no` text DEFAULT NULL,
                                      `bcd_notn_sno` text DEFAULT NULL,
                                      `bcd_rate` float(25,2) DEFAULT NULL,
                                      `bcd_amount` float(25,2) DEFAULT NULL,
                                      `bcd_duty_fg` text DEFAULT NULL,
                                      `acd_notn_no` text DEFAULT NULL,
                                      `acd_notn_sno` text DEFAULT NULL,
                                      `acd_rate` text DEFAULT NULL,
                                      `acd_amount` text DEFAULT NULL,
                                      `acd_duty_fg` text DEFAULT NULL,
                                      `sws_notn_no` text DEFAULT NULL,
                                      `sws_notn_sno` text DEFAULT NULL,
                                      `sws_rate` float(25,2) DEFAULT NULL,
                                      `sws_amount` float(25,2) DEFAULT NULL,
                                      `sws_duty_fg` text DEFAULT NULL,
                                      `sad_notn_no` text DEFAULT NULL,
                                      `sad_notn_sno` text DEFAULT NULL,
                                      `sad_rate` text DEFAULT NULL,
                                      `sad_amount` text DEFAULT NULL,
                                      `sad_duty_fg` text DEFAULT NULL,
                                      `igst_notn_no` text DEFAULT NULL,
                                      `igst_notn_sno` text DEFAULT NULL,
                                      `igst_rate` int(20) DEFAULT NULL,
                                      `igst_amount` float(25,2) DEFAULT NULL,
                                      `igst_duty_fg` float(25,2) DEFAULT NULL,
                                      `g_cess_notn_no` text DEFAULT NULL,
                                      `g_cess_notn_sno` text DEFAULT NULL,
                                      `g_cess_rate` int(20) DEFAULT NULL,
                                      `g_cess_amount` int(20) DEFAULT NULL,
                                      `g_cess_duty_fg` int(20) DEFAULT NULL,
                                      `add_notn_no` text DEFAULT NULL,
                                      `add_notn_sno` text DEFAULT NULL,
                                      `add_rate` text DEFAULT NULL,
                                      `add_amount` text DEFAULT NULL,
                                      `add_duty_fg` text DEFAULT NULL,
                                      `cvd_notn_no` text DEFAULT NULL,
                                      `cvd_notn_sno` text DEFAULT NULL,
                                      `cvd_rate` int(20) DEFAULT NULL,
                                      `cvd_amount` text DEFAULT NULL,
                                      `cvd_duty_fg` text DEFAULT NULL,
                                      `sg_notn_no` text DEFAULT NULL,
                                      `sg_notn_sno` text DEFAULT NULL,
                                      `sg_rate` text DEFAULT NULL,
                                      `sg_amount` text DEFAULT NULL,
                                      `sg_duty_fg` text DEFAULT NULL,
                                      `t_value_notn_no` text DEFAULT NULL,
                                      `t_value_notn_sno` text DEFAULT NULL,
                                      `t_value_rate` text DEFAULT NULL,
                                      `t_value_amount` text DEFAULT NULL,
                                      `t_value_duty_fg` text DEFAULT NULL,
                                      `sp_excd_notn_no` text DEFAULT NULL,
                                      `sp_excd_notn_sno` text DEFAULT NULL,
                                      `sp_excd_rate` text DEFAULT NULL,
                                      `sp_excd_amount` text DEFAULT NULL,
                                      `sp_excd_duty_fg` text DEFAULT NULL,
                                      `chcess_notn_no` text DEFAULT NULL,
                                      `chcess_notn_sno` text DEFAULT NULL,
                                      `chcess_rate` text DEFAULT NULL,
                                      `chcess_amount` text DEFAULT NULL,
                                      `chcess_duty_fg` text DEFAULT NULL,
                                      `tta_notn_no` text DEFAULT NULL,
                                      `tta_notn_sno` text DEFAULT NULL,
                                      `tta_rate` text DEFAULT NULL,
                                      `tta_amount` text DEFAULT NULL,
                                      `tta_duty_fg` text DEFAULT NULL,
                                      `cess_notn_no` text DEFAULT NULL,
                                      `cess_notn_sno` text DEFAULT NULL,
                                      `cess_rate` text DEFAULT NULL,
                                      `cess_amount` text DEFAULT NULL,
                                      `cess_duty_fg` text DEFAULT NULL,
                                      `caidc_cvd_edc_notn_no` text DEFAULT NULL,
                                      `caidc_cvd_edc_notn_sno` int(20) DEFAULT NULL,
                                      `caidc_cvd_edc_rate` int(20) DEFAULT NULL,
                                      `caidc_cvd_edc_amount` float(25,2) DEFAULT NULL,
                                      `caidc_cvd_edc_duty_fg` float(25,2) DEFAULT NULL,
                                      `eaidc_cvd_hec_notn_no` text DEFAULT NULL,
                                      `eaidc_cvd_hec_notn_sno` text DEFAULT NULL,
                                      `eaidc_cvd_hec_rate` text DEFAULT NULL,
                                      `eaidc_cvd_hec_amount` text DEFAULT NULL,
                                      `eaidc_cvd_hec_duty_fg` text DEFAULT NULL,
                                      `cus_edc_notn_no` text DEFAULT NULL,
                                      `cus_edc_notn_sno` text DEFAULT NULL,
                                      `cus_edc_rate` int(20) DEFAULT NULL,
                                      `cus_edc_amount` text DEFAULT NULL,
                                      `cus_edc_duty_fg` text DEFAULT NULL,
                                      `cus_hec_notn_no` text DEFAULT NULL,
                                      `cus_hec_notn_sno` text DEFAULT NULL,
                                      `cus_hec_rate` int(20) DEFAULT NULL,
                                      `cus_hec_amount` text DEFAULT NULL,
                                      `cus_hec_duty_fg` text DEFAULT NULL,
                                      `ncd_notn_no` text DEFAULT NULL,
                                      `ncd_notn_sno` text DEFAULT NULL,
                                      `ncd_rate` text DEFAULT NULL,
                                      `ncd_amount` text DEFAULT NULL,
                                      `ncd_duty_fg` text DEFAULT NULL,
                                      `aggr_notn_no` text DEFAULT NULL,
                                      `aggr_notn_sno` text DEFAULT NULL,
                                      `aggr_rate` text DEFAULT NULL,
                                      `aggr_amount` text DEFAULT NULL,
                                      `aggr_duty_fg` text DEFAULT NULL,
                                      `invsno_add_details` text DEFAULT NULL,
                                      `itmsno_add_details` text DEFAULT NULL,
                                      `refno` text DEFAULT NULL,
                                      `refdt` text DEFAULT NULL,
                                      `prtcd_svb_d` text DEFAULT NULL,
                                      `lab` text DEFAULT NULL,
                                      `pf` text DEFAULT NULL,
                                      `load_date` date DEFAULT NULL,
                                      `pf_` text DEFAULT NULL,
                                      `beno` text DEFAULT NULL,
                                      `bedate` date DEFAULT NULL,
                                      `prtcd` text DEFAULT NULL,
                                      `unitprice` text DEFAULT NULL,
                                      `currency_code` text DEFAULT NULL,
                                      `frt` text DEFAULT NULL,
                                      `ins` text DEFAULT NULL,
                                      `duty` text DEFAULT NULL,
                                      `sb_no` text DEFAULT NULL,
                                      `sb_dt` text DEFAULT NULL,
                                      `portcd` text DEFAULT NULL,
                                      `sinv` text DEFAULT NULL,
                                      `sitemn` text DEFAULT NULL,
                                      `type` text DEFAULT NULL,
                                      `manufact_cd` text DEFAULT NULL,
                                      `source_cy` text DEFAULT NULL,
                                      `trans_cy` text DEFAULT NULL,
                                      `address` text DEFAULT NULL,
                                      `accessory_item_details` text DEFAULT NULL,
                                      `notno` text DEFAULT NULL,
                                      `slno` text DEFAULT NULL,
                                      `created_at` datetime DEFAULT NULL,
                                    `a` text DEFAULT NULL,
                                    `b` text DEFAULT NULL,
                                    `c` text DEFAULT NULL,
                                    `d` text DEFAULT NULL,
                                    `e` text DEFAULT NULL
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
  
            /*****************************************Create Table equipment_details***************************************************************/
               $db2->query("CREATE TABLE `equipment_details` (
                          `equip_id` int(20)  NOT NULL PRIMARY KEY,
                          `sbs_id` int(20) DEFAULT NULL,
                          `container` text,
                          `seal` text,
                          `date` date DEFAULT NULL,
                          `s_no` text,
                          `created_at` datetime DEFAULT NULL,
                         `a` text DEFAULT NULL,
                                    `b` text DEFAULT NULL,
                                    `c` text DEFAULT NULL,
                                    `d` text DEFAULT NULL,
                                    `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
        
        
        /*****************************************Create Table invoice_and_valuation_details***************************************************************/
               $db2->query("CREATE TABLE `invoice_and_valuation_details` (
                                  `invoice_id` int(20)  NOT NULL  PRIMARY KEY,
                                  `boe_id` int(20) DEFAULT NULL,
                                  `s_no` int(20) DEFAULT NULL,
                                  `invoice_no` text DEFAULT NULL,
                                  `purchase_order_no` text DEFAULT NULL,
                                  `lc_no` text DEFAULT NULL,
                                  `contract_no` text DEFAULT NULL,
                                  `buyer_s_name_and_address` text ,
                                  `seller_s_name_and_address` text ,
                                  `supplier_name_and_address` text ,
                                  `third_party_name_and_address` text ,
                                  `aeo` text DEFAULT NULL,
                                  `ad_code` text DEFAULT NULL,
                                  `inv_value` float(25,2) DEFAULT NULL,
                                  `freight` text DEFAULT NULL,
                                  `insurance` text DEFAULT NULL,
                                  `hss` text DEFAULT NULL,
                                  `loading` text DEFAULT NULL,
                                  `commn` text DEFAULT NULL,
                                  `pay_terms` text DEFAULT NULL,
                                  `valuation_method` text DEFAULT NULL,
                                  `reltd` text DEFAULT NULL,
                                  `svb_ch` text DEFAULT NULL,
                                  `svb_no` text DEFAULT NULL,
                                  `date` date DEFAULT NULL,
                                  `loa` int(20) DEFAULT NULL,
                                  `cur` text DEFAULT NULL,
                                  `term` text DEFAULT NULL,
                                  `c_and_b` text DEFAULT NULL,
                                  `coc` text DEFAULT NULL,
                                  `cop` text DEFAULT NULL,
                                  `hnd_chg` text DEFAULT NULL,
                                  `g_and_s` text DEFAULT NULL,
                                  `doc_ch` text DEFAULT NULL,
                                  `coo` text DEFAULT NULL,
                                  `r_and_lf` text DEFAULT NULL,
                                  `oth_cost` text DEFAULT NULL,
                                  `ld_uld` text DEFAULT NULL,
                                  `ws` text DEFAULT NULL,
                                  `otc` text DEFAULT NULL,
                                  `misc_charge` float(25,2) DEFAULT NULL,
                                  `ass_value` float(25,2) DEFAULT NULL,
                                  `invoice_date` date DEFAULT NULL,
                                  `purchase_order_date` date DEFAULT NULL,
                                  `lc_date` date DEFAULT NULL,
                                  `contract_date` date DEFAULT NULL,
                                  `freight_cur` text DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `a` text DEFAULT NULL,
                                  `b` text DEFAULT NULL,
                                  `c` text DEFAULT NULL,
                                  `d` text DEFAULT NULL,
                                  `e` text DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

    /*************************************************************************************************************************************/
            
    /*****************************************Create Table equipment_details***************************************************************/
               $db2->query("CREATE TABLE `invoice_summary` (
                              `invoice_id` int(20)  NOT NULL  PRIMARY KEY,
                              `sbs_id` int(20) DEFAULT NULL,
                              `s_no_inv` text,
                              `inv_no` text,
                              `inv_date` date DEFAULT NULL,
                              `inv_no_date`  date DEFAULT NULL,
                              `po_no_date`  date DEFAULT NULL,
                              `loc_no_date`  date DEFAULT NULL,
                              `contract_no_date`  date DEFAULT NULL,
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
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /******************************************************************************************************************************/
        
            
             /*****************************************Create Table equipment_details***************************************************************/
               $db2->query("CREATE TABLE `item_details` (
                              `item_id` int(20) NOT NULL  PRIMARY KEY,
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
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
       
             /*****************************************Create Table item_manufacturer_details***************************************************************/
               $db2->query("CREATE TABLE `item_manufacturer_details` (
                              `item_manufact_id` int(20) NOT NULL PRIMARY KEY DEFAULT NULL,
                              `item_id` text DEFAULT NULL,
                              `inv_sno` text DEFAULT NULL,
                              `item_sno` text DEFAULT NULL,
                              `manufact_cd` text DEFAULT NULL,
                              `source_state` text DEFAULT NULL,
                              `trans_cy` text DEFAULT NULL,
                              `address` text DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
          /*****************************************Create Table item_manufacturer_details***************************************************************/
               $db2->query("CREATE TABLE `jobbing_details` (
                              `jobbing_detail_id` int(20) NOT NULL PRIMARY KEY,
                              `sbs_id` int(20) DEFAULT NULL,
                              `be_no` text,
                              `be_date` date DEFAULT NULL,
                              `port_code_j` text,
                              `descn_of_imported_goods` text,
                              `qty_imp` text,
                              `qty_used` text,
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            
               /*****************************************Create Table report_setting_export***************************************************************/
               $db2->query("CREATE TABLE `report_setting_export` (
                              `id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              `export_importer_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `type` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `BOE_Summary` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `BOE_Summary_index` text DEFAULT NULL,
                              `Bill_Of_Entry` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `Bill_Of_Entry_index` text DEFAULT NULL,
                              `Bond_Details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `Bond_Details_index` text DEFAULT NULL,
                              `Container_Details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                              `Container_Details_index` text DEFAULT NULL,
                              `Manifest_Details` text,
                              `Manifest_Details_index` text DEFAULT NULL,
                              `Payment_Details` text,
                              `Payment_Details_index` text DEFAULT NULL,
                              `License_Details` text,
                              `License_Details_index` text DEFAULT NULL,
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
                              `frequency` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `time` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                              `format` enum('excel','csv','both') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                               `report_name` text NOT NULL,
                              `from_date` text DEFAULT NULL,
                              `to_date` text DEFAULT NULL,
                              `email_time` text DEFAULT NULL,
                              `email_id` text DEFAULT NULL,
                              `report_path` text NOT NULL,
                              `CREATED` date NOT NULL,
                              `IS_DELETED` int(20) NOT NULL
                            ) ENGINE=MyISAM DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
                /*****************************************Create Table rodtep_details***************************************************************/
               $db2->query("CREATE TABLE `rodtep_details` (
                              `rodtep_id` int(20)  NOT NULL  PRIMARY KEY,
                              `item_id` int(20) DEFAULT NULL,
                              `inv_sno` int(20) DEFAULT NULL,
                              `item_sno` int(20) DEFAULT NULL,
                              `quantity` text DEFAULT NULL,
                              `uqc` text DEFAULT NULL,
                              `no_of_units` int(20) DEFAULT NULL,
                              `value` int(20) DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/

           /*****************************************Create Table report_setting_export***************************************************************/
               $db2->query("CREATE TABLE `sb_file_status` (
                              `sb_file_status_id` int(20) NOT NULL PRIMARY KEY,
                              `pdf_filepath` text DEFAULT NULL,
                              `pdf_filename` text DEFAULT NULL,
                              `user_iec_no` text DEFAULT NULL,
                              `lucrative_users_id` int(20) DEFAULT NULL,
                              `file_iec_no` text DEFAULT NULL,
                              `sb_no` text DEFAULT NULL,
                              `sb_date` date DEFAULT NULL,
                              `stage` text DEFAULT NULL,
                              `status` text DEFAULT NULL,
                              `remarks` text DEFAULT NULL,
                              `created_at` date DEFAULT NULL,
                              `br` text DEFAULT NULL,
                              `is_processed` text DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/

            /*****************************************Create Table ship_bill_summary******************************************/
               $db2->query("CREATE TABLE `ship_bill_summary` (
                              `sbs_id` int(20)  NOT NULL  PRIMARY KEY,
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
                              `mawb_dt` date DEFAULT NULL,
                              `hawb_no` text,
                              `hawb_dt` date DEFAULT NULL,
                              `noc` text,
                              `cin_no` text,
                              `cin_dt` date DEFAULT NULL,
                              `cin_site_id` text,
                              `seal_type` text,
                              `nature_of_cargo` text,
                              `no_of_packets` text,
                              `no_of_containers` text,
                              `loose_packets` text,
                              `marks_and_numbers` text,
                              `submission_date` date DEFAULT NULL,
                              `assessment_date` date DEFAULT NULL,
                              `examination_date` date DEFAULT NULL,
                              `leo_date` date DEFAULT NULL,
                              `submission_time` text,
                              `assessment_time` text,
                              `examination_time` text,
                              `leo_time` text,
                              `leo_no` text,
                              `leo_dt` text,
                              `brc_realisation_date` date DEFAULT NULL,
                              `created_at` datetime DEFAULT NULL,
                              `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
             /*****************************************Create Table ship_bill_summary***************************************************************/
               $db2->query("CREATE TABLE `tbl_graph_reports_settings` (
                              `graph_setting_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                `user_id` int(20) NOT NULL,
                                `graph_name` text NOT NULL,
                                `type` int(20) NOT NULL,
                                `sheet_id` text NOT NULL,
                                `fieldx` text NOT NULL,
                                `fieldy` text NOT NULL,
                                `graph_type` text NOT NULL,
                                `graph_looks` text NOT NULL,
                                `from_date` date DEFAULT NULL,
                                `to_date` date DEFAULT NULL,
                                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `a` text DEFAULT NULL,
                                `b` text DEFAULT NULL,
                                `c` text DEFAULT NULL,
                                `d` text DEFAULT NULL,
                                `e` text DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            /*****************************************Create Table ship_bill_summary***************************************************/
               $db2->query("CREATE TABLE `third_party_details` (
                                  `third_party_id` int(1) NOT NULL PRIMARY KEY,
                                  `item_id` varchar(10) DEFAULT NULL,
                                  `inv_sno` varchar(10) DEFAULT NULL,
                                  `item_sno` varchar(10) DEFAULT NULL,
                                  `iec_tpd` varchar(10) DEFAULT NULL,
                                  `exporter_name` varchar(10) DEFAULT NULL,
                                  `address` varchar(10) DEFAULT NULL,
                                  `gstn_id_type` varchar(3) DEFAULT NULL,
                                  `a` text DEFAULT NULL,
                                  `b` text DEFAULT NULL,
                                  `c` text DEFAULT NULL,
                                  `d` text DEFAULT NULL,
                                  `e` text DEFAULT NULL
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            /*****************************************Create Table ship_bill_summary***************************************************/
               $db2->query("CREATE TABLE `tbl_graph_set_user` (
                        `graphs_settings_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `graphs_id` text NOT NULL,
                        `user_id` int(20) NOT NULL,
                        `created_at` date NOT NULL,
                        `a` text DEFAULT NULL,
                        `b` text DEFAULT NULL,
                        `c` text DEFAULT NULL,
                        `d` text DEFAULT NULL,
                        `e` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
            
              /*****************************************Create Table tbl_import_export_sheets_name***************************************************/
               $db2->query("CREATE TABLE `tbl_import_export_sheets_name` (
  `ie_sheet_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` int(20) NOT NULL,
  `sheet_name` text NOT NULL,
`a` text DEFAULT NULL,
`b` text DEFAULT NULL,
`c` text DEFAULT NULL,
`d` text DEFAULT NULL,
`e` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

            /********************************************************************************************************/
            
              /*****************************************Create Table tbl_ie_sheets_fields***************************************************/
               $db2->query("CREATE TABLE `tbl_ie_sheets_fields` (
  `field_id` int(20)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ie_sheet_id` int(20) NOT NULL,
  `field_name` text NOT NULL,
`a` text DEFAULT NULL,
`b` text DEFAULT NULL,
`c` text DEFAULT NULL,
`d` text DEFAULT NULL,
`e` text DEFAULT NULL
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
                              `paymt_dt` date NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                       `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                       `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                       `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
                                      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                       `a` text DEFAULT NULL,
                              `b` text DEFAULT NULL,
                              `c` text DEFAULT NULL,
                              `d` text DEFAULT NULL,
                              `e` text DEFAULT NULL
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
    
    public function inArray_lucrative_users($array, $value){
 
   /* Initialize index -1 initially. */
 
    $index = -1;
 
    foreach($array as $val){
// print_r($val);
         /* If value is found, set index to 1. */
 // "=>".$val['be_no']. "==". $value;
         if($val['iec_no'] == $value){
 
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
    

    
}



?>