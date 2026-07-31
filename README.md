# 🌐 Soluciones Informáticas JD & PortilloLab - Portal Web Comercial

[![Organization](https://img.shields.io/badge/Organization-PortilloLab-blue.svg)](https://github.com/PortilloLab)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-success.svg)](#)
[![Stack](https://img.shields.io/badge/Stack-PHP%208%20%7C%20PostgreSQL%20%7C%20Vanilla%20CSS-indigo.svg)](#)

> **Portal web oficial y plataforma de servicios de Soluciones Informáticas JD y PortilloLab Enterprise.**  
> Solución integral para la venta y gestión de Servicios de Soporte IT Preventivo (MSP), Automatización de Infraestructura (**ITAT**) y Gobierno / Auditoría de Calidad de Datos (**DS Guardian**).

---

## 📸 Demostración Visual de la Interfaz (Dark Mode & Glassmorphism)

![Soluciones JD Landing Page](https://raw.githubusercontent.com/PortilloLab/Soluciones_JD_1/main/IMG_SOLUCIONES.jpg)

---

## 🌟 Características Principales

### 1. 🛠️ Integración con IT Automation Toolkit (ITAT)
- **Monitoreo 24/7:** Supervisión en tiempo real de recursos críticos (CPU, Memoria RAM, Discos y Red).
- **Auto-Fix & Autoreparación:** Módulos de autoreparación preventiva para servicios fuera de línea (**MySQL**, **Nginx**, **Power BI Gateway** y **Docker Daemon**).
- **Gestión de Tickets:** Integración con Helpdesk y registro de incidentes técnicos.

### 2. 🛡️ Integración con DS Guardian (Data Governance & QA)
- **Calidad de Datos:** Auditoría estática y dinámica de datasets previa al entrenamiento de modelos de Machine Learning e Inteligencia Artificial.
- **Prevención de Data Leakage:** Aislamiento estricto entre conjuntos de *Train* y *Test* en Winsorization (capping de outliers) e imputación de nulos.
- **Informes Ejecutivos:** Generación automática de reportes técnicos en Markdown y PDF.

### 3. 💼 Servicios MSP & Conectividad
- **Redes & Servidores:** Instalación de cableado estructurado, redes inalámbricas de alta disponibilidad y administración de servidores Linux/Windows Server.
- **Ciberseguridad:** Implementación de Firewalls perimetrales, copias de seguridad automáticas (Backup) y antivirus corporativos.
- **Desarrollo Web & Power BI:** Creación de aplicaciones web a medida y tableros interactivos de inteligencia de negocios.

### 4. 🔑 Portal de Clientes y Backend PHP / PostgreSQL
- **Autenticación Segura:** Registro (`register.php`), inicio de sesión (`login.php`) y panel administrativo (`admin.php`).
- **Procesamiento de Contacto:** Envíos de formularios procesados por AJAX vía socket SMTP directo (`procesar_contacto.php` & `mail_helper.php`).
- **Base de Datos:** Esquema DDL en PostgreSQL / MySQL (`schema.sql`).

---

## 📁 Estructura del Repositorio

```text
Soluciones_JD_1/
├── index.php             # Landing page principal dinámica con integración PHP/BD
├── index.html            # Versión estática HTML5 lista para despliegue rápido
├── config.php            # Configuración de variables de entorno y sesión
├── procesar_contacto.php # Procesador de formulario de contacto vía AJAX y SMTP
├── mail_helper.php       # Envio directo de emails mediante sockets SMTP
├── login.php             # Inicio de sesión al portal de clientes
├── register.php          # Registro de nuevos usuarios
├── admin.php             # Panel de administración de solicitudes e incidentes
├── schema.sql            # Script DDL para creación de tablas en PostgreSQL/MySQL
├── style.css             # Hoja de estilos del sistema de diseño (Glassmorphism & Neon)
├── main.js              # Lógica interactiva, menú móvil y simulación de terminal
├── nosotros.mp4          # Video institucional de presentación del equipo
└── README.md             # Documentación oficial del proyecto
```

---

## 🚀 Despliegue y Ejecución Local

### Prerrequisitos:
- PHP 8.0 o superior
- Servidor Web (Nginx, Apache o servidor embebido de PHP)
- Base de datos PostgreSQL o MySQL

### 1. Clonar el repositorio:
```bash
git clone https://github.com/PortilloLab/Soluciones_JD_1.git
cd Soluciones_JD_1
```

### 2. Importar la Base de Datos:
```bash
psql -U tu_usuario -d tu_base_datos -f schema.sql
```

### 3. Iniciar el Servidor de Desarrollo:
```bash
php -S localhost:8000
```
Abre en tu navegador: **`http://localhost:8000`**

---

## 👨‍💻 Equipo y Créditos

- **José Daniel Portillo** — *Fundador & Especialista en Sistemas e IA/Datos*  
  [LinkedIn](https://www.linkedin.com/in/jos%C3%A9-daniel-portillo-84657025/) | [GitHub](https://github.com/PortilloLab)
- **Sergio Duarte** — *Licenciado en Seguridad Informática*

---

## 📄 Licencia

Este proyecto está bajo la Licencia **MIT**. Consulta el archivo `LICENSE` para más detalles.
