// Product data structure
const products = [
    {
        id: 1,
        name: "كفر ايفون القدس | Jerusalem iPhone Cover",
        arabicName: "كفر ايفون القدس",
        category: "covers",
        price: 20.00,
        description: "iPhone case featuring Al-Aqsa Mosque design",
        arabicDescription: "كفر ايفون مع تصميم المسجد الأقصى",
        image: "imgs/cover.jpg"
    },
    {
        id: 2,
        name: "دفتر سلك فلسطين | Palestine Wire Notebook",
        arabicName: "دفتر سلك فلسطين",
        category: "wire-notebooks",
        price: 25.00,
        description: "Wire-bound notebook with Palestinian embroidery patterns",
        arabicDescription: "دفتر سلك مع نقوش التطريز الفلسطيني",
        image: "imgs/notebook.jpg"
    },
    {
        id: 3,
        name: "كفر جالكسي مطرز | Embroidered Galaxy Cover",
        arabicName: "كفر جالكسي مطرز",
        category: "covers",
        price: 20.00,
        description: "Samsung Galaxy case with traditional Palestinian embroidery",
        arabicDescription: "كفر جالكسي مع تطريز فلسطيني تقليدي",
        image: "imgs/covers.jpg"
    },
    {
        id: 4,
        name: "طقم أقلام القدس | Jerusalem Pen Set",
        arabicName: "طقم أقلام القدس",
        category: "stationery",
        price: 30.00,
        description: "Luxury pen set with Jerusalem-inspired designs",
        arabicDescription: "طقم أقلام فاخر مع تصاميم مقدسية",
        image: "imgs/دبابيس.png"
    },
    {
        id: 5,
        name: "دفتر سلك مقدسي | Jerusalem Wire Notebook",
        arabicName: "دفتر سلك مقدسي",
        category: "wire-notebooks",
        price: 25.00,
        description: "Wire-bound notebook featuring Jerusalem landmarks",
        arabicDescription: "دفتر سلك يضم معالم القدس",
        image: "imgs/bord.jpg"
    },
    {
        id: 6,
        name: "هودي مطرز | Embroidered Hoodie",
        arabicName: "هودي مطرز",
        category: "custom",
        price: 60.00,
        description: "Custom hoodie with Palestinian embroidery",
        arabicDescription: "هودي مع تطريز فلسطيني",
        image: "imgs/hoodies.jpg"
    },
    {
        id: 7,
        name: "دفتر سلك مزخرف | Decorated Wire Notebook",
        arabicName: "دفتر سلك مزخرف",
        category: "wire-notebooks",
        price: 25.00,
        description: "Wire-bound notebook with Islamic geometric patterns",
        arabicDescription: "دفتر سلك مع زخارف هندسية إسلامية",
        image: "imgs/book.jpg"
    },
    {
        id: 8,
        name: "أقلام ملونة القدس | Jerusalem Colored Pens",
        arabicName: "أقلام ملونة القدس",
        category: "stationery",
        price: 15.00,
        description: "Set of colored pens with Jerusalem designs",
        arabicDescription: "مجموعة أقلام ملونة مع تصاميم القدس",
        image: "imgs/مجموعة دبابيس.jpg"
    },
    {
        id: 9,
        name: "حقيبة مطرزة | Embroidered Bag",
        arabicName: "حقيبة مطرزة",
        category: "custom",
        price: 45.00,
        description: "Custom designed bag with Palestinian embroidery",
        arabicDescription: "حقيبة مع تطريز فلسطيني",
        image: "imgs/زرف.jpg"
    },
    {
        id: 10,
        name: "مج مطبوع | Custom Mug",
        arabicName: "مج مطبوع",
        category: "custom",
        price: 15.00,
        description: "Custom printed mug with your design",
        arabicDescription: "مج مطبوع حسب التصميم",
        image: "imgs/mugs.jpg"
    },
    {
        id: 11,
        name: "كفر ايفون التطريز | Embroidery iPhone Cover",
        arabicName: "كفر ايفون التطريز",
        category: "palestine",
        price: 20.00,
        description: "iPhone case with traditional Palestinian embroidery patterns",
        arabicDescription: "كفر ايفون مع نقوش التطريز الفلسطيني التقليدي",
        image: "imgs/cover2-removebg-preview.png"
    },
    {
        id: 12,
        name: "دفتر سلك القبة | Dome Wire Notebook",
        arabicName: "دفتر سلك القبة",
        category: "jerusalem",
        price: 25.00,
        description: "Wire-bound notebook featuring Dome of the Rock",
        arabicDescription: "دفتر سلك مع تصميم قبة الصخرة",
        image: "imgs/notebook2.jpg"
    }
];

