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
    $ubicacion = $_POST['relacion'] ?? '';
    $situacion = $_POST['otra'] ?? '';

    // Usar "otraRelacion" si "relacion" es "otra"
    if ($relacion === 'otra' && !empty($otraRelacion)) {
        $relacion = $otraRelacion;
    }

    // Validar datos
    if (empty($dni) || empty($relacion)) {
        header('Location: contactos.html?error=Datos incompletos');
        $mysqli->close();
        exit;
    }


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

    // Preparar consulta SQL para insertar los datos en la tabla usuario_contacto
    $stmt = $mysqli->prepare("INSERT INTO usuario_contacto (usuario_id, contacto_id, relacion) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('iis', $usuario_id, $dni, $relacion);

        // Ejecutar consulta
        if ($stmt->execute()) {
            // Redirigir al usuario a la página principal con éxito
            header('Location: principal.html?success=1');
        } else {
            // Error al insertar
            header('Location: contactos.html?error=' . ERR_INSERT);
        }

        $stmt->close();
    } else {
        // Error al preparar la consulta
        header('Location: contactos.html?error=' . ERR_INSERT);
    }

    // Cerrar conexión
    $mysqli->close();
?>
