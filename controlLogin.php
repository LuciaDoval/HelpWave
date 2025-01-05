<?php
// Definir códigos de error
define('ERR_CONN', 1); // No se puede conectar a la base de datos
define('ERR_USUARIO', 2); // Usuario o contraseña incorrectos

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
$dni = $_POST['id'] ?? '';
$password = $_POST['pass'] ?? '';

// Verificar si los campos están vacíos
if (empty($dni) || empty($password)) {
    header('Location: login.php?error=' . ERR_USUARIO);
    $mysqli->close(); 
    exit;
}

// Consulta segura usando una consulta preparada
$stmt = $mysqli->prepare('SELECT dni, nombre, apellido1, apellido2, contraseña FROM usuarios WHERE dni = ?');
$stmt->bind_param('s', $dni); // 's' indica que es un string
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró el usuario
if ($result->num_rows > 0) {
    $object = $result->fetch_object();
    $usuario_id = $object->dni;
    $nombre = $object->nombre . ' ' . $object->apellido1 . ' ' . $object->apellido2;
    $pass_hash = $object->contraseña;
} else {
    // Si no se encuentra el usuario
    header('Location: login.php?error=' . ERR_USUARIO);
    $mysqli->close(); 
    exit;
}

// Validar la contraseña (usando hash)
if ($password == $pass_hash) {
    session_start();
    $_SESSION['dni'] = $usuario_id; // Usamos el ID del usuario para la sesión
    $_SESSION['nombre'] = $nombre;
    header('Location: principal.html'); // Redirigir al área protegida
    $mysqli->close(); 
    exit;
} else {
    // Si la contraseña es incorrecta
    header('Location: login.php?error=' . ERR_USUARIO);
    $mysqli->close(); 
    exit;
}
?>
