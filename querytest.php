<?php
include 'getstuff.php';   
$password = 'joe12345'; 
$con = mysqli_connect($server, $uname , $ulogin, $dbtarget);
                if(mysqli_connect_errno()){
                    echo "Failed to connecto to MySQL: " . mysqli_connect_error();
                }
                $checkstring = "SELECT * FROM users WHERE " . "'joe'" .  " = username";
                $check = mysqli_query($con, $checkstring);
                $check2 = mysqli_num_rows($check);
                //echo $check2;
                while ($info = mysqli_fetch_array($check)){
                            if ($password == $info['password']){
                            $passcheck = $info['password'];
                            header('Location: sqlblast.php');
                            }
                    } 
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        
    </body>
</html>
