<?php

class jobApps {

	/*
	 * installScript 	- checks for the required database tables and fields and creates if missing.
	 * @global $db		- $db from /_inc/db.inc.php
	 * @param		- no parameters required. 
	 * @return $q		- the result of the execution.
	 */
	public function installScript() {

		// Declare our global variables
		global $db;

		// Thanks to "CREATE TABLE IF NOT EXISTS" we don't have to do any checking, we can just execute the required queries ;)
		$jobapps_forms = $db->query( "CREATE TABLE IF NOT EXISTS `jobapps_forms` () ENGINE=MyISAM  DEFAULT CHARSET=latin1;" );
		$jobapps_form_elements = $db->query( "CREATE TABLE IF NOT EXISTS `jobapps_form_elements` () ENGINE=MyISAM  DEFAULT CHARSET=latin1;" );
		$jobapps_form_submissions = $db->query( "CREATE TABLE IF NOT EXISTS `jobapps_form_submissions` () ENGINE=MyISAM  DEFAULT CHARSET=latin1;" );
	
	}

	/*
	 * unintallScript 	- removes all tables associated with this addon
	 * @global $db		- $db from /_inc/db.inc.php
	 * @param		- no parameters required. 
	 * @return $q		- the result of the execution.
	 */
	public function uninstallScript() {
		
		// Declare our global variables
		global $db;

		// Thanks to "CREATE TABLE IF NOT EXISTS" we don't have to do any checking, we can just execute the required queries ;)
		$jobapps_forms = $db->query( "DROP TABLE IF EXISTS `jobapps_forms`" );
		$jobapps_form_elements = $db->query( "DROP TABLE IF EXISTS `jobapps_form_elements`" );
		$jobapps_form_submissions = $db->query( "DROP TABLE IF EXISTS `jobapps_form_submissions`" );

	}
	
	/*
	 * createForm	 	- creates a form
	 * @global $db		- $db from /_inc/db.inc.php
	 * @global $core	- $core from /_inc/core.inc.php
	 * @param  $position	- the position name.
	 * @param  $description - the description of the position. 
	 * @return $q		- the result of the execution.
	 */
	public function createForm( $position, $description ) {
	
		// Declare our global variables
		global $db;
		global $core;
		
		// First, we clean the $position and $description variables
		$position = $core->clean( $position );
		$description = $core->clean( $description );
		
		try {
			// All done, so we execute the query (making the form non-active by default)
			$query = $db->query( "INSERT INTO jobapps_forms (id, position, description, active) VALUES (NULL, '{$position}', '{$description}', '0')" );
	
			// Check for success
			if ( !$query ) {
		
				// Query failed to execute, so we throw an exception
				throw new Exception( "Form creation routine failed. Please try again later." );
			}
			else {
			
				// It worked properly!
				echo "<div class=\"good\">";
				echo "<strong>Success</strong>";
				echo "<br />";
				echo "Form successfully created!";
				echo "</div>";
		
			}
		}
		catch( Exception $e ) {
					
			echo "<div class=\"bad\">";
			echo "<strong>Error</strong>";
			echo "<br />";
			echo $e->getMessage();
			echo "</div>";
					
		}
	}

	/*
	 * removeForm	 	- removes a form and all data associated with the form, including fields and submissions
	 * @global $db		- $db from /_inc/db.inc.php
	 * @global $core	- $core from /_inc/core.inc.php
	 * @param  $form_id	- the form_id of the form we want to remove. 
	 * @return $q		- the result of the execution.
	 */
	public function removeForm( $form_id ) {
	
		// Declare our global variables
		global $db;
		global $core;
		
		// Next, we can remove the form from jobapps_forms
		$query = $db->query( "DELETE * FROM jobapps_forms WHERE id = '{$form_id}'" );
		
		// And, we remove all the fields for the form contained in jobapps_form_elements
		$query = $db->query( "DELETE * FROM jobapps_form_elements WHERE form_id = '{$form_id}'" );
		
		// And finally, we remove all submissions for the form (to save loads of space) from jobapps_form_submissions
		$query = $db->query( "DELETE * FROM jobapps_form_submissions WHERE form_id = '{$form_id}'" );
		
		// And deliver a positive response
		return true;

	}

	public function addElementToForm() {

	}

	public function removeElementFromForm() {

	}
	
