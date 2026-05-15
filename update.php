
<?php 

    include 'connect.php'; 
    include 'includes/header.php';


    if(!$connection){
        die("Connection failed: " . mysqli_connect_error());
    }

    $id = $_GET['id'] ?? null;
    $currentUser = ['user_email' => '', 'firstname' => '', 'lastname' => ''];

    if($id){
        $getUser = mysqli_query($connection, "SELECT * FROM tblusers WHERE user_id = '$id'");
        if(mysqli_num_rows($getUser) > 0) {
            $currentUser = mysqli_fetch_array($getUser);
        }
    }


    if(isset($_POST['btnupdate'])){

        $fname = $_POST['txtfirstname'];
        $lname = $_POST['txtlastname'];
        $email = $_POST['txtemail'];

        $query = "UPDATE tblusers SET firstname='$fname', lastname='$lname', user_email='$email' WHERE user_id='$id'";
        mysqli_query($connection, $query);
        header("Location: dashboard.php");
    }

    // 3. Logic for resetting password
    if(isset($_POST['btnReset'])){
        $defaultPass = password_hash("password123", PASSWORD_DEFAULT);
        $query = "UPDATE tblusers SET user_password='$defaultPass' WHERE user_id='$id'";
        mysqli_query($connection, $query);
         echo "<script>showAlert('success', 'Password Reset', 'Password has been reset to password123.');</script>";
    }
?>


<div class="update-container">
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="txtemail" id="email" value="<?php echo $currentUser['user_email']; ?>"><br>

        <label for="firstname">Firstname:</label>
        <input type="text" name="txtfirstname" id="firstname" value="<?php echo $currentUser['firstname']; ?>"><br>

        <label for="lastname" >Lastname:</label>
        <input type="text" name="txtlastname" id="lastname" value="<?php echo $currentUser['lastname']; ?>"><br>
        
        <div style="display:flex; gap:10px; margin-top:10px;">
            <!-- Update button uses showConfirm() from footer.php -->
        <input type="submit" name="btnupdate" value="Update" class="btn-update"
            onclick="event.preventDefault();
                showConfirm(
                    'Update User?',
                    'Are you sure you want to save these changes?',
                    'Update',
                function(){ document.querySelector('[name=btnupdate]').form.submit(); }
            );">

            <!-- Reset button uses showConfirm() from footer.php -->
            <input type="submit" name="btnReset" value="Reset Password"
                onclick="event.preventDefault();
                    showConfirm(
                        'Reset Password?',
                        'This will reset the password to password123.',
                        'Reset',
                    function(){ document.querySelector('[name=btnReset]').form.submit(); }
                );">

            <button type="button" class="btn-cancel" onclick="window.location.href='dashboard.php'">
        </div>
    </form>
</div>

<?php 
    include 'includes/footer.php';
?>
