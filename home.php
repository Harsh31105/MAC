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
	<meta charset="utf-8">
	<title>M.A.C. - Home Page</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link href="/styles/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>

<body class="loggedin">
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
		<h2>Home Page</h2>
		<p>Welcome,
			<?= $_SESSION['name'] ?>!
		</p>
	</div>
	<div class="spacer"></div>
	<br>
	<h3 style="text-align: center;">Progress Bar!</h3>
	<br>

	<?php
        $stmt = $conn->prepare('SELECT * FROM users_lists WHERE username = ?');
        $stmt->bind_param('s', $_SESSION['name']);
        $stmt->execute();
        $result = $stmt->get_result();
		$c = 1;
		$aws = 0.0;
		$acc = 0.0;
		$wai = 0.0;
		$rej = 0.0;
		$def = 0.0;
		$app = 0.0;
		$yta = 0.0;

		while ($row = $result->fetch_assoc()) {
			if (strcmp($row['status'],"Accepted with Scholarship") == 0)
				$aws++;
			else if (strcmp($row['status'],"Accepted") == 0)
				$acc++;
			else if (strcmp($row['status'],"Waitlisted") == 0)
				$wai++;
			else if (strcmp($row['status'],"Rejected") == 0)
				$rej++;
			else if (strcmp($row['status'],"Deferred") == 0)
				$def++;
			else if (strcmp($row['status'],"Applied") == 0)
				$app++;
			else if (strcmp($row['status'],"Yet To Apply") == 0)
				$yta++;
			}

		$sum = 0.0;
		$sum = $aws+$acc+$wai+$rej+$def+$app+$yta;
		$aws = round($aws/$sum,2)*100;
		$acc = round($acc/$sum,2)*100;
		$wai = round($wai/$sum,2)*100;
		$rej = round($rej/$sum,2)*100;
		$def = round($def/$sum,2)*100;
		$app = round($app/$sum,2)*100;
		$yta = round($yta/$sum,2)*100;
	?>


	<div class="progress mb-3" style="padding-left: 15vw; padding-right: 15vw;">
		<div class="progress-bar" role="progressbar" style="background-color: #45b386; width: <?php echo $aws ?>%;" aria-valuenow="<?php echo $aws ?>" aria-valuemin="0" aria-valuemax="100"></div>
		<div class="progress-bar" role="progressbar" style="background-color: #32a752; width: <?php echo $acc ?>%;" aria-valuenow="<?php echo $acc ?>" aria-valuemin="0" aria-valuemax="100"></div>
		<div class="progress-bar" role="progressbar" style="background-color: #4186f5; width: <?php echo $wai ?>%;" aria-valuenow="<?php echo $wai ?>" aria-valuemin="0" aria-valuemax="100"></div>
		<div class="progress-bar" role="progressbar" style="background-color: #ee4234; width: <?php echo $rej ?>%;" aria-valuenow="<?php echo $rej ?>" aria-valuemin="0" aria-valuemax="100"></div>
		<div class="progress-bar" role="progressbar" style="background-color: #b7b7b7; width: <?php echo $def ?>%;" aria-valuenow="<?php echo $def ?>" aria-valuemin="0" aria-valuemax="100"></div>
		<div class="progress-bar" role="progressbar" style="background-color: #fabc06; width: <?php echo $app ?>%;" aria-valuenow="<?php echo $app ?>" aria-valuemin="0" aria-valuemax="100"></div>
		<div class="progress-bar" role="progressbar" style="background-color: #ffff00; width: <?php echo $yta ?>%;" aria-valuenow="<?php echo $yta ?>" aria-valuemin="0" aria-valuemax="100"></div>
	</div>

	<br>

	<div style="margin-left: 20vw;">
		<div class='box' style="background-color: #45b386"></div>&nbsp;Accepted with Scholarship
		<div class='box' style="background-color: #32a752"></div><br>&nbsp;Accepted
		<div class='box' style="background-color: #4186f5"></div><br>&nbsp;Waitlisted
		<div class='box' style="background-color: #ee4234"></div><br>&nbsp;Rejected
		<div class='box' style="background-color: #b7b7b7"></div><br>&nbsp;Deferred
		<div class='box' style="background-color: #fabc06"></div><br>&nbsp;Applied
		<div class='box' style="background-color: #ffff00"></div><br>&nbsp;Yet To Apply
	</div>

	<br><br>
	<h2 style="padding-left: 40vw;">Upcoming Deadlines...</h2>

	<table style="border-collapse: separate;
				  border-spacing: 15px;
    			margin: auto;
    			border-radius: 5px;
   				background-color: #ffcccb;
   				padding: 10px;">
        <thead>
            <tr>
                <td>University Name</td>
                <td>Round</td>
                <td>Deadline</td>
            </tr>

            <?php
                $stmt = $conn->prepare('SELECT * FROM users_lists WHERE username = ?');
                $stmt->bind_param('s', $_SESSION['name']);
                $stmt->execute();
                $result = $stmt->get_result();
                $c = 1;
				$count = 0;

                while ($row = $result->fetch_assoc()) {
                   if (!strcmp($row['status'],"Yet To Apply"))
						$count++;
                }

				$dd = array("", $count);
				$university = array("", $count);
				$round = array("", $count);
				$index = 0;

				$stmt = $conn->prepare('SELECT * FROM users_lists WHERE username = ?');
                $stmt->bind_param('s', $_SESSION['name']);
                $stmt->execute();
                $result = $stmt->get_result();

				while ($row = $result->fetch_assoc()) {
					if (!strcmp($row['status'],"Yet To Apply")) {
						$university[$index] = $row['university'];
						$round[$index] = $row['round'];
						$stmt2 = $conn->prepare('SELECT Deadline FROM rounds WHERE Round = ? AND Name = ?');
						$stmt2->bind_param('ss', $row['round'], $row['university']);
						$stmt2->execute();
						$result2 = $stmt2->get_result();
						$row2 = $result2->fetch_assoc();
						$dd[$index] = $row2['Deadline'];
						$index++;
					}
				}

				$len = sizeof($dd);
				for($i1 = 0; $i1 < $len; $i1++)
				{
					for ($j1 = 0; $j1 < $len - $i1 - 1; $j1++)
					{
						$date1 = $dd[$j1];
						$date2 = $dd[$j1+1];
						if ($date1 > $date2)
						{
							$t1 = $dd[$j1];
							$dd[$j1] = $dd[$j1+1];
							$dd[$j1+1] = $t1;

							$t2 = $university[$j1];
							$university[$j1] = $university[$j1+1];
							$university[$j1+1] = $t2;
							
							$t3 = $round[$j1];
							$round[$j1] = $round[$j1+1];
							$round[$j1+1] = $t3;
						}
					}
				}

				for ($i = 0 ; $i < $len ; $i++)
				{
					echo '<tr>';
						echo '<td>' . $university[$i] . '</td>';
						echo '<td>' . $round[$i] . '</td>';
						echo '<td>' . '<b>'.date('jS M, Y', strtotime($dd[$i])) . '</b>'.'</td>';
					echo '</tr>';


				}

            ?>

		</thead>
    </table>
</body>
</html>
