<?php
/**
 * Created by PhpStorm.
 * User: MisterCray
 * Date: 11/2/2016
 * Time: 11:23 PM
 * This file will handle the database connection and respond accordingly
 */

//Database settings to be input here. Will be included in other files if necessary.
$db_server = 'localhost';
$db_user = 'root';
$db_access = 'Craymon22';
$db_name = 'euro_master';

//Attempt to connect to the database.
$db_connect = mysqli_connect($db_server,$db_user,$db_access,$db_name) or die(mysqli_connect_error());

?>