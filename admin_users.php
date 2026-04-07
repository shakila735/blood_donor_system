<?php
require_once 'auth/guard.php';
require_once 'config/database.php';
require_once 'includes/validation.php';

require_login();
require_role('admin');

// Processing search and filters
$search = sanitize_input($_GET['search'] ?? '');
$role_filter = sanitize_input($_GET['role'] ?? '');
$blood_group_filter = sanitize_input($_GET['blood_group'] ?? '');
$status_filter = sanitize_input($_GET['status'] ?? '');

$query = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (u.name LIKE ? OR u.phone LIKE ? OR u.email LIKE ? OR u.location LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($role_filter)) {
    $query .= " AND r.name = ?";
    $params[] = $role_filter;
}

if (!empty($blood_group_filter)) {
    $query .= " AND u.blood_group = ?";
    $params[] = $blood_group_filter;
}

if ($status_filter !== '') {
    if ($status_filter === 'active') {
        $query .= " AND u.is_active = 1";
    } elseif ($status_filter === 'blocked') {
        $query .= " AND u.is_active = 0";
    } elseif ($status_filter === 'verified') {
        $query .= " AND u.is_verified = 1";
    } elseif ($status_filter === 'unverified') {
        $query .= " AND r.name = 'donor' AND u.is_verified = 0";
    }
}

$query .= " ORDER BY u.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

$roles_stmt = $pdo->query("SELECT name FROM roles");
$roles = $roles_stmt->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger">Manage Users</h2>
            <a href="dashboards/admin.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light">
                <form method="GET" action="admin_users.php" class="row gx-2 gy-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search name, phone, email..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <?php foreach($roles as $r): ?>
                                <option value="<?php echo $r; ?>" <?php echo $role_filter === $r ? 'selected' : ''; ?>><?php echo ucfirst($r); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="blood_group" class="form-select">
                            <option value="">All Blood Groups</option>
                            <?php 
                            $bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                            foreach($bgs as $bg): ?>
                                <option value="<?php echo $bg; ?>" <?php echo $blood_group_filter === $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="blocked" <?php echo $status_filter === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
                            <option value="verified" <?php echo $status_filter === 'verified' ? 'selected' : ''; ?>>Verified Donors</option>
                            <option value="unverified" <?php echo $status_filter === 'unverified' ? 'selected' : ''; ?>>Unverified Donors</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                                <th>Name / Contact</th>
                                <th>Role</th>
                                <th>Blood Group</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No users found matching your criteria.</td></tr>
                            <?php else: ?>
                                <?php foreach($users as $u): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($u['name']); ?></strong><br>
                                            <span class="text-muted small"><?php echo htmlspecialchars($u['phone']); ?> | <?php echo htmlspecialchars($u['email'] ?? 'No Email'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo ucfirst($u['role_name']); ?></span>
                                        </td>
                                        <td>
                                            <?php if($u['blood_group']): ?>
                                                <span class="badge bg-danger"><?php echo htmlspecialchars($u['blood_group']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($u['is_active']): ?>
                                                <span class="badge bg-success rounded-pill mb-1">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark rounded-pill mb-1">Blocked</span>
                                            <?php endif; ?>

                                            <?php if($u['role_name'] === 'donor'): ?>
                                                <br>
                                                <?php if($u['is_verified']): ?>
                                                    <span class="badge bg-info text-dark rounded-pill"><i class="fa-solid fa-check-circle"></i> Verified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark rounded-pill">Unverified</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="admin_action.php" class="d-inline" onsubmit="return confirm('Are you sure you want to perform this action?');">
                                                <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                                <input type="hidden" name="redirect_to" value="admin_users.php">
                                                
                                                <?php if($u['role_name'] === 'donor'): ?>
                                                    <?php if($u['is_verified']): ?>
                                                        <button type="submit" name="action" value="unverify_user" class="btn btn-sm btn-outline-warning mb-1" title="Unverify Donor">Unverify</button>
                                                    <?php else: ?>
                                                        <button type="submit" name="action" value="verify_user" class="btn btn-sm btn-outline-info mb-1" title="Verify Donor">Verify</button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                                    <?php if($u['is_active']): ?>
                                                        <button type="submit" name="action" value="block_user" class="btn btn-sm btn-outline-dark mb-1">Block</button>
                                                    <?php else: ?>
                                                        <button type="submit" name="action" value="activate_user" class="btn btn-sm btn-outline-success mb-1">Activate</button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </form>
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
