<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alandalus Design | Connect</title>
    <link rel="stylesheet" href="contactPage.css">
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
</head>
<body class="font-poppins bg-beige/10">
<header>
    <a href="../HomePage/index.php" class="logo text-primary font-['Pacifico'] text-2xl">Alandalus Design</a>
    <!-- Navigation Bar -->
    <nav class="main-nav">
        <ul>
            <li><a href="../HomePage/index.php">Home</a></li>
            <li><a href="../ProductsPage/product.php">Products</a></li>
            <li><a href="contact.php" class="text-primary font-bold">Connect</a></li>
            <li>
                <a href="../CartPage/cart.php" class="relative">
                    <i class="fa-solid fa-cart-shopping text-primary"></i>
                    <span class="absolute -top-2 -right-2 bg-secondary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center cart-count">0</span>
                </a>
            </li>
            <li>
            <li>
                <?php $profileLink = isset($_SESSION['user_email']) ? '../ProfilePage/profile.php' : '../SignIn&Up/sign.php'; ?>
                <a href="<?php echo $profileLink; ?>">
                    <i class="fa-solid fa-user text-primary"></i>
                </a>
        </ul>
    </nav>
</header>

<main class="container mx-auto px-4 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="../HomePage/index.php" class="text-gray-700 hover:text-primary">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-primary font-medium">Connect</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Contact Section -->
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-primary mb-4">Let's Connect</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Have a question about our products or interested in a custom design? We'd love to hear from you!
                Reach out to us and we'll get back to you as soon as possible.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <form id="contact-form" class="space-y-6" method="POST" action="send_email.php">
                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                               placeholder="Enter your full name">
                    </div>

                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                               placeholder="Enter your email address">
                    </div>

                    <div>
                        <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                        <input type="text" id="subject" name="subject"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                               placeholder="What's this about?">
                    </div>

                    <div>
                        <label for="message" class="block text-gray-700 font-medium mb-2">Message *</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                                  placeholder="Your message here..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-opacity-90 transition-colors flex items-center justify-center space-x-2">
                        <span>Send Message</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="lg:pl-8">
                <div class="bg-white p-8 rounded-lg shadow-lg mb-8">
                    <h3 class="text-2xl font-bold text-primary mb-6">Contact Information</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 mb-1">Email</h4>
                                <a href="mailto:infoalandalusdesign@gmail.com" class="text-primary hover:text-secondary transition-colors">
                                    infoalandalusdesign@gmail.com
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-primary"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 mb-1">Phone</h4>
                                <a href="tel:+97259-464-6503" class="text-primary hover:text-secondary transition-colors">
                                    +972 59-464-6503
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 mb-1">Location</h4>
                                <p class="text-gray-600">Sufyan Street - Raddad Building, Nablus 009709</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold text-primary mb-6">Follow Us</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="https://www.facebook.com/Al.Andalus.Design" target="_blank" rel="noopener noreferrer"
                           class="flex items-center space-x-3 p-4 rounded-lg bg-primary/5 hover:bg-primary/10 transition-colors">
                            <i class="fab fa-facebook text-primary text-xl"></i>
                            <span class="text-gray-700">Facebook</span>
                        </a>
                        <a href="https://www.instagram.com/andalus_design" target="_blank" rel="noopener noreferrer"
                           class="flex items-center space-x-3 p-4 rounded-lg bg-primary/5 hover:bg-primary/10 transition-colors">
                            <i class="fab fa-instagram text-primary text-xl"></i>
                            <span class="text-gray-700">Instagram</span>
                        </a>
                        <a href="https://t.me/andalusdesign" target="_blank" rel="noopener noreferrer"
                           class="flex items-center space-x-3 p-4 rounded-lg bg-primary/5 hover:bg-primary/10 transition-colors">
                            <i class="fab fa-telegram text-primary text-xl"></i>
                            <span class="text-gray-700">Telegram</span>
                        </a>
                        <a href="https://wa.me/message/7YZUEAMKO53SM1" target="_blank" rel="noopener noreferrer"
                           class="flex items-center space-x-3 p-4 rounded-lg bg-primary/5 hover:bg-primary/10 transition-colors">
                            <i class="fab fa-whatsapp text-primary text-xl"></i>
                            <span class="text-gray-700">WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
                    <li><a href="../ProductsPage/product.php" class="text-gray-600 hover:text-primary transition">Products</a></li>
                    <li><a href="contact.php" class="text-gray-600 hover:text-primary transition">Contact</a></li>
                    <li><a href="../CartPage/cart.php" class="text-gray-600 hover:text-primary transition">Cart</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="font-bold text-lg mb-4">Contact Us</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-gray-600">
                        <i class="fas fa-envelope w-6"></i>
                        <span>infoalandalusdesign@gmail.com</span>
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

<script>
    // Form validation and submission
    const form = document.getElementById('contact-form');
    form.addEventListener('submit', function(e) {


        // Basic form validation
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const message = document.getElementById('message');

        if (!name || !email || !message) {
            alert('Please fill in all required fields.');
            return;
        }

        if (!isValidEmail(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        // Here you would typically send the form data to your server
        alert('Thank you for your message! We will get back to you soon.');
        form.reset();
    });

    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    // Update cart count
    document.addEventListener('DOMContentLoaded', function() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = document.querySelector('.cart-count');
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    });
</script>
</body>
</html>