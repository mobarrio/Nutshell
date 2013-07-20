<?php 
/*
TIADM - Copyright (c) 2012 by Mariano Jorge Obarrio Miles.

This work is made available under the terms of licensed under a Creative Commons Atribución-NoComercial-SinDerivadas 3.0 España.
Legal Code (the full license): http://creativecommons.org/licenses/by-nc-nd/3.0/es/.

Esta obra está licenciada bajo la Licencia Creative Commons Atribución-NoComercial-SinDerivadas 3.0 España
Para ver una copia de esta licencia, visita http://creativecommons.org/licenses/by-nc-nd/3.0/es/.

Código Safe Creative: #1211060715189
*/

require_once("include/config.php");
require_once("adLDAP.php");               // Conexion al LDAP

function isLocalAccess($doms)
{
		$excluirDirectorio = false;
		$excluirArchivo    = false;
		$disableLogin      = false;

		$xDir   = array('include','errors');
		$xFiles = array('index','piepag','logout');
		$info   = pathinfo($_SERVER["SCRIPT_NAME"]);
		
		/* TRUE si esta definido el directorio */
		foreach ($xDir as $d) { if (strpos($info['dirname'], $d)) $excluirDirectorio = true; }
		
		/* TRUE si esta definido el archivo */
		$excluirArchivo = in_array($info['filename'], $xFiles);
		
		if(!$excluirDirectorio && !$excluirArchivo)
		{
			foreach ($doms as $d) { if (strpos($_SERVER['HTTP_REFERER'], $d)) return(true); }
			ob_clean();
			header("location: ".$_SESSION['PATH_URL']."/errors/401.html");
		}
}

class LoginSystem
{
	var	$db_host, $db_name, $db_user, $db_password, $connection, $username, $password;
	var $imgLogo         = ""; 
	var $Version		 = "V1P";
	var $redirect_page   = "";
	var $debug           = 0;
	var $AccessLevel	 = -1;
	var $ldap_on         = true;
	var $options         = array();	
	var $prefix          = "login_"; // Prefijo de la cookie
	var $cookie_duration = 90;       // Duracion en Minutos
	var $isExpired       = 0;        // 1 Si la sesion expiro
	
	/**
	 * Constructor
	 */
	function LoginSystem($redirect_page="/")
	{
		$this->Debug("LoginSystem(): Ejecuta el constructor de la clase");
		$this->Debug("LoginSystem(): Debug en ". $this->debug);
		$this->db_host       = DB_SERVER;
		$this->db_name       = DB_DATABASE;
		$this->db_user       = DB_USER;
		$this->db_password   = DB_PASS;
		$this->ldap_on       = $this->getParam('LDAP_ON');
		$this->redirect_page = $this->getParamValue('PATH_URL');
		$this->imgLogo       = $this->getParamValue('LOGO_MINI');
		$this->Version       = $this->getParamValue('TIADM_REV');
		if($this->ldap_on)
		{
		  $this->options    = array("account_suffix"     => $this->getParamValue('LDAP_ACCOUNT_SUFFIX'),
		                            "base_dn"            => $this->getParamValue('LDAP_BASE_DN'),
		                            "domain_controllers" => array($this->getParamValue('LDAP_DC1'), $this->getParamValue('LDAP_DC2')),
		                            "ad_username"        => $this->getParamValue('LDAP_USER'),
		                            "ad_password"        => $this->getParamValue('LDAP_PASS'));
		}
	}
	
	/**
	*  Retorna true o false segun el valor del parametro ON/OFF
	**/
	function getParam($param)
	{
		$db = ADONewConnection('mysql');
		$db->Connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$fields = $db->GetAll("SELECT valor FROM TB_Configuracion WHERE clave='$param';");  
		$db->Close(); # opcional	
		$ret = $fields[0]['valor'];
		if(preg_match('/ON/',$ret)) return(true);
		return(false);
	}

	function getParamValue($param)
	{
		$db = ADONewConnection('mysql');
		$db->Connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$fields = $db->GetAll("SELECT valor FROM TB_Configuracion WHERE clave='$param';");  
		$db->Close(); # opcional	
		return($fields[0]['valor']);
	}


