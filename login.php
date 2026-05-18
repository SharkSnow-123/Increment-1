<?php   
    session_start();
    include 'connect.php'; 
    include 'includes/header.php';
?>

<div class="login-container">
    <div class="form-section">
        <div class="form-content">
            <h1>Login to your Account</h1>
            <p class="subtitle">Use your email and password to log in.</p>

            <form method="POST">
                <div class="input-group">
                    <input type="email" name="txtemail" placeholder=" " required>
                    <label>Email</label>
                </div>
                <div class="input-group">
                    <input type="password" name="txtpassword" placeholder=" " required>
                    <label>Password</label>
                </div>
                <button type="submit" name="btnLogin" class="btnLogin">Log In</button>
            </form>
        </div>
    </div>

    <div class="image-section">
        <img src="images/wildcat.png" alt="Login Image">
    </div>
</div>

<?php 

    if(isset($_POST['btnLogin'])) {
    $email = $_POST['txtemail'];
    $pwd = $_POST['txtpassword'];

    //$hashed_pass = password_hash($pwd, PASSWORD_DEFAULT); // Commented its always incorrect password

    //checks if email exists in the database
    $sql = "SELECT * FROM tblusers where user_email = '$email'";
    
    $result = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);

    if($count == 0){
        echo "<script>
        showAlert('error', 'Login Failed', 'Email does not exist. Please try again.');
        </script>";
    } else if (!password_verify($pwd, $row['user_password'])){
        echo "<script>
        showAlert('error', 'Login Failed', 'Incorrect password. Please try again.');
        </script>";
    } else {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_role'] = $row['user_role'];
        echo "<script>
            showAlert('success', 'Login Successful', 'Welcome back, " . $row['firstname'] . "! Redirecting to dashboard...');
            setTimeout(function(){
                window.location.href = 'dashboard.php';
            }, 2000);
        </script>";
        
        exit();

    }
}
?>

<?php include 'includes/footer.php'; ?>
