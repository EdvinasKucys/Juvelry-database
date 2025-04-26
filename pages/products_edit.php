<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Initialize variables
$product_id = isset($_GET['id']) ? $_GET['id'] : '';
$is_edit_mode = !empty($product_id);
$success_message = $error_message = '';
$product_data = [];
$product_categories = [];

// Fetch categories and manufacturers for dropdowns
$categories_query = "SELECT id_KATEGORIJA, pavadinimas FROM kategorija ORDER BY pavadinimas";
$categories_result = $conn->query($categories_query);

$manufacturers_query = "SELECT gamintojo_id, pavadinimas FROM gamintojas ORDER BY pavadinimas";
$manufacturers_result = $conn->query($manufacturers_query);

// If edit mode, fetch existing product data
if ($is_edit_mode) {
    $product_query = "SELECT * FROM preke WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product_data = $result->fetch_assoc();
        
        // Fetch categories assigned to this product
        $categories_query = "SELECT pc.*, k.pavadinimas as category_name 
                            FROM preke_kategorija pc
                            JOIN kategorija k ON pc.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
                            WHERE pc.fk_PREKEid = ?
                            ORDER BY pc.pagrindine_kategorija DESC, k.pavadinimas";
        $stmt = $conn->prepare($categories_query);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $categories_result2 = $stmt->get_result();
        
        while ($cat = $categories_result2->fetch_assoc()) {
            $product_categories[] = $cat;
        }
    } else {
        $error_message = "Product not found.";
        $is_edit_mode = false;
    }
}

