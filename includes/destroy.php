<?php
/**
 * Created by PhpStorm.
 * User: MisterCray
 * Date: 11/5/2016
 * Time: 7:44 AM
 */

//Include necessary files
require("../includes/settings.php");
require("../includes/functions.php");

//Start session, then destroy all existing sessions.
session_start();

//Destroy only if session exists
if(isset($_SESSION['user'])) {
    //Destroy session and Add entry to log
    $u_temp = $_SESSION['user'];
    session_destroy();

    $activity = "User \'" . $u_temp . "\' logged out Successfully";

    $query = "INSERT INTO log (activity, category, risk) 
              VALUES ('$activity','logout','low')";

    //Add entry to log
    mysqli_query($db_connect, $query);

    ///Redirect back to homepage after session destroy
    header("Location: ../index.php");
    exit();
}

else    {
    header("Location: ../error.html");
    exit();
}

?>