// products.js

// Fungsi untuk memuat produk berdasarkan kategori
function loadProducts(category) {
    fetch(`getProducts.php?category=${category}`)
        .then(response => response.json())
        .then(data => {
            const productGrid = document.getElementById('product-grid');
            productGrid.innerHTML = ''; // Clear existing products

            data.forEach(product => {
                const productCard = `
                    <div class="col-6 col-md-3" data-aos="fade-up">
                        <div class="product-card">
                            <div class="placeholder-image" style="height: 250px; border-radius: 10px; overflow: hidden;">
                                <img src="${product.image}" alt="${product.name}" class="img-fluid" style="height: 100%; width: 100%; object-fit: cover; border-radius: 10px;">
                            </div>
                            <p class="mt-3">${product.name}</p>
                            <span class="text-muted">${product.price}</span>
                            <button class="btn-cart">
                                Tambah ke Keranjang <i class="bi bi-cart"></i>
                            </button>
                        </div>
                    </div>
                `;
                productGrid.innerHTML += productCard; // Append new product card
            });
        })
        .catch(error => console.error('Error fetching products:', error));
}

// Event listener untuk kategori
document.querySelectorAll('.nav-link[data-category]').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        const category = this.getAttribute('data-category'); // Get the category from the data attribute
        loadProducts(category); // Load products for the selected category
    });
});

// Load default products on page load
loadProducts('songket'); // You can change this to any default category