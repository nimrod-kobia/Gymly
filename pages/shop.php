<?php
$pageTitle = "Gym Shop - Quality Fitness Equipment";
require_once "../template/layout.php";
?>

<main class="container-fluid py-5 mt-5">
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-white mb-3">Gymly Shop</h1>
                <p class="lead text-light">Premium Quality Fitness Equipment & Supplements</p>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control bg-dark text-white border-dark" placeholder="Search products..." id="searchInput">
                    <button class="btn btn-primary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select bg-dark text-white border-dark" id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="equipment">Equipment</option>
                    <option value="supplements">Supplements</option>
                    <option value="apparel">Apparel</option>
                    <option value="accessories">Accessories</option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4" id="productsGrid">
            <!-- Product 1 - Professional Dumbbell Set -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="equipment">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1638536532686-d610adfc8e5c?w=500&h=400&fit=crop" class="card-img-top" alt="Professional Dumbbell Set" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Professional Dumbbell Set</h5>
                        <p class="card-text text-light flex-grow-1">Adjustable rubber hex dumbbells 5-50kg</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 35,000</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Dumbbell Set" data-price="35000">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 2 - Yoga Mat -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="equipment">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500&h=400&fit=crop" class="card-img-top" alt="Premium Yoga Mat" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Premium Yoga Mat</h5>
                        <p class="card-text text-light flex-grow-1">Non-slip TPE material, extra thick 6mm</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 2,500</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Yoga Mat" data-price="2500">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 3 - Whey Protein -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="supplements">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1593095948071-474c5cc2989d?w=500&h=400&fit=crop" class="card-img-top" alt="Whey Protein" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Whey Protein Isolate</h5>
                        <p class="card-text text-light flex-grow-1">24g protein per serving, various flavors</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 4,500</span>
                            <span class="badge bg-warning text-dark">Low Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Whey Protein" data-price="4500">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 4 - Performance T-Shirt -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="apparel">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=500&h=400&fit=crop" class="card-img-top" alt="Performance T-Shirt" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Performance T-Shirt</h5>
                        <p class="card-text text-light flex-grow-1">Moisture-wicking, breathable fabric</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 1,800</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Performance T-Shirt" data-price="1800">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 5 - Resistance Bands -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="equipment">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1598289431512-b97b0917affc?w=500&h=400&fit=crop" class="card-img-top" alt="Resistance Bands Set" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Stationary bicycles</h5>
                        <p class="card-text text-light flex-grow-1">Multiple bicycles</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 45,000</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Resistance Bands" data-price="3200">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 6 - Pre-Workout -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="supplements">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1579722821273-0f6c7d44362f?w=500&h=400&fit=crop" class="card-img-top" alt="Pre-Workout Supplement" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Pre-Workout Energizer</h5>
                        <p class="card-text text-light flex-grow-1">Boost energy and focus for intense workouts</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 3,800</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Pre-Workout" data-price="3800">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 7 - Water Bottle -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="accessories">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500&h=400&fit=crop" class="card-img-top" alt="Insulated Water Bottle" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Insulated Water Bottle</h5>
                        <p class="card-text text-light flex-grow-1">1L capacity, keeps drinks cold for 24 hours</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 1,200</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Water Bottle" data-price="1200">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product 8 - Training Shorts -->
            <div class="col-lg-3 col-md-4 col-sm-6" data-category="apparel">
                <div class="card h-100 product-card bg-dark border-secondary">
                    <img src="https://images.unsplash.com/photo-1744551472743-efc22070ec1a?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=654" class="card-img-top" alt="Training Shorts" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">Training Shorts</h5>
                        <p class="card-text text-light flex-grow-1">Flexible, quick-dry fabric with pockets</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">KSh 2,200</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Training Shorts" data-price="2200">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shopping Cart Sidebar -->
        <div class="offcanvas offcanvas-end bg-dark text-white" tabindex="-1" id="cartOffcanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Shopping Cart</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <div id="cartItems">
                    <p class="text-muted">Your cart is empty</p>
                </div>
                <div class="mt-auto border-top pt-3">
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total: KSh <span id="cartTotal">0</span></strong>
                    </div>
                    <button class="btn btn-primary w-100" id="checkoutBtn">Proceed to Checkout</button>
                </div>
            </div>
        </div>

        <!-- Cart Button -->
        <div class="position-fixed bottom-0 end-0 m-4">
            <button class="btn btn-primary rounded-circle p-3 shadow-lg" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                <i class="bi bi-cart3 fs-4"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">0</span>
            </button>
        </div>
    </div>
</main>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const productsGrid = document.getElementById('productsGrid');

    // Format currency for display
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Add to Cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const product = this.getAttribute('data-product');
            const price = parseFloat(this.getAttribute('data-price'));
            
            addToCart(product, price);
            showToast(`${product} added to cart!`);
        });
    });

    function addToCart(product, price) {
        const existingItem = cart.find(item => item.product === product);
        
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({ product, price, quantity: 1 });
        }
        
        updateCart();
    }

    function updateCart() {
        // Update cart count
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;

        // Update cart items display
        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-muted">Your cart is empty</p>';
        } else {
            cartItems.innerHTML = cart.map(item => `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                    <div>
                        <h6 class="mb-0">${item.product}</h6>
                        <small class="text-muted">KSh ${formatCurrency(item.price)} x ${item.quantity}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-light" onclick="updateQuantity('${item.product}', -1)">-</button>
                        <span>${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-light" onclick="updateQuantity('${item.product}', 1)">+</button>
                        <button class="btn btn-sm btn-danger" onclick="removeFromCart('${item.product}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Update total
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotal.textContent = formatCurrency(total);
    }

    // Search and filter functionality
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        
        document.querySelectorAll('.product-card').forEach(card => {
            const productName = card.querySelector('.card-title').textContent.toLowerCase();
            const productCategory = card.parentElement.getAttribute('data-category');
            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = category === 'all' || productCategory === category;
            
            if (matchesSearch && matchesCategory) {
                card.parentElement.style.display = 'block';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);

    // Toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // Global functions for cart operations
    window.updateQuantity = function(product, change) {
        const item = cart.find(item => item.product === product);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(product);
            } else {
                updateCart();
            }
        }
    };

    window.removeFromCart = function(product) {
        cart = cart.filter(item => item.product !== product);
        updateCart();
        showToast(`${product} removed from cart`);
    };

    // Checkout functionality
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        if (cart.length === 0) {
            showToast('Your cart is empty!', 'warning');
            return;
        }
        showToast('Checkout functionality coming soon!', 'info');
    });
});
</script>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
}

.add-to-cart {
    transition: all 0.3s ease;
}

.add-to-cart:hover {
    transform: scale(1.05);
}

.toast {
    z-index: 9999;
}
</style>

<?php include '../template/footer.php'; ?>