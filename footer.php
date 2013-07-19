<?
	if (session_id() == ''){ session_start(); }

	function autoUpdatingCopyright($startYear)
	{
	 
		// given start year (e.g. 2004)
		$startYear = intval($startYear);
	 
		// current year (e.g. 2007)
		$year = intval(date('Y'));
	 
		// is the current year greater than the
		// given start year?
		if ($year > $startYear)
			return "( ". $startYear .' - '. $year . " ) © Copyright - " . $_SESSION['COPYRIGHT'];
		else
			return "( ".$startYear . " )";
}
?>
<style>
.footer {
	color: grey;
	font-weight: normal;
	text-align: center;
	font: normal 0.7em "Trebuchet MS", Arial, Helvetica, sans-serif;
	display:block;
}
</style>

<div id='footer001' class="span12" style='cursor:pointer;'>
<span class='footer'><?=autoUpdatingCopyright(2004)?></span>
</div>
<script>
	jQuery("#footer001")
	.off("click")
	.on("click", function(event) { 
		event.preventDefault();  
		$.ajax({
		  url: "content.php",
		  beforeSend: function ( data ) { $("#contenido").html("<div class='loading' style='margin-top:40%;'><img src='img/preloaders/cargando3.gif' /> Cargando...</div>"); },
		  success: function(data) { $("#contenido").html(data); }
		});		
	});
</script>
