<?php
/**
 * The home page for My Spartan Sublease
 */

get_header(); 
include $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/sublease-manager/public.php';
function fail_page()
{
	status_header(404);
	nocache_headers();
	include( get_404_template() );
	exit;
}
function fail_page_error($error)
{
	echo $error; // take this out
	exit;
}

function check($m)
{
	if(!$m["success"]) fail_page_error("Error: " . $m["message"]);
}
config($mysqli);
$m = _prepare("
	SELECT 
	p.RequestId, p.State, p.DTSInserted AS postingdtsInserted, 
	r.FirstName, r.LastName, r.Price, r.Semesters, r.Address, 
	r.ContactNumber, r.RoomsAvailable, r.GoogleMapsData, r.TimeToBeReached,
	r.MoveInDate, r.MoveOutDate, r.PetsAllowed, r.UtilitiesIncluded, r.ApartmentGroup,
	u.Path
	FROM mss_postings p 
	JOIN mss_requests r
		ON p.RequestId = r.id
    JOIN mss_post_image_mappings pim
		ON pim.PostingId = p.id
    JOIN mss_uploads u
		ON u.id = pim.UploadId
    WHERE pim.UploadOrder = 0
    AND State = 0
	", 
	$q, $mysqli);
check($m);

$m = _execute($q, $mysqli);
check($m);

$q->bind_result($requestId, $state, $postingdtsInserted
, $firstName, $lastName, $price, $semesters, $address, $contactNumber, $roomsAvailable, $googleMapsData,
$timeToBeReached, $moveInDate, $moveOutDate, $petsAllowed, $utilitiesIncluded, $apartmentGroup, $path);

?>
<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/browse.css" type="text/css">

