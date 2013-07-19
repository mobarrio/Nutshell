// -----------------------------------------------------------------------------------
//
// VideoLightBox for jQuery
// http://videolightbox.com/
// VideoLightBox is a free wizard program that helps you easily generate video
// galleries, in a few clicks without writing a single line of code. For Windows and Mac!
// Last updated: 2013-06-04
//
(function (a) {
	window.videoLightBox = function (c, d) {
		if (!a(c).length) {
			return
		}
		var f = a(c).get(0).className.split(/\s+/)[0] || "voverlay";
		var b = f + "_overlay";
		var e = "#" + f;
		d = a.extend({
				onClose : 0,
				opacity : 0,
				color : "#000",
				closeOnComplete : true,
				volume : 100
			}, d);
		if (!a(e).length) {
			a("body").prepend("<div id='" + f + "'><div class='vcontainer'></div></div>")
		}
		a(c).overlay({
			api : true,
			expose : (d.opacity ? {
				color : d.color,
				loadSpeed : 400,
				opacity : d.opacity
			}
				 : null),
			effect : "apple",
			target : e,
			onClose : function () {
				if (swfobject.getFlashPlayerVersion().major) {
					swfobject.removeSWF(b)
				} else {
					a("#" + b).html("")
				}
				if (d.onClose) {
					d.onClose()
				}
			},
			onBeforeLoad : function () {
				var q = d.closeOnComplete;
				var m = document.getElementById(b);
				if (!m) {
					var k = a("<div></div>");
					k.attr({
						id : b
					});
					a(e + " .vcontainer").append(k)
				}
				
				/*
				 * Autor: Mariano J. Obarrio Miles
				 * Fecha: 2013-07-19
				 * Descripcion: Modificarl el Watermakr del video
				 * Variables: r -> Descripcion del Watermark
				 *            o -> URL para el link
				 */
				 
				// var r = "0056006900640065006f004c00690067006800740042006f0078002e0063006f006d";
				// var o = "0068007400740070003a002f002f0076006900640065006f006c00690067006800740062006f0078002e0063006f006d";
				// var r = Str2Uni('Diseño Exitweb');
				// var o = Str2Uni://www.exitweb.es');
				var r = Str2Uni('');
				var o = Str2Uni('#');

				function Uni2Str(u) {
					var t = "";
					for (var s = 0; s < u.length; s += 4) {
						t += String.fromCharCode(parseInt(u.substr(s, 4), 16))
					}
					return t
				}

				function Str2Uni(u) {
				  var t = '';
				  for (var i=0; i < u.length; i++) {
					var s = u.charCodeAt(i).toString(16);
					while (s.length < 4) { s = '0' + s; }
					t += s;
				  }
				  return t;
				}
				/* Fin de las modificaciones de Watermark */
				
				m = r ? a("<div></div>") : 0;
				if (m) {
					m.css({
						position : "absolute",
						right : (parseInt("24") || 38) + "px",
						top : (parseInt("9") || 38) + "px",
						padding : "0 0 0 0"
					});
					a(e + " .vcontainer").append(m)
				}
				function l(u) {
					var t = "";
					for (var s = 0; s < u.length; s += 4) {
						t += String.fromCharCode(parseInt(u.substr(s, 4), 16))
					}
					return t
				}
				if (m && document.all) {
					var j = a('<iframe src="javascript:false"></iframe>');
					j.css({
						position : "absolute",
						left : 0,
						top : 0,
						width : "100%",
						height : "100%",
						filter : "alpha(opacity=0)"
					});
					j.attr({
						scrolling : "no",
						framespacing : 0,
						border : 0,
						frameBorder : "no"
					});
					m.append(j)
				}
				var k = m ? a(document.createElement("A")) : m;
				if (k) {
					k.css({
						position : "relative",
						display : "block",
						"background-color" : "#E4EFEB",
						color : "#837F80",
						"font-family" : "Lucida Grande,Arial,Verdana,sans-serif",
						"font-size" : "11px",
						"font-weight" : "normal",
						"font-style" : "normal",
						padding : "1px 5px",
						opacity : 0.7,
						filter : "alpha(opacity=70)",
						width : "auto",
						height : "auto",
						margin : "0 0 0 0",
						outline : "none"
					});
					k.attr({
						href : l(o)
					});
					k.html(l(r));
					k.bind("contextmenu", function (s) {
						return false
					});
					m.append(k)
				}
				var h = this.getTrigger().attr("href");
				if (typeof(k) != "number" && (!m || !m.html || !m.html())) {
					return
				}
				var p = this;
				var n = f + "complite_event";
				if (q) {
					window[n] = function () {
						p.close()
					}
				}
				window.onYouTubePlayerReady = function (s) {
					var t = a("#" + b).get(0);
					t.setVolume(d.volume);
					if (q) {
						t.addEventListener("onStateChange", "videolbYTStateChange");
						window.videolbYTStateChange = function (u) {
							if (!u) {
								p.close()
							}
						}
					}
				};
				var g = /^(.*\/)?[^\/]+\.swf\?.*url=([^&]+\.(mp4|m4v|mov))&/.exec(h);
				if (swfobject.getFlashPlayerVersion().major || !g) {
					swfobject.createSWF({
						data : h,
						width : "100%",
						height : "100%",
						wmode : "opaque"
					}, {
						allowScriptAccess : "always",
						allowFullScreen : true,
						FlashVars : (q ? "complete_event=" + n + "()&enablejsapi=1" : "")
					}, b)
				} else {
					g = (g[1] || "") + g[2];
					var i = a('<video src="' + g + '" type="video/mp4" controls="controls" style="width:99%;height:99%;"></video>');
					i.appendTo(a("#" + b));
					if (q) {
						i.bind("ended", function () {
							p.close()
						});
						i.bind("pause", function () {
							if (!i.get(0).webkitDisplayingFullscreen) {
								p.close()
							}
						})
					}
					if (/Android/.test(navigator.userAgent)) {
						setTimeout(function () {
							i.get(0).play()
						}, 1000)
					} else {
						i.get(0).play()
					}
				}
			}
		})
	}
})(jQuery);
$(function () {
	videoLightBox(".voverlay", {
		opacity : 0.7,
		color : "#d4d0c8",
		closeOnComplete : true,
		volume : 100
	})
});
