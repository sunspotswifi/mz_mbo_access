<?php
use MZ_MBO_Access\Inc\Core as Core;
use MZ_Mindbody as MZ;
?>
<div id="mzAccessContainer">

<?php 
if ( false == $data->logged_in ):
	
	include 'login_form.php'; 
	
else:
	?>
	<p class="mbo-user">Hi, <?php echo $data->client_name; ?>.</p>
	<?php
	if ((!empty($data->atts['member_redirect']) || !empty($data->atts['member_redirect']) )) {
		// this is being used as a redirect login form so just echo content if it exists
		echo $data->content; 
	} else {
	
		if ( false == $data->access ) { ?>
			<div class="alert alert-warning">
				<?php echo '<strong>' . $data->denied_message .  '</strong>:'; ?>
				<ul>
					<?php foreach ($data->membership_types as $membership_type){
						echo '<li>' . $membership_type . '</li>';
					}
					foreach ($data->purchase_types as $purchase_type){
						echo '<li>' . $purchase_type . '</li>';
					}
					foreach ($data->contract_types as $contract_type){
						echo '<li>' . $contract_type . '</li>';
					}
					?>
				</ul>
			</div>
		<?php
		} else {
			echo $data->content; 
		}
	?>

			<div class="row" style="margin:.5em;">

				<div class="col-12">

					<a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=1&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary" id="MBOSite" target="_blank"><?php echo $data->manage_on_mbo; ?></a>

					<a href="#" class="btn btn-primary" id="MBOLogout" target="_blank"><?php echo $data->logout; ?></a>

				</div>
			
			</div>
	<?php } // End not a redirect login form ?>
<?php endif; ?>

</div>
<div style="display:none">
<?php include 'login_form.php'; // for use in logout routine ?>
</div>