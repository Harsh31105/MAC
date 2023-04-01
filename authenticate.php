<?php
    session_start();
    // Below is the connection information for mySQL
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

    // The isset() function will check and verify whether 
    if ( !isset($_POST['username'], $_POST['password']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the username and password fields to complete the form!');
    }

    // "Preparing" the SQL
    if ($stmt = $conn->prepare('SELECT id, password FROM userrecords WHERE username = ?')) {
        // Setting Bind Parameters; In our case the username is a string so we use "s"
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        // Storing the result so we can check if the account exists in the database.
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password);
            $stmt->fetch();
            // Account exists; Proceeding to Password Verification

            if (password_verify($_POST['password'], $password)) {
                // Authentication Complete! Verification success! User has logged-in!
                // Create sessions, so we know the user is logged in, they basically act like cookies but remembers the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                header('Location: /php/home.php');
            } else {
                // Incorrect password
                echo 'Incorrect username and/or password!';
            }
        } else {
            // Incorrect username
            echo 'Incorrect username and/or password!';
        }
        $stmt->close();
    }
?>