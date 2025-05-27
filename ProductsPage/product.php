<?php
session_start();
include '../connection.php';
global $con;
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch categories for sidebar and slider
$sidebar_categories = [];
$slider_categories = [];
$cat_sql = "SELECT id, name,image FROM categories WHERE status = 1";
$cat_res = $con->query($cat_sql);
while ($row = $cat_res->fetch_assoc()) {
    $sidebar_categories[] = $row;
    $slider_categories[] = $row;
}

// Fetch cart count
//$session_id = session_id();
$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
$cart_count = 0;
$cart_sql = $user_id ?
    "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?" :
    "SELECT SUM(quantity) as total FROM cart WHERE session_id = ?";
$cart_stmt = $con->prepare($cart_sql);
$var = $user_id ;
$cart_stmt->bind_param("s", $var);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
if ($row = $cart_result->fetch_assoc()) {
    $cart_count = $row['total'] ?? 0;
}
$cart_stmt->close();

// Fetch wishlist count
$wishlist_count = 0;
$wishlist_sql = $user_id ?
    "SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?" :
    "SELECT COUNT(*) as total FROM wishlist WHERE session_id = ?";
$wishlist_stmt = $con->prepare($wishlist_sql);
$var2 = $user_id ;
$wishlist_stmt->bind_param("s", $var2);
$wishlist_stmt->execute();
$wishlist_result = $wishlist_stmt->get_result();
if ($row = $wishlist_result->fetch_assoc()) {
    $wishlist_count = $row['total'] ?? 0;
}
$wishlist_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Products</title>
    <link rel="stylesheet" href="../HomePage/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
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
        .category__container {
            width: 100%; /* Ensure container takes full width of parent */
            height: 100%; /* Fixed height for consistency */
            overflow: hidden; /* Hide any overflow if image exceeds */
            border-radius: 12px;
        }
        .category__img {
            width: 100%;
            height: 100%;
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
            box-shadow: none;
            padding: 0;
            border-radius: 0;
            overflow: hidden;
        }
        .category__item:hover .category__title {
            color: #f9dd81;
        }
        .category__item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            z-index: 2;
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
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            position: relative;
            overflow-x: hidden;
        }
        body::before, body::after {
            content: '';
            position: fixed;
            z-index: 0;
            border-radius: 50%;
            opacity: 0.18;
            pointer-events: none;
            filter: blur(2px);
        }
        body::before {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle at 30% 30%, #a5b4fc 60%, transparent 100%);
            top: -120px;
            left: -120px;
        }
        body::after {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle at 70% 80%, #fbbf24 60%, transparent 100%);
            bottom: -100px;
            right: -100px;
        }
             /* Edgy card styling with subtle shadow and border */
         .review-card {
             position: relative;
             overflow: hidden;
             background: linear-gradient(135deg, #ffffff 70%, #f9f9f9 100%);
             border-left: 4px solid rgba(18, 44, 111, 0.5);
             border-radius: 12px;
             transition: all 0.3s ease;
         }
        .review-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        /* Form input and textarea styling */
        #review-form textarea {
            background: #fafafa;
            transition: all 0.3s ease;
        }
        #review-form textarea:focus {
            background: #ffffff;
            box-shadow: inset 0 0 5px rgba(241, 59, 28, 0.2);
        }

        /* Decorative gradient overlays */
        .review-section .bg-gradient-to-br,
        .review-section .bg-gradient-to-tl {
            z-index: 0;
            opacity: 0.8;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .review-section h2 {
                font-size: 2rem;
            }
            .review-section .grid {
                /*gap: 6;*/
            }
            .review-section .bg-gradient-to-br,
            .review-section .bg-gradient-to-tl {
                display: none;
            }
        }

    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 
                        primary: "#122c6f", 
                        secondary: "#f13b1c",
                        beige: "#F5F5DC",
                        gold: "#FFD700"
                    }
                }
            }
        };
    </script>
