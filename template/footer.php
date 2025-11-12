<footer class="footer bg-black text-white py-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <img src="../assets/images/logo.png" alt="Gymly Logo" style="width: 40px; height: 40px; margin-right: 10px;">
                    <h5 class="fw-bold mb-0 text-white">Gymly</h5>
                </div>
                <p class="text-white">
                    Empower your fitness journey with Gymly — your all-in-one fitness management platform.
                </p>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="fw-semibold mb-3 text-white">Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="../pages/home.php" class="text-decoration-none text-white">Home</a></li>
                    <li><a href="../pages/about.php" class="text-decoration-none text-white">About</a></li>
                    <li><a href="../pages/track.php" class="text-decoration-none text-white">Track</a></li>
                    <li><a href="../pages/shop.php" class="text-decoration-none text-white">Shop</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-semibold mb-3 text-white">Features</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="../pages/nutrition.php" class="text-decoration-none text-white">Nutrition Tracking</a></li>
                    <li><a href="../pages/trackHealth.php" class="text-decoration-none text-white">Health Tracking</a></li>
                    <li><a href="../pages/myWorkouts.php" class="text-decoration-none text-white">My Workouts</a></li>
                    <li><a href="../pages/workoutSplits.php" class="text-decoration-none text-white">Workout Splits</a></li>
                    <li><a href="../pages/profile.php" class="text-decoration-none text-white">Profile</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-semibold mb-3 text-white">Contact</h5>
                <ul class="list-unstyled text-white">
                    <li class="mb-2"><i class="bi bi-envelope"></i> info@gymly.com</li>
                    <li class="mb-2"><i class="bi bi-geo-alt"></i> Nairobi, Kenya</li>
                </ul>
                <p class="small text-white mt-3">Built by Strathmore University students</p>
            </div>
        </div>

        <div class="footer-bottom text-center border-top border-secondary mt-4 pt-3">
            <p class="mb-0 text-white">
                &copy; <?php echo date("Y"); ?> Gymly. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<!-- Shopping Cart Offcanvas (Global) -->
<div class="offcanvas offcanvas-end bg-dark text-white" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title" id="cartOffcanvasLabel">
            <i class="bi bi-cart3"></i> Shopping Cart
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div id="cartItemsList" class="flex-grow-1 overflow-auto">
            <!-- Cart items will be loaded here -->
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <p class="text-muted mt-3">Your cart is empty</p>
            </div>
        </div>
        
        <!-- Cart Summary (sticky at bottom) -->
        <div class="mt-auto border-top border-secondary pt-3">
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <strong id="cartSubtotal">KSh 0</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Items:</span>
                <strong id="cartItemCount">0</strong>
            </div>
            <a href="../pages/shop.php" class="btn btn-primary w-100 mb-2" id="viewCartBtn">
                <i class="bi bi-cart-check"></i> View Cart & Checkout
            </a>
            <button class="btn btn-outline-light w-100" data-bs-dismiss="offcanvas">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global Cart Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = JSON.parse(localStorage.getItem('gymlyCart')) || [];
    
    // Format currency helper
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE').format(amount);
    }
    
    // Update cart display
    function updateCartDisplay() {
        const cartItemsList = document.getElementById('cartItemsList');
        const cartSubtotal = document.getElementById('cartSubtotal');
        const cartItemCount = document.getElementById('cartItemCount');
        const globalCartCount = document.getElementById('globalCartCount');
        
        if (!cartItemsList) return;
        
        // Calculate totals
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        // Update cart count badge
        if (globalCartCount) {
            globalCartCount.textContent = totalItems;
            globalCartCount.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
        
        // Update item count
        if (cartItemCount) {
            cartItemCount.textContent = totalItems;
        }
        
        // Update subtotal
        if (cartSubtotal) {
            cartSubtotal.textContent = 'KSh ' + formatCurrency(subtotal);
        }
        
        // Update cart items list
        if (cart.length === 0) {
            cartItemsList.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <p class="text-muted mt-3">Your cart is empty</p>
                </div>
            `;
        } else {
            cartItemsList.innerHTML = cart.map((item, index) => `
                <div class="card bg-secondary border-0 mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0 text-white">${item.product}</h6>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeCartItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">KSh ${formatCurrency(item.price)} each</small>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-light" onclick="updateCartQuantity(${index}, -1)">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <button class="btn btn-outline-light" disabled>${item.quantity}</button>
                                <button class="btn btn-outline-light" onclick="updateCartQuantity(${index}, 1)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <strong class="text-primary">KSh ${formatCurrency(item.price * item.quantity)}</strong>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Save to localStorage
        localStorage.setItem('gymlyCart', JSON.stringify(cart));
        
        // Trigger cart updated event
        window.dispatchEvent(new Event('cartUpdated'));
    }
    
    // Update quantity
    window.updateCartQuantity = function(index, change) {
        if (cart[index]) {
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            
            updateCartDisplay();
        }
    };
    
    // Remove item
    window.removeCartItem = function(index) {
        cart.splice(index, 1);
        updateCartDisplay();
    };
    
    // Initialize cart display on page load
    updateCartDisplay();
    
    // Update cart count from localStorage
    function updateGlobalCartCount() {
        const cartData = JSON.parse(localStorage.getItem('gymlyCart')) || [];
        cart = cartData;
        updateCartDisplay();
    }
    
    // Listen for storage changes (when cart is updated in another tab/page)
    window.addEventListener('storage', function(e) {
        if (e.key === 'gymlyCart') {
            updateGlobalCartCount();
        }
    });
    
    // Listen for custom cart update event
    window.addEventListener('cartUpdated', updateGlobalCartCount);
});
</script>

</body>
</html>
