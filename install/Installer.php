<?php
require_once("../includes/secrets.php");

class Installer
{
    /**
     * The name of the database.
     */
    private const DATABASE_NAME = "bugreporter";

    /**
     * @var mysqli
     */
    private $db;

    /**
     * @var bool
     */
    private $didRun;

    /**
     * @var callable
     */
    private $observer;

    /**
     * Installer constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);
        $this->didRun = false;

        if ($this->db->connect_error)
        {
            throw new Exception("Could not connect to database");
        }
    }

    /**
     * Add a message observer.
     *
     * @param callable $observer
     */
    public function addMessageObserver(callable $observer)
    {
        $this->observer = $observer;
    }

    /**
     * Run the installer.
     *
     * @throws Exception when something went wrong.
     */
    public function run(): void
    {
        if ($this->didRun)
        {
            throw new Exception("Installer has already run.");
        }

        $this->createDatabase();
        $this->selectDatabase();
        $this->createBugTable();
        $this->createUserTable();
        $this->createSessionTable();

        $this->db->close();
        $this->didRun = true;

        $this->sendBackMessage("The installation has been executed successfully.");
    }

    /**
     * Creates a new database if it does not exist already.
     *
     * @throws Exception when database could not be created.
     */
    private function createDatabase(): void
    {
        if ($this->db->query("CREATE DATABASE IF NOT EXISTS `" . self::DATABASE_NAME . "`") === false)
        {
            throw new Exception("Database could not be created.");
        }

        $this->sendBackMessage("Database has been created.");
    }

    /**
     * Select the created database.
     *
     * @throws Exception when database could not be selected.
     */
    private function selectDatabase(): void
    {
        if (!$this->db->select_db(self::DATABASE_NAME))
        {
            throw new Exception("Database could not be selected.");
        }

        $this->sendBackMessage("Database selected.");
    }

    /**
     * Create the table for bugs.
     *
     * @throws Exception when table could not be created.
     */
    private function createBugTable(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `bug` (
                `id` int AUTO_INCREMENT NOT NULL PRIMARY KEY,
                `productName` VARCHAR(255),
                `productVersion` double,
                `hardware` VARCHAR(255),
                `frequency` VARCHAR(255),
                `proposedSolution` LONGTEXT,
                `solved` TINYINT
            )
        ";

        $this->executeTableQuery($query, "bug");
        $this->sendBackMessage("Table 'bug' has been created.");
    }

    /**
     * Create the table for users.
     *
     * @throws Exception when table could not be created.
     */
    private function createUserTable(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user` (
                `id` int AUTO_INCREMENT NOT NULL PRIMARY KEY,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL
            )
        ";

        $this->executeTableQuery($query, "user");
        $this->sendBackMessage("Table 'user' has been created.");
    }

    /**
     * Create the table for sessions.
     *
     * @throws Exception when table could not be created.
     */
    private function createSessionTable(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `session` (
                `id` int AUTO_INCREMENT NOT NULL PRIMARY KEY,
                `userid` int,
                `sessionhash` VARCHAR(255) NOT NULL,
                FOREIGN KEY (`userid`) REFERENCES `user`(`id`)
            )
        ";

        $this->executeTableQuery($query, "session");
        $this->sendBackMessage("Table 'session' has been created.");
    }

    /**
     * @param string $query The query to execute.
     * @param string $tableName The name of the table.
     * @throws Exception
     */
    private function executeTableQuery(string $query, string $tableName)
    {
        if ($this->db->query($query) === false)
        {
            throw new Exception("Table '" . $tableName . "' could not be created: " . $this->db->error);
        }
    }

    /**
     * Send a message to the observer.
     *
     * @param $message string The message to send.
     */
    private function sendBackMessage(string $message)
    {
        if (null !== $this->observer)
        {
            call_user_func($this->observer, $message);
        }
    }
}