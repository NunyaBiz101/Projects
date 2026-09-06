<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'parkview_supermarket');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SITE_NAME', 'Park View Supermarket');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

define('DEFAULT_ADMIN_USERNAME', 'admin');
define('DEFAULT_ADMIN_PASSWORD', ADMIN_PASS);

function getDB() {
    static $db = null;
    if ($db === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $db = new PDO($dsn, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        initDB($db);
    }
    return $db;
}

function initDB($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            display_name VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            description TEXT,
            image_url VARCHAR(500),
            in_stock TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS deals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            discount_percent INT,
            original_price DECIMAL(10,2),
            sale_price DECIMAL(10,2),
            image_url VARCHAR(500),
            valid_until DATE,
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $adminCount = $db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($adminCount == 0) {
        $stmt = $db->prepare("INSERT INTO admins (username, password, display_name) VALUES (?,?,?)");
        $stmt->execute([DEFAULT_ADMIN_USERNAME, DEFAULT_ADMIN_PASSWORD, 'Store Admin']);
    }

    $count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        $products = [
            ['Organic Avocados', 'Fruits & Veg', 3.99, 'Perfectly ripe Hass avocados, 4-pack. Grown without pesticides.', 'https://images.unsplash.com/photo-1519162808019-7de1683fa2ad?w=400&q=80', 1],
            ['Free Range Eggs', 'Dairy & Eggs', 4.49, 'Fresh free-range eggs, 12 count. From happy hens roaming open pastures.', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400&q=80', 1],
            ['Whole Grain Bread', 'Bakery', 3.29, 'Artisan whole grain loaf, baked fresh daily. Rich in fibre.', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80', 1],
            ['Atlantic Salmon', 'Meat & Fish', 12.99, 'Fresh Atlantic salmon fillet, per lb. Wild-caught and sustainably sourced.', 'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79c?w=400&q=80', 1],
            ['Organic Strawberries', 'Fruits & Veg', 5.49, 'Sweet organic strawberries, 16oz. Handpicked at peak ripeness.', 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=400&q=80', 1],
            ['Cheddar Cheese', 'Dairy & Eggs', 6.99, 'Aged sharp cheddar, 16oz block. Smooth and tangy flavour.', 'https://images.unsplash.com/photo-1618164436241-4473940d1f5c?w=400&q=80', 1],
            ['Chicken Breast', 'Meat & Fish', 8.99, 'Boneless skinless chicken breast, per lb. Hormone-free and antibiotic-free.', 'https://images.unsplash.com/photo-1604503468506-a8da13d11f36?w=400&q=80', 1],
            ['Almond Milk', 'Beverages', 3.79, 'Unsweetened almond milk, 64oz. Creamy and dairy-free.', 'https://images.unsplash.com/photo-1600718374662-0483d2b9da44?w=400&q=80', 1],
            ['Baby Spinach', 'Fruits & Veg', 3.49, 'Tender baby spinach leaves, 5oz bag. Pre-washed and ready to eat.', 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=400&q=80', 1],
            ['Greek Yogurt', 'Dairy & Eggs', 2.99, 'Plain full-fat Greek yogurt, 17.6oz. Thick, creamy and protein-rich.', 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&q=80', 1],
            ['Sourdough Loaf', 'Bakery', 4.99, 'Classic sourdough with crispy crust and chewy interior.', 'https://images.unsplash.com/photo-1585478259715-876acc5be8eb?w=400&q=80', 1],
            ['Orange Juice', 'Beverages', 4.29, 'Freshly squeezed orange juice, 52oz. No added sugar or preservatives.', 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&q=80', 1],
        ];
        $stmt = $db->prepare("INSERT INTO products (name, category, price, description, image_url, in_stock) VALUES (?,?,?,?,?,?)");
        foreach ($products as $p) $stmt->execute($p);

        $deals = [
            ['Weekend Fresh Deal', 'Get 25% off all Fruits & Veg this weekend only!', 25, 19.99, 14.99, 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&q=80', date('Y-m-d', strtotime('+7 days')), 1],
            ['Bakery Bundle', 'Buy any 2 bakery items and save 30%. Mix & match!', 30, 9.98, 6.99, 'https://images.unsplash.com/photo-1534432182912-63863115e106?w=600&q=80', date('Y-m-d', strtotime('+14 days')), 1],
            ['Protein Pack', 'Salmon + Chicken combo - save $5 when you buy both.', 20, 24.99, 19.99, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80', date('Y-m-d', strtotime('+10 days')), 1],
        ];
        $stmt = $db->prepare("INSERT INTO deals (title, description, discount_percent, original_price, sale_price, image_url, valid_until, active) VALUES (?,?,?,?,?,?,?,?)");
        foreach ($deals as $d) $stmt->execute($d);
    }
}
?>
