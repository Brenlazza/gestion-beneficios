
CREATE DATABASE gestion_beneficios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_beneficios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    rol ENUM('ADMINISTRADOR','VENDEDOR') NOT NULL DEFAULT 'VENDEDOR',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    telefono VARCHAR(30) NULL,
    documento VARCHAR(30) NULL,
    direccion VARCHAR(255) NULL,
    fecha_nacimiento DATE NULL,
    foto_perfil VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE campanias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM('BORRADOR','ACTIVA','CERRADA') NOT NULL DEFAULT 'BORRADOR',
    creado_por INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campania_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(12,2) NOT NULL,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 5,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campania_id) REFERENCES campanias(id)
);

CREATE TABLE vendedores_campanias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_id INT NOT NULL,
    campania_id INT NOT NULL,
    estado ENUM('PENDIENTE','ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    observaciones VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY vendedor_campania_unico (vendedor_id,campania_id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id),
    FOREIGN KEY (campania_id) REFERENCES campanias(id)
);

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_campania_id INT NOT NULL,
    fecha_venta DATE NOT NULL,
    cliente VARCHAR(150) NOT NULL,
    telefono_cliente VARCHAR(30) NULL,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    estado_entrega ENUM('PENDIENTE','ENTREGADA') NOT NULL DEFAULT 'PENDIENTE',
    fecha_entrega DATE NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendedor_campania_id) REFERENCES vendedores_campanias(id)
);

CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    vendedor_campania_id INT NOT NULL,
    fecha_pago DATE NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    medio_pago ENUM('EFECTIVO','TRANSFERENCIA','OTRO') NOT NULL,
    observacion VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendedor_campania_id) REFERENCES vendedores_campanias(id),
    FOREIGN KEY (venta_id) REFERENCES ventas(id)
);

CREATE TABLE rendiciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_campania_id INT NOT NULL,
    fecha_rendicion DATE NOT NULL,
    monto_declarado DECIMAL(12,2) NOT NULL,
    observacion VARCHAR(255) NULL,
    estado ENUM('PENDIENTE','APROBADA','OBSERVADA') NOT NULL DEFAULT 'PENDIENTE',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendedor_campania_id) REFERENCES vendedores_campanias(id)
);

CREATE TABLE rendicion_pagos (
 rendicion_id INT NOT NULL, pago_id INT NOT NULL,
 PRIMARY KEY(rendicion_id,pago_id),
 FOREIGN KEY(rendicion_id) REFERENCES rendiciones(id),
 FOREIGN KEY(pago_id) REFERENCES pagos(id)
);
