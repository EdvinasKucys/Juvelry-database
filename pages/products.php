<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle delete request
if(isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Start transaction to ensure data integrity
    $conn->begin_transaction();
    
    try {
        // First delete any category relationships
        $delete_categories_query = "DELETE FROM preke_kategorija WHERE fk_PREKEid = ?";
        $stmt = $conn->prepare($delete_categories_query);
        $stmt->bind_param("s", $id_to_delete);
        $stmt->execute();
        $categories_affected = $stmt->affected_rows;
        $stmt->close();
        
        // Then delete the product itself
        $delete_query = "DELETE FROM preke WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("s", $id_to_delete);
        $stmt->execute();
        $product_affected = $stmt->affected_rows;
        $stmt->close();
        
        // Commit the transaction
        $conn->commit();
        
        if($product_affected > 0) {
            $delete_message = "Product deleted successfully";
            if($categories_affected > 0) {
                $delete_message .= " along with " . $categories_affected . " category relationships";
            }
        } else {
            $delete_error = "Product not found or already deleted";
        }
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        $delete_error = "Error deleting product: " . $e->getMessage();
    }
}

// Fetch products with manufacturer and category info
$query = "SELECT p.id, p.pavadinimas, p.kaina, p.svoris, p.medziaga, 
          g.pavadinimas as manufacturer_name, 
          (SELECT COUNT(*) FROM preke_kategorija WHERE fk_PREKEid = p.id) as category_count,
          (SELECT GROUP_CONCAT(k.pavadinimas SEPARATOR ', ') 
           FROM preke_kategorija pc
           JOIN kategorija k ON pc.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
           WHERE pc.fk_PREKEid = p.id) as categories,
          (SELECT k.pavadinimas 
           FROM preke_kategorija pc
           JOIN kategorija k ON pc.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
           WHERE pc.fk_PREKEid = p.id AND pc.pagrindine_kategorija = TRUE
           LIMIT 1) as primary_category
          FROM preke p 
          JOIN gamintojas g ON p.fk_GAMINTOJASgamintojo_id = g.gamintojo_id
          ORDER BY p.id";
          
$result = $conn->query($query);
?>

<div class="container mt-4">
    <h1>Products Management</h1>
    
    <?php if(isset($delete_message)): ?>
        <div class="alert alert-success"><?php echo $delete_message; ?></div>
    <?php endif; ?>
    
    <?php if(isset($delete_error)): ?>
        <div class="alert alert-danger"><?php echo $delete_error; ?></div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-end mb-3">
        <a href="products_edit.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add New Product
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Weight</th>
                    <th>Material</th>
                    <th>Manufacturer</th>
                    <th>Primary Category</th>
                    <th>All Categories</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['pavadinimas']); ?></td>
                            <td><?php echo number_format($row['kaina'], 2); ?> €</td>
                            <td><?php echo number_format($row['svoris'], 2); ?> g</td>
                            <td><?php echo htmlspecialchars($row['medziaga'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['manufacturer_name']); ?></td>
                            <td>
                                <?php if($row['primary_category']): ?>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($row['primary_category']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if($row['categories']): 
                                    $category_array = explode(', ', $row['categories']);
                                    foreach($category_array as $cat): 
                                ?>
                                    <span class="badge bg-secondary category-badge"><?php echo htmlspecialchars($cat); ?></span>
                                <?php 
                                    endforeach;
                                else: 
                                ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="products_edit.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="products.php?delete=<?php echo urlencode($row['id']); ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>