<?php
// Seccion INCLUDES
require_once(dirname(__FILE__) . "/../include/config.php"); // Configuracion general
error_reporting(-1);

/*
Autor:								Exit
script de administración para:		Cine Landowski
tecnologías: 						HTML, mySQL y PHP.
fecha de creación: 					20 de Septiembre de 2012
*/

register_shutdown_function('cierre');

// -- Conexion con la DB
if(!isset($connMySQL)){
	$connMySQL = NewADOConnection('mysql');
	$connMySQL->Connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$connMySQL->SetFetchMode(ADODB_FETCH_ASSOC);
	$connMySQL->EXECUTE("set names 'utf8'");
	$connMySQL->debug=false;
}

$accion      = (isset($_REQUEST['accion'])		?	$_REQUEST['accion']					:	'');
$apartado    = (isset($_REQUEST['apartado'])	?	urldecode($_REQUEST['apartado'])	:	''); // No se utiliza actualmente!!!
$id_film     = (isset($_REQUEST['id_film'])		?	urldecode($_REQUEST['id_film'])		:	'');
$data        = (isset($_REQUEST['data'])		? 	urldecode($_REQUEST['data'])        : 'nada');
$date 		 = date('Y-m-d h:i:s',time());

switch ($accion)
{
	case "admusr":            			admusr();                 			break;
	case "pref":            			pref();                 			break;
	case "add_update_usuario":			add_update_usuario();				break;
	case "delete_usuario":				delete_usuario();					break;	
	case "nuevoUsuario":				nuevoUsuario();						break;	
	case "actualizarUsuario":			actualizarUsuario();				break;	
	default:																break;
	return(-1);
} 

function pref(){
	global $connMySQL;
	$datos = $connMySQL->GetAll("SELECT * FROM tb_configuracion WHERE parametrizable=1 ORDER BY orden;");
	?>
	<form id='frmparametros'>
		<div class="row-fluid">
			<div class='span12'><h3>Prametrizacion del sistema</h3></div>
		</div>
		<div class="row-fluid">
			<hr class='span6' style='margin-top: 0px;margin-bottom: 0px;'>
		</div>
		<? foreach($datos as $row): ?>
		<div class="row-fluid">
		  <div class="span2"><?=$row["desc"]?></div>
		  <div class="span4"><input type='text' class='span12 change' id='<?=$row["clave"]?>' name='<?=$row["clave"]?>' value='<?=$row["valor"]?>' /></div>
		  <div class="span6"><span style='color:red;'><?=(!empty($row["nota"]) ? '('.$row["nota"].')' : '')?></span></div>
		</div>
		<? endforeach; ?>
	</form>
	<script>
		$(document).ready(function() {
			$('#frmparametros').on('change', '.change', function(event) {
				event.preventDefault;
				$this = $(this);
				var clave = $this.attr('id');
				var valor = $this.val()
				console.log("Clave : ["+clave+"] Valor : ["+valor+"]");
			});

		});
	</script>
	<?
}

