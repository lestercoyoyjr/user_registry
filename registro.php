<?php
	
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';
	
	// for errors
	$errors = array();
	
	// if the POST exists, then it will validate
	if(!empty($_POST)){
		// here we will be receiving all the elements from our form
		// nombre is because the html form class is named below
		$nombre = $mysqli->real_escape_string($_POST['nombre']);
		$usuario = $mysqli->real_escape_string($_POST['usuario']);
		$password = $mysqli->real_escape_string($_POST['password']);
		$con_password = $mysqli->real_escape_string($_POST['con_password']);
		$email = $mysqli->real_escape_string($_POST['email']);
		// with the last one is because the captcha is generated only once
		$captcha = $mysqli->real_escape_string($_POST['g-recaptcha-response']);

		// the user is always deactivated
		$activo = 0;
		// user type
		$tipo_usuario = 2;
		// captch secret key
		$secret = '';

		//verify captcha
		if(!$captcha){
			$errors[] = "Por favor verifica el captcha";
		}

		// with /funcs.php where going to validate the user entrance
		// but from the db not from html form because the last one is
		// very easy to modify
		
		// fields
		if (isNull($nombre, $usuario, $password, $con_password, $email)){
			$errors[] = "Debe llenar todos los campos";
		}

		// if email is not valid, send the message
		if(!isEmail($email)){
			$errors[] = "Direccion de correo invalida";
		}

		// to validate the password
		if(!validaPassword($password, $con_password)){
			$errors[] = "las contrasenas no coinciden";
		}

		// Here we're going to use the functions to validate user and password
		// in function

		// to validate user
		if(usuarioExiste($usuario)){
			$errors[] = "El nombre de usuario ".$usuario." ya existe";
		}
		if(emailExiste($email)){
			$errors[] = "El nombre de usuario ".$email." ya existe";
		}

		// to show errors
		if(count($errors) == 0){
			// if we don't have any errors, we proceed with this
			
			// we validate captcha
			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");

			$arr = json_decode($response, TRUE);

			// we're gonna check the data from google is correct
			if($arr['success']){
				// we cipher the password
				// in funcs we found the function "hashPassword"
				$pass_hash = hashPassword($password);
				//generate token
				$token = generateToken();

				// user registry
				// with function "registraUsuario"
				$registro = registraUsuario($usuario, $pass_hash, $nombre, $email, $activo, $token, $tipo_usuario);

				// if register ends succesfully
				if($registro > 0){
					// we're going to validate the registry with email
					//subject
					$url = 'http://'.$_SERVER["SERVER_NAME"].'/user_registry/activar.php?id='.$registro.'&val='.$token;
					// body
					$cuerpo = "Estimado $nombre: <br/><br/> Para completar el registro es indispensable que haba clic en el siguiente link <a href='$url'> Activar Cuenta </a>";

					// we're gonna add the subject and body
					$asunto = 'Activar Cuenta - Sistema de Usuarios';
				} else {
					$errors[] = "Error al Registrar";
				}
			} else {
				$errors[] = "Error al comprobar el registro";
			}
		}
	}
	
?>

<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Registro</title>
		
		<link rel="stylesheet" href="css/bootstrap.min.css" >
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" >
		<script src="js/bootstrap.min.js" ></script>
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
	
	<body>
		<div class="container">
			<div id="signupbox" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">Reg&iacute;strate</div>
						<div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="index.php">Iniciar Sesi&oacute;n</a></div>
					</div>  
					
					<div class="panel-body" >
						
						<form id="signupform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
							
							<div id="signupalert" style="display:none" class="alert alert-danger">
								<p>Error:</p>
								<span></span>
							</div>
							
							<div class="form-group">
								<label for="nombre" class="col-md-3 control-label">Nombre:</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?php if(isset($nombre)) echo $nombre; ?>" required >
								</div>
							</div>
							
							<div class="form-group">
								<label for="usuario" class="col-md-3 control-label">Usuario</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="usuario" placeholder="Usuario" value="<?php if(isset($usuario)) echo $usuario; ?>" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="password" class="col-md-3 control-label">Password</label>
								<div class="col-md-9">
									<input type="password" class="form-control" name="password" placeholder="Password" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="con_password" class="col-md-3 control-label">Confirmar Password</label>
								<div class="col-md-9">
									<input type="password" class="form-control" name="con_password" placeholder="Confirmar Password" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="email" class="col-md-3 control-label">Email</label>
								<div class="col-md-9">
									<input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($email)) echo $email; ?>" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="captcha" class="col-md-3 control-label"></label>
								<div class="g-recaptcha col-md-9" data-sitekey="clave de reCaptcha"></div>
							</div>
							
							<div class="form-group">                                      
								<div class="col-md-offset-3 col-md-9">
									<button id="btn-signup" type="submit" class="btn btn-info"><i class="icon-hand-right"></i>Registrar</button> 
								</div>
							</div>
						</form>
						<?php echo resultBlock($errors); ?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>															