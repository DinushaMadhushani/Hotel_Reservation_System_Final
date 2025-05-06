<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Staff Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_staff') {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
   
    // Verify user is actually staff before deletion
    $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ? AND UserType = 'Staff'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
   
    $_SESSION['success'] = "Staff member deleted successfully!";
    header("Location: manage_staff.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <!-- Include all CSS/JS from your admin panel -->
</head>
<body>
    <!-- Add to navigation (update your existing nav) -->
    <!-- <li class="nav-item">
        <a class="nav-link <?= ($_GET['page'] ?? '') === 'staff' ? 'active' : '' ?>"
           href="?page=staff">
            <i class="fa-solid fa-users-gear me-2"></i>Staff
        </a>
    </li> -->

    <div class="container-fluid p-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">Staff Management</h5>
                    <a href="add_user.php?type=staff" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus me-2"></i>Add New Staff
                    </a>
                </div>
               
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
               
                <table id="staffTable" class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Schedule</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $staff = $conn->query("
                            SELECT u.*,
                                   GROUP_CONCAT(DISTINCT s.ScheduleDate ORDER BY s.ScheduleDate DESC SEPARATOR ', ') AS schedule
                            FROM Users u
                            LEFT JOIN StaffSchedule s ON u.UserID = s.UserID
                            WHERE u.UserType = 'Staff'
                            GROUP BY u.UserID
                        ")->fetch_all(MYSQLI_ASSOC);
                       
                        foreach($staff as $member):
                        ?>
                        <tr>
                            <td><?= $member['UserID'] ?></td>
                            <td><?= htmlspecialchars($member['FullName']) ?></td>
                            <td><?= htmlspecialchars($member['Email']) ?></td>
                            <td><?= $member['PhoneNumber'] ?: '-' ?></td>
                            <td>
                                <?php if($member['schedule']): ?>
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-calendar-days me-2"></i>
                                        <?= $member['schedule'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">No schedule</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_user.php?user_id=<?= $member['UserID'] ?>"
                                   class="btn btn-sm btn-primary me-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button class="btn btn-sm btn-danger"
                                        onclick="deleteStaff(<?= $member['UserID'] ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <a href="staff_schedule.php?user_id=<?= $member['UserID'] ?>"
                                   class="btn btn-sm btn-secondary ms-2">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#staffTable').DataTable({
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [5] }
                ]
            });
        });

        function deleteStaff(userId) {
            if(confirm('Are you sure you want to delete this staff member?')) {
                $.post('', {
                    action: 'delete_staff',
                    user_id: userId
                }, function() {
                    location.reload();
                });
            }
        }
    </script>
</body>
</html>