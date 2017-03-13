var searchJs = (function () {
    var my = {};
    my.doFilter = function (url, value, key) {
    	fiterurl = url.replace(key, encodeURIComponent(value));
        window.location = fiterurl
    };
    
    my.submitSearch = function () {
        var c = $("#search_key_url").val();
        var h = $("#SearchKey").val();
       

        var e = new Object();
        var d = [];
        if(h != '' && h != undefined)
        {
        	e.S_SEARCHKEY = h;
        }
        else
        {
        	d.push("q")
        }  
        
        var f = $("#SearchLocation").val();
        if (f != "" && f != undefined) {
            e.S_SEARCHLOC = f;
        } else {
            d.push("loc");
        }

        var lat = $("#SearchLat").val();
        if (lat != "" && lat != undefined) {
            e.S_SEARCHLAT = lat;
        } else {
            d.push("lat");
        }

        var lng = $("#SearchLng").val();
        if (lng != "" && lng != undefined) {
            e.S_SEARCHLNG = lng;
        } else {
            d.push("lng");
        }

        var tm = $("#whentime").html();
        if (tm != "") {
            e.S_SEARCHTIME = tm;
        } else {
            d.push("t");
        }

        if($("#divdatpicker").length)
        {
            var dateSel = $("#divdatpicker").html();
            if (dateSel != "") {
                e.S_SEARCHDATE = dateSel;
            } else {
                d.push("date");
            }
        }
        
        if($("#SearchPopular").length)
        {
            if($("#SearchPopular").is(':checked')) {
                e.S_SORTPOPULAR = 'popular';
            } else {
                d.push("o_popular");
            }
        }

        if($("#SearchRating").length)
        {
            if($("#SearchRating").is(':checked')) {
                e.S_SORTRATING = 'rating';
            } else {
                d.push("o_rating");
            }
        }
        
        cu = '';
        $('.cuisines:input:checked').each(function() {
            cu += $(this).val().toLowerCase()+',';
        });
        if(cu != '') {
        	e.S_SEARCHCUISINE = cu.substring(0, cu.length - 1);
        } else {
            d.push("cu");
        }
        
        diet = '';
        $('.diets:input:checked').each(function() {
        	//console.log(diet);
            //console.log($(this).val());
            diet += $(this).val().toLowerCase()+',';
        });
        if(diet != '') {
        	e.S_SEARCHDIET = diet.substring(0, diet.length - 1);
        } else {
            d.push("diet");
        }

        dine = '';
        $('.dine:input:checked').each(function() {
            dine += $(this).val().toLowerCase()+',';
        });
        if(dine != '') {
            e.S_SEARCHDINE = dine.substring(0, dine.length - 1);
        } else {
            d.push("dine");
        }
       
        c = this.stripUrlForParams(c, d);
        this.doFilter1(c, e)
    };
    my.stripUrlForParams = function (b, e) {
        for (var c = 0; c < e.length; c++) {
            var d = new RegExp("/" + e[c] + ":[\\w]*\\b");
            b = b.replace(d, "")
        }
        return b
    };
    my.doFilter1 = function (url, params, validation) {
        var gourl = url;

        var validated = true;
        //console.log(url+'--'+params)
        $.each(params, function (value, key) {
            if (validation == "numeric" && isNaN(value)) {
                validated = false
            }
            if (value) {
            	//console.log(value+'>>'+key);
                //console.log(gourl);
                gourl = gourl.replace(value, encodeURIComponent(key.replace(/\//g, "")))
            }
        });
        if (gourl == url || !validated) {
            return
        }
     //	console.log(gourl);
      window.location = gourl
    };
    my.loadOptions = function(id,type,updateid) {
		$.ajax({
			url: SITE_URL+'/professionals/load_options',
			type: 'POST',
			dataType: "html",
			data: { 'id' : id,'type' : type} ,
			success:function(resp) 
			{
				var subcategory_opt = '<option value="">Select Subcategory</option>';
				obj = $.parseJSON(resp);
				if(obj.status)
				{
					$.each(obj.subcategories, function( index, value ) {
						subcategory_opt += get_options(value,index);
					});
					$(updateid).html(subcategory_opt);
				}	
			},
			error:function(resp) 
			{
				
			}
		});			
    };
    my.getBaseUrl = function() {
    	 try {
    	        var url = location.href;

    	        var start = url.indexOf('//');
    	        if (start < 0)
    	            start = 0 
    	        else 
    	            start = start + 2;

    	        var end = url.indexOf('/', start);
    	        if (end < 0) end = url.length - start;

    	        var baseURL = url.substring(start, end);
    	        return baseURL;
    	    }
    	    catch (arg) {
    	        return null;
    	    }
    };
    
    my.clearActiveFilterLink = function (elem) {
    	linkelm = $('ul.category-list').find("li > a");
    	linkelm.each(function (a,d) {
           if($(d).hasClass('active'))
           {
        	   $(d).removeAttr('href');
           }
        })
    };
    my.clearFilterInput = function (elem) {
        elem = $(elem);
        if (elem.value == "Min" || elem.value == "Max") {
            elem.value = ""
        }
        elem.removeClass("hint")
    };
    my.doSort = function (url, value) {
        var sortOrder = value.split("-");
        url = url.replace("T_SORT", encodeURIComponent(sortOrder[0]));
        url = url.replace("T_ORDER", encodeURIComponent(sortOrder[1]));
        window.location.href = url
    };
    my.searchLogin = function (type, parameters) {
        var url = window.location;
        var url = url.toString();
        var params = type + "=" + parameters;
        if (url.indexOf("#") < 0) {
            url += "#"
        } else {
            url += "&"
        }
        url += params;
        window.location = "/login?redirect=" + encodeURIComponent(url)
    };
    my.toggleFilter = function (elem) {
        var items = $(elem).next();
        if(items.is(':visible'))
        {
        	$(elem).addClass('more');
        	$(elem).removeClass('less');
        }
        else
        {
        	$(elem).removeClass('more');
        	$(elem).addClass('less');
        }
        items.toggle();
    };
    my.selectSortFilter = function (elem) {
        var parent = $(elem).parent();
        if(parent.isSelected()) {
        	parent.removeClass('selected');
        	parent.addClass('unselected');
        } else {
        	parent.removeClass('unselected');
        	parent.addClass('selected');
        }
    };
    my.selectSortChangeFilter = function (elem) {
        var v = $(elem).attr('val');
        if(v == 'asc') {
        	$(elem).attr('val','desc');
        	$(elem).removeClass('up');
        	$(elem).addClass('down');
        } else {
        	$(elem).attr('val','asc');
        	$(elem).removeClass('down');
        	$(elem).addClass('up');
        }
    };
    
    my.catToggle = function (elem) {
        if ($("catmore").isDisplayed()) {
           // $(elem).set("html", "More");
            $("catmore").hide()
        } else {
           // $(elem).set("html", "Less");
            $("catmore").show()
        }
    };
     
    return my
}());

$.fn.isDisplayed = function () {
	$.each(this, function(key, element) {
	    alert('key: ' + key + '\n' + 'value: ' + element);
	});
	//return this.getStyle("display") != "none"
}
$.fn.isSelected = function() {
	cls = $(this).attr('class');
	
	if(cls == 'unselected')
	{
		return false;
	}
	else if(cls == 'selected')
	{
		return true
	}
}


