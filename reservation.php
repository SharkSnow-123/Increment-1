<?php
session_start();
include 'connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Student') {
    header('Location: dashboard.php');
    exit();
}

// Get student_id from tblstudent using session user_id
$user_id = $_SESSION['user_id'];
$studentQuery = "SELECT student_id FROM tblstudent WHERE user_id = $user_id";
$studentResult = mysqli_query($connection, $studentQuery);
$studentRow = mysqli_fetch_assoc($studentResult);
$student_id = $studentRow['student_id'];

// Handle reservation form submission
if (isset($_POST['btnReserve'])) {
    $date = $_POST['reservation_date'];
    $time = $_POST['reservation_time'];
    $pages = $_POST['total_pages'];

    // Check if timeslot is already taken (Business Rule 5)
    $checkSlot = "SELECT * FROM tblreservation 
                  WHERE reservation_date = '$date' AND reservation_time = '$time'";
    $slotResult = mysqli_query($connection, $checkSlot);

    if (mysqli_num_rows($slotResult) > 0) {
        $error = "That time slot is already taken. Please choose another.";
    } else {
        // Check daily page limit (Business Rule 7: max 20 pages per day)
        $checkPages = "SELECT SUM(total_pages) as daily_total FROM tblreservation 
                       WHERE student_id = $student_id 
                       AND reservation_date = '$date'
                       AND status != 'cancelled'";
        $pagesResult = mysqli_query($connection, $checkPages);
        $pagesRow = mysqli_fetch_assoc($pagesResult);
        $dailyTotal = $pagesRow['daily_total'] ?? 0;

        if (($dailyTotal + $pages) > 20) {
            $remaining = 20 - $dailyTotal;
            $error = "You can only print $remaining more pages today (20 page daily limit).";
        } else {
            $sql = "INSERT INTO tblreservation 
                        (reservation_date, reservation_time, status, total_pages, student_id, document_id) 
                    VALUES ('$date', '$time', 'pending', '$pages', '$student_id', 1)";

            if (mysqli_query($connection, $sql)) {
                $success = "Reservation submitted successfully!";
            } else {
                $error = "Something went wrong: " . mysqli_error($connection);
            }
        }
    }
}

// Fetch this student's reservations
$myReservations = mysqli_query($connection, 
    "SELECT * FROM tblreservation 
     WHERE student_id = $student_id 
     ORDER BY reservation_date DESC, reservation_time DESC"
);
?>

<div class="dashboard-wrapper">
    <div class="banner">
        <h2>MY RESERVATIONS</h2>
    </div>

    <div class="content-section">
        <h1>Book a Printing Slot</h1>
        <p class="subtitle">Reserve your time slot for printing.</p>

        <div class="action-links">
            <a href="dashboard.php" class="link-add">← Back to Dashboard</a>
            <a href="logout.php" class="link-logout">Logout</a>
        </div>

        <?php if (isset($success)): ?>
            <p style="color: green; font-weight: bold;"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Reservation Form -->
        <div class="update-container">
            <h3>New Reservation</h3>
            <form method="POST">
                <label for="reservation_date">Date:</label>
                <input type="date" name="reservation_date" id="reservation_date" 
                       min="<?php echo date('Y-m-d'); ?>" required>

                <label for="reservation_time">Time:</label>
                <select name="reservation_time" id="reservation_time" required>
                    <option value="">-- Select Time --</option>
                    <option value="08:00:00">8:00 AM</option>
                    <option value="08:30:00">8:30 AM</option>
                    <option value="09:00:00">9:00 AM</option>
                    <option value="09:30:00">9:30 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="10:30:00">10:30 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="11:30:00">11:30 AM</option>
                    <option value="13:00:00">1:00 PM</option>
                    <option value="13:30:00">1:30 PM</option>
                    <option value="14:00:00">2:00 PM</option>
                    <option value="14:30:00">2:30 PM</option>
                    <option value="15:00:00">3:00 PM</option>
                    <option value="15:30:00">3:30 PM</option>
                    <option value="16:00:00">4:00 PM</option>
                </select>

                <label for="total_pages">Number of Pages (max 20/day):</label>
                <input type="number" name="total_pages" id="total_pages" 
                       min="1" max="20" required>

                <input type="submit" name="btnReserve" value="Submit Reservation" class="btnLogin">
            </form>
        </div>

        <!-- My Reservations Table -->
        <h3 style="margin-top: 30px;">My Reservation History</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Pages</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($myReservations)): ?>
                <tr>
                    <td><?php echo $row['reservation_id']; ?></td>
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
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>