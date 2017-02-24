<?php
/**
 * Created by PhpStorm.
 * User: MisterCray
 * Date: 11/5/2016
 * Time: 12:46 AM
 */

/** This is the master file to handle all AJAX requests */


//Include necessary files
require("../includes/settings.php");
require("../includes/functions.php");

//Start session to make checks further down
session_start();

switch($_POST['type']) {

    case "login":
        //Handle login request only if post data is entered
        if(isset($_POST['uname']) && isset($_POST['upass']))    {
            //Attach salt to SHA1 of entered password
            $salty = sha1($_POST['upass']) . "cdd6fdff45d849dd5d46ee0704b072c967095eae";

            //Set default variables
            $flag = FALSE;
            $type ="staff";

            //Escape the posted username before processing
            $e_uname = mysqli_real_escape_string($db_connect, $_POST['uname']);

            //Query
            $query = mysqli_query($db_connect, "SELECT * FROM user_master");

            //Check if the credential match the stored data
            while ($check = mysqli_fetch_array($query)) {
                if ($check['username'] == $e_uname)
                    if ($check['pass'] == $salty) {
                        $flag = TRUE;
                        $type = $check['type'];
                        break;
                    }
            }

            //Send output based on check results from database
            if ($flag) {
                $activity = "User \'" . $e_uname . "\' logged in Successfully";

                $query = "INSERT INTO log (activity, category, risk) 
                VALUES ('$activity','login','low')";

                //Add entry to log
                mysqli_query($db_connect, $query);

                //Set session variables
                $_SESSION['user'] = $e_uname;
                $_SESSION['type'] = $type;

                //If user is admin, set table to admin table
                if($type == "admin")
                    $_SESSION['table'] = "admin";
                else
                    $_SESSION['table'] = $e_uname;

                //Indicate successful login to AJAX response
                echo $e_uname;
            } else {
                $ip = get_client_ip();
                if ($ip == "::1")
                    $activity = "Suspicious login from Server";
                else
                    $activity = "Suspicious login from " . $ip;

                $query = "INSERT INTO log (activity, category, risk) 
                VALUES ('$activity','login','high')";

                //Add entry to log
                mysqli_query($db_connect, $query);

                //Indicate failed login to AJAX response
                echo "fail";
        }
            break;
        }

    case "invupdate":
        //Handle update only if user session exists
        if(isset($_SESSION['user']))    {
            $user = $_SESSION['table'];

            //Escape the POST data before processing
            $e_ops = mysqli_real_escape_string($db_connect, $_POST['op']);
            $e_id = mysqli_real_escape_string($db_connect, $_POST['id']);
            $e_qty = mysqli_real_escape_string($db_connect, $_POST['qty']);

            $word = "SELECT * FROM ".$user." WHERE id = ".$e_id;
            //Query to retrieve and update quantity from DB
            $query = mysqli_query($db_connect, "SELECT * FROM ".$user." WHERE id = ".$e_id."");

            //Resulting data from DB
            $result = mysqli_fetch_array($query);

            //Variable to return AJAX
            $success_msg = -1;

            //Perform operation
            switch($e_ops)  {
                case '=':
                    $success_msg = $e_qty;
                    $activity = $_SESSION['user']." reset the quantity to ".$e_qty;
                    mysqli_query($db_connect, "UPDATE ".$user." SET count = ".$success_msg." WHERE id = ".$e_id."");
                    mysqli_query($db_connect, "INSERT INTO log (uid, pid, curqty, activity, category, risk) VALUES ('$user', '$e_id', '$success_msg', '$activity', 'invreset', 'low')");
                    break;

                case '+':
                    $success_msg = $result['count']+$e_qty;
                    $activity = $_SESSION['user']." increased the quantity by ".$e_qty;
                    mysqli_query($db_connect, "UPDATE ".$user." SET count = ".$success_msg." WHERE id = ".$e_id."");
                    mysqli_query($db_connect, "INSERT INTO log (uid, pid, curqty, activity, category, risk) VALUES ('$user', '$e_id', '$success_msg', '$activity', 'invadd', 'low')");
                    break;

                case '-':
                    $success_msg = $result['count']-$e_qty;
                    $activity = $_SESSION['user']." decreased the quantity by ".$e_qty;
                    mysqli_query($db_connect, "UPDATE ".$user." SET count = ".$success_msg." WHERE id = ".$e_id."");
                    mysqli_query($db_connect, "INSERT INTO log (uid, pid, curqty, activity, category, risk) VALUES ('$user', '$e_id', '$success_msg', '$activity', 'invsubtract', 'low')");
                    break;
            }

            echo $success_msg;
            break;
        }

    case "comments":
        //Handle comments only if user session exists
        if(isset($_SESSION['user']))    {
            $user = $_SESSION['table'];

            //Escape the POST data before processing
            $e_process = mysqli_real_escape_string($db_connect, $_POST['process']);
            $e_id = mysqli_real_escape_string($db_connect, $_POST['id']);

            switch ($e_process)  {

                case "get":
                    $query = "SELECT * FROM ".$user." WHERE id = ".$e_id;
                    $fetch = mysqli_query($db_connect, $query);
                    $result = mysqli_fetch_assoc($fetch);

                    //Check if there is any existing comment
                    if($result['comment'])
                        echo $result['comment'];
                    else
                        echo NULL;
                    break;

                case "set":
                    $e_comment = mysqli_escape_string($db_connect, $_POST['comment']);
                    $query = "UPDATE ".$user." SET comment = '".$e_comment."' WHERE id = ".$e_id;
                    mysqli_query($db_connect,$query);
                    echo "Added";

                    ///Log the new comment
                    $activity = $_SESSION['user']." added a comment";
                    mysqli_query($db_connect, "INSERT INTO log (uid, pid, activity, category, risk, extra) VALUES ('$user', '$e_id','$activity', 'addcomment', 'low', $e_comment)");
                    break;
            }
            break;
        }

    case "newitem":
        //Add new item only if user session exists
        if(isset($_SESSION['user']))    {
            $user= $_SESSION['table'];

            //Escape the new item details before adding to DB
            $e_id =mysqli_escape_string($db_connect, $_POST['id']);
            $e_name = mysqli_escape_string($db_connect, $_POST['name']);
            $e_stock = mysqli_escape_string($db_connect, $_POST['stock']);

            //Check for invalid entries
            if($e_id == NULL || $e_name == NULL || $e_stock == NULL)
                //Return AJAX response
                echo "Fail";
            else {
                //Build query
                $query = "INSERT INTO " . $user . "(productid, productname, count) VALUES ('" . $e_id . "','" . $e_name . "','" . $e_stock . "')";

                //Pass query to DB
                mysqli_query($db_connect, $query);

                //Log the new entry
                $activity = $_SESSION['user']." added a new item";
                mysqli_query($db_connect, "INSERT INTO log (uid, pid, activity, category, risk) VALUES ('$user', '$e_id','$activity', 'additem', 'medium')");

                //Return AJAX response
                echo "Pass";
            }
            break;
        }

    case "collate":
        //Collate only if the user is admin
        if(isset($_SESSION['use']) && $_SESSION['type'] == "admin") {
            //Variable to collect all stock taking users
            $stockers = array();

            //Query
            $query = mysqli_query($db_connect, "SELECT * FROM user_master WHERE type='staff'");

            //Read all staff from Usernames DB
            while($check = mysqli_fetch_array($query))
                array_push($stockers, $check['username']);

            //Initialize collated count to zero
            $col_count = array();

            //Collate count to collated table from each staff table
            //We're assuming all the tables to have same number of entries here
            $query = mysqli_connect($db_connect, "SELECT * FROM collated");

            //Go through the DB row by row
            while($check = mysqli_fetch_array($query))  {
                $count = 0;
                for ($i = 0; $i<count($stockers); $i++) {
                    $subquery = "SELECT count FROM ".$stockers[i]." WHERE id = ".$check['id'];
                    $subfetch = mysqli_query($db_connect, $subquery);
                    $subcount = mysqli_fetch_assoc($subfetch);
                    $count += $subcount['count'];
                }

                //Update the count in collated table
                mysqli_query($db_connect, "UPDATE collated SET count = '$count' WHERE id = '$check['id']");
            }
        }
        break;

    default:
        header("Location: ../error.html");
        exit();
}

?>