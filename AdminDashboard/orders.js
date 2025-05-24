// TODO: Replace with database query to fetch all orders
// Example: SELECT * FROM orders JOIN customers ON orders.customer_id = customers.id
let orders = [
    {
        id: 'ORD-001',
        customer: {
            name: 'John Doe',
            email: 'john@example.com',
            phone: '+970 59-123-4567',
            address: 'Gaza Strip, Palestine'
        },
        products: [
            {
                id: 1,
                name: 'Custom Notebook',
                price: 20.00,
                quantity: 2,
                image: '../HomePage/imgs/notebook2-removebg-preview.png'
            }
        ],
        total: 40.00,
        status: 'pending',
        date: '2024-03-15T10:30:00',
        paymentMethod: 'Credit Card',
        shippingMethod: 'Standard Delivery'
    },
    {
        id: 'ORD-002',
        customer: {
            name: 'Jane Smith',
            email: 'jane@example.com',
            phone: '+970 59-234-5678',
            address: 'Gaza Strip, Palestine'
        },
        products: [
            {
                id: 2,
                name: 'Custom Hoodie',
                price: 60.00,
                quantity: 1,
                image: '../HomePage/imgs/hoodi-removebg-preview.png'
            }
        ],
        total: 60.00,
        status: 'processing',
        date: '2024-03-14T15:45:00',
        paymentMethod: 'PayPal',
        shippingMethod: 'Express Delivery'
    }
];

// DOM Elements
const ordersTableBody = document.getElementById('ordersTableBody');
const orderModal = document.getElementById('orderModal');
const orderDetails = document.getElementById('orderDetails');
const searchOrder = document.getElementById('searchOrder');
const statusFilter = document.getElementById('statusFilter');
const dateFilter = document.getElementById('dateFilter');
const exportOrders = document.getElementById('exportOrders');
const printOrders = document.getElementById('printOrders');

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    renderOrders();
    setupEventListeners();
});

function setupEventListeners() {
    searchOrder.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
    dateFilter.addEventListener('change', filterOrders);
    exportOrders.addEventListener('click', handleExportOrders);
    printOrders.addEventListener('click', handlePrintOrders);
}