	/**
	*  Retorna true o false segun el valor del parametro ON/OFF
	**/
	function getRoldeUsuario()
	{                   
		$db = ADONewConnection('mysql');
		$db->Connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$sql = "SELECT idMenuTipo as rol FROM TB_Usuarios where idUsuario = '".$_SESSION['userName']."';";
		$fields = $db->GetAll($sql);  
		$db->Close(); # opcional	
		$ret = $fields[0]['rol'];
		return($ret);
	}

	/**
	 * @return true or false
	 */
	function isLoggedIn()
	{
		//$xLogin = array('dashboard30'); /* Deshabilitar login para las paginas definidas */
		//$info = pathinfo($_SERVER["SCRIPT_NAME"]);
		//if(in_array($info['filename'], $xLogin)) { return true; }

		// Si no esta definido el uid de sesion muestra el loguin
		if(!isset($_SESSION['userName'])) 
		{  
			$this->Debug("isLoggedIn(): _SESSION[userName] No Definido.");
			return false;
		} else { 
			$this->Debug("isLoggedIn(): _SESSION[userName] Definido OK.");
			$db = ADONewConnection('mysql');
			$db->Connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
			$sql = "SELECT AccessLevel FROM TB_Usuarios where idUsuario = '".$_SESSION['userName']."';";
			$fields = $db->GetRow($sql);  
			$db->Close(); # opcional	
			$this->AccessLevel = $fields['AccessLevel'];
			$_SESSION['AccessLevel'] = $this->AccessLevel;
		}

		// Redirecciona despues del loggin para la pagina que se intentaba acceder.
		$this->redirect_page = $_SERVER['REQUEST_URI'];

		// Verifica si la WEB esta en modo Mantenimiento
		if($this->getParam('MAINTENANCEPAGE') && $this->getRoldeUsuario() != 'Administrador' && $_SESSION['LoggedIn'] ) { ob_clean(); header("Location: ".$_SESSION['PATH_URL']."/errors/mantenimiento.html"); }

		// Si la cookie de expiracion existe y es distinta a TRUE y la variable de sesion LEGGEDIN esta a TRUE		
		if( isset($_COOKIE[$this->prefix.'expiration']) && ($_COOKIE['login_expiration'] == "true") && $_SESSION['LoggedIn'] )
		{ 
			// Renueva el periodo de expiracion de la cookie y retorna TRUE
			$this->Debug("isLoggedIn(): Login OK Retorna TRUE");
			$this->SetCookieTrue();
			return true; 
		}
		else
		{
			// Elimina la cookie de expiracion y retorna FALSE
			$this->Debug("isLoggedIn(): Login ERRONEO Retorna FALSE");
			$this->logout();
			return false;
		}
	}
	
