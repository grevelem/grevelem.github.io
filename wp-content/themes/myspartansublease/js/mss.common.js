function adjustPhotos()
{
	$(".photo img").each(function(index, p)
	{
		var $p = $(p);
		var $pr = $p.parents(".photo");
		$p.css({top: (-($p.height() / 2) + $pr.height() / 2)  + "px", left: (-($p.width() / 2) + $pr.width() / 2) + "px"});
	});
}
function createQuery(locs, semesters, minprice, maxprice)
{
	var qs = "";

	var all_locs = 0;
	if(locs)
	{
		for(var i = 0; i < locs.length; ++i)
		{
			all_locs = all_locs | (locs[i]);
		}
		qs += "&location=" + all_locs;
	}
	if(semesters != 0)
	{
		qs += "&semesters=" + semesters;
	}
	
	qs += "&minprice=" + minprice;
	qs += "&maxprice=" + maxprice;
	
	if(typeof(Storage)!=="undefined")
	{
		localStorage.setItem("locs",locs);
		localStorage.setItem("semesters",semesters);
		localStorage.setItem("minprice",minprice);
		localStorage.setItem("maxprice",maxprice);
	}
	
	return qs;
}
function createSlider(sliderId)
{
	var slider = new Object();
	var id = sliderId;
	
	slider.updateSlider = function(priceRangeLeft, priceRangeRight)
	{
		var value = $(id).data("value");
		if(value == undefined)
			value = $(id).data("sliderValue");
		$(priceRangeLeft).text("$" + value[0]);
		$(priceRangeRight).text("$" + value[1]);
		$(".slider").width('100%');
	}
	slider.setValue = function(min,max)
	{
		$(sliderId).slider('setValue',[min,max]);
	}
	
	setup();
	
	function setup()
	{
		$(sliderId).slider({tooltip:'hide'});
		slider.updateSlider(".price-range-left",".price-range-right");

		$(sliderId).on('slide',function(ev)
		{
			slider.updateSlider(".price-range-left",".price-range-right");
		});
	}
	
	return slider;
}

function getUrlParams()
{
    var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
        query  = window.location.search.substring(1);

    urlParams = {};
    while (match = search.exec(query))
       urlParams[decode(match[1])] = decode(match[2]);
	return urlParams;
}

function postJSON(url, data, success)
{
	$.ajax({
		  url:url,
		  type:"POST",
		  data:data,
		  dataType:"json",
		  success: success
	});
}