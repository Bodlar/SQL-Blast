<?php
include 'getstuff.php';
include 'hash.php';
$userName = $password = $password2 =
$userErr = $passErr = $passErr2 = $matchErr = "";
$posted = 99;

if(isset($_POST['submit'])){
    if (empty($_POST["username"])){
        $userErr= "Username is required!";
    } else {
        $userName = ($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9]*$/",$userName)){
            $userErr = "Only letters and numbers allowed!";
        } else {
            $con = mysqli_connect($server, $uname , $ulogin, $dbtarget);
                if(mysqli_connect_errno()){
                    echo "Failed to connecto to MySQL: " . mysqli_connect_error();
                }
                $checkstring = "SELECT * FROM users WHERE '" .  $userName . "' = username";
                $check = mysqli_query($con, $checkstring);
                $check2 = mysqli_num_rows($check);
                if ($check2 != 0){
                    $userErr = "Sorry that username is already in use!";
                }
            }
    }
    if (empty($_POST["password"])){
        $passErr= "Password is required!";
    } else {
        $password = ($_POST["password"]);
        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,50}$/', $password)) {
            $passErr = "The password does not meet the requirements!";
        }
    }
    if (empty($_POST["password2"])){
        $passErr2= "Password confirmation is required!";
    } else {
        $password2 = ($_POST["password2"]);
        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,50}$/', $password2)) {
            $passErr2 = "The password does not meet the requirements!";
        }
    }
    if ($password != $password2){
        $matchErr = 'Passwords do not match!';
    }
    if ($userErr == "" & $passErr == "" & $passErr2 == "" & $matchErr == ""){
        $posted = 0;
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
        Please enter the following to create a new account:
       <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
           Username: <input type="text" name="username" value="<?php echo $userName;?>">
           <span class="error"> <?php echo $userErr;?></span><br>
           Password: <input type="password" name="password" value="<?php echo $password;?>">
           <span class="error"> <?php echo $passErr; echo ' ' . $matchErr?></span><br>
           Confirm Password: <input type="password" name="password2" value="<?php echo $password2?>">
           <span class="error"> <?php echo $passErr2; echo ' ' . $matchErr?></span><br>
           <input type="submit" name="submit" value="Register"><br>
        </form>
        The password for this system is stored in the database with a randomized salt created with my_crypt_iv. 
        <?php
            if ($posted == 0){
                $con = mysqli_connect($server, $uname , $ulogin, $dbtarget);
                if(mysqli_connect_errno()){
                    echo "Failed to connecto to MySQL: " . mysqli_connect_error();
                }
                $hashedpass = create_hash($password);
                $parts = explode(":", $hashedpass);
                $pw1 = $parts[HASH_SALT_INDEX];
                $pw2 = $parts[HASH_PBKDF2_INDEX];
                $sql = "INSERT INTO users (username, pw1, pw2)
                    VALUES ('$userName', '$pw1', '$pw2')";
                if (!mysqli_query($con,$sql)) {
                    die('Error: ' . mysqli_error($con));
                }
                header('Location: sqlblast.php');
            }  
           
        ?>
    </body>
</html>
