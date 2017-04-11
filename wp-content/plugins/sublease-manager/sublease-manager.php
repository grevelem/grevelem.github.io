<?php
/**
 * Plugin Name: Sublease Manager
 * Plugin URI: http://www.myspartansublease.com
 * Description: Developed specifically for My Spartan Sublease to manage sublease postings on the site.
 * Version: 1.0
 * Author: Xavier Durand-Hollis Jr.
 * Author URI: http://www.xavdev.com
 * License: 
 */
include 'constants.php';

add_action('admin_init', 'sublease_admin_deps');
add_action('admin_menu', 'sublease_admin_menu');

 function sublease_admin_deps() 
 {
    wp_register_script( 'add-bootstrap-js', plugins_url('/bootstrap.min.js',__FILE__), array('jquery'),'',true  );
    wp_register_script( 'add-bootstrap-colorpicker-js', plugins_url('/bootstrap-colorpicker.js',__FILE__), array('jquery'),'',true  );
    wp_register_script( 'add-mss-subleasemanager-js', plugins_url('/mss.subleasemanager.js',__FILE__), array('jquery'),'',true  );
    wp_register_style( 'add-mss-subleasemanager-css', plugins_url('/mss.subleasemanager.css',__FILE__),'','', 'screen' );
	wp_register_style( 'add-bootstrap-css', plugins_url('/bootstrap.css',__FILE__),'','', 'screen' );
	wp_register_style( 'add-bootstrap-colorpicker-css', plugins_url('/css/bootstrap-colorpicker.css',__FILE__),'','', 'screen' );
}
 

/** Step 1. */
function sublease_admin_menu() 
{
	$page_hook_suffix = add_submenu_page( 'options-general.php', // The parent page of this submenu
									  __( 'Sublease Manager', 'sublease-manager' ), // The submenu title
									  __( 'Sublease Manager', 'sublease-manager' ), // The screen title
					  'manage_options', // The capability required for access to this submenu
					  'sublease-manager', // The slug to use in the URL of the screen
									  'sublease_manager' // The function to call to display the screen
								   );
	add_action('admin_print_scripts-' . $page_hook_suffix, 'sublease_scripts');
}

function sublease_scripts()
{
	wp_enqueue_style( 'add-bootstrap-css' );
	wp_enqueue_style( 'add-bootstrap-colorpicker-css' );
	wp_enqueue_style( 'add-mss-subleasemanager-css' );
	wp_enqueue_script( 'add-bootstrap-js' );
	wp_enqueue_script( 'add-bootstrap-colorpicker-js' );
	wp_enqueue_script( 'add-mss-subleasemanager-js' );
}

