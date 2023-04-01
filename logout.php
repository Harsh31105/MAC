<?php
    session_start();
    session_destroy();
    
    //After Logout, user is redirected to the Login page...
    header('Location: /html/login.html');
?>