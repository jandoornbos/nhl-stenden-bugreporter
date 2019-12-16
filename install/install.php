<?php
require_once('Installer.php');

/*
 * Only run this installation script once!
 */

echo "<h2>Bugreporter Installer</h2>";
echo "<pre>";

$installer = new Installer();
$installer->addMessageObserver(function($message) {
    echo $message . "<br>";
});

try
{
    $installer->run();
}
catch (Exception $e)
{
    echo $e->getMessage() . "<br>";
}

echo "</pre>";