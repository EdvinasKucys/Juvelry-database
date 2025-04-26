<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle delete request
if(isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Start transaction to ensure data integrity
    $conn->begin_transaction();
    
    try {
        // First delete any inventory records associated with this product
        $delete_inventory_query = "DELETE FROM sandeliuojama_preke WHERE fk_PREKEid = ?";
        $stmt = $conn->prepare($delete_inventory_query);
        $stmt->bind_param("s", $id_to_delete);
        $stmt->execute();
        $inventory_affected = $stmt->affected_rows;
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
            if($inventory_affected > 0) {
                $delete_message .= " along with " . $inventory_affected . " inventory records";
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

// Fetch products with category and manufacturer info
$query = "SELECT p.id, p.pavadinimas as product_name, p.kaina, p.medziaga, 
          k.pavadinimas as category_name, g.pavadinimas as manufacturer_name,
          (SELECT SUM(kiekis) FROM sandeliuojama_preke WHERE fk_PREKEid = p.id) as total_stock
          FROM preke p 
          JOIN kategorija k ON p.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
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
        <a href="products_edit.php" class="btn btn-primary">Add New Product</a>
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Material</th>
                <th>Category</th>
                <th>Manufacturer</th>
                <th>Total Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['kaina']); ?></td>
                        <td><?php echo htmlspecialchars($row['medziaga']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['manufacturer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_stock'] ?? 0); ?></td>
                        <td>
                            <a href="products_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No products found</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
</div>

<?php
$conn->close();
?>