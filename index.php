<!DOCTYPE html>
<html lang="en">
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
            
            <!-- boton recordarme -->
            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Recordarme</label>
            </div>

            <!-- Enlace para recuperar contraseña -->
            <div class="checkbox-group">
                <a href="recuperar_contrasena.php">¿Olvidaste tu contraseña?</a>
            </div>

            <!-- Entrar en el menu temporal -->
            <div class="checkbox-group">
                <a href="menu.php">Entrar al menú</a>
                
            </div>
        </form>    
        <p>Copyright © 2026 TecNM Campus Puruándiro - Todos los Derechos Reservados</p>

            
    </div>
    
</body>
</html>


