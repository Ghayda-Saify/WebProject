<?php
global $cart_count;
session_start();
include '../connection.php';
global $con;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

    <link
            href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
            rel="stylesheet"
    />
    <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet"
    />
    <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
    />
    <style>
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        body {
            padding-top: 0px;
        }
        @media (max-width: 900px) {
            body {
                padding-top: 60px;
            }
        }
        .swiper {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .swiper-slide {
            background-color: #eee;
            border-radius: 12px;
            text-align: center;
            font-size: 18px;
            padding: 40px 0;
            width: auto;
        }
        .category__title {
            transition: color 0.3s ease;
        }
        .category__img {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            object-fit: contain;
            background-color: #fff;
            transition: transform 0.3s ease;
        }
        .category__item:hover .category__img {
            transform: scale(1.05);
        }
        .category__item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: none;
            box-shadow: none;         /* Remove any shadow */
            padding: 0;               /* Remove any extra space */
            border-radius: 0;         /* Remove rounded corners */
            overflow: hidden;
        }
        .category__item:hover .category__title {
            color: #f9dd81; /* or your brand color */
        }
        .category__item:hover {
            transform: scale(1.05); /* slightly zoom in */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* add a soft shadow */
            z-index: 2;
        }
        .fancy-bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .fancy-bubbles::before, .fancy-bubbles::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 20s linear infinite;
        }

        .fancy-bubbles::before {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
        }

        .fancy-bubbles::after {
            width: 200px;
            height: 200px;
            bottom: 10%;
            right: 10%;
        }

        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
            100% { transform: translateY(0); }
        }


    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: "#122c6f", secondary: "#f13b1c" },
                    borderRadius: {
                        none: "0px",
                        sm: "4px",
                        DEFAULT: "8px",
                        md: "12px",
                        lg: "16px",
                        xl: "20px",
                        "2xl": "24px",
                        "3xl": "32px",
                        full: "9999px",
                        button: "8px",
                    },
                },
            },
        };
    </script>


</head>
<body>
<div class="fancy-bubbles"></div>

<header>
    <a href="index.php" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
    <button class="mobile-menu-btn" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php" class="text-primary font-bold">Home</a></li>
            <li><a href="../ProductsPage/product.php">Products</a></li>
            <li><a href="../ContactPage/contact.php">Connect</a></li>
            <li>
                <a href="../CartPage/cart.php" class="relative">
                    <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count"><?php echo $cart_count; ?></span>
                </a>
            </li>
            <li>
                <?php
                if (isset($_SESSION['user'])) {
                    echo '<a href="../ProfilePage/profile.php" class="flex items-center gap-2"><i class="fa-solid fa-user text-primary"></i><span class="hidden md:inline">Welcome, ' . $_SESSION['user']['name'] . '</span></a>';
                } else {
                    echo '<a href="../SignIn&Up/sign.php" class="btn btn-primary">Sign In</a>';
                }
                ?>
            </li>
        </ul>
    </nav>
    <div class="menu-overlay"></div>
