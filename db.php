<?php
$servername = "localhost"; // Change if not local
$username = "uiumzmgo1eg2q";
$password = "kuqi5gwec3tv";
$dbname = "dblavvsskzawbb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
