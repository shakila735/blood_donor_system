<?php
require_once 'config/database.php';
require_once 'auth/guard.php';

require_role('donor');

$user_id = $_SESSION['user_id'];

// Get donor's blood group for matching
$stmt = $pdo->prepare("SELECT blood_group FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$donor_bg = $stmt->fetchColumn();

// Fetch all Pending blood requests
// We order them by matching blood group first, then by date
$sql = "SELECT br.*, u.name as requester_name 
        FROM blood_requests br 
        JOIN users u ON br.user_id = u.id 
        WHERE br.status = 'Pending' 
        ORDER BY (br.blood_group = ?) DESC, br.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$donor_bg]);
$requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="text-danger">Blood Request Feed</h2>
        <a href="dashboards/donor.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm">
            <i class="fa-solid fa-circle-info"></i> Below are the active blood requests. Requests matching your blood group (<strong><?php echo $donor_bg; ?></strong>) are shown first.
        </div>

        <?php if (count($requests) > 0): ?>
            <div class="row">
                <?php foreach ($requests as $req): ?>
                    <?php 
                    $is_match = ($req['blood_group'] === $donor_bg);
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 <?php echo $is_match ? 'border-start border-danger border-4' : ''; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h4 class="text-danger mb-0"><?php echo htmlspecialchars($req['blood_group']); ?></h4>
                                    <?php if ($is_match): ?>
                                        <span class="badge bg-danger">Perfect Match</span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($req['location']); ?></p>
                                <p class="mb-2"><strong>Needed By:</strong> <?php echo date('M d, Y', strtotime($req['needed_date'])); ?></p>
                                <p class="mb-2 text-muted small"><strong>Posted By:</strong> <?php echo htmlspecialchars($req['requester_name']); ?></p>
                                
                                <?php if ($req['note']): ?>
                                    <p class="card-text small text-muted mt-2">
                                        <i class="fa-solid fa-quote-left"></i> 
                                        <?php echo htmlspecialchars($req['note']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white border-0 pb-3">
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#acceptModal<?php echo $req['id']; ?>">
                                    Accept Request
                                </button>
                            </div>
                        </div>

                        <!-- Accept Modal -->
                        <div class="modal fade" id="acceptModal<?php echo $req['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="blood_request_accept.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Acceptance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to accept this request for <strong><?php echo htmlspecialchars($req['blood_group']); ?></strong> blood?</p>
                                            <div class="alert alert-warning small">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Once accepted, your name and phone number will be shared with <strong><?php echo htmlspecialchars($req['requester_name']); ?></strong> so they can contact you.
                                            </div>
                                            <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, I'll Donate</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm py-5">
                <div class="card-body text-center">
                    <i class="fa-solid fa-heart-pulse fa-4x text-light mb-3"></i>
                    <h5 class="text-muted">No active blood requests found.</h5>
                    <p class="text-muted small">Check back later or update your profile to ensure you receive notifications.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