	/**
	 * @return true/false
	 */
	function doLogin($username, $password)
	{
		$this->Debug("doLogin(): Verifica el Usuario y Password via LDAP o Localmente");
		$this->connect();
		$this->username = $username;
		$this->password = $password;

		$usr = $this->clean($this->username);
		$psw = md5 ($this->clean($this->password));

		$this->Debug("doLogin(): LDAP [ON/OFF] : ". $this->ldap_on);
		if($this->ldap_on)
		{
			$this->Debug("doLogin(): Verificacion via LDAP");
			$adldap = new adLDAP($this->options);
			$authUser = $adldap->authenticate($this->clean($this->username), $this->clean($this->password));
			if ($authUser == true) 
			{ 
				$this->Debug("doLogin(): Usuario Autenticado via LDAP OK");
				$infousr     = $adldap->user_info($this->clean($this->username));
				$Descripcion = $infousr[0][displayname][0];
				$Mail        = $infousr[0][mail][0];
				
				$this->Debug("doLogin(): Define las variables de sesion [LoggedIn, userName y LDAP]");

				$_SESSION['LoggedIn'] = true;
				$_SESSION['userName'] = $this->username;
				$_SESSION['LDAP']     = $this->ldap_on;

				mysql_query("UPDATE TB_Usuarios SET InfoLastLoggin='Acceso OK via LDAP',LastAccess=NOW() WHERE idUsuario='".$this->username."';", $this->connection);

				$sql = "SELECT * FROM TB_Usuarios WHERE idUsuario = '$usr' and Passwd = '$psw' and active = 1;";
				$result = mysql_query($sql, $this->connection);
				if(mysql_affected_rows($this->connection) == 0)
				{
					$sql = "INSERT INTO TB_Usuarios (idUsuario,Passwd,Descripcion,Mail,active) VALUES ('$usr','$psw','$Descripcion','$Mail',0);";
					mysql_query($sql, $this->connection);
				}
				else // Si existe actualiza la psw local la cual se utilizara si se deshabilita el LDAP
				{
					$sql = "UPDATE TB_Usuarios SET Passwd='$psw' Where idUsuario='$usr';";
					$result = mysql_query($sql, $this->connection);				
				}
			}
			else // Si falla la autenticacion por LDAP busca Autenticacion LOCAL
			{ 
				$this->Debug("doLogin(): Usuario inexistente retorna FALSE");
				mysql_query("UPDATE TB_Usuarios SET InfoLastLoggin='Acceso Denegado via LDAP',LastAccess=NOW() WHERE idUsuario='".$this->username."';", $this->connection);
				$this->disconnect();
				return false;
			}
			return true;
		}
		else // Si LDAP desactivado busca Autenticacion LOCAL
		{				
				$this->Debug("doLogin(): LDAP desactivado busca Autenticacion LOCAL");
				$sql = "SELECT * FROM TB_Usuarios WHERE idUsuario = '$usr' and Passwd = '$psw' and active = 1;";
				$result = mysql_query($sql, $this->connection);
				if(mysql_affected_rows($this->connection) == 0)
				{
					$this->Debug("doLogin(): Usuario inexistente retorna FALSE");
					mysql_query("UPDATE TB_Usuarios SET InfoLastLoggin='Acceso Denegado via Local Login',LastAccess=NOW() WHERE idUsuario='".$this->username."';", $this->connection);
					$this->disconnect();			
					return false;
				}
				else // matching login ok
				{
					session_regenerate_id(); // more secure to regenerate a new id.
					$this->Debug("doLogin(): Usuario OK define las variables de sesion [LoggedIn, userName y LDAP] y retorna FALSE");
					mysql_query("UPDATE TB_Usuarios SET InfoLastLoggin='Acceso OK via Local Login',LastAccess=NOW() WHERE idUsuario='".$this->username."';", $this->connection);

					//set session vars up
					$_SESSION['LoggedIn'] = true;
					$_SESSION['userName'] = $this->username;
					$_SESSION['LDAP']     = $this->ldap_on;
				}
				$this->disconnect();		
		}
		return true;
	}
	
	function sendMail($usr,$psw,$Mail,$Descripcion)
	{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: TIADM <middleware1@globalia-corp.com>' . "\r\n";
		$subject  = "TIADM - No Responder";
		$body     = "<b>Informacion de la cuenta</b><br><br>";
		$body    .= "<b>Usuario    :</b> $usr<br>";
		// $body    .= "<b>Password   :</b> $psw<br>";
		$body    .= "<b>Descripcion:</b> $Descripcion<br><br><br>";
		$body    .= "El usuario se registro correctamente, solicite al administrador los permisos necesarios.<br><br>";
		$body    .= "Muchas gracias";
		
		mail($Mail, $subject, $body,$headers);
	}

	function SetCookieTrue()
	{
		$this->Debug("&nbsp;&nbsp;&nbsp;SetCookieTrue(): Renueva el periodo de expiracion de la cookie y isExpired a 0");
		// Si el login es correco guarda las cookies de la sesion
		$this->isExpired = 0;
		setcookie($this->prefix."expiration","true", time()+($this->cookie_duration*60),"/"); // (d*24h*60m*60s)
	}

	function SetCookieFalse()
	{
		$this->Debug("&nbsp;&nbsp;&nbsp;SetCookieFalse(): Destruye la cookie de expiracion seteando el tiempo en pasado y isExpired a 1");
		// Destruye cualquier cookie que exista seteando el tiempo en pasado
		$this->isExpired = 1;
		if(!empty($_COOKIE[$this->prefix.'expiration'])) setcookie($this->prefix."expiration", "false", time()-(3600*25),"/");
	}

	function SetLogo($img){	$this->imgLogo = $img; }
	function SetVersion($version){ $this->Version = $version; }
	function SetLDAPOn()	{	$this->ldap_on = true; }
	function SetLDAPOff()	{	$this->ldap_on = false; }
	function SetDebug()	  {	$this->debug   = 1; }

