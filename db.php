<?php

    $serverName = "localhost";
    $userName = "root";
    $password = "prakalya123";
    $dbName = "agroculture1";

    $conn = mysqli_connect($serverName, $userName, $password, $dbName);
    if (!$conn)
    {
        die("Connection failed: " . mysqli_connect_error());
    }

?>
