<?php 
if(!is_user_logged_in()) 
{
	header( 'Location: ' . get_admin_url() );
}

?>
<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); 

?>
<?php
function my_password_form() {
    global $post;
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
    ' . __( "To view this protected post, enter the password below:" ) . '
    <label for="' . $label . '">' . __( "Password:" ) . ' </label><input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" /><input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" />
    </form>
    ';
    return $o;
}
add_filter( 'the_password_form', 'my_password_form' );
?>
<?php if(!post_password_required() || !is_user_logged_in()) : ?>	
<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/management.css" type="text/css">		
<script type='text/javascript'>
	$(function()
	{
		$("input[name=semesterSpring]").each(function(index, element)
		{
			if($(element).parent().hasClass("active"))
			{
				$(element).prop('checked', true);
			}
		});
		
		$(".request").click(function()
		{
			var $this = $(this);
			$.ajax({
			  url:"/wp-content/themes/myspartansublease/management/manage_subleases.php",
			  type:"POST",
			  data:{ id: $this.children(".id").text() },
			  dataType:"json",
			  success:
					function(data)
					{
						if(data=="")
						{
							alert("Failed.");
							return;
						}
						if(data.success == false)
						{
							alert(data.message);
							return;
						}
					
						$(".default").fadeOut(function()
						{
						
							$(".section.request-to-edit").fadeOut(function()
							{
								var $sectionToEdit = $(".section.request-to-edit");
								var $firstName = $("input[name=firstName]");
								var $lastName = $("input[name=lastName]");
								var $price = $("input[name=price]");
								var $semesterFall = $("input[name=semesterFall]");
								var $semesterSpring = $("input[name=semesterSpring]");
								var $semesterSummer = $("input[name=semesterSummer]");
								var $address = $("input[name=address]");
								var $phone = $("input[name=contactNumber]");
								var $roomsAvailable = $("input[name=roomsAvailable]");
								var $timeToBeReached = $("input[name=timeToBeReached]");
								var $dts = $("span.time-created");

								$firstName.val(data.FirstName);
								$lastName.val(data.LastName);
								$price.val(data.Price);
								var fall = (data.Semesters & 1) == 1;
								var spring = (data.Semesters & 2) == 2;
								var summer = (data.Semesters & 4) == 4;
								$semesterFall.prop('checked', fall);
								$semesterSpring.prop('checked', spring);
								$semesterSummer.prop('checked', summer);
								
								if(fall)
									$semesterFall.parent().addClass("active");
								if(spring)
									$semesterSpring.parent().addClass("active");
								if(summer)
									$semesterSummer.parent().addClass("active");
								$address.val(data.Address);
								$phone.val(data.ContactNumber);
								$roomsAvailable.val(data.RoomsAvailable);
								$timeToBeReached.val(data.TimeToBeReached);
								$dts.text(data.DTSInserted);
								
								$sectionToEdit.fadeIn();
							});
						});
				
					}
				}
			);
		
		});
	});
</script>
<?php endif ?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php the_content(); ?>

			<?php if(!post_password_required()) : ?>
			
			<?php
			
				include '/wp-content/themes/myspartansublease/management/mss_config.php';

				if(mysqli_connect_errno())
				{
					echo "<div class='error'>There was a problem connecting to the database. Please contact a site administrator.</div>";
				}	
				
				$query = "SELECT * FROM mss_requests WHERE Actionable = 1";
				$result = $mysqli->query($query);
			?>
			<div class='admin-head'>
				<span class='internal'>Internal</span> - Create Sublease  <?php do_action('posts_logout_link') ?>
			</div>
			<div class='section'>
				<div class='body dark'>
					<div>
						<?php echo $result->num_rows; ?> actionable requests found.
					</div>
					<div class='separator'></div>
					<div class='table-headings'>
						<div class='id head'>
						Request Id
						</div>
						<div class='name head'>
						Address
						</div>
					</div>
					<div>
					<?php 
						while($row = mysqli_fetch_array($result))
						{
					?>
						<div class='request'>
							<div class='id column'><?php echo $row['id']; ?></div>
							<div class='address column'><?php echo $row['Address']; ?></div>
							
						</div>
					<?php 
						}
					?>
					</div>
				</div>
			</div>
			<div class='section default'>
				<div class='body light '>
				<div class='no-request-selected'>
					Select a request on the left to begin
				</div>
				</div>
			</div>
			<div class='section request-to-edit' data-id='-1' style='display:none;'>
				<div class='body light'>
					
					<div class='dts input'>
						Request created <span class='time-created'>?</span>
					</div>
					<div class='input half'>
						<label>First Name</label>
						<div class='field'>
							<input name='firstName' type='text' value=''/>
						</div>
					</div>
					<div class='input half'>
						<label>Last Name</label>
						<div class='field'>
							<input name='lastName' type='text' value=''/>
						</div>
					</div>					
					<div class='input'>
						<label>Address</label>
						<div class='field'>
							<input name='address' type='text' value=''/>
						</div>
					</div>
					<div style='clear:both;'></div>
					<div class='input'>
						<div><label>Semesters to Lease For</label></div>
						<div class="btn-group semester-selector" data-toggle="buttons">
							<!-- semester selector -->
							<label class="btn btn-primary">
								<input type="checkbox" name='semesterFall'>Fall</input>
							</label>
							<label class="btn btn-primary">
								<input type="checkbox" name='semesterSpring' >Spring</input>
							</label>
							<label class="btn btn-primary">
					
								<input type="checkbox" name='semesterSummer'>Summer
							</label>
						</div>
					</div>
					<div class='input half'>
						<label>Contact Number</label>
						<div class='field'>
							<input name='contactNumber' type='text' value=''/>
						</div>
					</div>
					<div class='input half'>
						<label>Preferred Time to be Reached</label>
						<div class='field'>
							<input name='timeToBeReached' type='text' value=''/>
						</div>
					</div>
					<div style='clear:both;'></div>
					
					<div class='separator'></div>
					
					<div class='input fourth'>
						<label>Price</label>
						<div class='field'>
							<input name='price' type='text' value=''/>
						</div>
					</div>
					<div class='input fourth'>
						<label>Rooms Available</label>
						<div class='field'>
							<input name='roomsAvailable' type='text' value=''/>
						</div>
					</div>
					<div style='clear:both;'></div>
				</div> <!-- body -->
			</div> <!-- section -->
			<div style='clear:both;'></div>
			
			<?php 
				$result->close();
			?>
			
			
			
			<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>