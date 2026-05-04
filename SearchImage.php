<?php
require "Connections/Connect.php";

$TopImagesData = [
    "Judul" => ""
];

$CompleteData = "";

if (isset($_SESSION['UserId']) && $_SESSION['UserId'] != 0) {
    $Statement = $ServerConnection->prepare("SELECT * FROM User WHERE UserId = ".$_SESSION['UserId']);
    $Statement->execute();
    $Result = $Statement->get_result();

    if ($Row = $Result->fetch_assoc()) {
        $CompleteData = $Row;
    }
    $CompleteData['Password'] = "NONE";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnappyCloud | My Profile</title>
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
    
<?php

$CurrentUserId = isset($_SESSION["UserId"]) ? (int)$_SESSION["UserId"] : 0;
$IsLoggedIn = ($CurrentUserId > 0);

// ==========================================
// 1. PROSES AKSI LIKE & COMMENT (POST)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["FotoId"])) {
    $TargetFotoId = (int)$_POST["FotoId"];

    if (!$IsLoggedIn) {
        die("Error: Access Denied. You must be logged in to interact. 🛑");
    }

    // A. PROSES LIKE
    if (isset($_POST["ActionLike"])) {
        $ActionType = $_POST["ActionLike"];
        
        $OwnershipQuery = "SELECT UserId FROM foto WHERE FotoId = ?";
        $OwnershipStatement = mysqli_prepare($ServerConnection, $OwnershipQuery);
        mysqli_stmt_bind_param($OwnershipStatement, "i", $TargetFotoId);
        mysqli_stmt_execute($OwnershipStatement);
        $OwnershipResult = mysqli_stmt_get_result($OwnershipStatement);
        $PhotoData = mysqli_fetch_assoc($OwnershipResult);

        if (!$PhotoData || $PhotoData["UserId"] === $CurrentUserId) {
            die("Error: You cannot like your own photo or photo doesn't exist. 🚫");
        }
        mysqli_stmt_close($OwnershipStatement);

        if ($ActionType === "Like") {
            $LikeQuery = "INSERT IGNORE INTO likefoto (UserId, FotoId) VALUES (?, ?)";
            $LikeStatement = mysqli_prepare($ServerConnection, $LikeQuery);
            mysqli_stmt_bind_param($LikeStatement, "ii", $CurrentUserId, $TargetFotoId);
            mysqli_stmt_execute($LikeStatement);
            mysqli_stmt_close($LikeStatement);
        } elseif ($ActionType === "UnLike") {
            $UnlikeQuery = "DELETE FROM likefoto WHERE UserId = ? AND FotoId = ?";
            $UnlikeStatement = mysqli_prepare($ServerConnection, $UnlikeQuery);
            mysqli_stmt_bind_param($UnlikeStatement, "ii", $CurrentUserId, $TargetFotoId);
            mysqli_stmt_execute($UnlikeStatement);
            mysqli_stmt_close($UnlikeStatement);
        }
    }

    // B. PROSES KOMENTAR
    if (isset($_POST["ActionComment"]) && isset($_POST["CommentText"])) {
        $CommentText = trim($_POST["CommentText"]);
        
        // Validasi agar tidak mengirim komentar kosong
        if (!empty($CommentText)) {
            // FIXED: Pastikan nama kolom (PhotoId vs FotoId) sesuai dengan database Anda.
            $InsertCommentQuery = "INSERT INTO komentar (UserId, PhotoId, Comment) VALUES (?, ?, ?)";
            $InsertCommentStatement = mysqli_prepare($ServerConnection, $InsertCommentQuery);
            
            if ($InsertCommentStatement) {
                mysqli_stmt_bind_param($InsertCommentStatement, "iis", $CurrentUserId, $TargetFotoId, $CommentText);
                mysqli_stmt_execute($InsertCommentStatement);
                mysqli_stmt_close($InsertCommentStatement);
            } else {
                die("Error: Failed to prepare comment statement. 🛠️");
            }
        }
    }

    // ==========================================
    // CRITICAL FIX: THE PRG REDIRECT
    // Memaksa browser memuat ulang halaman secara bersih untuk mencegah duplikasi
    // ==========================================
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit(); 
}

// ==========================================
// 2. TAMPILAN SINGLE PHOTO & KOMENTAR (GET)
// ==========================================
$DisplayFotoId = isset($_GET["PhotoId"]) ? (int)$_GET["PhotoId"] : 1; 

