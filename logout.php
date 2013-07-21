<?php
	/*
	TIADM - Copyright (c) 2012 by Mariano Jorge Obarrio Miles.

	This work is made available under the terms of licensed under a Creative Commons Atribución-NoComercial-SinDerivadas 3.0 España.
	Legal Code (the full license): http://creativecommons.org/licenses/by-nc-nd/3.0/es/.

	Esta obra está licenciada bajo la Licencia Creative Commons Atribución-NoComercial-SinDerivadas 3.0 España
	Para ver una copia de esta licencia, visita http://creativecommons.org/licenses/by-nc-nd/3.0/es/.

	Código Safe Creative: #1211060715189
	*/
	error_reporting(0);
	if (session_id() == ''){ session_start(); }
	require_once(dirname(__FILE__) . "/include/config.php");

	$go = $_SESSION['PATH_URL'] . "/index.php";
	$loginSys = new LoginSystem();
	$loginSys->logout();
	
	// Make sure file is not cached (as it happens for example on iOS devices)
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("location: ".$go);