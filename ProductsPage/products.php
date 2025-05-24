<?php
global $con;
require_once '../connection.php';

// Fetch categories
$categories = [];
$cat_sql = "SELECT * FROM categories WHERE status = 1";
$cat_res = $con->query($cat_sql);
while ($row = $cat_res->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch products for the first category
$first_cat_id = $categories[0]['id'] ?? 0;
$products = [];
if ($first_cat_id) {
    $prod_sql = "SELECT * FROM product WHERE status = 1 AND category_id = $first_cat_id";
    $prod_res = $con->query($prod_sql);
    while ($row = $prod_res->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
        .swiper, .swiper-container { width: 100%; padding: 20px 0; min-height: 70px; }
        .swiper-slide { text-align: center; font-size: 18px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 10px 20px; cursor: pointer; }
        .swiper-slide.active { background: #122c6f; color: #fff; }
        .products { display: flex; flex-wrap: wrap; gap: 24px; margin-top: 32px; }
        .product-card { width: 220px; border: 1px solid #eee; border-radius: 12px; box-shadow: 0 2px 8px #eee; padding: 16px; background: #fff; }
        .product-card img { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; }
        .product-card h3 { margin: 10px 0 6px 0; font-size: 1.1rem; }
        .product-card .price { color: #f13b1c; font-weight: bold; }
        .product-card .mrp { text-decoration: line-through; color: #888; font-size: 0.9em; margin-left: 8px; }
    </style>
</head>
<body>
    <!-- Categories Slider -->
    <div class="swiper swiper-container">
        <div class="swiper-wrapper">
            <?php foreach ($categories as $cat): ?>
                <div class="swiper-slide<?= $cat['id'] == $first_cat_id ? ' active' : '' ?>" data-id="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Products Section -->
    <div id="products" class="products">
        <?php foreach ($products as $prod): ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                <h3><?= htmlspecialchars($prod['name']) ?></h3>
                <div>
                    <span class="price">$<?= htmlspecialchars($prod['price']) ?></span>
                    <span class="mrp">$<?= htmlspecialchars($prod['mrp']) ?></span>
                </div>
                <div><?= htmlspecialchars($prod['short_desc']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Swiper init
        const swiper = new Swiper('.swiper', {
            slidesPerView: 'auto',
            spaceBetween: 16,
        });

        // AJAX for category click
        document.querySelectorAll('.swiper-slide').forEach(slide => {
            slide.addEventListener('click', function() {
                document.querySelectorAll('.swiper-slide').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                const catId = this.getAttribute('data-id');
                fetch('get_products.php?category_id=' + catId)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('products').innerHTML = html;
                    });
            });
        });
    </script>
</body>
</html> 