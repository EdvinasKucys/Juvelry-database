<?php
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="jumbotron">
        <h1>Juvelyrikos inventoriaus duomenys</h1>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Kategorijos</h5>
                    <p class="card-text">Kategorijų peržiūra, sudarimas redagavimas</p>
                    <a href="/juvelyrika/pages/categories.php" class="btn btn-primary">Atidaryti</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gamintojai</h5>
                    <p class="card-text">gamintojų peržiūra, sudarimas redagavimas</p>
                    <a href="/juvelyrika/pages/manufacturers.php" class="btn btn-primary">Atidaryti</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Products</h5>
                    <p class="card-text">View and manage products</p>
                    <a href="/juvelyrika/pages/products.php" class="btn btn-primary">Atidaryti</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Inventory</h5>
                    <p class="card-text">Manage warehouse inventory</p>
                    <a href="/juvelyrika/pages/inventory.php" class="btn btn-primary">Atidaryti</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
?>