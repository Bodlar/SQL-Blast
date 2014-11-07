<?php
function randLetter(){
    return chr(97 + mt_rand(0, 25));
}
function randemail(){
    $email = array("");
    $emailrand1 = rand(4,12);
    $emailrand2 = rand(6,12);
    for ($i=0; $i<=$emailrand1; $i++){
        $randLet = randLetter();
        array_push($email, $randLet);
    }
    array_push($email, '@');
    for ($i=0; $i<=$emailrand2; $i++){
        $randLet = randLetter();
        array_push($email, $randLet);
    }
    array_push($email, '.com');
    $emailFinal = implode($email);
    return $emailFinal;
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
            $email = randemail();
            echo $email;
        ?>
    </body>
</html>