// Render Orders
function renderOrders(filteredOrders = orders) {
    ordersTableBody.innerHTML = '';
    
    filteredOrders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${order.id}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${order.customer.name}</div>
                <div class="text-sm text-gray-500">${order.customer.email}</div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col space-y-2">
                    ${order.products.map(product => `
                        <div class="flex items-center">
                            <img src="${product.image}" alt="${product.name}" class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm">${product.name} (x${product.quantity})</span>
                        </div>
                    `).join('')}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">₪ ${order.total.toFixed(2)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${formatDate(order.date)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(order.status)}">
                    ${capitalizeFirstLetter(order.status)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewOrderDetails('${order.id}')" class="text-primary hover:text-primary-dark mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="printOrderDetails('${order.id}')" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-print"></i>
                </button>
            </td>
        `;
        ordersTableBody.appendChild(row);
    });
}

// Filter Orders
function filterOrders() {
    const searchTerm = searchOrder.value.toLowerCase();
    const statusValue = statusFilter.value;
    const dateValue = dateFilter.value;

    const filtered = orders.filter(order => {
        const matchesSearch = order.id.toLowerCase().includes(searchTerm) ||
                            order.customer.name.toLowerCase().includes(searchTerm) ||
                            order.customer.email.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusValue || order.status === statusValue;
        const matchesDate = !dateValue || isWithinDateRange(order.date, dateValue);

        return matchesSearch && matchesStatus && matchesDate;
    });

    renderOrders(filtered);
}

// View Order Details
function viewOrderDetails(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) return;

    orderDetails.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Name:</span> ${order.customer.name}</p>
                    <p><span class="font-medium">Email:</span> ${order.customer.email}</p>
                    <p><span class="font-medium">Phone:</span> ${order.customer.phone}</p>
                    <p><span class="font-medium">Address:</span> ${order.customer.address}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Order Information</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Order ID:</span> ${order.id}</p>
                    <p><span class="font-medium">Date:</span> ${formatDate(order.date)}</p>
                    <p><span class="font-medium">Payment Method:</span> ${order.paymentMethod}</p>
                    <p><span class="font-medium">Shipping Method:</span> ${order.shippingMethod}</p>
                    <p>
                        <span class="font-medium">Status:</span>
                        <select id="orderStatus" class="ml-2 px-2 py-1 border rounded">
                            ${['pending', 'processing', 'shipped', 'delivered', 'cancelled']
                                .map(status => `<option value="${status}" ${order.status === status ? 'selected' : ''}>
                                    ${capitalizeFirstLetter(status)}
                                </option>`).join('')}
                        </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Order Items</h3>
            <div class="space-y-4">
                ${order.products.map(product => `
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center">
                            <img src="${product.image}" alt="${product.name}" class="w-16 h-16 rounded object-cover">
                            <div class="ml-4">
                                <p class="font-medium">${product.name}</p>
                                <p class="text-gray-500">Quantity: ${product.quantity}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">₪ ${product.price.toFixed(2)}</p>
                            <p class="text-gray-500">Total: ₪ ${(product.price * product.quantity).toFixed(2)}</p>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="mt-6 text-right">
                <p class="text-lg font-semibold">Order Total: ₪ ${order.total.toFixed(2)}</p>
            </div>
        </div>
    `;

    orderModal.classList.remove('hidden');
    orderModal.classList.add('flex');
}

// Close Order Modal
function closeOrderModal() {
    orderModal.classList.remove('flex');
    orderModal.classList.add('hidden');
}

// Update Order Status
function updateOrderStatus() {
    const orderId = orderDetails.querySelector('p:nth-child(1)').textContent.split(': ')[1];
    const newStatus = document.getElementById('orderStatus').value;
    
    // TODO: Update order status in database
    // Example: UPDATE orders SET status = ? WHERE id = ?
    const orderIndex = orders.findIndex(o => o.id === orderId);
    if (orderIndex !== -1) {
        orders[orderIndex].status = newStatus;
        localStorage.setItem('orders', JSON.stringify(orders));
        renderOrders();
        closeOrderModal();
        showToast('Order status updated successfully!');
    }
}

// Export Orders
function handleExportOrders() {
    // TODO: Fetch orders from database with all related data
    // Example: SELECT * FROM orders 
    // JOIN customers ON orders.customer_id = customers.id
    // JOIN order_items ON orders.id = order_items.order_id
    // JOIN products ON order_items.product_id = products.id
    const csv = [
        ['Order ID', 'Customer Name', 'Email', 'Products', 'Total', 'Date', 'Status'],
        ...orders.map(order => [
            order.id,
            order.customer.name,
            order.customer.email,
            order.products.map(p => `${p.name} (x${p.quantity})`).join(', '),
            `₪ ${order.total.toFixed(2)}`,
            formatDate(order.date),
            capitalizeFirstLetter(order.status)
        ])
    ].map(row => row.join(',')).join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `orders_${formatDate(new Date().toISOString())}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Print Orders
function handlePrintOrders() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Orders Report</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; }
                </style>
            </head>
            <body>
                <h1>Orders Report</h1>
                <p>Generated on: ${formatDate(new Date().toISOString())}</p>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${orders.map(order => `
                            <tr>
                                <td>${order.id}</td>
                                <td>
                                    ${order.customer.name}<br>
                                    <small>${order.customer.email}</small>
                                </td>
                                <td>${order.products.map(p => `${p.name} (x${p.quantity})`).join('<br>')}</td>
                                <td>₪ ${order.total.toFixed(2)}</td>
                                <td>${formatDate(order.date)}</td>
                                <td>${capitalizeFirstLetter(order.status)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Print Individual Order
function printOrderDetails(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) return;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Order Details - ${order.id}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .section { margin-bottom: 30px; }
                    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; }
                    .total { text-align: right; margin-top: 20px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Order Details - ${order.id}</h1>
                    <p>Generated on: ${formatDate(new Date().toISOString())}</p>
                </div>
                <div class="grid">
                    <div class="section">
                        <h2>Customer Information</h2>
                        <p><strong>Name:</strong> ${order.customer.name}</p>
                        <p><strong>Email:</strong> ${order.customer.email}</p>
                        <p><strong>Phone:</strong> ${order.customer.phone}</p>
                        <p><strong>Address:</strong> ${order.customer.address}</p>
                    </div>
                    <div class="section">
                        <h2>Order Information</h2>
                        <p><strong>Order ID:</strong> ${order.id}</p>
                        <p><strong>Date:</strong> ${formatDate(order.date)}</p>
                        <p><strong>Payment Method:</strong> ${order.paymentMethod}</p>
                        <p><strong>Shipping Method:</strong> ${order.shippingMethod}</p>
                        <p><strong>Status:</strong> ${capitalizeFirstLetter(order.status)}</p>
                    </div>
                </div>
                <div class="section">
                    <h2>Order Items</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${order.products.map(product => `
                                <tr>
                                    <td>${product.name}</td>
                                    <td>₪ ${product.price.toFixed(2)}</td>
                                    <td>${product.quantity}</td>
                                    <td>₪ ${(product.price * product.quantity).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <div class="total">
                        <p>Order Total: ₪ ${order.total.toFixed(2)}</p>
                    </div>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Utility Functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function isWithinDateRange(dateString, range) {
    const date = new Date(dateString);
    const now = new Date();
    
    switch (range) {
        case 'today':
            return date.toDateString() === now.toDateString();
        case 'week':
            const weekAgo = new Date(now.setDate(now.getDate() - 7));
            return date >= weekAgo;
        case 'month':
            return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
        case 'year':
            return date.getFullYear() === now.getFullYear();
        default:
            return true;
    }
}

function getStatusColor(status) {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'processing': return 'bg-blue-100 text-blue-800';
        case 'shipped': return 'bg-purple-100 text-purple-800';
        case 'delivered': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
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