<?php       
    include 'connect.php'; 
    include 'includes/header.php';  

?>


<script src="js/register.js"></script>

<!-- Sharkie Notes:
    - naming sense is camelCase for php variables and snake_case for database columns,
     and for html elements, use lowercase with hyphens


-->


<div class="register-container">
    <form method="POST">
            <label for="email" class="input-group">Email:</label>
            <input type="email" name="txtemail" id="email" required><br>

            <label for="firstname" class="input-group">Firstname:</label>
            <input type="text" name="txtfirstname" id="firstname" required><br>

            <label for="lastname" class="input-group">Lastname:</label>
            <input type="text" name="txtlastname" id="lastname" required><br>

            <label for="usertype" class="input-group">User Type:</label>
            <select name="txtuserType" id="usertype" required>
                <option value="admin">Admin</option>
            </select><br>

            <div name="admin-container">
                <label for="admincode" class="input-group">Admin Code:</label>
                <input type="text" name="txtadminCode" id="admincode" value="1234" readonly><br>
            </div>

            <label for="password" class="input-group">Password:</label>
            <input type="password" name="txtpassword" id="password" required><br>
            
            <label for="confirmPassword" class="input-group">Confirm Password:</label>  
            <input type="password" name="txtconfirmPassword" id="confirmPassword" required><br>

            <input type="submit" name="btnregister" value="Register">
        </pre>
    </form>
</div>

<?php 
    if(isset($_POST['btnregister'])){
        //retrieve data from form and save tbe value to a variable
        //for tblusers
        $fname = $_POST['txtfirstname'];
        $lname = $_POST['txtlastname'];
        $userType = $_POST['txtuserType'];
        $email = $_POST['txtemail'];
        $password = $_POST['txtpassword'];
        $confirmPassword = $_POST['txtconfirmPassword'];

        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        
        if($password === $confirmPassword){
            $sql2 = "Select * from tblusers where user_email = '".$email."'";
            $result = mysqli_query($connection, $sql2);
            $row = mysqli_num_rows($result);

            if($row == 0){

                $sql = "Insert into tblusers(lastname, firstname, user_email, user_password, user_role) 
                                    values ('".$lname."','".$fname."','".$email."','".$hashedPass."', '".$userType."')";
                $result = mysqli_query($connection, $sql);
                echo "<script language = 'javascript'>
                    alert('Registration Successful!');
                    </script>";
                header("location: dashboard.php");
            } else {
                echo "<script language = 'javascript'>
                    alert('Email already exists!');
                    </script>";
            }

        } else {
            echo "<script language = 'javascript'>
                    alert('Password does not match!');
                    </script>";
        }

    }

?>