<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
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
            color: black;
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
            background: #fff;
            cursor: pointer;
        }
        .post-img {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
            background: #000;
        }

        .post-caption {
            padding: 15px;
            color: #111111ff;
            font-family: Arial, sans-serif;

        }
        .post-time {
            padding: 20px 15px 15px;
            color: rgba(39, 39, 39, 0.6);
            font-size: 14px;
            margin-top: -10px;
        }

        .feed { margin-top: 70px; }
    </style>
</head>
<body>
    <?php
    include('config.php');
    $current = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
    if(!$current){ header("Location: index.html"); exit; }

    $sql = "SELECT p.*, u.img as user_img, u.display_name,
            (SELECT COUNT(*) FROM follows WHERE follower = ? AND following = p.username) as is_following
            FROM posts p
            JOIN users u ON p.username = u.username
            WHERE p.username = ? OR p.username IN (SELECT following FROM follows WHERE follower = ?)
            ORDER BY p.created_at DESC";
    
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $current, $current, $current);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    ?>

    <?php include 'components/navbar.php'; ?>

    <div class="feed">
        <?php while($post = mysqli_fetch_assoc($result)): ?>
            <div class="post">
                <div class="post-header">
                    <img src="<?php echo htmlspecialchars($post['user_img']); ?>" alt="profile">
                    <div class="meta">
                        <strong><?php echo htmlspecialchars($post['display_name']); ?></strong>
                        <div style="opacity:0.7">@<?php echo htmlspecialchars($post['username']); ?></div>
                    </div>
                    <?php if($post['username'] !== $current): ?>
                        <form method="post" action="follow.php" style="margin:0">
                            <input type="hidden" name="target" value="<?php echo htmlspecialchars($post['username']); ?>">
                            <input type="hidden" name="action" value="<?php echo $post['is_following'] ? 'unfollow' : 'follow'; ?>">
                            <button type="submit" class="follow-btn" style="<?php echo $post['is_following'] ? 'background:transparent; border: 2px solid #ccc; color: #686868ff;' : ''; ?>">
                                <?php echo $post['is_following'] ? 'Following' : 'Follow'; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="post" class="post-img">
                <?php if($post['caption']): ?>
                    <div class="post-caption"><?php echo nl2br(htmlspecialchars($post['caption'])); ?></div>
                <?php endif; ?>
                <div class="post-time"><?php echo date('F j, Y g:i a', strtotime($post['created_at'])); ?></div>
            </div>
        <?php endwhile; ?>
        <?php if(mysqli_num_rows($result) === 0): ?>
            <div style="text-align:center;color:#fff;padding:40px;">
                <h3 style="color: #050505ff;">Welcome to your feed!</h3>
                <p style="color: #737373ff;">Follow people or make a post to see content here.</p>
                <a href="explore.php" class="nav-btn" style="display:inline-block;margin-top:20px;text-decoration: underline;">Find people to follow</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>