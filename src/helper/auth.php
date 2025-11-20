<?php
//te controleren of je bent ingelogd
session_start();
function toonLogin()
{
    if (isset($_SESSION["login_naam"])) {
        echo "<span class='c-profile'>welkom, " . $_SESSION["login_naam"] . "<span> <a class='c-profile__action' href='logout.php'>uitloggen</a>";
    } else {
        echo "<a class='c-profile__action' href='login.php'>log in </a>";
    }
}

//de login en logout knop
function checkValidLogin()
{
    if (!isset($_SESSION["login_naam"])) {
        echo "je hebt geen toegang tot deze pagina, ga terug naar <a
href='login.php'>index.php</a>";
        exit();
    }
}
