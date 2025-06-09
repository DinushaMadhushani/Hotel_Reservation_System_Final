<?php
session_start();
require '../config/db.con.php';

// Initialize variables
$error = $success = '';
$serviceRequests = [];
$activeBookings = [];
$editRequest = null;
$userId = $_SESSION['UserID'];

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle form submissions
try {
    // Handle edit parameter
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $requestId = intval($_GET['edit']);
        
        // Validate and get request details
        $stmt = $conn->prepare("SELECT * FROM ServiceRequests 
                               WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
        $stmt->bind_param("ii", $requestId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $editRequest = $result->fetch_assoc();
        } else {
            $error = "Invalid request or request cannot be edited";
        }
    }
    
    // Handle edit service request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_request'])) {
        $requestId = intval($_POST['request_id']);
        $requestType = trim($_POST['request_type']);
        $description = trim($_POST['description']);
        
        // Validate input
        if (empty($requestType) || empty($description)) {
            $error = "All fields are required.";
        } else {
            // Update service request
            $stmt = $conn->prepare("UPDATE ServiceRequests 
                                   SET RequestType = ?, Description = ?
                                   WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
            $stmt->bind_param("ssii", $requestType, $description, $requestId, $userId);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success = "Service request updated successfully!";
                // Clear edit mode after successful update
                $editRequest = null;
                // Redirect to remove the edit parameter from URL
                header("Location: manage_services.php");
                exit();
            } else {
                $error = "No changes made or request cannot be updated";
            }
        }
    }
    
    // Delete request
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $requestId = intval($_GET['delete']);
        
        // Check if request belongs to user and is in Pending status
        $stmt = $conn->prepare("SELECT Status FROM ServiceRequests WHERE RequestID = ? AND UserID = ?");
        $stmt->bind_param("ii", $requestId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $requestStatus = $result->fetch_assoc()['Status'];
            
            if ($requestStatus === 'Pending') {
                // Delete the request
                $deleteStmt = $conn->prepare("DELETE FROM ServiceRequests WHERE RequestID = ? AND UserID = ?");
                $deleteStmt->bind_param("ii", $requestId, $userId);
                
                if ($deleteStmt->execute() && $deleteStmt->affected_rows > 0) {
                    $success = "Service request deleted successfully!";
                } else {
                    $error = "Failed to delete the service request.";
                }
            } else {
                $error = "Only pending requests can be deleted.";
            }
        } else {
            $error = "Invalid request or you don't have permission to delete it.";
        }
    }
    
    // Add new request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_request'])) {
        $bookingId = intval($_POST['booking_id']);
        $requestType = trim($_POST['request_type']);
        $description = trim($_POST['description']);
        
        // Validate booking ownership
        $bookingCheck = $conn->prepare("SELECT BookingID FROM Bookings WHERE BookingID = ? AND UserID = ?");
        $bookingCheck->bind_param("ii", $bookingId, $userId);
        $bookingCheck->execute();
        
        if ($bookingCheck->get_result()->num_rows > 0) {
            // Insert new request
            $insertStmt = $conn->prepare("INSERT INTO ServiceRequests (BookingID, UserID, RequestType, Description, Status) VALUES (?, ?, ?, ?, 'Pending')");
            $insertStmt->bind_param("iiss", $bookingId, $userId, $requestType, $description);
            
            if ($insertStmt->execute()) {
                $success = "Service request submitted successfully!";
            } else {
                $error = "Failed to submit service request: " . $conn->error;
            }
        } else {
            $error = "Invalid booking selected.";
        }
    }
    
    // Fetch active bookings for new requests
    $bookingStmt = $conn->prepare("SELECT b.BookingID, r.RoomNumber, b.CheckInDate, b.CheckOutDate 
                                FROM Bookings b
                                JOIN Rooms r ON b.RoomID = r.RoomID
                                WHERE b.UserID = ? AND b.CheckOutDate >= CURDATE()
                                AND b.BookingStatus = 'Confirmed'
                                ORDER BY b.CheckInDate DESC");
    $bookingStmt->bind_param("i", $userId);
    $bookingStmt->execute();
    $activeBookings = $bookingStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Fetch user's service requests with booking and room details
    $requestStmt = $conn->prepare("SELECT sr.*, r.RoomNumber, b.CheckInDate, b.CheckOutDate 
                                 FROM ServiceRequests sr
                                 JOIN Bookings b ON sr.BookingID = b.BookingID
                                 JOIN Rooms r ON b.RoomID = r.RoomID
                                 WHERE sr.UserID = ?
                                 ORDER BY sr.CreatedAt DESC");
    $requestStmt->bind_param("i", $userId);
    $requestStmt->execute();
    $serviceRequests = $requestStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $error = "An error occurred: " . $e->getMessage();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Hotel System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a1a1a',
                        secondary: '#ffffff',
                        accent: '#d4af37',
                        light: '#f5f5f5',
                        dark: '#121212',
                        'primary-light': '#333333',
                        'primary-dark': '#0a0a0a',
                        'accent-light': '#f5e8c9',
                        'accent-dark': '#b39020',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        script: ['Dancing Script', 'cursive'],
                        serif: ['Playfair Display', 'serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-light min-h-screen">
    <?php include '../includes/user_header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-10" data-aos="fade-down">
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-primary mb-2">Service Requests</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Manage your service requests and enhance your stay with our premium services.</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-md alert-message" role="alert" data-aos="fade-in">
                <div class="flex items-center">
                    <div class="py-1"><i class="fas fa-exclamation-circle mr-2"></i></div>
                    <div>
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-md alert-message" role="alert" data-aos="fade-in">
                <div class="flex items-center">
                    <div class="py-1"><i class="fas fa-check-circle mr-2"></i></div>
                    <div>
                        <p><?= htmlspecialchars($success) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Service Request Form -->
            <div class="lg:col-span-1" data-aos="fade-right">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-primary to-primary-light p-4 text-white">
                        <h2 class="text-xl font-bold flex items-center">
                            <?php if (isset($editRequest)): ?>
                                <i class="fas fa-edit mr-2"></i> Edit Service Request
                            <?php else: ?>
                                <i class="fas fa-concierge-bell mr-2"></i> New Service Request
                            <?php endif; ?>
                        </h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($activeBookings) && !isset($editRequest)): ?>
                            <div class="bg-blue-50 text-blue-700 p-4 rounded-md mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-700"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm">You need an active booking to request services. Please make a booking first.</p>
                                    </div>
                                </div>
                            </div>
                            <a href="./new_booking.php" class="inline-block w-full text-center px-6 py-3 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent-dark transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                <i class="fas fa-calendar-plus mr-2"></i> Make a Booking
                            </a>
                        <?php else: ?>
                            <form method="POST" class="space-y-4">
                                <?php if (isset($editRequest)): ?>
                                    <input type="hidden" name="request_id" value="<?= $editRequest['RequestID'] ?>">
                                <?php else: ?>
                                    <div>
                                        <label for="booking_id" class="block text-sm font-medium text-gray-700 mb-1">Select Booking</label>
                                        <select id="booking_id" name="booking_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent transition-colors duration-200" required>
                                            <option value="">Choose a booking...</option>
                                            <?php foreach ($activeBookings as $booking): ?>
                                                <option value="<?= $booking['BookingID'] ?>">
                                                    Room <?= htmlspecialchars($booking['RoomNumber']) ?> (<?= date('M d, Y', strtotime($booking['CheckInDate'])) ?> - <?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                
                                <div>
                                    <label for="request_type" class="block text-sm font-medium text-gray-700 mb-1">Request Type</label>
                                    <select id="request_type" name="request_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent transition-colors duration-200" required>
                                        <option value="">Select service type...</option>
                                        <?php 
                                        $types = ['Room Service', 'Housekeeping', 'Maintenance', 'Transportation', 'Concierge', 'Other'];
                                        foreach ($types as $type): 
                                        ?>
                                            <option value="<?= $type ?>" <?= (isset($editRequest) && $editRequest['RequestType'] === $type) ? 'selected' : '' ?>>
                                                <?= $type ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-accent focus:border-accent transition-colors duration-200" placeholder="Please describe your request..." required><?= isset($editRequest) ? htmlspecialchars($editRequest['Description']) : '' ?></textarea>
                                </div>
                                
                                <div class="flex space-x-3">
                                    <?php if (isset($editRequest)): ?>
                                        <button type="submit" name="edit_request" class="flex-1 px-6 py-3 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent-dark transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                            <i class="fas fa-save mr-2"></i> Update Request
                                        </button>
                                        <a href="manage_services.php" class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg shadow-md hover:bg-gray-300 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 text-center">
                                            <i class="fas fa-times mr-2"></i> Cancel
                                        </a>
                                    <?php else: ?>
                                        <button type="submit" name="new_request" class="w-full px-6 py-3 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent-dark transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                                            <i class="fas fa-paper-plane mr-2"></i> Submit Request
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Service Requests List -->
            <div class="lg:col-span-2" data-aos="fade-left">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-primary to-primary-light p-4 text-white">
                        <h2 class="text-xl font-bold flex items-center">
                            <i class="fas fa-list-alt mr-2"></i> Your Service Requests
                        </h2>
                    </div>
                    
                    <?php if (empty($serviceRequests)): ?>
                        <div class="p-8 text-center">
                            <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-primary">No Service Requests</h3>
                            <p class="text-gray-600 mb-6">You haven't made any service requests yet.</p>
                            <?php if (!empty($activeBookings)): ?>
                                <p class="text-sm text-gray-500">Use the form to create your first request.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50 text-gray-600 text-sm leading-normal">
                                    <tr>
                                        <th class="py-3 px-4 text-left font-semibold">Room</th>
                                        <th class="py-3 px-4 text-left font-semibold">Type</th>
                                        <th class="py-3 px-4 text-left font-semibold hidden md:table-cell">Description</th>
                                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                                        <th class="py-3 px-4 text-left font-semibold hidden sm:table-cell">Date</th>
                                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm">
                                    <?php foreach ($serviceRequests as $index => $request): ?>
                                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                                            <td class="py-3 px-4">
                                                <span class="font-medium">Room <?= htmlspecialchars($request['RoomNumber']) ?></span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <?= htmlspecialchars($request['RequestType']) ?>
                                            </td>
                                            <td class="py-3 px-4 hidden md:table-cell">
                                                <div class="truncate max-w-xs"><?= htmlspecialchars($request['Description']) ?></div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <?php 
                                                $statusClass = '';
                                                switch ($request['Status']) {
                                                    case 'Pending':
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    case 'Assigned':
                                                        $statusClass = 'bg-blue-100 text-blue-800';
                                                        break;
                                                    case 'Completed':
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        break;
                                                }
                                                ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusClass ?>">
                                                    <?= htmlspecialchars($request['Status']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 hidden sm:table-cell">
                                                <?= date('M d, Y', strtotime($request['CreatedAt'])) ?>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <?php if ($request['Status'] === 'Pending'): ?>
                                                        <a href="manage_services.php?edit=<?= $request['RequestID'] ?>" class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" onclick="confirmDelete(<?= $request['RequestID'] ?>)" class="text-red-600 hover:text-red-800 transition-colors duration-200" title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 cursor-not-allowed" title="Cannot modify">
                                                            <i class="fas fa-edit"></i>
                                                        </span>
                                                        <span class="text-gray-400 cursor-not-allowed" title="Cannot delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                    <button type="button" onclick="showDetails(<?= $index ?>)" class="text-gray-600 hover:text-gray-800 transition-colors duration-200" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Request Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="bg-gradient-to-r from-primary to-primary-light p-4 text-white rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold" id="modalTitle">Service Request Details</h3>
                    <button type="button" onclick="closeModal()" class="text-white hover:text-accent transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="modalBody">
                <!-- Content will be dynamically inserted here -->
            </div>
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg">
                <button type="button" onclick="closeModal()" class="w-full px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-light transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
            <div class="bg-gradient-to-r from-red-600 to-red-700 p-4 text-white rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold">Confirm Deletion</h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-white hover:text-gray-200 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <p class="mb-4">Are you sure you want to delete this service request? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <a href="#" id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                        Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/sub_footer.php'; ?>

    <script>
        // Store service requests data for modal display
        const serviceRequests = <?= json_encode($serviceRequests) ?>;
        
        // Show service request details in modal
        function showDetails(index) {
            const request = serviceRequests[index];
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            
            modalTitle.textContent = request.RequestType + ' Request';
            
            let statusClass = '';
            switch (request.Status) {
                case 'Pending':
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'Assigned':
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
                case 'Completed':
                    statusClass = 'bg-green-100 text-green-800';
                    break;
            }
            
            modalBody.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Room Number</h4>
                        <p class="font-semibold text-primary">Room ${request.RoomNumber}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Description</h4>
                        <p class="text-gray-700">${request.Description}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                                ${request.Status}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date Requested</h4>
                            <p class="text-gray-700">${new Date(request.CreatedAt).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Show modal with animation
            const modal = document.getElementById('detailsModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        // Close details modal
        function closeModal() {
            const modal = document.getElementById('detailsModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        
        // Show delete confirmation modal
        function confirmDelete(requestId) {
            const deleteModal = document.getElementById('deleteModal');
            const deleteModalContent = document.getElementById('deleteModalContent');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            
            confirmDeleteBtn.href = `manage_services.php?delete=${requestId}`;
            
            deleteModal.classList.remove('hidden');
            setTimeout(() => {
                deleteModalContent.classList.remove('scale-95', 'opacity-0');
                deleteModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        // Close delete confirmation modal
        function closeDeleteModal() {
            const deleteModal = document.getElementById('deleteModal');
            const deleteModalContent = document.getElementById('deleteModalContent');
            
            deleteModalContent.classList.remove('scale-100', 'opacity-100');
            deleteModalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                deleteModal.classList.add('hidden');
            }, 300);
        }
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const detailsModal = document.getElementById('detailsModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === detailsModal) {
                closeModal();
            }
            
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });
        
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true,
            offset: 50
        });
    </script>
</body>
</html>