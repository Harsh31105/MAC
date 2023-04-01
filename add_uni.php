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

    $stmt = $conn->prepare('SELECT * FROM users_lists WHERE username = ? AND university = ?');
    $stmt->bind_param('ss', $_SESSION['name'], $_GET['uni']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        exit('You have already added this university to your list.');
    }

    $stmt = $conn->prepare('INSERT into users_lists (username,university,band,major,round) values (?,?,?,?,?)');
    $stmt->bind_param('sssss', $_SESSION['name'], $_GET['uni'], $_POST['Band'], $_POST['Major'], $_POST['Round']);
    $stmt->execute();
    $stmt->close();
    header('Location: /php/list.php');
?>

