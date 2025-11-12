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
                            <span class="h5 text-primary mb-0">KSh 1</span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                        <button class="btn btn-primary mt-3 add-to-cart" data-product="Water Bottle" data-price="1">
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

        <!-- Shopping Cart Offcanvas -->
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
                    <button class="btn btn-primary w-100 mb-2" id="checkoutBtn" disabled>
                        <i class="bi bi-credit-card"></i> Proceed to Checkout
                    </button>
                    <button class="btn btn-outline-light w-100" data-bs-dismiss="offcanvas">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="bi bi-credit-card-2-front"></i> Complete Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Customer Information -->
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Full Name *</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="customerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control bg-dark text-white border-secondary" id="customerEmail">
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mb-3">
                        <label class="form-label">Payment Method *</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="paymentMethod" id="mpesaMethod" value="mpesa" checked>
                            <label class="btn btn-outline-success" for="mpesaMethod">
                                <i class="bi bi-phone"></i> M-Pesa
                            </label>
                            
                            <input type="radio" class="btn-check" name="paymentMethod" id="cardMethod" value="card">
                            <label class="btn btn-outline-primary" for="cardMethod">
                                <i class="bi bi-credit-card"></i> Credit/Debit Card
                            </label>
                        </div>
                    </div>

                    <!-- M-Pesa Phone Number (shown when M-Pesa is selected) -->
                    <div class="mb-3" id="mpesaPhoneSection">
                        <label for="mpesaPhone" class="form-label">M-Pesa Phone Number *</label>
                        <input type="tel" class="form-control bg-dark text-white border-secondary" 
                               id="mpesaPhone" placeholder="0712345678 or 254712345678">
                        <small class="form-text text-muted">Enter your M-Pesa registered phone number</small>
                    </div>

                    <!-- Card Payment Section (shown when Card is selected) -->
                    <div class="mb-3 d-none" id="cardPaymentSection">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Card payment integration coming soon. 
                            For now, please use M-Pesa.
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="border border-secondary rounded p-3 mb-3">
                        <h6 class="mb-2">Order Summary</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Items:</span>
                            <span id="modalItemCount">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <strong class="text-success" id="modalTotalAmount">KSh 0</strong>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div id="paymentStatus" class="d-none">
                        <div class="alert" role="alert" id="paymentStatusMessage"></div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPaymentBtn">
                        <i class="bi bi-check-circle"></i> Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load cart from localStorage
    let cart = JSON.parse(localStorage.getItem('gymlyCart')) || [];
    
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const cartItemsList = document.getElementById('cartItemsList');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartItemCount = document.getElementById('cartItemCount');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // Format currency for display
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Update cart display
    function updateCartDisplay() {
        const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        // Update summary
        cartSubtotal.textContent = 'KSh ' + formatCurrency(subtotal);
        cartItemCount.textContent = totalItems;
        checkoutBtn.disabled = cart.length === 0;
        
        // Update cart items list
        if (cart.length === 0) {
            cartItemsList.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <p class="text-muted mt-3">Your cart is empty</p>
                    <p class="small text-muted">Add some products to get started!</p>
                </div>
            `;
        } else {
            cartItemsList.innerHTML = cart.map((item, index) => `
                <div class="cart-item mb-3 p-3 border border-secondary rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-1">${item.product}</h6>
                        <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">KSh ${formatCurrency(item.price)} each</small>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-light" onclick="updateQuantity(${index}, -1)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <button class="btn btn-outline-light" disabled>${item.quantity}</button>
                            <button class="btn btn-outline-light" onclick="updateQuantity(${index}, 1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <strong class="text-primary">KSh ${formatCurrency(item.price * item.quantity)}</strong>
                    </div>
                </div>
            `).join('');
        }
        
        // Save to localStorage
        localStorage.setItem('gymlyCart', JSON.stringify(cart));
        
        // Trigger global cart update
        window.dispatchEvent(new Event('cartUpdated'));
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
        
        updateCartDisplay();
    }

    // Update quantity
    window.updateQuantity = function(index, change) {
        if (cart[index]) {
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            
            updateCartDisplay();
        }
    };

    // Remove item
    window.removeItem = function(index) {
        const itemName = cart[index].product;
        cart.splice(index, 1);
        updateCartDisplay();
        showToast(`${itemName} removed from cart`);
    };

    // Checkout button
    checkoutBtn.addEventListener('click', function() {
        if (cart.length === 0) {
            showToast('Your cart is empty!', 'warning');
            return;
        }
        
        // Calculate totals
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        // Update modal with cart details
        document.getElementById('modalItemCount').textContent = totalItems;
        document.getElementById('modalTotalAmount').textContent = 'KSh ' + formatCurrency(totalAmount);
        
        // Reset payment form
        document.getElementById('customerName').value = '';
        document.getElementById('customerEmail').value = '';
        document.getElementById('mpesaPhone').value = '';
        document.getElementById('paymentStatus').classList.add('d-none');
        document.getElementById('mpesaMethod').checked = true;
        document.getElementById('mpesaPhoneSection').classList.remove('d-none');
        document.getElementById('cardPaymentSection').classList.add('d-none');
        
        // Show payment modal
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    });

    // Payment method toggle
    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'mpesa') {
                document.getElementById('mpesaPhoneSection').classList.remove('d-none');
                document.getElementById('cardPaymentSection').classList.add('d-none');
            } else {
                document.getElementById('mpesaPhoneSection').classList.add('d-none');
                document.getElementById('cardPaymentSection').classList.remove('d-none');
            }
        });
    });

    // Confirm payment button
    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
        const customerName = document.getElementById('customerName').value.trim();
        const customerEmail = document.getElementById('customerEmail').value.trim();
        const mpesaPhone = document.getElementById('mpesaPhone').value.trim();
        
        // Validate inputs
        if (!customerName) {
            showToast('Please enter your name', 'warning');
            return;
        }
        
        if (paymentMethod === 'mpesa' && !mpesaPhone) {
            showToast('Please enter your M-Pesa phone number', 'warning');
            return;
        }
        
        // Calculate total
        const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        // Disable button and show loading
        const confirmBtn = this;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        
        // Prepare payment data
        const paymentData = {
            paymentMethod: paymentMethod,
            phoneNumber: mpesaPhone,
            amount: totalAmount,
            items: cart,
            customerName: customerName,
            customerEmail: customerEmail
        };
        
        // Send payment request
        fetch('../handlers/processPayment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Confirm Payment';
            
            if (data.success) {
                // Show success message
                const statusDiv = document.getElementById('paymentStatus');
                const messageDiv = document.getElementById('paymentStatusMessage');
                statusDiv.classList.remove('d-none');
                messageDiv.className = 'alert alert-success';
                messageDiv.innerHTML = `
                    <i class="bi bi-check-circle"></i> ${data.message}
                    ${paymentMethod === 'mpesa' ? '<br><small><strong>Check your phone for the M-Pesa prompt.</strong></small>' : ''}
                    <br><small>Complete the payment on your phone. Your cart will remain until you confirm completion.</small>
                `;
                
                // Add a button to manually clear cart after payment
                messageDiv.innerHTML += `
                    <div class="mt-3">
                        <button class="btn btn-sm btn-success me-2" onclick="confirmPaymentComplete()">
                            <i class="bi bi-check-circle"></i> I've Completed Payment
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cancelPayment()">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                    </div>
                `;
            } else {
                // Show error message
                const statusDiv = document.getElementById('paymentStatus');
                const messageDiv = document.getElementById('paymentStatusMessage');
                statusDiv.classList.remove('d-none');
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = `
                    <i class="bi bi-exclamation-circle"></i> <strong>Payment Failed:</strong> ${data.message}
                    <br><small>Your cart has been preserved. Please try again.</small>
                `;
                
                // Show specific messages for common errors
                if (data.message && data.message.toLowerCase().includes('insufficient')) {
                    messageDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle"></i> <strong>Insufficient Funds</strong>
                        <br>You don't have enough money in your M-Pesa account.
                        <br><small>Please top up your M-Pesa and try again. Your cart is still here!</small>
                    `;
                }
            }
        })
        .catch(error => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Confirm Payment';
            
            const statusDiv = document.getElementById('paymentStatus');
            const messageDiv = document.getElementById('paymentStatusMessage');
            statusDiv.classList.remove('d-none');
            messageDiv.className = 'alert alert-danger';
            messageDiv.innerHTML = '<i class="bi bi-exclamation-circle"></i> Payment processing failed. Please try again.';
            
            console.error('Payment error:', error);
        });
    });

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

    // Payment completion handlers
    window.confirmPaymentComplete = function() {
        // Clear the cart
        cart = [];
        updateCartDisplay();
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
        if (modal) modal.hide();
        
        // Show success toast
        showToast('Thank you! Your order has been confirmed. 🎉', 'success');
        
        // Reset payment status
        document.getElementById('paymentStatus').classList.add('d-none');
    };

    window.cancelPayment = function() {
        // Just close the modal, keep the cart
        const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
        if (modal) modal.hide();
        
        // Reset payment status
        document.getElementById('paymentStatus').classList.add('d-none');
        
        showToast('Payment cancelled. Your cart is still here.', 'info');
    };

    // Toast notification
    function showToast(message, type = 'success') {
        const bgClass = type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning text-dark' : 'bg-info';
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${bgClass} border-0 position-fixed bottom-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // Initial cart display
    updateCartDisplay();
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