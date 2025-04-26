<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle update inventory
if(isset($_POST['update_inventory'])) {
    $inventory_id = $_POST['inventory_id'];
    $quantity = $_POST['kiekis'];
    
    $query = "UPDATE sandeliuojama_preke SET kiekis = ? WHERE id_SANDELIUOJAMA_PREKE = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $quantity, $inventory_id);
    
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
    
    // Check if product already has inventory
    $check_query = "SELECT COUNT(*) as count FROM sandeliuojama_preke WHERE fk_PREKEid = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if($row['count'] > 0) {
        $error_message = "This product already has an inventory entry. Please update it instead.";
    } else {
        $query = "INSERT INTO sandeliuojama_preke (kiekis, fk_PREKEid) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $quantity, $product_id);
        
        if($stmt->execute()) {
            $success_message = "Inventory added successfully";
        } else {
            $error_message = "Error adding inventory: " . $conn->error;
        }
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

// Fetch all inventory with product info
$query = "SELECT sp.id_SANDELIUOJAMA_PREKE, sp.kiekis, p.id as product_id, p.pavadinimas as product_name, 
          k.pavadinimas as category_name, g.pavadinimas as manufacturer_name, p.kaina
          FROM sandeliuojama_preke sp
          JOIN preke p ON sp.fk_PREKEid = p.id
          JOIN kategorija k ON p.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
          JOIN gamintojas g ON p.fk_GAMINTOJASgamintojo_id = g.gamintojo_id
          ORDER BY sp.id_SANDELIUOJAMA_PREKE";
$result = $conn->query($query);

// Get products without inventory
$products_query = "SELECT p.id, p.pavadinimas 
                  FROM preke p 
                  LEFT JOIN sandeliuojama_preke sp ON p.id = sp.fk_PREKEid
                  WHERE sp.id_SANDELIUOJAMA_PREKE IS NULL
                  ORDER BY p.pavadinimas";
$products_result = $conn->query($products_query);
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
    
    <!-- Add new inventory form -->
    <div class="card mb-4">
        <div class="card-header">
            Add New Inventory
        </div>
        <div class="card-body">
            <form method="post" action="" class="row g-3">
                <div class="col-md-6">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id" required>
                        <option value="">Select a product</option>
                        <?php while($product = $products_result->fetch_assoc()): ?>
                            <option value="<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['pavadinimas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="add_inventory" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Inventory table -->
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
                        <th>Manufacturer</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_SANDELIUOJAMA_PREKE']; ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['manufacturer_name']); ?></td>
                                <td><?php echo number_format($row['kaina'], 2); ?></td>
                                <td>
                                    <form method="post" action="" class="d-flex">
                                        <input type="hidden" name="inventory_id" value="<?php echo $row['id_SANDELIUOJAMA_PREKE']; ?>">
                                        <input type="number" class="form-control form-control-sm me-2" name="kiekis" 
                                               value="<?php echo $row['kiekis']; ?>" min="0" required style="width: 80px;">
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
                            <td colspan="7" class="text-center">No inventory records found</td>
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