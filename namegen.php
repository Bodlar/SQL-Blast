<?php
function randLetter(){
    return chr(97 + mt_rand(0, 25));
}
function getName($nameLen){
    $name = array("");
    for ($i=0; $i<=$nameLen; $i++){
        $randLet = randLetter();
        if ($i==0){
            $randLet = strtoupper($randLet);
        }
        array_push($name, $randLet);
    }
    $nameFinal = implode($name);
    return $nameFinal;
}
function suffix($rand){
    switch ($rand){
        case 0:
        $suffix = "Dr.";
        break;
        case 1:
        $suffix = "Ave.";
        break;
        case 2:
        $suffix = "St.";
        break;
        case 3:
        $suffix = "Pkwy.";
        break;
        case 4:
        $suffix = "Ct.";
        break;
        case 5:
        $suffix = "Blvd.";
        break;
        default:
        echo "Invalid random street number";
    }
    return $suffix;
}
function state($rand){
    switch ($rand){
        case 0:
        $state = "NV";
        break;
        case 1:
        $state = "WA";
        break;
        case 2:
        $state = "NY";
        break;
        case 3:
        $state = "CA";
        break;
        case 4:
        $state = "UT";
        break;
        case 5:
        $state = "CO";
        break;
        case 6:
        $state = "VA";
        break;
        case 7:
        $state = "FL";
        break;
        default:
        echo "Invalid random street number";
    }
    return $state;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        <?php
        for ($s=0; $s<=10; $s++){
        $nameLenF = rand(4,10);
        $nameLenL = rand(7,12);
        $firstName = getname($nameLenF);
        $lastName = getname($nameLenL);
        $addressNum = rand(100,29999);
        $nameLenS = rand(8,15);
        $streetName = getname($nameLenS);
        $randSuffix = rand(0,5);
        $streetSuffix = suffix($randSuffix);
        $nameLenC = rand(7,14);
        $cityName = getname($nameLenC);
        $staterand = rand(0,6);
        $stateName = state($staterand);
        $zipcode = rand(10001,99999);
        echo $firstName . " " . $lastName . " - " . $addressNum . " " . $streetName . " " . $streetSuffix . " - " . $cityName . ", " . $stateName . "  " . $zipcode;
        echo "</br>";
        }
        ?>
    </body>
</html>
