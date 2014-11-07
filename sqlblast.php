<?php
session_start();
if($_SESSION['auth'] != 1){
    $_SESSION['notauth'] = 1;
    header('Location: login.php');       
}
if (isset($_SESSION['lastact']) && (time() - $_SESSION['lastact'] > 600)) {
    // last request was more than 10 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    header('Location: login.php');
}
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // session started more than 30 minutes ago
    session_regenerate_id(true);    // change session ID for the current session an invalidate old session ID
    $_SESSION['created'] = time();  // update creation time
}
//includes the database information from another file for ease of switching this to another database
include 'getstuff.php';
//variable declaration
$nameErrF = $nameErrL = $emailErr = $websiteErr = $numErr = $streetErr = $suffixErr = $cityErr = $stateErr = $zipErr =
$firstName = $lastName = $email = $website = $addressNum = $streetName = $streetSuffix = $cityName = $stateName = $zipcode = $randomRows =  "";
//posted is used for conditionals associated with buttons on the form. This declares it to an unused value to prevent button conditionals from firing
$posted = 99;
//declare the statesAll variable to grab with $GLOBALS in multiple functions used later rather than re-declaring the array multiple times
$statesAll = array(
		array('AK', 'Alaska'), array('AL', 'Alabama'), array('AR', 'Arkansas'), array('AZ', 'Arizona'), array('CA', 'California'),
		array('CO', 'Colorado'), array('CT', 'Connecticut'), array('DC', 'District of Columbia'), array('DE', 'Delaware'),
		array('FL', 'Florida'), array('GA', 'Georgia'), array('HI', 'Hawaii'), array('IA', 'Iowa'), array('ID', 'Idaho'),
		array('IL', 'Illinois'), array('IN', 'Indiana'), array('KS', 'Kansas'), array('KY', 'Kentucky'), array('LA', 'Louisiana'),
		array('MA', 'Massachusetts'), array('MD', 'Maryland'), array('ME', 'Maine'), array('MI', 'Michigan'), array('MN', 'Minnesota'),
		array('MO', 'Missouri'), array('MS', 'Mississippi'), array('MT', 'Montana'), array('NC', 'North Carolina'), array('ND', 'North Dakota'),
		array('NE', 'Nebraska'), array('NH', 'New Hampshire'), array('NJ', 'New Jersey'), array('NM', 'New Mexico'), array('NV', 'Nevada'),
		array('NY', 'New York'), array('OH', 'Ohio'), array('OK', 'Oklahoma'), array('OR', 'Oregon'), array('PA', 'Pennsylvania'),
		array('PR', 'Puerto Rico'), array('RI', 'Rhode Island'), array('SC', 'South Carolina'), array('SD', 'South Dakota'),
        array('TN', 'Tennessee'), array('TX', 'Texas'), array('UT', 'Utah'), array('VA', 'Virginia'), array('VT', 'Vermont'),
		array('WA', 'Washington'), array('WI', 'Wisconsin'), array('WV', 'West Virginia'), array('WY', 'Wyoming')
	);
