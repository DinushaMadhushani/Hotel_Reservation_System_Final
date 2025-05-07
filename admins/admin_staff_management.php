<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Staff CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete_staff':
                $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ? AND UserType = 'Staff'");
                $stmt->bind_param("i", $_POST['user_id']);
                $stmt->execute();
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Hotel Admin</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --side-bar: rgb(197, 164, 54);
        }

        .staff-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .status-active {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-inactive {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .action-btn.edit {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
        }

        .action-btn.delete {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand brand-logo" href="#">
                <i class="fa-solid fa-hotel me-2"></i>Hotel Admin
            </a>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'dashboard' ? 'active' : '' ?>" 
                           href="?page=dashboard">
                            <i class="fa-solid fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="?page=staff">
                            <i class="fa-solid fa-user-tie me-2"></i>Staff
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown user-dropdown">
                        <a class="dropdown-toggle d-flex align-items-center" href="#" 
                           role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user-shield me-2"></i>
                            <span>Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">
                                <i class="fa-solid fa-user-cog me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid p-4">
        <div class="card border-0 shadow-lg" data-aos="fade-up">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0 text-dark"><i class="fa-solid fa-user-tie me-2 text-accent"></i>Staff Management</h3>
                <a href="add_staff.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>Add New Staff
                </a>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table id="staffTable" class="table table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Staff Member</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $staffMembers = $conn->query("
                                SELECT UserID, FullName, Email, PhoneNumber, CreatedAt, LastLogin 
                                FROM Users 
                                WHERE UserType = 'Staff'
                            ")->fetch_all(MYSQLI_ASSOC);

                            foreach($staffMembers as $staff): 
                                $initials = substr($staff['FullName'], 0, 2);
                            ?>
                            <tr>
                                <td><?= $staff['UserID'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="staff-avatar me-3">
                                            <?= strtoupper($initials) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= $staff['FullName'] ?></div>
                                            <small class="text-muted"><?= $staff['Email'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-nowrap">
                                        <div><?= $staff['PhoneNumber'] ?: '-' ?></div>
                                        <small class="text-muted">Last active: <?= date('M d, Y', strtotime($staff['LastLogin'])) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-active">
                                        <i class="fa-solid fa-circle-small me-1"></i> Active
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit_staff.php?user_id=<?= $staff['UserID'] ?>" 
                                           class="action-btn edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button class="action-btn delete" 
                                                onclick="deleteStaff(<?= $staff['UserID'] ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 800,
                once: true
            });

            $('#staffTable').DataTable({
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [4] },
                    { className: "align-middle", targets: "_all" }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search staff members...",
                }
            });
        });

        function deleteStaff(userId) {
            if(confirm('Are you sure you want to delete this staff member?\nThis action cannot be undone!')) {
                $.post('manage_staff.php', { 
                    action: 'delete_staff', 
                    user_id: userId 
                }, function() {
                    location.reload();
                }).fail(function() {
                    alert('Error deleting staff member. Please try again.');
                });
            }
        }
    </script>
</body>
</html>