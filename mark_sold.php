<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<script>location.href = "login.php";</script>';
    exit;
}
include 'db.php';

if (isset($_GET['id'])) {
    $ad_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE ads SET status = 'sold' WHERE id = $ad_id AND user_id = $user_id";
    $conn->query($sql);
    echo '<script>location.href = "profile.php";</script>';
}
?>
