<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if (file_exists(__DIR__ . "/../inc/config.php")) {
    require_once __DIR__ . "/../inc/config.php";
}

if (file_exists(__DIR__ . "/../inc/database.php")) {
    require_once __DIR__ . "/../inc/database.php";
}

class FormularioModel
{
    /**
     * Propiedades de clase.
     */
    private static $instancia;
    private $conn;

    /**
     * Constructor de la clase.
     */
    private function __construct()
    {
        try {
            $this->conn = Conexion::getInstance();
        } catch (Exception $e) {
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/error.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            die();
        }
    }

    /**
     * Destructor de la clase.
     */
    private function __destruct()
    {
        $this->conn = null;
    }

    /**
     * Obtiene la instancia de si misma de la clase utilizando el patrón singleton.
     *
     * @return __CLASS__
     */
    public static function getInstance()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;

            self::$instancia = new $miclase;
        }

        return self::$instancia;
    }

    /**
     * Inserta los registros por defecto del formulario.
     *
     * @param string $identificador N° que identifica al usuario.
     * @param datetime $fecha_inicio Fecha en la que se abrio el formulario en el frontend.
     * @param string $origen El origen desde el cual fue abierto.
     * @return array
     */
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
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return [0, $message];
        }
    }

    /**
     * Actualiza los datos del formulario.
     *
     * @param string $identificador N° que identifica al usuario.
     * @param datetime $fecha_fin Fecha en la que se termina de actualizar los datos restantes del formulario.
     * @param string $idMisFichadas El origen desde el cual fue abierto.
     * @return array
     */
    public function updateFormulario($identificador, $fecha_fin, $idMisFichadas)
    {
        try {
            $this->conn->setAttribute();
            $this->conn->beginTransaction();
            $sql = "UPDATE " . TB_FORMULARIO . " set fecha_fin = '$fecha_fin', id_mis_fichadas= $idMisFichadas WHERE identificador = '$identificador' ORDER BY id DESC LIMIT 1";
            $query = $this->conn->prepare($sql);

            if ($query->execute()) {
                $status = 1;
            } else {
                $status = 0;
            }

            $this->conn->commit();

            return [$status, ""];
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return [0, $message];
        }
    }

    /**
     * btiene los datos de Mis Fichadas.
     *
     * @return array
     */
    public function getMisFichadas()
    {
        try {
            $sql = "SELECT * FROM " . TB_MIS_FICHADAS;
            $query = $this->conn->prepare($sql);

            if ($query->execute()) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return [];
        }
    }

    /**
     * Obtiene los datos de los detalles de Mis Fichadas.
     *
     * @param integer $idMisFichadas Id de l fichada.
     * @return array
     */
    public function getDetallesMisFichadas($idMisFichadas)
    {
        try {
            $sql = "SELECT * FROM " . TB_DETALLE_MIS_FICHADAS . " WHERE id_mis_fichadas = $idMisFichadas";
            $query = $this->conn->prepare($sql);

            if ($query->execute()) {
                return $query->fetch(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            $line = $e->getLine();
            $file = $e->getFile();
            $trace = $e->getTraceAsString();
            $message = $e->getMessage();
            $error = "Ha ocurrido un error:\nLínea: $line\nFile: $file\nTrace:\n$trace\n" . $message . "\n\n" . "===================================================================================================================================================================";

            $fecha = date("Y/m/d H:i:s");
            file_put_contents(__DIR__ . '/php-errors.log', $fecha . "\n" . "Error: " . $error . "\n\n", FILE_APPEND);
            error_log($error);

            return [];
        }
    }
}
