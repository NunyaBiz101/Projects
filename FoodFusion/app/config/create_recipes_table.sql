CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(60) NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    cook_time INT,
    difficulty_level VARCHAR(50),
    image_path VARCHAR(255),
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);