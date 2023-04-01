<?php
    // Starting the user's session on the server.
    session_start();

    // If the user is not logged in, redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        header('Location: /html/login.html');
        exit;
    }
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
?>

<!DOCTYPE html>
<html>

<head>
    <title>My List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" 
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="/styles/style.css" rel="stylesheet" type="text/css">
    <link href="/styles/list.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color: #D3D3D3;">
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

    <div class="content">
        <h2>My List</h2>
        <br>
        <table>
            <thead>
                <tr>
                    <td>University Name</td>
                    <td>Band</td>
                    <td>Intended Major</td>
                    <td>Round</td>
                    <td>Deadline</td>
                    <td>Application Status</td>
                    <td>Edit</td>
                </tr>

                <?php
                    $stmt = $conn->prepare('SELECT * FROM users_lists WHERE username = ?');
                    $stmt->bind_param('s', $_SESSION['name']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $c = 1;
                    while ($row = $result->fetch_assoc()) {
                        $c++;
                        echo '<tr>';
                        echo '<td>' . $row['university'] . '</td>';

                        $band_color = array(
                            "High Reach" => "#32a752",
                            "Low Reach" => "#47bec6",
                            "High Target" => "#d967d9",
                            "Mid Target" => "#fabc06",
                            "Low Target" => "#ff6c00",
                            "Safety"    => "#ee4234"
                        );
                        echo '<td>' . $row['band'] . '</td>';
                        echo "<style> tr:nth-child($c)> td:nth-child(2){ background:" . $band_color[$row['band']] . "}</style>";
                        echo '<td>' . $row['major'] . '</td>';
                        echo '<td>' . $row['round'] . '</td>';
                        $stmt2 = $conn->prepare('SELECT Deadline FROM rounds WHERE Round = ? AND Name = ?');
                        $stmt2->bind_param('ss', $row['round'], $row['university']);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $row2 = $result2->fetch_assoc();
                        echo '<td>' . date('jS M, Y', strtotime($row2['Deadline'])) . '</td>';
                        // if (time() > strtotime($row2['Deadline'])) {
                            if (strtotime("15 December 2022") > strtotime($row2['Deadline'])) {
                            echo "<style> tr:nth-child($c)> td:nth-child(6){ background:red}</style>";
                        } else {
                            echo "<style> tr:nth-child($c)> td:nth-child(6){ background:green}</style>";
                        }
                        $status_color = array(
                            "Accepted with Scholarship" => "#45b386",
                            "Accepted" => "#32a752",
                            "Waitlisted" => "#4186f5",
                            "Rejected" => "#ee4234",
                            "Deferred" => "#b7b7b7",
                            "Applied" => "#fabc06",
                            "Yet To Apply" => "#ffff00"
                        );
                        echo '<td>' . $row['status'] . '</td>';
                        echo "<style> tr:nth-child($c)> td:nth-child(8){ background:" . $status_color[$row['status']] . "}</style>";
                        echo '<td width="55px" >' 
                ?>

                <button style='background: none;
                                color: inherit;
                                border: none;
                                padding: 0;
                                font: inherit;
                                cursor: pointer;
                                outline: inherit;' 
                                id="<?php echo $row["university"] ?>" onclick="openUpdateForm(this.id)">

                <i class="fas fa-edit"></i></button>

                <div class="form-popup" id="UpdateForm">
                    <form action="" class="form-container" method="post">
                        <h1></h1>
                        <label for="Band">Band</label>
                        <select id="Band" name="Band" required>
                            <?php
                                $stmt = $conn->prepare('SHOW COLUMNS FROM users_lists LIKE "band"');
                                $stmt->execute();
                                $result2 = $stmt->get_result();
                                $row2 = $result2->fetch_assoc();
                                $enumList = explode(",", str_replace("'", "", substr($row2['Type'], 5, (strlen($row2['Type']) - 6))));
                                $stmt = $conn->prepare('SELECT band FROM users_lists WHERE username = ? and university = ?');
                                $stmt->bind_param('ss', $_SESSION['name'], $_COOKIE['uni']);
                                $stmt->execute();
                                $result2 = $stmt->get_result();
                                $row2 = $result2->fetch_assoc();

                                    foreach ($enumList as $value) {
                                        if ($row2['band'] == $value) {
                                            echo "<option value='$value' selected>$value</option>";
                                        } else {
                                            echo "<option value='$value'>$value</option>";
                                        }
                                    }
                            ?>
                        </select>
                        <label for="Major">Intended Major</label>
                        <input type="text" name="Major" value='<?php
                                                                    $stmt = $conn->prepare('SELECT major FROM users_lists WHERE username = ? and university = ?');
                                                                    $stmt->bind_param('ss', $_SESSION['name'], $_COOKIE['uni']);
                                                                    $stmt->execute();
                                                                    $result2 = $stmt->get_result();
                                                                    $row2 = $result2->fetch_assoc();
                                                                    echo $row2['major']
                                                                    ?>' required>
                        <label for="Round">Round</label>
                        <select id="Round" name="Round" required>
                        <?php
                            $stmt = $conn->prepare('SELECT Round FROM rounds WHERE Name = ?');
                            $stmt->bind_param('s', $_COOKIE['uni']);
                            $stmt->execute();
                            $result2 = $stmt->get_result();
                            $stmt = $conn->prepare('SELECT round FROM users_lists WHERE username = ? and university = ?');
                            $stmt->bind_param('ss', $_SESSION['name'], $_COOKIE['uni']);
                            $stmt->execute();
                            $result3 = $stmt->get_result();
                            $row2 = $result3->fetch_assoc();
                            while ($row3 = $result2->fetch_assoc()) {
                                if ($row2['round'] == $row3['Round']) {
                                    echo '<option value="' . $row3['Round'] . '" selected>' . $row3['Round'] . '</option>';
                                } else {
                                    echo '<option value="' . $row3['Round'] . '">' . $row3['Round'] . '</option>';
                                }
                            }
                        ?>
                        </select>
                        <label for="Status">Application Status</label>
                        <select id='Status' name='Status' required>
                            <?php
                                $stmt = $conn->prepare('SHOW COLUMNS FROM users_lists LIKE "status"');
                                $stmt->execute();
                                $result2 = $stmt->get_result();
                                $row2 = $result2->fetch_assoc();
                                $enumList = explode(",", str_replace("'", "", substr($row2['Type'], 5, (strlen($row2['Type']) - 6))));
                                $stmt = $conn->prepare('SELECT status FROM users_lists WHERE username = ? and university = ?');
                                $stmt->bind_param('ss', $_SESSION['name'], $_COOKIE['uni']);
                                $stmt->execute();
                                $result2 = $stmt->get_result();
                                $row2 = $result2->fetch_assoc();

                                foreach ($enumList as $value) {
                                    if ($row2['status'] == $value) {
                                        echo "<option value='$value' selected>$value</option>";
                                    } else {
                                        echo "<option value='$value'>$value</option>";
                                    }
                                }
                            ?>
                        </select>
                        <button type="submit" class="btn">Update Details</button>
                        <button type="button" class="btn cancel" onclick="closeUpdateForm()">Cancel</button>
                    </form>
                </div>

                <button style='background: none;
                                color: inherit;
                                border: none;
                                padding: 0;
                                font: inherit;
                                cursor: pointer;
                                outline: inherit;' id="<?php echo $row["university"] ?>" onclick="openRemoveForm(this.id)">
                <i class="fas fa-trash-alt"></i></button>
                <div class="form-popup" id="confirmDelete">
                    <form action='' class="form-container" method="post">
                        <h1 id="heading"></h1>
                        <button type="submit" class="btn" name="confirmDelete">Yes</button>
                        <button type="button" class="btn cancel" onclick="closeRemoveForm()">No</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php
            }
        ?>
        </thead>
        </table>

        <script>
            function openRemoveForm(uni) {
                var form2 = document.getElementById('UpdateForm');
                form2.style.display = "none";
                var form = document.getElementById('confirmDelete');
                form.style.display = "block";
                var child = form.childNodes[1];
                child.action = "/php/remove_uni.php?uni=" + uni;
                var heading = child.childNodes[1];
                heading.innerHTML = "Are you sure you want to delete " + uni + "?";

            }

            function closeRemoveForm() {
                document.getElementById('confirmDelete').style.display = "none";
            }

            function openUpdateForm(uni) {
                document.cookie = "uni=" + uni;
                var form2 = document.getElementById('confirmDelete');
                form2.style.display = "none";
                var form = document.getElementById('UpdateForm');
                form.style.display = "block";
                var child = form.childNodes[1];
                child.action = "/php/update.php?uni=" + uni;
                child.childNodes[1].innerHTML = "Update " + uni;
            }

            function closeUpdateForm() {
                document.getElementById('UpdateForm').style.display = "none";
            }
        </script>
</body>
</html>