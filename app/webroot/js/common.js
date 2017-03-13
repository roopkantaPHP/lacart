var commonJs = (function () {
    var my = {};
    my.openClosePopup = function (elem) {
    	if($(elem).isVisible())	{
    		$(elem).removeClass('openedpop');
    		$(document).unbind('mousedown',closePopBox);
    	} else {
    		$('.sl-popup').slideUp().removeClass('openedpop');
    		$(elem).addClass('openedpop');
    		$(document).bind('mousedown',closePopBox)
    	}
    	$(elem).fadeToggle(300);
    };
    return my
}());

$.fn.isDisplayed = function () {
	return this.getStyle("display") != "none"
}

$.fn.isVisible = function() {
    return (this.is(":visible")) ? true : false;        
}
var currentElement=false;
var currentClickedElement=false;
function closePopBox(event){
	var eventelementparent=$(event.target).parents(".sl-popup").get();
	if(eventelementparent.length){
		
	}else{
		if($(event.target).parents('.openedpop').get().length){
			
		}else{
			$(currentClickedElement).slideUp().removeClass('openedpop');
			$(currentElement).hide();
			$(document).unbind('mousedown',closePopBox);
		}
	}
}
/*
$.fn.toggle= function() {
        return this[this.isDisplayed() ? "hide" : "show"]()
}
$.fn.hide= function() {
        var c;
        try {
            c = this.getStyle("display")
        } catch (d) {}
        if (c == "none") {
            return this
        }
        return this.store("element:_originalDisplay", c || "").setStyle("display", "none")
}
$.fn.show= function(b) {
        if (!b && this.isDisplayed()) {
            return this
        }
        b = b || this.retrieve("element:_originalDisplay") || "block";
        return this.setStyle("display", (b == "none") ? "block" : b)
}

$.fn.swapClass= function(d, c) {
        return this.removeClass(d).addClass(c)
}

$.fn.getComputedStyle = function(a) {
    if (this.currentStyle) {
        return this.currentStyle[a.camelCase()]
    }
    var b = Element.getDocument(this).defaultView,
        c = b ? b.getComputedStyle(this, null) : null;
    return (c) ? c.getPropertyValue((a == s) ? "float" : a.hyphenate()) : null
}
$.fn.setStyle= function(b, c) {
        if (b == "opacity") {
            if (c != null) {
                c = parseFloat(c)
            }
            o(this, c);
            return this
        }
        b = (b == "float" ? s : b).camelCase();
        if (typeOf(c) != "string") {
            var a = (Element.Styles[b] || "@").split(" ");
            c = Array.from(c).map(function(d, e) {
                if (!a[e]) {
                    return ""
                }
                return (typeOf(d) == "number") ? a[e].replace("@", Math.round(d)) : d
            }).join(" ")
        } else {
            if (c == String(Number(c))) {
                c = Math.round(c)
            }
        }
        this.style[b] = c;
        if ((c == "" || c == null) && r && this.style.removeAttribute) {
            this.style.removeAttribute(b)
        }
        return this
}
$.fn.getStyle= function(d) {
            if (d == "opacity") {
                return n(this)
            }
            d = (d == "float" ? s : d).camelCase();
            var c = this.style[d];
            if (!c || d == "zIndex") {
                c = [];
                for (var e in Element.ShortStyles) {
                    if (d != e) {
                        continue
                    }
                    for (var f in Element.ShortStyles[e]) {
                        c.push(this.getStyle(f))
                    }
                    return c.join(" ")
                }
                c = this.getComputedStyle(d)
            }
            if (c) {
                c = String(c);
                var a = c.match(/rgba?\([\d\s,]+\)/);
                if (a) {
                    c = c.replace(a[0], a[0].rgbToHex())
                }
            }
            if (Browser.opera || Browser.ie) {
                if ((/^(height|width)$/).test(d) && !(/px$/.test(c))) {
                    var b = (d == "width") ? ["left", "right"] : ["top", "bottom"],
                        g = 0;
                    b.each(function(h) {
                        g += this.getStyle("border-" + h + "-width").toInt() + this.getStyle("padding-" + h).toInt()
                    }, this);
                    return this["offset" + d.capitalize()] - g + "px"
                }
                if (Browser.ie && (/^border(.+)Width|margin|padding/).test(d) && isNaN(parseFloat(c))) {
                    return "0px"
                }
            }
            return c
}
$.fn.setStyles= function(a) {
            for (var b in a) {
                this.setStyle(b, a[b])
            }
            return this
}
$.fn.getStyles= function() {
            var a = {};
            Array.flatten(arguments).each(function(b) {
                a[b] = this.getStyle(b)
            }, this);
            return a
        }
*/        
$.fn.stars = function() {
    return $(this).each(function() {
        // Get the value
        var val = parseFloat($(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = $('<span />').width(size);
        // Replace the numerical value with stars
        $(this).html($span);
    });
}

$(document).ready(function() {
	if($("span.stars").length)
	{
		$('span.stars').stars();
	}
		
	$("form").submit(function () {
	    if ($(this).valid()) {
	        $(this).submit(function () {
	            return false;
	        });
	        return true;
	    }
	    else {
	        return false;
	    }
	});
	
	$('.acdian-hdg').click(function()
	{
		  $(this).next().slideToggle();
		  $(this).toggleClass('plus');
	});

	$(".menu-btn").click(function()
	{
		$(".nav-menu").slideToggle(700);
	});

	if($("#flashMessage").length){
		setTimeout(function()
		{
	        $('#flashMessage').delay(2000).fadeOut('slow');
	    }, 500);
	}

	if($(".success_msg").length){
		setTimeout(function()
		{
	        $('.success_msg').delay(2000).fadeOut('slow');
	    }, 500);	
	}

	if($(".error_msg").length){
		setTimeout(function() {
	        $('.error_msg').delay(2000).fadeOut('slow');
	    }, 500);	
	}
});


function checkAll(chobj){
	frm = chobj.form;
	for(var i=0; i<frm.elements.length; i++){
		if(frm.elements[i].type == "checkbox" && frm.elements[i].name != "gold"){
			frm.elements[i].checked = chobj.checked;
		}
	}
}

function submit_form(frmobj,comb)
{
	var comb = document.getElementById('action').value;
	if(comb=='')
	{
		alert("Please select an action.");
		return false;	
	}
	
	var checked = 0;
	
	if ((frmobj.elements['ids[]'] != null) && (frmobj.elements['ids[]'].length == null))
	{
		if (frmobj.elements['ids[]'].checked)
		{
			checked = 1;
		}
	}
	else
	{
		for (var i=0; i < frmobj.elements['ids[]'].length; i++)
		{
			if (frmobj.elements['ids[]'][i].checked)
			 {
				checked = 1;
				break;
			 }
		}
	}
	
	if (checked == 0)
	{
		alert("Please select checkboxes to do any operation.");
		return false;
	}
	
	
	if(comb == 'Delete')
	{
		if(confirm ("Are you sure you want to delete record(s)?"))
		{
			frmobj.listingAction.value = 'Delete';
			frmobj.submit();
		}
		else
		{
			return false;
		}
	}
	else
	{
		frmobj.listingAction.value = comb;
		frmobj.submit();
	}	

}

//Function for start loding sign
function startloading(){
	//$('#ajaxloading').show();
	$('.sl-popup:visible .overlay').show();
}

//function for end loading sign
function endloading(){
	//$("#ajaxloading").fadeOut(1000);
	$('.sl-popup:visible .overlay').fadeOut(1000);
}
function close_error(){
	$('.error_popup').hide();
}

//show all error and success messages
function showAjaxReturnMessageForPopup(data){
	if(data.response){
		var obj=jQuery.parseJSON(data.response);
	}else{
		var obj=jQuery.parseJSON(data);
	}
	var message="";
	var divId='';
	if(obj.success){
		message=obj.success;
		divId='successMessage';
		if(obj.redirect)
		{
			setTimeout(function(){
				window.location = obj.redirect;
			},5000);
		}
		//$(".overlay").fadeOut(500);
	}else if(obj.error){ 
		message=obj.error;
		divId='errorMessage';
	}
	$('.sl-popup').filter(':visible').find('#'+divId).show();
	setTimeout(function(){
	$('.sl-popup').filter(':visible').find('#'+divId).fadeOut(2000);
	},3000);
	$('.sl-popup').filter(':visible').find('#'+divId).text(message);
}

//validate form when submit form with ajax
function validateForm(formId,loading){
	loading=typeof loading!=='undefined'?loading:'true';
	if($('#'+formId).valid()){
		if(loading=='true'){
			startloading();
		}
		return true;
	}else{
		return false;
	}
}

//custom checkbox funtion
function customCheckbox(){
    var checkBox = $('input[type="checkbox"]');
    $(checkBox).each(function(){
    	 if($(this).is(':checked'))
        {
        	$(this).wrap("<span class='custom-checkbox selected'></span>" );
        }
        else
        {
        	$(this).wrap( "<span class='custom-checkbox'></span>" );
        }
    });
    $(checkBox).click(function(){
        $(this).parent().toggleClass("selected");
    });
}

	


$("document").ready(function(){
	customCheckbox();

	$("#reset").click(function(){		
		$(this).closest('form').find('input[type=text], textarea, select, input[type=date]').val('');
	});
	
	$(".numeric").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
     $(".count500").keyup(function(e){
		var remainChar = 500-parseInt($(this).val().length);
		$(this).siblings('.char-info').html(remainChar+' characters');
		$(this).val($(this).val().substr(0, 500));
	});

    $(".count200").keyup(function(e){
		var remainChar = 200-parseInt($(this).val().length);
		$(this).siblings('.char-info').html(remainChar+' characters');
		$(this).val($(this).val().substr(0, 200));
	});

	$(".count75").keyup(function(e){
		var remainChar = 75-parseInt($(this).val().length);
		$(this).siblings('.char-info').html(remainChar+' characters');
		$(this).val($(this).val().substr(0, 75));
	});
});
