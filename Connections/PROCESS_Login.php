<?php
require "Connect.php";

if (!isset($_POST['Method']) or !isset($_POST['Username']) or !isset($_POST['Password'])) {
    echo "Invalid data [1]";
    exit;
}

if ($_POST['Method'] == "SignIn") {
    $Username = $_POST['Username'];
    $PasswordPlain = $_POST['Password'];

    $Statement = $ServerConnection->prepare("SELECT UserId, Password FROM user WHERE Username = ?");
    $Statement->bind_param("s", $Username);
    $Statement->execute();
    $Result = $Statement->get_result();

    if ($Row = $Result->fetch_assoc()) {
        if (password_verify($PasswordPlain, $Row['Password'])) {
            $_SESSION['UserId'] = $Row['UserId'];
            $_SESSION['LoggedIn'] = true;
            header("Location: ../index.php");
            exit;
        } else {
            echo "Invalid password. ❌";
        }
    } else {
        echo "User not found. ❌";
    }
}
else if ($_POST['Method'] == "SignUp") {
    // 🛠️ Fix 1: Changed && to || 
    if (!isset($_POST['Email']) || !isset($_POST['FullName']) || !isset($_POST['Address'])) {
        echo "Invalid Data ❌";
        exit;
    }
    
    $Username = $_POST['Username']; 
    $Email = $_POST['Email'];
    $NamaLengkap = $_POST['FullName'];
    $Alamat = $_POST['Address'];
    $PasswordPlain = $_POST['Password'];

    $PasswordHash = password_hash($PasswordPlain, PASSWORD_DEFAULT);

    $Statement = $ServerConnection->prepare("INSERT INTO user (Username, Email, NamaLengkap, Alamat, Password) VALUES (?, ?, ?, ?, ?)");
    $Statement->bind_param("sssss", $Username, $Email, $NamaLengkap, $Alamat, $PasswordHash);
    try {
        if ($Statement->execute()) {
            header("Location: ../index.php");
            exit; 
        }
    } catch (Exception $Error) {
        echo "It seems the username has been reserved. Use another username.";
    exit;
}
}
?>