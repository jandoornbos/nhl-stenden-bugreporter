<?php

// Submit handler
if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "update":
            updateBug($_POST["id"], $_POST);
            return;
        case "add":
            addBug($_POST);
            return;
    }
}

// Query parameter handler
if (isset($_GET["a"])) {
    switch ($_GET["a"]) {
        case "remove":
            removeBug($_GET["id"]);
            return;
        case "solve":
            setSolved($_GET["id"]);
            return;
    }
}