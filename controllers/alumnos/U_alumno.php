<?php
    include '../../connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $n_de_control = $_POST["n_control"];
        $apellidos = $_POST["apellidos"];
        $nombres = $_POST["nombres"];
        $sexo = $_POST["sexo"];
        $estado_civil = $_POST["estado_civil"];
        $carrera = $_POST["carrera"];
        $reticula = $_POST["reticula"];
        $semestre_actual = $_POST["semestre_actual"];
        $correo_institucional = $_POST["correo_institucional"];
        $correo_personal = $_POST["correo_personal"];
        $telefono_contacto = $_POST["telefono_contacto"];
        $fecha_ingreso = $_POST["fecha_ingreso"];
        $estatus_academico = $_POST["estatus_academico"];
        $fecha_creacion = $_POST["fecha_creacion"];
        $fecha_actualizacion = $_POST["fecha_actualizacion"];
        

        try {
            $stmt = $pdo->prepare("UPDATE alumnos SET apellidos = ?, nombres = ?, sexo = ?, estado_civil = ?, carrera = ?, reticula = ?, semestre_actual = ?, correo_institucional = ?, correo_personal = ?, telefono_contacto = ?, fecha_ingreso = ?, estatus_academico = ?, fecha_creacion = ?, fecha_actualizacion = ? WHERE n_control = ?");
            $stmt->execute([$apellidos, $nombres, $sexo, $estado_civil, $carrera, $reticula, $semestre_actual, $correo_institucional, $correo_personal, $telefono_contacto, $fecha_ingreso, $estatus_academico, $fecha_creacion, $fecha_actualizacion, $n_de_control]);
            echo "Alumno actualizado correctamente";
        } catch (PDOException $e) {
            echo "Error al actualizar alumno: " . $e->getMessage();
        }
    }
?>