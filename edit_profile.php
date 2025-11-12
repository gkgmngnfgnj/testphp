<?php
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: /login');
    exit;
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_name = trim($_POST['display_name']);
    $bio = trim($_POST['bio']);
    $avatar_url = trim($_POST['avatar_url']);
    $custom_html = trim($_POST['custom_html']);
    
    // Validate
    if (empty($display_name)) {
        $message = '<div class="error">Display name is required</div>';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET display_name = ?, bio = ?, avatar_url = ?, custom_html = ? WHERE id = ?");
            $stmt->execute([$display_name, $bio, $avatar_url, $custom_html, $_SESSION['user_id']]);
            
            $message = '<div class="success">Profile updated successfully!</div>';
            
            // Update user data
            $user['display_name'] = $display_name;
            $user['bio'] = $bio;
            $user['avatar_url'] = $avatar_url;
            $user['custom_html'] = $custom_html;
            
        } catch (PDOException $e) {
            $message = '<div class="error">Error updating profile: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #667eea;
            outline: none;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn-save {
            background: #51cf66;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .preview-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <a href="/<?php echo $user['username']; ?>" class="home-link">← Back to Profile</a>
            <h1>Edit Your Profile</h1>
        </header>
        
        <?php echo $message; ?>
        
        <div class="edit-form">
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small>Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label for="display_name">Display Name *</label>
                    <input type="text" id="display_name" name="display_name" 
                           value="<?php echo htmlspecialchars($user['display_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="avatar_url">Avatar URL</label>
                    <input type="url" id="avatar_url" name="avatar_url" 
                           value="<?php echo htmlspecialchars($user['avatar_url'] ?? ''); ?>" 
                           placeholder="https://example.com/avatar.jpg">
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4" 
                              placeholder="Tell people about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="custom_html">Custom HTML</label>
                    <textarea id="custom_html" name="custom_html" rows="6" 
                              placeholder="Add custom HTML content to your profile..."><?php echo htmlspecialchars($user['custom_html'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="/<?php echo $user['username']; ?>" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
        
        <div class="preview-section">
            <h3>Preview</h3>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar">
                        <?php if (!empty($user['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar" id="preview-avatar">
                        <?php else: ?>
                            <div class="default-avatar" id="preview-default-avatar">
                                <?php echo strtoupper(substr($user['display_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <h2 id="preview-display-name"><?php echo htmlspecialchars($user['display_name']); ?></h2>
                        <p class="username">@<?php echo htmlspecialchars($user['username']); ?></p>
                        <p class="bio" id="preview-bio"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Live preview updates
        document.getElementById('display_name').addEventListener('input', function(e) {
            document.getElementById('preview-display-name').textContent = e.target.value;
            document.getElementById('preview-default-avatar').textContent = e.target.value.charAt(0).toUpperCase();
        });
        
        document.getElementById('bio').addEventListener('input', function(e) {
            document.getElementById('preview-bio').textContent = e.target.value;
        });
        
        document.getElementById('avatar_url').addEventListener('input', function(e) {
            const avatarUrl = e.target.value;
            const previewAvatar = document.getElementById('preview-avatar');
            const defaultAvatar = document.getElementById('preview-default-avatar');
            
            if (avatarUrl) {
                if (previewAvatar) {
                    previewAvatar.src = avatarUrl;
                } else {
                    // Create avatar image if it doesn't exist
                    const avatarDiv = document.querySelector('.avatar');
                    avatarDiv.innerHTML = `<img src="${avatarUrl}" alt="Avatar" id="preview-avatar">`;
                }
                if (defaultAvatar) {
                    defaultAvatar.style.display = 'none';
                }
            } else {
                // Show default avatar
                const avatarDiv = document.querySelector('.avatar');
                avatarDiv.innerHTML = `<div class="default-avatar" id="preview-default-avatar">${document.getElementById('display_name').value.charAt(0).toUpperCase()}</div>`;
            }
        });
    </script>
</body>
</html>