//conditional statements for different buttons along with form validation for each field for the add row button
if (isset($_POST['addrow'])){
    $_SESSION['lastact'] = time();
    if (empty($_POST["firstname"])){
        $nameErrF = "First Name is required!";
    } else {
        $firstName = test_input($_POST["firstname"]);
        if (!preg_match("/^[a-zA-Z]*$/",$firstName)){
            $nameErrF = "Only letters and whitespace allowed!";
        }
    }
    if (empty($_POST["lastname"])){
        $nameErrL = "Last Name is required!";
    } else {
        $lastName = test_input($_POST["lastname"]);
        if (!preg_match("/^[a-zA-Z]*$/",$lastName)){
            $nameErrL = "Only letters and whitespace allowed!";
        }
    }
    if (empty($_POST["email"])){
        $emailErr = "Email is required!";
    } else {
        $email = test_input($_POST["email"]);
        if (!preg_match("/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@
                    (?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/",$email)) {
            $emailErr = "Invalid email format";
        }
    }
    if (empty($_POST["website"])) {
        $websiteErr = "Website is required!";
    } else {
        $website = test_input($_POST["website"]);
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
            $websiteErr = "Invalid URL"; 
        }
    }
    if (empty($_POST["addressnum"])){
        $numErr = "Street Number is required!";
    } else {
        $addressNum = test_input($_POST["addressnum"]);
    }
    if (empty($_POST["streetname"])){
        $streetErr = "Street Name is required!";
    } else {
        $streetName = test_input($_POST["streetname"]);
        if (!preg_match("/^[a-zA-Z]*$/",$streetName)){
            $streetErr = "Only letters and whitespace allowed!";
        }
    }
    if (empty($_POST["suffix"])){
        $suffixErr = "Street Suffix is required!";
    } else {
        $streetSuffix = test_input($_POST["suffix"]);
        if (!preg_match("/^[a-zA-Z.]*$/",$streetSuffix)){
            $suffixErr = "Only letters and periods allowed!";
        }
    }
    if (empty($_POST["city"])){
        $cityErr = "City is required!";
    } else {
        $cityName = test_input($_POST["city"]);
        if (!preg_match("/^[a-zA-Z]*$/",$cityName)){
            $cityErr = "Only letters and whitespace allowed!";
        }
    }
    if (empty($_POST["state"])){
       $stateErr = "State is required!"; 
    } else {
        $stateName = ($_POST["state"]);
    }
    if (empty($_POST["zip"])){
        $zipErr = "Zip Code is required!";
    } else {
        $zipcode = test_input($_POST["zip"]);
        }
    $posted = 0;
}
//conditional statement for the button Add Random.  Validation for this field occurs as a conditional below to prevent database call from firing with invalid input.
if (isset($_POST['addrandomrow'])){
    $_SESSION['lastact'] = time();
    $randomRows = $_POST["randomrows"];
    $posted = 1;
}
//data cleaning
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
function StateDropdown($post=null, $type='abbrev') {
	$states = $GLOBALS['statesAll'];
	$options = '<option value=""></option>';
	foreach ($states as $state) {
		if ($type == 'abbrev') {
    	$options .= '<option value="'.$state[0].'" '. check_select($post, $state[0], false) .' >'.$state[0].'</option>'."\n";
    } elseif($type == 'name') {
    	$options .= '<option value="'.$state[1].'" '. check_select($post, $state[1], false) .' >'.$state[1].'</option>'."\n";
    } elseif($type == 'mixed') {
    	$options .= '<option value="'.$state[0].'" '. check_select($post, $state[0], false) .' >'.$state[1].'</option>'."\n";
    }
	}	
	echo $options;
}
//this function is used for the state selection above
function check_select($i,$m,$e=true) {
	if ($i != null) { 
		if ( $i == $m ) { 
			$var = ' selected="selected" '; 
		} else {
			$var = '';
		}
	} else {
		$var = '';	
	}
	if(!$e) {
		return $var;
	} else {
		echo $var;
	}
}
//this function creates random letters for the random data generation segments below
function randLetter(){
    return chr(97 + mt_rand(0, 25));
}
//random name generator that changes the firsr character to upper case
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
//random email generator
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
//random website generator
function randwebsite(){
    $website = array("");
    array_push($website, 'www.');
    $websiterand = rand(7,15);
    for ($i=0; $i<=$websiterand; $i++){
        $randLet = randLetter();
        array_push($website, $randLet);
    }
    array_push($website, '.com');
    $websiteFinal = implode($website);
    return $websiteFinal;
}
//random suffix generator
function suffix($rand){
        $suffixes = array('Dr.', 'Ave.', 'St.', 'Pkwy.', 'Ct.', 'Blvd.');        
    return $suffixes[$rand];
}
//random state generator
function state($rand){
    $states = $GLOBALS['statesAll'];
    return $states[$rand][0];
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
        <a href="dbviewer.php?page=1&limit=25">Click to go to the Database Viewer</a>
        <br>
        <br>
        This page allows the user to enter data into a MySQL database manually<br>
        or fill the database with randomized data for the case of this example system.
        <p><span class="error">* required field.</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Fill out the following fields then click add row to add a new row to the database: <br>
            First Name: <input type="text" name="firstname" value="<?php echo $firstName;?>">
            <span class="error">* <?php echo $nameErrF;?></span><br>
            Last Name: <input type="text" name="lastname" value="<?php echo $lastName;?>">
            <span class="error">* <?php echo $nameErrL;?></span><br>
            Email: <input type="text" name="email" value="<?php echo $email;?>">
            <span class="error">* <?php echo $emailErr;?></span><br>
            Website: <input type="text" name="website" value="<?php echo $website;?>">
            <span class="error">* <?php echo $websiteErr;?></span><br>
            Street Number: <input type="number" name="addressnum" value="<?php echo $addressNum;?>">
            <span class="error">* <?php echo $numErr;?></span><br>
            Street Name: <input type="text" name="streetname" value="<?php echo $streetName;?>">
            <span class="error">* <?php echo $streetErr;?></span><br>
            Street Suffix: <input type="text" name="suffix" value="<?php echo $streetSuffix;?>">
            <span class="error">* <?php echo $suffixErr;?></span><br>
            City: <input type="text" name="city" value="<?php echo $cityName;?>">
            <span class="error">* <?php echo $cityErr;?></span><br>
            State: <select name="state"><?php echo StateDropdown(null, 'abbrev'); ?></select>
            <span class="error">* <?php echo $stateErr;?></span><br>
            Zip: <input type="number" name="zip"value="<?php echo $zipcode;?>">
            <span class="error">* <?php echo $zipErr;?></span><br>
            <input type="submit" name="addrow" value="Add Row"><br>
            Or specify a number of randomized data rows to be added to the database(1-100 Max):<br>
            <input type="number" name="randomrows"><br>
            <input type="submit" name="addrandomrow" value="Add Random">
        </form>
        <?php       
        $con = mysqli_connect($server, $uname , $ulogin, $dbtarget);
        if(mysqli_connect_errno()){
               echo "Failed to connecto to MySQL: " . mysqli_connect_error();
           }            
        if ($posted == 0 & $nameErrL == "" & $numErr == "" & $streetErr == "" &  $suffixErr == "" &  $cityErr == "" &  $stateErr == "" &  $zipErr == ""){
            echo "One row added:" . "<br>";
            echo $firstName . " " . $lastName . " ";
            echo $addressNum . " " . $streetName . " " . $streetSuffix , " ";
            echo $cityName . ", " . $stateName . "  " . $zipcode;
            $sql = "INSERT INTO info (firstname, lastname, streetnum, street, suffix, city, state, zip)
            VALUES ('$firstName', '$lastName', '$addressNum', '$streetName', '$streetSuffix', '$cityName', '$stateName', '$zipcode')";
            if (!mysqli_query($con,$sql)) {
                die('Error: ' . mysqli_error($con));
                }
            }              
        if ($posted == 1){
            if ($randomRows < 101 & $randomRows > 0){
                echo "Adding " . $randomRows . " rows to the database with randomized info!" . "<br>";         
                for ($s=0; $s<$randomRows; $s++){
                    $nameLenF = rand(4,10);
                    $nameLenL = rand(7,12);
                    $firstName = getname($nameLenF);
                    $lastName = getname($nameLenL);
                    $email = randemail();
                    $website = randwebsite();
                    $addressNum = rand(100,29999);
                    $nameLenS = rand(8,15);
                    $streetName = getname($nameLenS);
                    $randSuffix = rand(0,5);
                    $streetSuffix = suffix($randSuffix);
                    $nameLenC = rand(7,14);
                    $cityName = getname($nameLenC);
                    $staterand = rand(0,51);
                    $stateName = state($staterand);
                    $zipcode = rand(10001,99999);
                    echo $firstName . " " . $lastName . " " . $email . " " . $website . " " . $addressNum . " " . $streetName . " " . $streetSuffix . " - " . $cityName . ", " . $stateName . "  " . $zipcode;
                    echo "</br>";
                    $sql = "INSERT INTO info (firstname, lastname, email, website, streetnum, street, suffix, city, state, zip)
                    VALUES ('$firstName', '$lastName', '$email', '$website', '$addressNum', '$streetName', '$streetSuffix', '$cityName', '$stateName', '$zipcode')";
                    if (!mysqli_query($con,$sql)) {
                        die('Error: ' . mysqli_error($con));
                        }
                    }
            } else{       
                echo '<span class="error">Random rows must be between 1 and 100!</span>';
                }
            }   
        ?>
        <br>
        <a href="destroy.php">Click to log out</a>
    </body>
</html>