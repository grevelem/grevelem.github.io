<?php
/**
 * The home page for My Spartan Sublease
 */

get_header(); 

include $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/sublease-manager/public.php';

if(!isset($_GET["id"]))
	fail_page();
	
$id = $_GET["id"];
	
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
	p.id, p.RequestId, p.State, p.DTSInserted AS postingdtsInserted, 
	r.FirstName, r.LastName, r.Price, r.Semesters, r.Address, 
	r.ContactNumber, r.RoomsAvailable, r.TimeToBeReached, r.GoogleMapsData,
	r.MoveInDate, r.MoveOutDate, r.PetsAllowed, r.UtilitiesIncluded, r.ApartmentGroup
	
	FROM mss_postings p 
	JOIN mss_requests r
		ON p.RequestId = r.id
	WHERE r.id = ?
	",
	$q, $mysqli);
check($m);

$q->bind_param('i',$id);
$m = _execute($q, $mysqli);
check($m);

$q->bind_result($postingId, $requestId, $state, $postingdtsInserted
, $firstName, $lastName, $price, $semesters, $address, $contactNumber, $roomsAvailable,
$timeToBeReached, $googleMapsData, $moveInDate, $moveOutDate, $petsAllowed, $utilitiesIncluded, $apartmentGroup);

if(!$q->fetch())
{
	fail_page_error("no result for id given");
}



if($state != STATE_LIVE)
	fail_page_error("Invalid posting.");
	
$q->close();

?>

<script type="text/javascript">
	$(function()
	{
		//$(".group1").colorbox({rel:"group1"});
		var $modals = $(".modal");
		
		$modals.modal({
			show:false
		});
		$("#ContactForm").validate({
			rules: {
				email:
				{
					required: true,
					email: true
				}
			},
			errorClass: "error",
			validClass: "valid"
		});
		$("#SubmitContact").click(function(e)
		{
			e.preventDefault();
			if($("#ContactForm").valid())
			{
				$("#ContactForm").ajaxSubmit({ dataType: 'json', success: contactSuccess, error: contactError });
				$(this).addClass("loading");
				disableThings();
			}
		});
		
		function disableThings()
		{
			$modals.each(function(i, m)
			{
				$(m).modal({
					show: false,
					backdrop: 'static',
					keyboard: false
				});
			});
			$("button").addClass("disabled");
		}
		
		function enableThings()
		{
			$modals.each(function(i, m)
			{
				$(m).modal({
					show: false,
					backdrop: true,
					keyboard: true
				});
			});
			$("button").removeClass("disabled");
		}
		
		$("input[name=contactNumber]").mask('(000) 000-0000');
		function contactError()
		{
			enableThings();
			$("#SubmitContact").removeClass("loading disabled");
			$("button").removeClass("disabled");
			ShowMessage("Oops!", "There was an internal problem processing your request. Try again later.");
		}
		function contactSuccess(response, statusText, xhr, $form)
		{
			enableThings();			
			$("#SubmitContact").removeClass("loading disabled");
			if(response.success)
			{
				ShowMessage("Success", response.message);
			}else{
				ShowMessage("Oops!", "There was a problem with your contact request: <br/>" + response.message);
			}
		}
			
		function ShowMessage(title, message)
		{
			$modals.modal('hide');
			var $messageModal = $("#Message");
			var $messageModalTitle = $messageModal.find(".modal-title");
			var $messageModalContent = $messageModal.find(".message");
			$messageModalTitle.text(title);
			$messageModalContent.html(message);
			$messageModal.modal('show');
		}
			
		$(".contact .btn").click(function()
		{
			$("#ContactOwnerForm").modal('show');
		});
		$(".photo .zoom").click(function()
		{
			$(this).siblings("img").click();
		});
		$(".photo img").click(function()
		{	
			setBigPhoto($(this));
		});
		setBigPhoto($(".photo img").first());
		function setBigPhoto($photoImg)
		{
			$(".photo").removeClass("selected");
			$photoImg.parent().addClass("selected");
			var imgSrc = $photoImg.attr("src");
			var $bigPhoto = $(".big-photo");
			$bigPhoto.find("img").fadeOut(function(){
				var $img = $(jQuery.parseHTML("<img src='" + imgSrc + "'/>")).load(function(){
					
					
					$bigPhoto.empty().append($(this));
					$(this).hide();
					$(this).width($(".big-photo").width());
					$(this).css({ position:"relative", top: (-($(this).height() / 2) + $bigPhoto.height() / 2)  + "px", left: (-($(this).width() / 2) + $bigPhoto.width() / 2) + "px"});
					$(this).fadeIn();
				});
			});
		}
	});
