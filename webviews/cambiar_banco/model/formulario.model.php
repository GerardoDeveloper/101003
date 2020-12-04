<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$d = "hilos/101003/webviews/cambiar_banco";
include_once $_SERVER['DOCUMENT_ROOT'] . "/$d/inc/config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/$d/inc/database.php";

class FormularioModel
{
    private static $instancia;
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = Conexion::getInstance();
        } catch (Exception $e) {
            $error = "Error!: " . $e->getMessage();

            die();
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }

    public static function getInstance()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;

            self::$instancia = new $miclase;
        }

        return self::$instancia;
    }

    public function insertFormulario($identificador, $fecha_inicio, $origen)
    {
        try {
            $this->conn->setAttribute();

            $this->conn->beginTransaction();

            $sql = "INSERT INTO " . TB_FORMULARIO . " (identificador, fecha_inicio, origen) VALUES (:identificador, :fecha_inicio, :origen)";

            $query = $this->conn->prepare($sql);

            $query->bindParam(':identificador', $identificador, PDO::PARAM_STR);
            $query->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
            $query->bindParam(':origen', $origen, PDO::PARAM_STR);

            if ($query->execute()) {
                $status = 1;
            } else {
                $status = 0;
            }

            $this->conn->commit();

            return [$status, ""];
        } catch (PDOException $e) {
            $this->conn->rollback();

            $error = $e->getMessage();

            return [0, $error];
        }
    }

    public function updateFormulario($identificador, $fecha_fin, $empresa, $apellidos, $nombres, $dni, $banco, $cbu, $fecha_ingreso, $provincia)
    {
        try {
            $this->conn->setAttribute();

            $this->conn->beginTransaction();

            $sql = "UPDATE " . TB_FORMULARIO . " set fecha_fin = '$fecha_fin', empresa = '$empresa', apellidos = '$apellidos', nombres = '$nombres', dni = '$dni', banco = '$banco', cbu= '$cbu', fecha_ingreso = '$fecha_ingreso', provincia= '$provincia' WHERE identificador = '$identificador' ORDER BY id DESC LIMIT 1";

            $query = $this->conn->prepare($sql);

            if ($query->execute()) {
                $status = 1;
            } else {
                $status = 0;
            }

            $this->conn->commit();

            return [$status, ""];
        } catch (PDOException $e) {
            $this->conn->rollBack();

            $error = "Error!: " . $e->getMessage();

            return [0, $error];
        }
    }
}