$PhotoQuery = "
    SELECT 
        foto.FotoId, 
        foto.UserId AS OwnerId,
        foto.JudulFoto, 
        foto.DeskripsiFoto, 
        foto.LokasiFile, 
        foto.TanggalUnggah, 
        album.NamaAlbum, 
        COUNT(likefoto.UserId) AS TotalLikes,
        MAX(CASE WHEN likefoto.UserId = ? THEN 1 ELSE 0 END) AS HasCurrentUserLiked
    FROM foto 
    LEFT JOIN album ON foto.AlbumId = album.AlbumId 
    LEFT JOIN likefoto ON foto.FotoId = likefoto.FotoId 
    WHERE foto.FotoId = ? 
    GROUP BY foto.FotoId
";

$DatabaseStatement = mysqli_prepare($ServerConnection, $PhotoQuery);

if ($DatabaseStatement) {
    mysqli_stmt_bind_param($DatabaseStatement, "ii", $CurrentUserId, $DisplayFotoId);
    mysqli_stmt_execute($DatabaseStatement);
    $ResultRawArray = mysqli_stmt_get_result($DatabaseStatement);

    if ($PhotoRow = mysqli_fetch_assoc($ResultRawArray)) {
        
        $FotoId = (int)$PhotoRow["FotoId"];
        $OwnerId = (int)$PhotoRow["OwnerId"];
        $SafeTitle = htmlspecialchars($PhotoRow["JudulFoto"], ENT_QUOTES, 'UTF-8');
        $SafeDescription = htmlspecialchars($PhotoRow["DeskripsiFoto"], ENT_QUOTES, 'UTF-8');
        $ImagePath = htmlspecialchars($PhotoRow["LokasiFile"], ENT_QUOTES, 'UTF-8');
        $UploadDate = htmlspecialchars($PhotoRow["TanggalUnggah"], ENT_QUOTES, 'UTF-8');
        $SafeAlbumName = !empty($PhotoRow["NamaAlbum"]) ? htmlspecialchars($PhotoRow["NamaAlbum"], ENT_QUOTES, 'UTF-8') : "No Album";
        
        $TotalLikes = (int)$PhotoRow["TotalLikes"];
        $HasCurrentUserLiked = (int)$PhotoRow["HasCurrentUserLiked"] === 1;

        ?>
        <br>
        <div class="ShadowedBox" style="margin-left: 10%; margin-right: 10%; width: 80%; border-radius: 20px; padding: 1%; padding-left: 2%;">
            <p style="font-family: Arial; font-size: 20px; color: gray; font-weight: bold;">📁 Album: <?php echo $SafeAlbumName; ?> [ID: <?php echo $FotoId; ?>]</p>
            <p style="font-family: Arial; font-size: 35px; font-weight: bold;"><?php echo $SafeTitle; ?></p>
            
            <img src="Connections/<?php echo $ImagePath; ?>" alt="<?php echo $SafeTitle; ?>" style="max-width: 400px; height: auto; border-radius: 10px; margin-top: 10px; margin-bottom: 10px;">
            
            <div style="display: flex; align-items: center; gap: 15px;">
                <p style="font-family: Arial; font-size: 25px; font-weight: bold; margin: 0;">❤️ Likes: <?php echo $TotalLikes; ?></p>
                
                <?php 
                if ($IsLoggedIn && $CurrentUserId !== $OwnerId) { 
                    $ButtonAction = $HasCurrentUserLiked ? "UnLike" : "Like";
                    $ButtonText = $HasCurrentUserLiked ? "💔 UnLike" : "❤️ Like";
                ?>
                    <form method="POST" action="" style="margin: 0;">
                        <input type="hidden" name="FotoId" value="<?php echo $FotoId; ?>">
                        <input type="hidden" name="ActionLike" value="<?php echo $ButtonAction; ?>">
                        <button type="submit" style="font-family: 'Comic Neue'; font-size: 20px; font-weight: bold; padding: 5px 15px; border-radius: 10px; cursor: pointer; background-color: #41b6ffff; border: 3px solid #4e57ffff;">
                            <?php echo $ButtonText; ?>
                        </button>
                    </form>
                <?php } ?>
            </div>
            
            <p style="font-family: 'Comic Neue'; font-size: 25px; margin-top: 15px;"><?php echo $SafeDescription; ?></p>
            <p style="font-family: Monospace; font-size: 20px; color: #00a37dff;">Date Uploaded: <?php echo $UploadDate; ?></p>
            
            <hr style="border: 1px solid #ccc; margin: 20px 0;">

            <!-- ========================================== -->
            <!-- SEKSI KOMENTAR DITAMBAHKAN DI SINI         -->
            <!-- ========================================== -->
            <div class="CommentSection" style="margin-top: 20px;">
                <h3 style="font-family: 'Caveat Brush', sans-serif; font-size: 30px;">💬 Comments</h3>
                
                <?php if ($IsLoggedIn) { ?>
                    <!-- Form Input Komentar -->
                    <form method="POST" action="" style="margin-bottom: 20px;">
                        <input type="hidden" name="FotoId" value="<?php echo $FotoId; ?>">
                        <input type="hidden" name="ActionComment" value="Post">
                        <textarea name="CommentText" placeholder="Write a comment..." required style="width: 100%; max-width: 500px; height: 80px; padding: 10px; border-radius: 10px; font-family: 'Comic Neue'; font-size: 18px; border: 2px solid #ccc;"></textarea><br>
                        <button type="submit" style="font-family: 'Comic Neue'; font-size: 18px; font-weight: bold; padding: 5px 20px; margin-top: 10px; border-radius: 8px; cursor: pointer; background-color: #00a37dff; color: white; border: none;">Post Comment</button>
                    </form>
                <?php } else { ?>
                    <p style="font-family: 'Comic Neue'; font-size: 18px; color: gray;"><i>Log in to post a comment.</i></p>
                <?php } ?>

                <!-- List Komentar -->
                <div class="CommentList" style="margin-top: 15px;">
                    <?php
                    // CRITICAL: Gunakan JOIN ke tabel user untuk mendapatkan UserName
                    $FetchCommentsQuery = "
                        SELECT komentar.Comment, komentar.DatePosted, user.UserName 
                        FROM komentar 
                        JOIN user ON komentar.UserId = user.UserId 
                        WHERE komentar.PhotoId = ? 
                        ORDER BY komentar.DatePosted DESC
                    ";
                    $FetchCommentsStatement = mysqli_prepare($ServerConnection, $FetchCommentsQuery);
                    
                    if ($FetchCommentsStatement) {
                        mysqli_stmt_bind_param($FetchCommentsStatement, "i", $FotoId);
                        mysqli_stmt_execute($FetchCommentsStatement);
                        $CommentResults = mysqli_stmt_get_result($FetchCommentsStatement);

                        if (mysqli_num_rows($CommentResults) > 0) {
                            while ($CommentRow = mysqli_fetch_assoc($CommentResults)) {
                                $SafeComment = nl2br(htmlspecialchars($CommentRow["Comment"], ENT_QUOTES, 'UTF-8'));
                                $SafeUserName = htmlspecialchars($CommentRow["UserName"], ENT_QUOTES, 'UTF-8');
                                $CommentDate = htmlspecialchars($CommentRow["DatePosted"], ENT_QUOTES, 'UTF-8');
                                ?>
                                <div style="background-color: #f9f9f9; padding: 10px 15px; border-radius: 10px; margin-bottom: 10px; border: 1px solid #ddd; max-width: 600px;">
                                    <p style="font-family: Arial; font-size: 16px; font-weight: bold; margin: 0; color: #333;">👤 <?php echo $SafeUserName; ?> <span style="font-size: 12px; font-weight: normal; color: gray; margin-left: 10px;"><?php echo $CommentDate; ?></span></p>
                                    <p style="font-family: 'Comic Neue'; font-size: 18px; margin: 5px 0 0 0; color: #444;"><?php echo $SafeComment; ?></p>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p style=\"font-family: 'Comic Neue'; font-size: 18px;\">No comments yet. Be the first! 🌟</p>";
                        }
                        mysqli_stmt_close($FetchCommentsStatement);
                    }
                    ?>
                </div>
            </div>
            <!-- Akhir Seksi Komentar -->

        </div>
        <?php
    } else {
       ?>
       <br><br>
       <div class="ShadowedBox" style="margin-left: 20%; margin-right: 20%; width: 60%; border-radius: 20px; padding: 3%; padding-left: 2%;">
        <br>
        <p style="font-family: Arial; font-size: 50px; text-align: center;">OOPS, NOT FOUND.</p>
        <br><br><br>
        <p style="font-family: 'Comic Neue'; font-size: 30px;">Photo has been removed or not exist.</p>
        </div>
       <?php
    }
    mysqli_stmt_close($DatabaseStatement);
} else {
    ?>
    <br><br>
    <div class="ShadowedBox" style="margin-left: 20%; margin-right: 20%; width: 60%; border-radius: 20px; padding: 3%; padding-left: 2%;">
        <br>
        <p style="font-family: Arial; font-size: 50px; text-align: center;">OOPS, NOT FOUND.</p>
        <br><br><br>
        <p style="font-family: 'Comic Neue'; font-size: 30px;">Photo has been removed or not exist.</p>
    </div>
    <?php
}
?>
    <br><br><br>

    <footer>
       <p style="text-align: center;">&copy; SMK Prestasi Prima</p>
       <p style="text-align: center;">&copy; Nayomi Bataritoja Laksmono</p>
       <p>Contact: +62851-3838-1902</p>
    </footer>
</body>
</html>