	function logout()
	{
		$this->Debug("logout(): Por seguridad elimina las cookies ejecuta SetCookieFalse()");
		$this->Debug("logout(): Realiza un Unset de la sesion");
		$this->Debug("logout(): Realiza un Destroy de la session");
		$this->Debug("logout(): Define isExpired en 1");
		$this->Debug("logout(): Realiza un session_start() y un session_regenerate_id()");
		$this->isExpired = 1;
		setcookie($this->prefix."expiration", "false", time()-(3600*25),"/");
		session_unset();   //destroys variables
		session_destroy(); //destroys session
		session_start();
		session_regenerate_id();
	}
	
	/**
	 * @return true/false
	 */
	function connect()
	{
		$this->connection = mysql_connect($this->db_host, $this->db_user, $this->db_password) or die("Unable to connect to MySQL");
		mysql_select_db($this->db_name, $this->connection) or die("Unable to select DB!");
		// Valid connection object? everything ok?
		if($this->connection)	{	return true;	}
		else return false;
	}
	
	/**
	 * Disconnect from the db
	 */
	function disconnect()
	{
		mysql_close($this->connection);
	}
	
	/**
	 * Cleans a string for input into a MySQL Database.
	 * Gets rid of unwanted characters/SQL injection etc.
	 * 
	 * @return string
	 */
	function clean($str)
	{
		// Only remove slashes if it's already been slashed by PHP
		if(get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}
		// Let MySQL remove nasty characters.
		$str = mysql_real_escape_string($str);
		
		return $str;
	}
	
	/**
	 * create a random password
	 * 
	 * @param	int $length - length of the returned password
	 * @return	string - password
	 *
	 */
	function randomPassword($length = 8)
	{
		$pass = "";
		
		// possible password chars.
		$chars = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			   "k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T",
			   "u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9");
			   
		for($i=0 ; $i < $length ; $i++)
		{
			$pass .= $chars[mt_rand(0, count($chars) -1)];
		}
		
