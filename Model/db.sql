-- Webshop Datenbank

CREATE DATABASE IF NOT EXISTS webshop_edv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webshop_edv;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('guest', 'customer', 'premium', 'admin') DEFAULT 'customer',
    bonus_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    final_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default Admin User
-- IMPORTANT: Change the password immediately after first login!
-- Default credentials: username='admin', password='Pass1234word'
-- To create a new admin user with a different password, use PHP:
-- php -r "echo password_hash('your_secure_password', PASSWORD_DEFAULT);"
INSERT INTO users (username, email, password_hash, role)
VALUES ('admin', 'admin@webshop.local', '$2y$12$RdRTqhPJwloP38lADLDxyeW3hWvf3PVzEy0WnDqpEu9LmjgX3A7ge', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Beispiel Produkte
INSERT INTO products (name, description, price, stock) VALUES
('Laptop Pro 15"', 'Leistungsstarker Laptop mit 15 Zoll Display', 1299.99, 25),
('Wireless Mouse', 'Ergonomische kabellose Maus', 29.99, 150),
('Mechanical Keyboard', 'Premium mechanische Tastatur', 149.99, 80);
INSERT INTO products (name, description, price, stock) VALUES
('HD Monitor 24"', '24 Zoll Full HD Monitor', 199.99, 60),
('USB-C Hub', 'Multifunktionaler USB-C Hub mit mehreren Anschlüssen', 49.99, 200),
('Gaming Headset', 'Surround Sound Gaming Headset mit Mikrofon', 89.99, 100);

