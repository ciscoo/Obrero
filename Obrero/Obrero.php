<?php
/**
 * Obrero - An improved PHP 5 mysqli wrapper class for CSCI 322. 
 *
 * @author      Francisco Mateo <hello@mateo.io>
 * @copyright   2015 Francisco Mateo
 * @version     0.0.1
 * @package     Obrero
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Obrero;

/**
 * Obrero Class
 * 
 * This class implements the singleton pattern using a static variable and
 * the static creation method getInstance().

 * Obrero reimplements many of the functions found in professors Knautz's connection
 * class using the MySQLi API. Many of the functions have been either renamed to a
 * more "standard" (Java) naming covention or their prefixed "_" underscore has been
 * dropped.

 * The user must edit the $config array variable with the appropiate credentials.
 * For example:
 *
 *     $config = [
 *         'host' => '127.0.0.1',
 *         'username' => 'root',
 *         'passwd' => 'passwd',
 *         'dbname' => 'volga'
 *     ];
 *
 */
class Obrero
{
    /**
     * The database configuration array.
     *
     * @var array
     */
    private $config = [
        'host' => null,
        'username' => null,
        'passwd' => null,
        'dbname' => null
    ];

    /**
     * The mysqli instance.
     *
     * @var mysqli
     */
    private $mysqli;

    /**
     * The last error message.
     *
     * @var string
     */
    private $errorMessage;

    /**
     * The last successful operation message.
     *
     * @var string
     */
    private $successMessage;    

    /**
     * The result set from a query.
     *
     * @var bool|mysqli_result
     */
    private $result;

    /**
     * The number of returned rows from a query.
     *
     * @var int
     */
    private $returnedRows;

    /**
     * Returns the number of rows affected by the last
     * INSERT, UPDATE, REPLACE or DELETE query.
     *
     * @var int
     */
    private $affectedRows;

    /**
     * Returns the singleton instance of this class.
     *
     * @return static The singleton instance.
     */
    public static function getInstance()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Prevent instantiation via the `new` operator.
     */
    protected function __construct()
    {
        $this->mysqli->init();

        if (!$this->mysqli) {
            $this->errorMessage = $this->mysqli->error;
            die("mysqli::init failed: " . $this->mysqli->error);
        }
    }

    public function __destruct()
    {
        @$this->mysqli->close();
        $this->mysqli = null;
    }

    /**
     * Prevent cloning of the instance.
     *
     * @return void
     */
    private function __clone() { }

    /**
     * Prevent unserializing of the instance.
     *
     * @return void
     */
    private function __wakeup() { }

    /**
     * Opens a connection to a mysql server.
     *
     * @return bool Always true, dies on failure.
     */
    public function connect()
    {
        @$this->mysqli->real_connect($this->config['host'], $this->config['username'],
                                    $this->config['passwd'], $this->config['dbname']);

        if ($this->mysqli->connect_error) {
            $this->errorMessage = $this->mysqli->connect_error;
            die("Connection error (" . $this->mysqli->connect_errno . ") "
                . $this->errorMessage);
        }
        
        return true;
    }

    /**
     * Disconnects from the current database.
     *
     * @return bool Always true, dies on failure.
     */
    public function disconnect()
    {
        if (!$this->mysqli->close()) {
            $this->errorMessage = "Failed to close the connection to database." . $this->mysqli->error;
            die("mysqli::close failed: " . $this->errorMessage);
        }

        return true;
    }

    /**
     * Selects the default database for database queries.
     *
     * @param string $dbname
     * @return bool Always true, dies on failure.
     */
    public function selectDatabase($dbname)
    {
        if (!$this->mysqli->select_db($dbname)) {
            $this->errorMessage = "Selecting the database named {$dbname} failed. "
                                . "Check if database exists or misspelled name.";
            die("mysqli::select_db failed: " . $this->errorMessage);
        }

        return true;
    }

    /**
     * Executes a raw query against the database.
     *
     * @param string $query The query string to execute.
     * @void
     */
    public function executeQuery($query)
    {
        if (preg_match('[SELECT|SHOW|DESCRIBE|EXPLAIN]', $query) === true) {
            if ($result = $this->mysqli->query($query)) {
                $this->result = $this->mysqli->store_result();
                $this->returnedRows = $result->num_rows;
                $result->free();
            }
        }

        if($this->mysqli->query($query) === true) {
            $this->errorMessage = "Query executed successfully, no error.";
            $this->affectedRows = $this->mysqli->affected_rows;
        } else {
            $this->errorMessage = $this->mysqli->error;
        }
    }

    /**
     * @return int $returnedRows The number of affected.
     */
    public function getNumberOfReturnedRows()
    {
        return $this->returnedRows;
    }

    /**
     * Returns the number of affected rows from a query.
     *
     * @return int $affectedRows The number of affected rows.
     */
    public function getNumberOfAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * Returns the first row from the result set.
     *
     *
     */
    public function getFirstResult()
    {

    }
}
