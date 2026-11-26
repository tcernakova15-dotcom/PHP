CREATE DATABASE IF NOT EXISTS gamevault CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamevault;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS games (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id INT UNSIGNED NULL,
    title VARCHAR(150) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    platform VARCHAR(50) NOT NULL,
    edition ENUM('Standard', 'Deluxe', 'Key') NOT NULL DEFAULT 'Standard',
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_games_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    game_id INT UNSIGNED NOT NULL,
    price_paid DECIMAL(10, 2) NOT NULL,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Тестовые игры для публичной витрины
INSERT INTO games (title, genre, platform, edition, price, is_featured, description) VALUES
('Hollow Knight', 'Metroidvania', 'PC', 'Standard', 14.99, 1, 'Атмосферное приключение в подземном королевстве Халлоунест.'),
('Ori and the Blind Forest', 'Metroidvania', 'Cross-platform', 'Deluxe', 19.99, 1, 'Визуально выразительный платформер с глубоким сюжетом и музыкой.'),
('Cyber Quest 2077', 'RPG', 'PC', 'Key', 29.50, 0, 'Ролевой экшен в мире высоких технологий и корпораций.');