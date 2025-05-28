let orders = []; // Initially empty, will be populated from API

// DOM Elements
const ordersTableBody = document.getElementById('ordersTableBody');
const orderModal = document.getElementById('orderModal');
const orderDetails = document.getElementById('orderDetails');
const searchOrder = document.getElementById('searchOrder');
const statusFilter = document.getElementById('statusFilter');
const dateFilter = document.getElementById('dateFilter');
const exportOrders = document.getElementById('exportOrders');
const printOrders = document.getElementById('printOrders');

// Fetch orders from API
async function fetchOrders() {
    try {
        const response = await fetch('/api/orders');
        if (!response.ok) throw new Error('Failed to fetch orders');
        const data = await response.json();
        orders = data; // update orders array
        renderOrders();
    } catch (error) {
        console.error(error);
        showToast('Error fetching orders');
    }
}

// Initialize event listeners
function setupEventListeners() {
    searchOrder.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
    dateFilter.addEventListener('change', filterOrders);
    exportOrders.addEventListener('click', handleExportOrders);
    printOrders.addEventListener('click', handlePrintOrders);

    // Close modal on outside click or close button (you may need to add these handlers if you want)
    orderModal.querySelector('.close-btn')?.addEventListener('click', closeOrderModal);
    orderModal.addEventListener('click', (e) => {
        if (e.target === orderModal) closeOrderModal();
    });

    // Delegate update status button inside modal
    orderModal.addEventListener('change', async (e) => {
        if (e.target.id === 'orderStatus') {
            await updateOrderStatus();
        }
    });
}

// Render orders in table
function renderOrders(filteredOrders = orders) {
    ordersTableBody.innerHTML = '';

    if (filteredOrders.length === 0) {
        ordersTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">No orders found.</td></tr>`;
        return;
    }

    filteredOrders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">${order.id}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${order.customer.name}</div>
                <div class="text-sm text-gray-500">${order.customer.email}</div>
            </td>
            <td class="px-6 py-4">
                ${order.products.map(p => `
                    <div class="flex items-center space-x-2">
                        <img src="${p.image}" alt="${p.name}" class="w-8 h-8 rounded-full">
                        <span class="text-sm">${p.name} (x${p.quantity})</span>
                    </div>
                `).join('')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">₪ ${order.total.toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap">${formatDate(order.date)}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(order.status)}">
                    ${capitalizeFirstLetter(order.status)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewOrderDetails('${order.id}')" class="text-primary hover:text-primary-dark mr-3" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="printOrderDetails('${order.id}')" class="text-gray-600 hover:text-gray-900" title="Print Order">
                    <i class="fas fa-print"></i>
                </button>
            </td>
        `;
        ordersTableBody.appendChild(row);
    });
}

// Filter orders by search, status, and date
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

// Show order details in modal
function viewOrderDetails(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) return;

    orderDetails.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                <p><strong>Name:</strong> ${order.customer.name}</p>
                <p><strong>Email:</strong> ${order.customer.email}</p>
                <p><strong>Phone:</strong> ${order.customer.phone}</p>
                <p><strong>Address:</strong> ${order.customer.address}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Order Information</h3>
                <p><strong>Order ID:</strong> ${order.id}</p>
                <p><strong>Date:</strong> ${formatDate(order.date)}</p>
                <p><strong>Payment Method:</strong> ${order.paymentMethod}</p>
                <p><strong>Shipping Method:</strong> ${order.shippingMethod}</p>
                <p>
                    <strong>Status:</strong>
                    <select id="orderStatus" class="ml-2 px-2 py-1 border rounded">
                        ${['pending', 'processing', 'shipped', 'delivered', 'cancelled'].map(status => `
                            <option value="${status}" ${order.status === status ? 'selected' : ''}>${capitalizeFirstLetter(status)}</option>
                        `).join('')}
                    </select>
                </p>
            </div>
        </div>
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Order Items</h3>
            <div>
                ${order.products.map(product => `
                    <div class="flex items-center justify-between border-b py-2">
                        <div class="flex items-center space-x-4">
                            <img src="${product.image}" alt="${product.name}" class="w-16 h-16 rounded object-cover">
                            <div>
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
            <div class="mt-4 text-right font-semibold">
                Order Total: ₪ ${order.total.toFixed(2)}
            </div>
        </div>
    `;

    orderModal.classList.remove('hidden');
    orderModal.classList.add('flex');
}

// Close modal
function closeOrderModal() {
    orderModal.classList.remove('flex');
    orderModal.classList.add('hidden');
}

// Update order status in backend and local copy
async function updateOrderStatus() {
    const orderId = orderDetails.querySelector('p strong').nextSibling.textContent.trim() ||
        orderDetails.querySelector('p').textContent.split(': ')[1];
    const newStatus = document.getElementById('orderStatus').value;

    try {
        const response = await fetch(`/api/orders/${orderId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus }),
        });
        if (!response.ok) throw new Error('Failed to update status');

        // Update local order status
        const idx = orders.findIndex(o => o.id === orderId);
        if (idx !== -1) {
            orders[idx].status = newStatus;
            renderOrders();
            showToast('Order status updated successfully!');
        }
        closeOrderModal();
    } catch (error) {
        console.error(error);
        showToast('Failed to update order status');
    }
}

