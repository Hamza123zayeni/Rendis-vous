<?php
$host = "db";  // Reste "db" pour Docker
$user = "root";
$password = "root";
$dbname = "edoc";

try {
    $database = new mysqli($host, $user, $password, $dbname);
    
    if ($database->connect_error) {
        throw new Exception("Connection failed: " . $database->connect_error);
    }
    
    $database->set_charset("utf8");
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>