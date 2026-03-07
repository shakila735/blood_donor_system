<?php
require_once __DIR__ . '/../../app/helpers/auth.php';
set_no_cache_headers();
require_role(['requester']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Requester Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="container mt-5">
    <h1>Requester Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    <p>You can post blood requests and search donors here.</p>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</body>
</html>