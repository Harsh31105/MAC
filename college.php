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

	$sql = "select * from collegerecords";
	$result = ($conn->query($sql));
	//declare array to store the data of database
	$row = [];

	if ($result->num_rows > 0) {
		// fetch all data from db into array
		$row = $result->fetch_all(MYSQLI_ASSOC);
	}
?>

<?php
	mysqli_close($conn);
?>


<html>

<head>
	<title>All Colleges - M.A.C.</title>
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
		<?php
			if (!empty($row))
				foreach ($row as $rows) {
		?>
		<div class="card w-125">
			<div class="card-body">
				<h4 class="card-title"><?php echo $rows['Name']; ?></h4>
				<p class="card-content">
					<img src="<?php echo $rows['Logo']; ?>" style="float: right" />
					<b><?php echo $rows['Location']; ?><br>
						Tags: <?php echo $rows['Tags']; ?><br></b>
					<?php echo $rows['Description']; ?>
				</p>
				<a href="/php/specific-university.php?name=<?php echo $rows['Name']; ?>" class="btn btn-info"><?php echo $rows['Name']; ?></a>
			</div>
		</div>
		<?php } ?>
	</div>
</body>

</html>