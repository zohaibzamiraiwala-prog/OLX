<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<script>location.href = "login.php";</script>';
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver']) ? (int)$_GET['receiver'] : 0;
$ad_id = isset($_GET['ad']) ? (int)$_GET['ad'] : null;

if ($receiver_id == 0) {
    // Show list of conversations
    $conv_sql = "SELECT DISTINCT IF(sender_id = $user_id, receiver_id, sender_id) AS other_user, users.name 
                 FROM messages JOIN users ON users.id = IF(sender_id = $user_id, receiver_id, sender_id)
                 WHERE sender_id = $user_id OR receiver_id = $user_id ORDER BY timestamp DESC";
    $convs = $conn->query($conv_sql);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Messages</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
            .convs { max-width: 600px; margin: auto; }
            .conv { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            a { color: #002f34; text-decoration: none; }
        </style>
    </head>
    <body>
        <h2>Your Conversations</h2>
        <div class="convs">
            <?php while ($conv = $convs->fetch_assoc()): ?>
                <div class="conv">
                    <a href="messages.php?receiver=<?php echo $conv['other_user']; ?>">Chat with <?php echo $conv['name']; ?></a>
                </div>
            <?php endwhile; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle send message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $conn->real_escape_string($_POST['message']);
    $ad_id_val = $ad_id ? $ad_id : 'NULL';
    $sql = "INSERT INTO messages (sender_id, receiver_id, ad_id, message) VALUES ($user_id, $receiver_id, $ad_id_val, '$message')";
    $conn->query($sql);
}

// Fetch messages
$msg_sql = "SELECT * FROM messages WHERE 
            (sender_id = $user_id AND receiver_id = $receiver_id) OR 
            (sender_id = $receiver_id AND receiver_id = $user_id) 
            ORDER BY timestamp ASC";
$msgs = $conn->query($msg_sql);

// Get receiver name
$rec_sql = "SELECT name FROM users WHERE id = $receiver_id";
$receiver_name = $conn->query($rec_sql)->fetch_assoc()['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo $receiver_name; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .chat { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .messages { height: 400px; overflow-y: scroll; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .msg { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .sent { background: #dcf8c6; align-self: flex-end; }
        .received { background: #fff; }
        form { display: flex; }
        input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; }
        button { background: #002f34; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 0 4px 4px 0; }
        @media (max-width: 768px) { .chat { padding: 10px; } }
        <script>
            function scrollToBottom() {
                var messages = document.querySelector('.messages');
                messages.scrollTop = messages.scrollHeight;
            }
            window.onload = scrollToBottom;
        </script>
    </style>
</head>
<body>
    <div class="chat">
        <h2>Chat with <?php echo $receiver_name; ?></h2>
        <div class="messages">
            <?php while ($msg = $msgs->fetch_assoc()): ?>
                <div class="msg <?php echo $msg['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <p><?php echo nl2br($msg['message']); ?></p>
                    <small><?php echo $msg['timestamp']; ?></small>
                </div>
            <?php endwhile; ?>
        </div>
        <form method="POST">
            <input type="text" name="message" placeholder="Type message..." required>
            <button type="submit">Send</button>
        </form>
    </div>
    <script>scrollToBottom();</script>
</body>
</html>
