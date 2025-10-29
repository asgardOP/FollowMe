<?php

include __DIR__ . '/config.php';
if (!$connect) { die('DB error: '.mysqli_connect_error()); }
$res = mysqli_query($connect, "DESCRIBE `users`");
if (!$res) { die('Query failed: '.mysqli_error($connect)); }
echo "<pre>";
while($r = mysqli_fetch_assoc($res)){
    echo "{$r['Field']}\t{$r['Type']}\t{$r['Null']}\t{$r['Key']}\n";
}
echo "</pre>";
?>