
// Seleccionamos los dos elementos por su atributo ID
const inputPassword = document.getElementById('password');
const checkboxMostrar = document.getElementById('showPassword');

// Escuchamos cada vez que el checkbox cambie de estado
checkboxMostrar.addEventListener('change', function() {
    // Si el checkbox está marcado (true), cambiamos el type a "text"
    if (this.checked) {
        inputPassword.type = 'text';
    } else {
        // Si se desmarca (false), lo regresamos a "password"
        inputPassword.type = 'password';
    }
});