function admusr(){
	global $connMySQL;
	$records = $connMySQL->GetAll("SELECT * FROM tb_usuarios;");
	?>
	<form id='frmusuarios'>
		<div class="row-fluid">
			<div class='span12'><h3>Listado de Usuarios</h3></div>
		</div>
		<table class="table table-bordered table-striped">
			<tr>
				<th class='span1' style='text-align:center;'>#</th>
				<th class='span2' style='text-align:center;'>UserID</th>
				<th class='span5'>Descripcion</th>
				<th class='span1' style='text-align:center;'>Estado</th>
				<th class='span3'>Acciones</th>
			</tr>	  
			<? foreach($records as $record): ?>
			<tr id='tr-<?=$record['idUsuario']?>'>
				<td style='text-align:center;'><?=$record['idUsuario']?></td>
				<td style='text-align:center;'><?=$record['Logname']?></td>
				<td>
					<?=$record['Descripcion']?>
				</td>
				<td style='text-align:center;'>
					<?=($record['active'] ? 'Activo' : 'Desactivado')?>
				</td>
				<td style='text-align:center;'>
					<a class='btn accion' href='#' data-accion='change' data-idusuario='<?=$record['idUsuario']?>'><i class='icon-fixed-width icon-edit'></i> Actualizar</a>
					<a class='btn accion' href='#' data-accion='delete' data-idusuario='<?=$record['idUsuario']?>'><i class='icon-fixed-width icon-trash'></i> Eliminar</a>
				</td>
			</tr>	  
			<? endforeach; ?>
			<tr>
				<td colspan='4'>
				</td>
				<td style='text-align:center;'>
					<a class="btn nueva" href="#"><i class='icon-plus'></i>&nbsp;&nbsp;Nuevo Usuario</a>
				</td>
			</tr>	  
		</table>
	</form>
	<script>
		$(document).ready(function() {

			$('#frmusuarios').on('click', '.accion', function(event) {
				event.preventDefault;
				$this = $(this);
				var idUsuario = $this.data('idusuario');
				var Accion    = $this.data('accion');
				if(Accion  === 'change'){
					$.ajax({ 
						url: 'sbin/configuracionfunc.php',
						type: 'POST',
						data: { accion : 'actualizarUsuario', idUsuario: idUsuario },
						beforeSend: function ( data ) { $("#contentenido").html("<div style='text-align: left;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
						success: function(resp)       { $("#contentenido").hide().html(resp).fadeIn(250); }
					});
				}else if(Accion  === 'delete'){
					jConfirm("Se eliminara permanentemente el usuario.\n\nConfirma la operacion?","ATENCION", 	function (ans) { 
						if (ans) 
						{					
							$.ajax({ 
								url: 'sbin/configuracionfunc.php',
								type: 'POST',
								async: false,
								dataType: 'json',
								data: { 
									'accion' : 'delete_usuario',
									'idUsuario' : idUsuario
								},
								success: function(data) { 
									if(data.status === 'success'){
										$("#tr-"+data.idUsuario).remove();
										jAlert(data.msg,"ATENCION", null, 1000);
									}else{
										jAlert(data.msg,"ERROR", null);
									}
									
								}
							});
						}
					});
				}
			});

			$(".nueva").click(function(event) {
				event.preventDefault();
				$this = $(this);
				$.ajax({ 
					url: 'sbin/configuracionfunc.php',
					type: 'POST',
					data: { 'accion' : 'nuevoUsuario' },
					beforeSend: function ( data ) { $("#contentenido").html("<div style='text-align: left;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
					success: function(resp)       { $("#contentenido").hide().html(resp).fadeIn(250); }
				});

			});


		}); <!-- Document.Ready -->
	
	</script>
	<?
}

/*
  Function    : cierre
  Parametros  : Nada
  Retorna     : Nada
  Descripcion : Esta funcion cierra la conexion a la DB al cerrar la pagina PHP
  Autor       : Exit
  Fecha       : 2013-04-15 09:00
*/
function cierre(){ 
	if($GLOBALS['connMySQL'])  { $GLOBALS['connMySQL']->Close();  }
}

function add_update_usuario(){
	$idUsuario   = isset($_REQUEST['idUsuario']) ? mysql_real_escape_string($_REQUEST['idUsuario']) : '';
	$Logname     = isset($_REQUEST['userid']) ? mysql_real_escape_string($_REQUEST['userid']) : '';
	$Descripcion = isset($_REQUEST['desc']) ? mysql_real_escape_string($_REQUEST['desc']) : '';
	$Passwd      = isset($_REQUEST['passwd1']) ? md5(mysql_real_escape_string($_REQUEST['passwd1'])) : '';	
	$Mail        = isset($_REQUEST['mail']) ? mysql_real_escape_string($_REQUEST['mail']) : '';
	$Movil       = isset($_REQUEST['movil']) ? mysql_real_escape_string($_REQUEST['movil']) : '';
	$active      = isset($_REQUEST['activo']) ? mysql_real_escape_string($_REQUEST['activo']) : 0;
	$AccessLevel = isset($_REQUEST['accesslevel']) ? mysql_real_escape_string($_REQUEST['accesslevel']) : -1;

	$field = '';
	$value = '';
	if(!empty($idUsuario)) { $field='idUsuario,'; $value = "'$idUsuario', ";}

	//$GLOBALS['connMySQL']->debug = true;
	$GLOBALS['connMySQL']->Execute("INSERT INTO tb_usuarios($field Logname, Descripcion, Passwd, Mail, Movil, active, AccessLevel) VALUES ($value '$Logname', '$Descripcion', '$Passwd', '$Mail', '$Movil', '$active', '$AccessLevel') ON DUPLICATE KEY UPDATE Logname='$Logname', Descripcion='$Descripcion', Passwd='$Passwd', Mail='$Mail', Movil='$Movil', active='$active', AccessLevel='$AccessLevel';");
	admusr();
}

function actualizarUsuario(){
	$idUsuario = isset($_REQUEST['idUsuario']) ? mysql_real_escape_string($_REQUEST['idUsuario']) : '';
	$row = $GLOBALS['connMySQL']->GetRow("Select * from tb_usuarios Where idUsuario='$idUsuario';");
	ob_start();
	?>
	<form id='frmaddusuarios'>
		<div class="row-fluid">
			<div class='span12'><h3>Actualizar datos del usuario</h3></div>
		</div>
		<div class="row-fluid">
			<hr class='span6' style='margin-top: 0px;margin-bottom: 0px;'>
		</div>
		<div class="row-fluid">
		  <div class="span2">UserID</div>
		  <div class="span4"><input type='text' class='span12' id='userid' name='userid' value='<?=$row["Logname"]?>' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Descripcion</div>
		  <div class="span4"><input type='text' class='span12' id='desc' name='desc' value='<?=$row["Descripcion"]?>' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Password</div>
		  <div class="span4"><input type='password' class='span12' id='passwd1' name='passwd1' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Confirmar Password</div>
		  <div class="span4"><input type='password' class='span12' id='passwd2' name='passwd2' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Mail</div>
		  <div class="span4"><input type='text' class='span12' id='mail' name='mail' value='<?=$row["Mail"]?>' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Movil</div>
		  <div class="span4"><input type='text' class='span12' id='movil' name='movil' value='<?=$row["Movil"]?>' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Habilitado</div>
		  <div class="span4">
		  	<select class='span12' id='activo' name='activo'>
				<option value='0' <?=$row["active"] == '0' ? 'Selected' : ''?>>Desactivado</option>
				<option value='1' <?=$row["active"] == '1' ? 'Selected' : ''?>>Activado</option>
			</select>
		  </div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Nivel de Acceso</div>
		  <div class="span4">
			<select class='span12' id='accesslevel' name='accesslevel'>
				<option value='0' <?=$row["AccessLevel"] == '0' ? 'Selected' : ''?>>Administrador</option>
				<option value='1' <?=$row["AccessLevel"] == '1' ? 'Selected' : ''?>>Nominal</option>
			</select>
		  </div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
			<hr class='span6' style='margin-top: 0px;margin-bottom: 0px;'>
		</div>
		<div class="row-fluid">
		  <div class="span2"></div>
		  <div class="span4" style='text-align:right;'>
			<a class="btn update"   href="#"><i class='icon-ok'></i>&nbsp;&nbsp;Actualizar</a>		
			<a class="btn cancelar" href="#"><i class='icon-remove'></i>&nbsp;&nbsp;Cancelar</a>		
		  </div>
		  <div class="span6"></div>
		</div>
		<input name="accion" type="hidden" value="add_update_usuario"/>
		<input name="idUsuario" type="hidden" value="<?=$idUsuario?>"/>
	</form>	
	<script type="text/javascript">
		$(document).ready(function() {
			var validator = $("#frmaddusuarios").validate({ 
				rules: { 
					userid  : { required: true,   minlength: 4 }, 
					desc    : { required: true,   minlength: 4 },
					passwd1 : { required: true,   minlength: 6 },
					passwd2:  { required: true,   minlength: 6, equalTo: "#passwd1" }
				}, 
				messages: { 
					passwd1 :" Entre una contraseña", 
					passwd2 :" Confirme la misma contraseña." 
				} 
			});

			$('#frmaddusuarios').on('click', '.update', function(event) {
				event.preventDefault;
				$this = $(this);
				if(!$("#frmaddusuarios").valid()) return false;

				jConfirm("Se creara un nuevo usuario.\n\nConfirma la operacion?","ATENCION", 	function (ans) { 
					if (ans) 
					{ 
						$.ajax({ 
							url: 'sbin/configuracionfunc.php', 
							type: 'POST', 
							data: $("#frmaddusuarios").serialize(),
							beforeSend: function ( data ) { $("#contentenido").html("<div style='text-align:left;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
							success: function(resp)       { $("#contentenido").hide().html(resp).fadeIn(250); }
						});
						
					} 
				});
			});
			$('#frmaddusuarios').on('click', '.cancelar', function(event) {
				event.preventDefault;
				$this = $(this);
				$.ajax({ 
					url: 'sbin/configuracionfunc.php', 
					type: 'POST', 
					data: { 'accion' : 'admusr'	},
					beforeSend: function ( data ) { $("#contentenido").html("<div style='text-align:left;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
					success: function(resp)       { $("#contentenido").hide().html(resp).fadeIn(250); }
				});
			});
		});
	</script>
	<?
    $html = ob_get_clean();
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header("Content-type: text/html; charset=UTF-8");
    echo $html;
}


function nuevoUsuario(){
	ob_start();
	?>
	<form id='frmaddusuarios'>
		<div class="row-fluid">
			<div class='span12'><h3>Creacion de nuevo usuario</h3></div>
		</div>
		<div class="row-fluid">
			<hr class='span6' style='margin-top: 0px;margin-bottom: 0px;'>
		</div>
		<div class="row-fluid">
		  <div class="span2">UserID</div>
		  <div class="span4"><input type='text' class='span12' id='userid' name='userid' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Descripcion</div>
		  <div class="span4"><input type='text' class='span12' id='desc' name='desc' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Password</div>
		  <div class="span4"><input type='password' class='span12' id='passwd1' name='passwd1' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Confirmar Password</div>
		  <div class="span4"><input type='password' class='span12' id='passwd2' name='passwd2' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Mail</div>
		  <div class="span4"><input type='text' class='span12' id='mail' name='mail' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Movil</div>
		  <div class="span4"><input type='text' class='span12' id='movil' name='movil' /></div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Habilitado</div>
		  <div class="span4">
		  	<select class='span12' id='activo' name='activo'>
				<option value='0'>Desactivado</option>
				<option value='1' Selected>Activado</option>
			</select>
		  </div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
		  <div class="span2">Nivel de Acceso</div>
		  <div class="span4">
			<select class='span12' id='accesslevel' name='accesslevel'>
				<option value='0'>Administrador</option>
				<option value='1' Selected>Nominal</option>
			</select>
		  </div>
		  <div class="span6"></div>
		</div>
		<div class="row-fluid">
			<hr class='span6' style='margin-top: 0px;margin-bottom: 0px;'>
		</div>
		<div class="row-fluid">
		  <div class="span2"></div>
		  <div class="span4" style='text-align:right;'>
			<a class="btn update"   href="#"><i class='icon-plus'></i>&nbsp;&nbsp;Crear Usuario</a>		
			<a class="btn cancelar" href="#"><i class='icon-remove'></i>&nbsp;&nbsp;Cancelar</a>		
		  </div>
		  <div class="span6"></div>
		</div>
		<input name="accion" type="hidden" value="add_update_usuario"/>
	</form>	
	<script type="text/javascript">
		$(document).ready(function() {
			var validator = $("#frmaddusuarios").validate({ 
				rules: { 
					userid  : { required: true,   minlength: 4 }, 
					desc    : { required: true,   minlength: 4 },
					passwd1 : { required: true,   minlength: 6 },
					passwd2:  { required: true,   minlength: 6, equalTo: "#passwd1" }
				}, 
				messages: { 
					passwd1 :" Entre una contraseña", 
					passwd2 :" Confirme la misma contraseña." 
				} 
			});

			$('#frmaddusuarios').on('click', '.update', function(event) {
				event.preventDefault;
				$this = $(this);
				if(!$("#frmaddusuarios").valid()) return false;

				jConfirm("Se creara un nuevo usuario.\n\nConfirmar.","ATENCION", 	function (ans) { 
					if (ans) 
					{ 
						$.ajax({ 
							url: 'sbin/configuracionfunc.php', 
							type: 'POST', 
							data: $("#frmaddusuarios").serialize(),
							beforeSend: function ( data ) { $("#contentenido").html("<div style='text-align:left;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
							success: function(resp)       { $("#contentenido").hide().html(resp).fadeIn(250); }
						});
						
					} 
				});
			});
			$('#frmaddusuarios').on('click', '.cancelar', function(event) {
				event.preventDefault;
				$this = $(this);
				$.ajax({ 
					url: 'sbin/configuracionfunc.php', 
					type: 'POST', 
					data: { 'accion' : 'admusr'	},
					beforeSend: function ( data ) { $("#contentenido").html("<div style='text-align:left;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
					success: function(resp)       { $("#contentenido").hide().html(resp).fadeIn(250); }
				});
			});
		});
	</script>
	<?
    $html = ob_get_clean();
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header("Content-type: text/html; charset=UTF-8");
    echo $html;
}

function delete_usuario(){
	global $connMySQL;

	$idUsuario  = isset($_REQUEST['idUsuario']) ? mysql_real_escape_string($_REQUEST['idUsuario']) : '';

	if(empty($idUsuario)) {
		$rJson = '{ "status" : "error", "msg": "Usuario no definido", "idUsuario" : "'.$idUsuario.'" }';
	}else{
		$connMySQL->Execute("DELETE FROM tb_usuarios WHERE idUsuario='$idUsuario';");
		$rJson = '{ "status" : "success", "msg": "Usuario eliminado correctamente", "idUsuario" : "'.$idUsuario.'" }';
	}
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=UTF-8');
	echo $rJson;	

}
