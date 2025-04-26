<?php
// Database seeding script for Jewelry Inventory System
// This script populates the database with test data

// Include database connection
include '../includes/db_connect.php';

// Start transaction for data integrity
$conn->begin_transaction();

try {
    echo "<h1>Database Seeding</h1>";
    
    // Clear existing data (if needed)
    echo "<h2>Clearing existing data...</h2>";
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("TRUNCATE TABLE sandeliuojama_preke");
    $conn->query("TRUNCATE TABLE preke");
    $conn->query("TRUNCATE TABLE gamintojas");
    $conn->query("TRUNCATE TABLE kategorija");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p>All tables cleared successfully.</p>";
    
    // --------------------
    // Create Categories (10 records)
    // --------------------
    echo "<h2>Creating categories...</h2>";
    
    $categories = [
        ['Rings', 'Various types of jewelry rings for different occasions'],
        ['Necklaces', 'Elegant necklaces for everyday wear and special events'],
        ['Bracelets', 'Stylish bracelets that complement any outfit'],
        ['Earrings', 'Beautiful earrings from studs to dangles'],
        ['Watches', 'Premium timepieces for men and women'],
        ['Pendants', 'Unique pendants to personalize your necklaces'],
        ['Brooches', 'Classic and modern brooches for any occasion'],
        ['Anklets', 'Delicate anklets for casual and formal wear'],
        ['Wedding Jewelry', 'Special jewelry for wedding ceremonies'],
        ['Sets', 'Matching jewelry sets for a coordinated look']
    ];
    
    $category_count = 0;
    $category_ids = [];
    
    $category_stmt = $conn->prepare("INSERT INTO kategorija (pavadinimas, aprasymas) VALUES (?, ?)");
    
    foreach ($categories as $category) {
        $category_stmt->bind_param("ss", $category[0], $category[1]);
        $category_stmt->execute();
        $category_ids[] = $conn->insert_id;
        $category_count++;
    }
    
    echo "<p>Added $category_count categories.</p>";
    
    // --------------------
    // Create Manufacturers (10 records)
    // --------------------
    echo "<h2>Creating manufacturers...</h2>";
    
    $manufacturers = [
        ['TIFF001', 'Tiffany & Co.', 'United States', 'contact@tiffany.com, +1-212-555-0101'],
        ['CART002', 'Cartier', 'France', 'info@cartier.com, +33-1-4455-3322'],
        ['BVLG003', 'Bulgari', 'Italy', 'support@bulgari.com, +39-06-8888-7766'],
        ['PAND004', 'Pandora', 'Denmark', 'service@pandora.net, +45-3333-2211'],
        ['SWRV005', 'Swarovski', 'Austria', 'crystal@swarovski.com, +43-5224-5000'],
        ['HRMS006', 'Hermès Jewelry', 'France', 'jewelry@hermes.com, +33-1-4017-4717'],
        ['GCCI007', 'Gucci', 'Italy', 'jewels@gucci.com, +39-055-7592-7010'],
        ['AMYR008', 'Amber Jewelry', 'Lithuania', 'info@amberjewelry.lt, +370-5-212-1212'],
        ['BCVR009', 'Baltic Crafts', 'Latvia', 'sales@balticcrafts.lv, +371-67-223344'],
        ['LTHR010', 'Lithuanian Heritage', 'Lithuania', 'orders@ltherjewelry.lt, +370-5-111-2222']
    ];
    
    $manufacturer_count = 0;
    $manufacturer_ids = [];
    
    $manufacturer_stmt = $conn->prepare("INSERT INTO gamintojas (gamintojo_id, pavadinimas, salis, kontaktai) VALUES (?, ?, ?, ?)");
    
    foreach ($manufacturers as $manufacturer) {
        $manufacturer_stmt->bind_param("ssss", $manufacturer[0], $manufacturer[1], $manufacturer[2], $manufacturer[3]);
        $manufacturer_stmt->execute();
        $manufacturer_ids[] = $manufacturer[0];
        $manufacturer_count++;
    }
    
    echo "<p>Added $manufacturer_count manufacturers.</p>";
    
    // --------------------
    // Create Products (150 records)
    // --------------------
    echo "<h2>Creating products...</h2>";
    
    // Sample product data
    $product_types = [
        'Ring' => ['Diamond', 'Gold', 'Silver', 'Platinum', 'Ruby', 'Sapphire', 'Emerald', 'Pearl'],
        'Necklace' => ['Chain', 'Pendant', 'Choker', 'Lariat', 'Bib', 'Pearl', 'Statement', 'Gemstone'],
        'Bracelet' => ['Bangle', 'Charm', 'Cuff', 'Chain', 'Beaded', 'Tennis', 'Link', 'Wrap'],
        'Earring' => ['Stud', 'Hoop', 'Drop', 'Dangle', 'Chandelier', 'Climber', 'Huggie', 'Jacket'],
        'Watch' => ['Analog', 'Digital', 'Automatic', 'Quartz', 'Smart', 'Chronograph', 'Dress', 'Sports'],
        'Pendant' => ['Heart', 'Cross', 'Geometric', 'Animal', 'Initial', 'Birthstone', 'Locket', 'Gemstone'],
        'Brooch' => ['Floral', 'Vintage', 'Modern', 'Animal', 'Geometric', 'Statement', 'Crystal', 'Pearl'],
        'Anklet' => ['Chain', 'Beaded', 'Charm', 'Gemstone', 'Pearl', 'Layered', 'Adjustable', 'Statement'],
        'Wedding Jewelry' => ['Engagement', 'Wedding Band', 'Bridal Set', 'Anniversary', 'Promise', 'Eternity', 'Solitaire', 'Three-Stone'],
        'Set' => ['Necklace & Earrings', 'Three-Piece', 'Wedding', 'Parure', 'Demi-Parure', 'Gemstone', 'Pearl', 'Diamond']
    ];
    
    $materials = ['Gold', 'Silver', 'Platinum', 'White Gold', 'Rose Gold', 'Stainless Steel', 'Titanium', 'Amber', 'Sterling Silver', 'Brass'];
    
    $product_count = 0;
    $product_ids = [];
    
    $product_stmt = $conn->prepare("INSERT INTO preke (id, pavadinimas, aprasymas, kaina, svoris, medziaga, fk_GAMINTOJASgamintojo_id, fk_KATEGORIJAid_KATEGORIJA) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Generate 150 products
    for ($i = 1; $i <= 150; $i++) {
        // Select a random category
        $category_index = ($i % 10); // Distribute evenly across categories
        $category_id = $category_ids[$category_index];
        $category_name = $categories[$category_index][0];
        
        // Select a random manufacturer
        $manufacturer_index = ($i % 10);
        $manufacturer_id = $manufacturer_ids[$manufacturer_index];
        
        // Get the correct key for product types - fix for the "Watches" issue
        $type_key = $category_name;
        if ($category_name == 'Watches') {
            $type_key = 'Watch';
        } else if (substr($category_name, -1) == 's' && $category_name != 'Wedding Jewelry' && $category_name != 'Sets') {
            // Remove trailing 's' for most categories except special cases
            $type_key = substr($category_name, 0, -1);
        }
        
        // Generate product details - ensure the key exists
        if (!isset($product_types[$type_key])) {
            echo "<p class='text-danger'>Warning: Missing product type key: '$type_key' for category '$category_name'</p>";
            // Use a fallback
            $type = "Standard";
            $product_type_array = ['Standard'];
        } else {
            $product_type_array = $product_types[$type_key];
            $type = $product_type_array[$i % count($product_type_array)];
        }
        
        $material = $materials[$i % count($materials)];
        
        $product_id = 'PROD' . str_pad($i, 4, '0', STR_PAD_LEFT);
        $name = $type . ' ' . $material . ' ' . $category_name . ' ' . $i;
        $description = 'Beautiful ' . strtolower($type) . ' ' . strtolower($category_name) . ' made of high-quality ' . strtolower($material) . '.';
        
        // Calculate price and weight
        $base_price = 50;
        if (in_array($material, ['Gold', 'Platinum', 'White Gold', 'Rose Gold'])) {
            $base_price = 150;
        }
        $price = $base_price + ($i % 100);
        
        $base_weight = 5;
        if ($category_name == 'Watches') {
            $base_weight = 50;
        } elseif (in_array($category_name, ['Necklaces', 'Bracelets'])) {
            $base_weight = 15;
        }
        $weight = $base_weight + ($i % 20);
        
        // Insert product
        $product_stmt->bind_param("sssddssi", $product_id, $name, $description, $price, $weight, $material, $manufacturer_id, $category_id);
        $product_stmt->execute();
        $product_ids[] = $product_id;
        $product_count++;
    }
    
    echo "<p>Added $product_count products.</p>";
    
    // --------------------
    // Create Inventory Items (150 records)
    // --------------------
    echo "<h2>Creating inventory items...</h2>";
    
    $inventory_count = 0;
    $inventory_stmt = $conn->prepare("INSERT INTO sandeliuojama_preke (kiekis, fk_PREKEid) VALUES (?, ?)");
    
    foreach ($product_ids as $product_id) {
        // Generate random stock quantity between 0 and 50
        $quantity = rand(0, 50);
        
        $inventory_stmt->bind_param("is", $quantity, $product_id);
        $inventory_stmt->execute();
        $inventory_count++;
    }
    
    echo "<p>Added $inventory_count inventory items.</p>";
    
    // --------------------
    // Commit transaction
    // --------------------
    $conn->commit();
    
    $total_records = $category_count + $manufacturer_count + $product_count + $inventory_count;
    echo "<h2>Database seeding completed successfully!</h2>";
    echo "<p>Total records created: $total_records</p>";
    echo "<ul>";
    echo "<li>Categories: $category_count</li>";
    echo "<li>Manufacturers: $manufacturer_count</li>";
    echo "<li>Products: $product_count</li>";
    echo "<li>Inventory items: $inventory_count</li>";
    echo "</ul>";
    
    echo "<p><a href='../index.php' class='btn btn-primary'>Return to Homepage</a></p>";
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "<div class='alert alert-danger'>";
    echo "<h2>Error occurred during database seeding:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Seeding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #0d6efd;
            margin-bottom: 20px;
        }
        h2 {
            color: #198754;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 15px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .text-danger {
            color: #dc3545;
        }
    </style>
</head>
<body class="container">
    <!-- Content is generated by PHP above -->
</body>
</html>