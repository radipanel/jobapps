<?php

    if( !preg_match( "/index.php/i", $_SERVER['PHP_SELF'] ) ) { die(); }

    if( $_GET['id'] ) {

        $id = $core->clean( $_GET['id'] );

        $query = $db->query( "SELECT * FROM jobapps_forms WHERE id = '{$id}'" );
        $data  = $db->assoc( $query );

        $editid = $data['id'];

    }

?>
<form action="" method="post" id="addApplication">

    <div class="box">

        <div class="square title">
            <strong>Create job application</strong>
        </div>

        <?php

            if( $_POST['submit'] ) {

                try {

                    $job_pos = $core->clean( $_POST['job_pos'] );
                    $job_des = $core->clean( $_POST['job_des'] );
                    $job_act = $core->clean( $_POST['job_act'] );

                    $query = $db->query( "SELECT * FROM jobapps_forms" );

                    if( !$job_pos or !$job_des or !$job_act ) {

                        throw new Exception( "All fields are required." );

                    }
                    else {

                        if( $editid ) {

                            $db->query( "UPDATE jobapps_forms SET position = '{$job_pos}', description = '{$job_des}', active = '{$job_act}' WHERE id = '{$editid}'" );

                            echo "<div class=\"square good\">";
                            echo "<strong>Success</strong>";
                            echo "<br />";
                            echo "Application form updated!";
                            echo "</div>";

                        }
                        else {

                            $db->query( "INSERT INTO jobapps_forms VALUES (NULL, '{$job_pos}', '{$job_des}', '{$job_act}');" );

                            echo "<div class=\"square good\">";
                            echo "<strong>Success</strong>";
                            echo "<br />";
                            echo "Application form created!";
                            echo "</div>";

                        }

                    }

                }
                catch( Exception $e ) {

                    echo "<div class=\"square bad\">";
                    echo "<strong>Error</strong>";
                    echo "<br />";
                    echo $e->getMessage();
                    echo "</div>";

                }

            }

        ?>

        <table width="100%" cellpadding="3" cellspacing="0">
            <?php
            
                echo $core->buildField( "text",
                                        "required",
                                        "job_pos",
                                        "Position",
                                        "The job position",
                                        $data['position'] );

                echo $core->buildField( "text",
                                        "required",
                                        "job_des",
                                        "Description",
                                        "The job description",
                                        $data['description'] );

                echo $core->buildField( "text",
                                        "",
                                        "job_act",
                                        "Active",
                                        "Set to 0 to disable.",
                                        $data['active'] );
            ?>
        </table>

    </div>

    <div class="box" align="right">

        <input class="button" type="submit" name="submit" value="Submit" />

    </div>

</form>

<?php
    echo $core->buildFormJS('addApplication');

?>
