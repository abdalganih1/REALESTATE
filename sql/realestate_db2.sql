-- Table: `property_types`
CREATE TABLE IF NOT EXISTS `property_types` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type_name` VARCHAR(100) NOT NULL UNIQUE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Insert initial property types (if they don't exist)
INSERT IGNORE INTO `property_types` (`type_name`) VALUES
('Luxury Villa'),
('Apartment'),
('Penthouse'),
('Villa House'),
('Modern Condo'),
('Studio'); -- يمكنك إضافة المزيد هنا