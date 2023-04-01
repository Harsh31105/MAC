<?php
	// Change this to your connection info.
	$DATABASE_HOST = '167.71.231.52';
	$DATABASE_USER = 'project-work';
	$DATABASE_PASS = file_get_contents('../pass.txt');
	$DATABASE_NAME = 'phplogin';

	// Connecting to mySQL using the information above.
	$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

	if ( mysqli_connect_errno() ) {
		// If condition states if there is an error. If there is, we are now displaying an error message for the same.
		exit('Failed to connect to MySQL: ' . mysqli_connect_error());
	}

	// Now we check if the data was submitted, isset() function will check if the data exists.
	if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
		// Could not get the data that should have been sent.
		exit('Please complete the registration form!');
	}

	// Make sure the submitted registration values are not empty.
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
		// One or more values are empty.
		exit('Please complete the registration form');
	}

	// We need to check if the account with that username exists.
	if ($stmt = $conn->prepare('SELECT id, password FROM userrecords WHERE username = ?')) {
		// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		$stmt->store_result();
		// Store the result so we can check if the account exists in the database.

		if ($stmt->num_rows > 0) {
			// Username already exists
			$text = 'Username exists, please choose another!';
			echo "<p style='font: size 50px;'>".$text."</p>";
		} else {
			// Username doesnt exists, insert new account
			if ($stmt = $conn->prepare('INSERT INTO userrecords (username, password, email) VALUES (?, ?, ?)')) {
				// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
				$hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$stmt->bind_param('sss', $_POST['username'], $hashedPassword, $_POST['email']);
				$stmt->execute();
				$text = 'You have successfully registered, you can now login!';
				echo "<p style='font: size 50px;'>".$text."</p>";
			} else {
				// Something is wrong with the sql statement, check to make sure userrecords table exists with all 3 fields.
				$text = 'Could not prepare statement!';
				echo "<p style='font: size 50px;'>".$text."</p>";
			}
		}
		$stmt->close();

	} else {
		// Something is wrong with the sql statement, check to make sure userrecords table exists with all 3 fields.
		$text = 'Could not prepare statement!';
		echo "<p style='font: size 50px;'>".$text."</p>";
	}
	
	$conn->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>M.A.C. - What's Next?</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="/styles/register.css" rel="stylesheet" type="text/css">
	</head>
	<style>
		form  input {
			width: 100px;
			margin-left: 650px;
		}
	</style>
	<body>
		<form id="redirect" action="/html/login.html">
			<input type="submit" value="Login">
		</form>
	</body>
</html>
