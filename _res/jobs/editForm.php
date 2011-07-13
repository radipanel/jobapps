<?php

	if( !preg_match( "/index.php/i", $_SERVER['PHP_SELF'] ) ) { die(); }

?>
<div class="box">

	<div class="square title">
		<strong>Manage form fields</strong>
	</div>

	<?php

		if( isset($_GET['moveUp']) or isset($_GET['moveDown']) ) {
			
			$id = $core->clean( $_GET['id'] );
			
			$query = $db->query( "SELECT * FROM jobapps_form_elements WHERE id = '{$id}'");
			$array = $db->assoc( $query );
			
			if( isset($_GET['moveUp']) ) {
			
				$weight = $array['display_position'] - 1;
			
			}
			else {
				
				$weight = $array['display_position'] + 1;
				
			}
			
			$db->query( "UPDATE jobapps_form_elements SET display_position = '{$weight}' WHERE id = '{$id}'");
			
		}

		$query = $db->query( "SELECT * FROM jobapps_forms ORDER BY id ASC" );

		$j = "a";

		while( $array = $db->assoc( $query ) ) {

			$query2 = $db->query( "SELECT * FROM jobapps_form_elements WHERE form_id = '{$array['id']}' ORDER BY display_position ASC" );
			
			$query3 = $db->query( "SELECT * FROM jobapps_form_elements WHERE form_id = '{$array['id']}' ORDER BY display_position DESC" );
			$array3 = $db->assoc( $query3 );
			
			echo "<div class=\"row\" style=\"background: #e6e6e6;\">";
			echo "<strong>{$array['position']}";
			echo "<span style=\"float: right; width: 32px; text-align: right;\">";
			echo "<a href=\"job.addFormElement?form_id={$array['id']}\">";
			echo "<img src=\"_img/pencil.png\" alt=\"Edit\" />";
			echo "</a>";
			echo "</strong>";
			echo "</span>";
			echo "</div>";
			
			while( $array2 = $db->assoc( $query2 ) ) {
	
				echo "<div class=\"row {$j}\" id=\"menu_{$array2['id']}\">";

				echo "<div style=\"float: right; width: 32px; text-align: right;\">";

				if( $array2['display_position'] < $array3['display_position'] ) {

					echo "<a href=\"?moveDown&id={$array2['id']}\">";
					echo "<img src=\"_img/down.png\" alt=\"Down\" />";
					echo "</a>";
					
				}
				
				if( $array2['display_position'] > 1 ) {
				
					echo "<a href=\"?moveUp&id={$array2['id']}\">";
					echo "<img src=\"_img/up.png\" alt=\"Up\" />";
					echo "</a>";
					
				}
				
				echo "</div>";
				echo "<div style=\"float: right; width: 32px; text-align: right;\">";

				echo "<a href=\"job.addFormElement?id={$array2['id']}\">";
				echo "<img src=\"_img/pencil.png\" alt=\"Edit\" />";
				echo "</a>";

				echo "<a href=\"#\" onclick=\"Radi.deleteFormElement('{$array2['id']}');\">";
				echo "<img src=\"_img/minus.png\" alt=\"Delete\" />";
				echo "</a>";

				echo "</div>";

				echo $array2['element_label'];
	
				echo "</div>";
	
				$j++;
	
				if( $j == "c" ) {
	
					$j = "a";
	
				}
	
			}

		}

	?>

</div>
