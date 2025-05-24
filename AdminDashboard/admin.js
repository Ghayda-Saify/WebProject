// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', () => {
    // Initialize notifications
    const notifications = [
        { title: 'New Order', message: 'Order #1234 has been placed', time: '2 minutes ago' },
        { title: 'Low Stock Alert', message: 'Custom Notebook is running low', time: '5 minutes ago' },
        { title: 'New Review', message: 'Customer left a 5-star review', time: '10 minutes ago' }
    ];

    // Handle mobile navigation
    const menuButton = document.createElement('button');
    menuButton.className = 'md:hidden fixed top-4 left-4 z-50 p-2 bg-primary text-white rounded-lg';
    menuButton.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.appendChild(menuButton);

    menuButton.addEventListener('click', () => {
        document.querySelector('aside').classList.toggle('translate-x-0');
    });

    // Handle notifications dropdown
    const notificationButton = document.querySelector('.fa-bell').parentElement;
    const notificationDropdown = document.createElement('div');
    notificationDropdown.className = 'absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden';
    notificationDropdown.innerHTML = `
        <div class="p-4 border-b">
            <h3 class="font-semibold">Notifications</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            ${notifications.map(notification => `
                <div class="p-4 border-b hover:bg-gray-50 cursor-pointer">
                    <p class="font-medium text-sm">${notification.title}</p>
                    <p class="text-gray-600 text-sm">${notification.message}</p>
                    <p class="text-gray-400 text-xs mt-1">${notification.time}</p>
                </div>
            `).join('')}
        </div>
        <div class="p-4 text-center">
            <a href="#" class="text-primary text-sm font-medium">View All Notifications</a>
        </div>
    `;

    notificationButton.parentElement.appendChild(notificationDropdown);

    notificationButton.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!notificationDropdown.contains(e.target) && !notificationButton.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });

    // Quick Actions
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach(action => {
        action.addEventListener('click', () => {
            // Handle quick action clicks
            const actionType = action.querySelector('span').textContent.trim();
            switch(actionType) {
                case 'Add New Product':
                    // Implement add product functionality
                    console.log('Adding new product...');
                    break;
                case 'Create Discount':
                    // Implement discount creation
                    console.log('Creating discount...');
                    break;
                case 'Send Newsletter':
                    // Implement newsletter sending
                    console.log('Sending newsletter...');
                    break;
            }
        });
    });

    // Add hover effect to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', () => {
            row.classList.add('bg-gray-50');
        });
        row.addEventListener('mouseleave', () => {
            row.classList.remove('bg-gray-50');
        });
    });

    // Initialize stats animation
    const stats = document.querySelectorAll('.stats-number');
    stats.forEach(stat => {
        const finalValue = parseInt(stat.textContent.replace(/[^0-9]/g, ''));
        let currentValue = 0;
        const duration = 1000; // 1 second
        const increment = finalValue / (duration / 16); // 60fps

        const animate = () => {
            currentValue = Math.min(currentValue + increment, finalValue);
            stat.textContent = Math.floor(currentValue).toLocaleString();

            if (currentValue < finalValue) {
                requestAnimationFrame(animate);
            }
        };

        animate();
    });

    // Add active state to navigation items
    const navItems = document.querySelectorAll('nav a');
    const currentPath = window.location.pathname;
    
    // Set active state based on current page
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (currentPath.endsWith(href)) {
            item.classList.add('active');
        }
    });

    // Update active state on click (without preventing navigation)
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            navItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });
}); 