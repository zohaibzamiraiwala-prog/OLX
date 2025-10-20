<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<script>location.href = "login.php";</script>';
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT ads.*, categories.name AS category FROM ads 
        JOIN categories ON ads.category_id = categories.id 
        WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);

$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user = $conn->query($user_sql)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .profile { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .listings { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .listing { background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 8px; }
        .listing img { width: 100%; height: 150px; object-fit: cover; }
        a { color: #002f34; text-decoration: none; }
        @media (max-width: 768px) { .profile { padding: 10px; } }
    </style>
</head>
<body>
    <div class="profile">
        <h2><?php echo $user['name']; ?>'s Profile</h2>
        <p>Email: <?php echo $user['email']; ?></p>
        <p>Phone: <?php echo $user['phone']; ?></p>
        <p>Location: <?php echo $user['location']; ?></p>
        <a href="post_ad.php">Post New Ad</a>
        <h3>My Listings</h3>
        <div class="listings">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="listing">
                    <?php 
                    $img_sql = "SELECT image_path FROM images WHERE ad_id = " . $row['id'] . " LIMIT 1";
                    $img = $conn->query($img_sql)->fetch_assoc();
                    ?>
                    <img src="<?php echo $img ? $img['image_path'] : 'default.jpg'; ?>" alt="">
                    <h4><?php echo $row['title']; ?></h4>
                    <p>Status: <?php echo $row['status']; ?></p>
                    <a href="edit_ad.php?id=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="delete_ad.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')">Delete</a> |
                    <?php if ($row['status'] == 'active'): ?>
                        <a href="mark_sold.php?id=<?php echo $row['id']; ?>">Mark Sold</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
