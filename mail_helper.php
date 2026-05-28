<?php
/**
 * Ayudante de Correo Electrónico (SMTP Autocontenido)
 * Soluciones Informática JD
 */

require_once __DIR__ . '/config.php';

/**
 * Envía un correo electrónico de forma real mediante SMTP o simula su envío si no está configurado.
 * 
 * @param string $to Correo del destinatario
 * @param string $subject Asunto del correo
 * @param string $message_html Contenido del mensaje en formato HTML
 * @param string|null $from_email Correo del remitente (opcional)
 * @param string|null $from_name Nombre del remitente (opcional)
 * @return array Array con estado de éxito y detalles del envío
 */
function send_email($to, $subject, $message_html, $from_email = null, $from_name = null) {
    $from = $from_email ?? SMTP_FROM;
    $name = $from_name ?? SMTP_FROM_NAME;

    // SISTEMA FALLBACK: Si no se han configurado credenciales reales, guardamos en un archivo de log local
    if (SMTP_USER === 'tu_correo@gmail.com' || empty(SMTP_USER) || strpos(SMTP_USER, '@') === false) {
        $log_file = __DIR__ . '/sent_emails.log';
        $boundary = md5(uniqid(time()));
        
        $log_content = "============================================================\n" .
                       "📧 CORREO ENVIADO (SIMULACIÓN LOCAL)\n" .
                       "Fecha: " . date('Y-m-d H:i:s') . "\n" .
                       "Para: $to\n" .
                       "De: $name <$from>\n" .
                       "Asunto: $subject\n" .
                       "------------------------------------------------------------\n" .
                       "Mensaje HTML:\n$message_html\n" .
                       "============================================================\n\n";
                       
        if (file_put_contents($log_file, $log_content, FILE_APPEND)) {
            return [
                'success' => true,
                'mode' => 'mock',
                'message' => 'El correo fue guardado localmente en sent_emails.log (Para envío real, edita tu SMTP en config.php).'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al escribir el correo simulado en el registro local.'
            ];
        }
    }

    // ENVÍO SMTP REAL MEDIANTE SOCKETS
    $host = SMTP_HOST;
    $port = SMTP_PORT;
    $user = SMTP_USER;
    $pass = SMTP_PASS;

    try {
        // Conexión inicial al socket
        $socket = @fsockopen(($port == 465 ? 'ssl://' : '') . $host, $port, $errno, $errstr, 15);
        if (!$socket) {
            throw new Exception("No se pudo conectar al servidor SMTP en $host:$port. Error: $errstr ($errno)");
        }

        // Leer saludo del servidor (espera código 220)
        smtp_read_response($socket, 220);

        // Enviar EHLO inicial
        $server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
        fwrite($socket, "EHLO " . $server_name . "\r\n");
        smtp_read_response($socket, 250);

        // Si es puerto 587 (TLS), negociar cifrado seguro
        if ($port == 587) {
            fwrite($socket, "STARTTLS\r\n");
            smtp_read_response($socket, 220);

            // Activar cifrado TLS sobre el socket existente
            $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            // Para compatibilidad con versiones PHP modernas
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $crypto_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
            }
            
            if (!stream_socket_enable_crypto($socket, true, $crypto_method)) {
                throw new Exception("Falló la negociación TLS segura sobre el socket.");
            }

            // Volver a enviar EHLO bajo conexión segura
            $server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
            fwrite($socket, "EHLO " . $server_name . "\r\n");
            smtp_read_response($socket, 250);
        }

        // Autenticación SMTP
        fwrite($socket, "AUTH LOGIN\r\n");
        smtp_read_response($socket, 334);

        fwrite($socket, base64_encode($user) . "\r\n");
        smtp_read_response($socket, 334);

        fwrite($socket, base64_encode($pass) . "\r\n");
        smtp_read_response($socket, 235);

        // Especificar Remitente y Destinatario
        fwrite($socket, "MAIL FROM: <$from>\r\n");
        smtp_read_response($socket, 250);

        fwrite($socket, "RCPT TO: <$to>\r\n");
        smtp_read_response($socket, 250);

        // Enviar DATA
        fwrite($socket, "DATA\r\n");
        smtp_read_response($socket, 354);

        // Construir Cabeceras del Mensaje
        $headers = [
            "MIME-Version: 1.0",
            "Content-type: text/html; charset=UTF-8",
            "To: <$to>",
            "From: =?UTF-8?B?" . base64_encode($name) . "?= <$from>",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Date: " . date('r'),
            "Message-ID: <" . md5(uniqid(time())) . "@" . $host . ">",
            "X-Mailer: PHP-SMTP-Socket-Client"
        ];

        $email_content = implode("\r\n", $headers) . "\r\n\r\n" . $message_html . "\r\n.\r\n";
        
        fwrite($socket, $email_content);
        smtp_read_response($socket, 250);

        // Cerrar Conexión
        fwrite($socket, "QUIT\r\n");
        smtp_read_response($socket, 221);
        fclose($socket);

        return [
            'success' => true,
            'mode' => 'smtp',
            'message' => 'Correo electrónico enviado exitosamente vía SMTP.'
        ];

    } catch (Exception $e) {
        error_log("Fallo en envío de correo SMTP: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al enviar el correo: ' . $e->getMessage()
        ];
    }
}

/**
 * Lee la respuesta del socket SMTP y verifica el código de estado esperado.
 */
function smtp_read_response($socket, $expected_code) {
    $response = '';
    $line = '';
    do {
        $line = fgets($socket, 512);
        if ($line === false) {
            throw new Exception("Error al leer respuesta del servidor SMTP.");
        }
        $response .= $line;
    } while (substr($line, 3, 1) === '-');
    
    $code = (int)substr($response, 0, 3);
    if ($code !== $expected_code) {
        throw new Exception("El servidor SMTP respondió con código inesperado: $response (se esperaba $expected_code)");
    }
    return $response;
}
?>
