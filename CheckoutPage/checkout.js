document.addEventListener('DOMContentLoaded', () => {
    const checkoutItemsContainer = document.getElementById('checkout-items');
    const subtotalElement = document.getElementById('checkout-subtotal');
    const totalElement = document.getElementById('checkout-total');
    const placeOrderButton = document.getElementById('place-order-button');
    
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

    function displayCheckoutItems() {
        checkoutItemsContainer.innerHTML = '';
        
        cartItems.forEach(item => {
            const isClothing = item.name.toLowerCase().includes('hoodie') || 
                             item.name.toLowerCase().includes('t-shirt') || 
                             item.name.toLowerCase().includes('shirt') || 
                             item.name.toLowerCase().includes('clothing');

            const itemElement = document.createElement('div');
            itemElement.className = 'flex justify-between items-center';
            itemElement.innerHTML = `
                <div class="flex items-center space-x-3">
                    <img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded">
                    <div>
                        <h4 class="font-medium">${item.name}</h4>
                        <p class="text-sm text-gray-600">Color: ${item.color}${isClothing ? `, Size: ${item.size}` : ''}</p>
                        <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                    </div>
                </div>
                <span class="font-medium">${item.price}</span>
            `;
            checkoutItemsContainer.appendChild(itemElement);
        });

        updateTotals();
    }

    function updateTotals() {
        const subtotal = cartItems.reduce((total, item) => {
            const price = parseFloat(item.price.replace('₪', ''));
            return total + (price * item.quantity);
        }, 0);

        const shipping = cartItems.length > 0 ? 20 : 0;
        const total = subtotal + shipping;

        subtotalElement.textContent = `₪${subtotal.toFixed(2)}`;
        totalElement.textContent = `₪${total.toFixed(2)}`;
    }

    // Form validation
    const shippingForm = document.getElementById('shipping-form');
    const paymentForm = document.getElementById('payment-form');

    function validateForms() {
        const shippingValid = Array.from(shippingForm.elements)
            .filter(element => element.hasAttribute('required'))
            .every(element => element.value.trim() !== '');

        const paymentValid = Array.from(paymentForm.elements)
            .filter(element => element.hasAttribute('required'))
            .every(element => element.value.trim() !== '');

        return shippingValid && paymentValid;
    }

    // Payment form input formatting
    const cardNumberInput = paymentForm.querySelector('input[placeholder="1234 5678 9012 3456"]');
    const expiryInput = paymentForm.querySelector('input[placeholder="MM/YY"]');
    const cvvInput = paymentForm.querySelector('input[placeholder="123"]');

    cardNumberInput.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{4})/g, '$1 ').trim();
        e.target.value = value.substring(0, 19);
    });

    expiryInput.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2);
        }
        e.target.value = value.substring(0, 5);
    });

    cvvInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 3);
    });

    // Place order functionality
    placeOrderButton.addEventListener('click', () => {
        if (!validateForms()) {
            alert('Please fill in all required fields');
            return;
        }

        // Here you would typically send the order to a backend service
        alert('Order placed successfully!');
        localStorage.removeItem('cartItems');
        window.location.href = '../index.html';
    });

    // Initialize checkout display
    displayCheckoutItems();
}); 