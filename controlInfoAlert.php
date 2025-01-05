<?php
    // Definir códigos de error
    define('ERR_CONN', 1); // No se puede conectar a la base de datos
    define('ERR_USER_NOT_FOUND', 2); // Usuario no encontrado

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
    $dni = $_POST['dni'];
    $ubicacion = $_POST['ubicacion'] ?? '';
    $situacion = $_POST['situacion'] ?? '';

    // Concatenar ubicación a situación
    $situacion = "Ubicacion: " . $ubicacion . ' - ' . $situacion;

    // Buscar ID del usuario por DNI
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE dni = ?");
    $stmt->bind_param('s', $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el usuario
    if ($result->num_rows === 0) {
        header('Location: controlLogin.php?error=' . ERR_USER_NOT_FOUND);
        $stmt->close();
        $mysqli->close();
        exit;
    }

    // Obtener el ID del usuario
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    
    // Guardar Alerta
    $stmt = $mysqli->prepare("INSERT INTO alertas (situacion, usuario_id) VALUES (?, ?)");
    $stmt->bind_param('si', $situacion, $user_id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "✅ Alerta Enviada.";
        header('Location: principal.html');
    } else {
        echo "❌ Error al enviar la alerta: " . $stmt->error;
    }

    // Cerrar conexiones
    $stmt->close();
    $mysqli->close();
?>