/** Step 3. */
function sublease_manager()
{
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	include 'mss_config.php';

	if(mysqli_connect_errno())
	{
		echo "<div class='error'>There was a problem connecting to the database. Please contact a site administrator.</div>";
	}	
	
?>
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
<div class="modal fade email-form">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Email links for selected</h4>
	  </div>
	  <div class="modal-body">
		<div class='input half'>
			<div class='name'>
				Email selected requests to
			</div>
			<div class='field'>
				<input id='SendLinksTo' type='text'/>
			</div>
		</div>
		<div class='input half'>
			<div class='name'>
				Mark requests with color
			</div>
			<div class='field input-group colorpicker'>
				<input type="text" id="EmailColor" style='display:none'; value="" class="form-control" />
				<span class="input-group-addon"><i></i></span>
			</div>
			<div><span class='required' style='font-size:0.8em'>* Note: this is local to your browser.</span></div>
		</div>
		<div style='clear:both;'></div>
	  </div>
	  <div class="modal-footer">
		<button id="SendEmail" type="button" class="btn btn-primary full">Send Links</button>
		<button type="button" class="btn btn-default full" data-dismiss="modal">Cancel</button>
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class='manager head'>
	<h2>Sublease Manager</h2>
	<div>
		Choose a tab to view different postings / requests. Perform actions on them using the options menu on the left.
	</div>
	<div class='manager-tab-list'>
		<div class='pad'></div>
		<div class='tab-list-item selected' data-tab="#Actionable" data-canemail="true"  data-candeleterequest="true">
			Requests
		</div>
		<div class='tab-list-item' data-tab="#AwaitingReview" data-canlive="true" data-canreject="true">
			Pending Review
		</div>
		
		<div class='tab-list-item' data-tab="#Live" data-canreject="true">
			Live
		</div>
		
		<div class='tab-list-item' data-tab="#Rejected" data-canreview="true" data-canemail="true" data-candelete="true">
			Rejected / Unlisted
		</div>
		<div style='clear:both;'></div>
	</div>
</div>
<div class='wrap'>
	<div class='manager'>
		<div class='options'>
			<h4>
				Options
			</h4>
			<button class='btn btn-primary' id="OptionEmail">
				Email links for selected
			</button>
			<button class='btn btn-primary' id="OptionLive">
				Approve selected
			</button>
			<button class='btn btn-primary' id="OptionReview">
				Send selected to be Reviewed
			</button>
			<button class='btn btn-primary' id="OptionReject">
				Reject / unlist selected
			</button>
			<button class='btn btn-primary first' id="OptionDelete">
				Delete selected
			</button>
			<button class='btn btn-primary first' id="OptionDeleteRequest">
				Delete selected requests
			</button>
			<button class='btn btn-primary first' id="OptionUnmarkSelected">
				Unmark selected
			</button>
			<div class='num-selected'></div>
		</div>
		
		<div class='tabs'>
			<div id="Actionable" class='manager-tab current'>
				<div class='head'>
					<h3 class='title'>
						User Submitted Requests
					</h3>
				</div>
		<?php 
			$query = "SELECT p.id as PostingId, r.id as id, r.Address, r.FirstName, 
			r.LastName, r.DTSInserted
			FROM mss_requests r
			LEFT JOIN mss_postings p
				ON r.id = p.RequestId
			WHERE r.Actionable = 1 AND p.id IS NULL";
			emit_posting_category($mysqli, $query, "There are no actionable, user submitted requests awaiting review.");
		?>
			</div>
			<div id="AwaitingReview" class='manager-tab'>
				<div class='head'>
					<h3 class='title'>
						Postings Awaiting Review
					</h3>
				</div>
		<?php 
			$query = "SELECT p.id as PostingId, r.id as id, r.Address, r.FirstName, 
			r.LastName, r.DTSInserted FROM mss_postings p INNER JOIN mss_requests r ON p.RequestId = r.id 
			WHERE p.State = 2";
			emit_posting_category($mysqli, $query, "No postings are awaiting administrator review.");
		?>
			</div>
			<div id="Live" class='manager-tab'>
				<div class='head'>
					<h3 class='title'>
						Live Postings
					</h3>
				</div>
		<?php 
			$query = "SELECT p.id as PostingId, r.id as id, r.Address, r.FirstName, 
			r.LastName, r.DTSInserted FROM mss_postings p INNER JOIN mss_requests r ON p.RequestId = r.id 
			WHERE p.State = 0";
			emit_posting_category($mysqli, $query, "No postings have been made live.");
		?>
			</div>
			<div id="Rejected" class='manager-tab'>
				<div class='head'>
					<h3 class='title'>
						Rejected / Unlisted Postings
					</h3>
				</div>
		<?php 
			$query = "SELECT  p.id as PostingId, r.id as id, r.Address, r.FirstName, 
			r.LastName, r.DTSInserted  FROM mss_postings AS p INNER JOIN mss_requests AS r ON p.RequestId = r.id WHERE p.State = 3";
			emit_posting_category($mysqli, $query, "No postings have been rejected.");
		?>
			</div>
			<div style='clear:both;'></div>
		</div> <!-- tabs -->
	</div> <!-- manager -->
</div> <!-- wrap -->
<?php
	$mysqli->close();
}

function emit_posting_category($mysqli, $query, $unavailable_text)
{
	$result = $mysqli->query($query);
?>
	<div class='none' style='<?php if($result->num_rows > 0){ echo "display:none;"; } ?>'>
		<div class='no-postings'><?php echo $unavailable_text; ?></div>
	</div>
<?php
	
	while($row = mysqli_fetch_array($result))
	{
		emit_posting($row);
	}
	
	$result->close();
?>
<?php
}

function emit_posting($row)
{
	$id = $row['id'];
?>
		<div class='request'>
			<div class='id column'>
			<?php echo $row['id']; ?>
			<input type='hidden' name='postingId' value='<?php echo $row['PostingId']; ?>'/>
			</div>
			<div class='tag column'>
				
			</div>
			<div class='email column'>
				
			</div>
			<div class='info column'>
				<div class='request-info'>
					<b>Address</b><h4><?php echo $row['Address']; ?></h4>
				</div>
				<div class='request-info'>
					<b>Name</b><h5><?php echo $row['FirstName'] . " " . $row['LastName']; ?></h5>
				</div>
				<div class='request-info'>
					<b>Submitted</b><h5><?php echo $row['DTSInserted']; ?></h5>
				</div>
				<div class='request-info'>
					<b>Link</b>
					<h5><a href="<?php echo sublease_link($id); ?>" target="_blank">Go to Sublease Page</a></h5>
				</div>
				<div style="clear:both;"></div>
			</div>
		
		</div>
<?php 
}
?>