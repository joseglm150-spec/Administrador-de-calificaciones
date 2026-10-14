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
            $stmt = $pdo->prepare("INSERT INTO alumnos (n_control, apellidos, nombres, sexo, estado_civil, carrera, reticula, semestre_actual, correo_institucional, correo_personal, telefono_contacto, fecha_ingreso, estatus_academico, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$n_de_control, $apellidos, $nombres, $sexo, $estado_civil, $carrera, $reticula, $semestre_actual, $correo_institucional, $correo_personal, $telefono_contacto, $fecha_ingreso, $estatus_academico, $fecha_creacion, $fecha_actualizacion]);
            echo "Alumno agregado correctamente";
        } catch (PDOException $e) {
            echo "Error al agregar alumno: " . $e->getMessage();
        }
    }
?>