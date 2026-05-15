<?php 

    include 'connect.php';

    if(!$connection){
        die('Connection Failed: ' . mysqli_error($connection));
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];

        $query = "DELETE FROM tblusers WHERE user_id = '$id'";

        if(mysqli_query($connection, $query)){
            echo "<script>
                    alert('Record deleted successfully!');
                </script>";
            header("Location: dashboard.php");
        } else {
            echo "Error deleting record: " . mysqli_error($connection);
        }
    }


?>

