<?php
$firstName = $lastName = $addressNum = $streetName = $streetSuffix = $cityName = $stateName = $zipcode = $posted = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $firstName = test_input($_POST["firstname"]);
   $lastName = test_input($_POST["lastname"]);
   $addressNum = test_input($_POST["addressnum"]);
   $streetName = test_input($_POST["streetname"]);
   $streetSuffix = test_input($_POST["suffix"]);
   $cityName = test_input($_POST["city"]);
   $stateName = test_input($_POST["state"]);
   $zipcode = test_input($_POST["zip"]);
   $posted = TRUE;
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Add a row to the database: <br>
            First Name: <input type="text" name="firstname">
            Last Name: <input type="text" name="lastname"><br>
            Street Number: <input type="text" name="addressnum">
            Street Name: <input type="text" name="streetname">
            Street Suffix: <input type="text" name="suffix"><br>
            City: <input type="text" name="city">
            State: <input type="text" name="state">
            Zip: <input type="text" name="zip"><br>
            <input type="submit">
        </form>
        <?php
            if ($posted == true){
             echo "One row added:" . "<br>";
             echo $firstName . " " . $lastName . " ";
             echo $addressNum . " " . $streetName . " " . $streetSuffix , " ";
             echo $cityName . ", " . $stateName . "  " . $zipcode;

            }
        ?>
    </body>
</html>
