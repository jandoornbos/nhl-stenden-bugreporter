<?php

/**
 * Login the user into the system.
 *
 * @param string $username The username of the user.
 * @param string $password The password of the user.
 */
function login(string $username, string $password): void
{
    global $db;

    $stmt = mysqli_prepare($db, "SELECT * FROM `user` WHERE `email` = ?");
    if (!mysqli_stmt_bind_param($stmt, "s", $username)
        || !mysqli_stmt_execute($stmt)
        || !$result = mysqli_stmt_get_result($stmt)) {
        die(mysqli_error($db));
    }

    mysqli_stmt_store_result($stmt);
    $amountOfResults = mysqli_num_rows($result);
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    if ($amountOfResults <= 0) {
        setErrorMessage("User could not be found.");
        header("Location: index.php?p=login");
        return;
    }

    $array = mysqli_fetch_assoc($result);
    if (!password_verify($password, $array["password"])) {
        setErrorMessage("Password is incorrect.");
        header("Location: index.php?p=login");
        return;
    }

    $hash = createSession($array["id"]);
    setSessionToken($hash);
    setSuccessMessage("You are now logged in.");
    header("Location: index.php");
}

/**
 * Create a new session for the user.
 *
 * @param int $userId
 * @return string|null Returns the session hash for the user.
 */
function createSession(int $userId): ?string
{
    global $db;

    try {
        $sessionHash = bin2hex(random_bytes(100));

        $stmt = mysqli_prepare($db, "INSERT INTO `session` (`userid`, `sessionhash`) VALUES (?, ?)");
        if (!mysqli_stmt_bind_param($stmt, "ss", $userId, $sessionHash)
            || !mysqli_stmt_execute($stmt)) {
            die(mysqli_error($db));
        }

        return $sessionHash;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get the currently logged in user.
 *
 * @return array|null Array with data of the user. If there is no user logged in <code>null</code> will be returned.
 */
function getLoggedInUser(): ?array
{
    global $db;

    if (isset($_SESSION["X-AUTH-TOKEN"])) {
        $stmt = mysqli_prepare($db, "SELECT (`user`.`email`) FROM `session` JOIN `user` ON `session`.`userid` = `user`.`id` WHERE `sessionhash` = ?");
        if (!mysqli_stmt_bind_param($stmt, "s", $_SESSION["X-AUTH-TOKEN"])
            || !mysqli_stmt_execute($stmt)
            || !$result = mysqli_stmt_get_result($stmt)) {
            die(mysqli_error($db));
        }

        mysqli_stmt_store_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }

    return null;
}

/**
 * Check if an user is logged in.
 *
 * @return bool True if a user is logged in.
 */
function isUserLoggedIn(): bool
{
    return isset($_SESSION["X-AUTH-TOKEN"]);
}

/**
 * Put this function on the top of pages that require login.
 */
function requiresLogin(): void
{
    if (!isset($_SESSION["X-AUTH-TOKEN"])) {
        include("./views/401.php");
        die();
    }
}

/**
 * Log out the user and delete the session from the database.
 */
function logout(): void
{
    global $db;

    $sessionHash = $_SESSION["X-AUTH-TOKEN"];

    $stmt = mysqli_prepare($db, "DELETE FROM `session` WHERE `sessionhash` = ?");
    if (!mysqli_stmt_bind_param($stmt, "s", $sessionHash)
        || !mysqli_stmt_execute($stmt)) {
        die(mysqli_error($db));
    }

    unset($_SESSION["X-AUTH-TOKEN"]);

    header("Location: index.php?p=login");
}

/**
 * Register a new user in the database.
 *
 * @param string $username The username, in this case e-mail, of the user.
 * @param string $password The password of the user.
 */
function register(string $username, string $password): void
{
    global $db;

    $password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = mysqli_prepare($db, "INSERT INTO `user` (`email`, `password`) VALUES (?, ?)");
    if (!mysqli_stmt_bind_param($stmt, "ss", $username, $password)
        || !mysqli_stmt_execute($stmt)) {
        die(mysqli_error($db));
    }

    header("Location: index.php?p=login");
}