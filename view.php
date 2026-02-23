<?php
// view.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    setFlashMessage('danger', 'ID tidak valid');
    header('Location: ' . $baseUrl);
    exit;
}

try {
    $card = $supabase->getBusinessCard($id);
    
    if (!$card) {
        setFlashMessage('danger', 'Data tidak ditemukan');
        header('Location: ' . $baseUrl);
        exit;
    }
    
    $pageTitle = $card['company_name'] . ' - ' . $siteName;
    
} catch (Exception $e) {
    setFlashMessage('danger', 'Error: ' . $e->getMessage());
    header('Location: ' . $baseUrl);
    exit;
}

include 'includes/header.php';
?>

<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $baseUrl; ?>">Home</a></li>
            <li class="breadcrumb-item active">Detail Kartu Nama</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <?php if (!empty($card['logo_url'])): ?>
                                <img src="<?php echo $card['logo_url']; ?>" alt="Logo" 
                                     class="img-fluid rounded" style="max-height: 150px;">
                            <?php else: ?>
                                <div class="bg-light p-4 rounded">
                                    <i class="fas fa-building fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h2 class="mb-2"><?php echo $card['company_name']; ?></h2>
                            
                            <?php if (!empty($card['categories'])): ?>
                            <p class="mb-2">
                                <span class="badge bg-primary">
                                    <i class="fas <?php echo getCategoryIcon($card['categories']['category_name']); ?> me-1"></i>
                                    <?php echo $card['categories']['category_name']; ?>
                                </span>
                                
                                <?php if ($card['is_featured']): ?>
                                <span class="badge bg-warning">
                                    <i class="fas fa-crown"></i> Featured
                                </span>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($card['tagline'])): ?>
                            <p class="text-muted fst-italic"><?php echo $card['tagline']; ?></p>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <?php echo renderStars($card['rating']); ?>
                                <span class="ms-2">(<?php echo $card['rating']; ?> / 5)</span>
                            </div>
                            
                            <div class="text-muted">
                                <i class="fas fa-eye me-1"></i> Dilihat <?php echo number_format($card['views']); ?> kali
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-address-card me-2"></i> Informasi Kontak</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> <strong>Alamat:</strong></p>
                            <p><?php echo nl2br($card['address'] ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><i class="fas fa-phone text-success me-2"></i> <strong>Telepon:</strong></p>
                            <p><?php echo formatPhone($card['phone'] ?? '-'); ?></p>
                            
                            <?php if (!empty($card['mobile'])): ?>
                            <p class="mb-1"><i class="fas fa-mobile-alt text-success me-2"></i> <strong>Mobile:</strong></p>
                            <p><?php echo formatPhone($card['mobile']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($card['fax'])): ?>
                            <p class="mb-1"><i class="fas fa-fax text-success me-2"></i> <strong>Fax:</strong></p>
                            <p><?php echo $card['fax']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><i class="fas fa-envelope text-info me-2"></i> <strong>Email:</strong></p>
                            <p>
                                <?php if (!empty($card['email'])): ?>
                                    <a href="mailto:<?php echo $card['email']; ?>"><?php echo $card['email']; ?></a>
                                <?php else: ?>-<?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><i class="fas fa-globe text-info me-2"></i> <strong>Website:</strong></p>
                            <p><?php echo !empty($card['website']) ? formatWebsite($card['website']) : '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($card['description'])): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Deskripsi</h5>
                </div>
                <div class="card-body">
                    <?php echo nl2br($card['description']); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Gallery -->
            <?php if (!empty($card['business_gallery'])): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i> Galeri Foto</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($card['business_gallery'] as $image): ?>
                        <div class="col-md-3 col-6">
                            <a href="<?php echo $image['image_url']; ?>" target="_blank">
                                <img src="<?php echo $image['image_url']; ?>" alt="Gallery" 
                                     class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                            </a>
                            <?php if (!empty($image['caption'])): ?>
                            <p class="small text-muted mt-1"><?php echo $image['caption']; ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Contact Person -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i> Contact Person</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($card['contact_person'])): ?>
                    <p class="fw-bold mb-1"><?php echo $card['contact_person']; ?></p>
                    <?php if (!empty($card['contact_position'])): ?>
                    <p class="text-muted small"><?php echo $card['contact_position']; ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['contact_phone'])): ?>
                    <p class="mb-1">
                        <i class="fas fa-phone text-success me-2"></i>
                        <?php echo formatPhone($card['contact_phone']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['contact_email'])): ?>
                    <p class="mb-0">
                        <i class="fas fa-envelope text-info me-2"></i>
                        <a href="mailto:<?php echo $card['contact_email']; ?>"><?php echo $card['contact_email']; ?></a>
                    </p>
                    <?php endif; ?>
                    <?php else: ?>
                    <p class="text-muted">Tidak ada informasi contact person</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="admin/edit.php?id=<?php echo $card['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Data
                        </a>
                        <a href="admin/delete.php?id=<?php echo $card['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Yakin ingin menghapus kartu nama ini?')">
                            <i class="fas fa-trash"></i> Hapus Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