</head>
<body class="font-poppins bg-beige/10 fancy-bubbles">
    <header>
    <a href="../HomePage/index.php" class="logo text-primary font-['Pacifico'] text-3xl">Alandalus Design</a>
        <nav class="main-nav flex items-center w-full">
        <div class="flex-1 min-w-[150px]"></div>
            <ul class="flex items-center justify-center gap-8">
                <li><a href="../HomePage/index.php">Home</a></li>
            <li><a href="product.php" class="text-primary font-bold">Products</a></li>
            <li><a href="../ContactPage/contact.php">Connect</a></li>
                <li>
                <a href="../CartPage/cart.php" class="relative">
                        <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count"><?php echo $cart_count; ?></span>
                    </a>
                </li>
                <li>
                <a href="../ProfilePage/profile.php">
                        <i class="fa-solid fa-user text-primary"></i>
                    </a>
                </li>
                <li>
                <a href="wishlist.php" class="relative" id="wishlist-icon">
                        <i class="fa-solid fa-heart text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center wishlist-count"><?php echo $wishlist_count; ?></span>
                    </a>
                </li>
            </ul>
        <div class="flex-1 flex justify-end">
                <div class="search-container flex items-center bg-white rounded-full border border-gray-200 px-3 py-1">
                    <input type="text" id="search-input" placeholder="Search products..." class="w-40 focus:outline-none text-sm">
                    <button id="search-button" class="ml-2">
                        <i class="fas fa-search text-gray-400 hover:text-primary transition"></i>
                    </button>
                    <div id="search-results" class="absolute top-full mt-2 w-64 bg-white rounded-lg shadow-lg hidden z-50 max-h-96 overflow-y-auto"></div>
                </div>
            </div>
        </nav>
    </header>

<!-- Categories Slider -->
<section >
    <h3 class="section__title"><span style="color: #122c6f">Popular</span> Categories</h3>
    <p class="text-gray-600 text-center mb-12">Discover our range of customizable products</p>
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
            <?php foreach ($slider_categories as $cat): ?>
                <a href="?category_id=<?php echo $cat['id']; ?>" class="swiper-slide category__item" data-id="<?php echo $cat['id']; ?>">
                    <img src="../HomePage/imgs/<?php echo htmlspecialchars($cat['image']); ?>" class="category__img" alt="">
                    <h3 class="category__title"><?php echo htmlspecialchars($cat['name']); ?></h3>
                </a>
            <?php endforeach; ?>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
        </section>

<main class="container mx-auto px-4 py-8 flex gap-8">
    <aside class="w-64 p-4 bg-white rounded-lg shadow-md h-fit sticky top-0 self-start mt-0">
        <h3 class="font-bold mb-4 text-lg text-primary">Categories</h3>
        <ul class="mb-8 max-h-64 overflow-y-auto">
            <?php foreach ($sidebar_categories as $cat): ?>
                <li>
                    <a href="#" class="block py-2 px-3 hover:bg-primary hover:text-white rounded category-link" data-category-id="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <h4 class="font-bold mb-2 text-primary">Filter by Price</h4>
        <select id="priceFilter" class="w-full border rounded p-2 mb-2">
            <option value="">All Prices</option>
            <option value="5-20">5-20 ₪</option>
            <option value="20-50">20-50 ₪</option>
            <option value="50-80">50-80 ₪</option>
            <option value="80+">More than 80 ₪</option>
        </select>
    </aside>

    <div class="flex-1">
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-2 font-aboreto">Our Products</h2>
            <p class="text-gray-600 text-center mb-12">Discover our collection of personalized creations</p>

            <div id="productContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 category__container"></div>
            <div class="text-center mt-8">
                <button id="loadMoreBtn" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition">
                    Load More Products
                </button>
            </div>
        </section>
    </div>
</main>

        <!-- Product Reviews Section -->
