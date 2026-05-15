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
        
        <!-- Per ari implement ang staff later -->
         <?php 
            $staffUserID = $_SESSION['user_id'];
            $staffQuery = "SELECT staff_id FROM tblstaff WHERE user_id = '$staffUserID'";
            $staffResult = mysqli_query($connection, $staffQuery);
            $staffRow = mysqli_fetch_array($staffResult);
            $staffID = $staffRow['staff_id'];

            if(isset($_POST['btnUpdateStatus'])){
                $reservationID = $_POST['reservation_id'];
                $newStatus = $_POST['new_status'];
                $updateQuery = "UPDATE tblreservation SET status = '$newStatus', staff_id = '$staffID'
                    WHERE reservation_id = '$reservationID'";
                mysqli_query($connection, $updateQuery);

                //if completed, then we'll log it in tblprintlog
                if($newStatus === 'completed'){
                    $getPagesQuery = "SELECT total_pages FROM tblreservation WHERE reservation_id = '$reservationID'";
                    $getPagesResult = mysqli_query($connection, $getPagesQuery);
                    $pagesRow = mysqli_fetch_array($getPagesResult);
                    $pages = $pagesRow['total_pages'];
                    $logQuery = "INSERT INTO tblprintlog (total_pages_printed, reservation_id, staff_id)
                                 VALUES ('$pages', '$reservationID', '$staffID')";
                    mysqli_query($connection, $logQuery);   
                }

                header("Location: dashboard.php");
                exit();

            }

            $reservations = mysqli_query($connection,
            "SELECT r.reservation_id, r.reservation_date, r.reservation_time, r.status, r.total_pages,
            u.firstname, u.lastname, s.student_program
            FROM tblreservation r
            INNER JOIN tblstudent s ON r.student_id = s.student_id
            INNER JOIN tblusers u ON s.user_id = u.user_id
            ORDER BY r.reservation_date ASC, r.reservation_time ASC");
            
         ?>

         <table class="table" >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Program</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Pages</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>
            </thead>

            <tbody>
                <?php while($row = mysqli_fetch_assoc($reservations)): ?>
                <tr>
                    <td><?php echo $row['reservation_id']; ?></td>
                    <td><?php echo $row['lastname'] . ' ' . $row['firstname']; ?></td>
                    <td><?php echo $row['student_program']; ?></td>
                    <td><?php echo $row['reservation_date']; ?></td>
                    <td><?php echo date('h:i A', strtotime($row['reservation_time'])); ?></td>
                    <td><?php echo $row['total_pages']; ?></td>
                    <td>
                        <strong style="color: <?php 
                            echo match($row['status']) {
                                'pending'   => 'orange',
                                'confirmed' => 'blue',
                                'completed' => 'green',
                                'cancelled' => 'red',
                                default     => 'black'
                            };
                        ?>">
                            <?php echo strtoupper($row['status']); ?>
                        </strong>
                    </td>
                    <td>
                        <?php if ($row['status'] !== 'completed' && $row['status'] !== 'cancelled'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                            <select name="new_status">
                                <option value="confirmed">Confirm</option>
                                <option value="completed">Complete</option>
                                <option value="cancelled">Cancel</option>
                            </select>
                            <button type="submit" name="btnUpdateStatus" class="btn-update">Update</button>
                        </form>
                        <?php else: ?>
                            <span style="color: gray; font-size: 12px;">No actions</span>
                        <?php endif; ?>
                    </td>
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