// Export orders as CSV
async function handleExportOrders() {
    try {
        const response = await fetch('/api/orders');
        if (!response.ok) throw new Error('Failed to fetch orders');
        const exportOrders = await response.json();

        const csvRows = [
            ['Order ID', 'Customer Name', 'Email', 'Products', 'Total', 'Date', 'Status'],
            ...exportOrders.map(order => [
                order.id,
                order.customer.name,
                order.customer.email,
                order.products.map(p => `${p.name} (x${p.quantity})`).join('; '),
                `₪ ${order.total.toFixed(2)}`,
                formatDate(order.date),
                capitalizeFirstLetter(order.status)
            ])
        ];

        const csvContent = csvRows.map(row => row.map(field => `"${field}"`).join(',')).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `orders_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error(error);
        showToast('Failed to export orders');
    }
}

// Print the currently displayed orders table
function handlePrintOrders() {
    const printContent = document.getElementById('ordersTable').outerHTML;
    const newWindow = window.open('', '', 'width=900,height=600');
    newWindow.document.write(`
        <html>
        <head><title>Print Orders</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
            th { background-color: #f3f4f6; }
        </style>
        </head>
        <body>${printContent}</body>
        </html>`);
    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
    newWindow.close();
}

// Helper: Format date string
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

// Helper: Check if order date matches filter
function isWithinDateRange(orderDateStr, filterValue) {
    // For simplicity: filterValue can be 'today', 'week', 'month'
    const orderDate = new Date(orderDateStr);
    const today = new Date();
    switch (filterValue) {
        case 'today':
            return orderDate.toDateString() === today.toDateString();
        case 'week':
            // Check if orderDate is within last 7 days
            const weekAgo = new Date(today);
            weekAgo.setDate(today.getDate() - 7);
            return orderDate >= weekAgo && orderDate <= today;
        case 'month':
            return orderDate.getMonth() === today.getMonth() && orderDate.getFullYear() === today.getFullYear();
        default:
            return true;
    }
}

// Helper: Capitalize first letter
function capitalizeFirstLetter(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Helper: Return CSS class for order status badges
function getStatusColor(status) {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'processing': return 'bg-blue-100 text-blue-800';
        case 'shipped': return 'bg-indigo-100 text-indigo-800';
        case 'delivered': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Show toast notifications (basic example)
function showToast(message) {
    alert(message);
}

// Print a single order details (opens print dialog)
function printOrderDetails(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) return;

    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
        <html>
        <head><title>Print Order ${order.id}</title></head>
        <body>
            <h2>Order ID: ${order.id}</h2>
            <p><strong>Customer:</strong> ${order.customer.name} (${order.customer.email})</p>
            <p><strong>Date:</strong> ${formatDate(order.date)}</p>
            <h3>Products</h3>
            <ul>
                ${order.products.map(p => `<li>${p.name} x${p.quantity} - ₪${p.price.toFixed(2)} each</li>`).join('')}
            </ul>
            <p><strong>Total:</strong> ₪ ${order.total.toFixed(2)}</p>
            <p><strong>Status:</strong> ${capitalizeFirstLetter(order.status)}</p>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// Initialize the page
function init() {
    fetchOrders();
    setupEventListeners();
}

window.onload = init;
