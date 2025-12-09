# README

## Overview

This project is a PHP-based system that provides user authentication,
role management, data handling, and integration with external services.
It supports two types of users (students and teachers), offers task and
client management features, connects to a SQL database, and includes
REST and SOAP communication modules.

## Features

-   **User Authentication:**\
    Login system to validate user credentials.
-   **Role Management:**\
    Differentiation between student and teacher profiles.
-   **Main Dashboard**
-   **Client Management**
-   **Task Management**
-   **Database Integration (SQL)**
-   **REST Services**
-   **SOAP Services**

## File Structure

-   index.php\
-   iniciarSesion.php\
-   estudiante.php\
-   profesor.php\
-   gestionClientes.php\
-   gestionTareas.php\
-   restServer.php / restCliente.php\
-   soapServer.php / soapCliente.php\
-   saludo.php

## Requirements

-   PHP 7.x+
-   Web server
-   SQL database
-   SOAP and cURL extensions

## Database Schema (Example)

``` sql
CREATE DATABASE IF NOT EXISTS sistema_php;
USE sistema_php;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('estudiante', 'profesor') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_cliente INT,
    id_usuario INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_limite DATE,
    estado ENUM('pendiente','en_proceso','completada') DEFAULT 'pendiente',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);
```

## REST and SOAP examples included.

## Deployment

Steps for setup, DB import, config, enabling extensions, etc.

## Usage

Login, navigate modules, use REST/SOAP clients.

## License

Educational or demo purposes.
