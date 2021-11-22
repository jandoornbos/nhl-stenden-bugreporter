<?php

// Submit handler
if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "login":
            login($_POST["username"], $_POST["password"]);
            return;
        case "register":
            register($_POST["username"], $_POST["password"]);
            return;
    }
}

// Query parameter handler
if (isset($_GET["a"])) {
    switch ($_GET["a"]) {
        case "logout":
            logout();
            return;
    }
}