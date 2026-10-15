<?php
// 1. Llamar a tu archivo de conexión
require '../../config/conecction.php'; 

// IMPORTANTE: Asegúrate de que la variable de conexión dentro de 'conexion.php' 
// se llame $pdo. Si se llama $conexion, $conn u otra cosa, 
// cambia todos los $pdo que están abajo por el nombre de tu variable.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recoger los datos del formulario 
    
    $id_usuario = trim($_POST['id_usuario']);
    $rfc_ingresado = trim($_POST['rfc']); 
    $nombre_usuario = trim($_POST['nombre_usuario']); 
    $password_ingresada = $_POST['password']; 
    $rol = $_POST['rol']; 

    if (!empty($id_usuario) && !empty($rfc_ingresado) && !empty($nombre_usuario) && !empty($password_ingresada)) {
        
        // PASO 2: Verificar si el RFC existe en la tabla personal
        $stmt_check_rfc = $pdo->prepare("SELECT rfc FROM personal WHERE rfc = :rfc");
        $stmt_check_rfc->execute(['rfc' => $rfc_ingresado]);

        if ($stmt_check_rfc->rowCount() > 0) {
            
            // PASO 3: Hashear la contraseña ingresada
            $password_hash = password_hash($password_ingresada, PASSWORD_DEFAULT);

            // PASO 4: Insertar en la tabla usuarios
            $sql_insert = "INSERT INTO usuarios (id_usuario, rfc, nombre_usuario, password_hash, rol) 
                           VALUES (:id_usuario, :rfc, :nombre_usuario, :password, :rol)";
            $stmt_insert = $pdo->prepare($sql_insert);
            
            try {
                $stmt_insert->execute([
                    'id_usuario' => $id_usuario,
                    'rfc' => $rfc_ingresado,
                    'nombre_usuario' => $nombre_usuario,
                    'password' => $password_hash,
                    'rol' => $rol
                ]);

                // Éxito
                echo "<script>alert('¡Usuario creado exitosamente!'); window.location.href='../../views/layout/admin_usuarios.php';</script>";

            } catch (PDOException $e) {
                 echo "<script>alert('Error al crear usuario: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }

        } else {
            // El RFC no existe
            echo "<script>alert('Error: El RFC ingresado no está registrado en la plantilla de personal. No se puede crear el usuario.'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('Por favor, llena todos los campos.'); window.history.back();</script>";
    }
}
?>