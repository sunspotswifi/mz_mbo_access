<?php
use MZ_MBO_Access\Inc\Core as Core;
?>
	<div id="mzLogInContainer">
	
		<form role="form" class="form-group" style="margin:1em 0;" data-async id="mzLogIn" data-target="#mzSignUpModal" method="POST">

			<h3><?php echo $data->login_to_sign_up; ?></h3>

			<input type="hidden" name="nonce" value="<?php echo $data->signup_nonce; ?>"/>

			<input type="hidden" name="siteID" value="<?php echo $data->siteID; ?>" />

			<div class="row">

				<div class="form-group col-xs-8 col-sm-6">

					<label for="username">Email</label>

					<input type="email" size="10" class="form-control" id="email" name="email" placeholder="<?php echo $data->email ?>" required>

				</div>

			</div>

			<div class="row">

				<div class="form-group col-xs-8 col-sm-6">

					<label for="password">Password</label>

					<input type="password" size="10" class="form-control" name="password" id="password" placeholder="<?php echo $data->password ?>" required>

				</div>

			</div>

			<div class="row" style="margin:.5em;">

				<div class="col-12">

					<button type="submit" class="btn btn-primary"><?php echo $data->login; ?></button>

					<a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=1&studioid=<?php echo $data->siteID; ?>" class="btn btn-primary" id="MBOSite" target="_blank"><?php echo $data->manage_on_mbo; ?></a>

				</div>
			
			</div>

		</form>

	</div>