<?php

/**
 * Get the view for displaying the page contents.
 *
 * @return string The view based on the GET parameter p.
 */
function getView(): string
{
    if (isset($_GET["p"]))
    {
        $page = $_GET["p"];
        $routes = getRoutes();
        if (array_key_exists($page, $routes))
        {
            $needsAuthentication = $routes[$page];
            if ($needsAuthentication && !isUserLoggedIn())
            {
                return requireOnceToVariable("./views/401.php");
            }

            return requireOnceToVariable("./views/" . $_GET["p"] . ".php");
        }

        return requireOnceToVariable("./views/404.php");
    }
    else
    {
        if (!isUserLoggedIn())
        {
            return requireOnceToVariable("./views/login.php");
        }

        return requireOnceToVariable("./views/list.php");
    }
}

/**
 * Convert the contents of a file to a variable. By doing this the code within the included file is also being
 * executed.
 *
 * @param string $file The file to put into a variable.
 * @return false|string False if an error occurs. Otherwise, it returns the contents of the file as a string.
 */
function requireOnceToVariable(string $file)
{
    ob_start();
    require_once $file;
    return ob_get_clean();
}