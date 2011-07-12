<?php
	// Include the required glob.php file
	require_once( "../_inc/glob.php" );
	
	// Require the job.inc.php file
	require_once( "../_inc/job.inc.php" );

	// Next, we fetch the form id from the URL (after being escaped)
	$form_id = $core->clean( $_GET['form_id'] );

	// Now a little bit of security, we have to make sure it's a number, or they are up to no good
	if ( !is_numeric( $form_id ) ) {

		// The value is not numeric, and as such will be removed, so we reset the form ID
		$form_id = "";

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

	<head>

		<title>radiPanel: Job Applications</title>

		<style type="text/css" media="screen">

			body {

				background: #ddeef6;
				padding: 0;
				margin: 0;

			}

			body, a, input, select, textarea {

				font-family: Verdana, Tahoma, Arial;
				font-size: 11px;
				color: #333;
				text-decoration: none;

			}

			a:hover {
			
				text-decoration: underline;
			
			}

			form {

				padding: 0;
				margin: 0;

			}

			.wrapper {

				background-color: #fcfcfc;
				width: 350px;
				margin: auto;
				padding: 5px;
				margin-top: 15px;

			}

			.title {

				padding: 5px;	
				margin-bottom: 5px;
				font-size: 14px;
				font-weight: bold;
				background-color: #eee;
				color: #444;

			}

			.content {

				padding: 5px;

			}

			.good, .bad {

				padding: 5px;	
				margin-bottom: 5px;

			}

			.good strong, .bad strong {

				font-size: 12px;
				font-weight: bold;

			}

			.good {

				background-color: #d9ffcf;
				border-color: #ade5a3;
				color: #1b801b;

			}

			.bad {

				background-color: #ffcfcf;
				border-color: #e5a3a3;
				color: #801b1b;

			}

			input, select, textarea {

				border: 1px #e0e0e0 solid;
				border-bottom-width: 2px;
				padding: 3px;

			}

			input {

				width: 170px;

			}

			input.button {

				width: auto;
				cursor: pointer;
				background: #eee;

			}

			select {

				width: 176px;

			}

			textarea {

				width: 288px;

			}

			label {

				display: block;
				padding: 3px;

			}

		</style>

	</head>

	<body>

		<div class="wrapper">

			<div class="title">

				<?php
				// Check if the user has selected a job to apply for
				if ( !$form_id ) {

					// They accessed the file directly, so we are going to display a list of available jobs
					echo "Job Applications";

				}
				else {

					// They selected a job, and now we are going to display the form they need, but first, we find out the name of the job :P
					$jobapps_forminfo_query = $db->query( "SELECT * FROM jobapps_forms WHERE id='{$form_id}'" );

					// And find out how many records were returned
					$jobapps_forminfo_num = $db->num( $jobapps_forminfo_query );

					// Now to check if the form exists
					if ( $jobapps_forminfo_num == "0" ) {
				
						// There is no form with that ID, so we just display the normal title
						echo "Job Applications";

						// And clear the ID
						$form_id = "";
					}
					else {
						// There is a form, so now we create an array of the information
						$jobapps_forminfo = $db->arr( $jobapps_forminfo_query );

						// And now we just return the name of the position
						echo "Apply: {$jobapps_forminfo['position']}";
					}
				}
				?>
			</div>

			<div class="content">

				<?php

					// So first, again, we check if they are calling the page directly or loading a form
					if ( !$form_id ) {

						// So they are just loading the page
						echo "
						<p>So you think you have what it takes to work for us? Want to apply? Simply select a job from the list below and fill in the easy application form!</p>
						<p>Good luck!</p>
						<strong>Available Jobs</strong>
						<br /><br />						
						";

						// So now we load the active positions we have available
						$jobapps_active_query = $db->query( "SELECT * FROM jobapps_forms WHERE active='1'" );
						
						// And now to display them using a while loop						
						while( $jobapps_active = $db->arr( $jobapps_active_query ) ) {

							echo "<a href=\"?form_id={$jobapps_active['id']}\"><strong>{$jobapps_active['position']} &raquo;</strong></a>";
							echo "<br />";
							echo "<em>{$jobapps_active['description']}</em>";
							echo "<br /><br />";

							$i++;
						}
					}
					else {

						// And now to test
						if ( $jobapps_forminfo['active'] != "1" ) {

							// The form_id is not active, so we return an error
							echo "<div class=\"bad\">";
							echo "<strong>Error</strong>";
							echo "<br />";
							echo "The form you have selected has not been activated by the system administrator.";
							echo "</div>";
						}
						else {

							// And now to see if the form has been submitted
							if( $_POST['submit'] ) {

								// We use the submitForm() handler to deal with it
								$submit_frm = $job->submitForm( $form_id );

							}							
							
							// So the form is active, so we can display the form, and here's where we do it
							$job->buildForm( $form_id );					
						}

					}
				?>

			</div>

		</div>

	</body>
</html>