	/*
	 * buildForm 		- outputs the required HTML to allow form submission and display for a valid form_id.
	 * @global $db		- $db from /_inc/db.inc.php
	 * @global $core	- $core from /_inc/core.inc.php
	 * @param  $form_id	- the form_id of the form we want to display. 
	 * @return $html	- the output HTML.
	 */
	public function buildForm( $form_id ) {

		// Declare our global variables
		global $db;
		global $core;

		// Now we need to execute a database query to select all form elements for the form
		$form_elements_query = $db->query( "SELECT * FROM jobapps_form_elements WHERE form_id='{$form_id}' ORDER BY 'display_position' asc" );

		// And fetch the number of form elements
		$form_elements_num = $db->num( $form_elements_query );

		// And now we use a while loop to build the form from it's elements
		echo "<form action=\"\" method=\"post\">";

		while( $form_elements = $db->assoc( $form_elements_query ) ) {

			// So first, we check what type of field we need
			if ( $form_elements['element_type'] == "text" ) {

				// We need a text element, so first we create a label for the element
				echo "<label for=\"{$form_elements['element_id']}\">{$form_elements['element_label']}:</label>";

				// Then we return the actual field
				echo "<input type=\"text\" name=\"{$form_elements['element_id']}\" id=\"{$form_elements['element_id']}\" maxlength=\"255\" />";

			}

			if ( $form_elements['element_type'] == "select" ) {

				// Now we need to do a bit of trickery, the values of the select dropdown are stored in a CSV string, so first we have to use explode to get the values
				$select_values = explode( ",", $form_elements['element_values'] );

				// Now that's done, we need to return them, but first, the label!
				echo "<label for=\"{$form_elements['element_id']}\">{$form_elements['element_label']}:</label>";

				// We also return some <select> HTML
				echo "<select name=\"{$form_elements['element_id']}\" id=\"{$form_elements['element_id']}\">";

				// And now on to return the values of the select
				foreach( $select_values as $key => $value ) {

					$value_lower = strtolower( $value );

					echo "<option value=\"{$value_lower}\">{$value}</option>";

				}

				// And close the <select> HTML
				echo "</select>";
			}

			if ( $form_elements['element_type'] == "radio" ) {

				// Now we need to do a bit of trickery, the values of the select dropdown are stored in a CSV string, so first we have to use explode to get the values
				$select_values = explode( ",", $form_elements['element_values'] );

				// Now that's done, we need to return them, but first, the label!
				echo "<label for=\"{$form_elements['element_id']}\">{$form_elements['element_label']}:</label>";

				// And now to return the values of the radio buttons
				foreach( $select_values as $key => $value ) {

					$value_lower = strtolower( $value );

					echo "<input type=\"radio\" name=\"{$form_elements['element_id']}\" value=\"{$value_lower}\" />{$value}</input><br />";

				}
			}
			
			if ( $form_elements['element_type'] == "textbox" ) {

				// First, the label!
				echo "<label for=\"{$form_elements['element_id']}\">{$form_elements['element_label']}:</label>";

				// And now to draw up the textbox
				echo "<textarea name=\"{$form_elements['element_id']}\" id=\"{$form_elements['element_id']}\" rows=\"5\"></textarea>";
			}

			// Insert a break
			echo "<br /><br />";

			// Move to the next element
			$i++;
		
		}

		// So now we've finished, we can end the form by adding a submit button, and closing the form HTML
		echo "<input class=\"button\" type=\"submit\" name=\"submit\" value=\"Submit\" style=\"float: right\" />";
		echo "<br /><br />";
		echo "</form>";

	}
	
	/*
	 * submitForm 		- submits the form for a valid form_id.
	 * @global $db		- $db from /_inc/db.inc.php
	 * @global $core	- $core from /_inc/core.inc.php
	 * @param  $form_id	- the form_id of the form we want to display. 
	 * @return $q		- the output HTML.
	 */
	public function submitForm( $form_id ) {

		// Declare our global variables
		global $db;
		global $core;

		try {

			// So now they are submitting the form, but first to clean all the members of the $_POST array
			$_POST = array_map( 'mysql_real_escape_string', $_POST );
			$_POST = array_map( 'htmlspecialchars', $_POST );
			
			// To tidy up the input, we're also going to use trim() on all array members
			$_POST = array_map( 'trim', $_POST );			
			
			// We also get the form_id data into an array
			$jobapps_forminfo_query = $db->query( "SELECT * FROM jobapps_forms WHERE id='{$form_id}'" );
			$jobapps_forminfo = $db->arr( $jobapps_forminfo_query );
			
			// Now we turn the already cleaned data stored in $_POST into a string for storage
			// First we clear the $post_string variable
			$post_string = "";
			
			// And check that $_POST is active and not null
			if ( $_POST ) {
				
				// We have an &submit=Submit key that's set (we don't really need it) so we unset it
  				unset( $_POST['submit'] );
  				
				// Declare a key value array at $kv
				$kv = array();
				
				// Using foreach() we will associate the keys with their values
				foreach ($_POST as $key => $value) {
					
					// But before we do that, we are going to check for empty values
					if ( !$value ) {
					
						// The value of the key is empty, and so we return an error and kill the script
						throw new Exception( "All fields are required." );
					}
					else {
					
						$kv[] = "$key=$value";
					}
  				}
  
  				// And join the string together using the ; character
  				$post_string = join(";", $kv);
			
			}
			
			// Now we have all the data we require, and it's all been cleaned, so we can insert the response string into the database
			$submission = $db->query( "INSERT INTO jobapps_form_submissions (id, form_id, response, status) VALUES (NULL, '{$form_id}', '{$post_string}', 'new')" );
			
			// And now to see if the request handled properly
			if ( !$submission ) {
			
				// There was an error
				throw new Exception( "Job application submission failed. Please try again later." );
			}
			else {
			
				// It worked!

				echo "<div class=\"good\">";
				echo "<strong>Success</strong>";
				echo "<br />";
				echo "Your application for the position of {$jobapps_forminfo['position']} has been successfully sent!";
				echo "</div>";
			}

		}
		catch( Exception $e ) {
					
			echo "<div class=\"bad\">";
			echo "<strong>Error</strong>";
			echo "<br />";
			echo $e->getMessage();
			echo "</div>";
					
		}

	}
}

$job = new jobApps();
?>
