<?php
require_once 'secrets.php';

// Constants are defined in includes/secrets.php
$db = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);
if (!$db)
{
    die("Could not connect to database." . mysqli_connect_error());
}

mysqli_select_db($db, "bugreporter");