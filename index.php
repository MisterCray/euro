<?php

session_start();

//Display index for non-existing session
if(!isset($_SESSION['user'])) {
	echo "<!DOCTYPE html>
	<html >
	<head>
		<meta charset='UTF-8'>
		<title>Euro Parts</title>
		<link rel='stylesheet' href='css/style.css'>
	</head>
	
	<body>
	  <div class='wrapper'>
		<div class='container'>
			<h1 class='title'>Euro Parts - Log In</h1>
			<form class='form'>
				<input type='text' id='user' placeholder='Username'>
				<input type='password' id='pass' placeholder='Password'>
				<button type='submit' id='login-button' >Login</button>
			</form>
		</div>
		
		<ul class='bg-bubbles'>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
	  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
	  <script src='js/index.js'></script>
	</body>
	</html>";
}

else    {
	header("Location: view.php");
	exit();
}
?>