<?php
session_start();

/*=========================================
    UNSET ALL SESSION VARIABLES
=========================================*/

$_SESSION = [];

/*=========================================
    DESTROY SESSION
=========================================*/

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(

        session_name(),

        '',

        time() - 42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );

}

session_destroy();

/*=========================================
    REDIRECT TO LOGIN
=========================================*/

header("Location: login.php");
exit();
?>