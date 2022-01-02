<?php
	session_start();
	// this is to close session
	session_destroy();

	header('location: index.php');
?>