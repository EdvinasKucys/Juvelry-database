<?php
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="jumbotron">
        <h1>Jewelry Inventory System</h1>
        <p class="lead">Manage your jewelry inventory with multiple category support</p>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <p class="card-text">Manage product categories for your jewelry inventory</p>
                    <a href="/juvelyrika/pages/categories.php" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manufacturers</h5>
                    <p class="card-text">Manage jewelry manufacturers information</p>
                    <a href="/juvelyrika/pages/manufacturers.php" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Products</h5>
                    <p class="card-text">Manage your jewelry products with multi-category support</p>
                    <a href="/juvelyrika/pages/products.php" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>