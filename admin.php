<?php
/**
 * Panel de Administración
 * Soluciones Informática JD & PortilloLab
 */

require_once __DIR__ . '/config.php';

// Requerir que el usuario sea administrador
requireAdmin();

$nombre_admin = $_SESSION['usuario_nombre'];
$error = '';
$success = '';

// Procesar actualización del estado de un ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'actualizar_estado') {
    $ticket_id = filter_var($_POST['ticket_id'] ?? 0, FILTER_VALIDATE_INT);
    $nuevo_estado = sanitize($_POST['estado'] ?? '');

    if (!$ticket_id || empty($nuevo_estado)) {
        $error = 'Parámetros no válidos.';
    } elseif (!in_array($nuevo_estado, ['abierto', 'en_proceso', 'resuelto', 'cerrado'])) {
        $error = 'El estado seleccionado no es válido.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tickets SET estado = :estado, actualizado_at = CURRENT_TIMESTAMP WHERE id = :id");
            if ($stmt->execute([':estado' => $nuevo_estado, ':id' => $ticket_id])) {
                $success = 'El estado del ticket #' . $ticket_id . ' se actualizó correctamente.';
            } else {
                $error = 'No se pudo actualizar el ticket de soporte.';
            }
        } catch (PDOException $e) {
            error_log("Error de BD al actualizar ticket admin: " . $e->getMessage());
            $error = 'Error de base de datos al actualizar el estado del ticket.';
        }
    }
}

// Procesar actualización del estado de servicios de un cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'actualizar_estado_servicio') {
    $cliente_id = filter_var($_POST['cliente_id'] ?? 0, FILTER_VALIDATE_INT);
    $estado_respaldo = sanitize($_POST['estado_respaldo'] ?? 'pendiente');
    $estado_seguridad = sanitize($_POST['estado_seguridad'] ?? 'pendiente');

    if (!$cliente_id) {
        $error = 'Cliente no válido.';
    } elseif (!in_array($estado_respaldo, ['pendiente', 'activo', 'atencion']) || !in_array($estado_seguridad, ['pendiente', 'activo', 'atencion'])) {
        $error = 'Los estados seleccionados no son válidos.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO estado_servicios (usuario_id, estado_respaldo, estado_seguridad, actualizado_at) 
                VALUES (:usuario_id, :respaldo, :seguridad, CURRENT_TIMESTAMP)
                ON CONFLICT (usuario_id) 
                DO UPDATE SET estado_respaldo = EXCLUDED.estado_respaldo, 
                              estado_seguridad = EXCLUDED.estado_seguridad, 
                              actualizado_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([
                ':usuario_id' => $cliente_id,
                ':respaldo' => $estado_respaldo,
                ':seguridad' => $estado_seguridad
            ]);
            $success = 'El estado de los servicios del cliente se actualizó correctamente.';
        } catch (PDOException $e) {
            error_log("Error de BD al actualizar estado de servicios admin: " . $e->getMessage());
            $error = 'Error de base de datos al actualizar los servicios del cliente.';
        }
    }
}

