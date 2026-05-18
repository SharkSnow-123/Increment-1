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
    
    <?php 

        //Pagination Logic
        $limit = 7; 
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $limit;

        $totalResult = mysqli_query($connection, "SELECT COUNT(*) AS total FROM tblusers");
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        $resultset = mysqli_query($connection, "SELECT * FROM tblusers LIMIT $limit OFFSET $offset");
    
    ?>




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

            <!-- ===== PAGINATION CONTROLS ===== -->
            <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-top:20px;">

                <!-- Previous button -->
                <?php if ($currentPage > 1): ?>
                    <a href="dashboard.php?page=<?php echo $currentPage - 1; ?>" 
                    style="padding:8px 14px; border:1px solid #ccc; border-radius:6px; text-decoration:none; color:#333;">
                    &laquo; Prev
                    </a>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="dashboard.php?page=<?php echo $i; ?>"
                    style="padding:8px 14px; border-radius:6px; text-decoration:none;
                            <?php echo $i === $currentPage 
                                    ? 'background:#3b82f6; color:white; border:1px solid #3b82f6;' 
                                    : 'border:1px solid #ccc; color:#333;'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <!-- Next button -->
                <?php if ($currentPage < $totalPages): ?>
                    <a href="dashboard.php?page=<?php echo $currentPage + 1; ?>"
                    style="padding:8px 14px; border:1px solid #ccc; border-radius:6px; text-decoration:none; color:#333;">
                    Next &raquo;
                    </a>
                <?php endif; ?>

            </div>

            <!-- Record count info -->
            <p style="text-align:center; color:#888; font-size:13px; margin-top:8px;">
                Showing <?php echo $offset + 1; ?>–<?php echo min($offset + $limit, $totalRecords); ?> 
                of <?php echo $totalRecords; ?> users
            </p>

        </div>
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

                    echo "<script>window.location.href='dashboard.php';</script>"; 
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

      <!-- STUDENT UPDATED BY JAZZ -->
       
  <?php elseif ($role === 'Student'): ?>
