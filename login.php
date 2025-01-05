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
    <div class="logo">
        <img src="media/images/logo.png" alt="Emergency Logo">
        <span>HELPWAVE</span>
    </div>
</div>

<div class="form-container">
    <h1>Log In:</h1>

    <!-- Formulario de login -->
    <form action="controlLogin.php" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="id" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="contraseña">Contraseña</label>
                <input type="password" id="contraseña" name="pass" required>
            </div>
        </div>

    <!-- Mostrar error si existe -->
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <?php
                $error_code = $_GET['error'];
                if ($error_code == 1) {
                    echo "Error de conexión a la base de datos.";
                } elseif ($error_code == 2) {
                    echo "Usuario o contraseña incorrectos.";
                }
            ?>
        </div>
    <?php endif; ?>

        <div class="form-row">
            <button type="submit" class="submit-button">Iniciar sesión</button>
        </div>

        <div class="form-row">
            <a href="registro.html">
                <button type="button" class="submit-button">No tengo cuenta</button>
            </a>
        </div>
        
    </form>
</div>
</body>
</html>

