<?php

	if( !preg_match( "/index.php/i", $_SERVER['PHP_SELF'] ) ) { die(); }

?>
<div class="box">

	<div class="square title">
		<strong>Manage forms</strong>
	</div>

		<?php

		$query = $db->query( "SELECT * FROM jobapps_forms" );
		$num   = $db->num( $query );

		$j = "a";

                if ( $db->num( $query ) == "0" ) {

                    echo "<div class=\"square bad\" style=\"margin-bottom: 0px;\">";
                    echo "<strong>Sorry</strong>";
                    echo "<br />";
                    echo "No job application forms have been created.";
                    echo "</div>";

                }
                
		while( $array = $db->assoc( $query ) ) {

			echo "<div class=\"row {$j}\" id=\"job_{$array['id']}\">";

			echo "<a href=\"#\" onclick=\"Radi.deleteJobForm('{$array['id']}');\">";
			echo "<img src=\"_img/minus.png\" alt=\"Delete\" align=\"right\" />";
			echo "</a>";

			echo "<a href=\"job.addForm?id={$array['id']}\">";
			echo "<img src=\"_img/pencil.png\" alt=\"Edit\" align=\"right\" />";
			echo "</a>";

			echo $array['position'];

			echo "</div>";

			$j++;

			if( $j == "c" ) {

				$j = "a";

			}

		}

	?>

</div>
