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
        <h2 style="font-family: 'Caveat Brush'; font-size: 48px; padding-left: 2%;">Your Profile</h2>
        <?php
        if ($CompleteData != "") {  
        ?>
        <br>
        <div class="ShadowedBox" style="margin-left: 5%; margin-right: 50%; width: 45%; border-radius: 20px; padding: 1%; padding-left: 2%;">
            <p style="font-family: 'Caveat Brush'; font-size: 35px;"><b>Name: </b><?php echo $CompleteData['Username']; ?></p>
            <p style="font-family: 'Comic Neue'; font-size: 25px;"><b>Full Name: </b><?php echo $CompleteData['NamaLengkap']; ?></p>
            <p style="font-family: 'Comic Neue'; font-size: 25px;"><b>Email: </b><?php echo $CompleteData['Email']; ?></p>
            <p style="font-family: 'Comic Neue'; font-size: 25px;"><b>Address: </b><?php echo $CompleteData['Alamat']; ?></p>
            <p style="font-family: 'Comic Neue'; font-size: 25px;"><b>Date Joined: </b><?php echo $CompleteData['DateJoined']; ?></p>
        </div>
        <br><br>
        <div class="ShadowedBox" style="margin-left: 5%; margin-right: 50%; width: 45%; border-radius: 20px; padding: 1%; padding-left: 2%;">
            <p style="font-family: 'Caveat Brush'; font-size: 30px;">Special Setting:</p>
            <br>
            <button style="font-size: 25px;"><a href="?Request=LogOut" class="A-Clean" style="font-family: 'Comic Neue';"><p><b>Log Out</b></p></a></button>
        </div>
        </div>
        <br><br>
        <?php
        } else {
        ?>
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