<?php
require_once __DIR__ . '/../../app/helpers/auth.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

require_role(['requester']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requester Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="container mt-5">
    <div class="card shadow p-4">
        <h1 class="mb-3">Requester Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</p>
        <p>You can post blood requests and search donors here.</p>
        <a href="create_blood_request.php" class="btn btn-danger">Post Blood Request</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>