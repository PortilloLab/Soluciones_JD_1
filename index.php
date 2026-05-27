<?php
/**
 * Página de Aterrizaje Pública (Landing Page)
 * Soluciones Informática JD
 */
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Soluciones Informática JD - Soporte y Desarrollo de Redes y Sistemas</title>
    <!-- Meta tags SEO -->
    <meta name="description" content="Brindamos soluciones inmediatas en conectividad de redes, seguridad informática, asesoría de sistemas, respaldos de datos y diseño de aplicaciones web.">
    <meta name="keywords" content="conectividad, redes, soporte tecnico, seguridad informatica, diseño web, backup, antivirus, misiones, soluciones informaticas">
    
    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="font-awesome.css">
    <link rel="stylesheet" href="style.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body>

    <!-- Barra de Navegación -->
    <header class="main-header" id="header">
        <div class="header-container">
            <a href="#inicio" class="logo">
                <img src="favicon_1.ico" alt="Logo Soluciones Informática JD">
                <div class="logo-text">
                    <span class="brand-name">Soluciones Informática JD</span>
                    <span class="tagline">Soporte Inmediato</span>
                </div>
            </a>
            
            <button class="mobile-nav-toggle" aria-label="Abrir menú" id="mobile-toggle">
                <i class="fa fa-bars"></i>
            </button>
            
            <nav class="nav-menu" id="nav-menu">
                <ul>
                    <li><a href="#inicio" class="active">Inicio</a></li>
                    <li><a href="#servicios">Servicios</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
                <div class="nav-actions">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <a href="admin.php" class="btn btn-secondary btn-sm"><i class="fa fa-user-shield"></i> Panel Admin</a>
                        <?php else: ?>
                            <a href="dashboard.php" class="btn btn-primary btn-sm"><i class="fa fa-dashboard"></i> Mi Panel</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-outline btn-sm"><i class="fa fa-sign-out"></i> Salir</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline btn-sm"><i class="fa fa-sign-in"></i> Ingresar</a>
                        <a href="register.php" class="btn btn-primary btn-sm"><i class="fa fa-user-plus"></i> Registrarse</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Sección Hero -->
    <section class="hero-section" id="inicio">
        <div class="hero-background-overlay"></div>
        <div class="hero-container">
            <div class="hero-content">
                <span class="hero-badge"><i class="fa fa-check-circle"></i> Disponibilidad y Soporte Técnico</span>
                <h1>¿Buscas soluciones inmediatas en sistemas?<br><span class="highlight">Aquí las encontrarás.</span></h1>
                <p>Nos especializamos en brindar soporte de redes, seguridad corporativa, administración de servidores, copias de seguridad de datos y desarrollo web para garantizar que su negocio nunca se detenga.</p>
                <div class="hero-actions">
                    <a href="#contacto" class="btn btn-primary btn-lg">Solicitar Asistencia <i class="fa fa-chevron-right"></i></a>
                    <a href="#servicios" class="btn btn-outline btn-lg">Ver Nuestros Servicios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Servicios -->
    <section class="services-section" id="servicios">
        <div class="section-container">
            <div class="section-header">
                <h2>Nuestros Servicios Profesionales</h2>
                <div class="line"></div>
                <p>Ofrecemos un catálogo integral de servicios tecnológicos diseñados para adaptar su empresa a la era digital con total tranquilidad.</p>
            </div>
            
            <div class="services-grid">
                <!-- Conectividad -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa fa-wifi"></i>
                    </div>
                    <h3>Conectividad</h3>
                    <p>Nada es más importante que la conectividad en un sistema informático. Nos especializamos en la planificación, instalación y mantenimiento de redes por cable e inalámbricas (WiFi), garantizando estabilidad total las 24 horas.</p>
                </div>

                <!-- Asesoría y Soporte -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa fa-support"></i>
                    </div>
                    <h3>Asesoría y Soporte</h3>
                    <p>Brindamos consultoría y soporte técnico durante toda la semana. Solo debe comunicarse, explicarnos el problema que presenta y a la brevedad nuestro equipo técnico acudirá o se conectará de manera remota para resolverlo.</p>
                </div>

                <!-- Escalabilidad -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa fa-line-chart"></i>
                    </div>
                    <h3>Escalabilidad</h3>
                    <p>Es esencial que una empresa logre adaptarse al cambio tecnológico constante. Nosotros diseñamos e implementamos mejoras progresivas asegurando que cada inversión mantenga y multiplique su valor en el tiempo.</p>
                </div>

                <!-- Desarrollo Web -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa fa-code"></i>
                    </div>
                    <h3>Diseño y Desarrollo</h3>
                    <p>Contamos con profesionales preparados para el desarrollo de páginas web y aplicaciones a medida. Facilitamos que exponga y venda sus servicios de manera óptima, acompañándolo y asesorándolo en todo el ciclo de vida.</p>
                </div>

                <!-- Resguardo de Datos -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa fa-database"></i>
                    </div>
                    <h3>Backup y Resguardo</h3>
                    <p>En cualquier sistema de información, lo más valioso son los datos. Garantizamos el resguardo de su información crítica a través de copias de seguridad automáticas y seguras, tanto locales como en la nube, durante todo el año.</p>
                </div>

                <!-- Ciberseguridad -->
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa fa-shield"></i>
                    </div>
                    <h3>Firewalls y Antivirus</h3>
                    <p>Protegemos la integridad de su infraestructura tecnológica contra amenazas cibernéticas. Implementamos firewalls perimetrales, sistemas antivirus corporativos de última generación y políticas estrictas de control de accesos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Sobre Nosotros -->
    <section class="about-section" id="nosotros">
        <div class="section-container">
            <div class="section-header">
                <h2>Sobre Nosotros</h2>
                <div class="line"></div>
            </div>
            
            <div class="about-grid">
                <!-- Historia -->
                <div class="about-info">
                    <h3>Nuestra Historia</h3>
                    <p>Soluciones Informáticas JD nace en tiempos de pandemia ante la urgente necesidad de las empresas y profesionales de adaptarse a las nuevas condiciones de teletrabajo y digitalización.</p>
                    <p>Tras una constante investigación de mercado y capacitación técnica, conformamos un equipo multidisciplinario altamente preparado para agrupar múltiples soluciones en el área de sistemas en un solo canal de atención.</p>
                    
                    <div class="stats-mini">
                        <div class="stat-item">
                            <span class="number">24/7</span>
                            <span class="label">Soporte Continuo</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">100%</span>
                            <span class="label">Datos Protegidos</span>
                        </div>
                    </div>
                </div>

                <!-- Video Conócenos -->
                <div class="about-video-container">
                    <h3>Conócenos en Acción</h3>
                    <div class="video-wrapper">
                        <!-- Reproductor de video moderno -->
                        <video controls poster="IMG_SOLUCIONES.jpg" class="custom-video">
                            <source src="nosotros.mp4" type="video/mp4">
                            Tu navegador no soporta la reproducción de este video.
                        </video>
                    </div>
                </div>
            </div>

            <!-- Nuestro Equipo -->
            <div class="team-container">
                <h3 class="team-title">Nuestro Equipo Especializado</h3>
                <div class="team-grid">
                    <!-- José Daniel Portillo -->
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="fa fa-user-circle-o"></i>
                        </div>
                        <div class="team-info">
                            <h4>José Daniel Portillo</h4>
                            <p class="role">Técnico Especializado en Sistemas e Infraestructura</p>
                            <p class="desc">Programador y Profesor en Educación Técnica Profesional. Experto en conectividad, base de datos y desarrollo backend.</p>
                        </div>
                    </div>

                    <!-- Sergio Duarte -->
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="fa fa-user-shield"></i>
                        </div>
                        <div class="team-info">
                            <h4>Sergio Duarte</h4>
                            <p class="role">Licenciado en Seguridad Informática</p>
                            <p class="desc">Especialista en auditorías de seguridad, protección de datos, configuración de firewalls perimetrales y redes corporativas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Contacto -->
    <section class="contact-section" id="contacto">
        <div class="section-container">
            <div class="section-header">
                <h2>¿Tienes una emergencia o proyecto en mente?</h2>
                <div class="line"></div>
                <p>Contáctanos completando el formulario. Nos pondremos en comunicación en la brevedad.</p>
            </div>
            
            <div class="contact-grid">
                <!-- Info de contacto -->
                <div class="contact-info-card">
                    <h3>Información de Contacto</h3>
                    <p>Comunícate directamente a nuestras líneas de atención o envíanos un correo.</p>
                    
                    <div class="info-list">
                        <div class="info-item">
                            <i class="fa fa-phone"></i>
                            <div class="info-text">
                                <span>Teléfono Celular:</span>
                                <strong>+54 3764 - 393390</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fa fa-envelope"></i>
                            <div class="info-text">
                                <span>Correo Electrónico:</span>
                                <strong>jsdnlportillo@gmail.com</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fa fa-clock-o"></i>
                            <div class="info-text">
                                <span>Horarios de Atención:</span>
                                <strong>Lunes a Viernes de 08:00 a 20:00 hs. Soporte de emergencia 24h.</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de contacto AJAX -->
                <div class="contact-form-card">
                    <h3>Envíanos un Mensaje</h3>
                    
                    <div id="contact-alert" class="alert d-none"></div>

                    <form id="contact-form" action="procesar_contacto.php" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" placeholder="Ej. juan@correo.com" required>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">¿En qué te podemos ayudar?</label>
                            <textarea id="mensaje" name="mensaje" rows="5" placeholder="Cuéntanos brevemente sobre tu requerimiento..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="btn-submit-contact">
                            Enviar Mensaje <i class="fa fa-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-col brand-col">
                <a href="#inicio" class="logo">
                    <img src="favicon_1.ico" alt="Logo">
                    <span>SOLUCIONES JD</span>
                </a>
                <p>Garantizamos el funcionamiento de su infraestructura tecnológica para que pueda centrarse en el crecimiento de su negocio.</p>
            </div>
            
            <div class="footer-col links-col">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#servicios">Servicios</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
            </div>

            <div class="footer-col info-col">
                <h4>Portal de Clientes</h4>
                <ul>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="register.php">Registrarse</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Copyright &copy; 2026 Todos los derechos reservados; Soluciones Informática JD.</p>
        </div>
    </footer>

    <!-- Vanilla Javascript -->
    <script src="main.js"></script>
</body>
</html>
