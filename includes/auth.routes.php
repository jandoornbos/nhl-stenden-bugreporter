<?php

/**
 * Get the routes and if they need authentication.
 *
 * @return bool[] An array with booleans. A key returns true if login is needed, false otherwise.
 */
function getRoutes(): array
{
    return [
        "login" => false,
        "register" => false,
        "list" => true,
        "edit" => true,
        "view" => true
    ];
}
