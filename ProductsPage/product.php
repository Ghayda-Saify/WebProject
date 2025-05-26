<?php
include '../connection.php';
global $con;
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
$sql = "SELECT * FROM product WHERE status = 1 LIMIT 36";
$result = $con->query($sql);

// Fetch categories for sidebar and slider
$sidebar_categories = [];
$slider_categories = [];
$cat_sql = "SELECT * FROM categories WHERE status = 1";
$cat_res = $con->query($cat_sql);
while ($row = $cat_res->fetch_assoc()) {
    $sidebar_categories[] = $row;
    $slider_categories[] = $row;
}
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
        /* Decorative bubbles using pseudo-elements */
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
                },
            },
        };
    </script>
</head>
<body class="font-poppins bg-beige/10 fancy-bubbles">
    <header>
        <a href="../HomePage/index.php" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
        <nav class="main-nav flex items-center w-full">
            <div class="flex-1 min-w-[150px]"></div> <!-- Increased minimum width spacer -->
            <ul class="flex items-center justify-center gap-8">
                <li><a href="../HomePage/index.php">Home</a></li>
                <li><a href="product.php" class="text-primary font-bold">Products</a></li>
                <li><a href="../ContactPage/contact.html">Connect</a></li>
                <li>
                    <a href="../CartPage/cart.html" class="relative">
                        <i class="fa-solid fa-cart-shopping text-primary"></i>
                        <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                    </a>
                </li>
                <li>
                    <a href="../ProfilePage/profile.html">
                        <i class="fa-solid fa-user text-primary"></i>
                    </a>
                </li>
                <li>
                    <a href="wishlist.html" class="relative" id="wishlist-icon">
                        <i class="fa-solid fa-heart text-primary"></i>
                        <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center wishlist-count">0</span>
                    </a>
                </li>
            </ul>
            <div class="flex-1 flex justify-end"> <!-- Spacer with search container -->
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

    <!-- Categories Slider at the top -->
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
                                <img src="../HomePAge/imgs/<?php echo htmlspecialchars($row['image']); ?>" class="category__img" alt="">
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
    <main class="container mx-auto px-4 py-8 flex gap-8">
        <!-- Sidebar -->
        <aside class="w-64 p-4 bg-white rounded-lg shadow-md h-fit sticky top-0 self-start mt-0">
            <h3 class="font-bold mb-4 text-lg text-primary">Categories</h3>
            <ul class="mb-8 max-h-64 overflow-y-auto">
                <?php foreach ($sidebar_categories as $cat): ?>
                    <li>
                        <a href="#" class="block py-2 px-3 hover:bg-primary hover:text-white rounded category-link" data-category-id="<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
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
        <!-- Products grid -->
        <div class="flex-1">
            <section class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-2 font-aboreto">Our Products</h2>
                <p class="text-gray-600 text-center mb-12">Discover our collection of personalized creations</p>
                <div id="productContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Products will be loaded here via AJAX -->
                </div>
                <div class="text-center mt-8">
                    <button id="loadMoreBtn" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-opacity-90 transition">
                        Load More Products
                    </button>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
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

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="../HomePage/index.php" class="text-gray-600 hover:text-primary transition">Home</a></li>
                        <li><a href="product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                        <li><a href="../ContactPage/contact.html" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                        <li><a href="../CartPage/cart.html" class="text-gray-600 hover:text-primary transition">Cart</a></li>
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

    <!-- Success Toast Notification -->
    <div id="success-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>Item added to cart successfully!</span>
    </div>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!--<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>-->

    <script>
        const cswiper = new Swiper('.mySwiper', {
            slidesPerView: 4,
            spaceBetween: 20,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            loop: true,
        });

        // Initialize cart count from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartCount = document.querySelector('.cart-count');
            cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
        });

    </script>

    <script>
        // Swiper init
        const swiper = new Swiper('.swiper', {
            slidesPerView: 'auto',
            spaceBetween: 16,
        });

        let selectedCategory = '';
        let selectedPrice = '';
        let offset = 0;
        const limit = 36;
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
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    loadBtn.textContent = "Error Loading Products";
                    loadBtn.disabled = false;
                    isLoading = false;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadProducts(true);
            // Sidebar category click
            document.querySelectorAll('.category-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.category-link').forEach(l => l.classList.remove('bg-primary', 'text-white'));
                    this.classList.add('bg-primary', 'text-white');
                    selectedCategory = this.getAttribute('data-category-id');
                    // Sync slider
                    document.querySelectorAll('.swiper-slide').forEach(slide => {
                        slide.classList.toggle('active', slide.getAttribute('data-id') === selectedCategory);
                    });
                    loadProducts(true);
                });
            });
            // Price filter
            document.getElementById('priceFilter').addEventListener('change', function() {
                selectedPrice = this.value;
                loadProducts(true);
            });
            // Slider category click
            document.querySelectorAll('.swiper-slide').forEach(slide => {
                slide.addEventListener('click', function() {
                    document.querySelectorAll('.swiper-slide').forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                    selectedCategory = this.getAttribute('data-id');
                    // Sync sidebar
                    document.querySelectorAll('.category-link').forEach(link => {
                        link.classList.toggle('bg-primary', link.getAttribute('data-category-id') === selectedCategory);
                        link.classList.toggle('text-white', link.getAttribute('data-category-id') === selectedCategory);
                    });
                    loadProducts(true);
                });
            });
            document.getElementById("loadMoreBtn").addEventListener("click", function() {
                loadProducts();
            });
        });
    </script>

