// TODO: Replace with database query to fetch all customers
// Example: SELECT * FROM customers
let customers = JSON.parse(localStorage.getItem('customers')) || [
    {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        phone: '+970 59-123-4567',
        address: 'Gaza Strip, Palestine',
        notes: 'Regular customer, prefers custom notebooks',
        joinedDate: '2024-01-15T10:00:00'
    },
    {
        id: 2,
        name: 'Jane Smith',
        email: 'jane@example.com',
        phone: '+970 59-234-5678',
        address: 'Gaza Strip, Palestine',
        notes: 'Interested in bulk orders for hoodies',
        joinedDate: '2024-02-20T15:30:00'
    }
];

// TODO: Replace with database query to fetch all orders
// Example: SELECT * FROM orders WHERE customer_id IN (SELECT id FROM customers)
const orders = JSON.parse(localStorage.getItem('orders')) || [];

// DOM Elements
const customersTableBody = document.getElementById('customersTableBody');
const customerModal = document.getElementById('customerModal');
const customerDetailsModal = document.getElementById('customerDetailsModal');
const customerForm = document.getElementById('customerForm');
const addCustomerBtn = document.getElementById('addCustomer');
const searchCustomer = document.getElementById('searchCustomer');
const orderFilter = document.getElementById('orderFilter');
const dateFilter = document.getElementById('dateFilter');
const exportCustomers = document.getElementById('exportCustomers');

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    renderCustomers();
    setupEventListeners();
});

function setupEventListeners() {
    addCustomerBtn.addEventListener('click', () => openCustomerModal());
    customerForm.addEventListener('submit', handleCustomerSubmit);
    searchCustomer.addEventListener('input', filterCustomers);
    orderFilter.addEventListener('change', filterCustomers);
    dateFilter.addEventListener('change', filterCustomers);
    exportCustomers.addEventListener('click', handleExportCustomers);
}

