<?php

    $connection = new mysqli('localhost', 'root', '', 'dbprintingsystem');
    
    if(!$connection){
        die('Connection Failed: ' . mysqli_error($connection));
    }

?>