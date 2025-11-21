-- Minecraft Inside Database Schema

SET FOREIGN_KEY_CHECKS=0;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default.png',
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    role ENUM('user','admin') DEFAULT 'user',
    is_banned BOOLEAN DEFAULT FALSE,
    last_login DATETIME,
    reputation INT DEFAULT 0
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type ENUM('mods','maps','resourcepacks','shaders','skins','servers') NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0
);

-- Files table
CREATE TABLE IF NOT EXISTS files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    minecraft_version VARCHAR(20) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    preview_image VARCHAR(255),
    downloads_count INT DEFAULT 0,
    rating FLOAT DEFAULT 0,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    file_size INT,
    file_type ENUM('mods','maps','resourcepacks','shaders','skins') NOT NULL,
    tags JSON,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_created_date (created_date),
    INDEX idx_downloads (downloads_count),
    INDEX idx_rating (rating)
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_edited BOOLEAN DEFAULT FALSE,
    edited_date DATETIME,
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_file_id (file_id),
    INDEX idx_created_date (created_date)
);

-- Ratings table
CREATE TABLE IF NOT EXISTS ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rating (file_id, user_id),
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (file_id, user_id),
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Downloads table
CREATE TABLE IF NOT EXISTS downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT NOT NULL,
    download_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_download_date (download_date)
);

-- News table
CREATE TABLE IF NOT EXISTS news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_published BOOLEAN DEFAULT TRUE,
    views_count INT DEFAULT 0,
    image VARCHAR(255),
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tags table
CREATE TABLE IF NOT EXISTS tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    usage_count INT DEFAULT 0
);

-- File tags relation
CREATE TABLE IF NOT EXISTS file_tags (
    file_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (file_id, tag_id),
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS=1;