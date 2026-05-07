<?php
require "Connect.php";

if (isset($_SESSION["LoggedIn"]) && isset($_SESSION["UserId"])) {
    if (isset($_POST["AlbumName"]) && isset($_POST["AlbumDescription"])) {
        mysqli_query($ServerConnection, "INSERT INTO album (NamaAlbum, Deskripsi, UserId) VALUES ('".$_POST["AlbumName"]."','".$_POST["AlbumDescription"]."',".$_SESSION["UserId"].")");
        header("Location: ../MyAlbums.php");
    }
}
else {
    echo "Access Denied.";
    exit;
}
?>