// Handle form submission
if (isset($_POST['save_product'])) {
    // Get form data
    $new_product_id = trim($_POST['product_id']);
    $product_name = trim($_POST['pavadinimas']);
    $product_description = trim($_POST['aprasymas']);
    $product_price = floatval($_POST['kaina']);
    $product_weight = floatval($_POST['svoris']);
    $product_material = trim($_POST['medziaga']);
    $manufacturer_id = $_POST['manufacturer_id'];
    
    // Get category data
    $category_ids = isset($_POST['category_id']) ? $_POST['category_id'] : [];
    $primary_categories = isset($_POST['primary_category']) ? $_POST['primary_category'] : [];
    
    // Start transaction
    $conn->begin_transaction();

    try {
        if ($is_edit_mode) {
            // Update existing product
            $update_query = "UPDATE preke SET 
                           pavadinimas = ?, 
                           aprasymas = ?, 
                           kaina = ?, 
                           svoris = ?, 
                           medziaga = ?, 
                           fk_GAMINTOJASgamintojo_id = ? 
                           WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param(
                "ssddssss",
                $product_name,
                $product_description,
                $product_price,
                $product_weight,
                $product_material,
                $manufacturer_id,
                $product_id
            );
            $stmt->execute();
            
            // Delete existing category relationships
            $delete_categories = "DELETE FROM preke_kategorija WHERE fk_PREKEid = ?";
            $stmt = $conn->prepare($delete_categories);
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            
            // Insert new category relationships
            if (!empty($category_ids)) {
                $insert_category = "INSERT INTO preke_kategorija 
                                   (fk_PREKEid, fk_KATEGORIJAid_KATEGORIJA, pagrindine_kategorija, priskirimo_data) 
                                   VALUES (?, ?, ?, CURRENT_DATE)";
                $stmt = $conn->prepare($insert_category);
                
                foreach ($category_ids as $index => $cat_id) {
                    $is_primary = in_array($cat_id, $primary_categories) ? 1 : 0;
                    $stmt->bind_param("sii", $product_id, $cat_id, $is_primary);
                    $stmt->execute();
                }
            }
            
        } else {
            // Check if product ID already exists
            $check_query = "SELECT COUNT(*) as count FROM preke WHERE id = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $new_product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                throw new Exception("Product ID already exists. Please use a different ID.");
            }

            // Insert new product
            $insert_query = "INSERT INTO preke 
                           (id, pavadinimas, aprasymas, kaina, svoris, medziaga, fk_GAMINTOJASgamintojo_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param(
                "sssddss",
                $new_product_id,
                $product_name,
                $product_description,
                $product_price,
                $product_weight,
                $product_material,
                $manufacturer_id
            );
            $stmt->execute();
            
            // Set product_id for category inserts
            $product_id = $new_product_id;
            
            // Insert category relationships
            if (!empty($category_ids)) {
                $insert_category = "INSERT INTO preke_kategorija 
                                   (fk_PREKEid, fk_KATEGORIJAid_KATEGORIJA, pagrindine_kategorija, priskirimo_data) 
                                   VALUES (?, ?, ?, CURRENT_DATE)";
                $stmt = $conn->prepare($insert_category);
                
                foreach ($category_ids as $index => $cat_id) {
                    $is_primary = in_array($cat_id, $primary_categories) ? 1 : 0;
                    $stmt->bind_param("sii", $product_id, $cat_id, $is_primary);
                    $stmt->execute();
                }
            }
        }

        // Commit transaction
        $conn->commit();
        
        // Set success message and redirect
        $success_message = "Product saved successfully.";
        
        // Reload page with the saved product ID if we're adding a new product
        if (!$is_edit_mode) {
            header("Location: products_edit.php?id=" . $new_product_id . "&success=1");
            exit;
        }
        
        // Refresh data for edit mode
        if ($is_edit_mode) {
            // Refresh product data
            $product_query = "SELECT * FROM preke WHERE id = ?";
            $stmt = $conn->prepare($product_query);
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product_data = $result->fetch_assoc();
            
            // Refresh product categories
            $product_categories = [];
            $categories_query = "SELECT pc.*, k.pavadinimas as category_name 
                                FROM preke_kategorija pc
                                JOIN kategorija k ON pc.fk_KATEGORIJAid_KATEGORIJA = k.id_KATEGORIJA
                                WHERE pc.fk_PREKEid = ?
                                ORDER BY pc.pagrindine_kategorija DESC, k.pavadinimas";
            $stmt = $conn->prepare($categories_query);
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            $categories_result2 = $stmt->get_result();
            
            while ($cat = $categories_result2->fetch_assoc()) {
                $product_categories[] = $cat;
            }
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Handle success messages from redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Product saved successfully.";
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <?php echo $is_edit_mode ? 'Edit Product' : 'Add New Product'; ?>
        </h1>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-box me-2"></i>Product Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Product ID</label>
                        <input type="text" class="form-control" id="product_id" name="product_id"
                            value="<?php echo htmlspecialchars($is_edit_mode ? $product_data['id'] : ''); ?>"
                            <?php echo $is_edit_mode ? 'readonly' : ''; ?> required>
                        <small class="form-text text-muted">Unique identifier for the product</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="manufacturer_id" class="form-label">Manufacturer</label>
                        <select class="form-select" id="manufacturer_id" name="manufacturer_id" required>
                            <option value="">Select a manufacturer</option>
                            <?php
                            $manufacturers_result->data_seek(0); // Reset pointer
                            while ($manufacturer = $manufacturers_result->fetch_assoc()):
                                $selected = ($is_edit_mode && $product_data['fk_GAMINTOJASgamintojo_id'] == $manufacturer['gamintojo_id']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $manufacturer['gamintojo_id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($manufacturer['pavadinimas']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="pavadinimas" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="pavadinimas" name="pavadinimas"
                            value="<?php echo htmlspecialchars($is_edit_mode ? $product_data['pavadinimas'] : ''); ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="aprasymas" class="form-label">Description</label>
                        <textarea class="form-control" id="aprasymas" name="aprasymas" rows="3"><?php echo htmlspecialchars($is_edit_mode ? $product_data['aprasymas'] : ''); ?></textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="kaina" class="form-label">Price (€)</label>
                        <input type="number" class="form-control" id="kaina" name="kaina" step="0.01" min="0"
                            value="<?php echo $is_edit_mode ? $product_data['kaina'] : ''; ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="svoris" class="form-label">Weight (g)</label>
                        <input type="number" class="form-control" id="svoris" name="svoris" step="0.01" min="0"
                            value="<?php echo $is_edit_mode ? $product_data['svoris'] : ''; ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="medziaga" class="form-label">Material</label>
                        <input type="text" class="form-control" id="medziaga" name="medziaga"
                            value="<?php echo htmlspecialchars($is_edit_mode ? $product_data['medziaga'] : ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-tags me-2"></i>Product Categories
                </span>
                <button type="button" class="btn btn-sm btn-light" id="add-category-row">
                    <i class="fas fa-plus-circle me-1"></i>Add Category
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    You can assign multiple categories to a product. Mark one category as primary.
                </div>

                <div id="categories-container">
                    <?php if (!empty($product_categories)): ?>
                        <?php foreach ($product_categories as $index => $category): ?>
                            <div class="row mb-3 category-row">
                                <div class="col-md-8">
                                    <select class="form-select" name="category_id[]" required>
                                        <?php
                                        $categories_result->data_seek(0); // Reset pointer
                                        while ($cat = $categories_result->fetch_assoc()):
                                            $selected = ($category['fk_KATEGORIJAid_KATEGORIJA'] == $cat['id_KATEGORIJA']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $cat['id_KATEGORIJA']; ?>" <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars($cat['pavadinimas']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input primary-check" type="checkbox" 
                                              name="primary_category[]" value="<?php echo $category['fk_KATEGORIJAid_KATEGORIJA']; ?>" 
                                              <?php echo $category['pagrindine_kategorija'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Primary</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-category-row mt-1">
                                        <i class="fas fa-trash me-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row mb-3 category-row">
                            <div class="col-md-8">
                                <select class="form-select" name="category_id[]" required>
                                    <option value="">Select category</option>
                                    <?php
                                    $categories_result->data_seek(0); // Reset pointer
                                    while ($cat = $categories_result->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $cat['id_KATEGORIJA']; ?>">
                                            <?php echo htmlspecialchars($cat['pavadinimas']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-2">
                                    <input class="form-check-input primary-check" type="checkbox" 
                                          name="primary_category[]" value="">
                                    <label class="form-check-label">Primary</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-category-row mt-1">
                                    <i class="fas fa-trash me-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <button type="submit" name="save_product" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Save Product
            </button>
            <a href="products.php" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add new category row
    document.getElementById('add-category-row').addEventListener('click', function() {
        const container = document.getElementById('categories-container');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 category-row';

        // Create category options
        let categoryOptions = '<option value="">Select category</option>';
        <?php
        $categories_result->data_seek(0); // Reset pointer
        while ($cat = $categories_result->fetch_assoc()):
        ?>
            categoryOptions += `<option value="<?php echo $cat['id_KATEGORIJA']; ?>"><?php echo htmlspecialchars($cat['pavadinimas']); ?></option>`;
        <?php endwhile; ?>

        newRow.innerHTML = `
            <div class="col-md-8">
                <select class="form-select" name="category_id[]" required>
                    ${categoryOptions}
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check mt-2">
                    <input class="form-check-input primary-check" type="checkbox" name="primary_category[]" value="">
                    <label class="form-check-label">Primary</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-category-row mt-1">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
        `;
        container.appendChild(newRow);

        // Add event listeners to the new row
        addCategoryRowEventListeners(newRow);
    });

    // Function to add event listeners to category rows
    function addCategoryRowEventListeners(row) {
        // Remove category row
        const removeButton = row.querySelector('.remove-category-row');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                row.parentNode.removeChild(row);
            });
        }

        // Update checkbox value when category is selected
        const categorySelect = row.querySelector('select[name="category_id[]"]');
        const primaryCheck = row.querySelector('.primary-check');
        
        if (categorySelect && primaryCheck) {
            categorySelect.addEventListener('change', function() {
                primaryCheck.value = this.value;
            });
        }
    }

    // Add event listeners to existing category rows
    document.querySelectorAll('.category-row').forEach(row => {
        addCategoryRowEventListeners(row);
    });
});
</script>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>