<?php
    session_start();

    // Definir códigos de error
    define('ERR_CONN', 1); // No se puede conectar a la base de datos
    define('ERR_USER_NOT_FOUND', 2); // Usuario no encontrado

    // Capturar el parámetro de la URL
    $alerta = $_GET['alerta'] ?? 'desconocido';

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

    // Buscar ID del usuario por DNI
    $stmt_user_id = $mysqli->prepare("SELECT id FROM usuarios WHERE dni = ?");
    $stmt_user_id->bind_param('s', $dni);
    $stmt_user_id->execute();
    $result_user_id = $stmt_user_id->get_result();

    // Verificar si se encontró el usuario
    if ($result_user_id->num_rows === 0) {
        header('Location: controlLogin.php?error=' . ERR_USER_NOT_FOUND);
        $stmt_user_id->close();
        $mysqli->close();
        exit;
    }

    // Obtener el ID del usuario
    $user = $result_user_id->fetch_assoc();
    $user_id = $user['id'];
    $stmt_user_id->close();

    // Obtener datos del usuario
    $stmt_user_data = $mysqli->prepare("SELECT nombre, apellido1, apellido2, dni, telefono, calle, numero, portal_escalera_piso FROM usuarios WHERE id = ?");
    $stmt_user_data->bind_param('i', $user_id);
    $stmt_user_data->execute();
    $result_user_data = $stmt_user_data->get_result();

    // Comprobar si hay datos
    if ($result_user_data->num_rows > 0) {
        $user = $result_user_data->fetch_assoc();
    } else {
        $user = null;
    }
    $stmt_user_data->close();

    // Obtener contactos del usuario
    $stmt_contacts = $mysqli->prepare("SELECT contacto_id FROM usuario_contacto WHERE usuario_id = ?");
    $stmt_contacts->bind_param('i', $user_id);
    $stmt_contacts->execute();
    $result_contacts = $stmt_contacts->get_result();
    $contactos = [];

    // Verificar si hay contactos
    if ($result_contacts->num_rows > 0) {
        while ($contacto = $result_contacts->fetch_assoc()) {
            // Obtener el nombre del usuario relacionado al contacto_id
            $stmt_contact_name = $mysqli->prepare("SELECT nombre FROM usuarios WHERE id = ?");
            $stmt_contact_name->bind_param('i', $contacto['contacto_id']);
            $stmt_contact_name->execute();
            $result_contact_name = $stmt_contact_name->get_result();

            if ($result_contact_name->num_rows > 0) {
                $contact = $result_contact_name->fetch_assoc();
                $contactos[] = $contact['nombre'];
            }
            $stmt_contact_name->close();
        }
    }
    $stmt_contacts->close();

    // Cerrar conexiones
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
                value="<?php 
                    echo isset($user) 
                        ? trim(
                            (isset($user['calle']) ? $user['calle'] : '') . ' ' .
                            (isset($user['numero']) ? $user['numero'] : '') . ', ' .
                            (isset($user['portal_escalera_piso']) ? $user['portal_escalera_piso'] : '')
                        , ' ,') 
                        : ''; ?>" 
                required>
            </div>
        </div>

        <div class="form-group">
            <label for="contactos">Seleccionar contactos</label>
            <select id="contactos" name="contactos">
                <option value="">Seleccione una opción</option>
                <?php foreach ($contactos as $nombre): ?>
                    <option value="<?php echo htmlspecialchars($nombre); ?>">
                        <?php echo htmlspecialchars($nombre); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="situacion">Situación</label>
            <textarea id="situacion" name="situacion" rows="5" placeholder="Explique brevemente el motivo de la alerta"required></textarea>
            <textarea id="situacion" name="situacion" rows="5" default=$alerta required><?php echo htmlspecialchars($alerta); ?></textarea>
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

