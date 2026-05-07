<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["LoggedIn"])){
    $_SESSION["LoggedIn"] = false;
}
if (!isset($_SESSION["UserId"])){
    $_SESSION["UserId"] = 0;
}

$ServerConnection = mysqli_connect("localhost", "SnappyCloud_Client", "112", "SnappyCloud");

if (!$ServerConnection) {
    echo Error;
    exit;
}

?>
