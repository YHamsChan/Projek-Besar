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

if (isset($_GET["Request"]) && $_GET["Request"] == "LogOut") {
    session_destroy();
    header("Location: index.php");
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
        <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Create An Album</h2>
        <div class="ShadowedBox" style="margin-left: 10%; margin-right: 40%; width: 50%; border-radius: 20px; padding: 1%; padding-left: 2%;">
            <form Action="Connections/Process_CreateAlbum.php" Method="Post">
                <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Your Album's Name:</p>
                <Input name="AlbumName" id="AlbumName" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 3px;" Type="text" placeholder="Limit is 25 character" Required maxlength="25" ><br><br>
                <p style="font-family: 'Comic Neue'; font-size: 25px; font-weight: bold;">Your Album's Description:</p>
                <Input name="AlbumDescription" id="AlbumDescription" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 3px;" Type="text" placeholder="Limit is 250 character" Required maxlength="250" ><br><br>
                
            <Button Type="Submit" Name="Submit" style="font-size: 25px;">Upload</Button>
            </form>
        </div>

        <?php
        $ResultRawArray = mysqli_query($ServerConnection, "SELECT * FROM album WHERE UserId=".$CompleteData["UserId"]);
        if (mysqli_num_rows($ResultRawArray) > 0) {
            while ($Row = mysqli_fetch_assoc($ResultRawArray)) {
                $ResultRawPhotos = mysqli_query($ServerConnection, "SELECT * FROM foto WHERE AlbumId=".$Row["AlbumId"]);
                $TotalPhotos = mysqli_num_rows($ResultRawPhotos);
        ?>
            <br>
            <div class="ShadowedBox" style="margin-left: 10%; margin-right: 10%; width: 80%; border-radius: 20px; padding: 1%; padding-left: 2%;">
                <p style="font-family: Arial; font-size: 35px; font-weight: bold;"><?php echo $Row['NamaAlbum'];?></p>
                <p style="font-family: Arial; font-size: 30px; font-weight: bold;">📸<?php echo $TotalPhotos;?></p>
                <p style="font-family: Arial; font-size: 25px;"><?php echo $Row['Deskripsi'];?></p>
                <div style="display: flex; align-items: center;">
                        <form action="EditUploadedFile.php" method="POST">
                            <input type="hidden" name="Method" value="Album">
                            <input type="hidden" name="AlbumId" value="<?php echo $Row["AlbumId"]; ?>">
                            <input type="hidden" name="AlbumName" value="<?php echo $Row['NamaAlbum']; ?>">
                            <input type="hidden" name="AlbumDescription" value="<?php echo $Row['Deskripsi']; ?>">
                            <button type="submit" style="font-size: 20px;">Edit</button></form>
                <p style="font-family: Monospace; font-size: 20px; color: #00a37dff; padding-left: 10px;">Date Created: <?php echo $Row['TanggalDibuat'];?></p>
                </div>
            </div>
        <?php
            }
        }
        else {
            ?>
            <br><br>
            <div class="ShadowedBox" style="margin-left: 10%; margin-right: 10%; width: 80%; border-radius: 20px; padding: 3%; padding-left: 2%;">
            <br>
            <p style="font-family: 'Comic Neue'; font-size: 30px;text-align: center;">Currently you don't have any album :&#40;</p>
            </div>
            <?php
        }
        ?>
        <br><br>
        <?php
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