		return $pass;
	}

	function Debug($msg)
	{
		if(!$this->debug) return(true); // Solo debug para usuario XX240
		$date = date('Y-m-d h:i:s',time());
		echo "<font style='font-size:11px;color:red;'>$date - ${msg}<br></font>";
	}
	
	function loginForm($msg='<font color=green><b>Introduzca usuario y password</b></font>')
	{
		$this->Debug("loginForm(): Genera el Formulario de Login.");
		if($_COOKIE['login_expiration'] == "true") $msg = "Sesion Expirada!";
		if(isset($_POST['Submit']))
		{
			$this->Debug("loginForm(): Se realizo el Submit del formulario");
			if((!$_POST['Username']) || (!$_POST['Password']))
			{
				$this->Debug("loginForm(): No se especifico usuario o Password");
			  $msg = "Por favor complete los campos.";
			}
			else
			{
				$this->Debug("loginForm(): Usuario y Password introducidos realiza el doLogin()");
				if($this->doLogin($_POST['Username'],$_POST['Password'])) // Si todo OK envia al usuario a la pagina principal
				{
					$this->SetCookieTrue();
					ob_clean();
					header("location: ".$this->redirect_page);
				}
				else
				{
					$this->Debug("loginForm(): doLogin() retorna FALSE");
				  // Redirecciona a la pagina de Login con mensaje de login incorrecto
				  $msg = "Login incorrecto!";
				}
			}
		}
		
		// Despliega el formulario de login
		?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8" />	
		<title>Login</title>
		<link    rel='stylesheet'      type='text/css' href='styles/libgsis.css'>
		<link    rel='stylesheet'      type='text/css' href='styles/themes/ui-smoothness/jquery-ui-1.10.2.custom.min.css' />
		<script type='text/javascript'                  src='js/jquery-1.9.1.min.js'></script>
		<script type='text/javascript'                  src='js/jquery-ui-1.10.2.custom.min.js'></script>
		<style  type='text/css'>
		  #fullwindow { background-color: white;bottom: 0;height: 100%;margin-left: 0;opacity: 1;position: fixed;top: 0;left:0;width: 100%;z-index: 100;}
		  #loginwindow { background-color: white; margin: 15% auto 0 auto; position: relative; width: 300px; z-index: 101; }		  
		  .titulo-span{color:#3300CC;font-size: 18px;background-color: #E5ECF9;font-weight: bold;padding: 4px;text-align: center;width: 10px;text-shadow:0px 0px 5px #6374AB;font-family: monospace;}	  
		  .top-bottom-blue{border-top: 1px solid #3366CC;border-bottom: 1px solid #3366CC;}
		  .label-span{color:#3300CC;font-weight:bold;font-family: monospace;}
		  .span-red{color:red;font-size:9px;font-family: monospace;}
		  input.ui-button { padding: 0.3em 0.2em 0.3em; }
		  strong { font-weight: normal; text-shadow: rgba(111, 179, 252, 0.85) 0 0 3px; font-size:20px; font-family: monospace; font-weight:700; text-align: center; display: block; color: #36C;}
		</style>
		<!--[if gt IE 5]>
			<style type='text/css'>
			#fullwindow { background-color: white;bottom: 0;height: 100%;opacity: 1;position: fixed;top: 0;width: 100%;z-index: 100; left: 0; }
			</style>
		<![endif]-->
	</head>
	<body>
		<div id='fullwindow'>
			<div id='loginwindow'>
				<form name='login' id='LoginFRM' method='post'>
					<table align='center' width='300' class='aad-o-table' style='border: 2px solid #36C !important;'>
						<tr class='caja' style='background-color:#FFF;'>
							<td colspan="2" style='border-bottom: 1px solid #36C;border-top:1px solid #36C;background-color:#9DBDFF;'>
								<table align='center'>
									<tr align='center'>
										<td>
											<span style='text-shadow: rgba(255, 255, 255, 0.85) 0px 0px 1px;color: white;font-weight: 700;font-size: 15px;font-family: monospace;'>Acceso al Sistema</span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class='aad-o-service' style='border-right: 0px'>
								<div align='center'>
									<span style='text-shadow: rgba(111, 179, 252, 0.85) 0 0 3px;font-size: 20px;font-family: monospace;font-weight: 700;text-align: center;color: #0856F3;font-size:11px;'>Usuario</span>
									<input type='text' name='Username' id='Username' size='30' maxlength='30' placeholder='Usuario' \>
								</div>
							</td>
							<td class='aad-o-service' style='border-right: 0px'>
								<div align='center'>
									<span style='text-shadow: rgba(111, 179, 252, 0.85) 0 0 3px;font-size: 20px;font-family: monospace;font-weight: 700;text-align: center;color: #0856F3;font-size:11px;'>Password</span>
									<input type='password' name='Password' id='Password' size='30' maxlength='30' placeholder='Password' \>
								</div>
							</td>
						</tr>
						<tr>
							<td class='aad-o-service' style='border-right: 0px;text-align:right;' colspan='2'>
								<input id='BTNLogin' name='Submit' type='submit' style='color: #0856F3;font-family: monospace;text-shadow: rgba(111, 179, 252, 0.85) 0 0 3px;' value='&nbsp;&nbsp;&nbsp;Login&nbsp;&nbsp;&nbsp;' class='dash-button'><br>
								<!-- button id='BTNLogin'>Login</button -->
							</td>
						</tr>
						<tr>
							<td class='aad-o-service' style='border-right: 0px;text-align:center;' colspan='2'>
								<div align='center' class='aad-header-text' style='font-size: 12px;font-family: monospace;'><font color='red'><?=$msg?></font></div>
							</td>
						</tr>
						<tr>
							<td class='aad-o-service' style='border-right: 0px;text-align:center;' colspan='2'>
								<div align='right'><img src='<?=$this->imgLogo?>' style='display: block;'><span style='font-size: 7px;margin-left: 0px;float: right;top: -8px;position: relative;font-weight: 700;right: 3px;'><?=$this->Version?></span></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
	<script type='text/javascript'>
		jQuery.noConflict();
		// Se ejecuta cuando el documento esta cargado
		jQuery(document).ready(function() 
		{
			// Centrar la ventana de Login
			var marginTop = jQuery('#fullwindow').height()/2-jQuery('#fullwindow').offset().top-jQuery('#loginwindow').height();
			jQuery('#loginwindow').css('marginTop',marginTop);
			jQuery("#Username").focus();
			jQuery("#Username").select();
			jQuery("#BTNLogin").button({ text: true, icons: { primary: "ui-icon-locked"}  });
		});	
	</script>
</html>
		<?php
		exit;		
	}
} // CLASS LoginSystem