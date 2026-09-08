<?php

mysqli_report(MYSQLI_REPORT_OFF);

$host = "localhost";
$username = "root";
$password = "";
$database = "library_management";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Something went wrong. Please try again.");
}

mysqli_set_charset($conn, "utf8mb4");

?>