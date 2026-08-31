CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categories_name (name)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'draft', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_products_category (category_id),
    INDEX idx_products_name (name)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Khalti', 'eSewa', 'Cash on Delivery') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    shipping_name VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(30) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_order_number (order_number)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order_items_order (order_id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    payment_method ENUM('Khalti', 'eSewa', 'Cash on Delivery') NOT NULL,
    transaction_id VARCHAR(150) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_payments_transaction (transaction_id)
);

INSERT INTO users (name, email, password, phone, address, city, role, status)
VALUES (
    'Admin User',
    'adminmarketnest123@gmail.com',
    '$2y$10$V1NXlZwebON5ONiccmTwZ.byV2a7EbeO4k.fSwtqTIzHpGcLkNITO',
    '9800000001',
    'Main Street 1',
    'Kathmandu',
    'admin',
    'active'
),
(
    'Demo Customer',
    'customer@marketnest.com',
    '$2y$10$z9s4Yg3E4Pq8JUNb3rA2ce55xw1xY0Y6VXG1jE0mJ9sK3O4xN5eF2',
    '9800000002',
    'Bhatbhateni Lane 2',
    'Pokhara',
    'customer',
    'active'
);

INSERT INTO categories (name, slug, description, image, status)
VALUES
('Electronics', 'electronics', 'Smart devices and modern tech essentials.', 'electronics.jpg', 'active'),
('Accessories', 'accessories', 'Daily-use accessories that complement your lifestyle.', 'accessories.jpg', 'active'),
('Clothing', 'clothing', 'Comfortable and stylish outfits for everyday wear.', 'clothing.jpg', 'active'),
('Home & Living', 'home-living', 'Home essentials and caring living items.', 'home-living.jpg', 'active'),
('Books', 'books', 'Knowledge, fiction, and productivity reads.', 'books.jpg', 'active'),
('Sports', 'sports', 'Active gear and performance essentials.', 'sports.jpg', 'active');

INSERT INTO products (category_id, name, slug, description, price, stock, image, status)
VALUES
(1, 'Wireless Headphones', 'wireless-headphones', 'Premium noise-cancelling wireless headphones with deep bass and all-day comfort.', 7999.00, 24, 'headphones.jpg', 'active'),
(1, 'Mechanical Keyboard', 'mechanical-keyboard', 'Compact mechanical keyboard with tactile switches and RGB backlighting.', 6999.00, 18, 'keyboard.jpg', 'active'),
(1, 'USB-C Hub', 'usb-c-hub', 'Multi-port USB-C hub with HDMI, USB-A, SD card reader, and charging support.', 3499.00, 30, 'hub.jpg', 'active'),
(1, 'Smart Watch', 'smart-watch', 'Fitness-focused smartwatch with Bluetooth calling and heart rate tracking.', 10999.00, 12, 'watch.jpg', 'active'),
(2, 'Laptop Backpack', 'laptop-backpack', 'Water-resistant backpack with padded laptop sleeve and organized compartments.', 2499.00, 40, 'backpack.jpg', 'active'),
(2, 'Bluetooth Speaker', 'bluetooth-speaker', 'Portable speaker with powerful sound, IPX5 protection, and long battery life.', 4599.00, 28, 'speaker.jpg', 'active'),
(2, 'Travel Adapter', 'travel-adapter', 'Universal travel adapter compatible with multiple international plugs.', 1499.00, 50, 'adapter.jpg', 'active'),
(3, 'Running Shoes', 'running-shoes', 'Lightweight cushioned running shoes built for daily training.', 5999.00, 20, 'running-shoes.jpg', 'active'),
(3, 'Cotton Hoodie', 'cotton-hoodie', 'Classic hoodie with soft cotton feel and relaxed fit.', 3499.00, 22, 'hoodie.jpg', 'active'),
(3, 'Classic Tee', 'classic-tee', 'Minimal everyday tee made from breathable cotton fabric.', 1299.00, 60, 'tee.jpg', 'active'),
(4, 'Desk Lamp', 'desk-lamp', 'Modern desk lamp with adjustable arm, warm LED glow, and USB charging.', 2399.00, 25, 'lamp.jpg', 'active'),
(4, 'Throw Blanket', 'throw-blanket', 'Soft woven throw blanket for cozy evenings and home décor.', 1999.00, 35, 'blanket.jpg', 'active'),
(4, 'Ceramic Mug Set', 'ceramic-mug-set', 'Elegant ceramic mug set for tea and coffee lovers.', 1799.00, 45, 'mug.jpg', 'active'),
(5, 'Clean Code', 'clean-code', 'A practical guide to writing maintainable software and clean architecture.', 1299.00, 18, 'clean-code.jpg', 'active'),
(5, 'Atomic Habits', 'atomic-habits', 'Proven techniques to build strong habits and improve productivity.', 1499.00, 22, 'atomic-habits.jpg', 'active'),
(5, 'Design Patterns', 'design-patterns', 'Essential patterns for object-oriented design and software engineering.', 1899.00, 15, 'design-patterns.jpg', 'active'),
(6, 'Yoga Mat', 'yoga-mat', 'Non-slip yoga mat with cushioned support for home workouts.', 2599.00, 26, 'yoga-mat.jpg', 'active'),
(6, 'Gym Bottle', 'gym-bottle', 'Insulated stainless steel bottle to keep drinks cold for hours.', 1499.00, 32, 'bottle.jpg', 'active'),
(6, 'Resistance Bands', 'resistance-bands', 'Portable resistance bands for strength and mobility workouts.', 1299.00, 40, 'bands.jpg', 'active');
