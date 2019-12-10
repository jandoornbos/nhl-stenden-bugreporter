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

function setMessagesShown(bool $shown)
{
    $_SESSION["shown"] = $shown;
}

/**
 * Get all the bugs that are in the database.
 *
 * @return array An array with all the bugs.
 */
function getBugs()
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
function getBug(int $id)
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
function doesBugExist(int $id)
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
function updateBug(int $id, array $data)
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
function addBug(array $data)
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
function removeBug(int $id)
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
function setSolved(int $id)
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
function validateDataArray(array $data)
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
    if ($_POST["action"] === "update" && isset($_POST["id"]))
    {
        // Update bug
        updateBug($_POST["id"], $_POST);
        return;
    }

    if ($_POST["action"] === "add")
    {
        // Create bug
        addBug($_POST);
        return;
    }
}

if (isset($_GET["a"]))
{
    if ($_GET["a"] === "remove" && isset($_GET["id"]))
    {
        removeBug($_GET["id"]);
        return;
    }

    if ($_GET["a"] === "solve" && isset($_GET["id"]))
    {
        setSolved($_GET["id"]);
        return;
    }
}