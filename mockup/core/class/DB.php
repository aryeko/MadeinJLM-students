<?php


/**
 * Database Connection Manager
 *
 * @package ptejada\uFlex
 * @author  Pablo Tejada <pablo@ptejada.com>
 */
class DB
{
    /** @var  Log - Log errors and report */
    public $log;
    /** @var string - The server IP or host name */
    private $host = 'localhost';
    /** @var string - The server user to login as */
    private $user = 'jobmadei_user';
    /** @var string - The user password */
    private $password = 'q1w2e3r4';
    /** @var string - The name of the database */
    private $dbName = 'jobmadei_db';
    /** @var string - Alternative DSN string */
    private $dsn = '';
    /** @var \PDO - The DB connection session */
    private $connection;
    /**
     * Initializes the Database object
     *
     * @param string $hostOrDSN|\PDO - The domain/IP of the DB, the PDO DSN string or PDO connection
     * @param string $dbName    - The name of the database
     */
    public function __construct($hostOrDSN = '', $dbName = '')
    {
        if (!$dbName) {
            if ($hostOrDSN instanceof \PDO) {
                // Saves the PDO connection
                $this->setConnection($hostOrDSN);
            } else {
                // add full DSN string
                $this->dsn = $hostOrDSN;
            }
        } else {
            // Add the default DB credentials for MySQL
            $this->host = $hostOrDSN;
            $this->dbName = $dbName;
        }
        $this->log = new Log('DB');
    }
    /**
     * Get table object
     *
     * @param $tableName
     *
     * @return DB_Table
     */
    public function getTable($tableName)
    {
        return new DB_Table($this, $tableName);
    }
    /**
     * Set the database username
     *
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    /**
     * Set the database user password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * Set the name of the Database to connect to
     * @param string $dbName
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }
    /**
     * Get the record of the last inserted record
     *
     * @return int
     */
    public function getLastInsertedID()
    {
        return $this->getConnection()->lastInsertId();
    }
    /**
     * Gets the connecting to the database
     * Check if the database connection exists if not connects to the database
     *
     * @return \PDO | bool
     */
    public function getConnection()
    {
        if (!($this->log instanceof Log)) {
            $this->log = new Log('DB');
        }
        // Use cached connection if already connected to server
        if ($this->connection instanceof \PDO) {
            return $this->connection;
        }
        $this->log->report('Connecting to database...');
        try{
            $this->connection = new \PDO($this->generateDSN(), $this->user, $this->password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->log->report('Connected to database.');
        } catch ( \PDOException $e ){
            $this->log->error('Failed to connect to database, [SQLSTATE] ' . $e->getCode());
        }
        // Check is the connection to server succeed
        if ($this->connection instanceof \PDO) {
            return $this->connection;
        } else {
            // There was an error connecting to the DB server
            return false;
        }
    }
    /**
     * Generate the DSN string for the connection
     *
     * @return string
     */
    protected function generateDSN()
    {
        if (!$this->dsn) {
            $this->dsn = "mysql:dbname={$this->dbName};host={$this->host}";
        }
        return $this->dsn;
    }
    /**
     * Set the connection
     * @param \PDO $connection
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;
    }
}
