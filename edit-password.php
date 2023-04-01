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
		// If condition states if there is an error. If there is, we are now displaying an error message for the same.
		exit('Failed to connect to MySQL: ' . mysqli_connect_error());
	}

	if ($stmt = $conn->prepare('SELECT id, password FROM userrecords WHERE username = ?')) {
		// Setting Bind Parameters; In our case the username is a string so we use "s"
		$stmt->bind_param('s', $_SESSION['name']);
		$stmt->execute();

		// Storing the result so we can check if the account exists in the database.
		$stmt->store_result();

		if ($stmt->num_rows > 0) {
			$stmt->bind_result($id, $password);
			$stmt->fetch();

			// Account exists; Proceeding to Password Verification
			if (password_verify($_POST['currentPassword'], $password)) {

				if ($_POST['newPassword'] == $_POST['confirmPassword']) {
					// Authentication Complete! Verification success! User has logged-in!
					// Create sessions, so we know the user is logged in, they basically act like cookies but remembers the data on the server.
					$stmt = $conn->prepare('UPDATE userrecords SET password = ? WHERE username = ?');
					// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
					$password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
					$stmt->bind_param('ss', $password, $_SESSION['name']);
					$stmt->execute();
					$stmt->close();
					echo 'Password Changed!'; 
?>

<script>
	alert('Password Changed! Please Login again!')
	window.location.href = '/html/login.html';
</script>

<?php
				} else {
					// Incorrect password
					echo 'Passwords dont match!';
				}
			} else {
				// Incorrect password
				echo 'Incorrect password!';
			}
		} else {
			// Incorrect username
			echo 'Error!';
		}
	}
?>