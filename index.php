<?php 
	error_reporting(0);
	$time = time() - 60; // or filemtime($fn), etc
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time).' GMT');
	require_once("include/config.php");
?>

<!DOCTYPE html>
<html lang="en">
	<head>
        <title><?=$_SESSION['APPNAME']?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta name="description" content="<?=$_SESSION['APPNAME']?>">
    	<meta name="author" content="<?=$_SESSION['COPYRIGHT']?>">
		<!-- Estilos personalizados -->

		<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome-ie7.min.css" rel="stylesheet">

		<!-- jQuery y jQuery-UI -->
		<script type='text/javascript'             src='js/jquery-1.9.1.min.js'></script>
		<script type='text/javascript'             src='js/jquery-ui-1.10.2.custom.min.js'></script>
		<script type='text/javascript'             src='js/ui/i18n/jquery.ui.datepicker-es.js'></script>
		<link   type='text/css' rel='stylesheet'  href='styles/themes/ui-smoothness/jquery-ui-1.10.2.custom.min.css' />
		<link   type='text/css' rel='stylesheet'  href='styles/themes/jquery.ui.selectmenu.css' />

		<script type="text/javascript" 			   src="js/bootstrap.min.js"></script> 

		<!-- Dropdown Menu HTML5 -->
		<link href="js/html5/css/dropdown/dropdown.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="js/html5/css/dropdown/themes/nvidia.com/default.advanced.css" media="screen" rel="stylesheet" type="text/css" />
		
		<!-- Plugins de jQuery -->
		<script type='text/javascript'             src='js/jquery.ui.selectmenu.js'></script>
		<script type='text/javascript'             src='js/plugins/timepicker/jquery.ui.timepicker.js'></script>
		<script type='text/javascript'             src='js/plugins/timepicker/i18n/jquery.ui.timepicker-fr.js'></script>
		<link   type='text/css' rel='stylesheet'  href='js/plugins/timepicker/jquery.ui.timepicker.css' />
		<script type='text/javascript'             src='js/plugins/dialogs/jquery.alerts.js'></script>
		<link   type='text/css' rel='stylesheet'  href='js/plugins/dialogs/jquery.alerts.css' />
		<script type='text/javascript'             src="js/plugins/validation/jquery.validate.min.js"></script>
		<script type='text/javascript'             src="js/plugins/validation/additional-methods.min.js"></script>
		<script type='text/javascript'             src="js/plugins/validation/localization/messages_es.js"></script>
		<script type='text/javascript'             src="js/plugins/idle-timer/jquery.idle-timer.js"></script>
		<script type="text/javascript" 			   src="js/plugins/fileupload/jquery.form.js"></script> 
		<script type="text/javascript" 			   src="js/plugins/tinymce/tinymce.min.js"></script> 
		<script type="text/javascript" 			   src="js/plugins/tinymce/jquery.tinymce.min.js"></script> 
		<script type="text/javascript" 			   src="js/plugins/mobarrio/timer/jquery.stopwatch.js"></script> 
	
		<style>
			@media (min-width: 980px) {
				body{ padding-top: 60px; }
				#principal  { margin: 0 auto 0 auto; width: 900px; }
				#top        { height: 33px; float: left; text-align: center; width: 900px; background-color: black; }
				#top div    { top: 0px; position: relative; }
				#content    { width: 900px; float: left; min-height: 750px; }
				#bottom     { height: 45px; width: 900px; float: left; text-align: center; }
				#bottom div { top: 10px; position: relative; }
				.loading    { width: 900px; margin: 0 auto 0 auto; float: left; text-align: center; }
			}
			/* Landscape phones and down */
			@media(max-width: 480px){
				body { font-family: 'Open Sans', Helvetica, Arial, sans-serif; }
				#principal  { margin: 0 auto 0 auto; width: 480px; }
				#top        { height: 33px; float: left; text-align: center; width: 480px; background-color: black; }
				#top div    { top: 0px; position: relative; }
				#content    { width: 480px; float: left; min-height: 750px; }
				#bottom     { height: 45px; width: 480px; float: left; text-align: center; }
				#bottom div { top: 10px; position: relative; }
				.loading    { width: 480px; margin: 0 auto 0 auto; float: left; text-align: center; }
			}

		</style>
	</head>
    <body>
	    <div class="navbar navbar-inverse navbar-fixed-top">
	      <div class="navbar-inner">
	        <div class="container">
	          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="brand" href="./index.php"><?=$_SESSION["APPNAME"]?></a>
	          <div class="nav-collapse collapse">
	            <ul class="nav">
	              <li class="active dropdown">
	                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menu <b class="caret"></b></a>
	                <ul class="dropdown-menu">
						<li><a href="#" class='opcion1'><i class="icon-user"></i> Opcion 1</a></li>
						<li><a href="#" class='opcion2'><i class="icon-tasks"></i> Opcion 2</a></li>
						<li class="dropdown-submenu">
						  <a href="#"><i class="icon-list-ul"></i> Opcion 3</a>
						  <ul class="dropdown-menu">
						    <li><a href="#" class='opcion31' data-tipo='Activas'>Opcion 3.1</a></li>
						    <li><a href="#" class='opcion32' data-tipo='Archivadas'>Opcion 3.2</a></li>
						  </ul>
						</li>
						<li><a href="#" class='opcion4'><i class="icon-eur"></i> Opcion 4</a></li>
						<li><a href="#" class='opcion5'><i class="icon-cogs"></i> Opcion 5</a></li>
	                </ul>
	              </li>
	              <? if($_SESSION['AccessLevel'] == 0): ?>
	              <li class="active dropdown">
	                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Configuración <b class="caret"></b></a>
	                <ul class="dropdown-menu">
						<li><a href="#" class='opcion1'><i class="icon-user"></i> Usuarios</a></li>
						<li><a href="#" class='opcion2'><i class="icon-cogs"></i> Preferencias</a></li>
	            	</ul>
				  </li>
	        	  <? endif; ?>
				  <li><a href="#" class='logout'><i class="icon-lock"></i> Logout</a></li>
	            </ul>
	          </div><!--/.nav-collapse -->
	        </div>
	      </div>
	    </div>

	    <div class="container">

	      <div class="row-fluid">
	        <div id='contentenido' class="span12">
	        		<p>Pagina principal</p>
	        </div>
	      </div>

	      <hr>

	      <footer id='bottom'></footer>

	    </div> <!-- /container -->
    </body>
</html>
<script> 
	var stimeout = <?=$_SESSION["APPSESTIMEOUT"]?>;
	
	$(document).ready(function() { 
		/* Definimos el TimeOut de la pagina */
		$(document).bind("idle.idleTimer", function(){ window.location = '<?=$_SESSION["PATH_URL"].$_SESSION["LOGOUTPAGE"]?>'; });
		$.idleTimer(1000*stimeout);		

		/* Cargamos el contenido de Menu, cuerpo principal y pie de pagina */
		$('.opcion1').on('click',function(event){
			$this = $(this);
			event.preventDefault();
			// loadOpcion($this.data('tipo'));
		});

		$('.logout').on('click',function(event){
			$this = $(this);
			event.preventDefault();
			window.location = '<?=$_SESSION["PATH_URL"].$_SESSION["LOGOUTPAGE"]?>';
		});
		

		$.ajax({
		  url: "footer.php",
		  beforeSend: function ( data ) { $("#bottom").html("<div class='loading'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
		  success: function(data) { $("#bottom").html(data); }
		});		
	}); 
</script> 
