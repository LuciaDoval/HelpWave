<?php
    // Definir códigos de error
    define('ERR_CONN', 1); // No se puede conectar a la base de datos

    // Conexión a la base de datos
    $mysqli = new mysqli('localhost', 'root', '', 'helpwave_db'); 
    $mysqli->set_charset('utf8');

    // Verificar conexión
    if ($mysqli->connect_errno) {
        header('Location: controlLogin.php?error=' . ERR_CONN);
        $mysqli->close(); 
        exit;
    }

    // Validar entradas
    $id = $_POST['id'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $situacion = $_POST['situacion'] ?? '';

    // Concatenar ubicación a situación
    $situacion = "Ubicacion: " . $ubicacion . ' - ' . $situacion;

    // Autocompletar datos
    $query = "SELECT nombre, apellido1, apellido2, dni, telefono, calle, numero, portal_escalera_piso FROM usuarios WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        // Obtener los datos del usuario
        $user = $result->fetch_assoc();
    } else {
        // Si no se encuentra el usuario, manejar el caso (opcional)
        echo "No se encontró el usuario.";
    }

    // Guardar Alerta
    $stmt = $mysqli->prepare("INSERT INTO alertas (situacion) VALUES (?)");
    $stmt->bind_param('s', $ubicacion);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "✅ Alerta Enviada.";
        header('Location: principal.html');
    } else {
        echo "❌ Error al enviar la alerta: " . $stmt->error;
    }
?>
