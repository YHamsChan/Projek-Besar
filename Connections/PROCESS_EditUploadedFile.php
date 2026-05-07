<?php
require "Connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['UserId'])) {
    
    // Menerima parameter Method dari form
    $SubmitMethod = isset($_POST["Method"]) ? $_POST["Method"] : "";
?><p><?php echo $SubmitMethod; ?></p><?php
    if ($SubmitMethod === "Photo") {
        // Logika untuk Edit Photo 📸
        $TargetFotoId = isset($_POST["FotoId"]) ? (int)$_POST["FotoId"] : 0;
        $NewPhotoTitle = isset($_POST["JudulFoto"]) ? trim($_POST["JudulFoto"]) : "";
        $NewPhotoDescription = isset($_POST["DeskripsiFoto"]) ? trim($_POST["DeskripsiFoto"]) : "";

        if ($TargetFotoId > 0 && !empty($NewPhotoTitle)) {
            $UpdatePhotoQuery = "UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ? WHERE FotoId = ? AND UserId = ?";
            $DatabaseStatement = mysqli_prepare($ServerConnection, $UpdatePhotoQuery);

            if ($DatabaseStatement) {
                mysqli_stmt_bind_param($DatabaseStatement, "ssii", $NewPhotoTitle, $NewPhotoDescription, $TargetFotoId, $_SESSION['UserId']);
                mysqli_stmt_execute($DatabaseStatement);
                mysqli_stmt_close($DatabaseStatement);
                
                header("Location: ../MyPhotos.php");
                exit();
            }
        } else {
            header("Location: ../MyPhotos.php?Error=InvalidInput");
            exit();
        }

    } elseif ($SubmitMethod === "Album") {
        // Logika untuk Edit Album 📁
        $TargetAlbumId = isset($_POST["AlbumId"]) ? (int)$_POST["AlbumId"] : 0;
        $NewAlbumName = isset($_POST["AlbumName"]) ? trim($_POST["AlbumName"]) : "";
        
        // Asumsi: Anda memiliki kolom DeskripsiAlbum. Jika tidak, hapus bagian ini dan sesuaikan Query-nya.
        $NewAlbumDescription = isset($_POST["AlbumDescription"]) ? trim($_POST["AlbumDescription"]) : ""; 

        if ($TargetAlbumId > 0 && !empty($NewAlbumName)) {
            $UpdateAlbumQuery = "UPDATE album SET NamaAlbum = ?, Deskripsi = ? WHERE AlbumId = ? AND UserId = ?";
            $DatabaseStatement = mysqli_prepare($ServerConnection, $UpdateAlbumQuery);

            if ($DatabaseStatement) {
                mysqli_stmt_bind_param($DatabaseStatement, "ssii", $NewAlbumName, $NewAlbumDescription, $TargetAlbumId, $_SESSION['UserId']);
                mysqli_stmt_execute($DatabaseStatement);
                mysqli_stmt_close($DatabaseStatement);
                
                header("Location: ../MyAlbums.php");
                exit();
            }
        } else {
            header("Location: ../MyAlbums.php?Error=InvalidInput");
            exit();
        }

    } else {
        // Jika Method tidak dikenali
        header("Location: ../index.php?Error=UnknownMethod");
        exit();
    }

} else {
    // Kicked out if accessed directly
    header("Location: ../index.php");
    exit();
}
?>