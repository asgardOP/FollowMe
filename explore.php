<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            padding-bottom: 50px;
            font-family: Arial, sans-serif;
        }

        .feed {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
        }
        .post {
            background: #edededff;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            border: 2px solid #ccc;
        }
        .post-header {
            display: flex;
            align-items: center;
            padding: 10px;
            gap: 10px;
        }
        .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .post-header .meta {
            flex: 1;
            color: #000000ff;
        }
        .post-header .follow-btn {
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            background: rgb(188, 72, 255);
            cursor: pointer;
            color: black;
            transition: all 0.2s ease;
        }
        
        .post-header .follow-btn:hover {
            opacity: 0.8;
        }
        
        .post-header .follow-btn.following {
            background: transparent;
            border: 2px solid #ccc;
            color: #666;
        }
        .post-img {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
            background: #000;
        }
        .post-caption {
            padding: 15px;
            color: #1e1e1eff;
            font-family: Arial, sans-serif;

        }
        .post-time {
            padding: 0 15px 15px;
            color: rgba(62, 62, 62, 0.6);
            font-size: 14px;
        }

        .nav-btn.active { background: #ff9ff2; }
        .feed { margin-top: 70px; }
    </style>
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <?php
    include('config.php');
    $current = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
    if(!$current){ header("Location: index.html"); exit; }

    // Get all posts ordered by newest first
    $sql = "SELECT p.*, u.img as user_img, u.display_name,
            (SELECT COUNT(*) FROM follows WHERE follower = ? AND following = p.username) as is_following
            FROM posts p
            JOIN users u ON p.username = u.username
            WHERE p.username != ?
            ORDER BY p.created_at DESC
            LIMIT 50";
    
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $current, $current);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    ?>



    <div class="feed">
        <?php while($post = mysqli_fetch_assoc($result)): ?>
            <div class="post">
                <div class="post-header">
                    <img src="<?php echo htmlspecialchars($post['user_img']); ?>" alt="profile">
                    <div class="meta">
                        <strong><?php echo htmlspecialchars($post['display_name']); ?></strong>
                        <div style="opacity:0.7">@<?php echo htmlspecialchars($post['username']); ?></div>
                    </div>
                    <form method="post" action="follow.php" style="margin:0">
                        <input type="hidden" name="target" value="<?php echo htmlspecialchars($post['username']); ?>">
                        <input type="hidden" name="action" value="<?php echo $post['is_following'] ? 'unfollow' : 'follow'; ?>">
                        <input type="hidden" name="redirect" value="explore.php">
                        <button type="submit" class="follow-btn <?php echo $post['is_following'] ? 'following' : ''; ?>">
                            <?php echo $post['is_following'] ? 'Following' : 'Follow'; ?>
                        </button>
                    </form>
                </div>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="post" class="post-img">
                <?php if($post['caption']): ?>
                    <div class="post-caption"><?php echo nl2br(htmlspecialchars($post['caption'])); ?></div>
                <?php endif; ?>
                <div class="post-time"><?php echo date('F j, Y g:i a', strtotime($post['created_at'])); ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>