<section class="mt-16 bg-white container mx-auto px-4 py-16 relative overflow-hidden">
    <h2 class="text-4xl font-bold mb-12 text-center text-primary font-['Cairo'] tracking-wide">Customer Reviews</h2>
    <div class="flex justify-center">
        <div class="grid grid-cols-1 md:grid-cols-1 gap-10 max-w-5xl w-full">
                <!-- Review Form -->
            <div class="bg-white p-6 rounded-xl shadow-lg transform hover:scale-105 transition duration-300 border-l-4 border-primary/50">
                <h3 class="text-xl font-semibold mb-6 text-gray-800 font-['Tajawal']">Write a Review</h3>
                <form id="review-form" class="space-y-6">
                    <input type="hidden" id="review-product-id" name="product_id">
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex space-x-2">
                            <i class="far fa-star text-2xl cursor-pointer rating-star text-yellow-500 hover:text-yellow-600 transition" data-rating="1"></i>
                            <i class="far fa-star text-2xl cursor-pointer rating-star text-yellow-500 hover:text-yellow-600 transition" data-rating="2"></i>
                            <i class="far fa-star text-2xl cursor-pointer rating-star text-yellow-500 hover:text-yellow-600 transition" data-rating="3"></i>
                            <i class="far fa-star text-2xl cursor-pointer rating-star text-yellow-500 hover:text-yellow-600 transition" data-rating="4"></i>
                            <i class="far fa-star text-2xl cursor-pointer rating-star text-yellow-500 hover:text-yellow-600 transition" data-rating="5"></i>
                            </div>
                        </div>
                        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                        <textarea class="w-full border border-gray-300 rounded-lg p-4 focus:ring-2 focus:ring-secondary focus:border-transparent resize-none transition" rows="5" name="comment" placeholder="Share your thoughts about the product..."></textarea>
                        </div>
                    <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition-all duration-300 transform hover:-translate-y-1 font-['Poppins'] font-medium">
                            Submit Review
                        </button>
                    </form>
                </div>
                <!-- Review List -->
            <div id="review-list" class="space-y-6"></div>
                            </div>
                        </div>
    <!-- Edgy Decorative Element -->
    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/20 to-transparent transform -rotate-12 -translate-y-1/4 -translate-x-1/4"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tl from-secondary/20 to-transparent transform rotate-12 translate-y-1/4 -translate-x-1/4"></div>
        </section>



        <!-- Product Detail Modal -->
        <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-end mb-4">
                    <button class="text-gray-400 hover:text-gray-600" id="close-modal">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <div class="mb-4 relative">
                            <img src="" alt="" id="modal-main-image" class="w-full h-auto rounded-lg shadow-lg">
                        </div>
                <div class="grid grid-cols-4 gap-4" id="modal-thumbnails"></div>
                        </div>
                    <div>
                        <h2 id="modal-product-name" class="text-3xl font-bold mb-4"></h2>
                        <p id="modal-product-price" class="text-2xl text-primary font-bold mb-4"></p>
                        <p id="modal-product-description" class="text-gray-600 mb-6"></p>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Select Design Style:</h3>
                            <div class="grid grid-cols-2 gap-4">
                        <button class="design-option border rounded-lg p-4 hover:border-primary transition" data-design="arabic">Arabic Calligraphy</button>
                        <button class="design-option border rounded-lg p-4 hover:border-primary transition" data-design="geometric">Geometric Pattern</button>
                        <button class="design-option border rounded-lg p-4 hover:border-primary transition" data-design="modern">Modern Arabic</button>
                        <button class="design-option border rounded-lg p-4 hover:border-primary transition" data-design="custom">Custom Design</button>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Quantity:</h3>
                            <div class="flex items-center space-x-4">
                                <button class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:border-primary transition" id="modal-decrease-quantity">-</button>
                                <input type="number" value="1" min="1" class="w-20 text-center border-gray-300 rounded" id="modal-quantity">
                                <button class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:border-primary transition" id="modal-increase-quantity">+</button>
                            </div>
                        </div>
                <button id="modal-add-to-cart" class="w-full bg-primary text-white px-8 py-3 rounded-full text-lg font-medium hover:bg-opacity-90 transition flex items-center justify-center" data-product-id="">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Add To Cart | أضف إلى السلة
                        </button>
                <button id="modal-add-to-wishlist" class="w-full bg-secondary text-white px-8 py-3 rounded-full text-lg font-medium hover:bg-opacity-90 transition flex items-center justify-center mt-4" data-product-id="">
                    <i class="fas fa-heart mr-2"></i>
                    Add To Wishlist
                </button>
                    </div>
                </div>
            </div>
        </div>

    <!-- Footer -->
    <footer class="bg-gray-100 mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <a href="../HomePage/index.php" class="text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
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
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="../HomePage/index.php" class="text-gray-600 hover:text-primary transition">Home</a></li>
                    <li><a href="product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                    <li><a href="../ContactPage/contact.php" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                    <li><a href="../CartPage/cart.php" class="text-gray-600 hover:text-primary transition">Cart</a></li>
                    </ul>
                </div>
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
            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-600">
            <p>© 2024 Alandalus Design. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Success Toast Notification -->
    <div id="success-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>Item added to cart successfully!</span>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
    // Swiper initialization
    const swiper = new Swiper('.mySwiper', {
        slidesPerView: 4,
        spaceBetween: 20,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        loop: true,
    });

    // Product loading
    let selectedCategory = '<?php echo isset($_GET['category_id']) ? $_GET['category_id'] : ''; ?>';
    let selectedPrice = '';
        let offset = 0;
    const limit = 12;
        let isLoading = false;

    function loadProducts(reset = false) {
            if (isLoading) return;
            isLoading = true;
            const loadBtn = document.getElementById("loadMoreBtn");
        if (reset) {
            offset = 0;
            document.getElementById("productContainer").innerHTML = '';
            loadBtn.textContent = "Loading...";
            loadBtn.disabled = true;
        } else {
            loadBtn.textContent = "Loading...";
            loadBtn.disabled = true;
        }
        fetch(`load_products.php?offset=${offset}&limit=${limit}&category_id=${selectedCategory}&price=${selectedPrice}`)
            .then(response => response.text())
                .then(data => {
                    if (data.trim() === '' || data.includes('No more products')) {
                        loadBtn.textContent = "No More Products";
                        loadBtn.disabled = true;
                    } else {
                        document.getElementById("productContainer").innerHTML += data;
                        offset += limit;
                        loadBtn.textContent = "Load More Products";
                        loadBtn.disabled = false;
                    }
                    isLoading = false;
                // Add event listeners to new product cards
                addProductCardListeners();
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    loadBtn.textContent = "Error Loading Products";
                    loadBtn.disabled = false;
                    isLoading = false;
                });
        }

    function addProductCardListeners() {
            document.querySelectorAll('.product-card').forEach(card => {
                const viewDetailsBtn = card.querySelector('.view-details');
                    viewDetailsBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openProductModal(card);
                    });
                    card.addEventListener('click', () => {
                        openProductModal(card);
            });
        });
    }

    // Search functionality
    document.getElementById('search-button').addEventListener('click', function() {
        const query = document.getElementById('search-input').value.trim();
        if (query) {
            fetch(`search_products.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const searchResults = document.getElementById('search-results');
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(product => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            div.innerHTML = `
                                    <div class="flex items-center">
                                        <img src="../HomePage/imgs/${product.image}" alt="${product.name}" class="w-10 h-10 object-contain mr-2">
                                        <div>
                                            <p class="text-sm font-medium">${product.name}</p>
                                            <p class="text-xs text-gray-500">₪ ${product.price}</p>
                                        </div>
                                    </div>
                                `;
                            div.addEventListener('click', () => {
                                openProductModalFromSearch(product);
                                searchResults.classList.add('hidden');
                            });
                            searchResults.appendChild(div);
                        });
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.innerHTML = '<p class="px-4 py-2 text-sm text-gray-500">No products found</p>';
                        searchResults.classList.remove('hidden');
                    }
                });
        }
    });

    // Category and price filter handlers
        document.addEventListener('DOMContentLoaded', function() {
        loadProducts(true);
        document.querySelectorAll('.category-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.category-link').forEach(l => l.classList.remove('bg-primary', 'text-white'));
                this.classList.add('bg-primary', 'text-white');
                selectedCategory = this.getAttribute('data-category-id');
                loadProducts(true);
            });
        });
        document.getElementById('priceFilter').addEventListener('change', function() {
            selectedPrice = this.value;
            loadProducts(true);
        });
        document.getElementById('loadMoreBtn').addEventListener('click', function() {
            loadProducts();
        });

        // Review form submission
        document.getElementById('review-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('review-product-id').value;
            const rating = document.querySelector('.rating-star.fas')?.dataset.rating || 0;
            const comment = this.querySelector('textarea').value;
            if (!productId) {
                showToast('Please select a product to review');
                return;
            }
            fetch('submit_review.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&rating=${rating}&comment=${encodeURIComponent(comment)}`
            })
                .then(response => response.json())
                .then(data => {
                    showToast(data.message);
                    if (data.success) {
                        loadReviews(productId);
                        this.reset();
                        document.querySelectorAll('.rating-star').forEach(star => star.classList.remove('fas', 'fa-star', 'far'));
                    }
                });
        });

        // Rating stars
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.querySelectorAll('.rating-star').forEach(s => {
                    s.classList.remove('fas', 'fa-star');
                    s.classList.add('far');
                    if (s.dataset.rating <= rating) {
                        s.classList.add('fas', 'fa-star');
                        s.classList.remove('far');
                    }
                });
            });
        });
    });

    // Modal functionality
        const productModal = document.getElementById('product-modal');
        const closeModal = document.getElementById('close-modal');
        const modalMainImage = document.getElementById('modal-main-image');
        const modalThumbnails = document.getElementById('modal-thumbnails');
        const modalProductName = document.getElementById('modal-product-name');
        const modalProductPrice = document.getElementById('modal-product-price');
        const modalProductDescription = document.getElementById('modal-product-description');
        const modalQuantity = document.getElementById('modal-quantity');
        const modalAddToCart = document.getElementById('modal-add-to-cart');
    const modalAddToWishlist = document.getElementById('modal-add-to-wishlist');

        function openProductModal(card) {
        const productId = card.dataset.id;
            const name = card.dataset.name;
            const price = card.dataset.price;
            const description = card.dataset.description;
        const images = JSON.parse(card.dataset.images || '[]');
        const categoryId = card.dataset.category_id;

            modalProductName.textContent = name;
            modalProductPrice.textContent = `₪ ${price}`;
            modalProductDescription.textContent = description;
        modalMainImage.src = images[0] || card.querySelector('img').src;
            modalMainImage.alt = name;
        modalAddToCart.dataset.productId = productId;
        modalAddToWishlist.dataset.productId = productId;
        document.getElementById('review-product-id').value = productId;

            modalQuantity.value = 1;
            document.querySelectorAll('.design-option').forEach(opt => opt.classList.remove('border-primary'));
            document.querySelector('.design-option[data-design="arabic"]').classList.add('border-primary');

            if (images.length > 1) {
                modalThumbnails.innerHTML = images.map(img => `
                    <img src="../HomePage/imgs/${img}" alt="${name}" class="w-full h-auto rounded cursor-pointer hover:opacity-75 transition border border-gray-200 modal-thumbnail">
                `).join('');
                document.querySelectorAll('.modal-thumbnail').forEach(thumb => {
                    thumb.addEventListener('click', () => {
                        modalMainImage.src = thumb.src;
                        modalMainImage.alt = thumb.alt;
                    });
                });
                modalThumbnails.classList.remove('hidden');
            } else {
                modalThumbnails.innerHTML = '';
                modalThumbnails.classList.add('hidden');
            }

            productModal.classList.remove('hidden');
            productModal.classList.add('flex');
            setTimeout(() => {
                productModal.querySelector('.bg-white').classList.add('scale-100');
                productModal.querySelector('.bg-white').classList.remove('scale-95');
            }, 10);

        // Load reviews for this product
        loadReviews(productId);
    }

    function openProductModalFromSearch(product) {
        modalProductName.textContent = product.name;
        modalProductPrice.textContent = `₪ ${product.price}`;
        modalProductDescription.textContent = product.description;
        modalMainImage.src = `../HomePage/imgs/${product.image}`;
        modalMainImage.alt = product.name;
        modalAddToCart.dataset.productId = product.id;
        modalAddToWishlist.dataset.productId = product.id;
        document.getElementById('review-product-id').value = product.id;

        modalQuantity.value = 1;
        document.querySelectorAll('.design-option').forEach(opt => opt.classList.remove('border-primary'));
        document.querySelector('.design-option[data-design="arabic"]').classList.add('border-primary');

        modalThumbnails.innerHTML = '';
        modalThumbnails.classList.add('hidden');

        productModal.classList.remove('hidden');
        productModal.classList.add('flex');
        setTimeout(() => {
            productModal.querySelector('.bg-white').classList.add('scale-100');
            productModal.querySelector('.bg-white').classList.remove('scale-95');
        }, 10);

        loadReviews(product.id);
    }

        function closeProductModal() {
            const modalContent = productModal.querySelector('.bg-white');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                productModal.classList.remove('flex');
                productModal.classList.add('hidden');
            }, 150);
        }

    closeModal.addEventListener('click', closeProductModal);
    productModal.addEventListener('click', (e) => {
        if (e.target === productModal) {
            closeProductModal();
        }
    });

        document.getElementById('modal-decrease-quantity').addEventListener('click', () => {
            if (modalQuantity.value > 1) modalQuantity.value--;
        });

        document.getElementById('modal-increase-quantity').addEventListener('click', () => {
            modalQuantity.value++;
        });

        document.querySelectorAll('.design-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.design-option').forEach(opt => opt.classList.remove('border-primary'));
                option.classList.add('border-primary');
            });
        });

        modalAddToCart.addEventListener('click', () => {
        const productId = modalAddToCart.dataset.productId;
        const quantity = parseInt(modalQuantity.value);
        const design = document.querySelector('.design-option.border-primary')?.dataset.design || 'arabic';
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&quantity=${quantity}&design=${design}`
        })
            .then(response => response.json())
            .then(data => {
                showToast(data.message);
                if (data.success) {
                    document.querySelector('.cart-count').textContent = data.cart_count;
            closeProductModal();
                }
            });
    });

    modalAddToWishlist.addEventListener('click', () => {
        const productId = modalAddToWishlist.dataset.productId;
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}`
        })
            .then(response => response.json())
            .then(data => {
                showToast(data.message);
                if (data.success) {
                    document.querySelector('.wishlist-count').textContent = data.wishlist_count;
                }
            });
    });

    function loadReviews(productId) {
        fetch(`load_reviews.php?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                const reviewList = document.getElementById('review-list');
                reviewList.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(review => {
                        const div = document.createElement('div');
                        div.className = 'bg-white p-6 rounded-lg shadow-sm';
                        div.innerHTML = `
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        ${'<i class="fas fa-star"></i>'.repeat(review.rating)}
                                        ${'<i class="far fa-star"></i>'.repeat(5 - review.rating)}
                        </div>
                                    <span class="ml-2 text-sm text-gray-500">${new Date(review.created_at).toLocaleDateString()}</span>
                                </div>
                                <p class="text-gray-700">${review.comment}</p>
                                <p class="text-sm text-gray-500 mt-2">- ${review.user_name || 'Anonymous'}</p>
                    `;
                        reviewList.appendChild(div);
                    });
                } else {
                    reviewList.innerHTML = '<p class="text-gray-500">No reviews yet for this product.</p>';
                }
            });
    }

    function showToast(message) {
        const toast = document.getElementById('success-toast');
        toast.querySelector('span').textContent = message;
        toast.classList.remove('translate-y-full', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }
    </script>
</body>
</html> 