document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.getElementById('cart-items');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const checkoutButton = document.getElementById('checkout-button');
    const cartCountBadge = document.querySelector('.cart-count');
    const wishlistCountBadge = document.querySelector('.wishlist-count');
    const confirmModal = document.getElementById('confirm-modal');
    const cancelRemoveBtn = document.getElementById('cancel-remove');
    const confirmRemoveBtn = document.getElementById('confirm-remove');
    const successToast = document.getElementById('success-toast');
    
    // TEMPORARY DATA: Replace with database integration
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    let itemToRemove = null;

    // DATABASE TODO: Replace with actual wishlist count from database
    wishlistCountBadge.textContent = wishlist.length;

    // Function to show toast notification
    function showToast() {
        successToast.classList.remove('translate-y-full', 'opacity-0');
        setTimeout(() => {
            successToast.classList.add('translate-y-full', 'opacity-0');
        }, 3000);
    }

    function updateCartDisplay() {
        // DATABASE TODO: Replace with database operations for cart management
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCountBadge.textContent = totalItems;
        
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '';
            emptyCartMessage.classList.remove('hidden');
            checkoutButton.disabled = true;
            return;
        }

        emptyCartMessage.classList.add('hidden');
        checkoutButton.disabled = false;
        
        // Calculate totals
        const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        const shipping = 20; // Fixed shipping cost
        const total = subtotal + shipping;

        // Update totals display
        subtotalElement.textContent = `₪${subtotal.toFixed(2)}`;
        totalElement.textContent = `₪${total.toFixed(2)}`;

        // Generate cart items HTML
        cartItemsContainer.innerHTML = cart.map((item, index) => `
            <div class="flex items-center justify-between p-3 border-b hover:bg-gray-50 transition-colors" data-index="${index}">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="relative group">
                        <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-contain rounded-lg border border-gray-200">
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-sm">${item.name}</h3>
                            <p class="text-primary font-semibold text-sm">₪${item.price.toFixed(2)}</p>
                        </div>
                        ${item.design ? `<p class="text-xs text-gray-500">Design: ${item.design}</p>` : ''}
                        <div class="flex items-center mt-2 space-x-4">
                            <div class="flex items-center border rounded-full bg-gray-50">
                                <button class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded-l-full decrease-quantity">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-8 text-center text-sm quantity">${item.quantity}</span>
                                <button class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded-r-full increase-quantity">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500">Total: ₪${(item.price * item.quantity).toFixed(2)}</p>
                            <button class="text-red-500 hover:text-red-700 transition remove-item flex items-center space-x-1">
                                <i class="fas fa-trash-alt"></i>
                                <span class="text-xs">Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Add event listeners to cart item controls
        cartItemsContainer.querySelectorAll('.decrease-quantity').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = parseInt(e.target.closest('[data-index]').dataset.index);
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                    updateCartWithAnimation(e.target, 'decrease');
                }
            });
        });

        cartItemsContainer.querySelectorAll('.increase-quantity').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = parseInt(e.target.closest('[data-index]').dataset.index);
                cart[index].quantity++;
                updateCartWithAnimation(e.target, 'increase');
            });
        });

        cartItemsContainer.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (e) => {
                const cartItem = e.target.closest('[data-index]');
                itemToRemove = {
                    element: cartItem,
                    index: parseInt(cartItem.dataset.index)
                };
                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');
            });
        });
    }

    // Cancel remove
    cancelRemoveBtn.addEventListener('click', () => {
        confirmModal.classList.remove('flex');
        confirmModal.classList.add('hidden');
        itemToRemove = null;
    });

    // Confirm remove
    confirmRemoveBtn.addEventListener('click', () => {
        if (itemToRemove) {
            const { element, index } = itemToRemove;
            
            // Add fade-out animation
            element.style.transition = 'all 0.3s ease';
            element.style.opacity = '0';
            element.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                cart.splice(index, 1);
                updateCart();
                showToast();
            }, 300);
        }
        confirmModal.classList.remove('flex');
        confirmModal.classList.add('hidden');
        itemToRemove = null;
    });

    // Close modal when clicking outside
    confirmModal.addEventListener('click', (e) => {
        if (e.target === confirmModal) {
            confirmModal.classList.remove('flex');
            confirmModal.classList.add('hidden');
            itemToRemove = null;
        }
    });

    function updateCartWithAnimation(element, action) {
        const quantityElement = element.closest('[data-index]').querySelector('.quantity');
        const currentValue = parseInt(quantityElement.textContent);
        
        // Add pop animation
        quantityElement.style.transform = 'scale(1.2)';
        quantityElement.style.transition = 'transform 0.2s ease';
        
        setTimeout(() => {
            quantityElement.style.transform = 'scale(1)';
        }, 200);

        updateCart();
    }

    function updateCart() {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }

    // Initialize cart display
    updateCartDisplay();

    // Checkout button handler with animation
    checkoutButton.addEventListener('click', () => {
        checkoutButton.classList.add('animate-pulse');
        setTimeout(() => {
            window.location.href = '../CheckoutPage/checkout.php';
        }, 300);
    });

    // Add promo code functionality
    const promoInput = document.querySelector('input[placeholder="Enter code"]');
    const promoButton = promoInput.nextElementSibling;

    promoButton.addEventListener('click', () => {
        const code = promoInput.value.trim().toUpperCase();
        const validCodes = {
            'WELCOME10': 10,
            'SAVE20': 20,
            'SPECIAL30': 30
        };

        if (code in validCodes) {
            const discount = validCodes[code];
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            const discountAmount = (subtotal * discount) / 100;
            const newTotal = subtotal - discountAmount + 20; // Add shipping

            // Show success message
            promoButton.innerHTML = '<i class="fas fa-check"></i>';
            promoButton.classList.remove('bg-gray-200', 'text-gray-700');
            promoButton.classList.add('bg-green-500', 'text-white');

            // Update totals with animation
            subtotalElement.style.transition = 'color 0.3s ease';
            totalElement.style.transition = 'color 0.3s ease';
            subtotalElement.style.color = '#16a34a';
            totalElement.style.color = '#16a34a';

            setTimeout(() => {
                subtotalElement.textContent = `₪${(subtotal - discountAmount).toFixed(2)}`;
                totalElement.textContent = `₪${newTotal.toFixed(2)}`;
                subtotalElement.style.color = '';
                totalElement.style.color = '';
            }, 300);

            // Reset after 2 seconds
            setTimeout(() => {
                promoButton.innerHTML = 'Apply';
                promoButton.classList.remove('bg-green-500', 'text-white');
                promoButton.classList.add('bg-gray-200', 'text-gray-700');
                promoInput.value = '';
            }, 2000);
        } else if (code) {
            // Show error state
            promoButton.innerHTML = '<i class="fas fa-times"></i>';
            promoButton.classList.remove('bg-gray-200', 'text-gray-700');
            promoButton.classList.add('bg-red-500', 'text-white');

            setTimeout(() => {
                promoButton.innerHTML = 'Apply';
                promoButton.classList.remove('bg-red-500', 'text-white');
                promoButton.classList.add('bg-gray-200', 'text-gray-700');
            }, 2000);
        }
    });
}); 