<?php
// includes/footer.php
?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-id-card-alt me-2"></i> Business Card Pariwisata</h5>
                    <p class="text-muted">Database kartu nama perusahaan pariwisata terintegrasi dengan Supabase.</p>
                </div>
                <div class="col-md-3">
                    <h6>Link Cepat</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo $baseUrl; ?>" class="text-muted">Home</a></li>
                        <li><a href="<?php echo $baseUrl; ?>categories.php" class="text-muted">Kategori</a></li>
                        <li><a href="<?php echo $baseUrl; ?>search.php" class="text-muted">Pencarian</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Kategori</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo $baseUrl; ?>?category=Hotel" class="text-muted">Hotel</a></li>
                        <li><a href="<?php echo $baseUrl; ?>?category=Restoran" class="text-muted">Restoran</a></li>
                        <li><a href="<?php echo $baseUrl; ?>?category=Wisata" class="text-muted">Wisata</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center text-muted">
                <small>&copy; <?php echo date('Y'); ?> Business Card Pariwisata. Powered by Supabase.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo $baseUrl; ?>assets/js/script.js"></script>
</body>
</html>
