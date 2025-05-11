<?php
session_start();
require '../config/db.con.php';

// Initialize variables
$error = $success = '';
$serviceRequests = [];
$activeBookings = [];
$editRequest = null;

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];

try {
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['new_request'])) {
            // Create new request
            $bookingId = intval($_POST['booking_id']);
            $requestType = trim($_POST['request_type']);
            $description = trim($_POST['description']);

            // Validate booking ownership
            $stmt = $conn->prepare("SELECT BookingID FROM Bookings 
                                   WHERE BookingID = ? AND UserID = ?");
            $stmt->bind_param("ii", $bookingId, $userId);
            $stmt->execute();
            
            if (!$stmt->get_result()->num_rows) {
                throw new Exception("Invalid booking selection");
            }

            $stmt = $conn->prepare("INSERT INTO ServiceRequests 
                (BookingID, UserID, RequestType, Description, Status)
                VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("iiss", $bookingId, $userId, $requestType, $description);
            $stmt->execute();
            $success = "Service request submitted successfully!";
        }
        elseif (isset($_POST['edit_request'])) {
            // Update existing request
            $requestId = intval($_POST['request_id']);
            $newType = trim($_POST['request_type']);
            $newDesc = trim($_POST['description']);

            // Validate request ownership and status
            $stmt = $conn->prepare("SELECT RequestID FROM ServiceRequests 
                                   WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
            $stmt->bind_param("ii", $requestId, $userId);
            $stmt->execute();
            
            if (!$stmt->get_result()->num_rows) {
                throw new Exception("Cannot edit this request");
            }

            $stmt = $conn->prepare("UPDATE ServiceRequests 
                                   SET RequestType = ?, Description = ?
                                   WHERE RequestID = ?");
            $stmt->bind_param("ssi", $newType, $newDesc, $requestId);
            $stmt->execute();
            $success = "Service request updated successfully!";
        }
        elseif (isset($_POST['cancel_request'])) {
            // Cancel request
            $requestId = intval($_POST['request_id']);
            
            $stmt = $conn->prepare("UPDATE ServiceRequests SET Status = 'Cancelled'
                                   WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
            $stmt->bind_param("ii", $requestId, $userId);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success = "Service request cancelled successfully!";
            } else {
                throw new Exception("Cannot cancel this request");
            }
        }
    }

    // Check for edit parameter
    if (isset($_GET['edit'])) {
        $requestId = intval($_GET['edit']);
        $stmt = $conn->prepare("SELECT * FROM ServiceRequests 
                               WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
        $stmt->bind_param("ii", $requestId, $userId);
        $stmt->execute();
        $editRequest = $stmt->get_result()->fetch_assoc();
    }

    // Get active bookings
    $stmt = $conn->prepare("SELECT b.BookingID, r.RoomNumber, 
                          DATE_FORMAT(b.CheckInDate, '%b %e, %Y') AS CheckIn,
                          DATE_FORMAT(b.CheckOutDate, '%b %e, %Y') AS CheckOut
                          FROM Bookings b
                          JOIN Rooms r ON b.RoomID = r.RoomID
                          WHERE b.UserID = ? AND b.CheckOutDate >= CURDATE()");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $activeBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get service requests
    $stmt = $conn->prepare("SELECT sr.RequestID, sr.RequestType, sr.Description, 
                          sr.Status, sr.CreatedAt, r.RoomNumber,
                          DATE_FORMAT(sr.CreatedAt, '%b %e, %Y %l:%i %p') AS RequestDate
                          FROM ServiceRequests sr
                          JOIN Bookings b ON sr.BookingID = b.BookingID
                          JOIN Rooms r ON b.RoomID = r.RoomID
                          WHERE sr.UserID = ?
                          ORDER BY sr.CreatedAt DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $serviceRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Hotel System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        .service-card {
            background: var(--secondary);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-assigned { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .request-type {
            color: var(--accent);
            font-weight: 600;
        }

        .edit-form {
            background: var(--light);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-4"><i class="fas fa-concierge-bell"></i> Manage Services</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- New/Edit Request Section -->
        <div class="card mb-4 border-accent">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas <?= $editRequest ? 'fa-edit' : 'fa-plus-circle' ?>"></i> 
                    <?= $editRequest ? 'Edit Service Request' : 'New Service Request' ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($activeBookings) && !$editRequest): ?>
                    <div class="alert alert-info">No active bookings available for service requests</div>
                <?php else: ?>
                    <form method="POST">
                        <?php if ($editRequest): ?>
                            <input type="hidden" name="request_id" value="<?= $editRequest['RequestID'] ?>">
                        <?php else: ?>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select name="booking_id" class="form-select" required>
                                        <option value="">Select Booking...</option>
                                        <?php foreach ($activeBookings as $booking): ?>
                                            <option value="<?= $booking['BookingID'] ?>">
                                                Room <?= htmlspecialchars($booking['RoomNumber']) ?> 
                                                (<?= $booking['CheckIn'] ?> - <?= $booking['CheckOut'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-<?= $editRequest ? '6' : '3' ?>">
                                <select name="request_type" class="form-select" required>
                                    <option value="">Request Type...</option>
                                    <?php foreach (['Room Service', 'Housekeeping', 'Maintenance', 'Transportation', 'Other'] as $type): ?>
                                        <option value="<?= $type ?>" 
                                            <?= ($editRequest && $editRequest['RequestType'] === $type) ? 'selected' : '' ?>>
                                            <?= $type ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-<?= $editRequest ? '6' : '4' ?>">
                                <input type="text" name="description" class="form-control" 
                                       value="<?= $editRequest ? htmlspecialchars($editRequest['Description']) : '' ?>"
                                       placeholder="Request description..." required>
                            </div>
                            
                            <div class="col-md-<?= $editRequest ? '12' : '1' ?>">
                                <button type="submit" name="<?= $editRequest ? 'edit_request' : 'new_request' ?>" 
                                        class="btn btn-dark w-80">
                                    <i class="fas fa-paper-plane"></i> <?= $editRequest ? 'Update' : 'Submit' ?>
                                </button>
                                <?php if ($editRequest): ?>
                                    <a href="manage_service.php" class="btn btn-secondary w-100 mt-2">
                                        <i class="fas fa-times"></i> Cancel Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Existing Requests -->
        <h4 class="mb-3"><i class="fas fa-history"></i> Service History</h4>
        
        <?php if (empty($serviceRequests)): ?>
            <div class="alert alert-info">No service requests found</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($serviceRequests as $request): ?>
                    <div class="col-md-6">
                        <div class="service-card p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="request-type"><?= htmlspecialchars($request['RequestType']) ?></span>
                                    <span class="status-badge status-<?= strtolower($request['Status']) ?>">
                                        <?= htmlspecialchars($request['Status']) ?>
                                    </span>
                                </div>
                                <small class="text-muted"><?= $request['RequestDate'] ?></small>
                            </div>

                            <p class="mb-2"><?= htmlspecialchars($request['Description']) ?></p>
                            
                            <div class="row text-muted small">
                                <div class="col-6">
                                    <i class="fas fa-door-open"></i> Room <?= htmlspecialchars($request['RoomNumber']) ?>
                                </div>
                                <div class="col-6 text-end">
                                    ID: #<?= $request['RequestID'] ?>
                                </div>
                            </div>

                            <?php if ($request['Status'] === 'Pending'): ?>
                                <div class="mt-2 d-flex gap-2">
                                    <a href="edit_services.php?edit=<?= $request['RequestID'] ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST">
                                        <input type="hidden" name="request_id" value="<?= $request['RequestID'] ?>">
                                        <button type="submit" name="cancel_request" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Cancel this request?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>