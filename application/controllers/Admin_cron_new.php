<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_cron_new extends CI_Controller {

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
	/******************************************************************Start PostgreSQL database***************************************************************************************/
   
   // PostgreSQL database connection
//$pgConn = pg_connect("host=doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com port=58272 dbname=doc2db user=postgres password=heroism_neon_77!");

// MySQL database connection
$mysqlConn = mysqli_connect("localhost", "root", "%!#^bFjB)z8C", "lucrativeesystem_D2D_master");
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
$q1 = $this->db->query("SELECT tablename, tableowner FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'");
   $tables =$q1->result_array();
   // Iterate over tables
   //print_r($tables);
foreach ($tables as $table) {
    $tableName = $table['tablename'];

    // Retrieve table structure from PostgreSQL
    $pgTableStructureResult = $this->db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '{$tableName}'");
    $pgTableStructure = $pgTableStructureResult->result_array($pgTableStructureResult);

    // Build the CREATE TABLE statement for MySQL
    $createTableQuery = "CREATE TABLE {$tableName} (";
    foreach ($pgTableStructure as $column) {
        $columnName = $column['column_name'];
        $columnType = $column['data_type'];
        $createTableQuery .= "{$columnName} {$columnType}, ";
    }
    $createTableQuery = rtrim($createTableQuery, ", ");
    $createTableQuery .= ")";
echo $createTableQuery;
    // Create the table in MySQL
    $mysqlConn->query($createTableQuery);

    // Retrieve data from PostgreSQL and insert into MySQL
    echo "SELECT * FROM {$tableName}";
    $pgDataResult = $this->db->query( "SELECT * FROM {$tableName}");
    while ($row = $pgDataResult->result_array($pgDataResult)) {
        $insertQuery = "INSERT INTO {$tableName} (";
        $values = "VALUES (";
        foreach ($row as $column => $value) {
            $insertQuery .= $column . ", ";
            $values .= "'" .($value). "', ";
        }
        $insertQuery = rtrim($insertQuery, ", ") . ")";
        $values = rtrim($values, ", ") . ")";
       echo $insertQuery .= $values;
        $mysqlConn->query($insertQuery);
    }
}

   
   
   
	/******************************************************************End PostgreSQL database***************************************************************************************/
		
	
	}
	
	

}