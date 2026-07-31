-- Esquema de Base de Datos para Soluciones Informática JD
-- Motor de Base de Datos: PostgreSQL

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
    mensaje TEXT NOT NULL,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar las búsquedas
CREATE INDEX idx_tickets_usuario ON tickets(usuario_id);
CREATE INDEX idx_tickets_estado ON tickets(estado);
CREATE INDEX idx_usuarios_usuario ON usuarios(usuario);

-- Insertar Datos de Prueba / Semilla
-- Contraseña de administrador predeterminada: admin123
-- Hash bcrypt generado para 'admin123': $2y$10$TRWMRganH25vExPnI8Reu.ITy2Vqo7nvaCwwuhTY86EDyr3dEUvhy
INSERT INTO usuarios (nombre, email, usuario, password_hash, rol) VALUES
('Administrador JD', 'jsdnlportillo@gmail.com', 'admin', '$2y$10$TRWMRganH25vExPnI8Reu.ITy2Vqo7nvaCwwuhTY86EDyr3dEUvhy', 'admin'),
('Cliente de Prueba', 'cliente@prueba.com', 'cliente', '$2y$10$TRWMRganH25vExPnI8Reu.ITy2Vqo7nvaCwwuhTY86EDyr3dEUvhy', 'cliente');

-- Insertar algunos tickets de prueba
INSERT INTO tickets (usuario_id, titulo, descripcion, prioridad, estado) VALUES
(2, 'Problema de conectividad en oficina', 'No nos funciona el router wifi de la sala de reuniones principal.', 'alta', 'abierto'),
(2, 'Actualización de antivirus requerida', 'Necesitamos actualizar la licencia del antivirus corporativo en 3 terminales.', 'baja', 'resuelto');
