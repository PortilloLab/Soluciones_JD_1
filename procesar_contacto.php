<?php
/**
 * Procesador del Formulario de Contacto
 * Soluciones Informática JD & PortilloLab
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail_helper.php';

// Validar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método de petición no permitido.'
    ]);
    exit;
}

// Obtener y sanitizar entradas
$nombre = isset($_POST['nombre']) ? sanitize($_POST['nombre']) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$servicio = isset($_POST['servicio']) ? sanitize($_POST['servicio']) : 'Consulta General';
$mensaje = isset($_POST['mensaje']) ? sanitize($_POST['mensaje']) : '';

// Validaciones del lado del servidor
if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, complete todos los campos obligatorios (Nombre, Email y Mensaje).'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'El correo electrónico ingresado no es válido.'
    ]);
    exit;
}

try {
    // 1. Guardar en la Base de Datos (con columna servicio)
    $stmt = $pdo->prepare("INSERT INTO mensajes_contacto (nombre, email, servicio, mensaje) VALUES (:nombre, :email, :servicio, :mensaje)");
    $stmt->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':servicio' => $servicio,
        ':mensaje' => $mensaje
    ]);

    // 2. Construir y enviar el correo de notificación al administrador
    $asunto = "Nuevo mensaje de contacto ($servicio): $nombre";
    $cuerpo_html = "
    <html>
    <head>
        <title>Nuevo Contacto en Soluciones Informática JD</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 600px; background-color: #f9f9f9; }
            .header { background-color: #1e293b; color: #fff; padding: 15px; border-radius: 6px 6px 0 0; text-align: center; }
            .content { padding: 20px; background-color: #fff; border-radius: 0 0 6px 6px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #4f46e5; }
            .text { white-space: pre-wrap; background: #f1f5f9; padding: 10px; border-radius: 4px; border-left: 4px solid #4f46e5; }
            .footer { margin-top: 20px; font-size: 11px; color: #777; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Mensaje Recibido</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Nombre:</span> <span>$nombre</span>
                </div>
                <div class='field'>
                    <span class='label'>Correo Electrónico:</span> <span>$email</span>
                </div>
                <div class='field'>
                    <span class='label'>Servicio de Interés:</span> <span>$servicio</span>
                </div>
                <div class='field'>
                    <span class='label'>Fecha:</span> <span>" . date('d/m/Y H:i:s') . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Mensaje:</span>
                    <div class='text'>$mensaje</div>
                </div>
            </div>
            <div class='footer'>
                <p>Este correo ha sido generado de manera automática desde el formulario de contacto web.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Enviar el correo usando el mail_helper
    $email_result = send_email(SMTP_TO, $asunto, $cuerpo_html, $email, $nombre);

    $msg = 'Tu mensaje ha sido enviado correctamente. Guardado en base de datos.';
    if (isset($email_result['mode']) && $email_result['mode'] === 'mock') {
        $msg .= ' (' . $email_result['message'] . ')';
    }

    echo json_encode([
        'success' => true,
        'message' => $msg
    ]);

} catch (PDOException $e) {
    error_log("Error de BD en formulario de contacto: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Hubo un error interno al guardar tu mensaje en la base de datos.'
    ]);
}
?>
