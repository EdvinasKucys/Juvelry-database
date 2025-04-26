<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle delete request
if(isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Check if category is used in any products
    $check_query = "SELECT COUNT(*) as count FROM preke WHERE fk_KATEGORIJAid_KATEGORIJA = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $id_to_delete);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if($row['count'] > 0) {
        $delete_error = "Cannot delete category because it is used in " . $row['count'] . " products. Remove those products first.";
    } else {
        $delete_query = "DELETE FROM kategorija WHERE id_KATEGORIJA = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $id_to_delete);
        
        if($stmt->execute()) {
            $delete_message = "Category deleted successfully";
        } else {
            $delete_error = "Error deleting category: " . $conn->error;
        }
    }
}

// Handle add/edit category
if(isset($_POST['save_category'])) {
    $category_name = $_POST['pavadinimas'];
    $category_desc = $_POST['aprasymas'];
    
    if(isset($_POST['category_id']) && $_POST['category_id'] > 0) {
        // Update existing category
        $category_id = $_POST['category_id'];
        $query = "UPDATE kategorija SET pavadinimas = ?, aprasymas = ? WHERE id_KATEGORIJA = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $category_name, $category_desc, $category_id);
        
        if($stmt->execute()) {
            $success_message = "Category updated successfully";
        } else {
            $error_message = "Error updating category: " . $conn->error;
        }
    } else {
        // Add new category
        $query = "INSERT INTO kategorija (pavadinimas, aprasymas) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $category_name, $category_desc);
        
        if($stmt->execute()) {
            $success_message = "Category added successfully";
        } else {
            $error_message = "Error adding category: " . $conn->error;
        }
    }
}

// Fetch category for editing
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : 0;
$edit_data = null;

if($edit_id > 0) {
    $edit_query = "SELECT * FROM kategorija WHERE id_KATEGORIJA = ?";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    
    if($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}

// Fetch all categories
$query = "SELECT * FROM kategorija ORDER BY id_KATEGORIJA";
$result = $conn->query($query);
?>

<div class="container mt-4">
    <h1>Categories Management</h1>
    
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
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <?php echo $edit_data ? 'Edit Category' : 'Add New Category'; ?>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <?php if($edit_data): ?>
                            <input type="hidden" name="category_id" value="<?php echo $edit_data['id_KATEGORIJA']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="pavadinimas" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="pavadinimas" name="pavadinimas" 
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['pavadinimas']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="aprasymas" class="form-label">Description</label>
                            <textarea class="form-control" id="aprasymas" name="aprasymas" rows="3"><?php echo $edit_data ? htmlspecialchars($edit_data['aprasymas']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" name="save_category" class="btn btn-primary">Save</button>
                        <?php if($edit_data): ?>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Categories List
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id_KATEGORIJA']; ?></td>
                                        <td><?php echo htmlspecialchars($row['pavadinimas']); ?></td>
                                        <td><?php echo htmlspecialchars($row['aprasymas'] ?? ''); ?></td>
                                        <td>
                                            <a href="categories.php?edit=<?php echo $row['id_KATEGORIJA']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="categories.php?delete=<?php echo $row['id_KATEGORIJA']; ?>" class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
?>