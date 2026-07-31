<?php
/**
 * Registro de Nuevos Clientes
 * Soluciones Informática JD
 */

require_once __DIR__ . '/config.php';

// Si ya inició sesión, redirigir al dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitize($_POST['nombre'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $usuario = sanitize($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($email) || empty($usuario) || empty($password) || empty($password_confirm)) {
        $error = 'Por favor, complete todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico ingresado no es válido.';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            // Verificar si el usuario o email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario OR email = :email");
            $stmt->execute([':usuario' => $usuario, ':email' => $email]);
            
            if ($stmt->fetch()) {
                $error = 'El nombre de usuario o correo electrónico ya está registrado.';
            } else {
                // Registrar el usuario en la BD (rol por defecto: cliente)
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, usuario, password_hash, rol) VALUES (:nombre, :email, :usuario, :password_hash, 'cliente')");
                
                if ($stmt->execute([
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':usuario' => $usuario,
                    ':password_hash' => $password_hash
                ])) {
                    // Redirigir a inicio de sesión
                    header("Location: login.php?registered=1");
                    exit;
                } else {
                    $error = 'Ocurrió un problema al registrar su cuenta. Inténtelo de nuevo.';
                }
            }
        } catch (PDOException $e) {
            error_log("Error de BD en registro: " . $e->getMessage());
            $error = 'Error de conexión con el servidor. Inténtelo más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Soluciones Informática JD</title>
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
                <h2>Crear una Cuenta</h2>
                <p>Únete a nuestra plataforma de soporte inmediato</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nombre"><i class="fa fa-user"></i> Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. José Daniel Portillo" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fa fa-envelope"></i> Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="Ej. cliente@correo.com" value="<?php echo isset($email) ? $email : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="usuario"><i class="fa fa-id-badge"></i> Nombre de Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Ej. jose_portillo" value="<?php echo isset($usuario) ? $usuario : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa fa-lock"></i> Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm"><i class="fa fa-lock"></i> Confirmar Contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Repite tu contraseña" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Registrarse <i class="fa fa-user-plus"></i>
                </button>
            </form>

            <div class="auth-footer">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión</a></p>
                <a href="index.php" class="back-link"><i class="fa fa-arrow-left"></i> Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
