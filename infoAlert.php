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
    <form action="controlInfoAlert.php" method="POST" id="formulario">
        <div class="form-group">
            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni" value="<?php echo isset($user['dni']) ? $user['dni'] : ''; ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo isset($user['nombre']) ? $user['nombre'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido1">Apellido 1</label>
                <input type="text" id="apellido1" name="apellido1" value="<?php echo isset($user['apellido1']) ? $user['apellido1'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido2">Apellido 2</label>
                <input type="text" id="apellido2" name="apellido2" value="<?php echo isset($user['apellido2']) ? $user['apellido2'] : ''; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion" 
                value="<?php echo isset($user['calle'], $user['numero'], $user['portal_escalera_piso']) ? $user['calle']
                . ' ' . $user['numero'] . ', ' . $user['portal_escalera_piso'] : ''; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="contactos">Seleccionar contactos</label>
            <select id="contactos" name="contactos">
                <option value="">Seleccione una opción</option>
                <option value="contacto1">Contacto 1</option>
                <option value="contacto2">Contacto 2</option>
            </select>
        </div>

        <div class="form-group">
            <label for="situacion">Situación</label>
            <textarea id="situacion" name="situacion" rows="5" required></textarea>
        </div>
        <br><br>
        <button type="submit" class="submit-button">Enviar</button>
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

