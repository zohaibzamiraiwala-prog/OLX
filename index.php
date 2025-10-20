<?php
session_start();
include 'db.php';

// Fetch recent ads
$sql = "SELECT ads.*, users.name AS seller, categories.name AS category FROM ads 
        JOIN users ON ads.user_id = users.id 
        JOIN categories ON ads.category_id = categories.id 
        WHERE status = 'active' ORDER BY created_at DESC LIMIT 20";
$result = $conn->query($sql);

// Fetch featured (top 5 by price or random, here recent high price)
$featured_sql = "SELECT ads.*, users.name AS seller, categories.name AS category FROM ads 
                 JOIN users ON ads.user_id = users.id 
                 JOIN categories ON ads.category_id = categories.id 
                 WHERE status = 'active' ORDER BY price DESC LIMIT 5";
$featured_result = $conn->query($featured_sql);

// Handle search
$search_query = "";
$category_filter = "";
$price_min = "";
$price_max = "";
$condition_filter = "";
$location_filter = "";
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $category_filter = isset($_GET['category']) ? (int)$_GET['category'] : "";
    $price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : "";
    $price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : "";
    $condition_filter = isset($_GET['condition']) ? $conn->real_escape_string($_GET['condition']) : "";
    $location_filter = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : "";

    $where = "WHERE status = 'active' AND (title LIKE '%$search_query%' OR description LIKE '%$search_query%')";
    if ($category_filter) $where .= " AND category_id = $category_filter";
    if ($price_min) $where .= " AND price >= $price_min";
    if ($price_max) $where .= " AND price <= $price_max";
    if ($condition_filter) $where .= " AND `condition` = '$condition_filter'";
    if ($location_filter) $where .= " AND location LIKE '%$location_filter%'";

    $sql = "SELECT ads.*, users.name AS seller, categories.name AS category FROM ads 
            JOIN users ON ads.user_id = users.id 
            JOIN categories ON ads.category_id = categories.id $where ORDER BY created_at DESC";
    $result = $conn->query($sql);
}

// Fetch categories for filter
$cat_sql = "SELECT * FROM categories";
$cat_result = $conn->query($cat_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Clone - Homepage</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; color: #333; }
        header { background: #002f34; color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 2em; }
        nav { display: flex; justify-content: space-around; background: #002f34; padding: 10px; }
        nav a { color: white; text-decoration: none; font-weight: bold; }
        .search-bar { margin: 20px auto; width: 80%; max-width: 800px; }
        .search-bar form { display: flex; flex-wrap: wrap; gap: 10px; }
        .search-bar input, .search-bar select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; flex: 1; min-width: 150px; }
        .search-bar button { background: #002f34; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        .featured, .recent { padding: 20px; }
        .listings { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .listing { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .listing:hover { transform: scale(1.02); }
        .listing img { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; }
        .listing h3 { margin: 10px 0; font-size: 1.2em; }
        .listing p { margin: 5px 0; }
        footer { background: #002f34; color: white; text-align: center; padding: 10px; position: fixed; bottom: 0; width: 100%; }
        @media (max-width: 768px) { .search-bar form { flex-direction: column; } nav { flex-direction: column; } }
    </style>
</head>
<body>
    <header>
        <h1>OLX Clone Marketplace</h1>
    </header>
    <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <a href="post_ad.php">Post Ad</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($cat = $cat_result->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if ($category_filter == $cat['id']) echo 'selected'; ?>><?php echo $cat['name']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="number" name="price_min" placeholder="Min Price" value="<?php echo $price_min; ?>">
            <input type="number" name="price_max" placeholder="Max Price" value="<?php echo $price_max; ?>">
            <select name="condition">
                <option value="">Any Condition</option>
                <option value="new" <?php if ($condition_filter == 'new') echo 'selected'; ?>>New</option>
                <option value="used" <?php if ($condition_filter == 'used') echo 'selected'; ?>>Used</option>
            </select>
            <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location_filter); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <section class="featured">
        <h2>Featured Listings</h2>
        <div class="listings">
            <?php while ($row = $featured_result->fetch_assoc()): ?>
                <div class="listing">
                    <?php 
                    $img_sql = "SELECT image_path FROM images WHERE ad_id = " . $row['id'] . " LIMIT 1";
                    $img_result = $conn->query($img_sql);
                    $img = $img_result->fetch_assoc();
                    ?>
                    <img src="<?php echo $img ? $img['image_path'] : 'default.jpg'; ?>" alt="Ad Image">
                    <h3><?php echo $row['title']; ?></h3>
                    <p>Price: $<?php echo $row['price']; ?></p>
                    <p>Category: <?php echo $row['category']; ?></p>
                    <p>Seller: <?php echo $row['seller']; ?></p>
                    <a href="ad.php?id=<?php echo $row['id']; ?>">View Details</a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <section class="recent">
        <h2>Recent Listings</h2>
        <div class="listings">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="listing">
                    <?php 
                    $img_sql = "SELECT image_path FROM images WHERE ad_id = " . $row['id'] . " LIMIT 1";
                    $img_result = $conn->query($img_sql);
                    $img = $img_result->fetch_assoc();
                    ?>
                    <img src="<?php echo $img ? $img['image_path'] : 'default.jpg'; ?>" alt="Ad Image">
                    <h3><?php echo $row['title']; ?></h3>
                    <p>Price: $<?php echo $row['price']; ?></p>
                    <p>Category: <?php echo $row['category']; ?></p>
                    <p>Seller: <?php echo $row['seller']; ?></p>
                    <a href="ad.php?id=<?php echo $row['id']; ?>">View Details</a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <footer>&copy; 2025 OLX Clone</footer>
</body>
</html>i
