<?php
require "Connections/Connect.php";

$CompleteData = "";

if (isset($_SESSION['UserId']) && $_SESSION['UserId'] != 0) {
    $DatabaseStatement = $ServerConnection->prepare("SELECT * FROM User WHERE UserId = " . $_SESSION['UserId']);
    $DatabaseStatement->execute();
    $DatabaseResult = $DatabaseStatement->get_result();

    if ($DataRow = $DatabaseResult->fetch_assoc()) {
        $CompleteData = $DataRow;
    }
}

// Retrieve Method to determine which UI to show
$SubmitMethod = isset($_POST['Method']) ? $_POST['Method'] : "";

// Store Raw Post For Debugging
$RawPostData = $_POST;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnappyCloud | Edit Data</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat+Brush&family=Caveat:wght@400..700&family=Cherry+Bomb+One&family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Indie+Flower&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Css/index.css">
</head>
<body>
    <header>
        <div style="width: 60%;">
            <div class="Icon"></div>
            <h1>Snappy Cloud</h1>
            <div class="SearchBox"></div>
        </div>
        <div style="display: flex;">
            <div><a href="index.php" class="Navigation">Home</a></div>
            <div><a href="MyAlbums.php" class="Navigation">My Albums</a></div>
            <div><a href="MyPhotos.php" class="Navigation">My Photos</a></div>
            <div><a href="MyProfile.php" class="Navigation">My Profile</a></div>
        </div>
    </header>
    
    <div>
        <?php 
        if ($CompleteData != "") { 
            
            // ==========================================
            // UI FOR EDITING PHOTOS 📸
            // ==========================================
            if ($SubmitMethod === "Photo") {
                $TargetFotoId = isset($_POST['FotoId']) ? (int)$_POST['FotoId'] : 0;
                $CurrentPhotoTitle = isset($_POST['CurrentPhotoTitle']) ? htmlspecialchars($_POST['CurrentPhotoTitle'], ENT_QUOTES, 'UTF-8') : "";
                $CurrentPhotoDescription = isset($_POST['CurrentPhotoDescription']) ? htmlspecialchars($_POST['CurrentPhotoDescription'], ENT_QUOTES, 'UTF-8') : "";
                
                if ($TargetFotoId > 0) {
        ?>
                <br>
                <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Edit Photo Details</h2>
                
                <div class="ShadowedBox" style="margin-left: 10%; margin-right: 40%; width: 50%; border-radius: 20px; padding: 2%; padding-left: 3%;">
                    <form action="Connections/Process_EditUploadedFile.php" method="POST">
                        <input type="hidden" name="Method" value="Photo">
                        <input type="hidden" name="FotoId" value="<?php echo $TargetFotoId; ?>">

                        <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Photo Title</p>
                        <input name="JudulFoto" value="<?php echo $CurrentPhotoTitle; ?>" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 5px; margin-bottom: 15px;" type="text" required maxlength="25">
                        
                        <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Description</p>
                        <textarea name="DeskripsiFoto" style="font-size: 20px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 5px; margin-bottom: 20px;" rows="4" required maxlength="120"><?php echo $CurrentPhotoDescription; ?></textarea>
                        
                        <br>
                        <button type="submit" style="font-family: 'Comic Neue'; font-weight: bold; font-size: 25px; padding: 5px 15px; border-radius: 10px; cursor: pointer;">
                            Save Photo
                        </button>
                    </form>
                </div>
        <?php
                }
            } 
            // ==========================================
            // UI FOR EDITING ALBUMS 📁
            // ==========================================
            elseif ($SubmitMethod === "Album") {
                $TargetAlbumId = isset($_POST['AlbumId']) ? (int)$_POST['AlbumId'] : 0;
                $CurrentAlbumName = isset($_POST['AlbumName']) ? htmlspecialchars($_POST['AlbumName'], ENT_QUOTES, 'UTF-8') : "";
                $CurrentAlbumDescription = isset($_POST['AlbumDescription']) ? htmlspecialchars($_POST['AlbumDescription'], ENT_QUOTES, 'UTF-8') : "";
                
                if ($TargetAlbumId > 0) {
        ?>
                <br>
                <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Edit Album Details</h2>
                
                <div class="ShadowedBox" style="margin-left: 10%; margin-right: 40%; width: 50%; border-radius: 20px; padding: 2%; padding-left: 3%;">
                    <form action="Connections/Process_EditUploadedFile.php" method="POST">
                        <input type="hidden" name="Method" value="Album">
                        <input type="hidden" name="AlbumId" value="<?php echo $TargetAlbumId; ?>">

                        <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Album Name</p>
                        <input name="AlbumName" value="<?php echo $CurrentAlbumName; ?>" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 5px; margin-bottom: 15px;" type="text" required maxlength="25">
                        
                        <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Album Description</p>
                        <textarea name="AlbumDescription" style="font-size: 20px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 5px; margin-bottom: 20px;" rows="4" required maxlength="250"><?php echo $CurrentAlbumDescription; ?></textarea>
                        
                        <br>
                        <button type="submit" style="font-family: 'Comic Neue'; font-weight: bold; font-size: 25px; padding: 5px 15px; border-radius: 10px; cursor: pointer;">
                            Save Album
                        </button>
                    </form>
                </div>
        <?php
                }
            } else { 
        ?>
            <!-- ERROR FALLBACK ❌ -->
            <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Error</h2>
            <div class="ShadowedBox" style="margin-left: 20%; margin-right: 20%; width: 60%; border-radius: 20px; padding: 3%; padding-left: 2%;">
                <p style="font-family: Arial; font-size: 50px; text-align: center;">OOPS, INVALID REQUEST.</p>
                <p style="font-family: 'Comic Neue'; font-size: 30px;">Missing data or unknown method.</p>
            </div>
        <?php 
            } 
        } 
        ?>
    </div>

   

    <br><br><br>
</body>
</html>