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
	$records = $connMySQL->GetAll("SELECT * FROM ts_clientes;");
	?>
	<form id='frmclientes'>
		<table class="table table-bordered table-striped">
			<tr id='tr-<?=$record['id_cliente']?>'>
				<th class='span1' style='text-align:center;'>#</th>
				<th class='span8'>Descripcion</th>
				<th class='span1' style='text-align:center;'>Info</th>
				<th class='span2'>Acciones</th>
			</tr>	  
			<? foreach($records as $record): ?>
			<tr>
				<td style='text-align:center;'><?=$record['id_cliente']?></td>
				<td>
					<input type='text' id="nombre<?=$record['id_cliente']?>" name='nombre<?=$record['id_cliente']?>' class='span12 nombre' value="<?=$record['Descripcion']?>" placeholder='Nombre del cliente' data-idcliente="<?=$record['id_cliente']?>" minlength="4" required />
					<div id="info<?=$record['id_cliente']?>" name="info<?=$record['id_cliente']?>" style='display:none;'>
						<textarea class="moxiecut span12" style='height: 280px;' id="contenido<?=$record['id_cliente']?>" name="contenido<?=$record['id_cliente']?>" data-idcliente="<?=$record['id_cliente']?>"><?=$record['Contacto']?></textarea>
					</div>
				</td>
				<td style='text-align:center;'><i class='icon-file-text-alt icon-2x info' style='cursor:pointer;' data-id='<?=$record['id_cliente']?>'></i></td>
				<td style='text-align:center;'>
					<a class='btn delete' href='#' data-idcliente='<?=$record['id_cliente']?>'><i class='icon-fixed-width icon-trash'></i> Eliminar</a>
				</td>
			</tr>	  
			<? endforeach; ?>
			<tr>
				<td colspan='3'>
				</td>
				<td style='text-align:center;' class='nueva'>
					<a class="btn" href="#"><i class='icon-plus'></i>&nbsp;&nbsp;Nuevo Cliente</a>
				</td>
			</tr>	  
		</table>
	</form>
	<script>
		function nuevoCliente(){
			var id_cliente = "";

			$.ajax({ 
				url: 'sbin/clientesfunc.php',
				type: 'POST',
				async: false,
				dataType: 'json',
				data: { 'accion' : 'nuevoCliente' },
				success: function(data) { id_cliente = data.id_cliente }
			});
			return([id_cliente]);
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
    				var id_cliente = $("#"+$(this).attr("id")).data("idcliente");

					// tinyMCE.triggerSave();
    				tinyMCE.triggerSave();

    				if(!$("#frmclientes").valid()) return false;
    				var nombre = $('#nombre'+id_cliente).val();
					$.ajax({ 
						url: 'sbin/clientesfunc.php', 
						type: 'POST', 
						data: { 
							'accion' : 'add_update_cliente',
							'id_cliente' : id_cliente,
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
				var id_cliente = $(this).data('idcliente');
				var nombre = $(this).val();
				$.ajax({ 
					url: 'sbin/clientesfunc.php', 
					type: 'POST', 
					data: { 
						'accion' : 'add_update_cliente',
						'id_cliente' : id_cliente,
						'nombre' : nombre
					},
					success: function(resp) { jAlert("Los datos se guardaron correctamente.","ATENCION", null, 1000); }
				});
			});

			$('#frmclientes').on('click', '.delete', function(event) {
				event.preventDefault;
				$this = $(this);
				var id_cliente = $this.data('idcliente');
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
								'id_cliente' : id_cliente
							},
							success: function(data) { 
								if(data.status === 'success'){
									$("#tr-"+data.id_cliente).remove();
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
				var id_cliente =  retVal[0];
				//var clone = $('#frmclientes tr:last').prev();

				$(  "<tr id='tr-"+id_cliente+"'>"+
					"   <td style='text-align:center;'>"+id_cliente+"</td>"+
					"   <td>"+
					"   	<input type='text' id='nombre"+id_cliente+"' name='nombre"+id_cliente+"' class='span12 nombre' value='' placeholder='Nombre del cliente' data-idcliente='"+id_cliente+"'   minlength='4' required />"+
					"   	<div id='info"+id_cliente+"' name='info"+id_cliente+"' style='display:none;'>"+
					"   		<textarea class='moxiecut span12' style='height: 280px;' id='contenido"+id_cliente+"' name='contenido"+id_cliente+"' data-idcliente='"+id_cliente+"'></textarea>"+
					"   	</div>"+
					"   </td>"+
					"   <td style='text-align:center;'><i class='icon-file-text-alt icon-2x info' style='cursor:pointer;' data-id='"+id_cliente+"'></i></td>"+
					"   <td style='text-align:center;'>"+
					"   	<a class='btn delete' href='#' data-idcliente='"+id_cliente+"'><i class='icon-fixed-width icon-trash'></i> Eliminar</a>"+
					"   </td>"+
					"</tr>").insertBefore('#frmclientes tr:last');
				 addTinyMCE('contenido'+id_cliente);
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
	$id_cliente  = isset($_REQUEST['id_cliente']) ? mysql_real_escape_string($_REQUEST['id_cliente']) : '';
	$nombre      = isset($_REQUEST['nombre'])     ? mysql_real_escape_string($_REQUEST['nombre'])     : '';
	$contenido   = isset($_REQUEST['contenido'])  ? mysql_real_escape_string($_REQUEST['contenido'])  : '';

	$field1 = '';
	$value1 = '';
	$ondup1 = '';
	$field2 = '';
	$value2 = '';
	$ondup2 = '';
	if(!empty($id_cliente)) { $field1='id_cliente,'; $value1 = "'$id_cliente', ";}
	if(!empty($contenido))  { $field2=', Contacto';  $value2 = ", '$contenido'"; $ondup2 = ", Contacto='$contenido'";}
	//$GLOBALS['connMySQL']->debug = true;
	$GLOBALS['connMySQL']->Execute("INSERT INTO ts_clientes($field1 Descripcion $field2) VALUES ($value1 '$nombre' $value2) ON DUPLICATE KEY UPDATE id_cliente='$id_cliente', Descripcion='$nombre' $ondup2;");
	echo "Los datos se actualizaron correctamente";
}

function nuevoCliente(){
	global $connMySQL;

	$connMySQL->Execute("INSERT INTO ts_clientes (Descripcion, Contacto) VALUES ('', '');");
	$id_cliente = $connMySQL->Insert_ID();
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=UTF-8');
	echo '{ "id_cliente": "'.$id_cliente.'" }';	
}


function delete_cliente(){
	global $connMySQL;

	$id_cliente  = isset($_REQUEST['id_cliente']) ? mysql_real_escape_string($_REQUEST['id_cliente']) : '';
	if(empty($id_cliente)) {
		$rJson = '{ "status" : "error", "msg": "Cliente no definido", "id_cliente" : "'.$id_cliente.'" }';
	}else{
		$connMySQL->Execute("DELETE FROM ts_clientes WHERE id_cliente='$id_cliente';");
		$rJson = '{ "status" : "success", "msg": "Cliente eliminado correctamente", "id_cliente" : "'.$id_cliente.'" }';
	}
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=UTF-8');
	echo $rJson;	

}
