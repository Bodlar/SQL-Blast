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
//class and function to create the paginated information
class paginate{
            public function init($limit, $page, $disp){
            //connects to the database and queries the table
                $_SESSION['lastact'] = time();
                $con = mysqli_connect($GLOBALS['server'], $GLOBALS['uname'] , $GLOBALS['ulogin'], $GLOBALS['dbtarget']);
                if(mysqli_connect_errno()){
                    echo "Failed to connecto to MySQL: " . mysqli_connect_error();
                }
                $query1 = mysqli_query($con,"SELECT * FROM info");
            //gathers the number of total pages for our pagination
                $total_pages = ceil(mysqli_num_rows($query1)/$limit);
            //creates the variable hyperlinks to navigate our paginated table
                $prev = '<a href="?page=' . ($page-1) . '&limit=' . $limit . '">Prev</a>' . '&nbsp';
                $next = '<a href="?page=' . ($page+1) . '&limit=' . $limit .  '">Next</a>';
                if($page <= 1){
                    $page = 1;
                    $prev = '';
                }
                elseif($page >= $total_pages){
                    $page = $total_pages;
                    $next = '';
                }
                if($page > 2){
                    $prev = '<a href="?page=1' . '&limit=' . $limit . '">First</a>' . '&nbsp' . $prev;
                }
                if($page < ($total_pages - 1)){
                    $next = $next . '&nbsp' . ' <a href="?page=' . $total_pages . '&limit=' . $limit .  '">Last</a>';
                }
            //starts our content variable which will contain all of our returned data for display
                $content="";
            //the $disp conditionals allow me to exclude the table generation portion and variable pagination links to only generate the bottom nav menu
                if ($disp == 0){
            //creates the starting point for our current page and queries the piece of the table we need to display
                    $start = ($limit*($page-1));
                    $query2 = mysqli_query($con, "SELECT * FROM info LIMIT $start, $limit") or die("Error In Query " . mysql_error());
            //runs a loop to add all of the current paginated data to be displayed to $content
                    while($row = mysqli_fetch_array($query2)) {
                        $content .= "<tr>" . "<td>" . $row['firstname'] . "</td>" ."<td>" . $row['lastname'] . "</td>" . "<td>" . $row['email'] . "</td>" . 
                                    "<td>" . $row['website'] . "</td>" . "<td>" . $row['streetnum'] . "</td>" . "<td>" . $row['street'] . "</td>" . "<td>" . 
                                    $row['suffix'] . "</td>". "<td>" . $row['city'] . "</td>". "<td>" . $row['state'] . "</td>" . "<td>" . $row['zip'] . "</td>" . "</tr>";
                    }
                //defines the variables used for variable pagination to maintain our position in the database when changing pagination variables
                    $twentyfivestart = (($start/25)+1);
                    $twentyfivestart = floor($twentyfivestart);
                    $fiftystart = (($start/50)+1);
                    $fiftystart = floor($fiftystart);
                    $hundredstart = (($start/100)+1);
                    $hundredstart = floor($hundredstart);
                //creates the hyperlinks for variable pagination
                    $twentyfive = '<a href="?page=' . $twentyfivestart . '&limit=25' . '">25</a>' . '&nbsp';
                    $fifty = '<a href="?page=' . $fiftystart . '&limit=50' . '">50</a>' . '&nbsp';
                    $hundred = '<a href="?page=' . $hundredstart . '&limit=100' . '">100</a>' . '&nbsp';
                    $content .= '<br><hr>' . $twentyfive . $fifty . $hundred;
                //adds the pagination information to the page for the top nav menu to $content
                    $content .= '<br><hr>' . $prev . ' Page ' . $page . '/' . $total_pages . ' ' . $next;
                    }
            //this $disp conditional allows me to only generate the bottom nav menu
                if ($disp == 1){
                    $content .=  $prev . ' Page ' . $page . '/' . $total_pages . ' ' . $next;
                }
            //closes the sql connection
                mysqli_close($con);
            //sends our content back to the program for echo
                return $content;
            }
        }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <style>
        p {color: #FF0000;}
        </style>
    </head>
    <body>
        <a href="sqlblast.php">Click to go back to the Database Input Page</a>
        <br>
        <br>
        <a href="destroy.php">Or, click here to log out</a>
        <br>
        <br>
        This page displays the data from the MySQL database in a variably paginated table.
        <br>
        <br>
        (The following selection will maintain your position in the<br>
        databse based on the records already being displayed<br>
        divided by the new number of rows to display at a time.)<br>
        Select the number of rows to display at a time: 
        
        <?php
//connects to the database and queries the table we are working with
        $con = mysqli_connect($server, $uname , $ulogin, $dbtarget);
        if(mysqli_connect_errno()){
               echo "Failed to connecto to MySQL: " . mysqli_connect_error();
          }
        $result = mysqli_query($con,"SELECT * FROM info");
//gets the page and limit values for the pagination function
        $pages = $_GET['page'];
        $limit = $_GET['limit'];
//performs some error checking in case the user modifies the values inapropriately in the url
        if ($limit > 100 || $limit < 25){
            $limit = 25;
            echo '<p>You tried to use an invalid limit! Tsk Tsk. Limit reset to 25 instead.</p>';
        }
        $total_pages_check = ceil(mysqli_num_rows($result)/$limit);        
        if ($pages < 1 || $pages > $total_pages_check){
            $pages = 1;
            echo '<p>You tried to us an invald page! Tsk Tsk. Sent to first page instead.</p>';
        }               
//sets the table headers for the table data
        echo "<table border='1'>
        <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Website</th>
        <th>St. Number</th>
        <th>Street Name</th>
        <th>St. Suffix</th>
        <th>City</th>
        <th>State</th>
        <th>Zip Code</th>
        </tr>";
//creates a new instance and performs the pagination function
        $class = new paginate;
        echo $class->init($limit, $pages, 0);
        echo "</table>";
        echo $class->init($limit, $pages, 1);
//closes the sql connection
        mysqli_close($con);
        ?>
    </body>
</html>
