<?php
$host = "localhost";     
$user = "root";        
$pass = "";              
$db   = "expensevoyage";  

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
