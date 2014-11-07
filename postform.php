<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        You entered: <?php 
        echo $_POST["firstname"] . " " . $_POST["lastname"] . "</br>";
        echo $_POST["streetnum"] . " " . $_POST["streetname"] . " " . $_POST["suffix"] . "</br>";
        echo $_POST["city"] . ", " . $_POST["state"] . "  " . $_POST["zip"];
 
 ?>
    </body>
</html>
