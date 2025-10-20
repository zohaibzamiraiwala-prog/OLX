<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);
    $location = $conn->real_escape_string($_POST['location']);

    $sql = "INSERT INTO users (name, email, password, phone, location) VALUES ('$name', '$email', '$password', '$phone', '$location')";
    if ($conn->query($sql)) {
        $_SESSION['user_id'] = $conn->insert_id;
        echo '<script>location.href = "index.php";</script>';
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
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        form { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
        input { display: block; width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #002f34; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; border-radius: 4px; }
        button:hover { background: #004f54; }
        @media (max-width: 768px) { form { width: 90%; padding: 20px; } }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="phone" placeholder="Phone">
        <input type="text" name="location" placeholder="Location">
        <button type="submit">Sign Up</button>
        <p>Already have account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>
