<?php
	
	// This function works to let us know if we're going to 
	// start session or take it from what we left
	session_start();
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';

	if(!isset($_SESSION["id_usuario"])){
		// if the session var is null or empty
		// it will redirect me to index.php
		// in order to don't allow to the user to
		// connect to an url without the user and password
		// in case it know the url previously		
		header("Location: index.php");
	}

	$idUsuario = $_SESSION["id_usuario"];

	// this is to consult the user's name that is starting a session

	$sql = "SELECT id, nombre FROM usuarios WHERE id='$idUsuario'";
	$result = $mysqli->query($sql);

	$row = $result->fetch_assoc();
?>

<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Bienvenido</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" >
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" >
		<script src="js/bootstrap.min.js" ></script>
		
		<style>
			body {
			padding-top: 20px;
			padding-bottom: 20px;
			}
		</style>
	</head>
	
	<body>
		<div class="container">
			
			<nav class='navbar navbar-default'>
				<div class='container-fluid'>
					<div class='navbar-header'>
						<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
							<span class='sr-only'>Men&uacute;</span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
						</button>
					</div>
					
					<div id='navbar' class='navbar-collapse collapse'>
						<ul class='nav navbar-nav'>
							<li class='active'><a href='welcome.php'>Inicio</a></li>			
						</ul>
						<!-- This is to allow to access acording to the user type-->
						<?php if($_SESSION['tipo_usuario']==1) { ?>
							<ul class='nav navbar-nav'>
								<li><a href='#'>Administrar Usuarios</a></li>
							</ul>
						<?php } ?>
						<!--This is to close session-->
						<ul class='nav navbar-nav navbar-right'>
							<li><a href='logout.php'>Cerrar Sesi&oacute;n</a></li>
						</ul>
					</div>
				</div>
			</nav>	
			
			<div class="jumbotron">
				<h2><?php echo 'Bienvenid@ '.utf8_decode($row['nombre']); ?></h1>
				<br />
			</div>
		</div>
	</body>
</html>		