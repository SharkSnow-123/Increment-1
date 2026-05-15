<?php       
    session_start();    
    include 'connect.php'; 
    include 'readrecords.php';
    include 'includes/header.php';

    $role = $_SESSION['user_role'];

?>

<div class="dashboard-wrapper">
    <div class="banner">
        <h2>
            <?php
                if ($role === 'Admin') echo 'ADMIN DASHBOARD';
                elseif ($role === 'Staff') echo 'STAFF DASHBOARD';
                elseif ($role === 'Student') echo 'STUDENT DASHBOARD';
            ?>
        </h2>
    </div>

    <?php if ($role === 'Admin'): ?>
    <!-- ===================== ADMIN SCREEN ===================== -->
    <div class="content-section">
        <h1>Welcome, Admin!</h1>
        <p class="subtitle">Management portal for Admin use only.</p>

        <div class="action-links">
            <a href="addrecords.php" class="link-add">+ Add New User</a>
            <a href="logout.php" class="link-logout">Logout</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th><th>Lastname</th><th>Firstname</th>
                    <th>Email</th><th>Role</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $resultset->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['lastname'] ?></td>
                    <td><?php echo $row['firstname'] ?></td>
                    <td><?php echo $row['user_email'] ?></td>
                    <td><strong><?php echo $row['user_role'] ?></strong></td>
                    <td>
                        <button type="button" class="btn-update"><a href="update.php?id=<?php echo $row['user_id']; ?>">UPDATE</a></button>


                        <button type="button" class="btn-delete" onclick="showConfirm('Are you sure you want to delete <?php echo $row['firstname']; ?>?', function() {
                            window.location.href = 'deleterecords.php?id=<?php echo $row['user_id']; ?>';
                        })">DELETE</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($role === 'Staff'): ?>
    <!-- ===================== STAFF SCREEN ===================== -->
    <div class="content-section">
        <h1>Welcome, Staff!</h1>
        <p class="subtitle">Your staff portal.</p>

        <div class="action-links">
            <a href="logout.php" class="link-logout">Logout</a>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th><th>Lastname</th><th>Firstname</th>
                    <th>Email</th><th>Role</th><th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while($row = $resultset -> fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['lastname'] ?></td>
                    <td><?php echo $row['firstname'] ?></td>
                    <td><?php echo $row['user_email'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>



        </table>


    </div>

    <?php elseif ($role === 'Student'): ?>
    <!-- ===================== STUDENT SCREEN ===================== -->
    <div class="content-section">
        <h1>Welcome, Student!</h1>
        <p class="subtitle">Your student portal.</p>

        <div class="action-links">
            <a href="logout.php" class="link-logout">Logout</a>
        </div>


        <!-- Per ari implement ang student later -->
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.
             Natus odit saepe consequuntur consequatur eaque eius molestias? 
             Nulla et totam neque officia sunt corporis tenetur quasi. 
             Vero illo ratione consequuntur libero?
        </p>
    </div>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
