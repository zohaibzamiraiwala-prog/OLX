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

    // Check ownership
    $check_sql = "SELECT * FROM ads WHERE id = $ad_id AND user_id = $user_id";
    if ($conn->query($check_sql)->num_rows > 0) {
        // Delete images first (files)
        $img_sql = "SELECT image_path FROM images WHERE ad_id = $ad_id";
        $imgs = $conn->query($img_sql);
        while ($img = $imgs->fetch_assoc()) {
            unlink($img['image_path']);
        }

        $sql = "DELETE FROM ads WHERE id = $ad_id";
        $conn->query($sql); // Cascades to images and messages
    }
    echo '<script>location.href = "profile.php";</script>';
}
?>
