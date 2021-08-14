<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>

        <h2>Reset</h2>
        <p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

        <form method="POST" action="project.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>

        <hr />

        <h2>Generate tables</h2>
        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="generateRequest" name="generateRequest">
            <input type="submit" name="reset"></p>
        </form>

        <hr />

        <h2>Display tables</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
            <input type="submit" name="displayTuples"></p>
        </form>

        <!--<h2>Populate tables</h2>-->
        <!--<form method="POST" action="project.php">--> <!--refresh page when submitted-->
            <!--<input type="hidden" id="populateRequest" name="populateRequest">-->
            <!--<input type="submit" name="reset"></p>-->
       <!-- </form>-->

        <hr />

        <h2>What would you like to do?</h2>
        <form method="POST" action="project_insert.php">
            <p><input type="submit" value="Insert data" name="reset"></p>
        </form>
        <form method="POST" action="project_delete.php">
            <p><input type="submit" value="Delete data" name="reset"></p>
        </form>
        <form method="POST" action="project_update.php">
            <p><input type="submit" value="Update data" name="reset"></p>
        </form>
        <form method="POST" action="project_select.php">
            <p><input type="submit" value="Query with selection" name="reset"></p>
        </form>
        <form method="POST" action="project_project.php">
            <p><input type="submit" value="Query with projection" name="reset"></p>
        </form>
        <form method="POST" action="project_join.php">
            <p><input type="submit" value="Query with join" name="reset"></p>
        </form>
        <form method="POST" action="project_division.php">
            <p><input type="submit" value="Query with division" name="reset"></p>
        </form>
        <form method="POST" action="project_group.php">
            <p><input type="submit" value="Aggregation with GROUP BY" name="reset"></p>
        </form>
        <form method="POST" action="project_having.php">
            <p><input type="submit" value="Aggregation with HAVING" name="reset"></p>
        </form>
        <form method="POST" action="project_nested.php">
            <p><input type="submit" value="Nested aggregation with GROUP BY" name="reset"></p>
        </form>
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
        function printResult($result, $name) { //prints results from a select statement
            echo "<br>Retrieved data from table $name:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";
        }
        function connectToDB() {
            global $db_conn;
            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_yuxuanhe", "a37063154", "dbhost.students.cs.ubc.ca:1522/stu");
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
        function handleUpdateRequest() {
            global $db_conn;
            $old_name = $_POST['oldName'];
            $new_name = $_POST['newName'];
            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
            OCICommit($db_conn);
        }
        function handleResetRequest() {
            global $db_conn;
            // Drop old table

            executePlainSQL("DROP TABLE branch1 cascade constraints");
            executePlainSQL("DROP TABLE branch2 cascade constraints");
            executePlainSQL("DROP TABLE client cascade constraints");
            executePlainSQL("DROP TABLE lifter cascade constraints");
            executePlainSQL("DROP TABLE runner cascade constraints");
            executePlainSQL("DROP TABLE goal cascade constraints");
            executePlainSQL("DROP TABLE program cascade constraints");
            executePlainSQL("DROP TABLE consistsof cascade constraints");
            executePlainSQL("DROP TABLE workout cascade constraints");
            executePlainSQL("DROP TABLE contains cascade constraints");
            executePlainSQL("DROP TABLE performs cascade constraints");
            executePlainSQL("DROP TABLE exercise cascade constraints");
            executePlainSQL("DROP TABLE requires cascade constraints");
            executePlainSQL("DROP TABLE equipment1 cascade constraints");
            executePlainSQL("DROP TABLE equipment2 cascade constraints");
            executePlainSQL("DROP TABLE coach cascade constraints");
            executePlainSQL("DROP TABLE coaches cascade constraints");
            executePlainSQL("DROP TABLE powerliftingCoach cascade constraints");
            executePlainSQL("DROP TABLE physiotherapist cascade constraints");

            // Create new table
            echo "<br> Deleting tables <br>";

            OCICommit($db_conn);
        }


        function createData() {
            global $db_conn;
            echo "<br> creating tables <br>";
            executePlainSQL("CREATE TABLE branch2 (postalCode char(10) PRIMARY KEY, province char(30))");
            executePlainSQL("CREATE TABLE branch1 (branchID int PRIMARY KEY, city char(20) NOT NULL, postalCode char(10) NOT NULL, FOREIGN KEY(postalCode) REFERENCES branch2)");
            executePlainSQL("CREATE TABLE program (name char(50) PRIMARY KEY)");
            executePlainSQL("CREATE TABLE client (id int PRIMARY KEY, branchID int NOT NULL, pname char(50), name char(20) NOT NULL, age int, FOREIGN KEY(branchID) REFERENCES branch1 ON DELETE CASCADE, FOREIGN KEY (pname) REFERENCES Program)");
            executePlainSQL("CREATE TABLE lifter (cid int PRIMARY KEY, timeSpentOnWeights int, timeSpentOnTreadmill int, FOREIGN KEY (cid) REFERENCES client ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE runner (cid int PRIMARY KEY, timeSpentOnWeights int, timeSpentOnTreadmill int, FOREIGN KEY (cid) REFERENCES client ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE goal (cid int, weight int, timeline char(50) NOT NULL, PRIMARY KEY(cid, weight), FOREIGN KEY(cid) REFERENCES client ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE workout (name char(50) PRIMARY KEY)");
            executePlainSQL("CREATE TABLE consistsof (pname char(50), wname char(50), PRIMARY KEY (pname, wname), FOREIGN KEY (pname) REFERENCES program, FOREIGN KEY (wname) REFERENCES workout)");
            executePlainSQL("CREATE TABLE exercise (name char(20) PRIMARY KEY)");
            executePlainSQL("CREATE TABLE contains (wname char(50), exname char(20), PRIMARY KEY (wname, exname), FOREIGN KEY (wname) REFERENCES workout, FOREIGN KEY (exname) REFERENCES exercise)");
            executePlainSQL("CREATE TABLE performs (weight int, reps int, sets int, cid int, exname char(20), PRIMARY KEY (cid, exname), FOREIGN KEY (cid) REFERENCES client)");
            executePlainSQL("CREATE TABLE equipment1 (name char(50) PRIMARY KEY, price int, sizes int)");
            executePlainSQL("CREATE TABLE requires (eqname char(50), exname char(20), PRIMARY KEY (eqname, exname), FOREIGN KEY (eqname) REFERENCES equipment1, FOREIGN KEY (exname) REFERENCES exercise)");

            executePlainSQL("CREATE TABLE equipment2 (id int PRIMARY KEY, branchID int DEFAULT 1, name char(50) NOT NULL, FOREIGN KEY (branchID) REFERENCES branch1, FOREIGN KEY (name) REFERENCES equipment1 ON DELETE CASCADE)"); # branch1 should be on delete set default
            executePlainSQL("CREATE TABLE coach (id int PRIMARY KEY, branchID int NOT NULL, name char(30) NOT NULL, age int, FOREIGN KEY (branchID) REFERENCES branch1 ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE coaches (coachID int, clientID int, PRIMARY KEY (coachID, clientID), FOREIGN KEY (coachID) REFERENCES coach ON DELETE CASCADE, FOREIGN KEY (clientID) REFERENCES client ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE powerliftingCoach (id int PRIMARY KEY, liftingTotal int, FOREIGN KEY (id) REFERENCES coach ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE physiotherapist (id int PRIMARY KEY, degree char(50), FOREIGN KEY (id) REFERENCES coach ON DELETE CASCADE)");

            OCICommit($db_conn);

        }

        function populateData() {
            global $db_conn;

            executePlainSQL("insert into branch2 values ('V3J 2Y6', 'British Columbia')");
            executePlainSQL("insert into branch2 values ('V1N 6L2', 'British Columbia')");
            executePlainSQL("insert into branch2 values ('B2J 7K3', 'Ontario')");
            executePlainSQL("insert into branch2 values ('J8L 0H2', 'Alberta')");
            executePlainSQL("insert into branch2 values ('V3B 1R3', 'British Columbia')");

            executePlainSQL("insert into branch1 values (1, 'Vancouver', 'V3J 2Y6')");
            executePlainSQL("insert into branch1 values (2, 'Vancouver', 'V3J 2Y6')");
            executePlainSQL("insert into branch1 values (3, 'Vancouver', 'V1N 6L2')");
            executePlainSQL("insert into branch1 values (4, 'Toronto', 'B2J 7K3')");
            executePlainSQL("insert into branch1 values (5, 'Edmonton', 'J8L 0H2')");
            executePlainSQL("insert into branch1 values (6, 'Coquitlam', 'V3B 1R3')");

            executePlainSQL("insert into program values('PPL')");
            executePlainSQL("insert into program values('Stronglifts')");
            executePlainSQL("insert into program values('Nsuns')");
            executePlainSQL("insert into program values('Upper Lower Split')");
            executePlainSQL("insert into program values('5 Day Split')");

            executePlainSQL("insert into client values(100, 1, 'PPL', 'John Lee', 19)");
            executePlainSQL("insert into client values(101, 1, 'Stronglifts', 'Robert Nun', 30)");
            executePlainSQL("insert into client values(102, 2, 'Stronglifts', 'Kim Rico', 22)");
            executePlainSQL("insert into client values(103, 3, 'Nsuns', 'Julie Wong', 43)");
            executePlainSQL("insert into client values(104, 4, 'Upper Lower Split', 'Andy Wire', 28)");
            executePlainSQL("insert into client values(105, 5, 'Upper Lower Split', 'Maria Paul', 33)");
            executePlainSQL("insert into client values(106, 6, '5 Day Split', 'Jamie Cal', 33)");
            executePlainSQL("insert into client values(107, 3, '5 Day Split', 'Jason Cal', 33)");

            executePlainSQL("insert into lifter values(100, 50, 10)");
            executePlainSQL("insert into lifter values(101, 60, 0)");
            executePlainSQL("insert into lifter values(102, 30, 30)");
            executePlainSQL("insert into lifter values(103, 40, 50)");
            executePlainSQL("insert into lifter values(104, 30, 30)");

            executePlainSQL("insert into runner values(102, 30, 30)");
            executePlainSQL("insert into runner values(103, 40, 50)");
            executePlainSQL("insert into runner values(104, 30, 30)");
            executePlainSQL("insert into runner values(105, 0, 40)");
            executePlainSQL("insert into runner values(106, 15, 50)");

            executePlainSQL("insert into goal values(100, 110, '6 months')");
            executePlainSQL("insert into goal values(102, 150, '1 year')");
            executePlainSQL("insert into goal values(103, 140, '2 months')");
            executePlainSQL("insert into goal values(103, 130, '6 months')");
            executePlainSQL("insert into goal values(104, 135, '3 months')");

            executePlainSQL("insert into workout values('Squat day')");
            executePlainSQL("insert into workout values('Chest day')");
            executePlainSQL("insert into workout values('Upper body day')");
            executePlainSQL("insert into workout values('Pull day')");
            executePlainSQL("insert into workout values('Push day')");

            executePlainSQL("insert into consistsof values('Nsuns', 'Squat day')");
            executePlainSQL("insert into consistsof values('Stronglifts', 'Chest day')");
            executePlainSQL("insert into consistsof values('5 Day Split', 'Upper body day')");
            executePlainSQL("insert into consistsof values('Upper Lower Split', 'Pull day')");
            executePlainSQL("insert into consistsof values('PPL', 'Push day')");

            executePlainSQL("insert into exercise values('Bicep curl')");
            executePlainSQL("insert into exercise values('Leg curl')");
            executePlainSQL("insert into exercise values('Leg press')");
            executePlainSQL("insert into exercise values('Tricep pushdown')");
            executePlainSQL("insert into exercise values('Shoulder press')");

            executePlainSQL("insert into contains values('Upper body day', 'Bicep curl')");
            executePlainSQL("insert into contains values('Squat day', 'Leg curl')");
            executePlainSQL("insert into contains values('Squat day', 'Leg press')");
            executePlainSQL("insert into contains values('Chest day', 'Tricep pushdown')");
            executePlainSQL("insert into contains values('Push day', 'Shoulder press')");

            executePlainSQL("insert into performs values(30, 10, 3, 100, 'Bicep curl')");
            executePlainSQL("insert into performs values(110, 5, 2, 101, 'Leg curl')");
            executePlainSQL("insert into performs values(180, 5, 2, 102, 'Leg press')");
            executePlainSQL("insert into performs values(50, 5, 3, 103, 'Tricep pushdown')");
            executePlainSQL("insert into performs values(95, 10, 3, 104, 'Shoulder press')");

            executePlainSQL("insert into equipment1 values('Leg Press', 6200, 8)");
            executePlainSQL("insert into equipment1 values('Dumbbell', 100, 0)");
            executePlainSQL("insert into equipment1 values('Barbell', 300, 0)");
            executePlainSQL("insert into equipment1 values('Shoulder Press Machine', 3499, 7)");
            executePlainSQL("insert into equipment1 values('Leg Extension Machine', 3699, 7)");

            executePlainSQL("insert into equipment2 values(102, 1, 'Leg Press')");
            executePlainSQL("insert into equipment2 values(121, 1, 'Leg Press')");
            executePlainSQL("insert into equipment2 values(107, 2, 'Barbell')");
            executePlainSQL("insert into equipment2 values(106, 2, 'Dumbbell')");
            executePlainSQL("insert into equipment2 values(133, 2, 'Leg Press')");
            executePlainSQL("insert into equipment2 values(108, 3, 'Dumbbell')");
            executePlainSQL("insert into equipment2 values(119, 3, 'Barbell')");
            executePlainSQL("insert into equipment2 values(122, 3, 'Leg Press')");
            executePlainSQL("insert into equipment2 values(178, 3, 'Shoulder Press Machine')");
            executePlainSQL("insert into equipment2 values(179, 3, 'Leg Extension Machine')");

            executePlainSQL("insert into requires values('Dumbbell', 'Bicep curl')");
            executePlainSQL("insert into requires values('Leg Extension Machine', 'Leg curl')");
            executePlainSQL("insert into requires values('Leg Press', 'Leg press')");
            executePlainSQL("insert into requires values('Barbell', 'Tricep pushdown')");
            executePlainSQL("insert into requires values('Shoulder Press Machine', 'Shoulder press')");

            executePlainSQL("insert into coach values(21, 1, 'Robbert Stone', 28)");
            executePlainSQL("insert into coach values(22, 2, 'Sarah McDonald', 39)");
            executePlainSQL("insert into coach values(33, 3, 'Vincent Chang', 27)");
            executePlainSQL("insert into coach values(41, 4, 'Derrick Manning', 45)");
            executePlainSQL("insert into coach values(42, 4, 'Michelle Farrell', 30)");
            executePlainSQL("insert into coach values(34, 4, 'Georgia Evin', 70)");

            executePlainSQL("insert into coaches values(21, 100)");
            executePlainSQL("insert into coaches values(21, 101)");
            executePlainSQL("insert into coaches values(22, 102)");
            executePlainSQL("insert into coaches values(33, 103)");
            executePlainSQL("insert into coaches values(41, 104)");
            executePlainSQL("insert into coaches values(42, 105)");

            executePlainSQL("insert into powerliftingcoach values(21, 1230)");
            executePlainSQL("insert into powerliftingcoach values(22, 880)");
            executePlainSQL("insert into powerliftingcoach values(41, 1560)");
            executePlainSQL("insert into powerliftingcoach values(33, NULL)");
            executePlainSQL("insert into powerliftingcoach values(34, NULL)");

            executePlainSQL("insert into physiotherapist values(21, 'Bachelor of Kinesiology at UBC')");
            executePlainSQL("insert into physiotherapist values(33, NULL)");
            executePlainSQL("insert into physiotherapist values(22, 'Bachelor of Physical Therapy at McGill')");
            executePlainSQL("insert into physiotherapist values(41, 'Bachelor of Physical Therapy at McGill')");
            executePlainSQL("insert into physiotherapist values(42, NULL)");

            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;
            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insNo'],
                ":bind2" => $_POST['insName']
            );
            $alltuples = array (
                $tuple
            );
            executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
            OCICommit($db_conn);
        }
        function handleCountRequest() {
            global $db_conn;
            $result = executePlainSQL("SELECT Count(*) FROM demoTable");
            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            }
        }

        function handleDisplayRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM branch1");
            echo "<br>Retrieved data from table branch1:<br>";
            echo "<table>";
            echo "<tr><th>branch id</th><th>city</th><th>postal code</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";
	    $result = executePlainSQL("SELECT * FROM coaches");
            echo "<br>Retrieved data from table coaches:<br>";
            echo "<table>";
            echo "<tr><th>coach ID</th><th>client ID</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";
            
            $result = executePlainSQL("SELECT * FROM coach");
            echo "<br>Retrieved data from table coach:<br>";
            echo "<table>";
            echo "<tr><th>id</th><th>branch ID</th><th>name</th><th>age</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";
            $result = executePlainSQL("SELECT * FROM client");
            echo "<br>Retrieved data from table client:<br>";
            echo "<table>";
            echo "<tr><th>id</th><th>branchID</th><th>pname</th><th>name</th><th>age</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM goal");
            echo "<br>Retrieved data from table goal:<br>";
            echo "<table>";
            echo "<tr><th>client id</th><th>weight</th><th>timeline</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM program");
            echo "<br>Retrieved data from table program:<br>";
            echo "<table>";
            echo "<tr><th>name</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM workout");
            echo "<br>Retrieved data from table workout:<br>";
            echo "<table>";
            echo "<tr><th>name</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM consistsof");
            echo "<br>Retrieved data from table consistsof:<br>";
            echo "<table>";
            echo "<tr><th>pname</th><th>wname</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM contains");
            echo "<br>Retrieved data from table contains:<br>";
            echo "<table>";
            echo "<tr><th>wname</th><th>ename</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM requires");
            echo "<br>Retrieved data from table requires:<br>";
            echo "<table>";
            echo "<tr><th>eqname</th><th>exname</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM equipment1");
            echo "<br>Retrieved data from table equipment1:<br>";
            echo "<table>";
            echo "<tr><th>name</th><th>price</th><th>square ft</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";

            $result = executePlainSQL("SELECT * FROM equipment2");
            echo "<br>Retrieved data from table equipment2:<br>";
            echo "<table>";
            echo "<tr><th>id</th><th>branch ID</th><th>name</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }
            echo "</table>";


        }
        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('generateRequest', $_POST)) {
                    #deleteData();
                    createData();
                    populateData();
                } else if (array_key_exists('populateRequest', $_POST)) {
                    #populateData();
                }
                disconnectFromDB();
            }
        }
        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if (array_key_exists('displayTuples', $_GET)) {
                    handleDisplayRequest();
                }
                disconnectFromDB();
            }
        }
		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
