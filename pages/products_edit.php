<?php
include '../includes/header.php';
include '../includes/db_connect.php';

// Initialize variables
$product_id = isset($_GET['id']) ? $_GET['id'] : '';
$is_edit_mode = !empty($product_id);
$success_message = $error_message = '';
$product_data = [];
$inventory_items = [];

// Fetch categories and manufacturers for dropdowns
$categories_query = "SELECT id_KATEGORIJA, pavadinimas FROM kategorija ORDER BY id_KATEGORIJA";
$categories_result = $conn->query($categories_query);

$manufacturers_query = "SELECT gamintojo_id, pavadinimas FROM gamintojas ORDER BY gamintojo_id";
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
    } else {
        $error_message = "Product not found.";
        $is_edit_mode = false;
    }

    // Fetch inventory items for this product
    $inventory_query = "SELECT * FROM sandeliuojama_preke WHERE fk_PREKEid = ?";
    $stmt = $conn->prepare($inventory_query);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $inventory_result = $stmt->get_result();

    while ($item = $inventory_result->fetch_assoc()) {
        $inventory_items[] = $item;
    }
}

// Handle form submission
if (isset($_POST['save_product'])) {
    // Get form data
    $new_product_id = $_POST['product_id'];
    $product_name = $_POST['pavadinimas'];
    $product_description = $_POST['aprasymas'];
    $product_price = $_POST['kaina'];
    $product_weight = $_POST['svoris'];
    $product_material = $_POST['medziaga'];
    $category_id = $_POST['category_id'];
    $manufacturer_id = $_POST['manufacturer_id'];

    // Get inventory items from form
    $inventory_ids = isset($_POST['inventory_id']) ? $_POST['inventory_id'] : [];
    $inventory_quantities = isset($_POST['inventory_quantity']) ? $_POST['inventory_quantity'] : [];

    // Start transaction
    $conn->begin_transaction();

    try {
        if ($is_edit_mode) {
            // Update existing product
            $update_query = "UPDATE preke SET pavadinimas = ?, aprasymas = ?, kaina = ?, 
                            svoris = ?, medziaga = ?, fk_GAMINTOJASgamintojo_id = ?, 
                            fk_KATEGORIJAid_KATEGORIJA = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param(
                "ssddssss",
                $product_name,
                $product_description,
                $product_price,
                $product_weight,
                $product_material,
                $manufacturer_id,
                $category_id,
                $product_id
            );
            $stmt->execute();

            // Handle inventory items
            foreach ($inventory_ids as $index => $inv_id) {
                if (empty($inv_id)) {
                    // This is a new inventory item
                    if (isset($inventory_quantities[$index]) && $inventory_quantities[$index] !== '') {
                        $insert_inventory = "INSERT INTO sandeliuojama_preke (kiekis, fk_PREKEid) VALUES (?, ?)";
                        $stmt = $conn->prepare($insert_inventory);
                        $stmt->bind_param("is", $inventory_quantities[$index], $product_id);
                        $stmt->execute();
                    }
                } else {
                    // This is an existing inventory item
                    if (isset($_POST['delete_inventory'][$index]) && $_POST['delete_inventory'][$index] == 1) {
                        // Delete this inventory item
                        $delete_inventory = "DELETE FROM sandeliuojama_preke WHERE id_SANDELIUOJAMA_PREKE = ?";
                        $stmt = $conn->prepare($delete_inventory);
                        $stmt->bind_param("i", $inv_id);
                        $stmt->execute();
                    } else {
                        // Update this inventory item
                        $update_inventory = "UPDATE sandeliuojama_preke SET kiekis = ? WHERE id_SANDELIUOJAMA_PREKE = ?";
                        $stmt = $conn->prepare($update_inventory);
                        $stmt->bind_param("ii", $inventory_quantities[$index], $inv_id);
                        $stmt->execute();
                    }
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
            $insert_query = "INSERT INTO preke (id, pavadinimas, aprasymas, kaina, svoris, medziaga, 
                            fk_GAMINTOJASgamintojo_id, fk_KATEGORIJAid_KATEGORIJA) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param(
                "sssddssi",
                $new_product_id,
                $product_name,
                $product_description,
                $product_price,
                $product_weight,
                $product_material,
                $manufacturer_id,
                $category_id
            );
            $stmt->execute();

            // Set product_id for inventory inserts
            $product_id = $new_product_id;

            // Process all non-empty inventory quantities
            foreach ($inventory_quantities as $index => $quantity) {
                if ($quantity !== '' && $quantity !== null && $quantity > 0) {
                    $insert_inventory = "INSERT INTO sandeliuojama_preke (kiekis, fk_PREKEid) VALUES (?, ?)";
                    $stmt = $conn->prepare($insert_inventory);
                    $stmt->bind_param("is", $quantity, $product_id);
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
            $product_query = "SELECT * FROM preke WHERE id = ?";
            $stmt = $conn->prepare($product_query);
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product_data = $result->fetch_assoc();

            // Refresh inventory items
            $inventory_items = [];
            $inventory_query = "SELECT * FROM sandeliuojama_preke WHERE fk_PREKEid = ?";
            $stmt = $conn->prepare($inventory_query);
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            $inventory_result = $stmt->get_result();

            while ($item = $inventory_result->fetch_assoc()) {
                $inventory_items[] = $item;
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
        <h1 class="display-5">
            <i class="fas fa-<?php echo $is_edit_mode ? 'edit' : 'plus-circle'; ?> me-2"></i>
            <?php echo $is_edit_mode ? 'Edit Product' : 'Add New Product'; ?>
        </h1>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-box-open me-2"></i>Product Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">
                            <i class="fas fa-fingerprint me-1"></i>Product ID
                        </label>
                        <input type="text" class="form-control" id="product_id" name="product_id"
                            value="<?php echo htmlspecialchars($is_edit_mode ? $product_data['id'] : ''); ?>"
                            <?php echo $is_edit_mode ? 'readonly' : ''; ?> required>
                        <small class="form-text text-muted">Unique identifier for the product</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="pavadinimas" class="form-label">
                            <i class="fas fa-tag me-1"></i>Name
                        </label>
                        <input type="text" class="form-control" id="pavadinimas" name="pavadinimas"
                            value="<?php echo htmlspecialchars($is_edit_mode ? $product_data['pavadinimas'] : ''); ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="aprasymas" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" id="aprasymas" name="aprasymas" rows="3"><?php echo htmlspecialchars($is_edit_mode ? $product_data['aprasymas'] : ''); ?></textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="kaina" class="form-label">
                            <i class="fas fa-euro-sign me-1"></i>Price
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="kaina" name="kaina" step="0.01" min="0"
                                value="<?php echo $is_edit_mode ? $product_data['kaina'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="svoris" class="form-label">
                            <i class="fas fa-weight me-1"></i>Weight
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="svoris" name="svoris" step="0.01" min="0"
                                value="<?php echo $is_edit_mode ? $product_data['svoris'] : ''; ?>" required>
                            <span class="input-group-text">g</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="medziaga" class="form-label">
                            <i class="fas fa-gem me-1"></i>Material
                        </label>
                        <input type="text" class="form-control" id="medziaga" name="medziaga"
                            value="<?php echo htmlspecialchars($is_edit_mode ? $product_data['medziaga'] : ''); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">
                            <i class="fas fa-list me-1"></i>Category
                        </label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php while ($category = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $category['id_KATEGORIJA']; ?>"
                                    <?php echo ($is_edit_mode && $product_data['fk_KATEGORIJAid_KATEGORIJA'] == $category['id_KATEGORIJA']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['pavadinimas']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="manufacturer_id" class="form-label">
                            <i class="fas fa-industry me-1"></i>Manufacturer
                        </label>
                        <select class="form-select" id="manufacturer_id" name="manufacturer_id" required>
                            <option value="">Select a manufacturer</option>
                            <?php while ($manufacturer = $manufacturers_result->fetch_assoc()): ?>
                                <option value="<?php echo $manufacturer['gamintojo_id']; ?>"
                                    <?php echo ($is_edit_mode && $product_data['fk_GAMINTOJASgamintojo_id'] == $manufacturer['gamintojo_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($manufacturer['pavadinimas']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-warehouse me-2"></i>Inventory Information</span>
                <button type="button" class="btn btn-sm btn-light" id="add-inventory-row">
                    <i class="fas fa-plus-circle me-1"></i>Add Inventory
                </button>
            </div>
            <div class="card-body">
                <div id="inventory-container">
                    <?php if (!empty($inventory_items)): ?>
                        <?php foreach ($inventory_items as $index => $item): ?>
                            <div class="row mb-3 inventory-row">
                                <input type="hidden" name="inventory_id[]" value="<?php echo $item['id_SANDELIUOJAMA_PREKE']; ?>">
                                <div class="col-md-6">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="inventory_quantity[]"
                                        value="<?php echo $item['kiekis']; ?>" min="0" required>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" name="delete_inventory[<?php echo $index; ?>]" value="1" id="delete-inventory-<?php echo $index; ?>">
                                        <label class="form-check-label" for="delete-inventory-<?php echo $index; ?>">
                                            Delete
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row mb-3 inventory-row">
                            <input type="hidden" name="inventory_id[]" value="">
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="inventory_quantity[]" min="0">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger remove-inventory-row">
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
        // Add new inventory row
        document.getElementById('add-inventory-row').addEventListener('click', function() {
            const container = document.getElementById('inventory-container');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-3 inventory-row';
            newRow.innerHTML = `
            <input type="hidden" name="inventory_id[]" value="">
            <div class="col-md-6">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="inventory_quantity[]" min="0">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-danger remove-inventory-row">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
        `;
            container.appendChild(newRow);

            // Add event listener to the new remove button
            newRow.querySelector('.remove-inventory-row').addEventListener('click', function() {
                container.removeChild(newRow);
            });
        });

        // Remove inventory row
        document.querySelectorAll('.remove-inventory-row').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.inventory-row');
                row.parentNode.removeChild(row);
            });
        });
    });
</script>

<?php
$conn->close();
?>