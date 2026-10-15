<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Calificaciones</title>
    <!-- Vincula el archivo CSS -->
    <link rel="stylesheet" href="../../assets/css/login.css">
    
</head>

<body>
    <!-- Barra superior con los logos -->
    <header class="top-bar">
        <h2>AGREGAR USUARIO</h2>
    </header>

    <!-- Contenedor del formulario de inicio de sesión -->
    <div class="login-container">
        <img src="../../assets/img/SIRCA.jpeg" alt="Logo de el sistema" class="logo-SIRCA">

        <form action="../../controllers/docentes/registrar_docente.php" method="POST">
        <!-- id -->
            <div class="input-group">
                <label for="id_usuario">ID de usuario</label>
                <input type="text" id="id_usuario" name="id_usuario" placeholder="Ingrese el ID de usuario" required>              
            </div>    
        
        <!-- Nombre de usuario -->
            <div class="input-group">
                <label for="rfc">RFC</label>
                <input type="text" id="rfc" name="rfc" placeholder="Ingrese su RFC" required>              
            </div>
            
            <!-- Nombre de usuario -->
            <div class="input-group">
                <label for="nombre_usuario">Nombre de usuario</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Ingrese su nombre de usuario" required>
            </div>

            <!-- Contraseña -->
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                
            </div>

            <!-- Rol -->
            <div class="input-group">
                <label for="rol">Rol</label>
                <select id="rol" name="rol" required>
                    <option value="DOCENTE">DOCENTE</option>
                    <option value="administrador">Administrador</option>
                    <option value="jefe_de_división">Jefe de División</option>

                </select>
            </div>

            <!-- Botón dar de alta -->
            <div class="checkbox-group">
                <button type="submit" class="btn-submit">Dar de Alta</button>
            </div> 
        </form>

        

        <p>Copyright © 2026 SIRCA - Todos los Derechos Reservados</p>

            
    </div>
</body>
</html>



