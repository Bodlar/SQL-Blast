<?php 
session_start();

include 'getstuff.php';
include 'hash.php';
$userName = $password = $userErr = $loginErr = $passErr = $sessionErr = "";
$posted = $usercheck= $passcheck = 99;
if(isset($_SESSION['notauth'])){
    $sessionErr = "Session expired! ";
          
}
if(isset($_POST['submit'])){
    if (empty($_POST["username"])){
        $userErr= "Username is required!";
    } else {
        $userName = ($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9]*$/",$userName)){
            $loginErr = "Invalid login!";
        } else{
            $usercheck = 0;
        }
    }
    if (empty($_POST["password"])){
        $passErr= "Password is required!";
    } else {
        $password = ($_POST["password"]);
        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,50}$/', $password)) {
            $loginErr = "Invalid login!";
        } else {
            $passcheck = 0;
        }
    }
} 
if ($usercheck == 0 & $passcheck == 0){
    $con = mysqli_connect($server, $uname , $ulogin, $dbtarget);
    if(mysqli_connect_errno()){
        echo "Failed to connecto to MySQL: " . mysqli_connect_error();
    }
    $checkstring = "SELECT * FROM users WHERE '" .  $userName . "' = username";
    $check = mysqli_query($con, $checkstring);
    $check2 = mysqli_num_rows($check);
    if ($check2 == 0){
        $loginErr = "Invalid login!";
    } else {
         while ($info = mysqli_fetch_array($check)){
            $totalpass = PBKDF2_HASH_ALGORITHM . ":" . PBKDF2_ITERATIONS . ":" .  $info['pw1'] . ":" . $info['pw2'];
            $validate = validate_password($password, $totalpass);
            if($validate == true){
                $_SESSION['auth'] = 1;
                $_SESSION['lastact'] = time();
                $_SESSION['created'] = time();
                unset($_SESSION['notauth']);
                header('Location: sqlblast.php');
            } else{
                $loginErr = "Invalid login!";
            }
         }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <style>
        .error {color: #FF0000;}
        </style>
    </head>
    <body>
        <span class="error"><?php echo $sessionErr;?></span>
        Please log in:
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Username: <input type="text" name="username" value="<?php echo $userName;?>">
            <span class="error"> <?php echo $userErr;?></span><br>
            Password: <input type="password" name="password" value="<?php echo $password;?>">
            <span class="error"> <?php echo $passErr;?></span><br>
            <input type="submit" name="submit" value="Log in">
            <span class="error"> <?php echo $loginErr;?></span><br>
        </form>
        Click <a href="registration.php">here</a> to register.
        <?php
            
        ?>
    </body>
</html>
