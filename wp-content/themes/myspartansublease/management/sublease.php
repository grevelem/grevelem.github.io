<?php
/*
	Name: sublease.php
	Authors: Xavier Durand-Hollis Jr
	
	A form that can be used to create a sublease posting that will be pending review
	by an administrator
*/
if(isset($_GET["id"]))
{
	$user_id = $_GET["id"];

}

include 'security.php';
include 'states.php';
get_header(); 
?>

			
<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/management.css" type="text/css">
<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/colorbox.css" type="text/css">
<div id='adminNav'>
	<button class='btn btn-primary' href="/wp-admin/options-general.php?page=sublease-manager">Go to Sublease Manager</button>
</div>

<?php 
if(is_user_logged_in())
{

	include $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/sublease-manager/common.php'; // database connection
	config($mysqli);
	
	$READ_ONLY_MODE = false;
	
/*
	For now we only require that the user be logged in. This could be a huge security
	vulnerability if registration on the site was opened and people found out about this
	form.
*/

	function textfield($field, $fieldName, $readOnly)
	{
		?>
			<input name='<?php echo $fieldName; ?>' type='text' value='<?php echo $field; ?>' required/>
		<?php
	}
	function datefield($field, $fieldName, $readOnly)
	{
		if($field == "Never")
		{
			$field = date("m-d-Y");
		}
		?>
			<input class='datepicker' name='<?php echo $fieldName; ?>' type='text' value='<?php echo $field; ?>' data-date-format="mm-dd-yyyy" required/>
		<?php
	}
	function checkbox($field, $fieldName, $readOnly)
	{
		?>
			<input name='<?php echo $fieldName; ?>' type='checkbox' <?php echo ($field == 1) ? "checked" : ""; ?>/>
		<?php
	}
	if(isset($user_id))
	{
		$query = "SELECT 
		r.id, r.Actionable, r.FirstName, r.LastName, r.Email, r.Price, r.Semesters,
		r.Address, r.ContactNumber, r.RoomsAvailable, r.TimeToBeReached, r.GoogleMapsData,
		r.MoveInDate, r.MoveOutDate, r.PetsAllowed, r.UtilitiesIncluded, r.ApartmentGroup, r.DTSInserted,
		p.id AS PostingId, p.State, p.DTSInserted
		FROM mss_requests r
		LEFT JOIN mss_postings p ON r.id = p.RequestId 
		WHERE r.id = ?";
		if($q = $mysqli->prepare($query))
		{
			
			$q->bind_param('i',$id);
			$id = $user_id;
			$q->execute();
			$q->bind_result(
			$my_id, $actionable, $firstName, $lastName, $email, $price, 
			$semesters, $address, $contactNumber, $roomsAvailable, 
			$timeToBeReached, $googleMapsData, $moveInDate, $moveOutDate, $petsAllowed,
			$utilitiesIncluded, $apartmentGroup
			,$dtsInserted, $postingId, $state, $postingdtsInserted
			);
			
			
			if($q->fetch())
			{
				if(!isset($state))
				{
					$state = STATE_UNKNOWN; // no posting was joined
				}
				$READ_ONLY_MODE = read_only($state);
				
				if($actionable == 0 || $actionable == 1)
				{
			?>
			<script type='text/javascript' src='/wp-content/themes/myspartansublease/js/jquery.colorbox-min.js'></script>
			<script type='text/javascript' src='/wp-content/themes/myspartansublease/js/jquery.sticky.js'></script>
			<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/mss.googlemaps.js"></script>
			<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/mss.common.js"></script>
			<script type='text/javascript' src='sublease.js'></script>
			
			<div class="modal fade checklist">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Submission Checklist</h4>
				  </div>
				  <div class="modal-body">
					<p>Please confirm you have done the following</p>
					<div class='input' data-toggle="buttons">
						<label class='btn btn-checkbox'>
							<input class="confirm" type='checkbox'/> Accepted payment?
						</label>
					</div>
					<div class='input' data-toggle="buttons">
						<label class='btn btn-checkbox'>
							<input class="confirm" type='checkbox'/> Signed sales agreement?
						</label>
					</div>
				  </div>
				  <div class="modal-footer">
					<button id="SubmitRequestFinal" type="button" class="btn btn-primary full disabled">Submit Request</button>
					<button type="button" class="btn btn-default full" data-dismiss="modal">Cancel</button>
				  </div>
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			

			<div class="modal fade confirmation">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Confirmation</h4>
				  </div>
				  <div class="modal-body">
					<p class='confirm-text'>confirmation text</p>
				  </div>
				  <div class="modal-footer">
					<button id="ConfirmAction" type="button" class="btn btn-primary full">Yes</button>
					<button type="button" class="btn btn-default full" data-dismiss="modal">No</button>
				  </div>
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<input type='hidden' name='admin' value='<?php echo (UserIsAdmin() ? 1 : 0); ?>'/>
			<input type='hidden' name='state' value='<?php echo $state; ?>'/>
			<input type='hidden' name='readonly' value='<?php echo ($READ_ONLY_MODE ? 1 : 0); ?>'/>
			<div class='section standalone' data-id='<?php echo $my_id; ?>'>
				<div class='body light'>
					<form id="FormCreatePosting" action="/wp-content/themes/myspartansublease/management/create_posting.php" method="post">
						<h2 class="state-review" style='color:red;'>
							This posting is currently awaiting review.
						</h2>
						<h2 class="state-rejected" style='color:red;'>
							This posting was rejected or unlisted by an administrator.
						</h2>
						<h3 class="state-live" style='color:green;'>
							This posting is live on My Spartan Sublease.
						</h3>
						<button class='state-live btn btn-primary' style='width:100%; margin-bottom:10px;' href="/view-sublease/?id=<?php echo $my_id; ?>">View Posting</button>

						<div class='state-readonly' style='font-size:0.8em; padding-left:5px; color:red;'>This posting is in Read-Only mode. No changes can be made.</div>
						
						<?php
						}
						if(isset($postingdtsInserted))
						{
						?>
							<input type='hidden' name='postingId' value='<?php echo $postingId; ?>'/>
							<div style='font-size:0.8em; padding-left:5px; margin-top:5px;'>
								Posting created on 
								<?php echo $postingdtsInserted; ?>
							</div>
						<?php
						}
						?>
						
						
						<input type='hidden' name='id' value='<?php echo $my_id; ?>'/>
						<input type='hidden' name='photoUpload1' value=""/>
						<input type='hidden' name='photoUpload2' value=""/>
						<input type='hidden' name='photoUpload3' value=""/>
						<input type='hidden' name='photoUpload4' value=""/>
						<h2>
							Provided Details
						</h2>
						<div class='dts input'>
							Request created <span class='time-created'><?php echo $dtsInserted; ?></span>
						</div>
						<div class='input half'>
							<label>First Name</label>
							<div class='field'>
								<?php textfield($firstName, 'firstName', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div class='input half'>
							<label>Last Name</label>
							<div class='field'>
								<?php textfield($lastName, 'lastName', $READ_ONLY_MODE); ?>
							</div>
						</div>					
						<div class='input'>
							<label>Address</label>
							<div class='field'>
								<?php textfield($address, 'address', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div style='clear:both;'></div>
						<div class='input'>
							<div><label>Semesters to Lease For</label></div>
							<div class="btn-group semester-selector" style='width: 591px;' data-toggle="buttons">
								<!-- semester selector -->
								<label class="btn btn-primary <?php if(($semesters & 1) == 1){ echo "active"; } ?>">
									<input type="checkbox" name='semesterFall'/>Fall
								</label>
								<label class="btn btn-primary <?php if(($semesters & 2) == 2){ echo "active"; } ?>">
									<input type="checkbox" name='semesterSpring'/>Spring
								</label>
								<label class="btn btn-primary <?php if(($semesters & 4) == 4){ echo "active"; } ?>">
									<input type="checkbox" name='semesterSummer'/>Summer
								</label>
							</div>
						</div>
						<div class='input half'>
							<label>Contact Number</label>
							<div class='field'>
								<?php textfield($contactNumber, 'contactNumber', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div class='input half'>
							<label>Email Address</label>
							<div class='field'>
								<?php textfield($email, 'email', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div style='clear:both;'></div>
						<div class='input half'>
							<label>Preferred Time to be Reached</label>
							<div class='field'>
								<?php textfield($timeToBeReached, 'timeToBeReached', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div style='clear:both;'></div>
						
						<div class='separator'></div>
						<h2>
							Other Details
						</h2>
						<div class='input fourth'>
							<label>Price</label>
							<div class='field'>
								<?php textfield($price, 'price', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div style='clear:both;'></div>
						<div class='input half'>
							<label>Move In Date</label>
							<div class='field'>
								<?php datefield($moveInDate, 'moveInDate', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div class='input half'>
							<label>Move Out Date</label>
							<div class='field'>
								<?php datefield($moveOutDate, 'moveOutDate', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div style='clear:both;'></div>
						<div class='input half'>
							<label>Pets Allowed?</label>
							<div class='field'>
								<?php checkbox($petsAllowed, 'petsAllowed', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div class='input half'>
							<label>Utilities Included?</label>
							<div class='field'>
								<?php checkbox($utilitiesIncluded, 'utilitiesIncluded', $READ_ONLY_MODE); ?>
							</div>
						</div>
						<div style='clear:both;'></div>
						<div class='input half'>
							<label>Apartment Group</label>
							<div class='field'>
								<?php textfield($apartmentGroup, 'apartmentGroup', $READ_ONLY_MODE); ?>
							</div>
						</div>
					</form>
					<div style='clear:both;'></div>
					<div class='separator'></div>
					<h2>
						Photos
					</h2>
					<div class='photosDesc desc'>
						Please upload the four indicated shots of the location to be subleased
					</div>
					<div class='input'>
			<?php
			
					$q->close(); 
					
					
					$query = "SELECT u.id, u.Path, pim.UploadOrder FROM mss_post_image_mappings pim 
						INNER JOIN mss_uploads u ON pim.UploadId = u.id
						WHERE pim.PostingId = '$postingId'
						ORDER BY pim.UploadOrder";
						
					$result = $mysqli->query($query);
					echo $mysqli->error;
					$i = 0;
					$max_photo_uploads = 4;
					$row = mysqli_fetch_array($result);
					while($i < $max_photo_uploads)
					{
						$row_for_this = false;

						if($row && $row["UploadOrder"] == $i) 
						{
							$row_for_this = true;
						}
			?>
						
						<form class='photo-form' action="/wp-content/themes/myspartansublease/management/upload.php" method="post" enctype="multipart/form-data">
							<div class='sublease-image <?php if($row_for_this){ echo "uploaded"; }?> <?php if($i == 0){ echo "first"; }?> '>
									<input type='hidden' name="uploadId" value="<?php echo $row["id"]; ?>" />
									<div class='delete'></div>	
								<div class='image'>
								<?php
									if($row_for_this)
									{
								?>
									<a class='group1 cboxElement' href="<?php echo $row["Path"]; ?>">
										<img src='<?php echo $row["Path"]; ?>'/>
									</a>
								<?php
									}
								?>
								</div>
								<div class='type'>
			<?php // this should be refactored. straight up.
								if($i == 0) echo "Living Room";
								if($i == 1) echo "Bedroom";
								if($i == 2) echo "Closet";
								if($i == 3) echo "Bathroom";
			?>
								</div>
							
								<input name='file' type='file'/>
							</div>
						</form>
			<?php
						$i++;
						if($row_for_this)
							$row = mysqli_fetch_array($result);
					}			
					$result->close();
			?>
						<div style='clear:both;'></div>
					</div>
					<div class='submit'>
							<button class='half state-review Approve admin-only'>Approve Posting</button>
							<button class='half state-review Reject admin-only'>Reject Posting</button>
							<div style='clear:both;'></div>
							<button class='state-live Reject admin-only' >Unlist Posting</button>
							<button class='state-default state-rejected' id='SubmitRequest'>Submit Request</button>
					</div>
				</div> <!-- body -->
			</div> <!-- section -->
			
			<?php

			}else{
				fail_page("No request was found for the given id");
			}	
			
			
		}
		else
		{
			fail_page($mysqli->error);
		}
	}
	else
	{
		fail_page("No id was given.");
	}

}else{
	fail_page("Not logged in.");
}

function success($message)
{
	echo json_encode($message);
	exit();
}
function fail_page($message)
{
	?>
		<div class='failure'>
	<?php echo $message; ?>
		</div>
	<?php
}

?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>