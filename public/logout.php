<?php
require_once __DIR__ . '/../app/helpers/auth.php';

set_no_cache_headers();
logout_user();

header("Location: login.php");
exit;
?>