<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/jquery.sticky.js"></script>
<script type="text/javascript">
	$(function()
	{
			 
		var map_markers = [];
		 
		$(".group1").colorbox();
		$(".toolbar").sticky({topSpacing:32});
		$(".map").sticky({topSpacing:103});
		$('select').selectpicker();

		var slider = createSlider("#priceRange");
		var map = setupGoogleMaps();

		var urlParams = getUrlParams();
		readUrlParams(urlParams);
		checkSubleases();
		setupGoogleMapsBrowse();

		$("#Search").click(onSearch);

		function setupGoogleMapsBrowse()
		{
			  var contentString = '<div id="content">'+
				  '<div id="siteNotice">'+
				  '</div>'+
				  '<h1 id="firstHeading" class="firstHeading">Uluru</h1>'+
				  '<div id="bodyContent">'+
				  '<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
				  'sandstone rock formation in the southern part of the '+
				  'Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) '+
				  'south west of the nearest large town, Alice Springs; 450&#160;km '+
				  '(280&#160;mi) by road. Kata Tjuta and Uluru are the two major '+
				  'features of the Uluru - Kata Tjuta National Park. Uluru is '+
				  'sacred to the Pitjantjatjara and Yankunytjatjara, the '+
				  'Aboriginal people of the area. It has many springs, waterholes, '+
				  'rock caves and ancient paintings. Uluru is listed as a World '+
				  'Heritage Site.</p>'+
				  '<p>Attribution: Uluru, <a href="http://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
				  'http://en.wikipedia.org/w/index.php?title=Uluru</a> '+
				  '(last visited June 22, 2009).</p>'+
				  '</div>'+
				  '</div>';

			  var infowindow = new google.maps.InfoWindow({
				  content: contentString
			  });
		
			var east_campus_coords = [
				new google.maps.LatLng(42.72810015579319, -84.46117401123047),
				new google.maps.LatLng(42.721290417567744, -84.4273567199707),
				new google.maps.LatLng(42.683066355848574, -84.4247817993164),
				new google.maps.LatLng(42.68319254657213, -84.46151733398438)
			];
			var northeast_campus_coords = [
				new google.maps.LatLng(42.73591782230738, -84.48417663574219),
				new google.maps.LatLng(42.78570078500505, -84.48366165161133),
				new google.maps.LatLng(42.78507087088499, -84.39165115356445),
				new google.maps.LatLng(42.71473218539458, -84.39250946044922)
			];
			var northwest_campus_coords = [
				new google.maps.LatLng(42.735665654897524, -84.48503494262695),
				new google.maps.LatLng(42.78532283730215, -84.48434829711914),
				new google.maps.LatLng(42.78595274885835, -84.56949234008789),
				new google.maps.LatLng(42.73541348646229, -84.5698356628418),
				new google.maps.LatLng(42.73364827870436, -84.49396133422852)
			];
			var west_campus_coords = [
				new google.maps.LatLng(42.73263956599822, -84.49378967285156),
				new google.maps.LatLng(42.73289174571287, -84.56228256225586),
				new google.maps.LatLng(42.712209603842425, -84.56296920776367),
				new google.maps.LatLng(42.711831207766046, -84.49378967285156)
			];
			var south_campus_coords = [
				new google.maps.LatLng(42.711200542512096, -84.46220397949219),
				new google.maps.LatLng(42.710948274616186, -84.5372200012207),
				new google.maps.LatLng(42.68268778214011, -84.53739166259766),
				new google.maps.LatLng(42.68268778214011, -84.46220397949219)
			];
			
			var central_campus_coords = [
				new google.maps.LatLng(42.73528740186016, -84.48503494262695),
				new google.maps.LatLng(42.73289174571287, -84.49378967285156),
				new google.maps.LatLng(42.71157894243346, -84.4932746887207),
				new google.maps.LatLng(42.71157894243346, -84.46186065673828),
				new google.maps.LatLng(42.7289828449078, -84.46271896362305)
			];
			var chosen_coords = [];
			chosen_coords.push(central_campus_coords);
			var loc = parseInt(urlParams.location);
			var locs = readLocs(loc);
			
			for(var i = 0; i < locs.length; ++i)
			{
				if(locs[i] == 1)
					chosen_coords.push(northeast_campus_coords);
				if(locs[i] == 2)
					chosen_coords.push(northwest_campus_coords);
				if(locs[i] == 4)
					chosen_coords.push(west_campus_coords);
				if(locs[i] == 8)
					chosen_coords.push(south_campus_coords);
				if(locs[i] == 16)
					chosen_coords.push(east_campus_coords);
			}
			
			var areas = [];
			for(var i = 0; i < chosen_coords.length; ++i)
			{
				if(i == 0)
					createCampus(chosen_coords[i]);
				else
					areas.push(createPoly(chosen_coords[i]));
			}

			//createPoly(northwest_campus_coords);
			//createPoly(west_campus_coords);
			//createPoly(south_campus_coords);
			function createCampus(coords)
			{
				poly = new google.maps.Polygon({
				paths: coords,
				strokeColor: '#0A6D0E',
				strokeOpacity: 0.8,
				strokeWeight: 1,
				fillColor: '#0A6D0E',
				fillOpacity: 0.5
				});
				poly.setMap(map);
				return poly;
			}
			function createPoly(coords)
			{
				poly = new google.maps.Polygon({
				paths: coords,
				strokeColor: '#597E69',
				strokeOpacity: 0.8,
				strokeWeight: 1,
				fillColor: '#597E69',
				fillOpacity: 0.1
				});
				poly.setMap(map);
				return poly;
			}
			
			var topleftCoord = chosen_coords[0];
			var bottomRightCoords = chosen_coords[chosen_coords.length - 2];
			

				$("input[name=googleMapsData]").each(function(index, i)
				{
					var $i = $(i);
					if(!$i.parent().is(":visible"))
						return true;
					var val = $i.val();
					var actual = val.replace(/\\/g, '');
					if(actual == "")
					{
						
						$i.parent().hide();
						return true;
					}
					
					if(!$i.parent().is(":visible"))
						return true;
					
					var googleMapsData = JSON.parse(actual);
					
					if(googleMapsData && googleMapsData.LatLng)
					{
						var myLatLng = new google.maps.LatLng(googleMapsData.LatLng.nb,googleMapsData.LatLng.ob);

						var found = false;
						
							
						if(!urlParams.location)
						{
							found = true;
						}else if(areas.length > 0){
							for(var k = 0; k < areas.length; ++k)
							{
								if(areas[k].containsLatLng(myLatLng))
								{
									found = true;
									break;
								}
							}
						}else{
							found = true;
						}
						
						if(found)
						{
							var marker = new google.maps.Marker({
								position: myLatLng,
								map: map,
								title:"Location",
								icon: '/wp-content/themes/myspartansublease/images/star.png'
							});
							$i.parent().data('marker-id',map_markers.length);
							$i.parent().addClass("clickable");
							google.maps.event.addListener(marker, 'click', function() {
								infowindow.open(map,marker);
								infowindow.setContent($i.parent().find(".address").text());
								$(".sublease").removeClass("active");
								$i.parent().addClass("active");
								for(var i = 0; i < map_markers.length; ++i)
								{
									map_markers[i].setIcon('/wp-content/themes/myspartansublease/images/star.png');
								}
								marker.setIcon('/wp-content/themes/myspartansublease/images/star_selected.png');
								map.panTo(marker.getPosition());
							 });
							 google.maps.event.addListener(marker, 'dblclick', function() {
								infowindow.open(map,marker);
								infowindow.setContent($i.parent().find(".address").text());
								$(".sublease").removeClass("active");
								$i.parent().addClass("active");
								map.setZoom(15);
								setTimeout(function(){
								map.setCenter(marker.getPosition());
								},200);
								for(var i = 0; i < map_markers.length; ++i)
								{
									map_markers[i].setIcon('/wp-content/themes/myspartansublease/images/star.png');
								}
								marker.setIcon('/wp-content/themes/myspartansublease/images/star_selected.png');
							 });
							 map_markers.push(marker);
							 
							$i.parent().click(function(e)
							{
								var markerid = $(this).data('marker-id');
								google.maps.event.trigger(map_markers[markerid], 'click');
							});
						}else{
							$i.parent().hide();
						}

					}
					
				});
			//map.fitBounds(new google.maps.LatLngBounds(topleftCoord,bottomRightCoords));
			map.setZoom(12);
		
		}
		
		function readLocs(loc)
		{
			var locs = [];
			
			if((loc & 1) == 1)
				locs.push(1);
			if((loc & 2) == 2)
				locs.push(2);
			if((loc & 4) == 4)
				locs.push(4);
			if((loc & 8) == 8)
				locs.push(8);
			if((loc & 16) == 16)
				locs.push(16);
				
			return locs;
		}
		
		function readUrlParams(urlParams)
		{
			
			if(typeof(Storage)!=="undefined")
			{
				if(!urlParams.location)
					urlParams.location = localStorage["locs"];
				if(!urlParams.semesters)
					urlParams.semesters = localStorage["semesters"];
				if(!urlParams.minprice)
					urlParams.minprice = localStorage["minprice"];
				if(!urlParams.maxprice)
					urlParams.maxprice = localStorage["maxprice"];
			}
			
			var loc = parseInt(urlParams.location);

			locs = readLocs(loc);
			
			$("#Location").selectpicker('val',locs);
				
			var semesters = parseInt(urlParams.semesters);
			if((semesters & 1) == 1)
			{
				$("input[name=semesterSpring]").prop("checked",true);
			}
			if((semesters & 2) == 2)
			{
				$("input[name=semesterSummer]").prop("checked",true);
			}
			if((semesters & 4) == 4)
			{
				$("input[name=semesterFall]").prop("checked",true);
			}
			$("input[type=checkbox]").each(function(i,c)
			{
				if($(c).is(":checked"))
				{
					$(c).parent().addClass("active");
				}
			});
			var min = parseInt(urlParams.minprice);
			var max = parseInt(urlParams.maxprice);
			if(!min)
			{
				min = 0;
			}
			if(!max)
			{
				max = 1000;
			}
			$("#priceRange").data("sliderValue",[min,max]);
			slider.setValue(min,max);
			slider.updateSlider(".price-range-left",".price-range-right");
			
			
		}
		
		
		
		function onSearch(e)
		{
			var qs = "";
		
			var loc = $("#Location").val();
			if(loc != null)
			{
				var all_locs = 0;
				for(var i = 0; i < loc.length; ++i)
				{
					all_locs = all_locs | parseInt(loc[i]);
				}
				qs += "&location=" + all_locs;
			}
			
			var semesters = 0;
			if($("input[name=semesterSpring]").is(":checked"))
			{
				semesters = semesters | 1;
			}
			if($("input[name=semesterSummer]").is(":checked"))
			{
				semesters = semesters | 2;
			}
			if($("input[name=semesterFall]").is(":checked"))
			{
				semesters = semesters | 4;
			}
			if(semesters != 0)
			{
				qs += "&semesters=" + semesters;
			}
			
			var minprice = $(".price-range-left").text().replace("$","");
			var maxprice = $(".price-range-right").text().replace("$","");
			
			qs += "&minprice=" + $(".price-range-left").text().replace("$","");
			qs += "&maxprice=" + $(".price-range-right").text().replace("$","");
			
			createQuery(loc, semesters, minprice, maxprice);
			window.location.search = qs;
			
			
		}
		
		function checkSubleases()
		{
			$(".sublease").each(function(index, s)
			{
				var search_result = false;
				var $s = $(s);
				var price = parseInt($s.find(".price").text().replace("$",""));
				var semester_fall = $s.find("input[name=fall]").val() == "1";
				var semester_spring = $s.find("input[name=spring]").val() == "1";
				var semester_summer = $s.find("input[name=summer]").val() == "1";
				
				var fall_checked = $("input[name=semesterFall]").is(":checked");
				var spring_checked = $("input[name=semesterSpring]").is(":checked");
				var summer_checked = $("input[name=semesterSummer]").is(":checked");
				
				if(fall_checked && semester_fall)
				{
					search_result = true;
				}
				if(spring_checked && semester_spring)
				{
					search_result = true;
				}
				if(summer_checked && semester_summer)
				{
					search_result = true;
				}
				
				var min = parseInt($(".price-range-left").text().replace("$",""));
				var max = parseInt($(".price-range-right").text().replace("$",""));
				
				if(price < min || price > max)
				{
					search_result = false;
				}else if(!spring_checked && !fall_checked && !summer_checked && price >= min && price <= max)
				{
					search_result = true;
				}
				
				if(!search_result)
				{
					$s.hide();
				}
				
			});
		}
	});
	
