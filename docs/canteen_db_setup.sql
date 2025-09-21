-- Create database
CREATE DATABASE IF NOT EXISTS canteen_db;
USE canteen_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY, -- Unique user ID
    name VARCHAR(100) NOT NULL, -- User name
    email VARCHAR(100) NOT NULL UNIQUE, -- User email
    password VARCHAR(255) NOT NULL, -- Hashed password
    role ENUM('admin','student') NOT NULL, -- User role
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Registration time
);

-- Create menu table
CREATE TABLE IF NOT EXISTS menu (
    item_id INT AUTO_INCREMENT PRIMARY KEY, -- Unique item ID
    item_name VARCHAR(100) NOT NULL, -- Item name
    description TEXT, -- Item description
    price DECIMAL(8,2) NOT NULL, -- Item price
    availability ENUM('yes','no') DEFAULT 'yes', -- Availability status
    image VARCHAR(255), -- Image filename
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Added time
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY, -- Unique order ID
    user_id INT NOT NULL, -- Student user ID
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP, -- Order date
    total_amount DECIMAL(10,2) NOT NULL, -- Total order amount
    status ENUM('pending','completed','cancelled') DEFAULT 'pending', -- Order status
    FOREIGN KEY (user_id) REFERENCES users(user_id) -- FK to users
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY, -- Unique order item ID
    order_id INT NOT NULL, -- FK to orders
    item_id INT NOT NULL, -- FK to menu
    quantity INT NOT NULL, -- Quantity ordered
    FOREIGN KEY (order_id) REFERENCES orders(order_id), -- FK to orders
    FOREIGN KEY (item_id) REFERENCES menu(item_id) -- FK to menu
);

-- Insert default users
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@canteen.com', '$2y$10$adminhash', 'admin'), -- Default admin
('Student One', 'student1@canteen.com', '$2y$10$student1hash', 'student'), -- Student 1
('Student Two', 'student2@canteen.com', '$2y$10$student2hash', 'student'), -- Student 2
('Student Three', 'student3@canteen.com', '$2y$10$student3hash', 'student'); -- Student 3

-- Insert default menu items
INSERT INTO menu (item_name, description, price, availability, image) VALUES
('Veg Sandwich', 'Fresh vegetable sandwich', 40.00, 'yes', 'veg_sandwich.jpg'),
('Chicken Roll', 'Spicy chicken roll', 60.00, 'yes', 'chicken_roll.jpg'),
('Paneer Wrap', 'Paneer wrap with salad', 55.00, 'yes', 'paneer_wrap.jpg'),
('Cold Coffee', 'Iced cold coffee', 35.00, 'yes', 'cold_coffee.jpg'),
('Samosa', 'Crispy samosa', 20.00, 'yes', 'samosa.jpg'),
('Fruit Juice', 'Mixed fruit juice', 30.00, 'yes', 'fruit_juice.jpg'),
('Burger', 'Veg burger', 50.00, 'yes', 'burger.jpg'),
('French Fries', 'Crispy fries', 45.00, 'yes', 'fries.jpg');

-- Insert sample orders for students
INSERT INTO orders (user_id, total_amount, status) VALUES
(2, 95.00, 'completed'), -- Student One
(2, 60.00, 'pending'),
(3, 50.00, 'completed'), -- Student Two
(3, 75.00, 'pending'),
(4, 40.00, 'completed'), -- Student Three
(4, 100.00, 'pending');

-- Insert sample order_items
INSERT INTO order_items (order_id, item_id, quantity) VALUES
(1, 1, 1), -- Veg Sandwich for order 1
(1, 2, 1), -- Chicken Roll for order 1
(2, 2, 2), -- Chicken Roll for order 2
(3, 7, 1), -- Burger for order 3
(3, 8, 1), -- French Fries for order 3
(4, 3, 1), -- Paneer Wrap for order 4
(4, 4, 2), -- Cold Coffee for order 4
(5, 5, 2), -- Samosa for order 5
(6, 6, 2), -- Fruit Juice for order 6
(6, 1, 1); -- Veg Sandwich for order 6
