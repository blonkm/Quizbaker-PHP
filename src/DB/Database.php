<?

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

class Database {

    public $host;
    public $database;
    public $user;
    public $password;

    function __construct($database = 'quizbaker', $user = 'root', $password = '', $host = 'localhost') {
        assert(!empty($database));
        $this->database = Config::$dbname or $database;
        $this->user = Config::$dbuser or $user;
        $this->password = Config::$dbpassword or $password;
        $this->host = Config::$dbserver or $host;
    }

    function connect() {
        try {
            $options = array(PDO::ATTR_PERSISTENT => true);
            $template = 'mysql:host=%s;dbname=%s;charset=utf8';
            $database = sprintf($template, $this->host, $this->database);
            $connection = new PDO($database, $this->user, $this->password, $options);
            // necessary to generate Exceptions, otherwise will simply return empty object
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->exec("set names utf8");
        } catch (Exception $e) {
            throw new Exception('can not connect');
        }
        return $connection;
    }

    function insert($sql) {
        assert(is_string($sql));

        try {
            $connection = $this->connect();
            $q = $connection->prepare($sql);
            $q->execute();
        } catch (PDOException $e) {
            $pdoMsg = $e->getMessage();
            $strMessage = 'insertRecord: Failed ' .
              $sql . ', Error Message: ' . $pdoMsg; 
            Debug::out($strMessage, false, true, true);
            throw $e;
        }
        return $connection->lastInsertId();
    }

    function executePrepared($sql, $data) {
        assert(is_string($sql));

        $result = false;
        $connection = $this->connect();
        try {
            $statement = $connection->prepare($sql);
            foreach ($data as $key => $value)
                $params[':' . $key] = $value;
            //$statement->bindParam(":" . $key, $value);
            $result = $statement->execute($params);
        } catch (Exception $e) {
            Debug::out($sql, false, true, true);
            throw $e;
        }
        return $result;
    }

    function query($sql) {
        assert(is_string($sql));

        try {
            $connection = $this->connect();
            $rs = $connection->query($sql, PDO::FETCH_OBJ);
        } catch (Exception $e) {
            Debug::out($sql, false, true, true);
            throw $e;
        }
        return $rs;
    }

    function execute($sql) {
        return $this->query($sql);
    }

    function getRs($sql) {
        return $this->query($sql);
    }

}

?>