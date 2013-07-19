(function( $ ){

    function incrementer(ct, increment) {
        return function() { ct+=increment; return ct; };
    }
    
    function pad2(number) {
         return (number < 10 ? '0' : '') + number;
    }

    function defaultFormatMilliseconds(millis) {
        var x, seconds, minutes, hours;
        x = millis / 1000;
        seconds = Math.floor(x % 60);
        x /= 60;
        minutes = Math.floor(x % 60);
        x /= 60;
        hours = Math.floor(x % 24);
        // x /= 24;
        // days = Math.floor(x);
        return [pad2(hours), pad2(minutes), pad2(seconds)].join(':');
    }

	/* Modificacion MarianoO 02-07-2013 */
	function generateTime(time){
		var second = time % 60;
		var minute = Math.floor(time / 60) % 60;
		var hour = Math.floor(time / 3600) % 60;
		second = (second < 10) ? '0'+second : second;
		minute = (minute < 10) ? '0'+minute : minute;
		hour = (hour < 10) ? '0'+hour : hour;
		var formatTime = hour + ":" + minute + ":" + second;
		return(formatTime);
	};
	
    //NOTE: This is a the 'lazy func def' pattern described at http://michaux.ca/articles/lazy-function-definition-pattern
    function formatMilliseconds(millis, data) {
        // Use jintervals if available, else default formatter
        var formatter;
        if (typeof jintervals == 'function') {
            formatter = function(millis, data){return jintervals(millis/1000, data.format);};
        } else {
            formatter = defaultFormatMilliseconds;
        }
        formatMilliseconds = function(millis, data) {
            return formatter(millis, data);
        };
        return formatMilliseconds(millis, data);
    }
	
	function setInitTime(sec){
		if(typeof(sec) !== 'undefined') {
			time = sec;
		} else {
			var hms = elem.val(); 
			var a = hms.split(':'); // split it at the colons
			var sec = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 
			time = sec;
		}
	};

    var methods = {
        
        init: function(options) {
            var defaults = {
                updateInterval: 1000,
                startTime: 0,
                format: '{HH}:{MM}:{SS}',
                formatter: formatMilliseconds,
				showdate: generateTime
            };
            
            // if (options) { $.extend(settings, options); }
            
            return this.each(function() {
                var $this = $(this), data = $this.data('stopwatch');

                // If the plugin hasn't been initialized yet
                if (!data) {
                    // Setup the stopwatch data
                    var settings = $.extend({}, defaults, options);
                    data = settings;
                    data.active = false;
                    data.target = $this;
                    data.elapsed = settings.startTime;
                    // create counter
                    data.incrementer = incrementer(data.startTime, data.updateInterval);
					
					/* Modificacion MarianoO 02-07-2013 */
					/* Recupera el tiempo transcurrido y calcula los segundos */
					var hms = $this.val(); 
					var a = hms.split(':'); // split it at the colons
					data.elapsedSec = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
					
                    data.tick_function = function() {
						data.elapsedSec++; /* Modificacion MarianoO 02-07-2013 */
                        var millis = data.incrementer();
                        data.elapsed = millis;
                        data.target.trigger('tick.stopwatch', [millis]);
                        data.target.stopwatch('render');
						$this.val(data.showdate(data.elapsedSec)); /* Modificacion MarianoO 02-07-2013 */
                    };
                    $this.data('stopwatch', data);
                }
                
            });
        },
        
        start: function() {
            return this.each(function() {
                var $this = $(this), data = $this.data('stopwatch');
				
				/* Modificacion MarianoO 02-07-2013 */
				/* Recupera el tiempo transcurrido y calcula los segundos */
				var hms = $this.val(); 
				var a = hms.split(':'); // split it at the colons
				data.elapsedSec = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 
				
                // Mark as active
                data.active = true;
                data.timerID = setInterval(data.tick_function, data.updateInterval);
                $this.data('stopwatch', data);
            });
        },
        
        stop: function() {
            return this.each(function() {
                var $this = $(this),
                    data = $this.data('stopwatch');
                clearInterval(data.timerID);
                data.active = false;
                $this.data('stopwatch', data);
            });
        },
        
        destroy: function() {
            return this.each(function(){
                var $this = $(this),
                    data = $this.data('stopwatch');
                $this.stopwatch('stop').unbind('.stopwatch').removeData('stopwatch');
            });
        },
        
        render: function() {
            var $this = $(this),
                data = $this.data('stopwatch');
            $this.html(data.formatter(data.elapsed, data));
        },

        getTime: function() {
            var $this = $(this),
                data = $this.data('stopwatch');
            return data.elapsed;
        },
        
        isActive: function() {
            var $this = $(this), data = $this.data('stopwatch');
            return data.active;
        },

        toggle: function() {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('stopwatch');
                if (data.active) {
                    $this.stopwatch('stop');
                } else {
                    $this.stopwatch('start');
                }
            });
        },
        
        reset: function() {
            return this.each(function() {
                var $this = $(this);
                    data = $this.data('stopwatch');
                data.incrementer = incrementer(data.startTime, data.updateInterval);
                data.elapsed = data.startTime;
                $this.data('stopwatch', data);
            });
        }
    };
    
    
    // Define the function
    $.fn.stopwatch = function( method ) {
        if (methods[method]) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.stopwatch' );
        }
    };

})( jQuery );