<?php
    // Starting the user's session on the server.
    session_start();

    // If the user is not logged in, redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        header('Location: /html/login.html');
        exit;
    }

    $param = $_GET['name'];
?>

<?php
    $DATABASE_HOST = '167.71.231.52';
    $DATABASE_USER = 'project-work';
    $DATABASE_PASS = file_get_contents('../pass.txt');
    $DATABASE_NAME = 'phplogin';

    // Connecting to mySQL using the information above.
    $conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    if (mysqli_connect_errno()) {
        // If condition states if there is an error. If there is, we are now displaying an error message for the same.
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    $sql = "SELECT * FROM `collegerecords` WHERE `Name` = '$_GET[name]'";
    $result = ($conn->query($sql));
    
    //declare array to store the data of database
    $row = [];

    if ($result->num_rows > 0) {
        // fetch all data from db into array
        $row = $result->fetch_all(MYSQLI_ASSOC);
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo $_GET['name']; ?>
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="/styles/style.css" rel="stylesheet" type="text/css">
    <link href="/styles/specific-university.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color: #D3D3D3;">
    <?php
    if (!empty($row))
        foreach ($row as $rows)
    ?>
    <nav class="navtop">
        <a href="/php/home.php">
            <h1>Managing Applications for College</h1>
        </a>
        <ul>
            <li><a href="/php/list.php"><i class="fa fa-list" aria-hidden="true"></i>My List</a></li>
            <li><a href="/php/college.php"><i class="fa fa-university" aria-hidden="true"></i>Colleges</a></li>
            <li><a href="/php/profile.php"><i class="fas fa-user-circle"></i>Profile</a></li>
            <li><a href="/php/logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </nav>
    <div>
        <div class="left-btn" style="padding-top: 85px; padding-left: 25px;">
            <form action="/php/college.php" style="float: left; margin-left:6%">
                <input type="submit" value="Back to All Colleges" class="btn btn-light btn-lg" />
            </form>
            <?php
                $stmt = $conn->prepare('SELECT * FROM users_lists WHERE username = ? AND university = ?');
                $stmt->bind_param('ss', $_SESSION['name'], $_GET['name']);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    echo '<button class="btn btn-success btn-lg" style="float: right; margin-right:6%">In your list!</button>';
                } else {
                    echo '<button class="btn btn-success btn-lg" style="float: right; margin-right:6%" onclick="openForm()">Add to My List</button>';
                }
            ?>

            <div class="form-popup" id="addUni">
                <form action="/php/add_uni.php?uni=<?php echo $_GET['name'] ?>" class="form-container" method="post">
                    <h1>Add <?php echo $_GET['name'] ?> </h1>

                    <label for="Band">Band</label>
                    <select id="Band" name="Band" required>
                        <?php
                            $stmt = $conn->prepare('SHOW COLUMNS FROM users_lists LIKE "band"');
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $enumList = explode(",", str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type']) - 6))));
                            echo '<option value="" disabled selected hidden>Choose Band</option>';
                            
                            foreach ($enumList as $value) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                        ?>
                    </select>
                    <label for="Major">Intended Major</label>
                    <input type="text" placeholder="Intended Major" name="Major" required>
                    <label for="Round">Round</label>
                    <select id="Round" name="Round" required>
                        <?php
                            $stmt = $conn->prepare('SELECT Round FROM rounds WHERE Name = ?');
                            $stmt->bind_param('s', $_GET['name']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            echo '<option value="" disabled selected hidden>Choose Round</option>';
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['Round'] . '">' . $row['Round'] . '</option>';
                            }
                        ?>
                    </select>
                    <button type="submit" class="btn">Add College</button>
                    <button type="button" class="btn cancel" onclick="closeForm()">Cancel</button>
                </form>
            </div>

            <script>
                function openForm() {
                    document.getElementById("addUni").style.display = "block";
                }

                function closeForm() {
                    document.getElementById("addUni").style.display = "none";
                }
            </script>
        </div>
    </div>

    <br><br>
    
    <hr>
    <h1 style="font-size: 70px;
  				font-weight: 600;
				background-image: linear-gradient(to right, #00A36C, #2A52BE);
				color: transparent;
				background-clip: text;
				-webkit-background-clip: text;
				text-align: center;
                padding-bottom: 10px">
    <?php echo $_GET['name']; ?>
    </h1>
    <br>

    <div style="border-radius: 10px; width: 85vw; height: 85vh; display: block; margin-left: auto; margin-right: auto;">
        <img src="<?php echo $rows['Image']; ?>" style="border-radius: 10px; width: 85vw; height: 85vh; display: block; margin-left: auto; margin-right: auto;" />
        <div style="float: left; margin-top: 10px; margin-left: 10px;">
            <a href="https://www.google.com/maps/search/<?php echo str_replace(' ', '+', $rows['Location']); ?>" target='_blank' style="font-size: 25px; text-decoration: none;">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo $rows['Location']; ?>
            </a>
            <p><b style="font-size: 20px;">Tags: </b>
                <?php echo $rows['Tags']; ?>
            </p>
            <p>
                <?php echo $rows['Description']; ?>
            </p>
            <p style="float: left;">Students are called <b>"<?php echo $rows['Students']; ?>"</b>.</p>
            <a href="<?php echo $rows['Website']; ?>" target="_blank">
                <button class="btn btn-info" style="float: right;">VISIT COLLEGE WEBSITE</button>
            </a><br><br><br>
        </div>
        <div style="font-size: 50px; display: inline; align-items: center;">
            Overall Acceptance Rate: 
            <?php echo $rows['AR']; ?> %<br>
            US News Global Ranking: #
            <?php echo $rows['USNewsARGlobal'] ? $rows['USNewsARGlobal'] : "N/A"; ?><br>
            US News National Ranking: #
            <?php echo $rows['USNewsARNational'] ? $rows['USNewsARNational'] : "N/A"; ?><br>
        </div>
        <hr>
        <div class="row">
            <div class="column card">
                <h2>Enrollment Size</h2>
                <progress class="progress-value" style="width: 80%; margin-left: auto; margin-right: auto; height: 25px; background-color: #0091EA; border-radius: 5px; overflow:hidden;" value="<?php echo $rows['Enrollment_Size']; ?>" max="55568"> 32% </progress>
                <h2>
                    <?php echo $rows['Enrollment_Size']; ?>
                </h2>
            </div>

            <div class="column card" style="width:22%;">
                <h2>Student:Faculty Ratio</h2>
                <h2 style="margin-top: 10px;">
                    <?php echo $rows['Student_Ratio']; ?>:
                    <?php echo $rows['Faculty_Ratio']; ?>
                </h2>
            </div>

            <div class="column card">
                <h2>International Enrollment</h2>
                <progress class="progress-value" style="width: 80%; margin-left: auto; margin-right: auto; height: 25px; background-color: #0091EA; border-radius: 5px; overflow:hidden;" value="<?php echo $rows['International_Enrolled']; ?>" max="100"></progress>
                <h2>
                    <?php echo $rows['International_Enrolled']; ?>%
                </h2>
            </div>
            <div class="column card">
                <h2>Setting</h2>
                <br>
                <h3>
                    <?php echo $rows['Setting']; ?>
                </h3>
            </div>
            <div class="column card">
                <h2>Type</h2>
                <br>
                <h3>
                    <?php echo $rows['Type']; ?>
                </h3>
            </div>
        </div>
        <br>
        <hr width="100%">

        <p style="float: left">
        <h4>Accepted Portals:
            <?php echo $rows['Portals']; ?>
        </h4>
        </p>
        <hr width=100%>
        <table>
            <tr>
                <th>Application Rounds</th>
                <th>Deadline</th>
            </tr>
            <?php
                $stmt = $conn->prepare('SELECT Round, Deadline FROM rounds WHERE Name = ?');
                $stmt->bind_param('s', $_GET['name']);
                $stmt->execute();
                $result = $stmt->get_result();
                $c = 1;

                while ($row = $result->fetch_assoc()) {
                    $c++;
                    echo "<tr><td>" . $row['Round'] . "</td><td>" . date('jS M, Y', strtotime($row['Deadline'])) . "</td>";

                    // if (time() > strtotime($row['Deadline'])) {
                    if (strtotime("15 December 2022") > strtotime($row['Deadline'])) {
                        echo "<style> tr:nth-child($c)> td:nth-child(2){ background:red}</style>";
                        echo "<td> Deadline Passed! </td>";
                    } else {
                        // $interval = date_diff(date_create(), date_create($row['Deadline']));
                        $interval = date_diff(date_create("15 December 2022"), date_create($row['Deadline']));
                        echo "<style> tr:nth-child($c)> td:nth-child(2){ background:green}</style>";
                        echo "<td>" . ($interval->y ? ($interval->y . " years, ") : '') . $interval->m . " months, " . $interval->d . " days remaining </td>";
                    }

                    echo "</tr>";
                }

                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='2'>No Rounds Found</td></tr>";
                }
                
                $stmt->close();
            ?>
        </table>
        <br>
        <hr>
        <div style="display: inline;">
            <h3>Standardized Testing:
                <?php echo $rows['Standardized_Tests']; ?>
            </h3><br>
            <div class="progress" style="position: relative; width: 210px; height: 30px; background: #9cbab4;  border-radius: 5px; overflow: hidden;">
                <div class="progress__fill" style="width: calc((<?php echo $rows['AvgACT']; ?>/36)*100%) ; height: 100%; background: #009579;">
                </div>
                <span class="progress__text" style="position: absolute; top: 50%;   left: 5px; transform: translateY(-50%);  font: bold 14px Quicksand, sans-serif;color: #ffffff;">Average
                    ACT Score:
                    <?php echo $rows['AvgACT']; ?>
                </span>
            </div>
            <hr style="opacity: 0;">
            <div class="progress" style="position: relative; width: 210px; height: 30px; background: #9cbab4;  border-radius: 5px; overflow: hidden;">
                <div class="progress__fill" style="width: calc((<?php echo $rows['AvgSAT']; ?>/1600)*100%) ; height: 100%; background: #009579;">
                </div>
                <span class="progress__text" style="position: absolute; top: 50%;   left: 5px; transform: translateY(-50%);  font: bold 14px Quicksand, sans-serif;color: #ffffff;">Average
                    SAT Score:
                    <?php echo $rows['AvgSAT']; ?>
                </span>
            </div>
        </div>

        <hr width="100%">

        <div style="font-size: 50px; display: inline; align-items: center;">
            English Proficiency Testing:
            <?php echo $rows['Eng_Proficiency_Exam']; ?><br>
            Average Annual Tuition: $
            <?php echo $rows['Avg_Tuition']; ?><br>
            Alumni Median Starting Salary: $
            <?php echo $rows['Median_Alumni_Starting_Salary']; ?><br>
        </div>

        <hr width="100%">

        <!-- Essays -->
        <p style="float: left">
        <h4>Essays: </h4>
        </p>
        <ul id='essaynav'>
            <script type='text/javascript'>
                var c = false

                function openEssay(essay, txt) {
                    if (c) {
                        document.getElementById('essayprompt').remove()
                    }
                    element = document.createElement('p');
                    element.id = 'essayprompt';
                    element.innerHTML = '<br>' + txt;
                    document.getElementById('essaynav').insertAdjacentElement('afterend', element);
                    c = true
                };
            </script>
            <?php
            $stmt = $conn->prepare('SELECT Essay_Type FROM collegerecords WHERE Name = ?');
            $stmt->bind_param('s', $_GET['name']);
            $stmt->execute();
            $result = $stmt->get_result();
            foreach (explode(',', $result->fetch_assoc()['Essay_Type']) as $essay) {
                if ($essay == "Supplements") {
                    $stmt = $conn->prepare('SELECT supplements FROM collegerecords WHERE Name = ?');
                    $stmt->bind_param('s', $_GET['name']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $txt = $result->fetch_assoc()['supplements'];
                } elseif ($essay == "CAE") {
                    $txt = file_get_contents('../assets/CAE.txt');
                } else {
                    $txt = file_get_contents('../assets/UC.txt');
                }
                echo "<li><button class='btn btn-info' onclick='openEssay(\"$essay\",\"$txt\")'>$essay</button></li>";
            }
            $stmt->close();
            ?>
        </ul>

        <hr width="100%">

        <div style="display: inline; padding-bottom: 50px;">
            <h3>Contact Details:</h3>
            Contact Number:
            <?php echo $rows['Contact']; ?><br>
            Email:
            <?php echo $rows['Email']; ?><br>
        </div>
</body>

</html>

<?php
    mysqli_close($conn);
?>