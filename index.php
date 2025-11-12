<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to <?php echo SITE_NAME; ?></h1>
            <p>View profiles by typing /username in the URL</p>
        </header>
        
        <div class="search-box">
            <input type="text" id="usernameInput" placeholder="Enter username">
            <button onclick="viewProfile()">View Profile</button>
        </div>
        
        <div class="features">
            <h2>Featured Profiles</h2>
            <!-- Add featured profiles here -->
        </div>
    </div>

    <script>
        function viewProfile() {
            const username = document.getElementById('usernameInput').value.trim();
            if (username) {
                window.location.href = '/' + username;
            }
        }
        
        // Allow Enter key to submit
        document.getElementById('usernameInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                viewProfile();
            }
        });
    </script>
</body>
</html>