<?php
// includes/config.php

session_start();

require_once __DIR__ . '/../api/supabase.php';

// Base URL
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $dir = str_replace(basename($script), '', $script);
    
    // Untuk GitHub Pages atau hosting di subfolder
    if (strpos($host, 'github.io') !== false) {
        return $protocol . $host . '/business-card-supabase/' . ltrim($path, '/');
    }
    
    return $protocol . $host . $dir . ltrim($path, '/');
}

// Global variables
$siteName = "Business Card Pariwisata";
$baseUrl = baseUrl();
?>
