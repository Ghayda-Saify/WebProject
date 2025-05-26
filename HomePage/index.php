<?php
session_start();
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
    <nav class="main-nav">
        <ul>
            <li><a href="index.php" class="text-primary font-bold">Home</a></li>
            <li><a href="../ProductsPage/product.php">Products</a></li>
            <li><a href="../ContactPage/contact.html">Connect</a></li>
            <li>
                <a href="../CartPage/cart.html" class="relative">
                    <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
            </li>
            <li>
                <?php $profileLink = isset($_SESSION['user_email']) ? '../ProfilePage/profile.html' : '../SignIn&Up/sign.php'; ?>
                <a href="<?php echo $profileLink; ?>">
                    <i class="fa-solid fa-user text-primary"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>
<main>
    <!--        Home-->
    <section class="carousel next">
        <div class="list">
            <article class="item other_1">
                <div class="main-content"
                     style="background-color: #ffffff;">
                    <div class="content">
                        <h2>Custom Hoodie</h2>
                        <p class="price">₪ 60</p>
                        <p class="description">
                            PERSONALIZED CREATIONS, CRAFTED FOR YOU
                        </p>
                        <button class="addToCard" style="background-color: #122c6f;">
                            Add To Card
                        </button>
                    </div>
                </div>
                <figure class="image">
                    <img src="imgs/hoodi-removebg-preview.png" alt="">
                    <figcaption>PERSONALIZED CREATIONS, CRAFTED FOR YOU</figcaption>
                </figure>
            </article>
            <article class="item active">
                <div class="main-content"
                     style="background-color: #ADB5AA;">
                    <div class="content">
                        <h2>Custom Notebook</h2>
                        <p class="price">₪ 20</p>
                        <p class="description">
                            PERSONALIZED CREATIONS, CRAFTED FOR YOU                            </p>
                        <button class="addToCard">
                            Add To Card
                        </button>
                    </div>
                </div>
                <figure class="image">
                    <img src="imgs/notebook2-removebg-preview.png" alt="">
                    <figcaption>PERSONALIZED CREATIONS, CRAFTED FOR YOU</figcaption>
                </figure>
            </article>
            <article class="item other_2">
                <div class="main-content"
                     style="background-color: #AF9864;">
                    <div class="content">
                        <h2>Custom Cover</h2>
                        <p class="price">₪ 20</p>
                        <p class="description">
                            PERSONALIZED CREATIONS, CRAFTED FOR YOU                            </p>
                        <button class="addToCard">
                            Add To Card
                        </button>
                    </div>
                </div>
                <figure class="image">
                    <img src="imgs/cover2-removebg-preview.png" alt="">
                    <figcaption>PERSONALIZED CREATIONS, CRAFTED FOR YOU</figcaption>
                </figure>
            </article>
            <article class="item">
                <div class="main-content"
                     style="background-color: #D5CEB1;">
                    <div class="content">
                        <h2>Custom HandBag</h2>
                        <p class="price">₪ 45</p>
                        <p class="description">
                            PERSONALIZED CREATIONS, CRAFTED FOR YOU                            </p>
                        <button class="addToCard">
                            Add To Card
                        </button>
                    </div>
                </div>
                <figure class="image">
                    <img src="imgs/زرف-removebg-preview.png" alt="">
                    <figcaption>PERSONALIZED CREATIONS, CRAFTED FOR YOU</figcaption>
                </figure>
            </article>
        </div>
        <div class="arrows">
            <button id="prev" style="--tw-text-opacity:1;
    color: rgb(18 44 111 / var(--tw-text-opacity, 1))"><</button>
            <button id="next" style="--tw-text-opacity:1;
    color: rgb(18 44 111 / var(--tw-text-opacity, 1))">></button>
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
                <!-- Product 1 -->
                <div
                        class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
                >
                    <div class="relative">
                        <div class="h-64 overflow-hidden">
                            <img
                                    src="imgs/weshah.jpg"
                                    alt="Modern Armchair"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div
                                class="quick-actions absolute top-4 right-4 flex flex-col gap-2"
                        >
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-heart-line text-gray-700"></i>
                            </button>
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-eye-line text-gray-700"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-medium mb-1">Modern Armchair</h3>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-half-fill ri-xs"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-1">(24)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-semibold">$249.99</span>
                            <button
                                    class="bg-primary text-white py-2 px-3 rounded-button text-sm hover:bg-opacity-90 transition-colors whitespace-nowrap"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 2 -->
                <div
                        class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
                >
                    <div class="relative">
                        <div class="h-64 overflow-hidden">
                            <img
                                    src="imgs/maska.jpg"
                                    alt="Glass Coffee Table"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div
                                class="quick-actions absolute top-4 right-4 flex flex-col gap-2"
                        >
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-heart-line text-gray-700"></i>
                            </button>
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-eye-line text-gray-700"></i>
                            </button>
                        </div>
                        <div
                                class="absolute top-4 left-4 bg-secondary text-white text-xs py-1 px-2 rounded"
                        >
                            New
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-medium mb-1">Glass Coffee Table</h3>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-line ri-xs"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-1">(18)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-semibold">$179.99</span>
                            <button
                                    class="bg-primary text-white py-2 px-3 rounded-button text-sm hover:bg-opacity-90 transition-colors whitespace-nowrap"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 3 -->
                <div
                        class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
                >
                    <div class="relative">
                        <div class="h-64 overflow-hidden">
                            <img
                                    src="https://readdy.ai/api/search-image?query=modern%20pendant%20lamp%20with%20brass%20details%20and%20glass%20shade%2C%20professional%20product%20photography%20on%20white%20background%2C%20high%20quality%2C%20detailed&width=500&height=500&seq=11&orientation=squarish"
                                    alt="Pendant Lamp"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div
                                class="quick-actions absolute top-4 right-4 flex flex-col gap-2"
                        >
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-heart-line text-gray-700"></i>
                            </button>
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-eye-line text-gray-700"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-medium mb-1">Pendant Lamp</h3>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-1">(32)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-semibold">$129.99</span>
                            <button
                                    class="bg-primary text-white py-2 px-3 rounded-button text-sm hover:bg-opacity-90 transition-colors whitespace-nowrap"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 4 -->
                <div
                        class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
                >
                    <div class="relative">
                        <div class="h-64 overflow-hidden">
                            <img
                                    src="https://readdy.ai/api/search-image?query=decorative%20ceramic%20vases%20set%20in%20different%20sizes%2C%20blue%20and%20white%20colors%2C%20professional%20product%20photography%20on%20white%20background%2C%20high%20quality%2C%20detailed&width=500&height=500&seq=12&orientation=squarish"
                                    alt="Ceramic Vases Set"
                                    class="w-full h-full object-cover object-top"
                            />
                        </div>
                        <div
                                class="quick-actions absolute top-4 right-4 flex flex-col gap-2"
                        >
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-heart-line text-gray-700"></i>
                            </button>
                            <button
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-100"
                            >
                                <i class="ri-eye-line text-gray-700"></i>
                            </button>
                        </div>
                        <div
                                class="absolute top-4 left-4 bg-red-500 text-white text-xs py-1 px-2 rounded"
                        >
                            Sale
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-medium mb-1">Ceramic Vases Set</h3>
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-fill ri-xs"></i>
                                <i class="ri-star-line ri-xs"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-1">(12)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-primary font-semibold">$79.99</span>
                                <span class="text-gray-400 text-sm line-through ml-2"
                                >$99.99</span
                                >
                            </div>
                            <button
                                    class="bg-primary text-white py-2 px-3 rounded-button text-sm hover:bg-opacity-90 transition-colors whitespace-nowrap"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-10">
                <a
                        href="../ProductsPage/product.php"
                        class="inline-block border-2 border-primary text-primary px-6 py-3 rounded-button font-medium hover:bg-primary hover:text-white transition-colors whitespace-nowrap"
                >View All Products</a
                >
            </div>
        </div>
    </section>
    <!--        deals-->
    <section class="deals">
        <!-- Promotional Banner -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Promo Banner 1 -->
                    <div class="relative h-80 rounded-lg overflow-hidden">
                        <img
                                src="https://readdy.ai/api/search-image?query=luxurious%20living%20room%20interior%20with%20elegant%20furniture%20and%20decor%2C%20soft%20lighting%2C%20blue%20accent%20colors%2C%20professional%20interior%20photography&width=800&height=500&seq=13&orientation=landscape"
                                alt="New Collection"
                                class="w-full h-full object-cover object-top"
                        />
                        <div
                                class="absolute inset-0 bg-gradient-to-r from-primary/80 to-primary/40 flex items-center"
                        >
                            <div class="p-8">
                                <h3 class="text-white text-2xl font-bold mb-2">
                                    Spring Collection 2025
                                </h3>
                                <p class="text-white text-sm mb-4">
                                    Refresh your space with our latest designs
                                </p>
                                <a
                                        href="#"
                                        class="bg-white text-primary px-4 py-2 rounded-button text-sm font-medium hover:bg-gray-100 transition-colors inline-block whitespace-nowrap"
                                >Shop Now</a
                                >
                            </div>
                        </div>
                    </div>
                    <!-- Promo Banner 2 -->
                    <div class="relative h-80 rounded-lg overflow-hidden">
                        <img
                                src="https://readdy.ai/api/search-image?query=modern%20bedroom%20interior%20with%20elegant%20furniture%20and%20decor%2C%20soft%20lighting%2C%20orange%20accent%20colors%2C%20professional%20interior%20photography&width=800&height=500&seq=14&orientation=landscape"
                                alt="Special Offer"
                                class="w-full h-full object-cover object-top"
                        />
                        <div
                                class="absolute inset-0 bg-gradient-to-r from-secondary/80 to-secondary/40 flex items-center"
                        >
                            <div class="p-8">
                                <h3 class="text-white text-2xl font-bold mb-2">
                                    Limited Time Offer
                                </h3>
                                <p class="text-white text-sm mb-4">
                                    Get up to 30% off on selected items
                                </p>
                                <a
                                        href="#"
                                        class="bg-white text-secondary px-4 py-2 rounded-button text-sm font-medium hover:bg-gray-100 transition-colors inline-block whitespace-nowrap"
                                >View Offers</a
                                >
                            </div>
                        </div>
                    </div>
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
    <!--        new arrival-->
    <section class="new-arrivals">
        <!-- Newsletter -->
        <section class="py-16 bg-primary">
            <div class="container mx-auto px-4">
                <div class="max-w-2xl mx-auto text-center">
                    <h2 class="text-3xl font-bold text-white mb-2">
                        Join Our Newsletter
                    </h2>
                    <p class="text-white/80 mb-8">
                        Subscribe to receive updates on new products, special offers, and
                        design tips.
                    </p>
                    <form class="flex flex-col sm:flex-row gap-3">
                        <input
                                type="email"
                                placeholder="Your email address"
                                class="flex-1 px-4 py-3 rounded-button border-none focus:outline-none focus:ring-2 focus:ring-white/30 text-sm"
                        />
                        <button
                                type="submit"
                                class="bg-secondary text-white px-6 py-3 rounded-button font-medium hover:bg-opacity-90 transition-colors whitespace-nowrap"
                        >
                            Subscribe
                        </button>
                    </form>
                    <p class="text-white/60 text-sm mt-4">
                        By subscribing, you agree to our Privacy Policy and consent to
                        receive updates from our company.
                    </p>
                </div>
            </div>
        </section>
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

    // Initialize cart count from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = document.querySelector('.cart-count');
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    });

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