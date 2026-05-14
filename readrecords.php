<?php
    include 'connect.php'; 
    include 'includes/header.php';

    if (!$connection){
        die('Connection Failed: ' . mysqli_error($connection));
    }

    $query = 'SELECT * FROM tblusers';
    $resultset = mysqli_query($connection, $query);

?>