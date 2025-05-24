document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.search-container input');
    const searchIcon = document.querySelector('.search-container i');

    function performSearch(searchTerm) {
        // Store the search term in sessionStorage (store original input for display purposes)
        sessionStorage.setItem('searchTerm', searchTerm);
        sessionStorage.setItem('searchTermLower', searchTerm.toLowerCase());
        
        // If we're not on the products page, redirect to it
        if (!window.location.href.includes('ProductsPage/product.php')) {
            window.location.href = '../ProductsPage/product.php';
            return;
        }

        // If we're already on the products page, trigger the filter
        const searchEvent = new CustomEvent('performSearch', {
            detail: { 
                searchTerm: searchTerm,
                searchTermLower: searchTerm.toLowerCase()
            }
        });
        document.dispatchEvent(searchEvent);
    }

    // Handle search input
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        }
    });

    // Handle search icon click
    searchIcon.addEventListener('click', () => {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            performSearch(searchTerm);
        }
    });

    // Check for stored search term when loading the products page
    if (window.location.href.includes('ProductsPage/product.php')) {
        const storedSearchTerm = sessionStorage.getItem('searchTerm');
        if (storedSearchTerm) {
            searchInput.value = storedSearchTerm;
            // Trigger the search event
            document.dispatchEvent(new CustomEvent('performSearch', {
                detail: { 
                    searchTerm: storedSearchTerm,
                    searchTermLower: storedSearchTerm.toLowerCase()
                }
            }));
        }
    }
}); 