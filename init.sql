CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
('Mouse Logitech', 'Logitech g305 color morado', 40, 50),
('Teclado Razer', 'Le falla una tecla pero anda, lo doy barato', 29.99, 30),
('Cargador USB C', 'Cargador USb C de 45 W marca UGREEN', 20.00, 20);
