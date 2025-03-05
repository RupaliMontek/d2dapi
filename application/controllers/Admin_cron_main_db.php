<?php defined("BASEPATH") or exit("No direct script access allowed");
//set_time_limit(6000000);
class Admin_cron_main_db extends CI_Controller
{
    //test1 test2
    public function __construct()
    {
        @parent::__construct();
        $this->load->model("admin/Common_model");
        $this->load->dbforge();

        //  $this->db_doc = $this->load->database('second', TRUE);
    }
    public function __destruct()
    {
        //mysqli_close($Db1->connection);
        $this->db->close();
    }
    private function load_secondary_database($name)
    {
        $db_secondary_config = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '%!#^bFjB)z8C',
            'database' => "lucrativeesystem_D2D_S{$name}",
            'dbdriver' => 'mysqli',
            'pconnect' => FALSE,
            'db_debug' => (ENVIRONMENT !== 'production'),
        ];
        return $this->load->database($db_secondary_config, TRUE);
    }
    //its final function to check count of all table in postgear local database   
    public function postgearlocal()
    {

        $pgHost = 'localhost';
        $pgUsername = 'symmetryindia';
        $pgPassword = 'Y$I3Q#U2[Dw_';
        $pgDatabase = 'mydatabase_postgear_aws_main';
        $pgPort = '5432';

        // Load the 'second' database configuration
        $this->load->database('second');

        // Now you can use the second database connection
        $query = $this->db->query("SELECT * FROM lucrative_users"); // Example query
        echo $query->num_rows();
        // Process query results, if needed
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                print_r($row);
                //echo $row->column_name;
            }
        } else {
            echo "No records found.";
        }
        exit;
        // Get all table names in the database
        $query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
        $tables = $query->result_array();

        $tableCounts = array();

        // Iterate through each table and get record count
        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            $countQuery = $this->db->query("SELECT COUNT(*) AS record_count FROM $tableName");
            $countResult = $countQuery->row_array();
            $recordCount = isset($countResult['record_count']) ? $countResult['record_count'] : 0;
            $tableCounts[$tableName] = $recordCount;
        }

        print_r($tableCounts);




    }
    public function alldatabaseupdate()
    {

        // Fetch admin users with paginated queries to handle large datasets
        $perPage = 50; // Adjust based on memory and performance testing
        $totalUsers = $this->db->query("SELECT COUNT(*)
        as total FROM lucrative_users WHERE role = 'admin'")->row()->total;
        $pages = ceil($totalUsers / $perPage);
        for ($page = 0; $page < $pages; $page++) {
            $offset = $page * $perPage;
            $adminUsersQuery = "SELECT lucrative_users_id, iec_no FROM lucrative_users WHERE role = 'admin' order by lucrative_users_id DESC LIMIT $perPage OFFSET $offset";
            $admin_users = $this->db->query($adminUsersQuery)->result_array();
            foreach ($admin_users as $user) {
                $lucrativeUsersIds[] = $user['lucrative_users_id'];
            }
        }

        //  print_r($lucrativeUsersIds);






        // Array of database names to process (lucrativeesystem_D2D_S1 to lucrativeesystem_D2D_S216)
/*$databases = array_map(function($index) {
    return 'lucrativeesystem_D2D_S' . $index;
}, range(1, 216));*/

        // Process each database
        foreach ($lucrativeUsersIds as $databaseName) {
            // Create a new database connection for the current database
            $db_secondary = $this->load_secondary_database($databaseName);
            // Use the $dbConnection object to perform database operations
            if ($db_secondary) {
                // Get the table name from the result array
                echo $databaseName;
                // Modify `accessory_item_details` column
                // $sql1 = "TRUNCATE TABLE invoice_summary";
                $sql1 = "ALTER TABLE `bill_container_details` CHANGE `container_details_pk` `container_details_id` INT(20) NOT NULL";

                //$sql1 = "ALTER TABLE `invoice_summary` CHANGE `inv_no_date` `inv_no_date` TEXT NULL DEFAULT NULL, CHANGE `po_no_date` `po_no_date` TEXT NULL DEFAULT NULL, CHANGE `loc_no_date` `loc_no_date` TEXT NULL DEFAULT NULL, CHANGE `contract_no_date` `contract_no_date` TEXT NULL DEFAULT NULL";

                // Modify `item_description` column
                /*     $sql2 = "ALTER TABLE `duties_and_additional_details` CHANGE `item_description` `item_description` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL";
               $sql3='CREATE TABLE `item_manufacturer_details` (
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
                                                             ) ENGINE=InnoDB DEFAULT CHARSET=latin1';*/
                // Execute SQL queries on the current database
                if ($db_secondary->query($sql1) === TRUE) {
                    echo "Columns modified successfully in table $tableName of database $databaseName.<br>";
                } else {
                    continue;
                    // echo "Error modifying columns in table $tableName of database $databaseName: " . $db_secondary->error . "<br>";
                }


                // Close the current database connection
                $db_secondary->close();
            } else {
                continue;
            }



        }

    }

    public function allreportdatabsesdrop()
    {
        $db_secondary = $this->load_secondary_database('1');


        // Fetch databases starting with '1_'
        $sql = "SHOW DATABASES LIKE '1_%'";
        $result = $db_secondary->query($sql);

        if ($result->num_rows > 0) {
            // Drop each database
            while ($row = $result->fetch_assoc()) {
                echo $database = $row["Database"];
                exit;
                $drop_sql = "DROP DATABASE `$database`";
                if ($db_secondary->query($drop_sql) === TRUE) {
                    echo "Dropped database: $database\n";
                } else {
                    echo "Error dropping database $database: " . $conn->error . "\n";
                }
            }
        } else {
            echo "No databases found matching the pattern '1_%'\n";
        }

        $db_secondary->close();
    }


    public function alldatabaseupadateprimarykey()
    {


        // Database connection configuration
        $host = 'localhost';
        $username = 'your_username';
        $password = 'your_password';
        $database = 'your_database';

        // Create database connection
        $conn = new mysqli($host, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Array of table names to process (lucrativeesystem_D2D_S1 to lucrativeesystem_D2D_S216)
        $tables = array_map(function ($index) {
            return 'lucrativeesystem_D2D_S' . $index;
        }, range(1, 216));

        // Process each table
        foreach ($tables as $table) {
            // Generate SQL query to drop primary key constraint
            $sql = "ALTER TABLE `$table` DROP PRIMARY KEY";

            // Execute SQL query
            if ($conn->query($sql) === TRUE) {
                echo "Primary key dropped successfully from table $table.<br>";
            } else {
                echo "Error dropping primary key from table $table: " . $conn->error . "<br>";
            }
        }

        // Close database connection
        $conn->close();



    }

    public function postgear_bill_of_entry_summary()
    {

        $pgHost = 'localhost';
        $pgUsername = 'symmetryindia';
        $pgPassword = 'Y$I3Q#U2[Dw_';
        $pgDatabase = 'mydatabase_postgear_aws_main';
        $pgPort = '5432';
        // Load the 'second' database configuration
        $this->load->database('second');

        $query = $this->db->query("SELECT bill_bond_details.*, bill_bond_details.boe_id as boe,bill_of_entry_summary.be_no,bill_of_entry_summary.iec_no 
        FROM bill_bond_details LEFT JOIN bill_of_entry_summary ON bill_of_entry_summary.boe_id = bill_bond_details.boe_id ");
        $tables = $query->result_array();
        $tableCounts = array();

        // Iterate through each table and get record count
        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            $countQuery = $this->db->query("SELECT COUNT(*) AS record_count FROM $tableName");
            $countResult = $countQuery->row_array();
            $recordCount = isset($countResult['record_count']) ? $countResult['record_count'] : 0;
            $tableCounts[$tableName] = $recordCount;
        }

        print_r($tableCounts);




    }

    //working fine with postgearSql Aws dump to local postgearsql dump its final
    public function DumpPostgearToMysql_tablewise()
    {


        // Remote PostgreSQL database credentials
        $remoteHost = 'doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com';
        $remotePort = '58272';
        $remoteUsername = 'postgres';
        $remotePassword = 'heroism_neon_77!';
        $remoteDatabase = 'doc2db';

        // Tables to dump (array of table names)
//$tablesToDump = ['bill_of_entry_summary']; // Specify the tables you want to dump
        $tablesToDump = [
            'lucrative_users',
            'sb_file_status',
            'third_party_details',
            'cb_file_status',
            'rodtep_details',
            'boe_delete_logs',
            'ship_bill_summary',
            'equipment_details',
            'invoice_summary',
            'jobbing_details',
            'item_details',
            'challan_details',
            'item_details11',
            'drawback_details',
            'aa_dfia_licence_details',
            'item_manufacturer_details',
            'bill_licence_details',
            'bill_payment_details',
            'bill_manifest_details',
            'bill_of_entry_summary',
            'duties_and_additional_details',
            'invoice_and_valuation_details',
            'boe_file_status',
            'bill_bond_details',
            'bill_container_details',
            'courier_bill_manifest_details',
            'courier_bill_procurment_details',
            'courier_bill_bond_details',
            'courier_bill_items_details',
            'courier_bill_payment_details',
            'courier_bill_invoice_details',
            'courier_bill_igm_details',
            'courier_bill_notification_used_for_items',
            'courier_bill_container_details',
            'courier_bill_duty_details',
            'courier_bill_summary'
        ];
        try {
            // Loop through each table and generate gzipped SQL dump file
            foreach ($tablesToDump as $table) {
                $backupFile = $table . '_dumps.sql.gz';

                // Generate gzipped SQL dump file for the current table from remote database
                $dumpCommand = "/usr/pgsql-13/bin/pg_dump -h $remoteHost -p $remotePort -U $remoteUsername -d $remoteDatabase --table $table | gzip > $backupFile";
                exec($dumpCommand, $output, $resultCode);

                if ($resultCode !== 0) {
                    throw new Exception("Failed to generate dump for table $table. Command output: " . implode("\n", $output));
                }

                echo "Dump created successfully for table $table.\n";
            }

            echo "Table dumps created successfully.\n";

            // Optionally, import the gzipped table dumps into a local database
            $localHost = 'localhost';
            $localPort = '5432';
            $localUsername = 'symmetryindia';
            $localDatabase = 'symmetryindia_postgear_aws_main';
            // Load the 'second' database configuration
            $this->load->database('second');



            // Construct the psql command to drop all tables in the local database
            $dropTablesCommand = "/usr/pgsql-13/bin/psql -h $localHost -p $localPort -U $localUsername -d $localDatabase -c \"DROP SCHEMA public CASCADE; CREATE SCHEMA public;\"";

            // Execute the command to drop all tables in the local database
            $output1 = shell_exec($dropTablesCommand);

            // Check if the drop tables command was successful
            if ($output1 === null || $output1 === '') {
                echo "All tables dropped successfully from the local database.\n";
            } else {
                echo "Error dropping tables in the local database.\n";
                echo "Command output: $output1\n";
                exit(1); // Exit script with error code
            }

            // Import command (uncomment and modify as needed)
            foreach ($tablesToDump as $table) {
                $backupFile = $table . '_dumps.sql.gz';
                $importCommand = "/bin/zcat $backupFile | /usr/pgsql-13/bin/psql -h $localHost -p $localPort -U $localUsername -d $localDatabase";
                // exec($importCommand, $importOutput, $importResultCode);


                // Execute the command to drop all tables in the local database
                $output2 = shell_exec($importCommand);

                print_r($output2);
                // Check if pg_dump command was successful
                if ($output2 === null) {
                    echo "Remote database dump completed successfully.\n";
                } else {
                    echo "Error: Remote database dump failed.\n";
                    echo "Command output: $output2\n";
                    exit(1); // Exit script with error code
                }


                echo "Dump imported successfully for table $table into local server.\n";
            }

            // Clean up the gzipped dump files
            /*foreach ($tablesToDump as $table) {
                $backupFile = $table . '_dumps.sql.gz';
                @unlink($backupFile);
                echo "Cleanup completed for dump file $backupFile.\n";
            }*/

           // echo "Cleanup completed.\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }

    }

    //not working function  tried take dump postgear to mysql
    public function exportPostgreSQLToMySQL()
    {
        // Connection parameters for PostgreSQL and MySQL
        // Local PostgreSQL database credentials
        $localPostgearHost = 'localhost';
        $localPostgearPort = '5432';
        $localPostgearUsername = 'symmetryindia';
        $localPostgearPassword = 'Y$I3Q#U2[Dw_';
        $localPostgearDatabase = 'symmetryindia_postgear_aws_main';

        $mysqlConfig = [
            'hostname' => 'localhost',
            'username' => 'root1',
            'password' => '[b~BiWQ!l9BH',
            'database' => 'd2d_aws_main'
        ];
        // Local MySQL database credentials
/*$localMySQLHost = 'localhost';
//$localMySQLPort = '5432';
$localMySQLUsername = 'root1';
$localMySQLPassword = '[b~BiWQ!l9BH';
$localMySQLDatabase = 'd2d_aws_main';  */
        $postgresqlConfig['password'] = 'Y$I3Q#U2[Dw_';
        // Path to store the database dump file locally
        $backupFile = 'remote_database_dump_local.sql';

        // Construct the pg_dump command for remote database
//$remoteDumpCommand = "pg_dump -h $remoteHost -p $remotePort -U $remoteUsername -d $remoteDatabase -w > $backupFile";
//sudo /usr/pgsql-<version>/bin/pg_dump
///usr/pgsql-15/bin/pg_dump -h doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com          -p 58272          -U postgres          -d doc2db          > remote_database_dump.sql
        putenv("PGPASSWORD={$postgresqlConfig['password']}");
        //$remoteDumpCommand = " /usr/pgsql-13/bin/pg_dump  -h $localPostgearHost -p $localPostgearPort -U $localPostgearUsername -w $localPostgearDatabase > $backupFile";
        $remoteDumpCommand = " pg_dump -h {$localPostgreSQLHost} -p {$localPostgreSQLPort} -U {$localPostgreSQLUsername} -d {$localPostgreSQLDatabase} --data-only --no-owner --no-acl -f {$backupFile}";

        // Execute the pg_dump command for remote database
        $output = []; // Initialize $output variable
        $resultCode = 0; // Initialize $resultCode variable
// Execute the pg_dump command for remote database
        $output = shell_exec($remoteDumpCommand);
        echo $output;
        // Check if pg_dump command was successful
        if ($output === null) {
            echo "Remote database pg_dump completed successfully.\n";
        } else {
            echo "Error: Remote database pg_dump failed.\n";
            echo "Command output: $output\n";
            exit(1); // Exit script with error code
        }



        /*
            // List of tables to export from PostgreSQL
            $tables = [
                'lucrative_users', 'sb_file_status', 'third_party_details', 'cb_file_status', 'rodtep_details', 'boe_delete_logs', 'ship_bill_summary', 'equipment_details',
                'invoice_summary', 'jobbing_details', 'item_details', 'challan_details', 'item_details11', 'drawback_details', 'aa_dfia_licence_details', 'item_manufacturer_details', 'bill_licence_details',
                'bill_payment_details', 'bill_manifest_details', 'bill_of_entry_summary', 'duties_and_additional_details', 'invoice_and_valuation_details',
                'boe_file_status', 'bill_bond_details', 'bill_container_details', 'courier_bill_manifest_details', 'courier_bill_procurment_details',
                'courier_bill_bond_details', 'courier_bill_items_details', 'courier_bill_payment_details', 'courier_bill_invoice_details', 'courier_bill_igm_details',
                'courier_bill_notification_used_for_items', 'courier_bill_container_details', 'courier_bill_duty_details', 'courier_bill_summary'
            ];

            // Loop through each table and export to MySQL
            foreach ($tables as $tableName) {
                // Export PostgreSQL table to a temporary SQL file
                $sqlDumpFile = "remote_database_dump_{$tableName}.sql";
                
                // Set the PGPASSWORD environment variable for PostgreSQL
                putenv("PGPASSWORD={$postgresqlConfig['password']}");

                // Generate pg_dump command to export table data
                $pgDumpCommand = "/usr/pgsql-13/bin/pg_dump --host={$postgresqlConfig['hostname']} --username={$postgresqlConfig['username']} --dbname={$postgresqlConfig['database']} --table={$tableName} --file={$sqlDumpFile} 2>&1";
                
                // Execute pg_dump command for PostgreSQL
                $pgDumpOutput = [];
                exec($pgDumpCommand, $pgDumpOutput, $pgDumpResult);

                if ($pgDumpResult !== 0) {
                    echo "Error exporting table $tableName from PostgreSQL: " . implode("\n", $pgDumpOutput) . "<br>";
                    continue; // Skip to the next table on error
                }
        */
        // Load MySQL database
        $db_mysql = $this->load->database('third', TRUE);

        // Generate mysql import command to import SQL dump file into MySQL  mysql -h {$mysqlHostname} -u {$mysqlUsername} -p{$mysqlPassword} {$mysqlDatabase} < {$backupFile}

        $mysqlImportCommand = "MYSQL_PWD='{$mysqlConfig['password']}' mysql --host={$mysqlConfig['hostname']} --user={$mysqlConfig['username']} --database={$mysqlConfig['database']} < {$backupFile} 2>&1";

        // Execute the pg_dump command for remote database
        $output1 = shell_exec($mysqlImportCommand);
        echo $output1;
        // Check if pg_dump command was successful
        if ($output1 === null) {
            echo "Remote database my sql completed successfully.\n";
        } else {
            echo "Error: Remote database my sql failed.\n";
            echo "Command output: $output1\n";
            exit(1); // Exit script with error code
        }

        // Remove the temporary SQL dump file
        //unlink($backupFile);


        echo "Export from PostgreSQL to MySQL completed.";
    }

    public function DumpPostgearToMysql_working()
    {
        // Remote PostgreSQL database credentials
        $remoteHost = 'doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com';
        $remotePort = '58272';
        $remoteUsername = 'postgres';
        $remotePassword = 'heroism_neon_77!';
        $remoteDatabase = 'doc2db';

        // Local PostgreSQL database credentials
        $localHost = 'localhost';
        $localPort = '5432';
        $localUsername = 'symmetryindia';
        $localPassword = 'Y$I3Q#U2[Dw_';
        $localDatabase = 'symmetryindia_postgear_aws_main';

        // Path to store the database dump file locally
        $backupFile = 'remote_database_dump_1.sql';

        // Construct the pg_dump command for remote database
//$remoteDumpCommand = "pg_dump -h $remoteHost -p $remotePort -U $remoteUsername -d $remoteDatabase -w > $backupFile";
//sudo /usr/pgsql-<version>/bin/pg_dump
///usr/pgsql-15/bin/pg_dump -h doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com          -p 58272          -U postgres          -d doc2db          > remote_database_dump.sql
        $remoteDumpCommand = " /usr/pgsql-15/bin/pg_dump -h $remoteHost -p $remotePort -U $remoteUsername -w $remoteDatabase > $backupFile";

        //sudo /usr/pgsql-15/bin/pg_dump -h doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com -p 58272 -U postgres -d doc2db -w > /path/to/directory/remote_database_dump.sql
// Execute the pg_dump command for remote database
        $output = []; // Initialize $output variable
        $resultCode = 0; // Initialize $resultCode variable
// Execute the pg_dump command for remote database
        $output = shell_exec($remoteDumpCommand);
        echo $output;
        // Check if pg_dump command was successful
        if ($output === null) {
            echo "Remote database dump completed successfully.\n";
        } else {
            echo "Error: Remote database dump failed.\n";
            echo "Command output: $output\n";
            exit(1); // Exit script with error code
        }

        // Local PostgreSQL connection parameters
        $localDsn = "host=$localHost port=$localPort dbname=$localDatabase user=$localUsername password=$localPassword";

        // Establish connection to local PostgreSQL server
        $localDbConn = pg_connect($localDsn);

        if (!$localDbConn) {
            die("Error in connection to local database: " . pg_last_error());
        }
        // Construct the psql command to drop all tables in the local database
        $dropTablesCommand = "/usr/pgsql-13/bin/psql -h $localHost -p $localPort -U $localUsername -d $localDatabase -c \"DROP SCHEMA public CASCADE; CREATE SCHEMA public;\"";

        // Execute the command to drop all tables in the local database
        $output1 = shell_exec($dropTablesCommand);

        // Check if the drop tables command was successful
        if ($output1 === null) {
            echo "All tables dropped successfully from the local database.\n";
        } else {
            echo "Error dropping tables in the local database.\n";
            echo "Command output: $output1\n";
            exit(1); // Exit script with error code
        }
        // Read the SQL dump file content
/*$sqlDumpContent = file_get_contents($backupFile);

// Execute the SQL dump content on the local database
if (pg_query($localDbConn, $sqlDumpContent)) {
    echo "Database restored successfully on local server.\n";
} else {
    echo "Error restoring database on local server: " . pg_last_error($localDbConn) . "\n";
}*/
        //$remoteDumpCommand="psql -h localhost -U symmetryindia symmetryindia_postgear_aws_main < remote_database_dump_1.sql";
        $remoteDumpCommand = "/usr/pgsql-13/bin/psql -h $localHost -p $localPort -U $localUsername -d $localDatabase -f $backupFile";
        $output = shell_exec($remoteDumpCommand);
        echo $output;
        // Close local database connection
        pg_close($localDbConn);

        // Optional: Clean up the SQL dump file
//unlink($backupFile);
    }

    public function DumpPostgearToMysql()
    {


        // Remote PostgreSQL database credentials
        $remoteHost = 'doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com';
        $remotePort = '58272';
        $remoteUsername = 'postgres';
        $remotePassword = 'heroism_neon_77!';
        $remoteDatabase = 'doc2db';

        // Local PostgreSQL database credentials
        $localHost = 'localhost';
        $localPort = '5432';
        $localUsername = 'symmetryindia';
        $localPassword = 'Y$I3Q#U2[Dw_';
        $localDatabase = 'symmetryindia_postgear_aws_main';

        // Path to store the generated gzipped database dump file locally
        $backupFile = 'remote_database_dump_1.sql.gz';

        try {
            // Generate gzipped SQL dump file from remote database
            $dumpCommand = "/usr/pgsql-15/bin/pg_dump -h $remoteHost -p $remotePort -U $remoteUsername -d $remoteDatabase | gzip > $backupFile";
            exec($dumpCommand, $output, $resultCode);

            if ($resultCode !== 0) {
                throw new Exception("Failed to generate database dump. Command output: " . implode("\n", $output));
            }

            echo "Remote database dump created successfully.\n";

            // Import the gzipped SQL dump file into local database
            $importCommand = "/bin/zcat $backupFile | /usr/pgsql-13/bin/psql -h $localHost -p $localPort -U $localUsername -d $localDatabase";


            // Execute the command to drop all tables in the local database
            $output2 = shell_exec($importCommand);

            print_r($output2);
            // Check if pg_dump command was successful
            if ($output2 === null) {
                echo "Remote database dump completed successfully.\n";
            } else {
                echo "Error: Remote database dump failed.\n";
                echo "Command output: $output2\n";
                exit(1); // Exit script with error code
            }

            echo "Database dump imported successfully into local server.\n";

            // Clean up the gzipped dump file
            unlink($backupFile);

            echo "Cleanup completed.\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }

    }

    public function aa_dfia_licence_details_main()
    {
        $query = "SELECT * from aa_dfia_licence_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();
        echo count($rows);
        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /*$dfia_licence_details_id= addslashes($str["dfia_licence_details_id"]);
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
             $created_at = addslashes($str["created_at"]);*/


            // Build and execute the INSERT query
            $sql_insert = "INSERT INTO aa_dfia_licence_details 
                   (dfia_licence_details_id, item_id, inv_s_no, item_s_no_, licence_no, descn_of_export_item, 
                    exp_s_no, expqty, uqc_aa, fob_value, sion, descn_of_import_item, imp_s_no, 
                    impqt, uqc_, indig_imp, created_at) 
                   VALUES ('{$values['dfia_licence_details_id']}', '{$values['item_id']}', '{$values['inv_s_no']}', 
                           '{$values['item_s_no_']}', '{$values['licence_no']}', '{$values['descn_of_export_item']}', 
                           '{$values['exp_s_no']}', '{$values['expqty']}', '{$values['uqc_aa']}', '{$values['fob_value']}', 
                           '{$values['sion']}', '{$values['descn_of_import_item']}', '{$values['imp_s_no']}', 
                           '{$values['impqt']}', '{$values['uqc_']}', '{$values['indig_imp']}', '{$values['created_at']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }

    public function bill_bond_details_main()
    {
        $query = "SELECT * from bill_bond_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $str_bill_bond_details) {
            /**************************************************/
            $boe_id = addslashes($str_bill_bond_details["boe_id"]);
            $be_no = addslashes($str_bill_bond_details["be_no"]);
            $bond_details_id = addslashes($str_bill_bond_details["bond_details_id"]);
            $bond_no = addslashes($str_bill_bond_details["bond_no"]);
            $port = addslashes($str_bill_bond_details["port"]);
            $bond_cd = addslashes($str_bill_bond_details["bond_cd"]);
            $debt_amt = addslashes($str_bill_bond_details["debt_amt"]);
            $bg_amt = addslashes($str_bill_bond_details["bg_amt"]);
            $created_at = addslashes($str_bill_bond_details["created_at"]);
            $sql_insert_bill_bond_details =
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
            $copy_bill_bond_details = $Db1->query(
                $sql_insert_bill_bond_details
            );

        }
        $Db1->close();
        $this->db->close();

    }


    public function bill_container_details_main()
    {
        $query = "SELECT * from bill_container_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            // Build and execute the INSERT query
            echo $sql_insert = "INSERT INTO `bill_container_details` (`container_details_pk`, `boe_id`,`sno`, `lcl_fcl`, `truck`, `seal`, `container_number`,`created_at`) 

                   VALUES ('{$values['container_details_id']}', '{$values['boe_id']}', '{$values['sno']}', 
                           '{$values['lcl_fcl']}', '{$values['truck']}', '{$values['seal']}', 
                           '{$values['container_number']}', '{$values['created_at']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }

    public function bill_licence_details_main()
    {
        $query = "SELECT * from bill_licence_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            // Build and execute the INSERT query
            $sql_insert = "INSERT INTO `bill_licence_details` (`duties_id`, `invsno`, `itemsn`, `lic_slno`, `lic_no`, `lic_date`,`code`,`port`,`debit_value`,`qty`,`uqc_lc_d`,`debit_duty`,`created_at`) 
    VALUES ('{$values['duties_id']}', '{$values['invsno']}', '{$values['itemsn']}', 
                           '{$values['lic_slno']}', '{$values['lic_no']}', '{$values['lic_date']}', 
                           '{$values['code']}', '{$values['port']}', '{$values['debit_value']}', '{$values['qty']}', '{$values['uqc_lc_d']}', '{$values['debit_duty']}', '{$values['created_at']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }

    public function bill_manifest_details_main()
    {
        $query = "SELECT * from bill_manifest_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            // Build and execute the INSERT query
            $sql_insert = "INSERT INTO `bill_manifest_details` (`boe_id`, `igm_no`, `igm_date`, `inw_date`, `gigmno`, `gigmdt`,`mawb_no`,`mawb_date`,`hawb_no`,`hawb_date`,`pkg`,`gw`,`created_at`)
                   VALUES ('{$values['boe_id']}', '{$values['igm_no']}', '{$values['igm_date']}', 
                           '{$values['inw_date']}', '{$values['gigmno']}', '{$values['gigmdt']}', 
                           '{$values['mawb_no']}', '{$values['mawb_date']}', '{$values['hawb_no']}', '{$values['hawb_date']}', '{$values['pkg']}', '{$values['gw']}', '{$values['created_at']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }

    public function bill_of_entry_summary_main()
    {
        $query = "SELECT * from bill_of_entry_summary";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $str1) {
            /**************************************************/
            // Escape and prepare values for insertion
            /*   $values = [];
               foreach ($row as $key => $value) {
                   $values[$key] = $Db1->real_escape_string(@$value);
               }*/
            /**************************************************/
            // Build and execute the INSERT query

            /*$nccd=@$values['nccd'];$sg=@$values['sg'];
 $sql_insert="INSERT INTO `bill_of_entry_summary`( `boe_id`,`boe_file_status_id`, `invoice_title`, `port`, `port_code`, `be_no`, `be_date`, `be_type`, `iec_br`, `iec_no`, `br`, `gstin_type`, `cb_code`, `nos`,
 `pkg`, `item`, `g_wt_kgs`, `cont`, `be_status`, `mode`, `def_be`, `kacha`, `sec_48`, `reimp`, `adv_be`, `assess`, `exam`, `hss`, `first_check`, `prov_final`, 
 `country_of_origin`, `country_of_consignment`, `port_of_loading`, `port_of_shipment`, `importer_name_and_address`, `ad_code`, `cb_name`, `aeo`, `ucr`, `bcd`, 
 `acd`, `sws`, `nccd`, `add`, `cvd`, `igst`, `g_cess`, `sg`, `saed`, `gsia`, `tta`, `health`, `total_duty`, `int`, `pnlty`, `fine`, `tot_ass_val`, `tot_amount`, 
 `wbe_no`, `wbe_date`, `wbe_site`, `wh_code`, `submission_date`, `assessment_date`, `examination_date`, `ooc_date`, `submission_time`, `assessment_time`, 
 `examination_time`, `ooc_time`, `submission_exchange_rate`, `assessment_exchange_rate`, `ooc_no`, `ooc_date_`, `created_at`, `examination_exchange_rate`, 
 `ooc_exchange_rate`) VALUES ('{$values['boe_id']}', '{$values['boe_file_status_id']}', '{$values['invoice_title']}', '{$values['port']}', '{$values['port_code']}', '{$values['be_no']}', '{$values['be_date']}', '{$values['be_type']}', '{$values['iec_br']}', '{$values['iec_no']}', '{$values['br']}', '{$values['gstin_type']}', '{$values['cb_code']}', '{$values['nos']}',
 '{$values['pkg']}', '{$values['item']}', '{$values['g_wt_kgs']}', '{$values['cont']}', '{$values['be_status']}', '{$values['mode']}', '{$values['def_be']}', '{$values['kacha']}', '{$values['sec_48']}', '{$values['reimp']}', '{$values['adv_be']}', '{$values['assess']}', '{$values['exam']}', '{$values['hss']}', '{$values['first_check']}', '{$values['prov_final']}', 
 '{$values['country_of_origin']}', '{$values['country_of_consignment']}', '{$values['port_of_loading']}', '{$values['port_of_shipment']}', '{$values['importer_name_and_address']}', '{$values['ad_code']}', '{$values['cb_name']}', '{$values['aeo']}', '{$values['ucr']}', '{$values['bcd']}', 
 '{$values['acd']}', '{$values['sws']}', $nccd, '{$values['add']}', '{$values['cvd']}', '{$values['igst']}', '{$values['g_cess']}', $sg, '{$values['saed']}', '{$values['gsia']}', '{$values['tta']}', '{$values['health']}', '{$values['total_duty']}', '{$values['int']}', '{$values['pnlty']}', '{$values['fine']}', '{$values['tot_ass_val']}', '{$values['tot_amount']}', 
 '{$values['wbe_no']}', '{$values['wbe_date']}', '{$values['wbe_site']}', '{$values['wh_code']}', '{$values['submission_date']}', '{$values['assessment_date']}', '{$values['examination_date']}', '{$values['ooc_date']}', '{$values['submission_time']}', '{$values['assessment_time']}', 
 '{$values['examination_time']}', '{$values['ooc_time']}', '{$values['submission_exchange_rate']}', '{$values['assessment_exchange_rate']}', '{$values['ooc_no']}', '{$values['ooc_date_']}', '{$values['created_at']}', '{$values['examination_exchange_rate']}', 
 '{$values['ooc_exchange_rate']}')";
*/
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
            $country_of_origin = addslashes($str1["country_of_origin"]);
            $importer_name_and_address = addslashes($str1["importer_name_and_address"]);
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
            $wbe_site = addslashes($str1["wbe_site"]);
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



            if ($int == '') {
                $int = 0;
            }
            if ($bcd == '') {
                $bcd = 0;
            }
            if ($acd == '') {
                $acd = 0;
            }
            if ($sws == '') {
                $sws = 0;
            }
            if ($nccd == '') {
                $nccd = 0;
            }
            if ($add == '') {
                $add = 0;
            }
            if ($cvd == '') {
                $cvd = 0;
            }
            if ($igst == '') {
                $igst = 0;
            }
            if ($g_cess == '') {
                $g_cess = 0;
            }
            if ($sg == '') {
                $sg = 0;
            }
            if ($saed == '') {
                $saed = 0;
            }
            if ($gsia == '') {
                $gsia = 0;
            }
            if ($tta == '') {
                $tta = 0;
            }
            if ($health == '') {
                $health = 0;
            }
            if ($total_duty == '') {
                $total_duty = 0;
            }
            if ($pnlty == '') {
                $pnlty = 0;
            }
            if ($fine == '') {
                $fine = 0;
            }
            if ($tot_ass_val == '') {
                $tot_ass_val = 0;
            }
            if ($tot_amount == '') {
                $tot_amount = 0;
            }

            // $submission_date;
            $wbe_date = date("Y-m-d", strtotime($wbe_date));
            $submission_date = date("Y-m-d", strtotime($submission_date));
            $assessment_date = date("Y-m-d", strtotime($assessment_date));
            $examination_date = date("Y-m-d", strtotime($examination_date));
            $ooc_date = date("Y-m-d", strtotime($ooc_date));
            echo $sql_insert1 =
                "INSERT INTO `bill_of_entry_summary`( `boe_id`,`boe_file_status_id`, `invoice_title`, `port`, `port_code`, `be_no`, `be_date`, `be_type`, `iec_br`, `iec_no`, `br`, `gstin_type`, `cb_code`, `nos`, `pkg`, `item`, `g_wt_kgs`, `cont`, `be_status`, `mode`, `def_be`, `kacha`, `sec_48`, `reimp`, `adv_be`, `assess`, `exam`, `hss`, `first_check`, `prov_final`, `country_of_origin`, `country_of_consignment`, `port_of_loading`, `port_of_shipment`, `importer_name_and_address`, `ad_code`, `cb_name`, `aeo`, `ucr`, `bcd`, `acd`, `sws`, `nccd`, `add`, `cvd`, `igst`, `g_cess`, `sg`, `saed`, `gsia`, `tta`, `health`, `total_duty`, `int`, `pnlty`, `fine`, `tot_ass_val`, `tot_amount`, `wbe_no`, `wbe_date`, `wbe_site`, `wh_code`, `submission_date`, `assessment_date`, `examination_date`, `ooc_date`, `submission_time`, `assessment_time`, `examination_time`, `ooc_time`, `submission_exchange_rate`, `assessment_exchange_rate`, `ooc_no`, `ooc_date_`, `created_at`, `examination_exchange_rate`, `ooc_exchange_rate`) 
                    VALUES ('" . $boe_id .
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
                @$nccd .
                "','" .
                $add .
                "','" .
                $cvd .
                "', '" .
                $igst .
                "','" .
                $g_cess .
                "','" .
                @$sg .
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
            // print_r($iecwise1_beo);exit;
            //   $copy_bill_of_entry_summary = $db1->query($sql_insert1);



            if ($Db1->query($sql_insert1) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }

    public function bill_payment_details_main()
    {
        $query = "SELECT * from bill_payment_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            $paid_on = date("Y-m-d", strtotime($values['paid_on']));

            // Build and execute the INSERT query
            $sql_insert = "INSERT INTO `bill_payment_details` (`boe_id`, `payment_details_id`, `sr_no`, `challan_no`, `amount`, `created_at`, `paid_on`)
                   VALUES ('{$values['boe_id']}', '{$values['payment_details_id']}', '{$values['sr_no']}', 
                           '{$values['challan_no']}', '{$values['amount']}', '{$values['created_at']}', 
                           '$paid_on')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }


    public function challan_details_main()
    {
        $query = "SELECT * from challan_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            // Build and execute the INSERT query
            $paymt_dt = date("Y-m-d", strtotime($values['paymt_dt']));
            $sql_insert = "INSERT INTO `challan_details` (`challan_id`, `sbs_id`, `sr_no`, `challan_no`, `paymt_dt`, `amount`, `created_at`)
                   VALUES ('{$values['challan_id']}', '{$values['sbs_id']}', '{$values['sr_no']}', 
                           '{$values['challan_no']}','{$paymt_dt}', '{$values['amount']}', '{$values['created_at']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }

    public function drawback_details_main()
    {
        $query = "SELECT * from drawback_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            // Build and execute the INSERT query
            //$paymt_dt =   date("Y-m-d",strtotime($values['paymt_dt']));
            $sql_insert = "INSERT INTO `drawback_details` (`drawback_id`,`item_id`, `inv_sno`, `item_sno`, `dbk_sno`, `qty_wt`, `value`,`dbk_amt`,`stalev`,`cenlev`,
  `rosctl_amt`,`created_at`,`rate`,`rebate`,`amount`,`dbk_rosl`)
                   VALUES ('{$values['drawback_id']}', '{$values['item_id']}', '{$values['inv_sno']}', 
                           '{$values['item_sno']}','{$values['dbk_sno']}', '{$values['qty_wt']}', '{$values['value']}', '{$values['dbk_amt']}', '{$values['stalev']}'
                           , '{$values['cenlev']}' , '{$values['rosctl_amt']}' , '{$values['created_at']}', '{$values['rate']}', '{$values['rebate']}', '{$values['amount']}', '{$values['dbk_rosl']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }
    public function item_details_main()
    {
        $query = "SELECT * from item_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $str_item_details) {
            /**************************************************/
            // Escape and prepare values for insertion
            $reference_code = addslashes($str_item_details["reference_code"]);
            $item_id = addslashes($str_item_details["item_id"]);
            $sb_date = addslashes($str_item_details["sb_date"]);
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
            /**************************************************/
            // Build and execute the INSERT query
            //$paymt_dt =   date("Y-m-d",strtotime($values['paymt_dt']));
            $sql_insert = "INSERT INTO `item_details` (`item_id`, `invoice_id`, `invsn`, `item_s_no`, `hs_cd`, `description`, `quantity`, `uqc`, `rate`, `value_f_c`, `fob_inr`, `pmv`, `duty_amt`, `cess_rt`,`cesamt`, `dbkclmd`,
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
 `created_at`)VALUES ('" .
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

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }
    public function duties_and_additional_details_main()
    {
        $query = "SELECT * from duties_and_additional_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);
        $batchSize = 1000;

        // Loop through the records in batches of 9000
        for ($offset = 0; $offset < count($rows); $offset += $batchSize) {
            // Insert data into MySQL table
            foreach ($rows as $str_duties_and_additional_details) {
                /**************************************************/

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

                /***********************************************************************/
                echo $sql_insert_duties_and_additional_details =
                    "INSERT INTO `duties_and_additional_details` (boe_id, invoice_id, duties_id, s_no, cth, description, unit_price, quantity, uqc, amount, invsno, itemsn, cth_item_detail, ceth, item_description, fs, pq, dc, wc, aq, upi, coo, c_qty, c_uqc, s_qty, s_uqc, sch, stdn_pr, rsp, reimp, prov, end_use, prodn, cntrl, qualfr, contnt, stmnt, sup_docs, assess_value, total_duty, bcd_notn_no, bcd_notn_sno, bcd_rate, bcd_amount, bcd_duty_fg, acd_notn_no, acd_notn_sno, acd_rate, acd_amount, acd_duty_fg, sws_notn_no, sws_notn_sno, sws_rate, sws_amount, sws_duty_fg, sad_notn_no, sad_notn_sno, sad_rate, sad_amount, sad_duty_fg, igst_notn_no, igst_notn_sno, igst_rate, igst_amount, igst_duty_fg, g_cess_notn_no, g_cess_notn_sno, g_cess_rate, g_cess_amount, g_cess_duty_fg, add_notn_no, add_notn_sno, add_rate, add_amount, add_duty_fg, cvd_notn_no, cvd_notn_sno, cvd_rate, cvd_amount, cvd_duty_fg, sg_notn_no, sg_notn_sno, sg_rate, sg_amount, sg_duty_fg, t_value_notn_no, t_value_notn_sno, t_value_rate, t_value_amount, t_value_duty_fg, sp_excd_notn_no, sp_excd_notn_sno, sp_excd_rate, sp_excd_amount, sp_excd_duty_fg, chcess_notn_no, chcess_notn_sno, chcess_rate, chcess_amount, chcess_duty_fg, tta_notn_no, tta_notn_sno, tta_rate, tta_amount, tta_duty_fg, cess_notn_no, cess_notn_sno, cess_rate, cess_amount, cess_duty_fg, caidc_cvd_edc_notn_no, caidc_cvd_edc_notn_sno, caidc_cvd_edc_rate, caidc_cvd_edc_amount, caidc_cvd_edc_duty_fg, eaidc_cvd_hec_notn_no, eaidc_cvd_hec_notn_sno, eaidc_cvd_hec_rate, eaidc_cvd_hec_amount, eaidc_cvd_hec_duty_fg, cus_edc_notn_no, cus_edc_notn_sno, cus_edc_rate, cus_edc_amount, cus_edc_duty_fg, cus_hec_notn_no, cus_hec_notn_sno, cus_hec_rate, cus_hec_amount, cus_hec_duty_fg, ncd_notn_no, ncd_notn_sno, ncd_rate, ncd_amount, ncd_duty_fg, aggr_notn_no, aggr_notn_sno, aggr_rate, aggr_amount, aggr_duty_fg, invsno_add_details, itmsno_add_details, refno, refdt, prtcd_svb_d, lab, pf, load_date, pf_, beno, bedate, prtcd, unitprice, currency_code, frt, ins, duty, sb_no, sb_dt, portcd, sinv, sitemn, type, manufact_cd, source_cy, trans_cy, address, accessory_item_details, notno, slno, created_at) 
VALUES('" . $boe_id .
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
                    $created_at . "')";

                $copy_insert_duties_and_additional_details = $Db1->query(
                    $sql_insert_duties_and_additional_details
                );
                if ($Db1->query($sql_insert) === TRUE) {
                    echo "Record inserted successfully<br>";
                } else {
                    echo "Error inserting record: " . $Db1->error . "<br>";
                }

            }
            $Db1->close();
            $this->db->close();

        }
    }

    public function equipment_details_main()
    {
        $query = "SELECT * from equipment_details";
        $statement = $this->db->query($query);
        $iecwise_data = [];
        $rows = $statement->result_array();

        //$rows = $this->db->fetchAll(PDO::FETCH_ASSOC);
        // MySQL database connection
        $hostname = "localhost";
        $username = "root";
        $password = "%!#^bFjB)z8C";
        $db_name1 = "d2d_aws_main";
        $Db1 = new mysqli($hostname, $username, $password, $db_name1);

        // Insert data into MySQL table
        foreach ($rows as $row) {
            /**************************************************/
            // Escape and prepare values for insertion
            $values = [];
            foreach ($row as $key => $value) {
                $values[$key] = $Db1->real_escape_string($value);
            }
            /**************************************************/
            // Build and execute the INSERT query
            $date = date("Y-m-d", strtotime($values['date']));
            $sql_insert = "INSERT INTO `equipment_details` (`equip_id`,`sbs_id`, `container`, `seal`, `date`, `s_no`, `created_at`)
                   VALUES ('{$values['equip_id']}', '{$values['sbs_id']}', '{$values['container']}', 
                           '{$values['seal']}','{$date}', '{$values['s_no']}', '{$values['created_at']}')";

            if ($Db1->query($sql_insert) === TRUE) {
                echo "Record inserted successfully<br>";
            } else {
                echo "Error inserting record: " . $Db1->error . "<br>";
            }

        }
        $Db1->close();
        $this->db->close();

    }


}