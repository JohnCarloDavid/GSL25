<!-- config.php -->
<?php
$host = "localhost";
$username = "root";
$password = ""; // Add your database password here
$dbname = "db_gsl25";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
