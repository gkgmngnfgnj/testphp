<?php
include 'config.php';

// Get username from URL
$username = isset($_GET['username']) ? trim($_GET['username']) : '';

if (empty($username)) {
    header('Location: /');
    exit;
}

// Function to get user profile from database
function getUserProfile($username, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR display_name = ?");
    $stmt->execute([$username, $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to create default profile if doesn't exist
function createDefaultProfile($username, $pdo) {
    // Check if user exists
    $user = getUserProfile($username, $pdo);
    
    if (!$user) {
        // Create default profile
        $stmt = $pdo->prepare("INSERT INTO users (username, display_name, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([strtolower($username), $username]);
        
        return getUserProfile($username, $pdo);
    }
    
    return $user;
}

// Get or create user profile
$user = createDefaultProfile($username, $pdo);

// If still no user, show error
if (!$user) {
    header('HTTP/1.0 404 Not Found');
    die('Profile not found');
}

// Update view count
$stmt = $pdo->prepare("UPDATE users SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$user['id']]);

// Check if current user owns this profile
$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['display_name']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="/" class="home-link">← Back to Home</a>
            <h1><?php echo htmlspecialchars($user['display_name']); ?></h1>
        </header>
        
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar">
                    <?php if (!empty($user['avatar_url'])): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="default-avatar">
                            <?php echo strtoupper(substr($user['display_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['display_name']); ?></h2>
                    <p class="username">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <?php if (!empty($user['bio'])): ?>
                        <p class="bio"><?php echo htmlspecialchars($user['bio']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-stats">
                <div class="stat">
                    <span class="stat-number"><?php echo $user['view_count'] ?? 0; ?></span>
                    <span class="stat-label">Views</span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo $user['created_at'] ? date('M Y', strtotime($user['created_at'])) : 'Unknown'; ?></span>
                    <span class="stat-label">Joined</span>
                </div>
            </div>
            
            <?php if (!empty($user['custom_html'])): ?>
                <div class="custom-content">
                    <?php echo $user['custom_html']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="profile-actions">
            <button onclick="shareProfile()" class="btn-share">Share Profile</button>
            <?php if ($is_owner): ?>
                <button onclick="editProfile()" class="btn-edit">Edit Profile</button>
            <?php endif; ?>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <button onclick="login()" class="btn-login">Login to Edit</button>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function shareProfile() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Profile URL copied to clipboard!');
            });
        }
        
        function editProfile() {
            window.location.href = '/edit_profile';
        }
        
        function login() {
            window.location.href = '/login';
        }
    </script>
</body>
</html>