// Render Customers
function renderCustomers(filteredCustomers = customers) {
    customersTableBody.innerHTML = '';
    
    filteredCustomers.forEach(customer => {
        const customerOrders = orders.filter(order => order.customer.email === customer.email);
        const totalSpent = customerOrders.reduce((total, order) => total + order.total, 0);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center text-lg font-semibold">
                            ${customer.name.charAt(0)}
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${customer.name}</div>
                        <div class="text-sm text-gray-500">${customer.notes || 'No notes'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${customer.email}</div>
                <div class="text-sm text-gray-500">${customer.phone}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${customerOrders.length} orders</div>
                <div class="text-sm text-gray-500">
                    ${customerOrders.length > 0 ? 'Last order: ' + formatDate(customerOrders[customerOrders.length - 1].date) : 'No orders'}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ₪ ${totalSpent.toFixed(2)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${formatDate(customer.joinedDate)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewCustomerDetails(${customer.id})" class="text-primary hover:text-primary-dark mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="editCustomerDetails(${customer.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteCustomer(${customer.id})" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        customersTableBody.appendChild(row);
    });
}

// Filter Customers
function filterCustomers() {
    const searchTerm = searchCustomer.value.toLowerCase();
    const orderValue = orderFilter.value;
    const dateValue = dateFilter.value;

    const filtered = customers.filter(customer => {
        const matchesSearch = customer.name.toLowerCase().includes(searchTerm) ||
                            customer.email.toLowerCase().includes(searchTerm) ||
                            customer.phone.toLowerCase().includes(searchTerm);
        
        const customerOrders = orders.filter(order => order.customer.email === customer.email);
        const matchesOrder = orderValue === '' ||
                           (orderValue === 'hasOrders' && customerOrders.length > 0) ||
                           (orderValue === 'noOrders' && customerOrders.length === 0);
        
        const matchesDate = !dateValue || isWithinDateRange(customer.joinedDate, dateValue);

        return matchesSearch && matchesOrder && matchesDate;
    });

    renderCustomers(filtered);
}

// Customer Modal Functions
function openCustomerModal(customer = null) {
    const modalTitle = document.getElementById('modalTitle');
    const customerId = document.getElementById('customerId');
    const customerName = document.getElementById('customerName');
    const customerEmail = document.getElementById('customerEmail');
    const customerPhone = document.getElementById('customerPhone');
    const customerAddress = document.getElementById('customerAddress');
    const customerNotes = document.getElementById('customerNotes');

    if (customer) {
        modalTitle.textContent = 'Edit Customer';
        customerId.value = customer.id;
        customerName.value = customer.name;
        customerEmail.value = customer.email;
        customerPhone.value = customer.phone;
        customerAddress.value = customer.address;
        customerNotes.value = customer.notes || '';
    } else {
        modalTitle.textContent = 'Add New Customer';
        customerForm.reset();
        customerId.value = '';
    }

    customerModal.classList.remove('hidden');
    customerModal.classList.add('flex');
}

function closeCustomerModal() {
    customerModal.classList.remove('flex');
    customerModal.classList.add('hidden');
    customerForm.reset();
}

// Handle Customer Form Submit
function handleCustomerSubmit(e) {
    e.preventDefault();

    const customerId = document.getElementById('customerId').value;
    const customerData = {
        id: customerId ? parseInt(customerId) : Date.now(),
        name: document.getElementById('customerName').value,
        email: document.getElementById('customerEmail').value,
        phone: document.getElementById('customerPhone').value,
        address: document.getElementById('customerAddress').value,
        notes: document.getElementById('customerNotes').value,
        joinedDate: customerId ? customers.find(c => c.id === parseInt(customerId)).joinedDate : new Date().toISOString()
    };

    if (customerId) {
        // TODO: Update customer in database
        // Example: UPDATE customers SET name = ?, email = ?, ... WHERE id = ?
        const index = customers.findIndex(c => c.id === parseInt(customerId));
        customers[index] = customerData;
    } else {
        // TODO: Insert new customer into database
        // Example: INSERT INTO customers (name, email, phone, ...) VALUES (...)
        customers.push(customerData);
    }

    // TODO: Replace with actual database save
    localStorage.setItem('customers', JSON.stringify(customers));
    renderCustomers();
    closeCustomerModal();
    showToast(customerId ? 'Customer updated successfully!' : 'Customer added successfully!');
}

// View Customer Details
function viewCustomerDetails(customerId) {
    const customer = customers.find(c => c.id === customerId);
    if (!customer) return;

    const customerOrders = orders.filter(order => order.customer.email === customer.email);
    const totalSpent = customerOrders.reduce((total, order) => total + order.total, 0);

    const customerDetails = document.getElementById('customerDetails');
    const customerOrders = document.getElementById('customerOrders');

    customerDetails.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Name:</span> ${customer.name}</p>
                    <p><span class="font-medium">Email:</span> ${customer.email}</p>
                    <p><span class="font-medium">Phone:</span> ${customer.phone}</p>
                    <p><span class="font-medium">Address:</span> ${customer.address}</p>
                    <p><span class="font-medium">Joined Date:</span> ${formatDate(customer.joinedDate)}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Customer Statistics</h3>
                <div class="space-y-2">
                    <p><span class="font-medium">Total Orders:</span> ${customerOrders.length}</p>
                    <p><span class="font-medium">Total Spent:</span> ₪ ${totalSpent.toFixed(2)}</p>
                    <p><span class="font-medium">Average Order Value:</span> ₪ ${customerOrders.length > 0 ? (totalSpent / customerOrders.length).toFixed(2) : '0.00'}</p>
                    <p><span class="font-medium">Notes:</span> ${customer.notes || 'No notes'}</p>
                </div>
            </div>
        </div>
    `;

    customerOrders.innerHTML = customerOrders.length > 0 ? customerOrders.map(order => `
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
                <div>
                    <span class="font-medium">${order.id}</span>
                    <span class="text-gray-500 text-sm ml-2">${formatDate(order.date)}</span>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(order.status)}">
                    ${capitalizeFirstLetter(order.status)}
                </span>
            </div>
            <div class="space-y-2">
                <div class="text-sm text-gray-600">
                    ${order.products.map(product => `
                        <div class="flex items-center justify-between">
                            <span>${product.name} (x${product.quantity})</span>
                            <span>₪ ${(product.price * product.quantity).toFixed(2)}</span>
                        </div>
                    `).join('')}
                </div>
                <div class="text-right font-medium">
                    Total: ₪ ${order.total.toFixed(2)}
                </div>
            </div>
        </div>
    `).join('') : '<p class="text-gray-500">No orders found</p>';

    customerDetailsModal.classList.remove('hidden');
    customerDetailsModal.classList.add('flex');
}

function closeCustomerDetailsModal() {
    customerDetailsModal.classList.remove('flex');
    customerDetailsModal.classList.add('hidden');
}

// Edit Customer
function editCustomerDetails(customerId) {
    const customer = customers.find(c => c.id === customerId);
    if (customer) {
        closeCustomerDetailsModal();
        openCustomerModal(customer);
    }
}

// Delete Customer
function deleteCustomer(customerId) {
    if (confirm('Are you sure you want to delete this customer?')) {
        // TODO: Delete customer from database
        // Example: DELETE FROM customers WHERE id = ?
        // Note: Consider handling related orders (CASCADE or SET NULL)
        customers = customers.filter(c => c.id !== customerId);
        localStorage.setItem('customers', JSON.stringify(customers));
        renderCustomers();
        showToast('Customer deleted successfully!');
    }
}

// Export Customers
function handleExportCustomers() {
    // TODO: Fetch customers with their order summaries from database
    // Example: SELECT c.*, 
    //         COUNT(o.id) as total_orders,
    //         SUM(o.total) as total_spent 
    // FROM customers c
    // LEFT JOIN orders o ON c.id = o.customer_id
    // GROUP BY c.id
    const csv = [
        ['Name', 'Email', 'Phone', 'Address', 'Joined Date', 'Total Orders', 'Total Spent'],
        ...customers.map(customer => {
            const customerOrders = orders.filter(order => order.customer.email === customer.email);
            const totalSpent = customerOrders.reduce((total, order) => total + order.total, 0);
            return [
                customer.name,
                customer.email,
                customer.phone,
                customer.address,
                formatDate(customer.joinedDate),
                customerOrders.length,
                `₪ ${totalSpent.toFixed(2)}`
            ];
        })
    ].map(row => row.join(',')).join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `customers_${formatDate(new Date().toISOString())}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
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