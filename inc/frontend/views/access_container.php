<?php
use MZ_MBO_Access\Inc\Core as Core;

?>
<div id="mzAccessContainer">

<?php 
            
if ( false == $data->logged || false == $data->access ):

	if ( false == $data->access ) {
		// User logged, but not has access
		sprintf(__('<p>Access to this content requires one of: </p>',  'mz-mbo-access'),
            implode(', ', $data->atts['memberships']));
	}
	
	include 'login_form.php'; 
	
else:

	echo $data->content; 

?>

			<div class="row" style="margin:.5em;">

				<div class="col-12">

					<a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=1&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary" id="MBOSite" target="_blank"><?php echo $data->manage_on_mbo; ?></a>

					<a href="#" class="btn btn-primary" id="MBOLogout" target="_blank"><?php echo $data->logout; ?></a>

				</div>
			
			</div>
			
<?php endif; ?>

</div>