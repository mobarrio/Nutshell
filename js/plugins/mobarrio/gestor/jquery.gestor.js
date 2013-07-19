(function( $ )
{
	var textbtn = "INSCRIPTION";
	var title = "Pour recevoir chaque semaine le programme du cinéma par email, inscrivez-vous à notre NEWSLETTER";
	var href = "gestor/sbin/newsletterfunc.php";
	var msgthxs = "Merci de votre inscription.<br><br>Dans les prochaines 24 heures Recive un email pour confirmer votre inscription.";
	
	$.fn.newsletter = function() 
	{
		this.append('<div class="newsletter-hock" style="position:relative;">');
		$('<div class="newsletter" style="width: 40px;"></div>').appendTo('.newsletter-hock');
		$('<a href="#" title="" class="icon"><span></span></a>').appendTo('.newsletter');
		$('<div class="newsletter-form" style="width: 0px; display: block;"></div>').appendTo('.newsletter');
		$('<form id="newsletter-form"></form>').appendTo('.newsletter-form');
		$('<h3><span class="frm002 oculto" style="font-weight: 700;float: left;position: relative;top: 40px;height: 120px;">'+msgthxs+'</span><span class="frm001">'+title+'</span></h3>').appendTo('#newsletter-form');
		$('<input type="text" name="name"  id="newsletter-name"  value="" placeholder="Votre nom"   class="inp frm001">').appendTo('#newsletter-form');
		$('<input type="text" name="email" id="newsletter-email" value="" placeholder="Votre email" class="inp frm001">').appendTo('#newsletter-form');
		$('<input type="hidden" id="accion" name="accion" value="add_user_auto" />').appendTo('#newsletter-form');
		$('<button id="newsletter-enviar" name="newsletter-enviar" class="frm001">'+textbtn+'</button>').appendTo('#newsletter-form');
		$('<div class="clear"></div>').appendTo('.newsletter');
		$(".newsletter .icon").click(function() {
			if($(this).hasClass('active')) {
				$(this).removeClass('active');			
				$(".newsletter .newsletter-form").animate({ width: 0 }, 700, function() {
					$(".newsletter").css('width', '40px');
					$('.frm001').removeClass('oculto');
					$('.frm002').addClass('oculto');
				});
			}
			else {
				$(this).addClass('active');
				$(".newsletter").css('width', '235px');
				$(".newsletter .newsletter-form").animate({ width: '191px' }, 700, function() {
					//
				});			
			}
		});	
		
		$("#newsletter-enviar").click(function(event) {
			event.preventDefault();
			$.ajax({ 
				url: href, 
				type: 'POST',
				data: $("#newsletter-form").serialize(),
				beforeSend: function() { 
					$('.frm001').addClass('oculto');
					$('.frm002').removeClass('oculto');
				},
				success: function() { 
					$(".newsletter .icon").trigger('click'); 
					$("#newsletter-name").val('');
					$("#newsletter-email").val('');
				}
			});
		});
	}
	
	$.newsletterPopup = function() 
	{
		$('body').append('<div class="newsletter-popup">');
		$('<div class="newsletter-form-popup" style="width: 191px; display: block;">').appendTo('.newsletter-popup');
		$('<span id="closeBT" style="float: right;margin: 0 2px;background: url(gestor/styles/themes/ui-smoothness/images/ui-icons_888888_256x240.png) no-repeat -34px -190px;padding: 5px;display: block;cursor:pointer;">&nbsp;</span>').appendTo('.newsletter-form-popup');
		$('<form id="newsletter-form-popup">').appendTo('.newsletter-form-popup');
		$('<h3 class="newsletter-form-popup-h3">').appendTo('#newsletter-form-popup');
		$('<span class="frm002-popup oculto" style="font-weight: 700;float: left;position: relative;top: 0px;height: 120px;">Merci de votre inscription.').appendTo('.newsletter-form-popup-h3');
		$('<br />').appendTo('.frm002-popup');
		$('<br />Dans les prochaines 24 heures Recive un email pour confirmer votre inscription.</span>').appendTo('.frm002-popup');
		$('<span class="frm001-popup">Pour recevoir chaque semaine le programme du cinéma par email, inscrivez-vous à notre NEWSLETTER</span>').appendTo('.newsletter-form-popup-h3');
		$('<input type="text" name="name" id="newsletter-name-popup" value="" placeholder="Votre nom" class="inp-popup frm001-popup" />').appendTo('#newsletter-form-popup');
		$('<input type="text" name="email" id="newsletter-email-popup" value="" placeholder="Votre email" class="inp-popup frm001-popup" />').appendTo('#newsletter-form-popup');
		$('<input type="hidden" id="accion" name="accion" value="add_user_auto" />').appendTo('#newsletter-form-popup');
		$('<button id="newsletter-enviar-popup" name="newsletter-enviar-popup" class="frm001-popup">INSCRIPTION</button>').appendTo('#newsletter-form-popup');
		$('<div class="clear"></div>').appendTo('.newsletter-popup');

		$("#newsletter-enviar-popup").click(function(event) {
			event.preventDefault();
			$.ajax({ 
				url: href, 
				type: 'POST',
				data: $("#newsletter-form-popup").serialize(),
				beforeSend: function() { 
					$('.frm001-popup').addClass('oculto');
					$('.frm002-popup').removeClass('oculto');
				},
				success: function() { 
					setTimeout("$('#closeBT').trigger('click');",1000);
					$("#newsletter-name-popup").val('');
					$("#newsletter-email-popup").val('');
				}
			});
		});
		
		$('#closeBT').click(function(){
			$(".newsletter-popup").hide("blind", 1000);
			$('.newsletter-popup').idleTimer('destroy');
		});
		
		$('.newsletter-popup').on( "idle.idleTimer", function(){
			$('#closeBT').trigger('click');
		});		
		
		$('.newsletter-popup').on( "active.idleTimer", function(){
		  $('.newsletter-popup').idleTimer(3000);
		});
		
		$('.newsletter-popup').show('blind', 500).idleTimer(3000);
	}
})( jQuery );