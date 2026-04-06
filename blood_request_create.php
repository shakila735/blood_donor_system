<?php
require_once 'config/database.php';
require_once 'includes/validation.php';
require_once 'auth/guard.php';
// Include the notification helper for Phase 2 integration
require_once 'includes/notification_helper.php';

require_login();

// Allow only Requester and Hospital
$role = $_SESSION['role_name'] ?? '';
if (!in_array($role, ['requester', 'hospital'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blood_group = $_POST['blood_group'] ?? '';
    // Basic exact match for location to align with simple notification logic
    $location = sanitize_input($_POST['location'] ?? '');
    $needed_date = sanitize_input($_POST['needed_date'] ?? '');
    $contact_info = sanitize_input($_POST['contact_info'] ?? '');
    $note = sanitize_input($_POST['note'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    if (empty($blood_group)) $errors[] = "Blood group is required.";
    if (empty($location)) $errors[] = "Location is required.";
    if (empty($needed_date)) $errors[] = "Needed date is required.";
    if (empty($contact_info)) $errors[] = "Contact information is required.";
    
    if (empty($errors)) {
        $sql = "INSERT INTO blood_requests (user_id, blood_group, location, needed_date, contact_info, note, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$user_id, $blood_group, $location, $needed_date, $contact_info, $note]);
            
            // Trigger the Notification specific to Phase 2
            $requester_name = $_SESSION['user_name'];
            $notified = notify_matching_donors($pdo, $blood_group, $location, $requester_name);
            
            $success = "Blood request posted successfully. It is now Pending. ";
            if($notified > 0) {
                $success .= "We also notified $notified matching donor(s) near you!";
            } else {
                $success .= "No perfectly matching donors found nearby right now, but your request is live.";
            }

            // Clear form
            $blood_group = $location = $needed_date = $contact_info = $note = "";
        } catch (PDOException $e) {
            $errors[] = "Failed to post blood request. Please try again.";
        }
    }
}

include 'includes/header.php';
$bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="text-danger">Post Blood Request</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <br><a href="blood_request_history.php" class="alert-link">View your requests history</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Blood Group *</label>
                        <select name="blood_group" class="form-select" required>
                            <option value="">Select Blood Group...</option>
                            <?php foreach ($bgs as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo (isset($blood_group) && $blood_group == $bg) ? 'selected' : ''; ?>>
                                    <?php echo $bg; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location (Use City/Zone exactly matching Donors) *</label>
                        <input type="text" name="location" class="form-control" required value="<?php echo htmlspecialchars($location ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Needed Date *</label>
                        <input type="date" name="needed_date" class="form-control" required value="<?php echo htmlspecialchars($needed_date ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Information * (Phone/Email)</label>
                        <input type="text" name="contact_info" class="form-control" required value="<?php echo htmlspecialchars($contact_info ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Additional Note (Optional)</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Condition, urgency, etc."><?php echo htmlspecialchars($note ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Post Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
