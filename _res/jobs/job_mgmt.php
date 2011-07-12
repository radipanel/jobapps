<?php

	if( !preg_match( "/index.php/i", $_SERVER['PHP_SELF'] ) ) { die(); }
	
	// We are also going to get the value of $_GET['process'] & $_GET['action']
	$process = $core->clean( $_GET['process'] );
	$action = $core->clean( $_GET['action'] );
	
	// And include our job.inc.php (as it is not stored in glob.php)
	require_once( '../../_inc/job.inc.php' );
	
?>
<form action="" method="post" id="addInfraction">

	<div class="box">

		<?php
		
			// So first, we start determining what they want to do
			switch ( $process ) {
			
				case addform:
					
					// They want to add a form, so we set the phase correctly
					$frm_title = "Create A Form";
					break;
			}
		
		?>
		<div class="square title">
			<strong><?php echo $frm_title; ?></strong>
		</div>


	</div>

</form>
