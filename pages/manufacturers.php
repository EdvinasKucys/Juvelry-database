<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Handle delete request
if(isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Check if manufacturer is used in any products
    $check_query = "SELECT COUNT(*) as count FROM preke WHERE fk_GAMINTOJASgamintojo_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $id_to_delete);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if($row['count'] > 0) {
        $delete_error = "Cannot delete manufacturer because it is used in " . $row['count'] . " products. Remove those products first.";
    } else {
        $delete_query = "DELETE FROM gamintojas WHERE gamintojo_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("s", $id_to_delete);
        
        if($stmt->execute()) {
            $delete_message = "Manufacturer deleted successfully";
        } else {
            $delete_error = "Error deleting manufacturer: " . $conn->error;
        }
    }
}

// Handle add/edit manufacturer
if(isset($_POST['save_manufacturer'])) {
    $manufacturer_id = $_POST['gamintojo_id'];
    $manufacturer_name = $_POST['pavadinimas'];
    $manufacturer_country = $_POST['salis'];
    $manufacturer_contacts = $_POST['kontaktai'];
    
    if(isset($_POST['edit_mode']) && $_POST['edit_mode'] == 'true') {
        // Update existing manufacturer
        $query = "UPDATE gamintojas SET pavadinimas = ?, salis = ?, kontaktai = ? WHERE gamintojo_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $manufacturer_name, $manufacturer_country, $manufacturer_contacts, $manufacturer_id);
        
        if($stmt->execute()) {
            $success_message = "Manufacturer updated successfully";
        } else {
            $error_message = "Error updating manufacturer: " . $conn->error;
        }
    } else {
        // Check if ID already exists
        $check_query = "SELECT COUNT(*) as count FROM gamintojas WHERE gamintojo_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $manufacturer_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        
        if($row['count'] > 0) {
            $error_message = "Manufacturer ID already exists. Please use a different ID.";
        } else {
            // Add new manufacturer
            $query = "INSERT INTO gamintojas (gamintojo_id, pavadinimas, salis, kontaktai) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $manufacturer_id, $manufacturer_name, $manufacturer_country, $manufacturer_contacts);
            
            if($stmt->execute()) {
                $success_message = "Manufacturer added successfully";
                // Clear form after successful add
                $_POST = array();
            } else {
                $error_message = "Error adding manufacturer: " . $conn->error;
            }
        }
    }
}

// Fetch manufacturer for editing
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : '';
$edit_data = null;

if(!empty($edit_id)) {
    $edit_query = "SELECT * FROM gamintojas WHERE gamintojo_id = ?";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bind_param("s", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    
    if($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}

// Fetch all manufacturers with product count
$query = "SELECT g.*, (SELECT COUNT(*) FROM preke WHERE fk_GAMINTOJASgamintojo_id = g.gamintojo_id) as product_count 
          FROM gamintojas g ORDER BY g.gamintojo_id";
$result = $conn->query($query);
?>

<div class="container mt-4">
    <h1>Manufacturers Management</h1>
    
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
                    <?php echo $edit_data ? 'Edit Manufacturer' : 'Add New Manufacturer'; ?>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <?php if($edit_data): ?>
                            <input type="hidden" name="edit_mode" value="true">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="gamintojo_id" class="form-label">Manufacturer ID</label>
                            <input type="text" class="form-control" id="gamintojo_id" name="gamintojo_id" 
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['gamintojo_id']) : (isset($_POST['gamintojo_id']) ? htmlspecialchars($_POST['gamintojo_id']) : ''); ?>" 
                                   <?php echo $edit_data ? 'readonly' : ''; ?> required>
                            <small class="form-text text-muted">Unique identifier for the manufacturer</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pavadinimas" class="form-label">Name</label>
                            <input type="text" class="form-control" id="pavadinimas" name="pavadinimas" 
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['pavadinimas']) : (isset($_POST['pavadinimas']) ? htmlspecialchars($_POST['pavadinimas']) : ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="salis" class="form-label">Country</label>
                            <input type="text" class="form-control" id="salis" name="salis" 
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['salis']) : (isset($_POST['salis']) ? htmlspecialchars($_POST['salis']) : ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="kontaktai" class="form-label">Contacts</label>
                            <textarea class="form-control" id="kontaktai" name="kontaktai" required><?php echo $edit_data ? htmlspecialchars($edit_data['kontaktai']) : (isset($_POST['kontaktai']) ? htmlspecialchars($_POST['kontaktai']) : ''); ?></textarea>
                        </div>
                        
                        <button type="submit" name="save_manufacturer" class="btn btn-primary">Save</button>
                        <?php if($edit_data): ?>
                            <a href="manufacturers.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Manufacturers List
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Country</th>
                                <th>Contacts</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['gamintojo_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pavadinimas']); ?></td>
                                        <td><?php echo htmlspecialchars($row['salis'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['kontaktai']); ?></td>
                                        <td><?php echo $row['product_count']; ?></td>
                                        <td>
                                            <a href="manufacturers.php?edit=<?php echo urlencode($row['gamintojo_id']); ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="manufacturers.php?delete=<?php echo urlencode($row['gamintojo_id']); ?>" class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this manufacturer?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No manufacturers found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>