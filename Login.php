<?php
require "Connections/Connect.php";

if ($_SESSION['LoggedIn'] == true) {
    header("Location: index.php");
}

$TopImagesData = [
    "Judul" => ""
];
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
            <div><a href="" class="Navigation">My Profile</a></div>
        </div>
    </header>

    <div>
        <?php
           if  (isset($_GET['Request']) && $_GET['Request'] == "SignIn") {
           ?>
        <div class="ImageBanner" style="width: 100vw; height: 30vh; background-image: url('https://i.pinimg.com/736x/2e/40/dc/2e40dc63dacbb648982ddc2589a76356.jpg');">
                <h1 style="text-align:center; font-family: 'Caveat'; color: white; text-shadow: 5px 5px 10px black; font-size: 86px; padding-top: 5%;">
                Welcome Back to Snappy Cloud!
            </h1>
        </div>
        <?php } ?>
        <div class="FormLogin">
           <?php
           if  (isset($_GET['Request']) && $_GET['Request'] == "SignIn") {
           ?>
             <form action="Connections/PROCESS_Login.php" method="Post">
                <br><br>
                <h2 style="font-size: 36px; font-family: 'Comic Neue';">Log In to An Account</h2>
                <input type="hidden" name="Method" id="Method" value="SignIn">
                <p class="Font-ComicBold" style="font-size: x-large;">Username:</p>
                <input class="Input1" type="text" name="Username" id="Username" placeholder="Username" required>
                <p class="Font-ComicBold" style="font-size: x-large;">Password:</p>
                <input class="Input1" type="password" name="Password" id="Password" placeholder="Your Account's Secret Code!" required>

                <br><br>
                <button type="submit" style="width: 40%; margin-left: 30%; margin-right: 30%; font-size: x-large;">Continue</button>
                </form>
                <br>
                <a href="?Request=SignUp" style="text-align: center; font-family: 'Comic Neue';">
                    <p>Create a new account.</p></a>
                <br><br><br><br>
           <?php
           } else {
           ?>
            <form action="Connections/PROCESS_Login.php" method="Post">
                <h2 style="font-size: 36px; font-family: 'Comic Neue';">Make a New Account</h2>
                <input type="hidden" name="Method" id="Method" value="SignUp">
                <p class="Font-ComicBold" style="font-size: x-large;">Username:</p>
                <input class="Input1" type="text" name="Username" id="Username" placeholder="Username" maxlength="20" required>
                <p class="Font-ComicBold" style="font-size: x-large;">Full Name:</p>
                <input class="Input1" type="text" name="FullName" id="FullName" placeholder="Your Full Name" required maxlength="220">
                <p class="Font-ComicBold" style="font-size: x-large;">Email:</p>
                <input class="Input1" type="emaul" name="Email" id="Email" placeholder="Your Email" required>
                <p class="Font-ComicBold" style="font-size: x-large;">Address:</p>
                <input class="Input1" type="text" name="Address" id="Address" placeholder="City, Country. Example: Tokyo, Japan." required maxlength="200">
                <p class="Font-ComicBold" style="font-size: x-large;">Password:</p>
                <input class="Input1" type="password" name="Password" id="Password" placeholder="Your Account's Secret Code!" required maxlength="86">

                <br><br>
                <div style="margin-left: 10%; margin-right: 10%; width: 80%; text-align: center; font-family: Arial;">
                    <input type="checkbox" required> I agree with the <a href="https://www.google.com">Terms Of Use.</a>
                </div>

                <br>
                <button type="submit" style="width: 40%; margin-left: 30%; margin-right: 30%; font-size: x-large;">Continue</button>
            </form>
            <br>
            <a href="?Request=SignIn" style="text-align: center; font-family: 'Comic Neue';">
                    <p>Login to an account.</p></a>
           <?php
           }
           ?>
        </div>
    </div>

    <footer>
       <p style="text-align: center;">&copy; SMK Prestasi Prima</p>
       <p style="text-align: center;">&copy; Nayomi Bataritoja Laksmono</p>
       <p>Contact: +62851-3838-1902</p>
    </footer>
</body>
</html>