<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<script>location.href = "login.php";</script>';
    exit;
}
include 'db.php';
 
// Fetch categories
$cat_sql = "SELECT * FROM categories";
$cat_result = $conn->query($cat_sql);
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category'];
    $condition = $conn->real_escape_string($_POST['condition']);
    $location = $conn->real_escape_string($_POST['location']);
    $user_id = $_SESSION['user_id'];
 
    $sql = "INSERT INTO ads (user_id, category_id, title, description, price, `condition`, location) 
            VALUES ($user_id, $category_id, '$title', '$description', $price, '$condition', '$location')";
    if ($conn->query($sql)) {
        $ad_id = $conn->insert_id;
 
        // Handle images (up to 4)
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $ext;
                    $target = $upload_dir . $filename;
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                        $img_sql = "INSERT INTO images (ad_id, image_path) VALUES ($ad_id, '$target')";
                        $conn->query($img_sql);
                    }
                }
            }
        }
        echo '<script>location.href = "profile.php";</script>';
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Ad</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        form { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        input, textarea, select { display: block; width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #002f34; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; border-radius: 4px; }
        button:hover { background: #004f54; }
        @media (max-width: 768px) { form { padding: 20px; } }
    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Post New Ad</h2>
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="number" name="price" placeholder="Price" required step="0.01">
        <select name="category" required>
            <option value="">Select Category</option>
            <?php while ($cat = $cat_result->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <select name="condition" required>
            <option value="">Select Condition</option>
            <option value="new">New</option>
            <option value="used">Used</option>
        </select>
        <input type="text" name="location" placeholder="Location" required>
        <input type="file" name="images[]" multiple accept="image/*" >
        <button type="submit">Post Ad</button>
    </form>
</body>
</html>