</script>
<div class='toolbar'>
	<div class='inner-wrapper'>
		<div class='toolbar-option'>
			<button id="Search" class='btn btn-primary'>
				Search
			</button>
		</div>
		<div class='toolbar-option' style='width: 276px;'>
			<div>Price Range: <span class="dark b"><span class="price-range-left">$0</span> - <span class="price-range-right">$999</span></span></div>
			<input type="text" class="span2" data-slider-min="100" data-slider-max="1000" data-slider-step="10" data-slider-value="[400,600]" id="priceRange" name='princeRange'>
		</div>
		<div class='toolbar-option' >
			<div class='semester btn-group' data-toggle="buttons">
				<label class='btn btn-primary'>
					<input type='checkbox' name='semesterSpring'>
					Spring
				</label>
				<label class='btn btn-primary'>
					<input type='checkbox' name='semesterSummer'>
					Summer
				</label>
				<label class='btn btn-primary'>
					<input type='checkbox' name='semesterFall'>
					Fall
				</label>
			</div>
			<div style='clear:both;'></div>
		</div>
		<div class='toolbar-option'>
			
			<select id="Location" multiple>
				<option value='1'>Northeast</option>
				<option value='2'>Northwest</option>
				<option value='4'>West</option>
				<option value='8'>South</option>
				<option value='16'>East</option>
			</select>
		</div>
		<div style='clear:both;'></div>
	</div>