</script>
<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/view_sublease.css" type="text/css">		
<div class="modal fade" id="Message">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Thanks</h4>
	  </div>
	  <div class="modal-body">
		<div class='message'>
			Your message has been sent.
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary full" data-dismiss="modal">Okay</button>
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="ContactOwnerForm">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Contact Owner</h4>
	  </div>
	  <div class="modal-body">
		<div class='explanation'>
			To contact the owner of this sublease, please fill out the information below.
		</div>
		<form id="ContactForm" method="POST" action="/wp-content/themes/myspartansublease/management/public_contact_owner.php">
		<input type='hidden' name='postingId' value="<?php echo $postingId; ?>"/>
		<div class='input half'>				
			<div class='name'>
				First Name<span class='required'>*</span>
			</div>
			<div class='field'>
				<input type='text' name='firstName' required/>
			</div>
		</div>
		<div class='input half'>				
			<div class='name'>
				Last Name<span class='required'>*</span>
			</div>
			<div class='field'>
				<input type='text' name='lastName' required/>
			</div>
		</div>
		<div style='clear:both;'></div>
		
		<div class='input half'>				
			<div class='name'>
				Phone Number<span class='required'>*</span>
			</div>
			<div class='field'>
				<input type='text' name='contactNumber' required/>
			</div>
		</div>
		<div class='input half'>				
			<div class='name'>
				Email Address<span class='required'>*</span>
			</div>
			<div class='field'>
				<input type='text' name='email' required/>
			</div>
		</div>
		<div style='clear:both;'></div>
		</form>
	  </div>
	  <div class="modal-footer">
		<button id="SubmitContact" type="button" class="btn btn-primary full">Send Message</button>
		<button type="button" class="btn btn-default full" data-dismiss="modal">Cancel</button>
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class='go-back light'>
	<div class='link'>
		<div class='link-icon left-arrow'>
		</div>
		<div class='link-text'>
			<a href="/browse">Go Back</a>
		</div>
	</div>
</div>
<div class='content'>
	<input type='hidden' name='googleMapsData' value='<?php echo $googleMapsData; ?>'/>
	<div class='details light'>
		<div class='address dark'>
			<h4><?php echo $address; ?></h4>
		</div>
		<div class='price med'>
			<h5>$<?php echo $price; ?>.00 / mo.</h5>
		</div>
		<div class='other'>
			<div class='semesters'>
				<h5>Semesters</h5>
				<div class='semester spring <?php if(($semesters & 1) == 1){ echo "active"; } ?>'>Spring</div>
				<div class='semester summer <?php if(($semesters & 2) == 2){ echo "active"; } ?>'>Summer</div>
				<div class='last semester fall <?php if(($semesters & 4) == 4){ echo "active"; } ?>'>Fall</div>
				
				
				
				<div style='clear:both;'></div>
			</div>
			<div class='separator'></div>
			<div class='owner'>
				Owner: <?php echo $firstName . " " . $lastName; ?>
			</div>
			<div class='phone'>
				Phone: <?php echo $contactNumber; ?>
			</div>
			<div class='separator'></div>
			<div class='info'>
				Move-In Date: <b><?php echo $moveInDate; ?></b>
			</div>
			<div class='info' style='margin-bottom:30px;'>
				Move-Out Date: <b><?php echo $moveOutDate; ?></b>
			</div>
			<?php if($petsAllowed == 1){ ?>
			<div class='special-inclusion petsAllowed'>
				Pets Allowed
			</div>
			<?php }else{ ?>
			<div class='not-included utilitiesIncluded'>
				Pets Not Allowed
			</div>
			<?php }?>
			<?php if($utilitiesIncluded == 1){ ?>
			<div class='special-inclusion utilitiesIncluded'>
				Utilities Included
			</div>
			<?php }else{ ?>
			<div class='not-included utilitiesIncluded'>
				Utilities Not Included
			</div>
			<?php }?>
		</div>
		<div class='contact'>
			<button class='btn btn-primary'>Contact Owner</button>
		</div>
	</div>
	<div class='photos'>
		<div class='all-photos'>
<?php

$m = _prepare("
	SELECT 
	u.Path, u.DTSInserted, pim.UploadOrder
	FROM mss_post_image_mappings pim
	JOIN mss_postings p
		ON p.id = pim.PostingId
	JOIN mss_uploads u
		ON pim.UploadId = u.id
	WHERE p.RequestId = ?
	ORDER BY pim.UploadOrder
	",
	$q, $mysqli);
check($m);

$q->bind_param('i',$id);
$m = _execute($q, $mysqli);
check($m);

$q->bind_result($path, $dtsInserted, $order);
while($q->fetch())
{
?>
	<div class='photo'>
		<div class='zoom'></div>
		
			<img src='<?php echo $path; ?>'/>
		
	</div>
<?php
}

$q->close();
$mysqli->close();
?>
	<div style='clear:both;'></div>
		</div> <!-- all photos -->
		<div class='big-photo'>
			<img src="" />
		</div>
	</div>
	<div style='clear:both;'></div>

</div>



<?php get_sidebar(); ?>
<?php get_footer(); ?>