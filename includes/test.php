<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/14/2016
 * Time: 10:35 AM
 */

ini_set('max_execution_time', 0);



require("settings.php");
require("functions.php");


//Variable to collect all stock taking users
$stockers = array();

//Query
$query = mysqli_query($db_connect, "SELECT * FROM user_master WHERE type='staff'");

//Read all staff from Usernames DB
while($check = mysqli_fetch_array($query))
    array_push($stockers, $check['username']);

//Collate count to collated table from each staff table
//We're assuming all the tables to have same number of entries here
$query = mysqli_query($db_connect, "SELECT * FROM collated");

while($check = mysqli_fetch_array($query))  {

    $count = 0;
    for ($i = 0; $i<count($stockers); $i++) {
        $subquery = "SELECT count FROM ".$stockers[$i]." WHERE id = ".$check['id'];
        $subfetch = mysqli_query($db_connect, $subquery);
        $subcount = mysqli_fetch_assoc($subfetch);
        $count += $subcount['count'];
        echo "<br/>For loop - Count: ".$count."<br/>Query: ".$subquery;
    }

    //Update the count in collated table
    $upquery = "UPDATE collated SET count = ".$count." WHERE id = ".$check['id'];
    mysqli_query($db_connect, $upquery);

    //Echo count to verify
    echo "-----Outside for loop, Count:".$count."<br/>Update query: ".$upquery."<br/>*******************";

}


sleep(1);
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "Process Time: {$time}";

?>