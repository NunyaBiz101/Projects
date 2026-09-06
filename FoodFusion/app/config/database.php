<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db_mname = "foodfusion_db";

    $conn = new mysqli($servername, $username, $password, $db_mname);

    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
?>