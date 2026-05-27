<?php
/**
 * Cierre de Sesión Seguro
 * Soluciones Informática JD
 */

require_once __DIR__ . '/config.php';

// Limpiar todas las variables de sesión
$_SESSION = [];

// Si se desea destruir la sesión por completo, borrar también la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión física en el servidor
session_destroy();

// Redirigir con confirmación de salida
header("Location: login.php?logout=1");
exit;
?>
