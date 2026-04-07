<?php
require_once 'config/database.php';
require_once 'includes/validation.php';
require_once 'auth/guard.php';

require_login();

// Allow only Requester and Hospital
$role = $_SESSION['role_name'] ?? '';
if (!in_array($role, ['requester', 'hospital'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id']) && isset($_POST['new_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['new_status'];
    
    // Ensure the status is valid
    $valid_statuses = ['Pending', 'Accepted', 'Completed', 'Cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        // Update only if this request belongs to the user
        $sql = "UPDATE blood_requests SET status = ? WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$new_status, $request_id, $user_id]);
            if ($stmt->rowCount() > 0) {
                $success = "Request status updated successfully.";
            } else {
                $error = "Failed to update status. Invalid request.";
            }
        } catch (PDOException $e) {
            $error = "Database error occurred.";
        }
    } else {
        $error = "Invalid status provided.";
    }
}

// Fetch all requests for this user with donor info
$sql = "SELECT br.*, u.name as donor_name, u.phone as donor_phone 
        FROM blood_requests br 
        LEFT JOIN users u ON br.donor_id = u.id 
        WHERE br.user_id = ? 
        ORDER BY br.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="text-danger">My Blood Requests</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body">
                <?php if (count($requests) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date Posted</th>
                                    <th>Blood Group</th>
                                    <th>Location</th>
                                    <th>Needed By</th>
                                    <th>Contact Info</th>
                                    <th>Status</th>
                                    <th>Donor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                        <td><strong><span class="text-danger"><?php echo htmlspecialchars($req['blood_group']); ?></span></strong></td>
                                        <td><?php echo htmlspecialchars($req['location']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($req['needed_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($req['contact_info']); ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'bg-secondary';
                                            if ($req['status'] == 'Pending') $badgeClass = 'bg-warning text-dark';
                                            if ($req['status'] == 'Accepted') $badgeClass = 'bg-info text-dark';
                                            if ($req['status'] == 'Completed') $badgeClass = 'bg-success';
                                            if ($req['status'] == 'Cancelled') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $req['status']; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($req['donor_id']): ?>
                                                <strong><?php echo htmlspecialchars($req['donor_name']); ?></strong><br>
                                                <span class="small text-muted"><?php echo htmlspecialchars($req['donor_phone']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">Not accepted yet</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($req['status'], ['Pending', 'Accepted'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $req['id']; ?>">
                                                    Update Status
                                                </button>

                                                <!-- Update Modal -->
                                                <div class="modal fade" id="updateModal<?php echo $req['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <form method="POST" action="">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Update Status</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                                                    <select name="new_status" class="form-select">
                                                                        <?php 
                                                                        $options = ['Pending', 'Accepted', 'Completed', 'Cancelled'];
                                                                        foreach ($options as $opt) {
                                                                            $selected = ($opt == $req['status']) ? 'selected' : '';
                                                                            echo "<option value=\"$opt\" $selected>$opt</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-danger btn-sm w-100">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">Locked</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 mb-0">You have not posted any blood requests yet. <a href="blood_request_create.php" class="text-danger">Post one now</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
