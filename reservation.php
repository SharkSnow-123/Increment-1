<?php
    session_start();
    include 'connect.php';
    include 'includes/header.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Student') {
        header("Location: index.php");
        exit();
    }

    $studentUserID = $_SESSION['user_id'];
    $message = '';
    $messageType = '';

    // Get student_id
    $studentQuery = "SELECT student_id FROM tblstudent WHERE user_id = '$studentUserID'";
    $studentResult = mysqli_query($connection, $studentQuery);
    $studentRow = mysqli_fetch_assoc($studentResult);
    $studentID = $studentRow['student_id'];

    // Handle form submission
    if (isset($_POST['btnReserve'])) {
        $resDate  = $_POST['reservation_date'];
        $resTime  = $_POST['reservation_time'];
        $docID    = $_POST['document_id'];

        // Get pages from selected document
        $docQuery  = "SELECT number_of_pages FROM tbldocument WHERE document_id = '$docID'";
        $docResult = mysqli_query($connection, $docQuery);
        $docRow    = mysqli_fetch_assoc($docResult);
        $totalPages = $docRow['number_of_pages'];

        // Check daily page limit — use student_id
        $today = date('Y-m-d');
        $usedQuery = "SELECT COALESCE(SUM(total_pages), 0) AS pages_used
                      FROM tblreservation
                      WHERE student_id = '$studentID'
                      AND reservation_date = '$today'
                      AND status != 'cancelled'";
        $usedResult = mysqli_query($connection, $usedQuery);
        $usedRow    = mysqli_fetch_assoc($usedResult);
        $pagesUsed  = $usedRow['pages_used'];

        if (($pagesUsed + $totalPages) > 20) {
            $message = "You have exceeded the 20-page daily limit. You have " . (20 - $pagesUsed) . " pages remaining today.";
            $messageType = 'error';
        } else {
            // Check for double booking
            $slotQuery = "SELECT reservation_id FROM tblreservation
                          WHERE reservation_date = '$resDate'
                          AND reservation_time = '$resTime'
                          AND status NOT IN ('cancelled')";
            $slotResult = mysqli_query($connection, $slotQuery);

            if (mysqli_num_rows($slotResult) > 0) {
                $message = "That time slot is already booked. Please choose a different time.";
                $messageType = 'error';
            } else {
                // Insert reservation — use student_id, no user_id column
                $insertQuery = "INSERT INTO tblreservation (reservation_date, reservation_time, status, total_pages, student_id, document_id)
                                VALUES ('$resDate', '$resTime', 'pending', '$totalPages', '$studentID', '$docID')";
                mysqli_query($connection, $insertQuery);

                $message = "Reservation submitted successfully! Status is pending confirmation.";
                $messageType = 'success';
            }
        }
    }

    // Get student's uploaded documents via user_id in tbldocument
    $docsQuery = "SELECT document_id, file_name, number_of_pages
                  FROM tbldocument
                  WHERE user_id = '$studentUserID'
                  ORDER BY upload_date DESC";
    $docsResult = mysqli_query($connection, $docsQuery);
?>

<div class="dashboard-wrapper">
    <div class="banner">
        <h2>MAKE A RESERVATION</h2>
    </div>

    <div class="content-section">
        <h1>New Reservation</h1>
        <p class="subtitle">Fill in the details below to reserve a printing slot.</p>

        <div class="action-links">
            <a href="dashboard.php">← Back to Dashboard</a>
            <a href="logout.php" class="link-logout">Logout</a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="reservation-form">
            <div class="form-group">
                <label>Select Document</label>
                <select name="document_id" id="document_id" onchange="updatePages(this)" required>
                    <option value="">-- Select a document --</option>
                    <?php while ($doc = mysqli_fetch_assoc($docsResult)): ?>
                    <option value="<?php echo $doc['document_id']; ?>"
                            data-pages="<?php echo $doc['number_of_pages']; ?>">
                        <?php echo $doc['file_name']; ?> (<?php echo $doc['number_of_pages']; ?> pages)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Total Pages</label>
                <input type="text" id="total_pages_display" placeholder="Auto-filled from document" disabled>
            </div>

            <div class="form-group">
                <label>Reservation Date</label>
                <input type="date" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Reservation Time</label>
                <input type="time" name="reservation_time" required>
            </div>

            <button type="submit" name="btnReserve" class="btn-reserve-submit">Submit Reservation</button>
        </form>
    </div>
</div>

<script>
    function updatePages(select) {
        const pages = select.options[select.selectedIndex].getAttribute('data-pages');
        document.getElementById('total_pages_display').value = pages ? pages + ' pages' : '';
    }
</script>

<?php include 'includes/footer.php'; ?>