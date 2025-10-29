<?php
setcookie("username", "", time() - 3600, "/");
header("Location: index.html");
exit;
?>