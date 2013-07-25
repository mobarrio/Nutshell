// jQuery Alert Dialogs Plugin
//
// Version 1.1
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 14 May 2009
//
// Visit http://abeautifulsite.net/notebook/87 for more information
//
// Usage:
//		jAlert( message, [title, callback] )
//		jConfirm( message, [title, callback] )
//		jPrompt( message, [value, title, callback] )
// 
// History:
//
//		1.00 - Released (29 December 2008)
//
//		1.01 - Fixed bug where unbinding would destroy all resize events
//
// License:
// 
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC. 
//
(function($) {
	
	jQuery.alerts = {
		
		// These properties can be read/written by accessing jQuery.alerts.propertyName from your scripts at any time
		
		verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels/
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .01,                // transparency level of overlay
		overlayColor: '#FFF',               // base color of overlay
		draggable: true,                    // make the dialogs draggable (requires UI Draggables plugin)
		okButton: 'Aceptar',         // text for the OK button
		cancelButton: 'Anular', // text for the Cancel button
		dialogClass: null,                  // if specified, this class will be applied to all dialogs
		
		// Public methods
		
		alert: function(message, title, callback, timeout) {
			if( title == null ) title = 'Alert';
			if(timeout > 0 ) { setTimeout(function() { jQuery.alerts._hide(); }, timeout);}
			jQuery.alerts._show(title, message, null, 'alert', function(result) {
				if( callback ) callback(result);
			});
		},
		
		confirm: function(message, title, callback) {
			if( title == null ) title = 'Confirm';
			jQuery.alerts._show(title, message, null, 'confirm', function(result) {
				if( callback ) callback(result);
			});
		},
			
		prompt: function(message, value, title, callback) {
			if( title == null ) title = 'Prompt';
			jQuery.alerts._show(title, message, value, 'prompt', function(result) {
				if( callback ) callback(result);
			});
		},

		hide: function() { jQuery.alerts._hide(); },

		
		// Private methods
		
		_show: function(title, msg, value, type, callback) {
			
			jQuery.alerts._hide();
			jQuery.alerts._overlay('show');
			
			jQuery("BODY").append(
			  '<div id="popup_container">' +
			    '<h1 id="popup_title"></h1>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( jQuery.alerts.dialogClass ) jQuery("#popup_container").addClass(jQuery.alerts.dialogClass);
			
			var pos = 'fixed'; // // IE6 Fix (jQuery.browser.msie && parseInt(jQuery.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 
			
			jQuery("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});
			
			jQuery("#popup_title").text(title);
			jQuery("#popup_content").addClass(type);
			jQuery("#popup_message").text(msg);
			jQuery("#popup_message").html( jQuery("#popup_message").text().replace(/\n/g, '<br />') );
			
			jQuery("#popup_container").css({
				minWidth: jQuery("#popup_container").outerWidth(),
				maxWidth: jQuery("#popup_container").outerWidth()
			});
			
			jQuery.alerts._reposition();
			jQuery.alerts._maintainPosition(true);
			
			switch( type ) {
				case 'alert':
					//jQuery("#popup_message").after('<div id="popup_panel" style="margin-top: 35px;"><button id="popup_ok">&nbsp;' + jQuery.alerts.okButton + '&nbsp;</button></div>');
					//jQuery("#popup_ok").button({ text: true, icons: { primary: "ui-icon-check"}  });
					jQuery("#popup_message").after('<div id="popup_panel" style="margin-top: 35px;"><a id="popup_ok" class="btn" href="#"><i class="icon-ok"></i>&nbsp;&nbsp;' + jQuery.alerts.okButton + '</a></div>');					
					jQuery("#popup_ok").click( function() {
						jQuery.alerts._hide();
						callback(true);
					});
					jQuery("#popup_ok").focus().keypress( function(e) {
						if( e.keyCode == 13 || e.keyCode == 27 ) jQuery("#popup_ok").trigger('click');
					});
				break;
				
				case 'confirm':
					// jQuery("#popup_message").after('<div id="popup_panel" style="margin-top: 35px;"><button id="popup_ok">&nbsp;' + jQuery.alerts.okButton + '&nbsp;</button><button id="popup_cancel">&nbsp;' + jQuery.alerts.cancelButton + '&nbsp;</button></div>');
					jQuery("#popup_message").after('<div id="popup_panel" style="margin-top: 35px;"><a id="popup_ok" class="btn" href="#"><i class="icon-ok"></i>&nbsp;&nbsp;' + jQuery.alerts.okButton + '</a><a id="popup_cancel" class="btn" href="#"><i class="icon-remove"></i>&nbsp;&nbsp;'+ jQuery.alerts.cancelButton + '</a></div>');
					//jQuery("#popup_ok").button({ text: true, icons: { primary: "ui-icon-check"}  });
					//jQuery("#popup_cancel").button({ text: true, icons: { primary: "ui-icon-close"}  });
					jQuery("#popup_ok").click( function() {
						jQuery.alerts._hide();
						if( callback ) callback(true);
					});
					jQuery("#popup_cancel").click( function() {
						jQuery.alerts._hide();
						if( callback ) callback(false);
					});
					jQuery("#popup_ok").focus();
					jQuery("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) jQuery("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) jQuery("#popup_cancel").trigger('click');
					});
				break;
				case 'prompt':
					// jQuery("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" />').after('<div id="popup_panel" style="margin-top: 35px;"><button id="popup_ok">&nbsp;' + jQuery.alerts.okButton + '&nbsp;</button><button id="popup_cancel">&nbsp;' + jQuery.alerts.cancelButton + '&nbsp;</button></div>');
					// jQuery("#popup_cancel").button({ text: true, icons: { primary: "ui-icon-close"}  });
					// jQuery("#popup_ok").button({ text: true, icons: { primary: "ui-icon-check"}  });
					jQuery("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" />').after('<div id="popup_panel" style="margin-top: 35px;"><a id="popup_ok" class="btn" href="#"><i class="icon-ok"></i>&nbsp;&nbsp;' + jQuery.alerts.okButton + '</a><a id="popup_cancel" class="btn" href="#"><i class="icon-remove"></i>&nbsp;&nbsp;'+ jQuery.alerts.cancelButton + '</a></div>');			
					jQuery("#popup_prompt").width( jQuery("#popup_message").width() );
					jQuery("#popup_ok").click( function() {
						var val = jQuery("#popup_prompt").val();
						jQuery.alerts._hide();
						if( callback ) callback( val );
					});
					jQuery("#popup_cancel").click( function() {
						jQuery.alerts._hide();
						if( callback ) callback( null );
					});
					jQuery("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) jQuery("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) jQuery("#popup_cancel").trigger('click');
					});
					if( value ) jQuery("#popup_prompt").val(value);
					jQuery("#popup_prompt").focus().select();
				break;
			}
			
			// Make draggable
			if( jQuery.alerts.draggable ) {
				try {
					jQuery("#popup_container").draggable({ handle: jQuery("#popup_title") });
					jQuery("#popup_title").css({ cursor: 'move' });
				} catch(e) { /* requires jQuery UI draggables */ }
			}
		},
		
		_hide: function() {
			jQuery("#popup_container").remove();
			jQuery.alerts._overlay('hide');
			jQuery.alerts._maintainPosition(false);
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					jQuery.alerts._overlay('hide');
					jQuery("BODY").append('<div id="popup_overlay"></div>');
					jQuery("#popup_overlay").css({
						position: 'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: jQuery(document).height(),
						background: jQuery.alerts.overlayColor,
						opacity: jQuery.alerts.overlayOpacity
					});
				break;
				case 'hide':
					jQuery("#popup_overlay").remove();
				break;
			}
		},
		
		_reposition: function() {
			var top = ((jQuery(window).height() / 2) - (jQuery("#popup_container").outerHeight() / 2)) + jQuery.alerts.verticalOffset;
			var left = ((jQuery(window).width() / 2) - (jQuery("#popup_container").outerWidth() / 2)) + jQuery.alerts.horizontalOffset;
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;
			
			// IE6 fix
			//if( jQuery.browser.msie && parseInt(jQuery.browser.version) <= 6 ) top = top + jQuery(window).scrollTop();
			
			jQuery("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			jQuery("#popup_overlay").height( jQuery(document).height() );
		},
		
		_maintainPosition: function(status) {
			if( jQuery.alerts.repositionOnResize ) {
				switch(status) {
					case true:
						jQuery(window).bind('resize', jQuery.alerts._reposition);
					break;
					case false:
						jQuery(window).unbind('resize', jQuery.alerts._reposition);
					break;
				}
			}
		}
		
	}
	
	// Shortuct functions
	jAlert = function(message, title, callback, timeout) {
		jQuery.alerts.alert(message, title, callback, timeout);
	}

	jConfirm = function(message, title, callback) {
		jQuery.alerts.confirm(message, title, callback);
	};
		
	jPrompt = function(message, value, title, callback) {
		jQuery.alerts.prompt(message, value, title, callback);
	};
	
	jRemove = function() { jQuery.alerts.hide(); };
	
})(jQuery);