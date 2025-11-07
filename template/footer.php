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
                    <li><a href="../pages/tracking.php" class="text-decoration-none text-white">Tracking</a></li>
                    <li><a href="../pages/shop.php" class="text-decoration-none text-white">Shop</a></li>
                    <li><a href="../pages/contact.php" class="text-decoration-none text-white">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-semibold mb-3 text-white">Features</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#" class="text-decoration-none text-white">Member Management</a></li>
                    <li><a href="#" class="text-decoration-none text-white">Progress Tracking</a></li>
                    <li><a href="#" class="text-decoration-none text-white">Workout Analytics</a></li>
                    <li><a href="#" class="text-decoration-none text-white">Nutrition Plans</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-semibold mb-3 text-white">Contact</h5>
                <ul class="list-unstyled text-white">
                    <li class="mb-2"><i class="bi bi-envelope"></i> info@gymly.com</li>
                    <li><i class="bi bi-geo-alt"></i> Nairobi, Kenya</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom text-center border-top border-secondary mt-4 pt-3">
            <p class="mb-0 text-white">
                &copy; <?php echo date("Y"); ?> Gymly. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global Cart Count Update -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count from localStorage
    function updateGlobalCartCount() {
        const cart = JSON.parse(localStorage.getItem('gymlyCart')) || [];
        const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
        const badge = document.getElementById('globalCartCount');
        if (badge) {
            badge.textContent = totalItems;
            badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
    }
    
    // Update on page load
    updateGlobalCartCount();
    
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