<div class="content-section">
    <h1>Welcome, Student!</h1>
    <p class="subtitle">Your student portal.</p>

    <div class="action-links">
        <a href="logout.php" class="link-logout">Logout</a>
    </div>

    <?php
        $studentUserID = $_SESSION['user_id'];

        // Get student_id
        $studentQuery = "SELECT student_id FROM tblstudent WHERE user_id = '$studentUserID'";
        $studentResult = mysqli_query($connection, $studentQuery);
        $studentRow = mysqli_fetch_assoc($studentResult);
        $studentID = $studentRow['student_id'];

        // Handle document upload
        if (isset($_POST['btnUpload'])) {
            $fileName = $_FILES['document']['name'];
            $numPages = $_POST['num_pages'];
            $uploadQuery = "INSERT INTO tbldocument (file_name, number_of_pages, upload_date, user_id)
                            VALUES ('$fileName', '$numPages', NOW(), '$studentUserID')";
            mysqli_query($connection, $uploadQuery);
            echo "<script>window.location.href='dashboard.php';</script>"; 
            exit();
        }

        // Handle reservation cancellation
        if (isset($_POST['btnCancel'])) {
            $cancelID = $_POST['reservation_id'];
            $cancelQuery = "UPDATE tblreservation SET status = 'cancelled'
                            WHERE reservation_id = '$cancelID' AND student_id = '$studentID'";
            mysqli_query($connection, $cancelQuery);
            echo "<script>window.location.href='dashboard.php';</script>"; 
            exit();
        }

        // Get today's total pages used
        $today = date('Y-m-d');
        $pagesQuery = "SELECT COALESCE(SUM(total_pages), 0) AS pages_used
                       FROM tblreservation
                       WHERE student_id = '$studentID'
                       AND reservation_date = '$today'
                       AND status != 'cancelled'";
        $pagesResult = mysqli_query($connection, $pagesQuery);
        $pagesRow = mysqli_fetch_assoc($pagesResult);
        $pagesUsed = $pagesRow['pages_used'];

        // Get active reservations count
        $activeQuery = "SELECT COUNT(*) AS active_count
                        FROM tblreservation
                        WHERE student_id = '$studentID'
                        AND status IN ('pending', 'confirmed')";
        $activeResult = mysqli_query($connection, $activeQuery);
        $activeRow = mysqli_fetch_assoc($activeResult);
        $activeCount = $activeRow['active_count'];

        // Get student's documents
        $docsQuery = "SELECT document_id, file_name, number_of_pages, upload_date
                      FROM tbldocument
                      WHERE user_id = '$studentUserID'
                      ORDER BY upload_date DESC";
        $docsResult = mysqli_query($connection, $docsQuery);

        // Get student's reservations
        $resQuery = "SELECT r.reservation_id, r.reservation_date, r.reservation_time,
                            r.status, r.total_pages, d.file_name
                     FROM tblreservation r
                     LEFT JOIN tbldocument d ON r.document_id = d.document_id
                     WHERE r.student_id = '$studentID'
                     ORDER BY r.reservation_date DESC, r.reservation_time DESC";
        $resResult = mysqli_query($connection, $resQuery);
    ?>

    <!-- SUMMARY BAR -->
    <div class="summary-bar">
        <div class="summary-card">
            <span class="summary-number"><?php echo $pagesUsed; ?>/20</span>
            <span class="summary-label">Pages Used Today</span>
            <div class="page-bar">
                <div class="page-bar-fill" style="width: <?php echo min(($pagesUsed/20)*100, 100); ?>%"></div>
            </div>
        </div>
        <div class="summary-card">
            <span class="summary-number"><?php echo $activeCount; ?></span>
            <span class="summary-label">Active Reservations</span>
        </div>
    </div>

    <!-- MY DOCUMENTS -->
    <h3 class="section-title">My Documents</h3>
    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <div style="display:flex; flex-direction:column; gap:10px; max-width:400px;">
            <input type="file" name="document" accept=".pdf,.doc,.docx" required
                   style="padding:10px; border:1px solid #ddd; border-radius:6px;">
            <input type="number" name="num_pages" placeholder="Number of pages" min="1" max="20" required
                   style="padding:10px; border:1px solid #ddd; border-radius:6px;">
            <button type="submit" name="btnUpload" class="btn-upload">Upload Document</button>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Pages</th>
                <th>Upload Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($doc = mysqli_fetch_assoc($docsResult)): ?>
            <tr>
                <td><?php echo $doc['file_name']; ?></td>
                <td><?php echo $doc['number_of_pages']; ?></td>
                <td><?php echo date('M d, Y', strtotime($doc['upload_date'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- MY RESERVATIONS -->
    <div class="section-header">
        <h3 class="section-title">My Reservations</h3>
        <a href="reservation.php" class="btn-reserve">+ Make a Reservation</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Document</th>
                <th>Date</th>
                <th>Time</th>
                <th>Pages</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($res = mysqli_fetch_assoc($resResult)): ?>
            <tr>
                <td><?php echo $res['reservation_id']; ?></td>
                <td><?php echo $res['file_name'] ?? '—'; ?></td>
                <td><?php echo date('M d, Y', strtotime($res['reservation_date'])); ?></td>
                <td><?php echo date('h:i A', strtotime($res['reservation_time'])); ?></td>
                <td><?php echo $res['total_pages']; ?></td>
                <td>
                    <strong style="color: <?php
                        echo match($res['status']) {
                            'pending'   => 'orange',
                            'confirmed' => 'blue',
                            'completed' => 'green',
                            'cancelled' => 'red',
                            default     => 'black'
                        };
                    ?>">
                        <?php echo strtoupper($res['status']); ?>
                    </strong>
                </td>
                <td>
                    <?php if ($res['status'] === 'pending'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="reservation_id" value="<?php echo $res['reservation_id']; ?>">
                        <button type="submit" name="btnCancel" class="btn-delete"
                            onclick="showConfirm('Cancel Reservation', 'Are you sure you want to cancel this reservation?', 'Yes, Cancel', function(){
                                this.closest('form').submit();
                            }.bind(this)); return false;">
                            CANCEL
                        </button>
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
<?php endif; ?>
<?php include 'includes/footer.php'; ?>