document.addEventListener('DOMContentLoaded', () => {
    const productsGrid = document.getElementById('products-grid');
    const cartCountElement = document.querySelector('.cart-count');
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

    // Create and render product cards
    function createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card bg-white rounded-lg shadow-md overflow-hidden relative group cursor-pointer transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl';
        
        card.innerHTML = `
            <div class="aspect-square overflow-hidden">
                <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover transform transition-transform duration-300 group-hover:scale-105">
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-1" dir="rtl">${product.arabicName}</h3>
                <h4 class="text-md font-medium mb-2">${product.name}</h4>
                <p class="text-sm text-gray-600 mb-1">${product.description}</p>
                <p class="text-sm text-gray-600 mb-2" dir="rtl">${product.arabicDescription}</p>
                <div class="flex justify-between items-center">
                    <p class="text-lg font-bold text-primary">$${product.price.toFixed(2)}</p>
                    <button class="add-to-cart-btn bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-colors duration-300">
                        Add to Cart
                    </button>
                </div>
            </div>
        `;

        // Add click event to open modal
        card.addEventListener('click', () => openProductModal(product));

        return card;
    }

    function renderProducts(productsToRender) {
        productsGrid.innerHTML = '';
        productsToRender.forEach(product => {
            const card = createProductCard(product);
            productsGrid.appendChild(card);
        });
        updatePagination();
    }

    // Update cart count
    function updateCartCount() {
        cartCountElement.textContent = cartItems.length;
    }
    updateCartCount();

    // Cart link functionality
    document.querySelector('.cart-trigger').addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = '../CartPage/cart.html';
    });

    // Pagination functionality
    const itemsPerPage = 6;
    let currentPage = 1;
    let totalPages = Math.ceil(products.length / itemsPerPage);

    function updatePagination() {
        const visibleProducts = document.querySelectorAll('.product-card:not([style*="display: none"])');
        totalPages = Math.ceil(visibleProducts.length / itemsPerPage);
        showPage(1);
    }

    function showPage(pageNumber) {
        const startIndex = (pageNumber - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        document.querySelectorAll('.product-card').forEach((product, index) => {
            if (index >= startIndex && index < endIndex) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });

        updatePaginationNumbers(pageNumber);
    }

    function updatePaginationNumbers(currentPage) {
        const paginationContainer = document.querySelector('.pagination-numbers');
        paginationContainer.innerHTML = '';

        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, startPage + 2);

        if (endPage - startPage < 2) {
            if (startPage === 1) {
                endPage = Math.min(3, totalPages);
            } else {
                startPage = Math.max(1, endPage - 2);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const button = document.createElement('button');
            button.className = `pagination-button px-4 py-2 border rounded ${i === currentPage ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'}`;
            button.textContent = i;
            button.addEventListener('click', () => {
                currentPage = i;
                showPage(currentPage);
            });
            paginationContainer.appendChild(button);
        }
    }

    // Initialize pagination
    document.querySelector('.pagination-prev').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    document.querySelector('.pagination-next').addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    });

    // Product Modal functionality
    const modal = document.getElementById('product-modal');
    let selectedColor = '';
    let selectedSize = '';

    function openProductModal(product) {
        const modalImage = modal.querySelector('img');
        const modalTitle = modal.querySelector('h2');
        const modalDescription = modal.querySelector('p');
        const modalPrice = modal.querySelector('.text-blue-600');
        const sizeSection = modal.querySelector('.size-section');
        
        modalImage.src = product.image;
        modalImage.alt = product.name;
        modalTitle.textContent = product.name;
        modalDescription.textContent = product.description;
        modalPrice.textContent = `$${product.price.toFixed(2)}`;

        // Show/hide size section based on category
        const isClothing = product.category === 'clothing';
        sizeSection.style.display = isClothing ? 'block' : 'none';

        selectedColor = '';
        selectedSize = '';

        modal.classList.remove('hidden');
    }

    // Close modal
    document.querySelector('.modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Color selection in modal
    modal.querySelectorAll('.color-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.querySelectorAll('.color-btn').forEach(b => b.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-600'));
            btn.classList.add('ring-2', 'ring-offset-2', 'ring-blue-600');
            selectedColor = btn.dataset.color;
        });
    });

    // Size selection in modal
    modal.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.querySelectorAll('.size-btn').forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
            btn.classList.add('bg-blue-600', 'text-white');
            selectedSize = btn.textContent;
        });
    });

    // Add to cart functionality
    function addToCart(product) {
        const cartItem = {
            id: Date.now(),
            name: product.name,
            price: product.price,
            image: product.image,
            color: selectedColor,
            size: selectedSize || 'N/A',
            quantity: 1
        };

        cartItems.push(cartItem);
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        updateCartCount();
        showToast(`Added to cart: ${product.name}`);
    }

    // Toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-0 z-50';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.transform = 'translateY(200%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Filter functionality
    const filterForm = document.querySelector('aside');
    const searchInput = document.getElementById('filter-search');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const categoryCheckboxes = document.querySelectorAll('input[type="checkbox"]');

    // Initialize filter values
    let currentFilters = {
        categories: new Set(['all']),
        minPrice: 0,
        maxPrice: Infinity,
        searchTerm: '',
        viewMode: 'grid'
    };

    // Category filter
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (checkbox.value === 'all' && checkbox.checked) {
                // If 'all' is checked, uncheck others
                categoryCheckboxes.forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                    }
                });
                currentFilters.categories = new Set(['all']);
            } else {
                // If a specific category is checked
                const allCheckbox = document.querySelector('input[value="all"]');
                if (checkbox.checked) {
                    // Uncheck 'all' when a specific category is selected
                    allCheckbox.checked = false;
                    currentFilters.categories.delete('all');
                    currentFilters.categories.add(checkbox.value);
                } else {
                    // If unchecking a category
                    currentFilters.categories.delete(checkbox.value);
                    // If no categories are selected, check 'all'
                    if (currentFilters.categories.size === 0) {
                        allCheckbox.checked = true;
                        currentFilters.categories = new Set(['all']);
                    }
                }
            }
            // Apply filters immediately when category changes
            filterProducts();
        });
    });

    // Search filter
    searchInput.addEventListener('input', (e) => {
        currentFilters.searchTerm = e.target.value.toLowerCase();
    });

    // Price filter
    minPriceInput.addEventListener('input', (e) => {
        currentFilters.minPrice = parseFloat(e.target.value) || 0;
    });

    maxPriceInput.addEventListener('input', (e) => {
        currentFilters.maxPrice = parseFloat(e.target.value) || Infinity;
    });

    // Apply filters
    function filterProducts() {
        let filteredProducts = [...products];

        // Category filter
        if (!currentFilters.categories.has('all')) {
            filteredProducts = filteredProducts.filter(product =>
                currentFilters.categories.has(product.category)
            );
        }

        // Price filter
        filteredProducts = filteredProducts.filter(product =>
            product.price >= currentFilters.minPrice &&
            (currentFilters.maxPrice === Infinity || product.price <= currentFilters.maxPrice)
        );

        // Search filter
        if (currentFilters.searchTerm) {
            filteredProducts = filteredProducts.filter(product =>
                product.name.toLowerCase().includes(currentFilters.searchTerm) ||
                product.arabicName.toLowerCase().includes(currentFilters.searchTerm) ||
                product.description.toLowerCase().includes(currentFilters.searchTerm) ||
                product.arabicDescription.toLowerCase().includes(currentFilters.searchTerm)
            );
        }

        renderProducts(filteredProducts);
        updateViewMode();
    }

    // Apply filters button
    applyFiltersBtn.addEventListener('click', filterProducts);

    // View mode functionality
    const gridViewBtn = document.querySelector('button:has(.ri-grid-line)');
    const listViewBtn = document.querySelector('button:has(.ri-list-check-2)');

    function updateViewMode() {
        const productsGrid = document.querySelector('.grid');
        if (currentFilters.viewMode === 'grid') {
            productsGrid.className = 'grid grid-cols-2 md:grid-cols-3 gap-1 md:gap-2';
            gridViewBtn.classList.remove('text-gray-400');
            listViewBtn.classList.add('text-gray-400');
        } else {
            productsGrid.className = 'grid grid-cols-1 gap-4';
            listViewBtn.classList.remove('text-gray-400');
            gridViewBtn.classList.add('text-gray-400');
        }
    }

    gridViewBtn.addEventListener('click', () => {
        currentFilters.viewMode = 'grid';
        updateViewMode();
    });

    listViewBtn.addEventListener('click', () => {
        currentFilters.viewMode = 'list';
        updateViewMode();
    });

    // Initial render of all products
    renderProducts(products);
    showPage(1);

    // Initialize filters
    filterProducts();
});