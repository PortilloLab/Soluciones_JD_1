<?php
/**
 * Script CLI Seguro para Creación de Usuario Administrador
 * Soluciones Informática JD & PortilloLab
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>Este script solo puede ejecutarse desde la línea de comandos (CLI).</p>";
    exit(1);
}

require_once __DIR__ . '/config.php';

echo "============================================================" . PHP_EOL;
echo "   🔑 CREACIÓN SEGURA DE ADMINISTRADOR - SOLUCIONES JD" . PHP_EOL;
echo "============================================================" . PHP_EOL;

function prompt_input($label, $required = true) {
    do {
        echo $label . ": ";
        $input = trim(fgets(STDIN));
        if ($required && empty($input)) {
            echo "   ⚠️  Este campo no puede estar vacío. Intente nuevamente." . PHP_EOL;
        }
    } while ($required && empty($input));
    return $input;
}

$nombre = prompt_input("Nombre Completo del Administrador");
$email = prompt_input("Correo Electrónico");

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Error: El correo electrónico ingresado no es válido." . PHP_EOL;
    exit(1);
}

$usuario = prompt_input("Nombre de Usuario (Login)");

do {
    echo "Contraseña (mínimo 8 caracteres): ";
    system('stty -echo 2>/dev/null');
    $password = trim(fgets(STDIN));
    system('stty echo 2>/dev/null');
    echo PHP_EOL;

    if (strlen($password) < 8) {
        echo "   ⚠️  La contraseña debe tener al menos 8 caracteres." . PHP_EOL;
        continue;
    }

    echo "Confirmar Contraseña: ";
    system('stty -echo 2>/dev/null');
    $confirm = trim(fgets(STDIN));
    system('stty echo 2>/dev/null');
    echo PHP_EOL;

    if ($password !== $confirm) {
        echo "   ⚠️  Las contraseñas no coinciden. Intente nuevamente." . PHP_EOL;
    }
} while (strlen($password) < 8 || $password !== $confirm);

$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, usuario, password_hash, rol) VALUES (:nombre, :email, :usuario, :hash, 'admin')");
    $stmt->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':usuario' => $usuario,
        ':hash' => $hash
    ]);

    echo "============================================================" . PHP_EOL;
    echo " ✅ Administrador creado con éxito!" . PHP_EOL;
    echo " Usuario: $usuario ($email)" . PHP_EOL;
    echo "============================================================" . PHP_EOL;

} catch (PDOException $e) {
    echo "❌ Error al crear administrador: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
?>
