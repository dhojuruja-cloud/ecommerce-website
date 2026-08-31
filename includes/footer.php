<footer class="site-footer mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <h5><?= htmlspecialchars(APP_NAME) ?></h5>
                <p>Modern essentials for everyday living.</p>
            </div>
            <div class="col-md-3">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= APP_URL ?>/index.php">Home</a></li>
                    <li><a href="<?= APP_URL ?>/products.php">Products</a></li>
                    <li><a href="<?= APP_URL ?>/categories.php">Categories</a></li>
                    <li><a href="<?= APP_URL ?>/cart.php">Cart</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Customer Care</h6>
                <ul class="list-unstyled">
                    <li>Support: +977-9800000000</li>
                    <li>Email: hello@marketnest.com</li>
                    <li>Shipping: 2-5 days</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Payments</h6>
                <ul class="list-unstyled">
                    <li>Khalti</li>
                    <li>eSewa</li>
                    <li>Cash on Delivery</li>
                </ul>
            </div>
        </div>
        <div class="border-top mt-4 pt-3 text-center small text-muted">
            © <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?>. All rights reserved.
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
