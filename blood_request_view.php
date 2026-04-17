<?php
require_once 'config/database.php';
require_once 'auth/guard.php';

require_role('donor');

if (!isset($_GET['id'])) {
    header("Location: dashboards/donor.php");
    exit();
}

$request_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT br.*, u.name as requester_name, u.phone as requester_phone, u.location as requester_location 
                     FROM blood_requests br 
                     JOIN users u ON br.user_id = u.id 
                     WHERE br.id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch();

if (!$request) {
    header("Location: dashboards/donor.php?error=not_found");
    exit();
}

// Check if donor already accepted this or any other context needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Request Details | BloodNet</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        .detail-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .detail-header {
            background: linear-gradient(135deg, #dc3545 0%, #a52834 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .blood-group-badge {
            width: 80px;
            height: 80px;
            background: white;
            color: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            margin: 0 auto 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .info-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
        }
        .info-value {
            color: #1a1a1a;
            font-weight: 600;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <div class="container py-5 text-center">
        <a href="dashboards/donor.php?section=blood-requests" class="btn btn-outline-secondary mb-4 rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="detail-card card text-start">
                    <div class="detail-header">
                        <div class="blood-group-badge"><?php echo htmlspecialchars($request['blood_group']); ?></div>
                        <h2 class="mb-1">Blood Request Details</h2>
                        <p class="mb-0 opacity-75">Posted on <?php echo date('M d, Y', strtotime($request['created_at'])); ?></p>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="badge bg-<?php 
                                echo $request['status'] == 'Pending' ? 'warning' : 'success'; 
                            ?> fs-6 rounded-pill px-3">
                                <?php echo $request['status']; ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Requester Name</span>
                            <div class="info-value"><?php echo htmlspecialchars($request['requester_name']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Location</span>
                            <div class="info-value">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                <?php echo htmlspecialchars($request['location']); ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Date Needed</span>
                            <div class="info-value">
                                <i class="fas fa-calendar-alt text-danger me-1"></i>
                                <?php echo date('F j, Y', strtotime($request['needed_date'])); ?>
                            </div>
                        </div>
                        
                        <?php if ($request['note']): ?>
                        <div class="info-item">
                            <span class="info-label">Notes</span>
                            <div class="info-value text-muted fw-normal" style="font-style: italic;">
                                "<?php echo htmlspecialchars($request['note']); ?>"
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-4 pt-4 border-top">
                            <?php if ($request['status'] === 'Pending'): ?>
                                <button type="button" class="btn btn-danger btn-lg w-100 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#acceptModal">
                                    <i class="fas fa-check me-2"></i>Accept This Request
                                </button>
                            <?php else: ?>
                                <div class="alert alert-info text-center">
                                    This request has already been <?php echo strtolower($request['status']); ?>.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accept Modal -->
    <div class="modal fade" id="acceptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form method="POST" action="blood_request_accept.php">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Donation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <p>Are you sure you want to accept this request for <strong><?php echo htmlspecialchars($request['blood_group']); ?></strong> blood?</p>
                        <div class="alert alert-warning border-0 shadow-sm mt-3">
                            <i class="fas fa-info-circle me-2"></i> Your contact details will be shared with <strong><?php echo htmlspecialchars($request['requester_name']); ?></strong>.
                        </div>
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="redirect" value="dashboards/donor.php?section=blood-requests">
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Yes, I'll Donate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
