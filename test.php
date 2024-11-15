<?php 


//phpinfo();
$host = "doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com";
$port = "58272";       //note: 5434 is the correct port
$user = "postgres"; 
$pass = "heroism_neon_77!"; 
$db = "doc2db"; 




$con = pg_connect("host=$host port=$port dbname=$db user=$user password=$pass")
    or die ("Could not connect to server\n"); 

$query = "SELECT VERSION()"; 
$rs = pg_query($con, $query) or die("Cannot execute query: $query\n"); 
$row = pg_fetch_row($rs);

echo "Database version... <br>";
echo $row[0] . "\n";
echo "<br><br>";
/* 'dsn'      => 'pgsql:host=doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com;port=58272;dbname=doc2db',
    'hostname' => 'doc2data-db.cluster-ro-chxypqtoy5ez.ap-south-1.rds.amazonaws.com',
    'username' => 'postgres',
    'password' => 'heroism_neon_77!',
    'database' => 'doc2db',
    'dbdriver' => 'postgre', // or 'pdo' if using PDO PostgreSQL driver
    'port'     => '58272',*/ ?>