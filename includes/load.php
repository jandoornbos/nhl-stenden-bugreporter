<?php
define("MESSAGES_KEY", "messages");

session_start();

require_once "connection.db.php";
require_once "session.functions.php";
require_once "auth.functions.php";
require_once "auth.handlers.php";
require_once "bug.functions.php";
require_once "bug.handlers.php";
