<?php
// admin/add.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$pageTitle = "Tambah Kartu Nama - " . $siteName;

// Get categories for dropdown
try {
    $categories = $supabase->getCategories();
} catch (Exception $e) {
    $categories = [];
    setFlashMessage('danger', 'Error mengambil data kategori');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'category_id' => $_POST['category_id'],
        'company_name' => sanitizeInput($_POST['company_name']),
        'tagline' => sanitizeInput($_POST['tagline']),
        'address' => sanitizeInput($_POST['address']),
        'phone' => sanitizeInput($_POST['phone']),
        'mobile' => sanitizeInput($_POST['mobile']),
        'fax' => sanitizeInput($_POST['fax']),
        'email' => sanitizeInput($_POST['email']),
        'website' => sanitizeInput($_POST['website']),
        'contact_person' => sanitizeInput($_POST['contact_person']),
        'contact_position' => sanitizeInput($_POST['contact_position']),
        'contact_phone' => sanitizeInput($_POST['contact_phone']),
        'contact_email' => sanitizeInput($_POST['contact_email']),
        'description' => sanitizeInput($_POST['description']),
        'is_active' => true,
        'views' => 0
    ];
    
    try {
        $result = $supabase->createBusinessCard($data);
        setFlashMessage('success', 'Kartu nama berhasil ditambahkan');
        header('Location: ' . $baseUrl . 'view.php?id=' . $result[0]['id']);
        exit;
    } catch (Exception $e) {
        setFlashMessage('danger', 'Error: ' . $e->getMessage());
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Tambah Kartu Nama Baru</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Kategori *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo $cat['category_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan *</label>
                            <input type="text" class="form-control" name="company_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tagline</label>
                            <input type="text" class="form-control" name="tagline">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat *</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fax</label>
                                <input type="text" class="form-control" name="fax">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website" placeholder="https://">
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Contact Person</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Contact Person</label>
                                <input type="text" class="form-control" name="contact_person">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control" name="contact_position">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon Contact Person</label>
                                <input type="text" class="form-control" name="contact_phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Contact Person</label>
                                <input type="email" class="form-control" name="contact_email">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Kartu Nama
                            </button>
                            <a href="<?php echo $baseUrl; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
