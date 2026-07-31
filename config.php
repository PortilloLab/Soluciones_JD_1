<?php
/**
 * Configuración global y Conexión a la Base de Datos
 * Soluciones Informática JD
 */

// Iniciar sesión de forma segura si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Establecer parámetros de cookies de sesión seguros
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    // Si estás bajo HTTPS, descomenta la siguiente línea:
    // ini_set('session.cookie_secure', 1);
    
    session_start();
}

// Configuración de la Base de Datos PostgreSQL (Estándar)
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'soluciones_jd');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres'); // Cambia esto por tu contraseña real

// Conexión PDO a PostgreSQL
try {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // En producción, no mostrar detalles del error para evitar fugas de información.
    // Guardamos en el log y mostramos un mensaje amigable.
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Lo sentimos, ha ocurrido un error al conectar con la base de datos. Por favor, verifica que PostgreSQL esté iniciado y las credenciales en config.php sean correctas.");
}

// Configuración de Correo SMTP (Para mail_helper.php)
define('SMTP_HOST', 'smtp.gmail.com');       // Cambiar por tu servidor SMTP
define('SMTP_PORT', 587);                    // Puerto SMTP (usualmente 587 para TLS o 465 para SSL)
define('SMTP_USER', 'tu_correo@gmail.com');   // Tu correo para autenticar
define('SMTP_PASS', 'tu_contraseña_app');   // Tu contraseña o clave de aplicación
define('SMTP_FROM', 'tu_correo@gmail.com');   // Correo desde el que se envía
define('SMTP_FROM_NAME', 'Soluciones Informática JD');
define('SMTP_TO', 'jsdnlportillo@gmail.com');  // Correo de destino del administrador

/**
 * Sanitiza las entradas del usuario para evitar XSS
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica si el usuario actual ha iniciado sesión
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Verifica si el usuario actual tiene rol de administrador
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Redirecciona al login si el usuario no ha iniciado sesión
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Redirecciona al index si el usuario no es administrador
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: dashboard.php");
        exit;
    }
}
?>
