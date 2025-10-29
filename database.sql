CREATE DATABASE travel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE travel_db;

CREATE TABLE destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    ticket_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert dummy data
INSERT INTO destinations (name, location, description, ticket_price) VALUES
('Borobudur', 'Magelang, Jawa Tengah', 'Candi Buddha terbesar di dunia.', 50000.00),
('Bali', 'Denpasar, Bali', 'Pulau dewata dengan pantai indah.', 0.00),
('Raja Ampat', 'Papua Barat', 'Surga bawah laut dunia.', 1000000.00),
('Gunung Bromo', 'Jawa Timur', 'Gunung berapi aktif dengan pemandangan sunrise.', 35000.00),
('Labuan Bajo', 'NTT', 'Gerbang menuju Taman Nasional Komodo.', 75000.00);