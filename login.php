<?php
include 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /' . $_SESSION['username']);
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $message = '<div class="error">Please fill in all fields</div>';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // For demo purposes, any password works. In production, use password_verify()
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['display_name'] = $user['display_name'];
            
            header('Location: /' . $user['username']);
            exit;
        } else {
            $message = '<div class="error">Invalid username or password</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="/" class="home-link">← Back to Home</a>
            <h1>Login to Your Account</h1>
        </header>
        
        <?php echo $message; ?>
        
        <div class="profile-card">
            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small>For demo, any password will work</small>
                </div>
                
                <div class="form-actions">
                    <a href="/" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">Login</button>
                </div>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Demo accounts: john, sarah, demo (any password)</p>
            </div>
        </div>
    </div>
</body>
</html>