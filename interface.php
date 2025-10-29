<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <center>
        <?php include 'components/navbar.php'; ?>

        <div id="profile">
        <?php
            include('config.php');
            $cookie =  isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
            if(!$cookie){
                header("Location: index.html");
                exit;
            }
            $stmt = mysqli_prepare($connect, "SELECT img, username, display_name, bio, created_at FROM `users` WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $cookie);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt); 
            mysqli_stmt_bind_result($stmt, $img, $username, $display_name, $bio, $created_at);
            if(mysqli_stmt_fetch($stmt)){
                $s1 = mysqli_prepare($connect, "SELECT COUNT(*) FROM follows WHERE following = ?");
                mysqli_stmt_bind_param($s1, "s", $username);
                mysqli_stmt_execute($s1);
                mysqli_stmt_bind_result($s1, $followers_cnt);
                mysqli_stmt_fetch($s1);
                mysqli_stmt_close($s1);

                $s2 = mysqli_prepare($connect, "SELECT COUNT(*) FROM follows WHERE follower = ?");
                mysqli_stmt_bind_param($s2, "s", $username);
                mysqli_stmt_execute($s2);
                mysqli_stmt_bind_result($s2, $following_cnt);
                mysqli_stmt_fetch($s2);
                mysqli_stmt_close($s2);

                echo "
                <div id='img' style='background-image: url(" . htmlspecialchars($img) . ")'></div>
                <p id='user'>" . htmlspecialchars($display_name) . " (" . htmlspecialchars($username) . ")</p>
                <p id='year'>" . htmlspecialchars($created_at) . "</p>
                <p id='bio'>" . htmlspecialchars($bio) . "</p>
                <ul id='numbers'>
                    <li> <span style='color: rgb(255, 159, 242); font-weight: 900;'>" . intval($followers_cnt) . "</span>  <br> Followers</li>
                    <li> <span style='color: rgb(255, 159, 242); font-weight: 900;'>" . intval($following_cnt) . "</span>  <br> Following</li>
                </ul>
                ";
            } else {
                echo "<p style='color:white'>User not found</p>";
            }
            mysqli_stmt_close($stmt);
            ?>

        <ul id="btns">
            <li>
                <a href="post.php" style="text-decoration:none">
            <button class="button" type="button">
                <p>+ New Post</p>
            </button>
        </a>
            </li>
            <li>
                <a href="users.php" style="text-decoration:none">
                <button class="button" type="button">
                    <p>Users</p>
                </button>
                </a>
            </li>
            <li>
                <a href="logout.php" style="text-decoration:none">
                <button class="button" style="left: 0px; background-color: rgb(236, 56, 24); color: white;">
                    <p>Logout</p>
                </button>
                </a>
            </li>
        </ul>
        </div>

        <!-- Add Posts Section -->
        <div class="posts-grid">
            <h3>My Posts</h3>
            <?php
            // Get user's posts
            $posts_sql = "SELECT * FROM posts WHERE username = ? ORDER BY created_at DESC";
            $posts_stmt = mysqli_prepare($connect, $posts_sql);
            mysqli_stmt_bind_param($posts_stmt, "s", $username);
            mysqli_stmt_execute($posts_stmt);
            $posts_result = mysqli_stmt_get_result($posts_stmt);
            
            if(mysqli_num_rows($posts_result) > 0):
                while($post = mysqli_fetch_assoc($posts_result)): ?>
                    <div class="post-item">
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="post">
                        <div class="post-overlay">
                            <p><?php echo htmlspecialchars($post['caption']); ?></p>
                            <form method="post" action="delete_post.php" class="delete-form" 
                                  onsubmit="return confirm('Are you sure you want to delete this post?');">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="delete-btn">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <p class="no-posts">No posts yet</p>
            <?php endif;
            mysqli_stmt_close($posts_stmt);
            ?>
        </div>
    </center>
</body>
</html>