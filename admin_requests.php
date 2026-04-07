<?php
require_once 'auth/guard.php';
require_once 'config/database.php';
require_once 'includes/validation.php';

require_login();
require_role('admin');

$status_filter = sanitize_input($_GET['status'] ?? '');

$query = "SELECT br.*, u.name as requester_name, u.phone as requester_phone 
          FROM blood_requests br 
          JOIN users u ON br.user_id = u.id WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $query .= " AND br.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY br.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger">Monitor Requests</h2>
            <a href="dashboards/admin.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light">
                <form method="GET" action="admin_requests.php" class="row gx-2 gy-2">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Accepted" <?php echo $status_filter === 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                            <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Requester</th>
                                <th>Blood Group / Needed Date</th>
                                <th>Location</th>
                                <th>Contact Info</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No blood requests found.</td></tr>
                            <?php else: ?>
                                <?php foreach($requests as $r): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($r['requester_name']); ?></strong><br>
                                            <span class="text-muted small">ID: <?php echo $r['user_id']; ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger fs-6"><?php echo htmlspecialchars($r['blood_group']); ?></span><br>
                                            <span class="text-muted small"><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($r['needed_date']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($r['location']); ?></td>
                                        <td><?php echo htmlspecialchars($r['contact_info']); ?></td>
                                        <td>
                                            <?php 
                                            $bg = match($r['status']) {
                                                'Pending' => 'bg-warning text-dark',
                                                'Accepted' => 'bg-info text-dark',
                                                'Completed' => 'bg-success',
                                                'Cancelled' => 'bg-secondary',
                                                default => 'bg-dark'
                                            };
                                            ?>
                                            <span class="badge <?php echo $bg; ?>"><?php echo htmlspecialchars($r['status']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