</div>
<div class='map-wrapper'>
	<div class='map'>
		<div id='map-canvas'>
		
		</div>
	</div>
</div>
<div class='subleases'>
<?php

while($q->fetch())
{
?>
	<div class='sublease light'>
		<input type='hidden' name='googleMapsData' value='<?php echo $googleMapsData; ?>'/>
		<input type='hidden' name='requestId' value='<?php echo $requestId; ?>'/>
		<input type='hidden' name='spring' value='<?php echo (($semesters & 1) == 1) ? '1' : '0'; ?>'/>
		<input type='hidden' name='summer' value='<?php echo (($semesters & 2) == 2) ? '1' : '0'; ?>'/>
		<input type='hidden' name='fall' value='<?php echo (($semesters & 4) == 4) ? '1' : '0'; ?>'/>
		<div class='image'>
			<a class='cboxElement' href="<?php echo $path; ?>">
				<img src='<?php echo $path; ?>'/>
			</a>
		</div>
		<div class='info'>
			<div class='price'>
				$<?php echo $price; ?>
			</div>
			<div class='address'>
				<?php echo $address; ?>
			</div>
			<div style='clear:both;'></div>
			<div class='movedates'>
				Move-In Date: <b><?php echo $moveInDate; ?> </b>
				<br/>
				Move-Out Date: <b><?php echo $moveOutDate; ?> </b>
			</div>
			<div style='clear:both;'></div>
			<div class='inclusion'>
				<?php echo ($petsAllowed == 1) ? "<h5 class='allowed'>Pets Allowed</h5>" : "<h5 class='not-allowed'>Pets Not Allowed</h5>"; ?>
				<?php echo ($utilitiesIncluded == 1) ? "<h5 class='allowed'>Utilities Included</h5>" : "<h5 class='not-allowed'>Utilities Not Included</h5>"; ?>
			</div>
		</div>
		<div style='clear:both;'></div>
		<div class='btn-wrap contact'>
			<a href="/view-sublease/?id=<?php echo $requestId; ?>" target="_blank">
				<button class='btn btn-default'>View Details</button>
			</a>
		</div>
	</div>	
<?php
}
?>
</div>
<div style='clear:both;'></div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>