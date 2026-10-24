<?php 
// DB credentials.
define('DB_HOST','127.0.0.1');
define('DB_PORT','3307');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','carrental');
// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>