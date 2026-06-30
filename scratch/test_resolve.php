<?php
$isDev = false;
$viteHost = 'http://localhost:5173';

function resolve_asset($path) {
    global $isDev, $viteHost;
    if (empty($path)) return '';
    
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    $cleanPath = ltrim($path, '/');
    if (strpos($cleanPath, '../') === 0) {
        $cleanPath = substr($cleanPath, 3);
    }
    
    if (strpos($cleanPath, 'public/') === 0) {
        $cleanPath = substr($cleanPath, 7);
    }
    if (strpos($cleanPath, 'dist/') === 0) {
        $cleanPath = substr($cleanPath, 5);
    }
    
    if ($isDev) {
        return $viteHost . '/' . $cleanPath;
    } else {
        $baseUrl = '';
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $dir = dirname($_SERVER['SCRIPT_NAME']);
            if ($dir !== '/' && $dir !== '\\') {
                $baseUrl = rtrim($dir, '/\\');
            }
        }
        
        if (is_dir(__DIR__ . '/../dist')) {
            if (!empty($baseUrl)) {
                return $baseUrl . '/dist/' . $cleanPath;
            } else {
                return 'dist/' . $cleanPath;
            }
        } else {
            if (!empty($baseUrl)) {
                return $baseUrl . '/' . $cleanPath;
            } else {
                return '/' . $cleanPath;
            }
        }
    }
}

echo "Resolved: " . resolve_asset("/assets/images/uploads/img_6a408cbe059122.23888706.png") . "\n";
?>
