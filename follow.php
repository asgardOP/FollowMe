<?php
include('config.php');
$current = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
if(!$current){ header("Location: index.html"); exit; }

$target = isset($_POST['target']) ? $_POST['target'] : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'users.php';

if(!$target || !preg_match('/^[A-Za-z0-9_.]+$/', $target)){
    header("Location: $redirect"); exit;
}
if($target === $current){ header("Location: $redirect"); exit; }

if($action === 'follow'){
    $stmt = mysqli_prepare($connect, "INSERT IGNORE INTO follows (follower, following) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $current, $target);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} elseif($action === 'unfollow'){
    $stmt = mysqli_prepare($connect, "DELETE FROM follows WHERE follower = ? AND following = ?");
    mysqli_stmt_bind_param($stmt, "ss", $current, $target);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: $redirect");
exit;
?>