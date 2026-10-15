<?php
    include '../../connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $n_de_control = $_POST["n_control"];

        try {
            $stmt = $pdo->prepare("DELETE FROM alumnos WHERE n_control = ?");
            $stmt->execute([$n_de_control]);
            echo "Alumno eliminado correctamente";
        } catch (PDOException $e) {
            echo "Error al eliminar alumno: " . $e->getMessage();
        }
    }
?>