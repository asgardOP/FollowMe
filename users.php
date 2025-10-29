<?php
include('config.php');
$current = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
if(!$current){ header("Location: index.html"); exit; }

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Users</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{
        color:black;
    }
    .list{ max-width:900px;margin:40px auto;padding:20px;
        background-color: #ebebebff;
        border-radius:10px;
        border: 2px solid #ccc;
    }
    .user{ display:flex;align-items:center;gap:16px;padding:10px;border-bottom:1px solid rgba(255,255,255,0.03);}
    .user img{width:60px;height:60px;border-radius:8px;object-fit:cover;}
    .user button{padding:10px 12px;border:0;border-radius:6px;cursor:pointer; transition: .1s;}
    .user button:hover{opacity:.7;}
    .user button:active{transform:scale(.9);}

    /* Search bar styles */
    .search-container {
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
    }

    .search-bar {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.2s;
        background: white;
    }

    .search-bar:focus {
        outline: none;
        border-color: rgb(188, 72, 255);
        box-shadow: 0 0 0 2px rgba(188, 72, 255, 0.1);
    }

    .search-btn {
        padding: 12px 24px;
        background: rgb(188, 72, 255);
        border: none;
        border-radius: 8px;
        color: black;
        cursor: pointer;
        transition: all 0.2s;
    }

    .search-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .no-results {
        text-align: center;
        padding: 30px;
        color: #666;
        font-style: italic;
    }
  </style>
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <div class="list">
        <h2>All users</h2>
        
        <!-- Search Form -->
        <form method="GET" class="search-container">
            <input 
                type="text" 
                name="search" 
                class="search-bar" 
                placeholder="Search by username or display name..."
                value="<?php echo htmlspecialchars($search); ?>"
            >
            <button type="submit" class="search-btn">Search</button>
        </form>

        <?php
        $query = "SELECT username, display_name, img, bio FROM users 
                 WHERE (username LIKE ? OR display_name LIKE ?) 
                 AND username != ?
                 ORDER BY username ASC";
        
        $stmt = mysqli_prepare($connect, $query);
        $searchTerm = "%$search%";
        mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $searchTerm, $current);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $found = false;
        while($row = mysqli_fetch_assoc($result)){
            $found = true;
            $s = mysqli_prepare($connect, "SELECT COUNT(*) FROM follows WHERE follower = ? AND following = ?");
            mysqli_stmt_bind_param($s, "ss", $current, $row['username']);
            mysqli_stmt_execute($s);
            mysqli_stmt_bind_result($s, $isFollowing);
            mysqli_stmt_fetch($s);
            mysqli_stmt_close($s);

            $btnText = $isFollowing ? "Unfollow" : "Follow";
            $btnColor = $isFollowing ? "background:transparent;color:grey;border:2px solid #ccc;" : "background:rgb(188, 72, 255);color:black;";
            echo "<div class='user'>
                    <img src='".htmlspecialchars($row['img'])."' alt='avatar'>
                    <div style='flex:1'>
                      <strong>".htmlspecialchars($row['display_name'])."</strong> <small style='opacity:.7'>(".htmlspecialchars($row['username']).")</small><br>
                      <small>".htmlspecialchars(substr($row['bio'],0,80))."</small>
                    </div>
                    <form method='post' action='follow.php'>
                      <input type='hidden' name='target' value='".htmlspecialchars($row['username'])."'>
                      <input type='hidden' name='action' value='".($isFollowing ? 'unfollow' : 'follow')."'>
                      <button type='submit' style='$btnColor'>$btnText</button>
                    </form>
                  </div>";
        }
        
        if (!$found) {
            echo "<div class='no-results'>No users found matching your search.</div>";
        }
        
        mysqli_stmt_close($stmt);
        ?>
    </div>
</body>
</html>