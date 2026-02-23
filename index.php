<?php
// index.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;

$pageTitle = "Beranda - " . $siteName;

// Build filters
$filters = [];
if ($search) $filters['search'] = $search;
if ($category) $filters['category_id'] = $category;
if ($rating > 0) $filters['min_rating'] = $rating;

try {
    // Get business cards
    $businessCards = $supabase->getBusinessCards($filters, $page, 12);
    
    // Get categories for filter
    $categories = $supabase->getCategories();
    
    // Get statistics
    $stats = $supabase->getStatistics();
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $businessCards = [];
    $stats = ['total_cards' => 0, 'total_categories' => 0, 'total_views' => 0, 'avg_rating' => 0];
}

include 'includes/header.php';
?>

<div class="container">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-2x mb-2"></i>
                    <h3><?php echo number_format($stats['total_cards']); ?></h3>
                    <p class="mb-0">Total Perusahaan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x mb-2"></i>
                    <h3><?php echo $stats['total_categories']; ?></h3>
                    <p class="mb-0">Kategori</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-eye fa-2x mb-2"></i>
                    <h3><?php echo number_format($stats['total_views']); ?></h3>
                    <p class="mb-0">Total Views</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <h3><?php echo $stats['avg_rating']; ?></h3>
                    <p class="mb-0">Rating Rata-rata</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Cari perusahaan, alamat..." value="<?php echo $search; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['category_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="rating">
                        <option value="">Semua Rating</option>
                        <option value="4" <?php echo $rating == 4 ? 'selected' : ''; ?>>4+ Bintang</option>
                        <option value="3" <?php echo $rating == 3 ? 'selected' : ''; ?>>3+ Bintang</option>
                        <option value="2" <?php echo $rating == 2 ? 'selected' : ''; ?>>2+ Bintang</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Business Cards Grid -->
    <?php if (empty($businessCards)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-id-card fa-3x mb-3"></i>
            <h4>Tidak ada data kartu nama</h4>
            <p>Belum ada kartu nama yang ditambahkan ke database.</p>
            <a href="admin/add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kartu Nama
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($businessCards as $card): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm hover-card">
                    <?php if (!empty($card['logo_url'])): ?>
                    <img src="<?php echo $card['logo_url']; ?>" class="card-img-top" alt="Logo" 
                         style="height: 150px; object-fit: cover;">
                    <?php else: ?>
                    <div class="bg-light text-center py-4">
                        <i class="fas fa-building fa-3x text-muted"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h6 class="card-title fw-bold"><?php echo $card['company_name']; ?></h6>
                        
                        <?php if (!empty($card['categories'])): ?>
                        <span class="badge bg-primary mb-2">
                            <i class="fas <?php echo getCategoryIcon($card['categories']['category_name']); ?> me-1"></i>
                            <?php echo $card['categories']['category_name']; ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($card['rating'] > 0): ?>
                        <div class="mb-2">
                            <?php echo renderStars($card['rating']); ?>
                            <small class="text-muted">(<?php echo $card['rating']; ?>)</small>
                        </div>
                        <?php endif; ?>
                        
                        <p class="card-text small">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            <?php echo truncateText($card['address'] ?? '-', 40); ?>
                        </p>
                        
                        <p class="card-text small">
                            <i class="fas fa-phone text-success me-1"></i>
                            <?php echo formatPhone($card['phone'] ?? $card['mobile'] ?? '-'); ?>
                        </p>
                    </div>
                    
                    <div class="card-footer bg-white border-0 pb-3">
                        <a href="view.php?id=<?php echo $card['id']; ?>" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
