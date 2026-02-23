<?php
// includes/functions.php

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function truncateText($text, $limit = 100) {
    if (strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, $limit) . '...';
}

function renderStars($rating) {
    $output = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $output .= '<i class="fas fa-star text-warning"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $output .= '<i class="fas fa-star-half-alt text-warning"></i>';
        } else {
            $output .= '<i class="far fa-star text-warning"></i>';
        }
    }
    return $output;
}

function formatPhone($phone) {
    if (empty($phone)) return '-';
    return $phone;
}

function formatWebsite($url) {
    if (empty($url)) return '-';
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "https://" . $url;
    }
    return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . parse_url($url, PHP_URL_HOST) . '</a>';
}

function getCategoryIcon($category) {
    $icons = [
        'Hotel & Resort' => 'fa-hotel',
        'Depot Bus & Transportasi' => 'fa-bus',
        'Tempat Wisata' => 'fa-umbrella-beach',
        'Rumah Makan & Restoran' => 'fa-utensils',
        'Travel Agent' => 'fa-plane',
        'Souvenir & Oleh-oleh' => 'fa-gift',
        'Pemandu Wisata' => 'fa-user-tie',
        'Event Organizer' => 'fa-calendar-alt',
        'Spa & Wellness' => 'fa-spa',
        'Destinasi Wisata' => 'fa-mountain'
    ];
    
    return $icons[$category] ?? 'fa-building';
}
?>
