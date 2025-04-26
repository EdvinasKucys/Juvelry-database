<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle update inventory
if(isset($_POST['update_inventory'])) {
    $inventory_id = $_POST['inventory_id'];
    $quantity = $_POST['kiekis'];
    $manufacturer_id = $_POST['manufacturer_id']; // Added manufacturer ID
    
    $query = "UPDATE sandeliuojama_preke SET kiekis = ?, fk_GAMINTOJASgamintojo_id = ? WHERE id_SANDELIUOJAMA_PREKE = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isi", $quantity, $manufacturer_id, $inventory_id);
    
    if($stmt->execute()) {
        $success_message = "Inventory updated successfully";
    } else {
        $error_message = "Error updating inventory: " . $conn->error;
    }
}

// Handle add new inventory
if(isset($_POST['add_inventory'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $manufacturer_id = $_POST['manufacturer_id']; // Added manufacturer ID
    
    $query = "INSERT INTO sandeliuojama_preke (kiekis, fk_PREKEid, fk_GAMINTOJASgamintojo_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $quantity, $product_id, $manufacturer_id);
    
    if($stmt->execute()) {
        $success_message = "Inventory added successfully";
    } else {
        $error_message = "Error adding inventory: " . $conn->error;
    }
}

// Handle delete inventory
if(isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    $delete_query = "DELETE FROM sandeliuojama_preke WHERE id_SANDELIUOJAMA_PREKE = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id_to_delete);
    
    if($stmt->execute()) {
        $delete_message = "Inventory record deleted successfully";
    } else {
        $delete_error = "Error deleting inventory record: " . $conn->error;
    }
}

// Fetch all inventory with product info (updated query to include both manufacturer relationships)
$query = "SELECT sp.id_SANDELIUOJAMA_PREKE, sp.kiekis, p.id as product_id, p.pavadinimas as product_name, 
          k.pavadinimas as category_name, g1.pavadinimas as product_manufacturer_name,
          g2.pavadinimas as inventory_manufacturer_name, g2.gamintojo_id as inventory_manufacturer_id,
          p.kaina
          FROM sandeliuojama_preke sp
          JOIN preke p ON sp.fk_PREKEid = p.id
          JOIN kategorija k ON p.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
          JOIN gamintojas g1 ON p.fk_GAMINTOJASgamintojo_id = g1.gamintojo_id
          JOIN gamintojas g2 ON sp.fk_GAMINTOJASgamintojo_id = g2.gamintojo_id
          ORDER BY p.id, sp.id_SANDELIUOJAMA_PREKE";
$result = $conn->query($query);

// Get all products for dropdown
$products_query = "SELECT p.id, p.pavadinimas 
                  FROM preke p 
                  ORDER BY p.pavadinimas";
$products_result = $conn->query($products_query);

// Get all manufacturers for dropdown (new)
$manufacturers_query = "SELECT gamintojo_id, pavadinimas FROM gamintojas ORDER BY pavadinimas";
$manufacturers_result = $conn->query($manufacturers_query);
?>

<div class="container mt-4">
    <h1>Inventory Management</h1>
    
    <?php if(isset($delete_message)): ?>
        <div class="alert alert-success"><?php echo $delete_message; ?></div>
    <?php endif; ?>
    
    <?php if(isset($delete_error)): ?>
        <div class="alert alert-danger"><?php echo $delete_error; ?></div>
    <?php endif; ?>
    
    <?php if(isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Add new inventory form (updated with manufacturer selection) -->
    <div class="card mb-4">
        <div class="card-header">
            Add New Inventory
        </div>
        <div class="card-body">
            <form method="post" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id" required>
                        <option value="">Select a product</option>
                        <?php 
                        while($product = $products_result->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['pavadinimas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="manufacturer_id" class="form-label">Manufacturer</label>
                    <select class="form-select" id="manufacturer_id" name="manufacturer_id" required>
                        <option value="">Select a manufacturer</option>
                        <?php 
                        $manufacturers_result->data_seek(0); // Reset pointer
                        while($manufacturer = $manufacturers_result->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $manufacturer['gamintojo_id']; ?>">
                                <?php echo htmlspecialchars($manufacturer['pavadinimas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="add_inventory" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Inventory table (updated to show both manufacturers) -->
    <div class="card">
        <div class="card-header">
            Current Inventory
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Product Mfr.</th>
                        <th>Inventory Mfr.</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php 
                        $current_product = '';
                        $row_class = '';
                        while($row = $result->fetch_assoc()): 
                            // Add visual grouping for same product entries
                            if($current_product != $row['product_id']) {
                                $current_product = $row['product_id'];
                                $row_class = ($row_class == '') ? 'table-light' : '';
                            }
                        ?>
                            <tr class="<?php echo $row_class; ?>">
                                <td><?php echo $row['id_SANDELIUOJAMA_PREKE']; ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['product_manufacturer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['inventory_manufacturer_name']); ?></td>
                                <td><?php echo number_format($row['kaina'], 2); ?></td>
                                <td>
                                    <form method="post" action="" class="d-flex">
                                        <input type="hidden" name="inventory_id" value="<?php echo $row['id_SANDELIUOJAMA_PREKE']; ?>">
                                        <input type="number" class="form-control form-control-sm me-2" name="kiekis" 
                                               value="<?php echo $row['kiekis']; ?>" min="0" required style="width: 80px;">
                                        
                                        <!-- Added manufacturer selection -->
                                        <select class="form-select form-select-sm me-2" name="manufacturer_id" required style="width: 120px;">
                                            <?php 
                                            $manufacturers_result->data_seek(0); // Reset pointer
                                            while($manufacturer = $manufacturers_result->fetch_assoc()): 
                                                $selected = ($manufacturer['gamintojo_id'] == $row['inventory_manufacturer_id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $manufacturer['gamintojo_id']; ?>" <?php echo $selected; ?>>
                                                    <?php echo htmlspecialchars($manufacturer['pavadinimas']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        
                                        <button type="submit" name="update_inventory" class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="inventory.php?delete=<?php echo $row['id_SANDELIUOJAMA_PREKE']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this inventory record?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No inventory records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
?>