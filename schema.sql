-- Esquema de Base de Datos para Soluciones Informática JD & PortilloLab
-- Motor de Base de Datos: PostgreSQL / MySQL

-- Eliminar tablas si existen (para facilitar la reinstalación)
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS mensajes_contacto;
DROP TABLE IF EXISTS usuarios;

-- 1. Tabla de Usuarios
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'cliente' CHECK (rol IN ('cliente', 'admin')),
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabla de Tickets de Soporte
CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad VARCHAR(20) DEFAULT 'media' CHECK (prioridad IN ('baja', 'media', 'alta')),
    estado VARCHAR(20) DEFAULT 'abierto' CHECK (estado IN ('abierto', 'en_proceso', 'resuelto', 'cerrado')),
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. Tabla de Mensajes de Contacto
CREATE TABLE mensajes_contacto (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    servicio VARCHAR(150) DEFAULT 'Consulta General',
    mensaje TEXT NOT NULL,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar las búsquedas
CREATE INDEX idx_tickets_usuario ON tickets(usuario_id);
CREATE INDEX idx_tickets_estado ON tickets(estado);
CREATE INDEX idx_usuarios_usuario ON usuarios(usuario);

-- NOTA DE SEGURIDAD:
-- Las cuentas de administración se crean de forma segura mediante el script CLI:
-- php crear_admin.php
