<?php
// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "helpwave_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que todos los campos están presentes y no están vacíos
    $campos = ['nombre', 'apellido1', 'apellido2', 'dni', 'telefono', 'calle', 'numero', 'portal', 'contraseña', 'reptContraseña'];
    $errores = [];

    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            $errores[] = "El campo $campo es obligatorio.";
        }
    }

    if (empty($errores)) {
        // Recoger los datos y escapar
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $dni = $_POST['dni'];
        $telefono = $_POST['telefono'];
        $calle = $_POST['calle'];
        $numero = $_POST['numero'];
        $portal = $_POST['portal'];
        $contraseña = $_POST['contraseña'];
        $reptContraseña = $_POST['reptContraseña'];

        // Validar que las contraseñas coincidan
        if ($contraseña === $reptContraseña) {
            // Encriptar la contraseña
            $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

            // Preparar la consulta SQL
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido1, apellido2, dni, telefono, calle, numero, portal_escalera_piso, contraseña) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $nombre, $apellido1, $apellido2, $dni, $telefono, $calle, $numero, $portal, $contraseña_hash);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "✅ Registro exitoso.";
                header('Location: login.php');
            } else {
                echo "❌ Error al registrar: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "❌ Las contraseñas no coinciden.";
        }
    } else {
        foreach ($errores as $error) {
            echo "❌ $error<br>";
        }
    }
}

// Cerrar conexión
$conn->close();

include 'registro.html';
?>
