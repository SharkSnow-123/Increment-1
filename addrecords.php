
<?php
    session_start();
    include 'connect.php';
    include 'includes/header.php';

?>

<div class="login-container"> <div class="form-section">
        <div class="form-content">
            <h1>Add New Record</h1>
            <p class="subtitle">Enter details to create a user profile.</p>

            <form method="POST" action="addrecords.php">
                <div class="input-group">
                    <input type="text" name="txtFirstname" placeholder=" " required>
                    <label>First Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="txtLastname" placeholder=" " required>
                    <label>Last Name</label>
                </div>
                <div class="input-group">
                    <input type="email" name="txtEmail" placeholder=" " required>
                    <label>Email Address</label>
                </div>
                
                <div class="input-group">
                    <select name="userType" id="userType" onchange="toggleFields()" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="Staff">Staff</option>
                        <option value="Student">Student</option>
                    </select>
                </div>

                <div id="studentFields" style="display:none;">
                    <div class="input-group">
                        <input type="text" name="txtProgram" placeholder=" ">
                        <label>Program (e.g. BSIT)</label>
                    </div>
                    <div class="input-group">
                       <select name="txtYear" id="year" required>
                        <option value="1st-year">1</option>
                        <option value="2nd-year">2</option>
                        <option value="3rd-year">3</option>
                        <option value="4th-year">4</option>
                        <option value="5th-year">5</option>
                    </select>
                    </div>
                </div>

                <button type="submit" name="btnSaveRecord" class="btnLogin">Save Record</button>
                <br>
                <center><a href="dashboard.php" style="font-size: 12px; color: #888;">Back to Dashboard</a></center>
            </form>
        </div>
    </div>

    <div class="image-section">
        <img src="images/wildcat.png" alt="Branding">
    </div>
</div>



<?php
if (isset($_POST['btnSaveRecord'])) {
    $lname = $_POST['txtLastname'];
    $fname = $_POST['txtFirstname'];
    $email = $_POST['txtEmail'];
    $role = $_POST['userType'];

    // Default password for new users, they can change it later
    $password = password_hash("password123", PASSWORD_DEFAULT); 

    $sqlUser = "INSERT INTO tblusers (lastname, firstname, user_email, user_password, user_role) 
                VALUES ('".$lname."', '".$fname."', '".$email."', '".$password."', '".$role."')";

    if (mysqli_query($connection, $sqlUser)) {
        $new_id = mysqli_insert_id($connection);

        if ($role == 'Student') {
            $program = $_POST['txtProgram'];
            $year = $_POST['txtYear'];
            $sqlExtra = "INSERT INTO tblstudent (user_id, student_program, year_level) 
                         VALUES ('$new_id', '$program', '$year')";
        } elseif ($role == 'Staff') {
            $sqlExtra = "INSERT INTO tblstaff (user_id) VALUES ('$new_id')";
        }

        if (isset($sqlExtra)) { mysqli_query($connection, $sqlExtra); }
        header("Location: dashboard.php");
        echo "<script>
                alert('New $role added successfully!'); 
            </script>";
        echo "Error: " . mysqli_error($connection);
    }
}
?>


<script>
function toggleFields() {
    var role = document.getElementById("userType").value;
    var studentSection = document.getElementById("studentFields");
    studentSection.style.display = (role === "Student") ? "block" : "none";
}
</script>

<?php include 'includes/footer.php'; ?>