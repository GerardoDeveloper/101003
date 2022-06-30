<?php
include "config.php";

class Conexion
{
    private static $instancia;
    private $conn;

    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASSWORD;

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);

            $this->conn->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            $error = "Error!: " . $e->getMessage();

            echo $error;

            die();
        }
    }

    public function prepare($sql)
    {
        return $this->conn->prepare($sql);
    }

    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    public function commit()
    {
        return $this->conn->commit();
    }

    public function rollBack()
    {
        return $this->conn->rollBack();
    }

    public function setAttribute()
    {
        return $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;

            self::$instancia = new $miclase;
        }

        return self::$instancia;
    }

    public function __clone()
    {
        trigger_error("La clonación de este objeto no está permitida", E_USER_ERROR);
    }
}
