<?php
    // Definir códigos de error
    define('ERR_CONN', 1); // No se puede conectar a la base de datos
    define('ERR_USER_NOT_FOUND', 2); // Usuario no encontrado
    define('ERR_INSERT', 3); // Error al insertar en la base de datos

    // Conexión a la base de datos
    $mysqli = new mysqli('localhost', 'root', '', 'helpwave_db');
    $mysqli->set_charset('utf8');

    // Verificar conexión
    if ($mysqli->connect_errno) {
        die('Error de conexión: ' . $mysqli->connect_error);
    }

    // Recuperar variables del formulario
    $dni = $_POST['dni'] ?? '';
    $relacion = $_POST['relacion'] ?? '';
    $otraRelacion = $_POST['otra'] ?? '';

    // Usar "otraRelacion" si "relacion" es "otra"
    if ($relacion === 'otra' && !empty($otraRelacion)) {
        $relacion = $otraRelacion;
    }

    // Validar datos
    if (empty($dni) || empty($relacion)) {
        die('Error: Datos incompletos. Por favor, rellene todos los campos.');
    }

    // 1. Verificar si el usuario con ese DNI existe
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE dni = ?");
    if (!$stmt) {
        die('Error al preparar la consulta para obtener el usuario: ' . $mysqli->error);
    }

    $stmt->bind_param('s', $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el usuario
    if ($result->num_rows === 0) {
        die('Error: Usuario con DNI no encontrado.');
    }

    // Obtener el ID del contacto (usuario relacionado)
    $user = $result->fetch_assoc();
    $contacto_id = $user['id']; // El ID del usuario relacionado

    // 2. Verificar si el usuario actual está en la sesión
    session_start();
    if (!isset($_SESSION['usuario'])) {
        die('Error: Sesión no iniciada.');
    }

    $usuario_actual = $_SESSION['usuario'];
    $query = "SELECT id FROM usuarios WHERE username = ?";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die('Error al preparar la consulta para obtener el usuario actual: ' . $mysqli->error);
    }

    $stmt->bind_param('s', $usuario_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró al usuario actual
    if ($result->num_rows === 0) {
        die('Error: Usuario actual no encontrado.');
    }

    // Obtener el ID del usuario actual
    $current_user = $result->fetch_assoc();
    $usuario_id = $current_user['id'];

    // 3. Comprobar los valores obtenidos
    echo "Usuario actual ID: " . $usuario_id . "<br>";
    echo "Contacto ID (por DNI): " . $contacto_id . "<br>";
    echo "Relación: " . $relacion . "<br>";

    // 4. Preparar consulta SQL para insertar los datos en la tabla usuario_contacto
    $stmt = $mysqli->prepare("INSERT INTO usuario_contacto (usuario_id, contacto_id, relacion) VALUES (?, ?, ?)");
    if (!$stmt) {
        die('Error al preparar la consulta de inserción: ' . $mysqli->error);
    }

    $stmt->bind_param('iis', $usuario_id, $contacto_id, $relacion);

    // Ejecutar consulta
    if ($stmt->execute()) {
        echo 'Datos insertados correctamente.';
        header('Location: principal.html?success=1');
    } else {
        // Mostrar el error de la consulta SQL
        die('Error al insertar los datos: ' . $stmt->error);
    }

    // Cerrar conexión
    $stmt->close();
    $mysqli->close();
?>