// Obtener todas las solicitudes de soporte de la BD con el nombre y email del cliente
try {
    $stmt = $pdo->query("
        SELECT t.*, u.nombre as cliente_nombre, u.email as cliente_email 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        ORDER BY t.creado_at DESC
    ");
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error de BD al cargar tickets admin: " . $e->getMessage());
    $tickets = [];
}

// Obtener todos los mensajes de contacto de la BD
try {
    $stmt_msg = $pdo->query("SELECT * FROM mensajes_contacto ORDER BY creado_at DESC");
    $mensajes = $stmt_msg->fetchAll();
} catch (PDOException $e) {
    error_log("Error de BD al cargar mensajes admin: " . $e->getMessage());
    $mensajes = [];
}

// Obtener clientes registrados y el estado real de sus servicios
try {
    $stmt_clientes = $pdo->query("
        SELECT u.id, u.nombre, u.email, u.usuario, 
               COALESCE(es.estado_respaldo, 'pendiente') as estado_respaldo,
               COALESCE(es.estado_seguridad, 'pendiente') as estado_seguridad
        FROM usuarios u
        LEFT JOIN estado_servicios es ON u.id = es.usuario_id
        WHERE u.rol = 'cliente'
        ORDER BY u.nombre ASC
    ");
    $clientes_servicios = $stmt_clientes->fetchAll();
} catch (PDOException $e) {
    error_log("Error de BD al cargar clientes admin: " . $e->getMessage());
    $clientes_servicios = [];
}

// Contar estadísticas generales
$total_tickets = count($tickets);
$abiertos_count = 0;
foreach ($tickets as $t) {
    if ($t['estado'] === 'abierto' || $t['estado'] === 'en_proceso') {
        $abiertos_count++;
    }
}
$mensajes_count = count($mensajes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Soluciones Informática JD</title>
    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="font-awesome.css">
    <!-- Estilos específicos y globales -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <header class="dashboard-nav">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <img src="favicon_1.ico" alt="Logo">
                <span>SOLUCIONES JD (ADMIN)</span>
            </a>
            <div class="user-menu">
                <span><i class="fa fa-user-shield"></i> Administrador: <?php echo htmlspecialchars($nombre_admin); ?></span>
                <span class="user-role-badge badge-admin">Admin</span>
                <a href="logout.php" class="btn btn-outline btn-sm"><i class="fa fa-sign-out"></i> Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        <section class="dashboard-welcome">
            <h1>Panel de Control de Administración</h1>
            <p>Gestione tickets de soporte técnico, estado de servicios por cliente e interactúe con los mensajes recibidos.</p>
        </section>

        <!-- Tarjetas de Estadísticas -->
        <section class="dashboard-stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fa fa-ticket"></i>
                </div>
                <div class="stat-details">
                    <h3>Tickets Activos</h3>
                    <p class="stat-number"><?php echo $abiertos_count; ?> / <?php echo $total_tickets; ?></p>
                    <span class="stat-meta">Tickets en espera de resolución</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fa fa-envelope-o"></i>
                </div>
                <div class="stat-details">
                    <h3>Mensajes de Contacto</h3>
                    <p class="stat-number"><?php echo $mensajes_count; ?></p>
                    <span class="stat-meta">Consultas recibidas en la web</span>
                </div>
            </div>
        </section>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;">
                <i class="fa fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Layout de Tablas -->
        <div class="dashboard-tabs-container">
            
            <!-- Gestión de Estado de Servicios por Cliente -->
            <div class="dashboard-section" style="margin-bottom: 30px;">
                <div class="section-header">
                    <h2>Gestión de Estado de Servicios por Cliente</h2>
                </div>

                <?php if (empty($clientes_servicios)): ?>
                    <div class="empty-state">
                        <i class="fa fa-users"></i>
                        <p>No hay clientes registrados en el sistema actualmente.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Estado de Respaldo</th>
                                    <th>Seguridad de Red</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes_servicios as $cli): ?>
                                    <tr>
                                        <td>
                                            <form id="form_cli_<?php echo $cli['id']; ?>" action="admin.php" method="POST">
                                                <input type="hidden" name="action" value="actualizar_estado_servicio">
                                                <input type="hidden" name="cliente_id" value="<?php echo $cli['id']; ?>">
                                            </form>
                                            <div class="ticket-title"><?php echo htmlspecialchars($cli['nombre']); ?></div>
                                            <div class="ticket-desc"><?php echo htmlspecialchars($cli['email']); ?> (<?php echo htmlspecialchars($cli['usuario']); ?>)</div>
                                        </td>
                                        <td>
                                            <select name="estado_respaldo" form="form_cli_<?php echo $cli['id']; ?>">
                                                <option value="pendiente" <?php echo $cli['estado_respaldo'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente de Configuración</option>
                                                <option value="activo" <?php echo $cli['estado_respaldo'] === 'activo' ? 'selected' : ''; ?>>Activo (Protegido)</option>
                                                <option value="atencion" <?php echo $cli['estado_respaldo'] === 'atencion' ? 'selected' : ''; ?>>Requiere Atención</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="estado_seguridad" form="form_cli_<?php echo $cli['id']; ?>">
                                                <option value="pendiente" <?php echo $cli['estado_seguridad'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente de Configuración</option>
                                                <option value="activo" <?php echo $cli['estado_seguridad'] === 'activo' ? 'selected' : ''; ?>>Activo (Protegido)</option>
                                                <option value="atencion" <?php echo $cli['estado_seguridad'] === 'atencion' ? 'selected' : ''; ?>>Requiere Atención</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" form="form_cli_<?php echo $cli['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fa fa-save"></i> Guardar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tickets de Soporte -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Tickets de Soporte de Clientes</h2>
                </div>

                <?php if (empty($tickets)): ?>
                    <div class="empty-state">
                        <i class="fa fa-check-square-o"></i>
                        <p>No se registran tickets de soporte técnico de clientes.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Ticket / Cliente</th>
                                    <th>Fecha Creación</th>
                                    <th>Prioridad</th>
                                    <th>Estado Actual</th>
                                    <th>Actualizar Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td>
                                            <div class="ticket-title">#<?php echo $ticket['id']; ?>: <?php echo htmlspecialchars($ticket['titulo']); ?></div>
                                            <div class="ticket-desc"><?php echo htmlspecialchars($ticket['descripcion']); ?></div>
                                            <div class="ticket-meta-info">
                                                <strong>Cliente:</strong> <?php echo htmlspecialchars($ticket['cliente_nombre']); ?> 
                                                (<?php echo htmlspecialchars($ticket['cliente_email']); ?>)
                                            </div>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($ticket['creado_at'])); ?></td>
                                        <td>
                                            <?php 
                                            $prio_class = '';
                                            switch($ticket['prioridad']) {
                                                case 'alta': $prio_class = 'badge-danger'; break;
                                                case 'media': $prio_class = 'badge-warning'; break;
                                                case 'baja': $prio_class = 'badge-success'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $prio_class; ?>">
                                                <?php echo ucfirst($ticket['prioridad']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $estado_class = '';
                                            $estado_texto = '';
                                            switch($ticket['estado']) {
                                                case 'abierto': $estado_class = 'badge-info'; $estado_texto = 'Abierto'; break;
                                                case 'en_proceso': $estado_class = 'badge-primary'; $estado_texto = 'En Proceso'; break;
                                                case 'resuelto': $estado_class = 'badge-success'; $estado_texto = 'Resuelto'; break;
                                                case 'cerrado': $estado_class = 'badge-secondary'; $estado_texto = 'Cerrado'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $estado_class; ?>">
                                                <?php echo $estado_texto; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="admin.php" method="POST" class="admin-state-form">
                                                <input type="hidden" name="action" value="actualizar_estado">
                                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                                <select name="estado" onchange="this.form.submit()">
                                                    <option value="abierto" <?php echo $ticket['estado'] === 'abierto' ? 'selected' : ''; ?>>Abierto</option>
                                                    <option value="en_proceso" <?php echo $ticket['estado'] === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                                    <option value="resuelto" <?php echo $ticket['estado'] === 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                                                    <option value="cerrado" <?php echo $ticket['estado'] === 'cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tabla de Mensajes Recibidos -->
            <div class="dashboard-section" style="margin-top: 30px;">
                <div class="section-header">
                    <h2>Mensajes del Formulario de Contacto</h2>
                </div>

                <?php if (empty($mensajes)): ?>
                    <div class="empty-state">
                        <i class="fa fa-envelope-open-o"></i>
                        <p>No se registran mensajes de contacto recibidos en el sitio web.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Mensaje / Remitente</th>
                                    <th>Fecha de Recepción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mensajes as $msg): ?>
                                    <tr>
                                        <td>
                                            <div class="ticket-meta-info">
                                                <strong>Remitente:</strong> <?php echo htmlspecialchars($msg['nombre']); ?> 
                                                (<?php echo htmlspecialchars($msg['email']); ?>)
                                            </div>
                                            <div class="ticket-desc" style="font-size: 14px; margin-top: 5px; background: rgba(255,255,255,0.05); padding: 10px; border-radius: 4px;">
                                                <?php echo nl2br(htmlspecialchars($msg['mensaje'])); ?>
                                            </div>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($msg['creado_at'])); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=RE: Consulta Soluciones Informática JD" class="btn btn-primary btn-sm">
                                                Responder <i class="fa fa-reply"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="dashboard-footer">
        <p>Copyright &copy; 2026 Todos los derechos reservados; Soluciones Informática JD.</p>
    </footer>
</body>
</html>
