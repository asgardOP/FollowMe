<?php
include("config.php");

if(isset($_POST['submit'])){
    $username = trim($_POST['name']);
    if(!preg_match('/^[A-Za-z0-9_.]+$/', $username)){
        echo "<script>alert('Invalid username. Allowed: letters, numbers, _ and .'); window.history.back();</script>";
        exit;
    }

    if(!$connect){
        die("DB connect error: " . mysqli_connect_error());
    }

    // check duplicate on username column
    $query = "SELECT COUNT(*) FROM `users` WHERE `username` = ?";
    $stmt = mysqli_prepare($connect, $query);
    if(!$stmt){
        die("Prepare failed: " . mysqli_error($connect) . " — run DESCRIBE users; to verify columns.");
    }
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $cnt);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if($cnt > 0){
        echo "<script>alert('Username already taken'); window.history.back();</script>";
        exit;
    }

    $uploadDir = "imgs/";
    $img_up = $uploadDir . "default.png";

    if(isset($_FILES['img']) && !empty($_FILES['img'])) {
        $fileName = $_FILES['img']['name'];
        $fileTmp  = $_FILES['img']['tmp_name'];
        $fileErr  = $_FILES['img']['error'] ?? 0;

        if (is_array($fileName)) {
            $fileName = $fileName[0];
            $fileTmp  = $fileTmp[0];
            $fileErr  = is_array($fileErr) ? $fileErr[0] : $fileErr;
        }

        if ($fileName !== '' && $fileErr === UPLOAD_ERR_OK && is_uploaded_file($fileTmp)) {
            $orig = basename((string)$fileName);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $allowed = ['png','jpg','jpeg','gif','webp'];
            if(!in_array(strtolower($ext), $allowed)){
                echo "<script>alert('Invalid image type'); window.history.back();</script>";
                exit;
            }
            $safeName = time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
            $target = $uploadDir . $safeName;
            if(move_uploaded_file($fileTmp, $target)){
                $img_up = $target;
            } else {
                echo "<script>alert('Image upload failed'); window.history.back();</script>";
                exit;
            }
        }
    }

    $display_name = isset($_POST['display_name']) && trim($_POST['display_name']) !== '' ? trim($_POST['display_name']) : $username;
    $bio = "Unknown";

    $ins = "INSERT INTO `users` (`img`, `username`, `display_name`, `bio`, `created_at`) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($connect, $ins);
    if(!$stmt){
        die("Prepare failed for INSERT: " . mysqli_error($connect));
    }
    mysqli_stmt_bind_param($stmt, "ssss", $img_up, $username, $display_name, $bio);
    if(mysqli_stmt_execute($stmt)){
        setcookie("username", $username, time() + (864000 * 30), "/");
        header("Location: interface.php");
        exit;
    } else {
        die("Insert failed: " . mysqli_error($connect));
    }
    mysqli_stmt_close($stmt);
}
?>