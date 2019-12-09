<?php
session_start();
require_once('database.php');

/**
 * A global array with messages to display. This could contain error or success messages.
 */
$messages = [];

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
    if (!mysqli_stmt_bind_param($stmt, 'i', $id))
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

    if (null !== validateDataArray($data))
    {
        $_SESSION["error"] = validateDataArray($data);
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
    }

    if ($_POST["action"] === "add")
    {
        // Create bug
        addBug($_POST);
    }
}