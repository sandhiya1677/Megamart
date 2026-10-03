DROP DATABASE IF EXISTS megamart_db;
CREATE DATABASE megamart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE megamart_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(15),
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(60) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  old_price DECIMAL(10,2) DEFAULT NULL,
  rating DECIMAL(2,1) DEFAULT 4.0,
  stock INT DEFAULT 20,
  image VARCHAR(255) DEFAULT ''
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  customer_name VARCHAR(100),
  phone VARCHAR(15),
  address VARCHAR(255),
  payment_method VARCHAR(30),
  total DECIMAL(10,2),
  status VARCHAR(20) DEFAULT 'Pending',
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2)
);

INSERT INTO products (name, category, description, price, old_price, rating, stock, image) VALUES
('Wireless Headphones', 'Electronics', 'Over-ear Bluetooth headphones with deep bass, soft ear cushions and 30 hours of battery life.', 1799, 2499, 4.5, 25, 'headphones.svg'),
('Smart Watch', 'Electronics', 'Fitness watch with heart-rate tracking, sleep monitor, call alerts and a bright AMOLED display.', 2999, 3999, 4.3, 15, 'smartwatch.svg'),
('Bluetooth Speaker', 'Electronics', 'Portable speaker with 360-degree sound, splash-proof body and 12 hours of playtime.', 1499, 1999, 4.4, 30, 'speaker.svg'),
('Wireless Mouse', 'Electronics', 'Silent-click ergonomic mouse with 2.4GHz receiver and adjustable DPI.', 599, 899, 4.1, 50, 'mouse.svg'),
('Running Sneakers', 'Fashion', 'Lightweight, breathable sneakers with cushioned soles for daily runs and walks.', 1999, 2799, 4.6, 18, 'sneakers.svg'),
('Laptop Backpack', 'Fashion', 'Water-resistant 25L backpack with padded laptop sleeve and anti-theft back pocket.', 1199, 1599, 4.2, 22, 'backpack.svg'),
('Polarized Sunglasses', 'Fashion', 'UV400 polarized lenses in a lightweight frame. Comes with a hard case.', 799, 1299, 4.0, 40, 'sunglasses.svg'),
('Sports Cap', 'Fashion', 'Adjustable cotton cap with moisture-wicking band. One size fits most.', 349, NULL, 4.1, 60, 'cap.svg'),
('Steel Water Bottle 1L', 'Home & Kitchen', 'Double-wall insulated bottle that keeps drinks cold for 24 hours or hot for 12.', 449, 649, 4.5, 80, 'bottle.svg'),
('Table Lamp', 'Home & Kitchen', 'Warm-light LED desk lamp with touch dimmer and a stable weighted base.', 899, 1199, 4.3, 28, 'lamp.svg'),
('Ceramic Coffee Mug', 'Home & Kitchen', 'Matte-finish 350ml mug, microwave and dishwasher safe.', 249, NULL, 4.4, 100, 'mug.svg'),
('Desk Plant Pot', 'Home & Kitchen', 'Artificial leafy plant in a terracotta-style pot. Needs no watering.', 399, 599, 3.9, 35, 'plant.svg');
