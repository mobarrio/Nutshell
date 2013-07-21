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
	case "add_update_cliente":			add_update_cliente();				break;
	case "delete_cliente":				delete_cliente();					break;	
	case "nuevoCliente":				nuevoCliente();						break;	
	default:																break;
	return(-1);
} 

function admusr(){
	global $connMySQL;
	$records = $connMySQL->GetAll("SELECT * FROM tb_usuarios;");
	?>
	<form id='frmclientes'>
		<table class="table table-bordered table-striped">
			<tr>
				<th class='span2' style='text-align:center;'>UserID</th>
				<th class='span3'>Descripcion</th>
				<th class='span3' style='text-align:center;'>Mail</th>
				<th class='span1' style='text-align:center;'>Estado</th>
				<th class='span1' style='text-align:center;'>Level</th>
				<th class='span2'>Acciones</th>
			</tr>	  
			<? foreach($records as $record): ?>
			<tr id='tr-<?=$record['idUsuario']?>'>
				<td style='text-align:center;'><?=$record['idUsuario']?></td>
				<td>
					<input type='text' id="nombre<?=$record['idUsuario']?>" name='nombre<?=$record['idUsuario']?>' class='span12 nombre' value="<?=$record['Descripcion']?>" placeholder='UserID' data-idusuario="<?=$record['idUsuario']?>" minlength="4" required />
				</td>
				<td style='text-align:center;'>
					<input type='text' id="mail<?=$record['idUsuario']?>" name='mail<?=$record['idUsuario']?>' class='span12 mail' value="<?=$record['Mail']?>" placeholder='Mail Address' data-idusuario="<?=$record['idUsuario']?>" minlength="4" required />
				</td>
				<td style='text-align:center;'>
					ON/OFF
				</td>
				<td style='text-align:center;'>
					<select>
						<option value='0'>Administrador</option>
						<option value='1' Selected>Nominal</option>
					</select>
				</td>
				<td style='text-align:center;'>
					<a class='btn delete' href='#' data-idusuario='<?=$record['idUsuario']?>'><i class='icon-fixed-width icon-trash'></i> Eliminar</a>
				</td>
			</tr>	  
			<? endforeach; ?>
			<tr>
				<td colspan='5'>
				</td>
				<td style='text-align:center;' class='nueva'>
					<a class="btn" href="#"><i class='icon-plus'></i>&nbsp;&nbsp;Nuevo Usuario</a>
				</td>
			</tr>	  
		</table>
	</form>
	<script>
		function nuevoCliente(){
			var idUsuario = "";

			$.ajax({ 
				url: 'sbin/clientesfunc.php',
				type: 'POST',
				async: false,
				dataType: 'json',
				data: { 'accion' : 'nuevoCliente' },
				success: function(data) { idUsuario = data.idUsuario }
			});
			return([idUsuario]);
		}

		function addTinyMCE(id){
		    if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
		        tinyMCE.execCommand('mceAddEditor', false, id);
		    }
		}

		function deldTinyMCE(id){
		    if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
		        tinyMCE.execCommand('mceRemoveEditor', false, id);
		    }
		}

		$(document).ready(function() {
			/* Inicializacion del plugin de TinyMCE con MOXIECUT */
			tinymce.PluginManager.load('moxiecut', '../js/plugins/tinymce/plugins/moxiecut/plugin.min.js');
			tinymce.init({
				selector: ".moxiecut",
				theme: "modern",
				language : 'es',
				plugins: [
					"save advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste moxiecut"
				],
				menubar : false,
				statusbar : false,
				toolbar: "save | styleselect | undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link insertfile media | inserttime | preview code",
				autosave_ask_before_unload: false,
				save_enablewhendirty: true,
    			save_onsavecallback: function(ed){
    				var idUsuario = $("#"+$(this).attr("id")).data("idusuario");

					// tinyMCE.triggerSave();
    				tinyMCE.triggerSave();

    				if(!$("#frmclientes").valid()) return false;
    				var nombre = $('#nombre'+idUsuario).val();
					$.ajax({ 
						url: 'sbin/clientesfunc.php', 
						type: 'POST', 
						data: { 
							'accion' : 'add_update_cliente',
							'idUsuario' : idUsuario,
							'nombre' : nombre,
							'contenido' : ed.getContent()
						},
						success: function(resp) { jAlert("Los datos se guardaron correctamente.","ATENCION", null, 1000); }
					});
    			},
				height: 280,
				relative_urls : false,
				remove_script_host : false,
				document_base_url : "http://localhost/TimeSheet",
				convert_urls : true,
			});

			$('#frmclientes').on('click', '.info', function(event) {
				event.preventDefault;
				$this = $(this);
				/* Montrar el contenido */
				$("#info"+$this.data('id')).toggle('showOrHide');
			});

			$('#frmclientes').on('change', '.nombre', function(event) {
				event.preventDefault;
				$this = $(this);
				if(!$("#frmclientes").valid()) return false;
				var idUsuario = $(this).data('idusuario');
				var nombre = $(this).val();
				$.ajax({ 
					url: 'sbin/clientesfunc.php', 
					type: 'POST', 
					data: { 
						'accion' : 'add_update_cliente',
						'idUsuario' : idUsuario,
						'nombre' : nombre
					},
					success: function(resp) { jAlert("Los datos se guardaron correctamente.","ATENCION", null, 1000); }
				});
			});

			$('#frmclientes').on('click', '.delete', function(event) {
				event.preventDefault;
				$this = $(this);
				var idUsuario = $this.data('idusuario');
				jConfirm("Se eliminara permanentemente el cliente.\n\nConfirma la operacion?","ATENCION", 	function (ans) { 
					if (ans) 
					{					
						$.ajax({ 
							url: 'sbin/clientesfunc.php',
							type: 'POST',
							async: false,
							dataType: 'json',
							data: { 
								'accion' : 'delete_cliente',
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
			});

			$(".nueva").click(function(event) {
				event.preventDefault();
				$this = $(this);
				var retVal     = nuevoCliente();
				var idUsuario =  retVal[0];
				//var clone = $('#frmclientes tr:last').prev();

				$(  "<tr id='tr-"+idUsuario+"'>"+
					"   <td style='text-align:center;'>"+idUsuario+"</td>"+
					"   <td>"+
					"   	<input type='text' id='nombre"+idUsuario+"' name='nombre"+idUsuario+"' class='span12 nombre' value='' placeholder='Nombre del cliente' data-idusuario='"+idUsuario+"'   minlength='4' required />"+
					"   	<div id='info"+idUsuario+"' name='info"+idUsuario+"' style='display:none;'>"+
					"   		<textarea class='moxiecut span12' style='height: 280px;' id='contenido"+idUsuario+"' name='contenido"+idUsuario+"' data-idusuario='"+idUsuario+"'></textarea>"+
					"   	</div>"+
					"   </td>"+
					"   <td style='text-align:center;'><i class='icon-file-text-alt icon-2x info' style='cursor:pointer;' data-id='"+idUsuario+"'></i></td>"+
					"   <td style='text-align:center;'>"+
					"   	<a class='btn delete' href='#' data-idusuario='"+idUsuario+"'><i class='icon-fixed-width icon-trash'></i> Eliminar</a>"+
					"   </td>"+
					"</tr>").insertBefore('#frmclientes tr:last');
				 addTinyMCE('contenido'+idUsuario);
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

function add_update_cliente(){
	$idUsuario  = isset($_REQUEST['idUsuario']) ? mysql_real_escape_string($_REQUEST['idUsuario']) : '';
	$nombre      = isset($_REQUEST['nombre'])     ? mysql_real_escape_string($_REQUEST['nombre'])     : '';
	$contenido   = isset($_REQUEST['contenido'])  ? mysql_real_escape_string($_REQUEST['contenido'])  : '';

	$field1 = '';
	$value1 = '';
	$ondup1 = '';
	$field2 = '';
	$value2 = '';
	$ondup2 = '';
	if(!empty($idUsuario)) { $field1='idUsuario,'; $value1 = "'$idUsuario', ";}
	if(!empty($contenido))  { $field2=', Contacto';  $value2 = ", '$contenido'"; $ondup2 = ", Contacto='$contenido'";}
	//$GLOBALS['connMySQL']->debug = true;
	$GLOBALS['connMySQL']->Execute("INSERT INTO ts_clientes($field1 Descripcion $field2) VALUES ($value1 '$nombre' $value2) ON DUPLICATE KEY UPDATE idUsuario='$idUsuario', Descripcion='$nombre' $ondup2;");
	echo "Los datos se actualizaron correctamente";
}

function nuevoCliente(){
	global $connMySQL;

	$connMySQL->Execute("INSERT INTO ts_clientes (Descripcion, Contacto) VALUES ('', '');");
	$idUsuario = $connMySQL->Insert_ID();
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=UTF-8');
	echo '{ "idUsuario": "'.$idUsuario.'" }';	
}


function delete_cliente(){
	global $connMySQL;

	$idUsuario  = isset($_REQUEST['idUsuario']) ? mysql_real_escape_string($_REQUEST['idUsuario']) : '';
	if(empty($idUsuario)) {
		$rJson = '{ "status" : "error", "msg": "Cliente no definido", "idUsuario" : "'.$idUsuario.'" }';
	}else{
		$connMySQL->Execute("DELETE FROM ts_clientes WHERE idUsuario='$idUsuario';");
		$rJson = '{ "status" : "success", "msg": "Cliente eliminado correctamente", "idUsuario" : "'.$idUsuario.'" }';
	}
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=UTF-8');
	echo $rJson;	

}
