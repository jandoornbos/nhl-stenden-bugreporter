<?php

/**
 * Login the user into the system.
 *
 * @param string $username The username of the user.
 * @param string $password The password of the user.
 * @throws Exception When something goes wrong.
 */
function login(string $username, string $password): void
{
    $user = getUserByEmail($username);
    if (null === $user)
    {
        showErrorAndRedirectToLogin("User could not be found.");
        return;
    }

    if (!verifyPassword($password, $user["password"]))
    {
        showErrorAndRedirectToLogin("Password is incorrect.");
        return;
    }

    $hash = createSessionHash();
    if (!storeSession($hash, $user["id"]))
    {
        showErrorAndRedirectToLogin("Could not make a user session.");
        return;
    }

    setSessionToken($hash);
    setSuccessMessage("You are now logged in.");
    header("Location: index.php");
}

/**
 * Get a user from the database based on email address.
 *
 * @param string $email The email address to search for.
 * @return array|null Returns an array with user data when a user is found. Otherwise, it returns null.
 */
function getUserByEmail(string $email): ?array
{
    global $db;

    $stmt = mysqli_prepare($db, "SELECT * FROM `user` WHERE `email` = ?");
    if (!mysqli_stmt_bind_param($stmt, "s", $email)
        || !mysqli_stmt_execute($stmt)
        || !$result = mysqli_stmt_get_result($stmt)) {
        die(mysqli_error($db));
    }

    mysqli_stmt_store_result($stmt);
    $amountOfResults = mysqli_num_rows($result);
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    if ($amountOfResults <= 0)
    {
        return null;
    }

    return mysqli_fetch_assoc($result);
}

/**
 * Verify a password.
 *
 * @param string $password A password.
 * @param string $otherPassword Another password.
 * @return bool True when password is correct, false otherwise.
 */
function verifyPassword(string $password, string $otherPassword): bool
{
    return password_verify($password, $otherPassword);
}

/**
 * Create a unique session hash.
 *
 * @return string A string to use as a session hash.
 * @throws Exception
 */
function createSessionHash(): string
{
    return bin2hex(random_bytes(100));
}

/**
 * Store a session in the database.
 *
 * @param string $hash The hash to store.
 * @param int $userId The user to link it to.
 * @return bool True when storing the session was successful, false otherwise.
 */
function storeSession(string $hash, int $userId): bool
{
    global $db;

    try
    {
        $stmt = mysqli_prepare($db, "INSERT INTO `session` (`userid`, `sessionhash`) VALUES (?, ?)");
        if (!mysqli_stmt_bind_param($stmt, "ss", $userId, $hash)
            || !mysqli_stmt_execute($stmt)) {
            die(mysqli_error($db));
        }

        return true;
    }
    catch (Exception $e)
    {
        return false;
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

/**
 * Set a message to be shown on the webpage. Then redirect to the login page.
 *
 * @param string $message The message to display.
 */
function showErrorAndRedirectToLogin(string $message): void
{
    setErrorMessage($message);
    header("Location: index.php?p=login");
}