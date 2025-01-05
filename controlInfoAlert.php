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
    $ubicacion = $_POST['ubicacion'] ?? '';
    $situacion = $_POST['situacion'] ?? '';

    // Concatenar ubicación a situación
    $situacion = "Ubicacion: " . $ubicacion . ' - ' . $situacion;

    // Consulta segura usando una consulta preparada
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
