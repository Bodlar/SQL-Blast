<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <?php
           $con = mysqli_connect("localhost", "root", "SQLyay", "myDB");
           if(mysqli_connect_errno()){
               echo "Failed to connecto to MySQL: " . mysqli_connect_error();
           } 
           mysqli_close($con);
        ?>
    </body>
</html>
