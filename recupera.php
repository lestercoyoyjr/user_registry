<?php
	
	require 'funcs/conexion.php';
	require 'funcs/funcs.php';

	// for errors
	$errors = array();

	// if the POST exists, then it will validate
	if(!empty($_POST)){
		$email = $mysqli->real_escape_string($_POST['email']);

		// this function detects if a mail is valid or not
		if(!isEmail($email)){
			$errors[] = "Debe ingresar un correo electronico valido"; 
		}
		if(emailExiste($email)){
			// we require some data from the user
			$user_id = getValor('id', 'correo', $email);
			$nombre = getValor('nombre', 'correo', $email);

			// It is necessary to generate a token to validate
			// the email the same way we did to validate an account
			$token = generaTokenPass($user_id);

			// url with name server
			$url = 'http://'.$_SERVER["SERVER_NAME"].'/user_registry/cambia_pass.php?id='.$user_id.'&token='.$token;

			// we're gonna add the subject and body
			$asunto = 'Recuperar Password - Sistema de Usuarios';
			// body
			$cuerpo = "Hola $nombre: <br/><br/> Se ha solicitado un reinicio de contrase&ntilde;a. <br/><br/> Para restaurar la contrase&ntilde;a, visita la siguiente direcci%oacute;n <a href='$url'> Canbiar password </a>";

			// this is to send email
			if(enviarEmail($email, $nombre, $asunto, $cuerpo)){
				echo "Hemos enviado un correo electronico a la direccion $email para restablecer tu password.<br/>";
				echo "<a href='index.php'>Iniciar Sesion </a>";
				exit;
			} else {
				$errors[] = "Error al enviar Email";
			}
		} else{
			$errors[] = "No existe el correo electronico";
		}
	}
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Recuperar Password</title>
		
		<link rel="stylesheet" href="css/bootstrap.min.css" >
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" >
		<script src="js/bootstrap.min.js" ></script>
		
	</head>
	
	<body>
		
		<div class="container">    
			<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
				<div class="panel panel-info" >
					<div class="panel-heading">
						<div class="panel-title">Recuperar Password</div>
						<div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="index.php">Iniciar Sesi&oacute;n</a></div>
					</div>     
					
					<div style="padding-top:30px" class="panel-body" >
						
						<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
						
						<form id="loginform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
							
							<div style="margin-bottom: 25px" class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
								<input id="email" type="email" class="form-control" name="email" placeholder="email" required>                                        
							</div>
							
							<div style="margin-top:10px" class="form-group">
								<div class="col-sm-12 controls">
									<button id="btn-login" type="submit" class="btn btn-success">Enviar</a>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-12 control">
									<div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
										No tiene una cuenta! <a href="registro.php">Registrate aquí</a>
									</div>
								</div>
							</div>    
						</form>
						<!--To show the errors-->
						<?php echo resultBlock($errors); ?>
					</div>                     
				</div>  
			</div>
		</div>
	</body>
</html>							