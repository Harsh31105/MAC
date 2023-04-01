<?php
	// We need to use sessions, so you should always start sessions using the below code.
	session_start();
	
	// If the user is not logged in redirect to the login page...
	if (!isset($_SESSION['loggedin'])) {
		header('Location: /html/login.html');
		exit;
	}

	$DATABASE_HOST = '167.71.231.52';
	$DATABASE_USER = 'project-work';
	$DATABASE_PASS = file_get_contents('../pass.txt');
	$DATABASE_NAME = 'phplogin';

	// Connecting to mySQL using the information above.
	$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
	if (mysqli_connect_errno()) {
		exit('Failed to connect to MySQL: ' . mysqli_connect_error());
	}

	// We don't have the password or email info stored in sessions so instead we can get the results from the database.
	$stmt = $conn->prepare('SELECT password, email FROM userrecords WHERE id = ?');
	
	// In this case we can use the account ID to get the account info.
	$stmt->bind_param('i', $_SESSION['id']);
	$stmt->execute();
	$stmt->bind_result($password, $email);
	$stmt->fetch();
	$stmt->close();
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>User Profile</title>
	<link href="/styles/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
		<h2>User Profile</h2>
		<div>
			<p>Your account details are below:</p>
			<table>
				<tr>
					<td>Username:</td>
					<td><?= $_SESSION['name'] ?></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>***************
						<form style="float: right;" action="/html/edit-password.html">
                			<input type="submit" value="Change Password" />
            			</form>
					</td>
				</tr>
				<tr>
					<td>Email:</td>
					<td><?= $email ?></td>
				</tr>
			</table>
		</div>
	</div>
</body>

</html>