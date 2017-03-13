<script>
		(function($) {  
    $.widget("ui.timeslider", {
        getTime: function(value){
        	  var time = null;
            var localTime = new Date('<?php echo date("Y"); ?>','<?php echo date("m"); ?>','<?php echo date("d"); ?>',(value-1110)/60,0,0);
            
            minutes = localTime.getMinutes(),
            hours = localTime.getHours();
            if (hours === 0) {
                hours = 12;
                time = "AM";
            }
            else if (hours === 12) {
                time = "PM";
            }
            else if (hours > 12) {
                hours = hours - 12;
                time = "PM";
            }
            else {
                time = "AM";   
            }
            
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
        
        //    return hours + ":" + minutes + " " + time;
            return hours + ":00" + " " + time;
        },
        getHours: function(value){   
            var time = null,
                from = new Date(value * 60 * 1000),
                minutes = from.getMinutes(),
                hours = from.getHours();
            
            if (hours === 0) {
               // hours = 12;
            }
        
            if (hours > 12) {
                hours = hours - 12;
            }
          
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
        
            return hours+" hours";
        },
        _slideTime: function (event, ui){
            var that = $(this),
                startTime = null,
                totalHours = null,
                endTime = null;
            
            if(that.slider("option", "range") && that.slider("option", "range")!='min')
            {
                startTime = that.timeslider('getTime',(that.slider("values", 0)));
                endTime = that.timeslider('getTime', that.slider("values", 1));
                that.timeslider('option', 'timeDisplay').text(startTime + ' - ' + endTime);
            }
            else if(that.slider( "option", "range" ) && that.slider( "option", "range" )=='min')
            {
                if(that.slider( "option", "hours"))
                {
                    totalHours = that.timeslider('getHours',(that.slider("values", 0)));
                    that.timeslider('option', 'timeDisplay').text(totalHours);
                }
                else
                {
                    startTime = that.timeslider('getTime',(that.slider("values", 0)));
                    that.timeslider('option', 'timeDisplay').text(startTime);    
                }    
            }
            else 
            {
                startTime = that.timeslider('getTime', that.slider("value"));
                that.timeslider('option', 'timeDisplay').text(startTime);
                that.timeslider('option', 'timeInput').val(startTime);
            }
        },
        _checkMax: function(event, ui) {
            var that = $(this);
        
            if(that.slider( "option", "range" )){
                var div = that.find('div'),
                    size = that.slider("values", 1) - that.slider("values", 0);
                if( size >= 1435) {
		            div.addClass("ui-state-error");
                    that.timeslider('option', 'submitButton').attr("disabled","disabled").addClass("ui-state-disabled");
                    that.timeslider('option', 'errorMessage').text("Cannot be more than 24 hours");
	            }
		        else {	
                    div.removeClass("ui-state-error");
                    that.timeslider('option', 'submitButton').attr("disabled",null).removeClass("ui-state-disabled");
                    that.timeslider('option', 'errorMessage').text("");
                } 
            }
        },
        options: {
            sliderOptions: {},
            errorMessage: null,
            timeDisplay: null,
            submitButton: null,
            clickSubmit: null,
            timeInput: null            
            
        },
        _create: function() {
            var that = this,
                o = that.options,
                el = that.element;
                
                o.sliderOptions.slide = that._slideTime;
                o.sliderOptions.change = that._checkMax;
                o.sliderOptions.stop = that._slideTime;
                el.slider(o.sliderOptions);
                
                o.errorMessage = $(o.errorMessage);
                o.timeDisplay = $(o.timeDisplay);
                o.timeInput = $(o.timeInput);
                o.submitButton = $(o.submitButton).click(o.clickSubmit);
                
                
                that._slideTime.call(el);
        },
        _destroy: function() {
            this.element.remove();
        }
    });
})(jQuery);
 
</script>
