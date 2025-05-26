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
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 32px auto 0 auto;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
            padding: 32px 24px;
        }
        .swiper, .swiper-container {
            width: 100%;
            padding: 20px 0 10px 0;
            min-height: 70px;
        }
        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #f3f4f6;
            border-radius: 16px;
            box-shadow: 0 2px 8px #e0e7ff;
            padding: 12px 28px;
            cursor: pointer;
            margin: 0 4px;
            color: #122c6f;
            font-weight: 600;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            border: 2px solid transparent;
        }
        .swiper-slide.active {
            background: linear-gradient(90deg, #122c6f 60%, #f13b1c 100%);
            color: #fff;
            border: 2px solid #f13b1c;
            box-shadow: 0 4px 16px #f13b1c33;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            margin-top: 32px;
            justify-content: center;
        }
        .product-card {
            width: 250px;
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 rgba(31, 38, 135, 0.10);
            padding: 20px 16px 24px 16px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative;
        }
        .product-card:hover {
            box-shadow: 0 8px 32px 0 rgba(241,59,28,0.15);
            transform: translateY(-4px) scale(1.03);
        }
        .product-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 12px;
            background: #f3f4f6;
        }
        .product-card h3 {
            margin: 10px 0 6px 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: #122c6f;
            text-align: center;
        }
        .product-card .price {
            color: #f13b1c;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .product-card .mrp {
            text-decoration: line-through;
            color: #888;
            font-size: 0.95em;
            margin-left: 8px;
        }
        .product-card div:last-child {
            margin-top: 10px;
            color: #555;
            font-size: 0.98rem;
            text-align: center;
        }
        @media (max-width: 900px) {
            .container {
                padding: 12px 2vw;
            }
            .products {
                gap: 18px;
            }
            .product-card {
                width: 95vw;
                max-width: 340px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
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