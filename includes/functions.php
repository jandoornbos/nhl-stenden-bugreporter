<?php
session_start();
require_once('database.php');

/**
 * A global array with messages to display. This could contain error or success messages.
 */
$messages = $_SESSION;
if (isset($messages["shown"]))
{
    unset($_SESSION["success"]);
    unset($_SESSION["error"]);
}

function setMessagesShown(bool $shown): void
{
    $_SESSION["shown"] = $shown;
}

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
        || !$result = mysqli_stmt_get_result($stmt))
    {
        die(mysqli_error($db));
    }

    mysqli_stmt_store_result($stmt);
    $amountOfResults = mysqli_num_rows($result);
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    if ($amountOfResults <= 0)
    {
        $_SESSION["error"] = "User could not be found.";
        header("Location: index.php?p=login");
        return;
    }

    $array = mysqli_fetch_assoc($result);
    if (!password_verify($password, $array["password"]))
    {
        $_SESSION["error"] = "Password is incorrect.";
        header("Location: index.php?p=login");
        return;
    }

    $hash = createSession($array["id"]);
    $_SESSION["X-AUTH-TOKEN"] = $hash;
    $_SESSION["success"] = "You're now logged in.";
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

    try
    {
        $sessionHash = bin2hex(random_bytes(100));

        $stmt = mysqli_prepare($db, "INSERT INTO `session` (`userid`, `sessionhash`) VALUES (?, ?)");
        if (!mysqli_stmt_bind_param($stmt, "ss", $userId, $sessionHash)
            || !mysqli_stmt_execute($stmt))
        {
            die(mysqli_error($db));
        }

        return $sessionHash;
    }
    catch (Exception $e)
    {
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

    if (isset($_SESSION["X-AUTH-TOKEN"]))
    {
        $stmt = mysqli_prepare($db, "SELECT (`user`.`email`) FROM `session` JOIN `user` ON `session`.`userid` = `user`.`id` WHERE `sessionhash` = ?");
        if (!mysqli_stmt_bind_param($stmt, "s", $_SESSION["X-AUTH-TOKEN"])
            || !mysqli_stmt_execute($stmt)
            || !$result = mysqli_stmt_get_result($stmt))
        {
            die(mysqli_error($db));
        }

        mysqli_stmt_store_result($stmt);
        if (mysqli_num_rows($result) > 0)
        {
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
    if (!isset($_SESSION["X-AUTH-TOKEN"]))
    {
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
        || !mysqli_stmt_execute($stmt))
    {
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
        || !mysqli_stmt_execute($stmt))
    {
        die(mysqli_error($db));
    }

    header("Location: index.php?p=login");
}

/**
 * Get all the bugs that are in the database.
 *
 * @return array An array with all the bugs.
 */
function getBugs(): array
{
    global $db;

    $result = mysqli_query($db, "SELECT * FROM `bug`");
    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

/**
 * Get a single bug based on it's id.
 *
 * @param int $id The id of the bug.
 * @return string[]|null An associated array.
 */
function getBug(int $id): ?array
{
    global $db;

    $stmt = mysqli_prepare($db, "SELECT * FROM `bug` WHERE `id` = ?");
    if (!mysqli_stmt_bind_param($stmt, "i", $id))
    {
        die("Binding went wrong.");
    }

    if (!mysqli_stmt_execute($stmt))
    {
        echo mysqli_error($db);
        die('Getting results went wrong.');
    }

    if (!$result = mysqli_stmt_get_result($stmt))
    {
        die('Whoops');
    }

    $array = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $array;
}

/**
 * Check if a bug exists in the database.
 *
 * @param int $id The id of the bug.
 * @return bool True if the bug exists. False otherwise.
 */
function doesBugExist(int $id): bool
{
    global $db;

    $stmt = mysqli_prepare($db, "SELECT COUNT(*) FROM `bug` WHERE `id` = ?");
    if (!mysqli_stmt_bind_param($stmt, "i", $id))
    {
        die("Binding went wrong.");
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_row($result)[0] > 0;
}

/**
 * Update an existing bug with new information.
 *
 * @param int $id The id of the bug.
 * @param array $data The new data to update the bug with.
 * @return bool Returns false if bug could not be updated.
 */
function updateBug(int $id, array $data): bool
{
    global $db;

    if (null !== validateDataArray($data))
    {
        $_SESSION["error"] = validateDataArray($data);
        return false;
    }

    $stmt = mysqli_prepare($db, "UPDATE `bug` SET `productName` = ?, `productVersion` = ?, `hardware` = ?, `frequency` = ?, `proposedSolution` = ? WHERE `id` = ?");
    if (!mysqli_stmt_bind_param($stmt, "sdsssi", $data['productName'], $data["productVersion"], $data["hardware"], $data["frequency"], $data["proposedSolution"], $id))
    {
        die("Binding went wrong.");
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["success"] = "Bug #" . $id . " has been updated!";

    header("Location: index.php?p=view&id=" . $id);

    return true;
}

/**
 * Add a new bug to the system.
 *
 * @param array $data The data for the bug.
 * @return bool Returns false if bug could not be added.
 */
function addBug(array $data): bool
{
    global $db;
    global $messages;

    if (null !== validateDataArray($data))
    {
        $messages["error"] = validateDataArray($data);
        return false;
    }

    $stmt = mysqli_prepare($db, "INSERT INTO `bug` (`productName`, `productVersion`, `hardware`, `frequency`, `proposedSolution`) VALUES (?,?,?,?,?)");
    if (!mysqli_stmt_bind_param($stmt, "sdsss", $data["productName"], $data["productVersion"], $data["hardware"], $data["frequency"], $data["proposedSolution"]))
    {
        die("Binding went wrong.");
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["success"] = "Bug has been added!";

    header("Location: index.php");

    return true;
}

/**
 * Remove a certain bug. After a bug has been removed, user will be redirected to index page.
 *
 * @param int $id The id of the bug.
 */
function removeBug(int $id): void
{
    global $db;

    if (!doesBugExist($id))
    {
        $_SESSION["error"] = "Bug #" . $id . " does not exist.";
        header("Location: index.php");
        return;
    }

    $stmt = mysqli_prepare($db, "DELETE FROM `bug` WHERE `id` = ?");
    if (!mysqli_stmt_bind_param($stmt, "i", $id))
    {
        die("Binding went wrong.");
    }

    if (!mysqli_stmt_execute($stmt))
    {
        $_SESSION["error"] = "Bug #" . $id . " could not be removed.";
    }

    mysqli_stmt_close($stmt);

    $_SESSION["success"] = "Bug #" . $id . " has been removed.";

    header("Location: index.php");
}

/**
 * Set the bug to solved. After bug is set to solved, user will be redirected to index page.
 *
 * @param int $id The id of the bug.
 */
function setSolved(int $id): void
{
    global $db;

    $stmt = mysqli_prepare($db, "UPDATE `bug` SET `solved` = 1 WHERE `id` = ?");
    if (!mysqli_stmt_bind_param($stmt, "i", $id))
    {
        die("Binding went wrong.");
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["success"] = "Bug #" . $id . " has been solved.";

    header("Location: index.php");
}

/**
 * Validate the data array, to see if everything is in there.
 *
 * @param array $data The array of data to validate.
 * @return string|null Returns a string if errors happened. When there are no errors null is returned.
 */
function validateDataArray(array $data): ?string
{
    $fieldsToCheck = [
        "productName" => "Product name",
        "hardware" => "Hardware",
        "productVersion" => "Version",
        "frequency" => "Frequency",
        "proposedSolution" => "Solution"
    ];

    $errorMessages = [];

    foreach ($fieldsToCheck as $field => $name)
    {
        if (!isset($data[$field]) || empty($data[$field]))
        {
            $errorMessages[] = "The field '" . $name . "' is empty.";
        }
    }

    return count($errorMessages) > 0 ? "<ul class=\"mb-0\"><li>" . implode("</li><li>", $errorMessages) . "</li></ul>" : null;
}

// Submit handling
if (isset($_POST["action"]))
{
    switch ($_POST["action"])
    {
        case "update":
            updateBug($_POST["id"], $_POST);
            return;
        case "add":
            addBug($_POST);
            return;
        case "login":
            login($_POST["username"], $_POST["password"]);
            return;
        case "register":
            register($_POST["username"], $_POST["password"]);
            return;
    }
}

if (isset($_GET["a"]))
{
    switch ($_GET["a"])
    {
        case "remove":
            removeBug($_GET["id"]);
            return;
        case "solve":
            setSolved($_GET["id"]);
            return;
        case "logout":
            logout();
            return;
    }
}