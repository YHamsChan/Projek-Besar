<?php
// Session is assumed to be handled in Connect.php
require "Connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["PhotoFile"])) {
    $MaximumFileSize = 4 * 1024 * 1024; // 4MB Limit
    $AllowedMimeTypes = ["image/jpeg", "image/png", "image/jpg"];
    $UploadDirectory = "Uploads/";

    if (!is_dir($UploadDirectory)) {
        mkdir($UploadDirectory, 0755, true);
    }

    $UploadedFile = $_FILES["PhotoFile"];

    if ($UploadedFile["error"] !== UPLOAD_ERR_OK) {
        die("Error: Upload failed with code " . $UploadedFile["error"] . " ❌");
    }

    if ($UploadedFile["size"] > $MaximumFileSize) {
        die("Error: File is too large. Maximum 4MB allowed. 🚫");
    }

    $FileTemporaryPath = $UploadedFile["tmp_name"];
    $FileMimeType = strtolower(mime_content_type($FileTemporaryPath));

    if (!in_array($FileMimeType, $AllowedMimeTypes)) {
        die("Error: Invalid file type (" . $FileMimeType . "). Only JPG and PNG allowed. 🚫");
    }

    $FileExtension = pathinfo($UploadedFile["name"], PATHINFO_EXTENSION);
    $CleanExtension = strtolower($FileExtension);
    
    $CurrentUserId = isset($_SESSION["UserId"]) ? $_SESSION["UserId"] : 0;
    
    // CRITICAL FIX: Fetching form data to satisfy strict database columns
    $PhotoTitle = isset($_POST["PhotoTitle"]) ? $_POST["PhotoTitle"] : "Untitled";
    $AlbumId = isset($_POST["AlbumId"]) ? $_POST["AlbumId"] : "Untitled";
    $PhotoDescription = isset($_POST["PhotoDescription"]) ? $_POST["PhotoDescription"] : "No description provided.";

    $NewFileName = "User_" . $CurrentUserId . "_" . bin2hex(random_bytes(8)) . "." . $CleanExtension;
    $TargetFilePath = $UploadDirectory . $NewFileName;

    if (move_uploaded_file($FileTemporaryPath, $TargetFilePath)) {
        
        // FIXED: Added JudulFoto and DeskripsiFoto to prevent MySQL crash
        $DatabaseQuery = "INSERT INTO foto (UserId, AlbumId, JudulFoto, DeskripsiFoto, LokasiFile) VALUES (?, ?, ?, ?, ?)";
        $DatabaseStatement = mysqli_prepare($ServerConnection, $DatabaseQuery);

        if ($DatabaseStatement) {
            
            // "isss" = Integer (UserId), String (Title), String (Description), String (Path)
            mysqli_stmt_bind_param($DatabaseStatement, "iisss", $CurrentUserId, $AlbumId, $PhotoTitle, $PhotoDescription, $TargetFilePath);

            if (mysqli_stmt_execute($DatabaseStatement)) {
                echo "Upload Success! File saved as: " . $NewFileName . " and recorded in database. ✅";
                header("Location: ../MyPhotos.php");
            } else {
                die("Error: Failed to save record to the database. 🛠️");
            }
            
            mysqli_stmt_close($DatabaseStatement);
        } else {
            die("Error: Database statement preparation failed. 🚨");
        }

    } else {
        die("Error: Could not save the file to the server. 🛠️");
    }
}
?>