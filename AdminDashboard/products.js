// TODO: Replace with database query to fetch all products
// Example: SELECT * FROM products
let products = JSON.parse(localStorage.getItem('products')) || [
    {
        id: 1,
        name: 'Custom Notebook',
        category: 'notebooks',
        price: 20.00,
        stock: 50,
        description: 'PERSONALIZED CREATIONS, CRAFTED FOR YOU',
        image: '../HomePage/imgs/notebook2-removebg-preview.png',
        status: 'inStock'
    },
    {
        id: 2,
        name: 'Custom Hoodie',
        category: 'hoodies',
        price: 60.00,
        stock: 30,
        description: 'Comfortable and stylish hoodies with Arabic calligraphy',
        image: '../HomePage/imgs/hoodi-removebg-preview.png',
        status: 'inStock'
    },
    {
        id: 3,
        name: 'Custom Mug',
        category: 'mugs',
        price: 15.00,
        stock: 5,
        description: 'Personalized mugs with Arabic calligraphy',
        image: '../HomePage/imgs/mug-removebg-preview.png',
        status: 'lowStock'
    }
];

// DOM Elements
const productsTableBody = document.getElementById('productsTableBody');
const productModal = document.getElementById('productModal');
const productForm = document.getElementById('productForm');
const addProductBtn = document.getElementById('addProductBtn');
const searchProduct = document.getElementById('searchProduct');
const categoryFilter = document.getElementById('categoryFilter');
const statusFilter = document.getElementById('statusFilter');

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    renderProducts();
    setupEventListeners();
});

function setupEventListeners() {
    addProductBtn.addEventListener('click', () => openProductModal());
    productForm.addEventListener('submit', handleProductSubmit);
    searchProduct.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    statusFilter.addEventListener('change', filterProducts);
}

// Render Products
function renderProducts(filteredProducts = products) {
    productsTableBody.innerHTML = '';
    
    filteredProducts.forEach(product => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                        <img class="h-10 w-10 rounded-full object-cover" src="${product.image}" alt="${product.name}">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${product.name}</div>
                        <div class="text-sm text-gray-500">${product.description.substring(0, 50)}...</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    ${getCategoryColor(product.category)}">
                    ${capitalizeFirstLetter(product.category)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">₪ ${product.price.toFixed(2)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${product.stock}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    ${getStatusColor(product.status)}">
                    ${getStatusText(product.status)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editProduct(${product.id})" class="text-primary hover:text-primary-dark mr-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        productsTableBody.appendChild(row);
    });
}

// Filter Products
function filterProducts() {
    const searchTerm = searchProduct.value.toLowerCase();
    const categoryValue = categoryFilter.value;
    const statusValue = statusFilter.value;

    const filtered = products.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchTerm) ||
                            product.description.toLowerCase().includes(searchTerm);
        const matchesCategory = !categoryValue || product.category === categoryValue;
        const matchesStatus = !statusValue || product.status === statusValue;

        return matchesSearch && matchesCategory && matchesStatus;
    });

    renderProducts(filtered);
}

// Modal Functions
function openProductModal(product = null) {
    const modalTitle = document.getElementById('modalTitle');
    const productId = document.getElementById('productId');
    const productName = document.getElementById('productName');
    const productCategory = document.getElementById('productCategory');
    const productPrice = document.getElementById('productPrice');
    const productStock = document.getElementById('productStock');
    const productDescription = document.getElementById('productDescription');

    if (product) {
        modalTitle.textContent = 'Edit Product';
        productId.value = product.id;
        productName.value = product.name;
        productCategory.value = product.category;
        productPrice.value = product.price;
        productStock.value = product.stock;
        productDescription.value = product.description;
    } else {
        modalTitle.textContent = 'Add New Product';
        productForm.reset();
        productId.value = '';
    }

    productModal.classList.remove('hidden');
    productModal.classList.add('flex');
}

function closeProductModal() {
    productModal.classList.remove('flex');
    productModal.classList.add('hidden');
    productForm.reset();
}

// Handle Form Submit
async function handleProductSubmit(e) {
    e.preventDefault();

    const productId = document.getElementById('productId').value;
    const productData = {
        id: productId ? parseInt(productId) : Date.now(),
        name: document.getElementById('productName').value,
        category: document.getElementById('productCategory').value,
        price: parseFloat(document.getElementById('productPrice').value),
        stock: parseInt(document.getElementById('productStock').value),
        description: document.getElementById('productDescription').value,
        status: getProductStatus(parseInt(document.getElementById('productStock').value))
    };

    // Handle image upload
    const imageFile = document.getElementById('productImage').files[0];
    if (imageFile) {
        // TODO: Implement file upload to server/cloud storage
        // Example: Upload to AWS S3 or similar service
        productData.image = await readFileAsDataURL(imageFile);
    } else if (!productId) {
        productData.image = getDefaultProductImage(productData.category);
    } else {
        const existingProduct = products.find(p => p.id === parseInt(productId));
        productData.image = existingProduct.image;
    }

    if (productId) {
        // TODO: Update product in database
        // Example: UPDATE products SET ... WHERE id = productId
        const index = products.findIndex(p => p.id === parseInt(productId));
        products[index] = { ...products[index], ...productData };
    } else {
        // TODO: Insert new product into database
        // Example: INSERT INTO products (name, category, price, ...) VALUES (...)
        products.push(productData);
    }

    // TODO: Replace with actual database save
    localStorage.setItem('products', JSON.stringify(products));
    renderProducts();
    closeProductModal();
    showToast(productId ? 'Product updated successfully!' : 'Product added successfully!');
}

// Edit Product
function editProduct(id) {
    const product = products.find(p => p.id === id);
    if (product) {
        openProductModal(product);
    }
}

// Delete Product
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        // TODO: Delete product from database
        // Example: DELETE FROM products WHERE id = ?
        products = products.filter(p => p.id !== id);
        localStorage.setItem('products', JSON.stringify(products));
        renderProducts();
        showToast('Product deleted successfully!');
    }
}

// Utility Functions
function getProductStatus(stock) {
    if (stock <= 0) return 'outOfStock';
    if (stock <= 10) return 'lowStock';
    return 'inStock';
}

function getStatusColor(status) {
    switch (status) {
        case 'inStock': return 'bg-green-100 text-green-800';
        case 'lowStock': return 'bg-yellow-100 text-yellow-800';
        case 'outOfStock': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusText(status) {
    switch (status) {
        case 'inStock': return 'In Stock';
        case 'lowStock': return 'Low Stock';
        case 'outOfStock': return 'Out of Stock';
        default: return status;
    }
}

function getCategoryColor(category) {
    switch (category) {
        case 'notebooks': return 'bg-blue-100 text-blue-800';
        case 'hoodies': return 'bg-purple-100 text-purple-800';
        case 'mugs': return 'bg-green-100 text-green-800';
        case 'covers': return 'bg-pink-100 text-pink-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getDefaultProductImage(category) {
    const images = {
        notebooks: '../HomePage/imgs/notebook2-removebg-preview.png',
        hoodies: '../HomePage/imgs/hoodi-removebg-preview.png',
        mugs: '../HomePage/imgs/mug-removebg-preview.png',
        covers: '../HomePage/imgs/cover2-removebg-preview.png'
    };
    return images[category] || images.notebooks;
}

function readFileAsDataURL(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// Toast Notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-0 opacity-100 transition-all duration-300';
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
} 