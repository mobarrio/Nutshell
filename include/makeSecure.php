<?php
	/*
	TIADM - Copyright (c) 2012 by Mariano Jorge Obarrio Miles.

	This work is made available under the terms of licensed under a Creative Commons Atribuci�n-NoComercial-SinDerivadas 3.0 Espa�a.
	Legal Code (the full license): http://creativecommons.org/licenses/by-nc-nd/3.0/es/.

	Esta obra est� licenciada bajo la Licencia Creative Commons Atribuci�n-NoComercial-SinDerivadas 3.0 Espa�a
	Para ver una copia de esta licencia, visita http://creativecommons.org/licenses/by-nc-nd/3.0/es/.

	C�digo Safe Creative: #1211060715189
	*/
	require_once("include/config.php");
	require_once("include/LoginSystem.class.php");
	
	isLocalAccess($_SESSION['DOMACCESS']);

	$loginSys = new LoginSystem();
	$loginSys->debug = 0;
	if(!$loginSys->isLoggedIn()){ $loginSys->loginForm(); exit;}
	$_SESSION['AccessLevel'] = $loginSys->AccessLevel;
	
	