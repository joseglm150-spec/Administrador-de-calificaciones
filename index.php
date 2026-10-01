<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Calificaciones</title>
    <!-- Vincula el archivo CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="login-container">
        <h2>Inicio de sesión</h2>

        <form>
            <div class="input-group">
                <!-- Nombre de usuario -->
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="Ingrese su nombre de usuario" required>
                
            </div>
            
            <!-- Contraseña -->
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                <!-- Checkbox para mostrar/ocultar contraseña -->
                <input type="checkbox" id="showPassword" name="showPassword">
            </div>

            <!-- Botón acceder -->
            <div class="checkbox-group">
                <button type="submit" class="btn-submit">Acceder</button>
            </div>    
            
        </form>    

            
    </div>
    <!-- Vincula el archivo JavaScript -->
    <script src="assets/js/login.js"></script>
</body>
</html>


