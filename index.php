<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Calificaciones</title>
    <!-- Vincula el archivo CSS -->
    <link rel="stylesheet" href="assets/css/login.css">
    
</head>

<body>
    <!-- Barra superior con los logos -->
    <header class="top-bar">
        <h2>INICIAR SESIÓN</h2>
    </header>

    <!-- Contenedor del formulario de inicio de sesión -->
    <div class="login-container">
        <img src="assets/img/SIRCA.jpeg" alt="Logo de el sistema" class="logo-SIRCA">

        <form>
            <!-- Nombre de usuario -->
            <div class="input-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="Ingrese su nombre de usuario" required>              
            </div>
            
            <!-- Contraseña -->
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                
            </div>

            <!-- Botón acceder -->
            <div class="checkbox-group">
                <button type="submit" class="btn-submit">Acceder</button>
            </div> 
        </form>

        <nav class="footer-links" aria-label="Opciones de acceso">
            <a class="recovery-link" href="recuperar_contrasena.php">¿Olvidaste tu contraseña?</a>
            <a class="temporary-menu-link" href="/administrador-de-calificaciones/views/layout/menu.php">Entrar al menú temporalmente</a>
        </nav>

        <p>Copyright © 2026 SIRCA - Todos los Derechos Reservados</p>

            
    </div>
    
</body>
</html>