</header>
<main>
    <!--        Home-->
    <section class="carousel next">
        <div class="list">
            <?php
            // Fetch products for the carousel (e.g., latest 4)
            $carousel_sql = "SELECT * FROM product WHERE id BETWEEN 16 AND 19 Order By id ASC LIMIT 4";
            $carousel_result = $con->query($carousel_sql);

            $bg_colors = ['#ADB5AA', '#AF9864', '#D5CEB1','#ffffff'];
            $item_index = 0;

            if ($carousel_result && $carousel_result->num_rows > 0) {
                while ($product = $carousel_result->fetch_assoc()) {
                    $item_class = '';
                    if ($item_index === 0) {
                        $item_class = 'active';
                    } elseif ($item_index === 1) {
                        $item_class = 'other_1';
                    } elseif ($item_index === 2) {
                        $item_class = 'other_2';
                    }

                    $bg_color = $bg_colors[$item_index % count($bg_colors)];
                    ?>
                    <article class="item <?php echo $item_class; ?>" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                        <div class="main-content" style="background-color: <?php echo htmlspecialchars($bg_color); ?>;" data-bg="<?php echo htmlspecialchars($bg_color); ?>">
                            <div class="content">
                                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                                <p class="price">₪<?php echo number_format($product['price'], 2); ?></p>
                                <p class="description" style="text-align: left">
                                    <?php echo htmlspecialchars($product['description'] ?? 'PERSONALIZED CREATIONS, CRAFTED FOR YOU'); // Assuming description column or using default ?>
                                </p>
                                <button class="addToCard" style="background-color: #122c6f;">
                                    Add To Card
                                </button>
                            </div>
                        </div>
                        <figure class="image">
                            <img src="imgs/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <figcaption><?php echo htmlspecialchars($product['description'] ?? 'PERSONALIZED CREATIONS, CRAFTED FOR YOU'); ?></figcaption>
                        </figure>
                    </article>
                    <?php
                    $item_index++;
                }
            } else {
                // Display placeholder items or a message if no products are found
                ?>
                 <article class="item active">
                     <div class="main-content" style="background-color: #ffffff;" data-bg="#ffffff">
                         <div class="content">
                             <h2>No Products Yet</h2>
                             <p class="price"></p>
                             <p class="description">Check back later for new arrivals!</p>
                             <button class="addToCard" style="background-color: #122c6f;">Browse Products</button>
                         </div>
                     </div>
                     <figure class="image">
                         <img src="imgs/default_placeholder.png" alt="Placeholder Image">
                         <figcaption>No products available at the moment.</figcaption>
                     </figure>
                 </article>
                <?php
            }
            ?>
        </div>
        <div class="arrows">
            <button id="prev" style="--tw-text-opacity:1;color: rgb(18 44 111 / var(--tw-text-opacity, 1))"><</button>
            <button id="next" style="--tw-text-opacity:1;color: rgb(18 44 111 / var(--tw-text-opacity, 1))">></button>
        </div>
    </section>

    <!--      categories  -->
    <!-- Featured Categories -->
    <?php
        include_once '../connection.php';
        global $con;
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }
        $sql = "SELECT id, name, image FROM categories";
        $result = $con->query($sql);

    ?>
    <section class="">
        <h3 class="section__title "><span style="color : #122c6f">Popular</span> Categories</h3>
        <p class="text-gray-600 text-center mb-12">
            Discover our range of customizable products
        </p>
        <!-- Slider main container -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <a href="../ProductsPage/product.php?category_id=<?php echo $row['id']; ?>" class="swiper-slide category__item">
                                <img src="imgs/<?php echo htmlspecialchars($row['image']); ?>" class="category__img" alt="">
                                <h3 class="category__title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            </a>
                        <?php } ?>
                    </div>

                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </div>

    </section>

    <!--        Products-->
    <!-- Featured Products -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-2">Featured Products</h2>
            <p class="text-gray-600 text-center mb-12">
                Discover our most popular items
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                // Fetch specific featured products by ID range
                $product_sql = "SELECT * FROM product WHERE id BETWEEN 6 AND 9 ORDER BY id ";
                $product_result = $con->query($product_sql);
                if ($product_result && $product_result->num_rows > 0) {
                    while ($product = $product_result->fetch_assoc()) {
                    ?>
                    <div class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                        <div class="relative">
                            <div class="h-64 overflow-hidden">
                                <img
                                    src="imgs/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="w-full h-full object-cover object-top"
                                />
                            </div>
                            <div class="quick-actions absolute top-4 right-4 flex flex-col gap-2">
                                <button class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100">
                                    <i class="ri-heart-line text-gray-700"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100">
                                    <i class="ri-eye-line text-gray-700"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-medium mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400">
                                    <i class="ri-star-fill ri-xs"></i>
                                    <i class="ri-star-fill ri-xs"></i>
                                    <i class="ri-star-fill ri-xs"></i>
                                    <i class="ri-star-fill ri-xs"></i>
                                    <i class="ri-star-line ri-xs"></i>
                                </div>
                                <span class="text-xs text-gray-500 ml-1">(0)</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-primary font-semibold">₪<?php echo number_format($product['price'], 2); ?></span>
                                <button class="addToCard bg-primary text-white py-2 px-3 rounded-button text-sm hover:bg-opacity-90 transition-colors whitespace-nowrap">
                                    Add to Cart
                                </button>

                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-span-4 text-center text-gray-500">No featured products found with IDs 15-18.</div>';
            }
            ?>
        </div>
        <div class="text-center mt-10">
            <a
                href="../ProductsPage/product.php"
                class="inline-block border-2 border-primary text-primary px-6 py-3 rounded-button font-medium hover:bg-primary hover:text-white transition-colors whitespace-nowrap"
            >View All Products</a>
        </div>
    </div>
</section>
    <!--        deals-->
    <section class="deals">
        <!-- Promotional Banner -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php
                    // Fetch active promotions from the database
                    $promo_sql = "SELECT * FROM promotions WHERE active = 1 ORDER BY id DESC LIMIT 2";
                    $promo_result = $con->query($promo_sql);
                    if ($promo_result && $promo_result->num_rows > 0) {
                        while ($promo = $promo_result->fetch_assoc()) {
                            $discount = '';
                            if (!empty($promo['discount_value'])) {
                                if (isset($promo['discount_type']) && $promo['discount_type'] === 'amount') {
                                    $discount = 'Discount: ₪' . $promo['discount_value'];
                                } else {
                                    $discount = 'Discount: ' . $promo['discount_value'] . '%';
                                }
                            }
                            ?>
                            <div class="relative h-80 rounded-lg overflow-hidden" style="background: linear-gradient(to right, <?php echo htmlspecialchars($promo['color'] ?? '#122c6f'); ?>99, <?php echo htmlspecialchars($promo['color'] ?? '#122c6f'); ?>66), url('<?php echo htmlspecialchars($promo['image_url']); ?>'); background-size: cover; background-position: center;">
                                <div class="absolute inset-0 flex items-center" style="background: linear-gradient(to right, <?php echo htmlspecialchars($promo['color'] ?? '#122c6f'); ?>/80, <?php echo htmlspecialchars($promo['color'] ?? '#122c6f'); ?>/40);">
                                    <div class="p-8">
                                        <h3 class="text-white text-2xl font-bold mb-2">
                                            <?php echo htmlspecialchars($promo['title']); ?>
                                        </h3>
                                        <p class="text-white text-sm mb-4">
                                            <?php echo htmlspecialchars($promo['subtitle']); ?>
                                        </p>
                                        <?php if ($discount) { ?>
                                            <div class="text-white text-lg font-semibold mb-2"><?php echo $discount; ?></div>
                                        <?php } ?>
                                        <a
                                            href="<?php echo htmlspecialchars($promo['btn_link']); ?>"
                                            class="bg-white text-primary px-4 py-2 rounded-button text-sm font-medium hover:bg-gray-100 transition-colors inline-block whitespace-nowrap"
                                        ><?php echo htmlspecialchars($promo['btn_text']); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-span-2 text-center text-gray-500">No promotions available.</div>';
                    }
                    ?>
                </div>
            </div>
        </section>

    </section>
    <!--        feedback-->
    <!-- Testimonials -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-2">
                What Our Customers Say
            </h2>
            <p class="text-gray-600 text-center mb-12">
                Hear from our satisfied customers
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex text-yellow-400 mb-4">
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                    </div>
                    <p class="text-gray-700 mb-6">
                        "The quality of their furniture is exceptional. I purchased the
                        Modern Armchair and it has transformed my living room. The
                        customer service was also outstanding."
                    </p>
                    <div class="flex items-center">
                        <div
                                class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4"
                        >
                            <img
                                    src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20middle-aged%20woman%20with%20short%20brown%20hair%2C%20smiling%2C%20neutral%20background%2C%20high%20quality%20portrait&width=100&height=100&seq=15&orientation=squarish"
                                    alt="Emily Robertson"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div>
                            <h4 class="font-medium">Emily Robertson</h4>
                            <p class="text-gray-500 text-sm">New York, USA</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex text-yellow-400 mb-4">
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                    </div>
                    <p class="text-gray-700 mb-6">
                        "I've ordered multiple items from Alandalus Design and have
                        never been disappointed. Their attention to detail and
                        craftsmanship is unmatched. Highly recommend!"
                    </p>
                    <div class="flex items-center">
                        <div
                                class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4"
                        >
                            <img
                                    src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20young%20man%20with%20glasses%20and%20dark%20hair%2C%20smiling%2C%20neutral%20background%2C%20high%20quality%20portrait&width=100&height=100&seq=16&orientation=squarish"
                                    alt="Michael Chen"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div>
                            <h4 class="font-medium">Michael Chen</h4>
                            <p class="text-gray-500 text-sm">San Francisco, USA</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex text-yellow-400 mb-4">
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-half-fill"></i>
                    </div>
                    <p class="text-gray-700 mb-6">
                        "The pendant lamp I purchased exceeded my expectations. It's not
                        just a lighting fixture but a piece of art. Delivery was prompt
                        and packaging was secure."
                    </p>
                    <div class="flex items-center">
                        <div
                                class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4"
                        >
                            <img
                                    src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20woman%20with%20curly%20hair%2C%20smiling%2C%20neutral%20background%2C%20high%20quality%20portrait&width=100&height=100&seq=17&orientation=squarish"
                                    alt="Sophia Martinez"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div>
                            <h4 class="font-medium">Sophia Martinez</h4>
                            <p class="text-gray-500 text-sm">London, UK</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Map + Footer -->
    <footer class="bg-gray-100 mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="col-span-1 md:col-span-2">
                    <a href="index.php" class="text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
                    <p class="mt-4 text-gray-600">Crafting personalized Arabic and Islamic designs that tell your unique story. Every piece is created with love and attention to detail.</p>
                    <div class="mt-6 flex space-x-4">
                        <a href="https://www.facebook.com/Al.Andalus.Design" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Follow us on Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/andalus_design" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Follow us on Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://t.me/andalusdesign" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Join our Telegram Channel">
                            <i class="fab fa-telegram"></i>
                        </a>
                        <a href="https://wa.me/message/7YZUEAMKO53SM1" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-secondary transition" title="Contact us on WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-600 hover:text-primary transition">Home</a></li>
                        <li><a href="../ProductsPage/product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                        <li><a href="../ContactPage/contact.php" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                        <li><a href="../CartPage/cart.php" class="text-gray-600 hover:text-primary transition">Cart</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-envelope w-6"></i>
                            <span>info@alandalusdesign.com</span>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-phone w-6"></i>
                            <span>+972 59-464-6503</span>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-6"></i>
                            <span>Sufyan Street - Raddad Building, Nablus 009709</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-600">
                <p>&copy; 2024 Alandalus Design. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- Back to Top Button -->
    <button
            id="back-to-top"
            class="fixed bottom-6 right-6 w-12 h-12 bg-primary text-white rounded-full shadow-lg flex items-center justify-center opacity-0 invisible transition-all duration-300"
    >
        <i class="ri-arrow-up-line ri-lg"></i>
    </button>
</main>



<script src="app.js" defer></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!--<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>-->

<script>
    const swiper = new Swiper('.mySwiper', {
        slidesPerView: 4,
        spaceBetween: 20,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        loop: true,
    });

    // // Initialize cart count from localStorage
    // document.addEventListener('DOMContentLoaded', function() {
    //     const cart = JSON.parse(localStorage.getItem('cart')) || [];
    //     const cartCount = document.querySelector('.cart-count');
    //     cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    // });

</script>
<script>
    const swiper = new Swiper('.categories__container', {
        loop: true,
        autoplay: {
            delay: 2500, // Time between slides in ms
            disableOnInteraction: false, // Keep autoplay after user interaction
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        slidesPerView: 3, // You can change this based on your layout
        spaceBetween: 20,
        breakpoints: {
            768: { slidesPerView: 3 },
            480: { slidesPerView: 2 },
            0: { slidesPerView: 1 },
        },
    });
</script>

</body>
</html>