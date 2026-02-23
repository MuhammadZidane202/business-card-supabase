<?php
// api/supabase.php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class SupabaseClient {
    private $url;
    private $apiKey;
    private $headers;
    
    public function __construct() {
        $this->url = $_ENV['SUPABASE_URL'];
        $this->apiKey = $_ENV['SUPABASE_ANON_KEY'];
        $this->headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
    }
    
    // Execute query ke Supabase
    private function executeQuery($method, $endpoint, $data = null, $params = []) {
        $ch = curl_init();
        
        // Build URL dengan parameter
        $url = $this->url . '/rest/v1/' . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        
        if ($data && in_array(strtoupper($method), ['POST', 'PATCH', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 400) {
            throw new Exception("Supabase error: " . $response, $httpCode);
        }
        
        return json_decode($response, true);
    }
    
    // ==================== CATEGORIES ====================
    public function getCategories() {
        $params = [
            'select' => '*',
            'order' => 'category_name.asc'
        ];
        return $this->executeQuery('GET', 'categories', null, $params);
    }
    
    public function getCategory($id) {
        $params = [
            'select' => '*',
            'id' => 'eq.' . $id
        ];
        $result = $this->executeQuery('GET', 'categories', null, $params);
        return $result[0] ?? null;
    }
    
    public function createCategory($data) {
        return $this->executeQuery('POST', 'categories', $data);
    }
    
    public function updateCategory($id, $data) {
        return $this->executeQuery('PATCH', 'categories?id=eq.' . $id, $data);
    }
    
    public function deleteCategory($id) {
        return $this->executeQuery('DELETE', 'categories?id=eq.' . $id);
    }
    
    // ==================== BUSINESS CARDS ====================
    public function getBusinessCards($filters = [], $page = 1, $limit = 12) {
        $params = [
            'select' => '*,categories(*)',
            'order' => 'created_at.desc',
            'limit' => $limit,
            'offset' => ($page - 1) * $limit
        ];
        
        // Filter berdasarkan kategori
        if (!empty($filters['category_id'])) {
            $params['category_id'] = 'eq.' . $filters['category_id'];
        }
        
        // Search
        if (!empty($filters['search'])) {
            $params['or'] = '(company_name.ilike.*' . $filters['search'] . '*,address.ilike.*' . $filters['search'] . '*,description.ilike.*' . $filters['search'] . '*)';
        }
        
        // Filter rating
        if (!empty($filters['min_rating'])) {
            $params['rating'] = 'gte.' . $filters['min_rating'];
        }
        
        // Featured only
        if (!empty($filters['featured'])) {
            $params['is_featured'] = 'eq.true';
        }
        
        // Active only
        $params['is_active'] = 'eq.true';
        
        return $this->executeQuery('GET', 'business_cards', null, $params);
    }
    
    public function getBusinessCard($id) {
        // Increment views
        $this->incrementViews($id);
        
        $params = [
            'select' => '*,categories(*),business_gallery(*)',
            'id' => 'eq.' . $id
        ];
        $result = $this->executeQuery('GET', 'business_cards', null, $params);
        return $result[0] ?? null;
    }
    
    public function createBusinessCard($data) {
        return $this->executeQuery('POST', 'business_cards', $data);
    }
    
    public function updateBusinessCard($id, $data) {
        return $this->executeQuery('PATCH', 'business_cards?id=eq.' . $id, $data);
    }
    
    public function deleteBusinessCard($id) {
        return $this->executeQuery('DELETE', 'business_cards?id=eq.' . $id);
    }
    
    private function incrementViews($id) {
        $card = $this->getBusinessCard($id);
        if ($card) {
            $views = ($card['views'] ?? 0) + 1;
            $this->executeQuery('PATCH', 'business_cards?id=eq.' . $id, ['views' => $views]);
        }
    }
    
    // ==================== GALLERY ====================
    public function addGalleryImage($data) {
        return $this->executeQuery('POST', 'business_gallery', $data);
    }
    
    public function deleteGalleryImage($id) {
        return $this->executeQuery('DELETE', 'business_gallery?id=eq.' . $id);
    }
    
    // ==================== STATISTICS ====================
    public function getStatistics() {
        $stats = [];
        
        // Total cards
        $cards = $this->executeQuery('GET', 'business_cards?select=id&is_active=eq.true');
        $stats['total_cards'] = count($cards);
        
        // Total categories
        $categories = $this->executeQuery('GET', 'categories?select=id');
        $stats['total_categories'] = count($categories);
        
        // Total views
        $views = $this->executeQuery('GET', 'business_cards?select=views');
        $stats['total_views'] = array_sum(array_column($views, 'views'));
        
        // Average rating
        $ratings = $this->executeQuery('GET', 'business_cards?select=rating&rating=neq.0');
        if (count($ratings) > 0) {
            $sum = array_sum(array_column($ratings, 'rating'));
            $stats['avg_rating'] = round($sum / count($ratings), 1);
        } else {
            $stats['avg_rating'] = 0;
        }
        
        return $stats;
    }
}

// Inisialisasi koneksi Supabase
$supabase = new SupabaseClient();
?>