<!--    <script>-->
<!--        const swiper = new Swiper('.categories__container', {-->
<!--            loop: true,-->
<!--            autoplay: {-->
<!--                delay: 2500, // Time between slides in ms-->
<!--                disableOnInteraction: false, // Keep autoplay after user interaction-->
<!--            },-->
<!--            navigation: {-->
<!--                nextEl: '.swiper-button-next',-->
<!--                prevEl: '.swiper-button-prev',-->
<!--            },-->
<!--            slidesPerView: 3, // You can change this based on your layout-->
<!--            spaceBetween: 20,-->
<!--            breakpoints: {-->
<!--                768: { slidesPerView: 3 },-->
<!--                480: { slidesPerView: 2 },-->
<!--                0: { slidesPerView: 1 },-->
<!--            },-->
<!--        });-->
<!--    </script>-->
    <script>
        // TEMPORARY DATA: Replace with database integration
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
        const cartCount = document.querySelector('.cart-count');
        const wishlistCount = document.querySelector('.wishlist-count');
        const productModal = document.getElementById('product-modal');
        const closeModal = document.getElementById('close-modal');
        const modalMainImage = document.getElementById('modal-main-image');
        const modalThumbnails = document.getElementById('modal-thumbnails');
        const modalProductName = document.getElementById('modal-product-name');
        const modalProductPrice = document.getElementById('modal-product-price');
        const modalProductDescription = document.getElementById('modal-product-description');
        const modalQuantity = document.getElementById('modal-quantity');
        const modalAddToCart = document.getElementById('modal-add-to-cart');
        const wishlistIcon = document.getElementById('wishlist-icon');
        
        function updateCartCount() {
            cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        function updateWishlistCount() {
            wishlistCount.textContent = wishlist.length;
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
        }

        // Initialize counts
        updateCartCount();
        updateWishlistCount();

        // Set initial wishlist count to 2 if empty
        if (wishlist.length === 0) {
            wishlist = [
                {
                    id: 1,
                    name: "Custom Notebook",
                    price: 20.00,
                    image: "../HomePage/imgs/notebook2-removebg-preview.png"
                },
                {
                    id: 2,
                    name: "Custom Hoodie",
                    price: 60.00,
                    image: "../HomePage/imgs/hoodi-removebg-preview.png"
                }
            ];
            updateWishlistCount();
        }

        // Show modal when clicking product card or view details button
        document.querySelectorAll('.product-card').forEach(card => {
            const viewDetailsBtn = card.querySelector('.view-details');
            
            // Handle click on the view details button
            viewDetailsBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent card click event
                openProductModal(card);
            });

            // Handle click on the entire card
            card.addEventListener('click', () => {
                openProductModal(card);
            });
        });

        // Function to open product modal
        function openProductModal(card) {
            const name = card.dataset.name;
            const price = card.dataset.price;
            const description = card.dataset.description;
            const images = JSON.parse(card.dataset.images);

            modalProductName.textContent = name;
            modalProductPrice.textContent = `₪ ${price}`;
            modalProductDescription.textContent = description;
            modalMainImage.src = images[0];
            modalMainImage.alt = name;

            // Reset quantity
            modalQuantity.value = 1;

            // Reset design selection
            document.querySelectorAll('.design-option').forEach(opt => opt.classList.remove('border-primary'));
            document.querySelector('.design-option[data-design="arabic"]').classList.add('border-primary');

            // Generate thumbnails
            if (images.length > 1) {
                modalThumbnails.innerHTML = images.map(img => `
                    <img src="${img}" alt="${name}" class="w-full h-auto rounded cursor-pointer hover:opacity-75 transition border border-gray-200 modal-thumbnail">
                `).join('');

                // Add thumbnail click handlers
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

            // Show modal with animation
            productModal.classList.remove('hidden');
            productModal.classList.add('flex');
            setTimeout(() => {
                productModal.querySelector('.bg-white').classList.add('scale-100');
                productModal.querySelector('.bg-white').classList.remove('scale-95');
            }, 10);
        }

        // Close modal
        closeModal.addEventListener('click', () => {
            closeProductModal();
        });

        // Close modal when clicking outside
        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                closeProductModal();
            }
        });

        // Function to close product modal
        function closeProductModal() {
            const modalContent = productModal.querySelector('.bg-white');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                productModal.classList.remove('flex');
                productModal.classList.add('hidden');
            }, 150);
        }

        // Modal quantity controls
        document.getElementById('modal-decrease-quantity').addEventListener('click', () => {
            if (modalQuantity.value > 1) modalQuantity.value--;
        });

        document.getElementById('modal-increase-quantity').addEventListener('click', () => {
            modalQuantity.value++;
        });

        // Design option selection
        document.querySelectorAll('.design-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.design-option').forEach(opt => opt.classList.remove('border-primary'));
                option.classList.add('border-primary');
            });
        });

        // Modal add to cart
        modalAddToCart.addEventListener('click', () => {
            const product = {
                id: Date.now(),
                name: modalProductName.textContent,
                price: parseFloat(modalProductPrice.textContent.replace('₪ ', '')),
                quantity: parseInt(modalQuantity.value),
                image: modalMainImage.src,
                design: document.querySelector('.design-option.border-primary')?.dataset.design || 'arabic'
            };

            const existingProduct = cart.find(item => 
                item.name === product.name && 
                item.design === product.design
            );

            if (existingProduct) {
                existingProduct.quantity += product.quantity;
            } else {
                cart.push(product);
            }

            updateCartCount();
            showToast();
            
            // Close modal with animation
            closeProductModal();
        });

        // Toast notification
        function showToast(message = 'Item added to cart successfully!') {
            const toast = document.getElementById('success-toast');
            toast.querySelector('span').textContent = message;
            toast.classList.remove('translate-y-full', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
            }, 3000);
        }

        // Add wishlist functionality
        wishlistIcon.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get current product details from modal if open
            if (productModal.classList.contains('flex')) {
                const currentProduct = {
                    id: Date.now(),
                    name: modalProductName.textContent,
                    price: parseFloat(modalProductPrice.textContent.replace('₪ ', '')),
                    image: modalMainImage.src
                };

                const existingProduct = wishlist.find(item => item.name === currentProduct.name);
                
                if (!existingProduct) {
                    wishlist.push(currentProduct);
                    showToast('Item added to wishlist!');
                } else {
                    wishlist = wishlist.filter(item => item.name !== currentProduct.name);
                    showToast('Item removed from wishlist!');
                }

                updateWishlistCount();
            } else {
                // If no product is selected, navigate to wishlist page
                window.location.href = 'wishlist.html';
            }
        });

        // Initialize functionality
        document.addEventListener('DOMContentLoaded', () => {
            updateCartCount();
            updateWishlistCount();
        });

        // Product data for search functionality
        const products = [
            {
                name: "Custom Notebook",
                price: 20.00,
                image: "../HomePage/imgs/notebook2-removebg-preview.png",
                description: "PERSONALIZED CREATIONS, CRAFTED FOR YOU"
            },
            {
                name: "Custom Cover",
                price: 20.00,
                image: "../HomePage/imgs/cover2-removebg-preview.png",
                description: "Elegant and personalized covers for your books and devices"
            },
            {
                name: "Custom Hoodie",
                price: 60.00,
                image: "../HomePage/imgs/hoodi-removebg-preview.png",
                description: "Comfortable and stylish hoodies with Arabic calligraphy"
            },
            {
                name: "Custom Mug",
                price: 15.00,
                image: "../HomePage/imgs/mug-removebg-preview.png",
                description: "Personalized mugs with Arabic calligraphy"
            },
            {
                name: "Custom Handbag",
                price: 45.00,
                image: "../HomePage/imgs/زرف-removebg-preview.png",
                description: "Elegant handbags with unique Arabic designs"
            },
            {
                name: "Custom Pins",
                price: 10.00,
                image: "../HomePage/imgs/دبابيس.png",
                description: "Beautiful pins featuring Arabic calligraphy"
            }
        ];

        // Search functionality
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search-input');
            const searchButton = document.getElementById('search-button');
            const searchResults = document.getElementById('search-results');

            function performSearch(query) {
                query = query.toLowerCase().trim();
                if (query === '') {
                    searchResults.classList.add('hidden');
                    return;
                }

                const results = products.filter(product => 
                    product.name.toLowerCase().includes(query) ||
                    product.description.toLowerCase().includes(query)
                );

                displayResults(results);
            }

            function displayResults(results) {
                if (results.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-4 text-center text-gray-500">
                            No products found
                        </div>
                    `;
                } else {
                    searchResults.innerHTML = results.map(product => `
                        <div class="block p-4 hover:bg-gray-50 border-b last:border-b-0 cursor-pointer product-search-result" 
                             data-name="${product.name}">
                            <div class="flex items-center">
                                <img src="${product.image}" alt="${product.name}" class="w-12 h-12 object-contain">
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium">${product.name}</h4>
                                    <p class="text-xs text-gray-500">${product.description}</p>
                                    <span class="text-sm text-primary font-semibold">₪${product.price.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // Add click handlers for search results
                    document.querySelectorAll('.product-search-result').forEach(result => {
                        result.addEventListener('click', () => {
                            const productName = result.dataset.name;
                            const productCard = document.querySelector(`.product-card[data-name="${productName}"]`);
                            if (productCard) {
                                productCard.querySelector('.view-details').click();
                                searchResults.classList.add('hidden');
                                searchInput.value = '';
                            }
                        });
                    });
                }
                searchResults.classList.remove('hidden');
            }

            // Search input event listeners
            searchInput.addEventListener('input', (e) => {
                performSearch(e.target.value);
            });

            searchButton.addEventListener('click', () => {
                performSearch(searchInput.value);
            });

            // Close search results when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Handle Enter key
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    performSearch(searchInput.value);
                }
            });
        });
    </script>
</body>
</html> 