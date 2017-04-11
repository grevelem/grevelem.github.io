<?php
/**
 * The home page for My Spartan Sublease
 */

get_header(); ?>
<script type="text/javascript">
$(function()
{
	$("#priceRange").slider({tooltip:'hide'});
	updateSlider();
	
	$("#priceRange").on('slide',function(ev)
	{
		updateSlider();
	});
		
	$("#phone").mask('(000) 000-0000');
	
	function updateSlider()
	{
		var value = $("#priceRange").data("value");
		if(value == undefined)
			value = $("#priceRange").data("sliderValue");
		$(".price-range-left").text("$" + value[0]);
		$(".price-range-right").text("$" + value[1]);
		$(".slider").width('100%');
	}
	
	$("#the-map").mapster(
	{ 
	 mapKey: 'data-key',
	render_select: { altImage: "/wp-content/themes/myspartansublease/images/map_full.png", highlight: true },
	onClick : mapClick
	}
	);
	var zones_selected = 0
	function mapClick(e)
	{
		if(e.selected)
			zones_selected++
		else
			zones_selected--;
		if(zones_selected == 1)
			$(".zones-selected").text(zones_selected + " zone selected.");
		else
			$(".zones-selected").text(zones_selected + " zones selected.");
		$("input#" + e.key).val(e.selected ? "1" : "0");
	};
	
	$("#selectAll").click(function()
	{
		if($("#selectAll").is(":checked"))
		{
			$('area[data-key="northwest"]').mapster('select');
			$('area[data-key="northeast"]').mapster('select');
			$('area[data-key="east"]').mapster('select');
			$('area[data-key="west"]').mapster('select');
			$('area[data-key="south"]').mapster('select');
			$(".zones-selected").text(5 + " zones selected.");
		}else{
			$('area[data-key="northwest"]').mapster('deselect');
			$('area[data-key="northeast"]').mapster('deselect');
			$('area[data-key="east"]').mapster('deselect');
			$('area[data-key="west"]').mapster('deselect');
			$('area[data-key="south"]').mapster('deselect');
			$(".zones-selected").text(0 + " zones selected.");
		}
		
	});

	$("#createSublease").validate({
		rules: {
			email:
			{
				required: true,
				email: true
			}
		},
		errorClass: "error",
		validClass: "valid",
		errorPlacement: function(error, element)
		{
			
		}
		
	});
	$(".lease-it-button").click(function()
	{
		if($("#createSublease").valid())
		{
			if(!$(".lease-it-button").hasClass(".loading-button"))
			{
				$(".lease-it-button").addClass("loading-button");
				$(".lease-it-button").siblings(".loading").show();
				$("#createSublease").ajaxSubmit({ dataType: 'json', success: createSubleaseResponse, error: createSubleaseError });
			}
		}
	});
	$(".find-it-button").click(function(e)
	{
		var locs = [];
		if($("#the-map").mapster('get',"northeast"))
			locs.push(1);
		if($("#the-map").mapster('get',"northwest"))
			locs.push(2);
		if($("#the-map").mapster('get',"west"))
			locs.push(4);
		if($("#the-map").mapster('get',"south"))
			locs.push(8);
		if($("#the-map").mapster('get',"east"))
			locs.push(16);
		
		var minprice = $(".price-range-left").text().replace("$","");
		var maxprice = $(".price-range-right").text().replace("$","");
		
		var semesters = 0;
		
		if($("input[name=semester-fall]").is(":checked"))
		{
			semesters = semesters | 1;
		}
		if($("input[name=semester-spring]").is(":checked"))
		{
			semesters = semesters | 2;
		}
		if($("input[name=semester-summer]").is(":checked"))
		{
			semesters = semesters | 4;
		}
		
		window.location = "/browse?" + createQuery(locs, semesters, minprice, maxprice);
	});
	
	function createSubleaseError(e)
	{
		$(".lease-it-button").removeClass("loading-button");
		$(".lease-it-button").siblings(".loading").hide();
		alert("There was an internal problem submitting your request. Please try again later.");
	};
	
	function createSubleaseResponse(response, statusText, xhr, $form)
	{
		if(response.success)
		{
			window.location = "/submission-received/";
		}
		else
		{
			$(".lease-it-button").removeClass("loading-button");
			$(".lease-it-button").siblings(".loading").hide();
			alert("There was a problem submitting your request. Please make sure all fields are filled out correctly and try again.");
		}
	}
});
</script>
<img style='display:none;' src="/wp-content/themes/myspartansublease/images/find_it_hover.png">
<img style='display:none;' src="/wp-content/themes/myspartansublease/images/lease_it_hover.png">
<div class="sections">

	<div class="section findSublease">
		<div style="float:right;">
			<div class="sub light browse-all-listings">
				<a href="/browse">Browse all listings</a>
			</div>
		</div>
		<h1 class="light">
			Find a Sublease
		</h1>
		<div style="clear:both;"></div>
		<div class="body light">
			<h3>Price Range: <span class="dark b"><span class="price-range-left">$0</span> - <span class="price-range-right">$999</span></span></h3>
			<div class="input">
				<!-- range selector -->
				<input type="text" class="span2" data-slider-min="100" data-slider-max="1000" data-slider-step="10" data-slider-value="[400,600]" id="priceRange" name='princeRange'>
			</div>
			<h3>Semesters</h3>
			<div class="input btn-group semester-selector" data-toggle="buttons">
				<!-- semester selector -->
				<label class="btn btn-primary">
					<div class='inner'></div>
					<input type="checkbox" name='semester-fall'>Fall</input>
				</label>
				
				<label class="btn btn-primary">
					<div class='inner'></div>
					<input type="checkbox" name='semester-spring'>Spring</input>
				</label>
				
					<label class="btn btn-primary">
						<input type="checkbox" name='semester-summer'>Summer</input>
					</label>
				
			</div>
			<div class='map'>
			
			<map id="campus-map" name="campus-map">
				<area shape="poly" alt="" title="" coords="1,1,148,1,148,76,120,90,1,90" href="" target="" data-key="northwest" />
				<area shape="poly" alt="" title="" coords="149,1,149,77,297,131,297,0,278,0" href="" target="" data-key="northeast" />
				<area shape="rect" alt="" title="" coords="1,91,119,168" href="" target="" data-key="west" />
				<area shape="rect" alt="" title="" coords="1,169,214,232" href="" target="" data-key="south"/>
				<area shape="poly" alt="" title="" coords="299,232,297,132,213,103,215,230" href="" target="" data-key="east" />
			</map>
				<input type="hidden" id="northwest" name="campus-northwest" value="0"/>
				<input type="hidden" id="northeast" name="campus-northeast" value="0"/>
				<input type="hidden" id="west" name="campus-west" value="0"/>
				<input type="hidden" id="south" name="campus-south" value="0"/>
				<input type="hidden" id="east" name="campus-east" value="0"/>
				<div class="campus-map">
					<img id='the-map' src='wp-content/themes/myspartansublease/images/map.png' usemap="#campus-map"/>
				</div>
				<div class="desc">
					<h3>Select Locations</h3>
					<p>
						Choose one or more zones on the left.<br>
						<span class="zones-selected">0 zones selected.</span>
					</p>
					<p>
						<input type="checkbox" id="selectAll"/> Select all locations
					</p>
					<div class="find-it-button"></div>
					<div style='clear:both'></div>
				</div>
				<div style='clear:both;'></div>
			</div>
		</div>
	</div>
	<form id="createSublease" method="POST" action="/wp-content/themes/myspartansublease/management/public_create_sublease.php">
		<div class="section createSublease">
		<h1 class="dark">
			Create a Sublease
		</h1>
		<div class="body dark">
			<div class='input half'>
				<div class='name'>
					First Name<span class='required'>*</span>
				</div>
				<div class='field'>
					<input type='text' name='firstName' id='firstName' required/>
				</div>
			
			</div>
			<div class='input half'>
				<div class='name'>
					Last Name<span class='required'>*</span>
				</div>
				<div class='field'>
					<input type='text' name='lastName' id='lastName' required/>
				</div>
			
			</div>
			<div style="clear:both;"></div>
			<div class='input'>
				<div class='name'>
					Address<span class='required'>*</span>
				</div>
				<div class='field'>
					<input type='text' name='address' id='address' required/>
				</div>
			</div>
			<div class='input group'>
				<div class='name'>
				I want to lease for the following semesters
				</div>
				<div class="btn-group semester-selector" data-toggle="buttons" style='width:588px;'>
					<!-- semester selector -->
					<label class="btn btn-primary">
						<input id="semesterFallCreate" type="checkbox" name='semesterFall'>Fall</input>
					</label>
					<label class="btn btn-primary">
						<input id="semesterSpringCreate" type="checkbox" name='semesterSpring'>Spring</input>
					</label>
					<label class="btn btn-primary">
					
						<input id="semesterSummerCreate" type="checkbox" name='semesterSummer'>Summer</input>
					</label>
				</div>
			</div>
			<div class='input fourth'>
				<div class='name'>
					Email Address<span class='required'>*</span>
				</div>
				<div class='field'>
					<input type='text' name='email' id='email' required/>
				</div>
			</div>
			<div class='input fourth'>
				<div class='name'>
					Phone Number<span class='required'>*</span>
				</div>
				<div class='field'>
					<input type='text' name='phone' id='phone' required/>
				</div>
			</div>
			<div class='input half'>
				<div class='name'>
					Preferred Time to be Reached
				</div>
				<div class='field'>
					<input type='text' name='timeToBeReached' id='timeToBeReached'/>
				</div>
			</div>
			<div style='clear:both;'></div>
			<div class='input terms'>
				<div class='wrapper'>
					<input name='agreeToTerms' type='checkbox' required/>
					<label>I have read the <a href='#'>My Spartan Sublease Terms & Conditions</a></label>
					<div style='clear:both;'></div>
				</div>
			</div>
			<div class='lease-it-button-wrapper'>
				<div class='loading'></div>
				<div class='lease-it-button'></div>
			</div>
			<div class='errors'>
				<span class='required'>*</span> Denotes a required field.
			</div>
			<div style='clear:both'></div>
		</div>
	</div>
	</form>
	<div style="clear:both;"></div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>