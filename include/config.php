<?php
if (session_id() == ''){ session_start(); }
error_reporting(-1);

// Seccion DATABASE
define('DB_SERVER',   'localhost');                       	  // DB MySQL Server
define('DB_USER',     'nutshell');                            // DB MySQL User
define('DB_PASS',     'nutshell');                            // DB MySQL Password
define('DB_DATABASE', 'nutshell');                            // DB MySQL Name

// Recupera configuraciones dinamicas utilizando la conexion basica a MySQL de PHP
if ($_SESSION['SESSIONACTIVE'] == 0){
	echo "Carga variables de entorno y sesion!<br>";
	$con=mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	if (!$con) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); exit; }
	$result = mysqli_query($con,"SELECT REPLACE(REPLACE(mascara, '_KEY_', clave),'_VAL_',valor) AS mascara FROM tb_configuracion WHERE visible=1 ORDER BY orden;");
	while($row = mysqli_fetch_array($result)){ eval($row['mascara']); }
	mysqli_close($con);
}

// Seccion PATH
$PATH_FULL    = $_SERVER["DOCUMENT_ROOT"] . $_SESSION['APP_DOCUMENT_ROOT'];                  // Path al Document Root
$USREGISTRADO = ((isset($_SESSION['userName']) && $_SESSION['userName'])?$_SESSION['userName']:'Unknown'); // LDAP/LOCAL AUTH

// Seccion ACCESO
require_once("adodb5/adodb.inc.php");                                        // Manejo de Base de Datos
require_once("LoginSystem.class.php");
require_once("makeSecure.php");                                              // Control de Acceso al sistema
