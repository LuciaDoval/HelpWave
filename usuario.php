<?php
    session_start();

    // Definir códigos de error
    define('ERR_CONN', 1); // No se puede conectar a la base de datos
    define('ERR_USER_NOT_FOUND', 2); // Usuario no encontrado

    // Verificar si el DNI está en la sesión
    if (!isset($_SESSION['dni'])) {
        die('No hay un DNI en la sesión.');
    }

    // Recuperrar DNI
    $dni = $_SESSION['dni'];

    // Conexión a la base de datos
    $mysqli = new mysqli('localhost', 'root', '', 'helpwave_db');
    $mysqli->set_charset('utf8');

    // Verificar conexión
    if ($mysqli->connect_errno) {
        header('Location: controlLogin.php?error=' . ERR_CONN);
        $mysqli->close(); 
        exit;
    }

    // Obtener datos del usuario
    $stmt = $mysqli->prepare("SELECT nombre, apellido1, apellido2, dni, telefono, calle, numero, portal_escalera_piso, contraseña FROM usuarios WHERE dni = ?");
    $stmt->bind_param('s', $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si hay datos
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $user = null;
    }

    // Cerrar conexiones
    $stmt->close();
    $mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpWave</title>
    <meta name="author" content="Victor Bellod, Vidhi Sharma, Sergio Maza & Lucía Doval">
    <meta name="description" content="Web developer working with HTML5, CSS3 and JavaScript">
    <link rel="icon" type="image/x-icon" href="media/images/logo.png">
    <link rel="stylesheet" href="css/styles.css" type="text/css">
</head>
<body>
<div class="header">
    <a href = "principal.html">
        <div class="logo">
            <img src="media/images/logo.png" alt="Emergency Logo">
            
            <span>HELPWAVE</span>
        </div>
        </a>
    <div class="menu">
        <a href="usuario.html" class="user-link">
            <i class="user-icon">👤</i>
        </a>
        <i class="menu-icon" onclick="toggleMenu()">☰</i>
        <div class="menu-dropdown" id="menuDropdown">
            <a href="principal.html">Principal</a>
            <a href="contactos.html">Contactos</a>
        </div>
    </div>
</div>

<div class="form-container">
    <h1>Datos de usuario:</h1>
    <form action="controlUsuario.php" method="POST" id="usuario">
        <div class ="form-row">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo isset($user['nombre']) ? $user['nombre'] : ''; ?>" disabled class="disabled">
            </div>
            <br>
            <div class="form-group">
                <label for="apellido1">Apellido 1</label>
                <input type="text" id="apellido1" name="apellido1" value="<?php echo isset($user['apellido1']) ? $user['apellido1'] : ''; ?>" disabled class="disabled">
            </div>
            <br>
            <div class="form-group">
                <label for="apellido2">Apellido 2</label>
                <input type="text" id="apellido2" name="apellido2" value="<?php echo isset($user['apellido2']) ? $user['apellido2'] : ''; ?>" disabled class="disabled">
            </div>
        </div>
        <br>
        <div class="form-row">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" value="<?php echo isset($user['dni']) ? $user['dni'] : ''; ?>" disabled class="disabled">
            </div>
            <br>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo isset($user['telefono']) ? $user['telefono'] : ''; ?>">
            </div>
        </div>
        <br>
        <div class="form-row">
            <div class="form-group">
                <label for="calle">Calle</label>
                <input type="text" id="calle" name="calle" value="<?php echo isset($user['calle']) ? $user['calle'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="numero">N°</label>
                <input type="text" id="numero" name="numero" value="<?php echo isset($user['numero']) ? $user['numero'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="portal">Portal, Escalera, Piso</label>
                <input type="text" id="portal" name="portal" value="<?php echo isset($user['portal_escalera_piso']) ? $user['portal_escalera_piso'] : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="contraseña">Contraseña</label>
                <input type="text" id="contraseña" name="contraseña" value="<?php echo isset($user['contraseña']) ? $user['contraseña'] : ''; ?>">
            </div>
        </div>

        <button type="submit" class="submit-button">Confirmar Cambios</button>
    </form>
</div>

<script>
    // Función para mostrar u ocultar el menú desplegable
    function toggleMenu() {
        const menuDropdown = document.getElementById("menuDropdown");
        if (menuDropdown.style.display === "none" || menuDropdown.style.display === "") {
            menuDropdown.style.display = "block";
        } else {
            menuDropdown.style.display = "none";
        }
    }

    // Cerrar el menú si se hace clic fuera de él
    window.onclick = function(event) {
        const menuDropdown = document.getElementById("menuDropdown");
        if (!event.target.matches('.menu-icon') && !event.target.matches('.menu-dropdown') && !event.target.closest('.menu-dropdown')) {
            if (menuDropdown.style.display === "block") {
                menuDropdown.style.display = "none";
            }
        }
    };
</script>

</body>
</html>