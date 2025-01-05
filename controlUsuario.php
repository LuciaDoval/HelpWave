<?php
    session_start();

    // Definir códigos de error
    define('ERR_CONN', 1); // No se puede conectar a la base de datos

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

    // Recuperar variables
    $telefono = $_POST['telefono'];
    $calle = $_POST['calle'] ?? '';
    $numero = $_POST['numero'] ?? ''; 
    $portal = $_POST['portal'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    // Actualizar datos del usuario
    $stmt = $mysqli->prepare("UPDATE usuarios SET telefono = ?, calle = ?, numero = ?, portal_escalera_piso = ?, contraseña = ? WHERE dni = ?");
    $stmt->bind_param('ssisss', $telefono, $calle, $numero, $portal, $contraseña, $dni);
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "✅ Usuario Actualizado.";
        header('Location: principal.html');
    } else {
        echo "❌ Error al actualizar el usuario: " . $stmt->error;
    }

    // Cerrar conexiones
    $stmt->close();
    $mysqli->close();
?>
