//
// Nombre:      jquery.fancybox.personalizado.js
// Version:     20130726
// Descripcion: Funciones utilizadas para mostrar baner con popup
// Copyright:   (c) Exitweb - exitweb.es
//

(function( $ )
{
  $.fn.openPopup = function(href,timeout){
     $(this).fancybox({ 
       width  : '1000', 
       height : '1000',
       padding: 0,
       margin : 0,
       afterLoad: function(){ 
          setTimeout( function() { $.fancybox.close(); },timeout*1000); 
       },
       afterShow: function() {
          $("img.fancybox-image").click(function() {
            window.location.href = href;
          });
      }
    }).mouseover(function(){ $(this).trigger('click'); }).trigger('click');
  }
})( jQuery );