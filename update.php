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

    $stmt = $conn->prepare('SELECT Round FROM rounds WHERE Name = ?');
    $stmt->bind_param('s', $_GET['uni']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $validrounds = array();

    while ($row = $result->fetch_assoc()) {
        array_push($validrounds, $row['Round']);
    }

    echo var_dump($validrounds);
    
    if (!in_array($_POST['Round'], $validrounds)) {
        exit('ERROR: Invalid Round' . $_POST["Round"] . 'Selected for' . $_GET["uni"]);
    }

    $stmt = $conn->prepare('UPDATE users_lists SET band=?, major=?, round=?,status=? WHERE username = ? AND university = ?');
    $stmt->bind_param('ssssss', $_POST['Band'], $_POST['Major'], $_POST['Round'], $_POST['Status'], $_SESSION['name'], $_GET['uni']);
    $stmt->execute();
    $stmt->close();
    header('Location: /php/list.php');
?>

