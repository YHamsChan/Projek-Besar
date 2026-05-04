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
    
    <div>
        <?php
        if ($CompleteData != "") {  
        ?>
        <br>
        <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Upload A Photo</h2>
        <div class="ShadowedBox" style="margin-left: 10%; margin-right: 40%; width: 50%; border-radius: 20px; padding: 1%; padding-left: 2%;">
            <form Action="Connections/Process_UploadImage.php" Method="Post" Enctype="Multipart/Form-Data">
                <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Your Photo Title</p>
                <Input name="PhotoTitle" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 3px;" Type="text" placeholder="Limit is 25 character" Required maxlength="25" ><br>
                <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Description</p>
                <Input name="PhotoDescription" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 3px;" Type="text" placeholder="Limit is 120 character" Required maxlength="120" ><br><br>
                <label for="Picture" style="font-size: 30px; font-family: 'Comic Neue'; font-weight: bold;">Upload Your File: </label>
                <p style="font-family: Monospace; font-size: 14px; width: 100%;">The file size limit is 4mb!</p>
                <Input style="font-size: 20px; font-family: 'Comic Neue'; font-weight: bold;" Type="File" Name="PhotoFile" Id="Picture" Accept=".jpg, .jpeg, .png" Required><br><br>
                <label for="Album" style="font-size: 30px; font-family: 'Comic Neue'; font-weight: bold;">Choose Your Album: </label>
                <?php
                $RawAlbumList = mysqli_query($ServerConnection, "SELECT * FROM album WHERE UserId=".$CompleteData["UserId"]);
                if (mysqli_num_rows($RawAlbumList) > 0) {
                ?>
                <select name="AlbumId" id="AlbumId" style="font-size: 25px;">
                    <?php
                    while ($Row = mysqli_fetch_assoc($RawAlbumList)) {
                    ?>
                    <option value="<?php echo $Row["AlbumId"];?>"><?php echo $Row["NamaAlbum"];?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <?php
                }
                else {
                ?>
                <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">There is no album :&#40;</p>
                <?php
                }
                ?>
            <Button Type="Submit" Name="Submit" style="font-size: 25px;">Upload</Button>
            </form>
        </div>
        <br><br>

        <?php
        // CRITICAL: We use LEFT JOIN so photos without albums or likes do not disappear.
        $PhotoQuery = "
            SELECT 
                foto.FotoId, 
                foto.JudulFoto, 
                foto.DeskripsiFoto, 
                foto.LokasiFile, 
                foto.TanggalUnggah, 
                album.NamaAlbum, 
                COUNT(likefoto.UserId) AS TotalLikes
            FROM foto 
            LEFT JOIN album ON foto.AlbumId = album.AlbumId 
            LEFT JOIN likefoto ON foto.FotoId = likefoto.FotoId 
            WHERE foto.UserId = ? 
            GROUP BY foto.FotoId
        ";

        $DatabaseStatement = mysqli_prepare($ServerConnection, $PhotoQuery);

        if ($DatabaseStatement) {
            mysqli_stmt_bind_param($DatabaseStatement, "i", $CompleteData["UserId"]);
            mysqli_stmt_execute($DatabaseStatement);
            $ResultRawArray = mysqli_stmt_get_result($DatabaseStatement);

            if (mysqli_num_rows($ResultRawArray) > 0) {
                while ($PhotoRow = mysqli_fetch_assoc($ResultRawArray)) {
                    
                    // Sanitize text outputs to prevent XSS attacks
                    $FotoId = (int)htmlspecialchars($PhotoRow["FotoId"], ENT_QUOTES, 'UTF-8');
                    $SafeTitle = htmlspecialchars($PhotoRow["JudulFoto"], ENT_QUOTES, 'UTF-8');
                    $SafeDescription = htmlspecialchars($PhotoRow["DeskripsiFoto"], ENT_QUOTES, 'UTF-8');
                    $ImagePath = htmlspecialchars($PhotoRow["LokasiFile"], ENT_QUOTES, 'UTF-8');
                    $UploadDate = htmlspecialchars($PhotoRow["TanggalUnggah"], ENT_QUOTES, 'UTF-8');
                    
                    // Fallback in case AlbumId is NULL
                    $SafeAlbumName = !empty($PhotoRow["NamaAlbum"]) ? htmlspecialchars($PhotoRow["NamaAlbum"], ENT_QUOTES, 'UTF-8') : "No Album";
                    $TotalLikes = (int)$PhotoRow["TotalLikes"];
            ?>
                <br>
                <div class="ShadowedBox" style="margin-left: 10%; margin-right: 10%; width: 80%; border-radius: 20px; padding: 1%; padding-left: 2%;">
                    <!-- Added Album Name -->
                    <p style="font-family: Arial; font-size: 20px; color: gray; font-weight: bold;">📁 Album: <?php echo $SafeAlbumName; ?> [ID: <?php echo $FotoId; ?>]</p>
                    <p style="font-family: Arial; font-size: 35px; font-weight: bold;"><?php echo $SafeTitle; ?></p>
                    
                    <img src="Connections/<?php echo $ImagePath; ?>" alt="<?php echo $SafeTitle; ?>" style="max-width: 400px; height: auto; border-radius: 10px; margin-top: 10px; margin-bottom: 10px;">
                    
                    <!-- Added Total Likes -->
                    <p style="font-family: Arial; font-size: 25px; font-weight: bold;">❤️Likes: <?php echo $TotalLikes; ?></p>
                    
                    <p style="font-family: 'Comic Neue'; font-size: 25px;"><?php echo $SafeDescription; ?></p>
                    <p style="font-family: Monospace; font-size: 20px; color: #00a37dff;">Date Uploaded: <?php echo $UploadDate; ?></p>
                </div>
            <?php
                }
            } else {
                ?>
                <br><br>
                <div class="ShadowedBox" style="margin-left: 10%; margin-right: 10%; width: 80%; border-radius: 20px; padding: 3%; padding-left: 2%;">
                <br>
                <p style="font-family: 'Comic Neue'; font-size: 30px; text-align: center;">Currently you don't have any photo :&#40;</p>
                </div>
                <?php
            }
            mysqli_stmt_close($DatabaseStatement);
        } else {
            echo "<p>Error: Could not load the gallery data.</p>";
        }

        } else {
        ?>
        <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Your Gallery</h2>
        <div class="ShadowedBox" style="margin-left: 20%; margin-right: 20%; width: 60%; border-radius: 20px; padding: 3%; padding-left: 2%;">
            <br>
            <p style="font-family: Arial; font-size: 50px; text-align: center;">OOPS, ACCESS DENIED.</p>
            <br><br><br>
            <p style="font-family: 'Comic Neue'; font-size: 30px;">Please log-in or sign-in through the home page to access this page.</p>
        </div>
        <?php
        }   
        ?>
    </div>

    <br><br><br>

    <footer>
       <p style="text-align: center;">&copy; SMK Prestasi Prima</p>
       <p style="text-align: center;">&copy; Nayomi Bataritoja Laksmono</p>
       <p>Contact: +62851-3838-1902</p>
    </footer>
</body>
</html>