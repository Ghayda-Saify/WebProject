<?php
session_start();
include '../connection.php';
global $con;

// Fetch an active promo code from the database
$promo_code_sql = "SELECT * FROM promotions WHERE active = 1 ORDER BY id DESC LIMIT 1"; // Fetch the latest active promo
$promo_code_result = $con->query($promo_code_sql);
$promo_data = null;
if ($promo_code_result && $promo_code_result->num_rows > 0) {
    $promo_data = $promo_code_result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $promo_data ? htmlspecialchars($promo_data['title']) : 'Special Offer'; ?> - Alandalus Design</title>
    <link rel="stylesheet" href="../HomePage/style.css"> <!-- Adjust path if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <style>
        /* Add any specific styles for the offers page here */
        .promo-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            margin: 40px auto; /* Ensure horizontal centering */
        }
        .promo-code-display {
            font-size: 2em;
            font-weight: bold;
            color: #f13b1c; /* Secondary color */
            margin: 1.5rem 0;
            padding: 0.8rem 1.5rem;
            border: 2px dashed #f13b1c;
            display: inline-block;
            border-radius: 4px;
            background-color: #fffaf0; /* Light beige */
        }
        .instructions {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 2rem;
        }
        .checkout-link {
             background-color: #122c6f; /* Primary color */
             color: white;
             padding: 12px 24px;
             border-radius: 8px;
             font-size: 1.1em;
             transition: background-color 0.3s ease;
        }
        .checkout-link:hover {
            background-color: #0e225a; /* Darker primary */
        }
    </style>
</head>
<body class="font-poppins bg-beige/10">
    <header>
        <!-- Header content goes here (copy from index.php) -->
         <a href="../HomePage/index.php" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
         <nav class="main-nav">
             <ul>
                 <li><a href="../HomePage/index.php">Home</a></li>
                 <li><a href="../ProductsPage/product.php">Products</a></li>
                 <li><a href="../ContactPage/contact.php">Connect</a></li>
                 <li>
                     <a href="../CartPage/cart.php" class="relative">
                         <i class="fa-solid fa-cart-shopping text-primary"></i>
                         <!-- Cart count will be updated via PHP -->
                         <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
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
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="promo-container">
            <?php if ($promo_data) { ?>
                <h2 class="text-2xl font-bold mb-4 text-primary"><?php echo htmlspecialchars($promo_data['title']); ?></h2>
                <p class="instructions"><?php echo htmlspecialchars($promo_data['subtitle']); ?></p>

                <?php
                // Display discount or promo code
                if (!empty($promo_data['discount_value']) && !empty($promo_data['discount_type'])) {
                     $discount_text = '';
                     if ($promo_data['discount_type'] === 'percent') {
                         $discount_text = htmlspecialchars($promo_data['discount_value']) . '%';
                     } else {
                         $discount_text = '₪' . htmlspecialchars($promo_data['discount_value']);
                     }
                     echo '<p class="instructions">Get a ' . $discount_text . ' discount with this code:</p>';
                }
                ?>

                <div class="promo-code-display"><?php echo htmlspecialchars($promo_data['code'] ?? 'NO CODE'); ?></div> <!-- Displaying the 'code' column -->

                <p class="instructions mt-4">Click the button below to go to the products page:</p>
                <a href="../ProductsPage/product.php" class="checkout-link"><?php echo htmlspecialchars($promo_data['btn_text'] ?? 'Go to Products Page'); ?></a>
            <?php } else { ?>
                <h2 class="text-2xl font-bold mb-4 text-primary">No Special Offers Available</h2>
                <p class="instructions">Please check back later for exciting new promotions!</p>
                <a href="../HomePage/index.php" class="checkout-link">Return Home</a>
            <?php } ?>
        </div>
    </main>

    <!-- Footer content goes here (copy from index.php) -->
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
                         <li><a href="../ProductsPage/product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                         <li><a href="../ContactPage/contact.php" class="text-gray-600 hover:text-primary transition">Connect</a></li>
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


</body>
</html> 