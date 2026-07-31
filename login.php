<?php
/**
 * Inicio de Sesión de Usuarios
 * Soluciones Informática JD
 */

require_once __DIR__ . '/config.php';

// Si ya inició sesión, redirigir según su rol
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

$error = '';
$success = '';

// Alerta de registro exitoso o cierre de sesión
if (isset($_GET['registered'])) {
    $success = '¡Registro completado con éxito! Por favor, inicie sesión.';
} elseif (isset($_GET['logout'])) {
    $success = 'Sesión cerrada correctamente.';
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_ingresado = sanitize($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usuario_ingresado) || empty($password)) {
        $error = 'Por favor, ingrese su usuario y contraseña.';
    } else {
        try {
            // Buscar por usuario o correo electrónico
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :val OR email = :val");
            $stmt->execute([':val' => $usuario_ingresado]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Generar nueva ID de sesión para prevenir Session Fixation
                session_regenerate_id(true);

                // Guardar datos en la sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_usuario'] = $user['usuario'];
                $_SESSION['usuario_rol'] = $user['rol'];

                // Redirigir según el rol del usuario
                if ($user['rol'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error = 'Nombre de usuario/correo o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            error_log("Error de BD en login: " . $e->getMessage());
            $error = 'Ocurrió un error en el servidor. Inténtelo más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Soluciones Informática JD</title>
    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="font-awesome.css">
    <!-- Estilos específicos y globales -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.php" class="auth-logo">
                    <img src="favicon_1.ico" alt="Logo">
                    <span>SOLUCIONES JD</span>
                </a>
                <h2>Bienvenido de Nuevo</h2>
                <p>Ingresa a tu portal de soporte informático</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="usuario"><i class="fa fa-user"></i> Usuario o Correo Electrónico</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Ej. jose_portillo o admin@correo.com" value="<?php echo isset($usuario_ingresado) ? $usuario_ingresado : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa fa-lock"></i> Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Tu contraseña de acceso" required>
                </div>

                <div class="recordar">
                    <a href="#" class="forgot-link">¿Olvidó su contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión <i class="fa fa-sign-in"></i>
                </button>
            </form>

            <div class="auth-footer">
                <p>¿No tienes una cuenta? <a href="register.php">Regístrate ahora</a></p>
                <a href="index.php" class="back-link"><i class="fa fa-arrow-left"></i> Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
