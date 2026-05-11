<?php
require "Connections/Connect.php";

$TopImagesData = [
    "Judul" => ""
];

$Username = "";

$Statement = $ServerConnection->prepare("SELECT UserId, Username FROM User WHERE UserId = ".$_SESSION['UserId']);
$Statement->execute();
$Result = $Statement->get_result();

if ($Row = $Result->fetch_assoc()) {
    $Username = $Row['Username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnappyCloud | Home</title>
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
    
    <div class="ImageBanner" style="width: 100vw; height: 60vh; background-image: url('https://i.pinimg.com/736x/2e/40/dc/2e40dc63dacbb648982ddc2589a76356.jpg');">
        <h1 style="text-align:center; font-family: 'Caveat'; color: white; text-shadow: 5px 5px 10px black; font-size: 86px; padding-top: 10%;">
            Welcome to Snappy Cloud
        </h1>
        <p style="text-align:center; font-family: 'Caveat Brush'; color: white; text-shadow: 5px 5px 10px black; font-size: 36px;">
            <?php
            if ($Username != ""){
                echo $Username;
            } else {
                echo "You're a guest here.";
            }
            ?>
        </p>
    </div>
    
    <div class="ShadowedBox">
        <h2 class="H2-Special1">Search Image By Id</h2>
        <div class="SearchBarHome" width="90vw">
            <form action="SearchImage.php" Method="Get">
                <Input name="PhotoId" style="font-size: 25px; font-family: 'Comic Neue'; font-weight: bold; width: 90%; border-radius: 15px; padding: 3px;" Type="number">
                <br><br>
            <button Type="Submit" Name="Submit" style="font-size: 25px;">Upload</button>
            </form>
            <br><br>
        </div>
    </div>

    <div>
        <br><br>
        <h2 class="H2-Special2">Showing Our Community</h2>
        <div style="display: flex;" width="90vw">
            <div class="LandingTextFrame1 SoftShadowedBox" style="width: 30%; margin-left: 5px; margin-right: 5px;">
                <div class="TopImageTitle" style="font-size: 25px; color: white;">Starting With Over</div>
                <?php
                $sql = "SELECT COUNT(*) FROM foto";
                $result = mysqli_query($ServerConnection, $sql);
                $Row = mysqli_fetch_array($result);
                ?>
                <div class="TopImageTitle" style="font-size: 48px; color: white;"><?php echo $Row[0];?>+ Photos</div>
                <div>
                    <img src="Sources\Images\ImagesIcon.png" alt="" style="width: 20%; margin-left: 40%; margin-right: 40%;">
                </div>
                <div class="TopImageTitle" style="font-size: 20px; color: white;">Being Published</div>
            </div>

            <div class="LandingTextFrame1 SoftShadowedBox" style="width: 30%; margin-left: 5px; margin-right: 5px;">
                <div class="TopImageTitle" style="font-size: 25px; color: white;">And Over</div>
                <?php
                $sql = "SELECT COUNT(*) FROM user";
                $result = mysqli_query($ServerConnection, $sql);
                $Row = mysqli_fetch_array($result);
                ?>
                <div class="TopImageTitle" style="font-size: 48px; color: white;"><?php echo $Row[0];?>+ Members</div>
                <div>
                    <img src="Sources\Images\UsersIcon.png" alt="" style="width: 20%; margin-left: 40%; margin-right: 40%;">
                </div>
                <div class="TopImageTitle" style="font-size: 20px; color: white;">Joined Us</div>
            </div>

            <div class="LandingTextFrame1 SoftShadowedBox" style="width: 30%; margin-left: 5px; margin-right: 5px;">
                <div class="TopImageTitle" style="font-size: 25px; color: white;">Along With</div>
                <?php
                $sql = "SELECT COUNT(*) FROM album";
                $result = mysqli_query($ServerConnection, $sql);
                $Row = mysqli_fetch_array($result);
                ?>
                <div class="TopImageTitle" style="font-size: 48px; color: white;"><?php echo $Row[0];?>+ Albums</div>
                <div>
                    <img src="Sources\Images\FolderCameraIcon.png" alt="" style="width: 20%; margin-left: 40%; margin-right: 40%;">
                </div>
                <div class="TopImageTitle" style="font-size: 20px; color: white;">Ever Made</div>
            </div>
        </div>
        <br><br>
    </div>

    <?php
    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] == true) {
    ?>
    <div>
        <h2 class="H2-Special2">Yeay, you are a member already! &lt;3</h2>
        <p style="text-align: center; font-size: 30px;" class="Font-ComicBold">You can sign out through your profile.</p>
        <p style="text-align: center; font-size: 22px;" class="Font-ComicBold"><a href="MyProfile.php">Click Here.</a></p>
        <br><br>
    </div>
    <?php } else {?>

    <div>
        <h2 class="H2-Special2">What are you waiting for?</h2>
        <button style="font-size: 36px; width: 40%; margin-left: 30%; margin-right: 30%;" class="Font-ComicBold">
            <a class="A-Clean" href="Login.php?Request=SignUp">Join SnappyCloud Now</a>
        </button>
        <br>
        <p style="text-align: center; font-size: 30px;" class="Font-ComicBold">for free!</p>
        <p style="text-align: center; font-size: 22px;" class="Font-ComicBold"><a href="Login.php?Request=SignIn">Login to an existing account.</a></p>
        <br><br>
    </div>

    <?php }?>

    <footer>
       <p style="text-align: center;">&copy; SMK Prestasi Prima</p>
       <p style="text-align: center;">&copy; Nayomi Bataritoja Laksmono</p>
       <p>Contact: +62851-3838-1902</p>
    </footer>
</body>
</html>