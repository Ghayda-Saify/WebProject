let next = document.getElementById('next');
let prev = document.getElementById('prev');
let carousel = document.querySelector('.carousel');
let items = document.querySelectorAll('.carousel .item');
let countItem = items.length;
let active = 1;
let other_1 = null;
let other_2 = null;

next.onclick = () => {
    carousel.classList.remove('prev');
    carousel.classList.add('next');
    active =active + 1 >= countItem ? 0 : active + 1;
    other_1 =active - 1 < 0 ? countItem -1 : active - 1;
    other_2 = active + 1 >= countItem ? 0 : active + 1;
    changeSlider();
}
prev.onclick = () => {
    carousel.classList.remove('next');
    carousel.classList.add('prev');
    active = active - 1 < 0 ? countItem - 1 : active - 1;
    other_1 = active + 1 >= countItem ? 0 : active + 1;
    other_2 = other_1 + 1 >= countItem ? 0 : other_1 + 1;
    changeSlider();
}
const changeSlider = () => {
    let itemOldActive = document.querySelector('.carousel .item.active');
    if(itemOldActive) itemOldActive.classList.remove('active');

    let itemOldOther_1 = document.querySelector('.carousel .item.other_1');
    if(itemOldOther_1) itemOldOther_1.classList.remove('other_1');

    let itemOldOther_2 = document.querySelector('.carousel .item.other_2');
    if(itemOldOther_2) itemOldOther_2.classList.remove('other_2');

    items.forEach(e => {
        e.querySelector('.image img').style.animation = 'none';
        e.querySelector('.image figcaption').style.animation = 'none';
        void e.offsetWidth;
        e.querySelector('.image img').style.animation = '';
        e.querySelector('.image figcaption').style.animation = '';
    })

    items[active].classList.add('active');
    items[other_1].classList.add('other_1');
    items[other_2].classList.add('other_2');

    clearInterval(autoPlay);
    autoPlay = setInterval(() => {
        next.click();
    }, 5000);
}
let autoPlay = setInterval(() => {
    next.click();
}, 5000);

let currentSlide = 0;
const slides = document.querySelectorAll('.slide');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (i === index) {
            slide.classList.add('active');
        }
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

// Show the first slide initially
showSlide(currentSlide);

// Automatically change slide every 5 seconds
setInterval(nextSlide, 5000);


// categories slider
var swiperCategory = new Swiper(".categories__container", {
    spaceBetween: 24,
    loop: true,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        640: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 4,
            spaceBetween: 40,
        },
        1400: {
            slidesPerView: 6,
            spaceBetween: 24,
        },
    },
});

// Cart and Wishlist functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

// Update cart and wishlist counts
function updateCounts() {
    // Update cart count
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    }

    // Update wishlist count
    const wishlistCount = document.querySelector('.wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = wishlist.length;
    }
}

// Show success toast message
function showToast(message) {
    // Create toast element if it doesn't exist
    let toast = document.getElementById('success-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'success-toast';
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center';
        toast.innerHTML = `
            <i class="fas fa-check-circle mr-2"></i>
            <span></span>
        `;
        document.body.appendChild(toast);
    }
    
    // Update message and show toast
    toast.querySelector('span').textContent = message;
    toast.classList.remove('translate-y-full', 'opacity-0');
    
    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
    }, 3000);
}

// Initialize counts when page loads
document.addEventListener('DOMContentLoaded', () => {
    updateCounts();
    
    // Add to cart functionality for carousel items
    const addToCardButtons = document.querySelectorAll('.addToCard');
    addToCardButtons.forEach(button => {
        button.addEventListener('click', () => {
            const item = button.closest('.item');
            const product = {
                name: item.querySelector('h2').textContent,
                price: parseFloat(item.querySelector('.price').textContent.replace('₪ ', '')),
                quantity: 1,
                image: item.querySelector('.image img').src
            };

            const existingProduct = cart.find(p => p.name === product.name);
            if (existingProduct) {
                existingProduct.quantity++;
            } else {
                cart.push(product);
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCounts();
            
            // Show success message
            showToast('Product added to cart successfully!');
        });
    });
});

// Handle wishlist icon click
const wishlistIcon = document.getElementById('wishlist-icon');
if (wishlistIcon) {
    wishlistIcon.addEventListener('click', (e) => {
        e.preventDefault();
        showToast('Wishlist feature coming soon!');
    });
}

// Product data for search functionality
const products = [
    {
        name: "Custom Notebook",
        price: 20.00,
        image: "imgs/notebook2-removebg-preview.png",
        description: "PERSONALIZED CREATIONS, CRAFTED FOR YOU"
    },
    {
        name: "Custom Cover",
        price: 20.00,
        image: "imgs/cover2-removebg-preview.png",
        description: "Elegant and personalized covers for your books and devices"
    },
    {
        name: "Custom Hoodie",
        price: 60.00,
        image: "imgs/hoodi-removebg-preview.png",
        description: "Comfortable and stylish hoodies with Arabic calligraphy"
    },
    {
        name: "Custom Mug",
        price: 15.00,
        image: "imgs/mug-removebg-preview.png",
        description: "Personalized mugs with Arabic calligraphy"
    },
    {
        name: "Custom Handbag",
        price: 45.00,
        image: "imgs/زرف-removebg-preview.png",
        description: "Elegant handbags with unique Arabic designs"
    },
    {
        name: "Custom Pins",
        price: 10.00,
        image: "imgs/دبابيس.png",
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
                <a href="../ProductsPage/product.php" class="block p-4 hover:bg-gray-50 border-b last:border-b-0">
                    <div class="flex items-center">
                        <img src="${product.image}" alt="${product.name}" class="w-12 h-12 object-contain">
                        <div class="ml-3">
                            <h4 class="text-sm font-medium">${product.name}</h4>
                            <p class="text-xs text-gray-500">${product.description}</p>
                            <span class="text-sm text-primary font-semibold">₪${product.price.toFixed(2)}</span>
                        </div>
                    </div>
                </a>
            `).join('');
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
