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
        $band = $_POST["band"];
        echo $band;
    }
?>