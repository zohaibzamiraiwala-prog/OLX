<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo '<script>location.href = "index.php";</script>';
    exit;
}
$ad_id = (int)$_GET['id'];
$sql = "SELECT ads.*, users.name AS seller, users.phone, categories.name AS category FROM ads 
        JOIN users ON ads.user_id = users.id 
        JOIN categories ON ads.category_id = categories.id 
        WHERE ads.id = $ad_id";
$ad = $conn->query($sql)->fetch_assoc();

if (!$ad) {
    echo '<script>location.href = "index.php";</script>';
    exit;
}

// Fetch images
$img_sql = "SELECT image_path FROM images WHERE ad_id = $ad_id";
$imgs = $conn->query($img_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ad['title']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .ad-detail { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .images { display: flex; flex-wrap: wrap; gap: 10px; }
        .images img { width: 200px; height: 200px; object-fit: cover; border-radius: 4px; }
        .contact { margin-top: 20px; }
        button { background: #002f34; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        @media (max-width: 768px) { .ad-detail { padding: 15px; } .images img { width: 100%; height: auto; } }
    </style>
</head>
<body>
    <div class="ad-detail">
        <h2><?php echo $ad['title']; ?></h2>
        <p>Price: $<?php echo $ad['price']; ?></p>
        <p>Category: <?php echo $ad['category']; ?></p>
        <p>Condition: <?php echo ucfirst($ad['condition']); ?></p>
        <p>Location: <?php echo $ad['location']; ?></p>
        <p>Description: <?php echo nl2br($ad['description']); ?></p>
        <div class="images">
            <?php while ($img = $imgs->fetch_assoc()): ?>
                <img src="<?php echo $img['image_path']; ?>" alt="Ad Image">
            <?php endwhile; ?>
        </div>
        <div class="contact">
            <h3>Contact Seller: <?php echo $ad['seller']; ?></h3>
            <p>Phone: <?php echo $ad['phone']; ?></p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $ad['user_id']): ?>
                <a href="messages.php?receiver=<?php echo $ad['user_id']; ?>&ad=<?php echo $ad_id; ?>"><button>Chat with Seller</button></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html><?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo '<script>location.href = "index.php";</script>';
    exit;
}
$ad_id = (int)$_GET['id'];
$sql = "SELECT ads.*, users.name AS seller, users.phone, categories.name AS category FROM ads 
        JOIN users ON ads.user_id = users.id 
        JOIN categories ON ads.category_id = categories.id 
        WHERE ads.id = $ad_id";
$ad = $conn->query($sql)->fetch_assoc();

if (!$ad) {
    echo '<script>location.href = "index.php";</script>';
    exit;
}

// Fetch images
$img_sql = "SELECT image_path FROM images WHERE ad_id = $ad_id";
$imgs = $conn->query($img_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ad['title']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .ad-detail { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .images { display: flex; flex-wrap: wrap; gap: 10px; }
        .images img { width: 200px; height: 200px; object-fit: cover; border-radius: 4px; }
        .contact { margin-top: 20px; }
        button { background: #002f34; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        @media (max-width: 768px) { .ad-detail { padding: 15px; } .images img { width: 100%; height: auto; } }
    </style>
</head>
<body>
    <div class="ad-detail">
        <h2><?php echo $ad['title']; ?></h2>
        <p>Price: $<?php echo $ad['price']; ?></p>
        <p>Category: <?php echo $ad['category']; ?></p>
        <p>Condition: <?php echo ucfirst($ad['condition']); ?></p>
        <p>Location: <?php echo $ad['location']; ?></p>
        <p>Description: <?php echo nl2br($ad['description']); ?></p>
        <div class="images">
            <?php while ($img = $imgs->fetch_assoc()): ?>
                <img src="<?php echo $img['image_path']; ?>" alt="Ad Image">
            <?php endwhile; ?>
        </div>
        <div class="contact">
            <h3>Contact Seller: <?php echo $ad['seller']; ?></h3>
            <p>Phone: <?php echo $ad['phone']; ?></p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $ad['user_id']): ?>
                <a href="messages.php?receiver=<?php echo $ad['user_id']; ?>&ad=<?php echo $ad_id; ?>"><button>Chat with Seller</button></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
