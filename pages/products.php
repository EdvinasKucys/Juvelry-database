<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle delete request
if(isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $delete_query = "DELETE FROM preke WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $id_to_delete);
    
    if($stmt->execute()) {
        $delete_message = "Product deleted successfully";
    } else {
        $delete_error = "Error deleting product: " . $conn->error;
    }
    $stmt->close();
}

// Fetch products with category and manufacturer info
$query = "SELECT p.id, p.pavadinimas as product_name, p.kaina, p.medziaga, 
          k.pavadinimas as category_name, g.pavadinimas as manufacturer_name,
          IFNULL(sp.kiekis, 0) as stock_quantity
          FROM preke p 
          JOIN kategorija k ON p.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
          JOIN gamintojas g ON p.fk_GAMINTOJASgamintojo_id = g.gamintojo_id
          LEFT JOIN sandeliuojama_preke sp ON p.id = sp.fk_PREKEid
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
                <th>Stock</th>
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
                        <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
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