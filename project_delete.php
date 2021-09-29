<!-- Base Code reference to Test Oracle file for UBC CPSC304 2018 Winter Term 1
 -->

<html>
    <body>

        <h2>Delete</h2>
        <form method="GET" action="project_delete.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteTupleRequest" name="deleteTupleRequest">
<hr />

	    Instruction: input a table name and input either a Number OR a Name that you would like to delete. p.s. Postal code goes under Name category. <br />
            Example of an ON CASCADE situation: delete id 21 from coach, and id 21 from coaches table will also be deleted due to on cascade delete.<br />
<hr />
            <h4> Input: </h4>
            Table: <input type="text" name="table"> <br /><br />
            Number: <input type="text" name="delNo"> <br /><br />
            Name: <input type="text" name="delName"> <br /><br />
            <input type="submit" value="Delete" name="deleteTuple"></p>
        </form>
        <hr />
        <form method="POST" action="project.php">
            <p><input type="submit" value="Back to main page" name="reset"></p>
        </form>
        <hr />

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }
			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_alexc330", "a43949767", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

	function handleDeleteRequest() {
	    global $db_conn;
	    $tuple = array ();
	    $alltuples = array ($tuple);
	    //Getting a value from user and delete that row from  the table
	    $delete_from_table = $_GET['table']; 
            $delete_number = $_GET['delNo']; 
	    $delete_name = $_GET['delName']; 

		
	    if ($delete_from_table == "branch1") {
            executeBoundSQL("DELETE FROM branch1 WHERE branchID = '".$delete_number."'", $alltuples);
	    executeBoundSQL("DELETE FROM branch1 WHERE postalCode = '".$delete_name."'", $alltuples);
	    executeBoundSQL("DELETE FROM branch1 WHERE city = '".$delete_name."'", $alltuples);  
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "branch2") {
            executeBoundSQL("DELETE FROM branch2 WHERE postalCode = '".$delete_name."'", $alltuples);   
            executeBoundSQL("DELETE FROM branch2 WHERE province = '".$delete_name."'", $alltuples); 
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "program") {
            executeBoundSQL("DELETE FROM program WHERE name = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";
	    
	    } else if ($delete_from_table == "client") {
            executeBoundSQL("DELETE FROM client WHERE id = '".$delete_number."'", $alltuples);
	    executeBoundSQL("DELETE FROM client WHERE branchID = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM client WHERE pname = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM client WHERE name = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM client WHERE age= '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "lifter") {
            executeBoundSQL("DELETE FROM lifter WHERE cid = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM lifter WHERE timeSpentOnWeights = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM lifter WHERE timeSpentOnTreadmill = '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "runner") {
            executeBoundSQL("DELETE FROM runner WHERE cid = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM runner WHERE timeSpentOnWeights = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM runner WHERE timeSpentOnTreadmill = '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "goal") {
            executeBoundSQL("DELETE FROM goal WHERE cid = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM goal WHERE weight = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM goal WHERE timeline = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "workout") {
            executeBoundSQL("DELETE FROM workout WHERE name = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "consistsof") {
            executeBoundSQL("DELETE FROM consistsof WHERE pname = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM consistsof WHERE wname= '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "exercise") {
            executeBoundSQL("DELETE FROM exercise WHERE name = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "contains") {
            executeBoundSQL("DELETE FROM contains WHERE wname = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM contains WHERE exname = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "performs") {
            executeBoundSQL("DELETE FROM performs WHERE weight = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM performs WHERE reps = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM performs WHERE sets = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM performs WHERE cid = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM performs WHERE exname = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "equipment1") {
            executeBoundSQL("DELETE FROM equipment1 WHERE name = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM equipment1 WHERE price = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM equipment1 WHERE sizes = '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "requires") {
            executeBoundSQL("DELETE FROM requires WHERE eqname = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM requires WHERE exname = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "equipment2") {
            executeBoundSQL("DELETE FROM equipment2 WHERE id = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM equipment2 WHERE branchID = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM equipment2 WHERE name = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "coach") {
            executeBoundSQL("DELETE FROM coach WHERE id = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM coach WHERE name = '".$delete_name."'", $alltuples);
            executeBoundSQL("DELETE FROM coach WHERE age = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM coach WHERE branchID = '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";


	    } else if ($delete_from_table == "coaches") {
            executeBoundSQL("DELETE FROM coaches WHERE coachID = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM coaches WHERE clientID= '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "powerliftingCoach") {
            executeBoundSQL("DELETE FROM powerliftingCoach WHERE id = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM powerliftingCoach WHERE liftingTotal = '".$delete_number."'", $alltuples);
	    echo "Successfully deleted.";

	    } else if ($delete_from_table == "physiotherapist") {
            executeBoundSQL("DELETE FROM physiotherapist WHERE id = '".$delete_number."'", $alltuples);
            executeBoundSQL("DELETE FROM physiotherapist WHERE degree = '".$delete_name."'", $alltuples);
	    echo "Successfully deleted.";
	    } else {
		echo "Please pick a table in order to delete data";
		}

            OCICommit($db_conn);
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('deleteTuple', $_GET)) {
                    handleDeleteRequest();
                }
            }
        }

	if (isset($_GET['deleteTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
