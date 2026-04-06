<?php
require_once 'auth/guard.php';
require_once 'config/database.php';
require_login();

// Handle Mark All as Read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    header("Location: notifications.php?success=1");
    exit();
}

// Fetch user's notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger"><i class="fa-solid fa-bell"></i> Notifications</h2>
            <?php if (count($notifications) > 0): ?>
                <form method="POST" class="m-0">
                    <button type="submit" name="mark_read" class="btn btn-outline-danger btn-sm">Mark All as Read</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                All unread notifications marked as read.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $notification): ?>
                            <div class="list-group-item p-3 <?php echo $notification['is_read'] ? 'bg-light' : ''; ?>">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <p class="mb-1 <?php echo $notification['is_read'] ? 'text-muted' : 'fw-bold'; ?>">
                                        <?php if (!$notification['is_read']): ?>
                                            <span class="badge bg-danger rounded-circle p-1 me-2" style="width: 10px; height: 10px;"><span class="visually-hidden">Unread</span></span>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                    </p>
                                    <small class="text-muted" style="white-space: nowrap;"><?php echo date('M d, g:i A', strtotime($notification['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fa-regular fa-bell-slash fa-3x mb-3 text-light-subtle"></i>
                            <p>You have no notifications yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
