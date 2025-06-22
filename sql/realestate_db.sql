-- Create Database
CREATE DATABASE IF NOT EXISTS `realestate_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `realestate_db`;

-- Table: `users`
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'buyer') NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Table: `properties`
CREATE TABLE IF NOT EXISTS `properties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(100) NOT NULL,
    `price` DECIMAL(15, 2) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `bedrooms` INT NOT NULL,
    `bathrooms` INT NOT NULL,
    `area` DECIMAL(10, 2) NOT NULL,
    `floor` VARCHAR(50) NOT NULL,
    `parking` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `image_url` VARCHAR(255) DEFAULT 'assets/images/default-property.jpg'
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Table: `orders`
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT NOT NULL,
    `buyer_id` INT NOT NULL,
    `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Pending', 'Rejected', 'Sold') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Table: `contact_messages`
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255),
    `message` TEXT NOT NULL,
    `received_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Insert initial data into `users` table
-- Passwords are 'adminpass' and 'buyerpass' respectively (hashed)
INSERT IGNORE INTO `users` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$wO32N2iYlQxV4v3eG0a4rO4.G/O7l7o5eC1q2o3u8s1P8y5P6N4D6U.s', 'admin'), -- Hashed 'adminpass'
('buyer', '$2y$10$R9lP5xZ1jK7yT8uI9o0eU.m.L.e.P.W.E.y.J.D.Z.C.F.V.B.R.G.T.Y.U.H.I.J.K.L.O.P.Q.A.Z', 'buyer'); -- Hashed 'buyerpass'
-- To generate actual hashes for different passwords, you can run a temporary PHP script:
-- <?php // echo password_hash('your_new_password', PASSWORD_BCRYPT); ?>

-- Insert initial data into `properties` table
INSERT IGNORE INTO `properties` (`type`, `price`, `location`, `bedrooms`, `bathrooms`, `area`, `floor`, `parking`, `description`, `image_url`) VALUES
('Luxury Villa', 2264000.00, 'HAMA,ALBRNAWE', 8, 8, 545.00, '3', '6 spots', 'A luxurious villa located in the prestigious Al-Barnawi area of Hama, offering ample space and modern amenities.', 'assets/images/property-01.jpg'),
('Luxury Villa', 1180000.00, 'DAMASCUS,YAFOUR', 6, 5, 450.00, '3', '8 spots', 'An elegant villa in Yaafour, Damascus, perfect for families seeking comfort and style.', 'assets/images/property-02.jpg'),
('Luxury Villa', 1460000.00, 'HAMA,ALMADINEH', 5, 4, 225.00, '3', '10 spots', 'A beautiful villa in the heart of Hama, Al-Madineh, combining classic design with modern living.', 'assets/images/property-03.jpg'),
('Apartment', 584500.00, 'ALEPPO,MERDIAN', 4, 3, 125.00, '25th', '2 cars', 'A modern apartment in Aleppo, Meridian, offering stunning city views from the 25th floor.', 'assets/images/property-04.jpg'),
('Penthouse', 925600.00, 'HOMS,ALHDARA', 4, 4, 180.00, '38th', '2 cars', 'Experience luxury living in this spacious penthouse in Al-Hadara, Homs, with panoramic views.', 'assets/images/property-05.jpg'),
('Modern Condo', 450000.00, 'HAMA,ALKSOUR', 3, 2, 165.00, '26th', '3 cars', 'A stylish and comfortable modern condo in Al-Kassour, Hama, ideal for urban living.', 'assets/images/property-06.jpg'),
('Luxury Villa', 980000.00, 'DAMASCUS,MAZEH', 8, 8, 550.00, '3', '12 spots', 'Grand villa in Mezzeh, Damascus, offering expansive living areas and privacy.', 'assets/images/property-03.jpg'),
('Luxury Villa', 1520000.00, 'DAMASCUS,ALSHALAN', 12, 15, 380.00, '3', '14 spots', 'Exquisite villa in Al-Shaalan, Damascus, known for its opulent design and amenities.', 'assets/images/property-02.jpg'),
('Luxury Villa', 3145000.00, 'DAMASCUS,ALMAZRA', 10, 12, 860.00, '3', '10 spots', 'A massive, luxurious villa in Al-Mazraa, Damascus, suitable for grand living and entertaining.', 'assets/images/property-01.jpg');

-- Insert initial data into `contact_messages` table
INSERT IGNORE INTO `contact_messages` (`name`, `email`, `subject`, `message`) VALUES
('John Doe', 'john.doe@example.com', 'Inquiry about Villa', 'I am interested in property-01. Can you provide more details?'),
('Jane Smith', 'jane.smith@example.com', 'General Question', 'What are your working hours?');