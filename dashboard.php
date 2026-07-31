<?php
/**
 * Panel de Control del Cliente
 * Soluciones Informática JD
 */

require_once __DIR__ . '/config.php';

// Requerir inicio de sesión
requireLogin();

// Si el usuario es administrador, redirigir a su panel de administración
if (isAdmin()) {
    header("Location: admin.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['usuario_nombre'];

$error = '';
$success = '';

// Procesar la creación de un nuevo ticket de soporte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear_ticket') {
    $titulo = sanitize($_POST['titulo'] ?? '');
    $descripcion = sanitize($_POST['descripcion'] ?? '');
    $prioridad = sanitize($_POST['prioridad'] ?? 'media');

    if (empty($titulo) || empty($descripcion)) {
        $error = 'Por favor, complete el título y la descripción del problema.';
    } elseif (!in_array($prioridad, ['baja', 'media', 'alta'])) {
        $error = 'La prioridad seleccionada no es válida.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (usuario_id, titulo, descripcion, prioridad, estado) VALUES (:usuario_id, :titulo, :descripcion, :prioridad, 'abierto')");
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':titulo' => $titulo,
                ':descripcion' => $descripcion,
                ':prioridad' => $prioridad
            ]);
            $success = 'Ticket de soporte técnico creado con éxito. Un técnico se comunicará a la brevedad.';
        } catch (PDOException $e) {
            error_log("Error de BD al crear ticket: " . $e->getMessage());
            $error = 'Hubo un error al registrar tu ticket en la base de datos. Inténtelo más tarde.';
        }
    }
}

// Obtener los tickets del cliente actual
try {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE usuario_id = :usuario_id ORDER BY creado_at DESC");
    $stmt->execute([':usuario_id' => $usuario_id]);
    $tickets = $stmt->fetchAll();
    
    // Contar tickets activos (abiertos y en proceso)
    $stmt_active = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE usuario_id = :usuario_id AND estado IN ('abierto', 'en_proceso')");
    $stmt_active->execute([':usuario_id' => $usuario_id]);
    $tickets_activos_count = $stmt_active->fetchColumn();
} catch (PDOException $e) {
    error_log("Error de BD al cargar tickets: " . $e->getMessage());
    $tickets = [];
    $tickets_activos_count = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Soluciones Informática JD</title>
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
                <span>SOLUCIONES JD</span>
            </a>
            <div class="user-menu">
                <span><i class="fa fa-user-circle"></i> Hola, <?php echo htmlspecialchars($nombre_usuario); ?></span>
                <span class="user-role-badge">Cliente</span>
                <a href="logout.php" class="btn btn-outline btn-sm"><i class="fa fa-sign-out"></i> Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        <section class="dashboard-welcome">
            <h1>Panel de Control de Clientes</h1>
            <p>Monitorea tus servicios contratados y solicita soporte inmediato.</p>
        </section>

        <!-- Tarjetas de Estado General -->
        <section class="dashboard-stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fa fa-ticket"></i>
                </div>
                <div class="stat-details">
                    <h3>Tickets Activos</h3>
                    <p class="stat-number"><?php echo $tickets_activos_count; ?></p>
                    <span class="stat-meta">Tickets en atención</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fa fa-cloud-upload"></i>
                </div>
                <div class="stat-details">
                    <h3>Estado del Respaldo</h3>
                    <p class="stat-number">100%</p>
                    <span class="stat-meta text-green"><i class="fa fa-check-circle"></i> Copia de seguridad activa</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fa fa-shield"></i>
                </div>
                <div class="stat-details">
                    <h3>Seguridad de Red</h3>
                    <p class="stat-number">Activo</p>
                    <span class="stat-meta text-green"><i class="fa fa-lock"></i> Firewall y Antivirus OK</span>
                </div>
            </div>
        </section>

        <div class="dashboard-layout-grid">
            <!-- Sección de Tickets Activos -->
            <section class="dashboard-section table-section">
                <div class="section-header">
                    <h2>Mis Solicitudes de Soporte</h2>
                </div>

                <?php if (empty($tickets)): ?>
                    <div class="empty-state">
                        <i class="fa fa-folder-open-o"></i>
                        <p>No tienes tickets de soporte registrados en este momento.</p>
                        <p class="sub-text">Si tienes algún inconveniente técnico, crea un ticket en el formulario lateral.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Asunto</th>
                                    <th>Creado el</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td>
                                            <div class="ticket-title"><?php echo htmlspecialchars($ticket['titulo']); ?></div>
                                            <div class="ticket-desc"><?php echo htmlspecialchars($ticket['descripcion']); ?></div>
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
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Formulario de Creación de Ticket -->
            <section class="dashboard-section form-section">
                <div class="section-header">
                    <h2>Solicitar Soporte Técnico</h2>
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

                <form action="dashboard.php" method="POST" class="ticket-form">
                    <input type="hidden" name="action" value="crear_ticket">
                    
                    <div class="form-group">
                        <label for="titulo">Asunto / Problema</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Ej. No puedo conectar la impresora de red" required>
                    </div>

                    <div class="form-group">
                        <label for="prioridad">Prioridad</label>
                        <select id="prioridad" name="prioridad" required>
                            <option value="baja">Baja (Consultas o mejoras)</option>
                            <option value="media" selected>Media (Inconvenientes de red o pc normales)</option>
                            <option value="alta">Alta (Problema bloqueante - Servidores caídos, corte total)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del Problema</label>
                        <textarea id="descripcion" name="descripcion" rows="5" placeholder="Detalle qué ocurre, qué error se presenta y cuándo inició el problema..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Enviar Ticket de Soporte <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            </section>
        </div>
    </main>

    <footer class="dashboard-footer">
        <p>Copyright &copy; 2026 Todos los derechos reservados; Soluciones Informática JD.</p>
    </footer>
</body>
</html>
